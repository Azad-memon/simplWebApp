<?php

namespace App\Http\Controllers;

use App\Models\BranchSetting;
use App\Models\Cart;
use App\Models\CashoutTransaction;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Ingredient;
use App\Models\IngredientProductVariant;
use App\Models\Order;
use App\Models\OrderQueue;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShiftCashNote;
use App\Models\ShiftIngredient;
use App\Models\ShiftUser;
use App\Models\UrlSetting;
use App\Models\User;
use App\Repositories\Interfaces\CartRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use App\Repositories\Interfaces\IngredientRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\ProductVariantRepositoryInterface;
use App\Services\StationPrintService;
use Auth;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;

class POSController extends Controller
{
    protected $categoryRepository;

    protected $productRepository;

    protected $cartRepository;

    protected $customerRepository;

    protected $orderRepository;

    protected $ingredientRepository;

    protected $productVariantRepository;

    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        ProductRepositoryInterface $productRepository,
        CartRepositoryInterface $cartRepository, CustomerRepositoryInterface $customerRepository, OrderRepositoryInterface $orderRepository,
        ingredientRepositoryInterface $ingredientRepository,
        ProductVariantRepositoryInterface $productVariantRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->cartRepository = $cartRepository;
        $this->customerRepository = $customerRepository;
        $this->orderRepository = $orderRepository;
        $this->ingredientRepository = $ingredientRepository;
        $this->productVariantRepository = $productVariantRepository;

    }

    public function index()
    {
        $categories = $this->categoryRepository->all();
        $products = $this->productRepository->all();
        $cart = $this->cartRepository->all("",'pos');

        return view('admin.staff.dashboard', compact('categories', 'products', 'cart'));
    }

    public function getProductsByCategory($id)
    {
        $products = $this->productRepository->findByCategory($id);

        return response()->json([
            'products' => $products,
        ]);
    }

    public function loadVariantOptions(Request $request)
    {
        $ingrediants = IngredientProductVariant::with(['ingredientCategory.ingredients.sizes', 'defaultIngredient', 'ingredient'])->where('product_variant_id', $request->variant_id)->get();
        // dump( $ingrediants );
        $variant = ProductVariant::find($request->variant_id);
        $product = Product::with(['addons.addonable'])->find($request->product_id);
        // dump( $product->addons);
        $addonIngredients = $product->addons->map(function ($addon) use ($variant) {
            $addonable = $addon->addonable;

            if (! $addonable) {
                return null;
            }

            return [
                'id' => $addonable->id,
                'addon_id' => $addon->id,
                'name' => $addonable->name,
                'quantity' => $addon->qty,
                'type' => 'optional',
                'cat_id' => $addon->addonable_id,
                'is_replace' => $addon->is_replace,
                'default' => null,
                'others' => $addon->items->map(function ($item) use ($variant) {
                    $ingredient = Ingredient::find($item->ingredient_id);

                    return [
                        'ing_id' => $ingredient?->ing_id,
                        'name' => $ingredient?->ingredient_label ?? $ingredient?->ing_name,
                        'size_id' => $variant->sizes?->id,
                        'unit' => $ingredient?->unit?->symbol ?? '',
                        'image' => $ingredient?->main_image ?? '',
                        'price' => $ingredient && $ingredient->sizes
                            ? optional($ingredient->sizes->where('size_id', $variant->sizes?->id)->first())->price ?? 0
                            : 0,
                    ];
                })->filter()->values() ?? [],

                // ✅ Selected ingredients for edit checkboxes
                'selected_ingredients' => $addon->items->pluck('ingredient_id')->toArray(),
            ];
        })->filter()
            ->sortBy('addon_id')
            ->values();

        $html = view('admin.staff.partials.variant-options', compact('ingrediants', 'addonIngredients', 'variant'))->render();

        return response()->json(['html' => $html]);
    }

    public function addcart(Request $request)
    {
        //  dd($request->all());
        $addons = $request->addons ? json_decode($request->addons, true) : [];
        $variant = $this->productVariantRepository->find($request->product_variant_id);
        $ingredients = $variant->ingredientCategories()->with('ingredients')->get();

        $removedIngredients = [];

        foreach ($addons as $addon) {
            if (isset($addon['cat_id'], $addon['replace']) && $addon['replace'] == 1) {

                // Match by pivot_ing_category_id instead of id
                $matchedCategory = $ingredients->firstWhere('id', $addon['cat_id']);
                if (! empty($matchedCategory->pivot->default_ing)) {
                    $removedIngredients[] = [
                        'quantity' => (int) ($addon['quantity'] ?? 1),
                        'size_id' => (int) ($addon['size_id'] ?? 1),
                        'ing_id' => (int) $matchedCategory->pivot->default_ing,
                    ];
                }
            }
        }

        $ingredients = $request->ingredients ? json_decode($request->ingredients, true) : [];
        $data['addons'] = array_merge($addons, $ingredients);
        $data['removed_ingredients'] = $removedIngredients ? $removedIngredients : null;
        $data['product_variant_id'] = $request->product_variant_id;
        $data['quantity'] = $request->qty;
        $data['ing_id'] = $request->ingredients ? json_decode($request->ingredients, true) : null;
        $data['branch_id'] = Auth::user()->branchstaff()->first()?->branch_id;
        // dd($data);
        $this->cartRepository->create($data);

        return response()->json(['message' => 'Cart updated successfully']);
    }

    public function updateTax(Request $request)
    {
        $cartId = $request->cart_id;
        $this->cartRepository->updateTax($request->all());

        return response()->json(['message' => 'Cart updated successfully']);
    }

    public function removecart(Request $request)
    {
        $cartId = $request->cart_id;
        $this->cartRepository->delete($cartId);

        return response()->json(['message' => 'Cart item removed successfully']);
    }

    public function getCart()
    {
        $couponCode = null;
        $order_platform = 'pos';
        // dd($order_platform );
        $cart = $this->cartRepository->all($couponCode, $order_platform);

        return view('admin.staff.partials.cart', compact('cart'))->render();

        // return response()->json(['cart' => $cart]);
    }

    public function getCartRecipt(Request $request)
    {
        $order_id = $request->order_id;
        // $order_id=4;
        $order = $this->orderRepository->find($order_id);
        $cart = $order->items;
        $printerService = new StationPrintService;
        $ip = '192.168.18.200';
        // $printerService->printReceipt($order,$ip);
        // $printerService->printOrderItems($order);
        // $response = $printerService->handlePrinterJob($order, $ip);
        $printerService = new StationPrintService;
        $getreciptData = $this->printReceiptOrderLocal($order_id);
        $getKOT = $printerService->printOrderItems($order);

        return response()->json(['getreciptData' => $getreciptData, 'getKOT' => $getKOT]);

        // if ($response['status']) {
        //     // Print success SweetAlert or response message
        //     echo $response['message'];
        // } else {
        //     // Handle error (show message or log)
        //     echo 'Error: '.$response['error'];
        // }
        // return view('admin.staff.partials.recipt', compact('cart', 'order_id', 'order'))->render();
    }

    public function completePayment(Request $request)
    {
        // /dd($request->all());
        $data = $request->validate([
            'customer_name' => 'string|nullable',
            'customer_phone' => 'string|nullable',
            'customer_email' => 'email|nullable',
            'payment_method' => 'string|nullable',
            'change_return' => 'numeric|nullable',
            'order_type' => 'string|nullable',
        ]);
        $data['branch_id'] = Auth::user()->branchstaff()->first()?->branch_id;

        // $customer = $this->customerRepository->createinvoiceCustomer($data);
        $orderData = [
            'customer_id' => $request->customer_id ? $request->customer_id : 0,
            'dining_type' => $data['order_type'] == 'dine_in' ? Order::DINE_IN : Order::TAKE_AWAY,
            'delivery_type' => $data['order_type'] == 'dine_in' ? Order::PICKUP : Order::DELIVERY,
            'branch_id' => $data['branch_id'],
            'customer_name' => $data['customer_name'],
            'customer_phone' => $data['customer_phone'],
            'customer_email' => $data['customer_email'],
            'staff_id' => Auth::user()->id,
            // 'total_amount' => $this->cartRepository->totalAmount(),
            'paymentMethod' => $data['payment_method'],
            'change_return' => $data['change_return'],
            'platform' => 'pos',
            'card_number' => $request->card_number,
            'address_id' => 0,
            // 'status' => 'pending',
        ];
        $couponCode = $request->coupon_code ?? null;
        $order_platform = 'pos';
        $cart = $this->cartRepository->all($couponCode, $order_platform);
        $order = $this->orderRepository->create($orderData, $cart);

        // Payment processing logic here (e.g., integrating with a payment gateway)
        if ($order) {
            return response()->json(['status' => 'success', 'message' => 'Payment completed successfully', 'order_id' => $order->id]);
        }
    }

    public function updatequantity(Request $request)
    {
        $cartId = $request->cart_id;
        $qty = $request->qty;
        $data = ['quantity' => $qty, 'id' => $cartId, 'action' => $request->action];
        $this->cartRepository->updateCartQuantity($data);

        return response()->json(['message' => 'Cart updated successfully']);
    }

    public function editcart(Request $request)
    {
        $cartId = $request->cart_id;
        $cart = $this->cartRepository->finditem($cartId);

        $ingrediants = IngredientProductVariant::with([
            'ingredientCategory.ingredients.sizes',
            'defaultIngredient',
            'ingredient',
        ])->where('product_variant_id', $request->variant_id)->get();

        $variant = ProductVariant::with('sizes')->find($request->variant_id);

        $product = Product::with(['addons.addonable'])->find($request->product_id);
        $productVariants = $product->variants ?? collect();
        $productSizes = $productVariants
            ->map(function ($variant) {
                $size = $variant->sizes;

                return [
                    'variant_id' => $variant->id,
                    'size_id' => $size->id ?? null,
                    'code' => $size->code ?? null,
                    'price' => $variant->price ?? 0,
                ];
            })
            ->values();

        $addonIngredients = $product->addons->map(function ($addon) use ($variant) {
            $addonable = $addon->addonable;

            if (! $addonable) {
                return null;
            }

            return [
                'id' => $addonable->id,
                'addon_id' => $addon->id,
                'name' => $addonable->name,
                'type' => 'optional',
                'deftault' => null,
                'others' => $addonable->ingredients->map(function ($ingredient) use ($variant) {
                    return [
                        'ing_id' => $ingredient->ing_id,
                        'name' => $ingredient->ing_name,
                        'size_id' => $variant->sizes?->id,
                        'unit' => $ingredient->unit->symbol ?? '',
                        'image' => $ingredient->main_image,
                        'price' => optional($ingredient->sizes)
                            ->where('size_id', $variant->sizes?->id)
                            ->first()
                            ->price ?? 0,
                    ];
                })->values() ?? [],
            ];
        })->filter()->values();

        $html = view('admin.staff.partials.edit-variant-options', compact('ingrediants', 'addonIngredients', 'variant', 'cart'))->render();

        return response()->json([
            'html' => $html,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'image' => $product->main_image,
                'sizes' => $productSizes,
                'price' => $variant->price ?? $product->price,
                'qty' => $cart->quantity ?? 1,
            ],
            'variant_id' => $variant->id,
            'size_id' => $variant->sizes?->id,
            'cart_id' => $cart->id,
        ]);
    }

    public function updatecart(Request $request)
    {
        $addons = $request->addons ? json_decode($request->addons, true) : [];
        $ingredients = $request->ingredients ? json_decode($request->ingredients, true) : [];
        $data['addons'] = array_merge($addons, $ingredients);
        $data['removed_ingredients'] = $request->remove_ingredients ? json_decode($request->remove_ingredients, true) : null;
        $data['product_variant_id'] = $request->product_variant_id;
        $data['quantity'] = $request->qty;
        $data['ing_id'] = $request->ingredients ? json_decode($request->ingredients, true) : null;
        $data['id'] = $request->cart_id;
        // dump($data);
        $this->cartRepository->update($data);

        return response()->json(['message' => 'Cart updated successfully']);
    }

    public function updateNote(Request $request)
    {
        $cartId = $request->cart_id;
        $note = $request->note;
        $data = ['notes' => $note, 'id' => $cartId];
        // dd($data);
        $this->cartRepository->updateCartNote($data);

        return response()->json(['message' => 'Cart updated successfully']);
    }

    public function updateOrderNote(Request $request)
    {
        $cartId = $request->cart_id;
        $note = $request->note;
        $data = ['order_note' => $note, 'id' => $cartId];
        $this->cartRepository->updateCartOrderNote($data);

        return response()->json(['message' => 'Cart updated successfully']);
    }

    public function orders(Request $request)
    {
        $orders = $this->orderRepository->orderPOS();
        $branchId = Auth::user()->branchstaff()->first()?->branch_id ?? Auth::user()->branches[0]->id ?? null;
        $orders = $orders->where('branch_id', $branchId);
        if (auth()->user()->role_id == User::ROLE_WAITER) {
            $orders = $orders->where('staff_id', auth()->user()->id);
        }
        // if ($branchId) {
        //     $orders = $orders->where('branch_id', $branchId);
        // }

        if ($request->status && $request->status !== 'all') {
            $orders = $orders->where('status', $request->status);
        }

        // 🔹 Filters from form
        if ($request->filled('ref_no')) {
            $orders->where('order_uid', 'like', "%{$request->ref_no}%");
        }

        if ($request->filled('phone') || $request->filled('name')) {
            $orders->whereHas('customer', function ($q) use ($request) {
                if ($request->filled('phone')) {
                    $q->where('phone', 'like', "%{$request->phone}%");
                }

                if ($request->filled('name')) {
                    $q->where(function ($subQuery) use ($request) {
                        $subQuery->where('first_name', 'like', "%{$request->name}%")
                            ->orWhere('last_name', 'like', "%{$request->name}%");
                    });
                }
            });
        }
        if ($request->filled('status') && $request->status !== 'all') {
            $orders->where('status', $request->status);
        }

        if (isset($request->payment_status) && $request->payment_status !== 'all') {
            $orders->whereHas('payment', function ($query) use ($request) {
                $query->where('payment_method', $request->payment_status);
            });
        }

        $orders = $orders->orderBy('id', 'desc');

//         $role_id = auth()->user()->role_id;

//         if($role_id == User::ROLE_WAITER){
//             $roleTemp = [$role_id];
//         }else{
// $roleTemp= [
// User::ROLE_ADMIN,
// User::ROLE_BRANCHADMIN,
// User::ROLE_ACCOUNTANT,
// User::ROLE_WAITER];
//         }

        // ✅ Finally, apply pagination
        $orders = $orders->paginate(10);
        if (auth()->user()->role_id == User::ROLE_WAITER) {
              $statusCounts = [
            'new' => Order::where('branch_id', $branchId)->where('status', 'pending')
            ->where('staff_id', auth()->user()->id)
            ->count(),
            'processing' => Order::where('branch_id', $branchId)->whereIn('status', ['accepted', 'processing'])
            ->where('staff_id', auth()->user()->id)
            ->count(),
            'preparing' => Order::where('branch_id', $branchId)->where('status', 'preparing')
            ->where('staff_id', auth()->user()->id)
            ->count(),
            'dispatched' => Order::where('branch_id', $branchId)->where('status', 'dispatched')
            ->where('staff_id', auth()->user()->id)
            ->count(),
            'completed' => Order::where('branch_id', $branchId)->where('status', 'completed')
            ->where('staff_id', auth()->user()->id)
            ->count(),
            'all' => Order::where('branch_id', $branchId) ->where('staff_id', auth()->user()->id)->count(),
        ];

        }else{
              $statusCounts = [
            'new' => Order::where('branch_id', $branchId)->where('status', 'pending')->count(),
            'processing' => Order::where('branch_id', $branchId)->whereIn('status', ['accepted', 'processing'])->count(),
            'preparing' => Order::where('branch_id', $branchId)->where('status', 'preparing')->count(),
            'dispatched' => Order::where('branch_id', $branchId)->where('status', 'dispatched')->count(),
            'completed' => Order::where('branch_id', $branchId)->where('status', 'completed')->count(),
            'all' => Order::where('branch_id', $branchId)->count(),
        ];

        }


        return view('admin.staff.partials.orders', compact('orders', 'statusCounts'));
    }

    public function orderlist(Request $request)
    {
        $orders = $this->orderRepository->orderPOS();
        $branchId = Auth::user()->branchstaff()->first()?->branch_id;

        // if ($branchId) {
        //     $orders = $orders->where('branch_id', $branchId);
        // }

        // if ($request->status) {
        //     $orders = $orders->where('status', $request->status);
        // }

        // if ($request->payment_status) {
        //     $orders = $orders->where('payment_status', $request->payment_status);
        // }

        // if ($request->ref_no) {
        //     $orders = $orders->where('ref_no', 'like', '%'.$request->ref_no.'%');
        // }

        // if ($request->customer_ref) {
        //     $orders = $orders->where('customer_ref', 'like', '%'.$request->customer_ref.'%');
        // }

        // if ($request->phone) {
        //     $orders = $orders->whereHas('customer', function ($query) use ($request) {
        //         $query->where('phone', 'like', '%'.$request->phone.'%');
        //     });
        // }

        // if ($request->name) {
        //     $orders = $orders->whereHas('customer', function ($query) use ($request) {
        //         $query->where('name', 'like', '%'.$request->name.'%');
        //     });
        // }

        $branchId = Auth::user()->branchstaff()->first()?->branch_id ?? Auth::user()->branches[0]->id ?? null;
        $orders = $orders->where('branch_id', $branchId);
        if (auth()->user()->role_id == User::ROLE_WAITER) {
            $orders = $orders->where('staff_id', auth()->user()->id);
        }
        $orders = $orders->orderBy('id', 'desc');

        //  Finally, apply pagination
        $orders = $orders->paginate(10);

        return view('admin.staff.partials.order-list', compact('orders'));
    }

    public function viewOrder($id)
    {

        $order = $this->orderRepository->find($id);

        // app(\App\Services\BranchStockService::class)->handleIngredientOut($order);
        // event(new \App\Events\OrderAccepted($order));
        $printerService = new StationPrintService;
        $getreciptData = $this->printReceiptOrderLocal($id);
        $getKOT = $printerService->printOrderItems($order);

        return view('admin.staff.partials.view-order', compact('order', 'getreciptData', 'getKOT'));
    }

    public function inventories()
    {
        $branchId = auth()->user()->branchstaff()->first()?->branch_id;
        $inventories = $this->ingredientRepository->getBranchIngredients($branchId);

        $inventories = array_filter($inventories, fn ($ing) => $ing['is_quantify'] == 1);

        return view('admin.staff.partials.inventories', compact('inventories', 'branchId'));
    }

    //  cashout / refund
    public function CashoutRefund()
    {

        $branchId = auth()->user()->branchstaff()->first()?->branch_id;
        $ingredients = $this->ingredientRepository->getBranchIngredients($branchId);
        $ingredients = array_filter($ingredients, fn ($ing) => $ing['ing_type'] === 'standard');
        $cashoutTransactions = $this->ingredientRepository->getCashoutTransactions($branchId);

        return view('admin.staff.partials.cashout-refund', compact('branchId', 'ingredients', 'cashoutTransactions'));
    }

    public function getOrderAmount(Request $request)
    {
        $orderRef = $request->order_ref;

        //  Check if order exists
        $order = Order::where('order_uid', $orderRef)->first();

        if (! $order) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'No order found with that reference number.',
                'amount' => null,
            ]);
        }
        //  Check if refund already exists
        $alreadyRefunded = CashoutTransaction::where('order_ref', $orderRef)->exists();

        if ($alreadyRefunded) {
            return response()->json([
                'status' => 'refunded',
                'message' => 'This order has already been refunded.',
                'amount' => null,
            ]);
        }

        // Return valid order
        return response()->json([
            'status' => 'found',
            'message' => 'Order found.',
            'amount' => $order->total_amount,
        ]);
    }

    public function storeCashoutRefund(Request $request)
    {

        try {
            $request->validate([
                'type' => 'required|in:cashout,refund',
                'category' => 'nullable|string',
                'ingredient_ids' => 'nullable',
                'item_name' => 'nullable|string|max:255',
                'amount' => 'required|numeric|min:0.01',
                'order_ref' => 'nullable',
                'remarks' => 'nullable|string|max:500',
            ]);
            $user = auth()->user();
            $branchId = $user->branchstaff()->first()?->branch_id;

            if (! $branchId) {
                return response()->json(['success' => false, 'message' => 'Branch not found for this user.']);
            }
            $ing_ids = is_array($request->ingredient_ids) ? json_encode($request->ingredient_ids) : $request->ingredient_ids;

            //  Save cashout or refund transaction
            $transaction = new CashoutTransaction;
            $transaction->branch_id = $branchId;
            $transaction->type = $request->type;              // 'cashout' or 'refund'
            $transaction->category = $request->category;          // 'ingredient', 'other', or 'refund'
            $transaction->ingredient_id = $ing_ids ?? null;
            $transaction->item_name = $request->item_name ?? null;
            $transaction->amount = $request->amount ?? null;
            $transaction->order_ref = $request->order_ref ?? null;
            $transaction->remarks = $request->remarks ?? null;
            $transaction->save();
            Order::where('order_uid', $request->order_ref)->update(['status' => Order::STATUS_REFUNDED]);

            return response()->json([
                'success' => true,
                'message' => ucfirst($request->type).' recorded successfully!',
                'data' => $transaction,
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        // return true;
        $status = $request->status;
        $reason = $request->reason;
        $data = ['status' => $status, 'reason' => $reason];
        // dd($data);
        $this->orderRepository->updateStatus($id, $data);

        return response()->json(['message' => 'Order updated successfully']);
    }

    public function logoutpos()
    {
        Auth::logout();

        return redirect()->route('staff.login');
    }

    public function shiftInventory()
    {
        $user = auth()->user();
        if ($user && ($user->role_id == 4  || $user->role_id == 6)) {

            return redirect()->route('pos.index');
        }
        $ingredients = $this->ingredientRepository->all();
        $staffId = auth()->id();

        $openingShift = ShiftCashNote::where('user_id', $staffId)
            ->where('entry_type', 'opening')
            ->whereDate('created_at', Carbon::today())
            ->first();

        $closingShift = ShiftCashNote::where('user_id', $staffId)
            ->where('entry_type', 'closing')
            ->whereDate('created_at', Carbon::today())
            ->first();

        $shiftStarted = ($openingShift && ! $closingShift) ? true : false;

        return view('admin.staff.partials.shift_inventory', compact('ingredients', 'shiftStarted'));
    }

    public function storeShiftInventory(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branchstaff()->first()?->branch_id;
        $shiftId = $user->shift()->latest()->first()?->id;
        $totalAmount = 0;
        $today = now()->toDateString();

        // 🪙 Cash Notes
        if ($request->has('cash_notes')) {
            foreach ($request->cash_notes as $note => $qty) {
                if ($qty > 0) {
                    $amount = $note * $qty;
                    $totalAmount += $amount;

                    ShiftCashNote::create([
                        'shift_id' => $shiftId,
                        'user_id' => $user->id,
                        'branch_id' => $branchId,
                        'note_value' => $note,
                        'entry_type' => $request->input('action'),
                        'quantity' => $qty,
                        'total' => $amount,
                    ]);
                }
            }
        }

        // 🧂 Ingredients
        if ($request->has('ing_id') && $request->has('ingredients')) {
            foreach ($request->ing_id as $ingredientId) {
                $qty = $request->ingredients[$ingredientId] ?? 0;

                if ((int) $qty > 0) {
                    ShiftIngredient::create([
                        'shift_id' => $shiftId,
                        'user_id' => $user->id,
                        'branch_id' => $branchId,
                        'ingredient_id' => $ingredientId,
                        'entry_type' => $request->input('action'),
                        'quantity' => (int) $qty,
                    ]);
                }
            }
        }

        ShiftUser::where('user_id', $user->id)
            ->where('branch_id', $branchId)
            ->where('status', 'open')
            ->update(['status' => 'closed']);

        // 2. Ab naya shift create/update karo
        $shiftUser = ShiftUser::updateOrCreate(
            [
                'user_id' => $user->id,
                'branch_id' => $branchId,
                'shift_date' => $today,
            ],
            [
                'shift_id' => $shiftId,
                'last_amount' => $totalAmount,
                'status' => $request->input('action') == 'closing' ? 'closed' : 'open',
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Shift inventory saved successfully for today.',
            'shift_status' => $shiftUser->status,
            'total_amount' => $shiftUser->last_amount,
            'date' => $shiftUser->shift_date,
        ]);
    }

    public function cashCount()
    {
        $staffId = auth()->id();
        $branchId = auth()->user()->branchstaff()->first()?->branch_id;

        // ✅ Get current active opening shift
        $currentOpening = ShiftCashNote::where('user_id', $staffId)
            ->where('branch_id', $branchId)
            ->where('entry_type', 'opening')
            ->latest('created_at')
            ->first();

        // ✅ Get closing shift after that (if any)
        $closingShift = ShiftCashNote::where('user_id', $staffId)
            ->where('branch_id', $branchId)
            ->where('entry_type', 'closing')
            ->where('created_at', '>', optional($currentOpening)->created_at)
            ->first();

        // ✅ Get notes between current opening & closing (or till now)
        $todayNotes = collect();
        if ($currentOpening) {
            $query = ShiftCashNote::where('user_id', $staffId)
                ->where('branch_id', $branchId)
                ->where('entry_type', 'opening')
                ->where('created_at', '>=', $currentOpening->created_at);

            if ($closingShift) {
                $query->where('created_at', '<=', $closingShift->created_at);
            }

            $todayNotes = $query->get(['note_value', 'quantity']);
        }

        $previousNotes = collect();
        $cashDifference = 0;

        if ($currentOpening) {
            // 🔹 Get the most recent closing record before the current opening
            $previousClosing = ShiftCashNote::where('branch_id', $branchId)
                ->where('entry_type', 'closing')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($previousClosing) {
                // 🔹 Get all notes for that closing time (same date as last closing)
                $previousNotes = ShiftCashNote::where('branch_id', $branchId)
                    ->where('entry_type', 'closing')
                    ->whereDate('created_at', $previousClosing->created_at->toDateString())
                    ->get();

                // 🔹 Calculate total from those notes
                $previousTotal = $previousNotes->sum(fn ($n) => $n->note_value * $n->quantity);
            } else {
                $previousNotes = collect();
                $previousTotal = 0;
            }

            // 🔹 Current user’s opening total
            $currentTotal = ShiftCashNote::where('user_id', $staffId)
                ->where('branch_id', $branchId)
                ->where('entry_type', 'opening')
                ->whereDate('created_at', $currentOpening->created_at->toDateString())
                ->sum(\DB::raw('note_value * quantity'));

            // 🔹 Calculate difference
            $cashDifference = $currentTotal - $previousTotal;
        }

        // ✅ Total current cash
        $total = $todayNotes->sum(fn ($n) => $n->note_value * $n->quantity);

        // ✅ Render modal
        $html = view('admin.staff.partials.cash-count-modal', compact(
            'todayNotes',
            'previousNotes',
            'total'
        ))->render();

        return response()->json(['html' => $html]);
    }

    public function stockCount()
    {
        $staffId = auth()->id();
        $branchId = auth()->user()->branchstaff()->first()?->branch_id;
        // ✅ 1. Get latest opening shift (regardless of date)
        $currentOpening = ShiftIngredient::where('user_id', $staffId)
            ->where('branch_id', $branchId)
            ->where('entry_type', 'opening')
            ->latest('created_at')
            ->first();

        // ✅ 2. Get the corresponding closing shift (if it exists after this opening)
        $closingShift = ShiftIngredient::where('branch_id', $branchId)
            ->where('entry_type', 'closing')
            ->where('created_at', '>', optional($currentOpening)->created_at)
            ->orderBy('created_at', 'asc')
            ->first();

        // ✅ 3. Get previous shift (the one before this opening)
        $previousShift = ShiftIngredient::where('branch_id', $branchId)
            ->where('entry_type', 'closing')
            ->where('created_at', '<', optional($currentOpening)->created_at)
            ->orderBy('created_at', 'desc')
            ->first();

        // ✅ 4. Get current shift ingredients (still active even if midnight passed)
        $currentIngredients = collect();
        if ($currentOpening) {
            $query = ShiftIngredient::with('ingredient')
                ->where('branch_id', $branchId)
                ->where('user_id', $staffId)
               // ->where('shift_id', $currentOpening->shift_id)
                ->where('entry_type', 'opening');

            if ($closingShift) {

                $query->where('created_at', '<=', $closingShift->created_at);
            } else {

                $query->whereDate('created_at', '>=', $currentOpening->created_at->toDateString());
            }

            $currentIngredients = $query->get();
        }

        // ✅ 5. Get previous shift ingredients
        $previousIngredients = collect();
        if ($previousShift) {
            $previousIngredients = ShiftIngredient::with('ingredient')
                ->where('shift_id', $previousShift->shift_id)
                ->where('entry_type', 'closing')
                ->get();
        }

        // ✅ 6. Prepare summary
        $summary = $currentIngredients
            ->groupBy('ingredient_id')
            ->map(function ($group) use ($previousIngredients) {
                $currentQty = $group->sum('quantity');
                $item = $group->first();

                $prev = $previousIngredients->firstWhere('ingredient_id', $item->ingredient_id);
                $prevQty = $prev ? $prev->quantity : 0;
                $diff = $currentQty - $prevQty;

                return [
                    'ingredient_name' => $item->ingredient->first()->ing_name ?? 'Unknown ',
                    'previous_qty' => $prevQty,
                    'current_qty' => $currentQty,
                    'difference' => $diff,
                ];
            })
            ->values();

        //    /dd($summary);

        return view('admin.staff.partials.stock-summary', compact('summary'));
    }

    public function printStickerOrder($id)
    {
        $printerService = new StationPrintService;
        $order = $this->orderRepository->find($id);
        $cart = $order->items;

        // dd($order);
        return $printerService->StationPrint($order);
        // return redirect()->back()->with(['success' => "Kitchen order printed successfully", 'message' => 'Kitchen order printed successfully']);

    }

    // Test functions
    public function testprinter($id)
    {

        $order = $this->orderRepository->find($id);
        $ip = '192.168.18.200';
        $port = 9100;
        $itemsToPrint = $order->items;

        $orderData = [
            'printerIP' => $ip,
            'printerPort' => $port,
            'header' => 'KITCHEN ORDER',
            'station' => 'Hot',
            'order' => [
                'id' => $order->order_uid,
                'date' => $order->created_at->format('d M Y h:i A'),
                'cashier' => $order->staff->first_name.' '.$order->staff->last_name,
                'type' => $order->order_type_label,
            ],
            'items' => $itemsToPrint->map(function ($item) {
                return [
                    'name' => $item->productVariant->product->name ?? 'N/A',
                    'qty' => $item->quantity ?? 1,
                    'size' => $item->productVariant->sizes->name ?? '-',
                    'notes' => $item->notes,
                ];
            })->toArray(),
        ];

        try {
            $client = new Client([
                'timeout' => 10, // seconds
                'connect_timeout' => 5,
            ]);
            $ngrokUrl = UrlSetting::get('printer_ngrok_url');

            $response = $client->post($ngrokUrl.'/Loop/public/api/getprinter', [
                'json' => $orderData,
            ]);

            $body = json_decode($response->getBody(), true);
            dd($body);

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            dd([
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }

        // Simple view with a button
        //  return view('printer.test');
    }

    public function getprinter(Request $request)
    {

        return response()->json($request->all());
    }

    public function saveNgrokUrl(Request $request)
    {
        $ngrokUrl = $request->input('ngrok_url');
        Log::info('Ngrok URL received: '.$ngrokUrl);

        // Save to database or configuration
        // Example: Config::set('app.ngrok_url', $ngrokUrl);

        // return response()->json(['success' => true]);
    }

    public function testPrint()
    {
        $ip = '192.168.18.200';
        $port = 9100;

        // Example data
        $order = (object) [
            'id' => 27816,
            'drink_name' => 'ABCDEF',
            'size' => '5',
            'remarks' => 'test test test',
        ];

        // 1. Render Blade view as HTML
        $html = view('stickers.dynamic', compact('order'))->render();

        // 2. Convert HTML to plain ESC/POS
        $payload = $this->convertHtmlToEscPos($html);

        // 3. Send to printer
        $result = $this->sendToPrinter($ip, $port, $payload);

        return response()->json($result);
    }

    private function convertHtmlToEscPos($html)
    {
        $esc = chr(27);
        $gs = chr(29);
        $payload = $esc.'@'; // initialize printer

        // Replace basic HTML formatting with ESC/POS
        $search = [
            '/<br\s*\/?>/i',
            '/<b>/i',
            '/<\/b>/i',
            '/<h[1-6][^>]*>/i',
            '/<\/h[1-6]>/i',
            '/<center>/i',
            '/<\/center>/i',
            '/<[^>]+>/',
        ];
        $replace = [
            "\n",
            $esc.'E'.chr(1),
            $esc.'E'.chr(0),
            $esc.'E'.chr(1),
            $esc.'E'.chr(0)."\n",
            $esc.'a'.chr(1),
            $esc.'a'.chr(0),
            '',
        ];

        $text = preg_replace($search, $replace, $html);

        $payload .= trim($text);
        $payload .= "\n\n";
        $payload .= $esc.'d'.chr(4); // feed lines
        $payload .= $gs.'V'.chr(1); // full cut

        return $payload;
    }

    private function sendToPrinter($ip, $port, $data)
    {
        try {
            $fp = @fsockopen($ip, $port, $errno, $errstr, 5);
            if (! $fp) {
                return ['success' => false, 'message' => "Socket failed: $errstr ($errno)"];
            }

            stream_set_timeout($fp, 2);
            fwrite($fp, $data);
            fclose($fp);

            return ['success' => true, 'message' => "Print sent to {$ip}:{$port}"];
        } catch (\Throwable $th) {
            return ['success' => false, 'message' => $th->getMessage()];
        }
    }

    public function printReceiptOrderLocal($id)
    {

        $printerService = new StationPrintService;
        $getIP = BranchSetting::where('branch_id', Auth::user()->branchstaff()->first()?->branch_id ?? null)->first();
        $ip = $getIP->printer_ip ?? '192.168.18.200';
        $order = $this->orderRepository->find($id);
        $cart = $order->items;
        $orderData = [
            'order_uid' => $order->order_uid,
            'created_at' => $order->created_at->toDateTimeString(),
            'customer_name' => $order->customer_name,
            'staff' => [
                'first_name' => $order->staff->first_name ?? '',
                'last_name' => $order->staff->last_name ?? '',
            ],
            'branch' => [
                'address' => $order->branch->address ?? '',
                'phone' => $order->branch->phone ?? '',
                'ip' => $ip,
            ],
            'queue_number' => $order->queue_number,
            'items' => $order->items->map(function ($item) {
                $sizeid = $item->productVariant->sizes->id ?? null;
                $addonDetail = getIngredientDetails($item->addon_id, true, $sizeid);
                $addonArray = json_decode($item->addon_id, true) ?? [];

                return [

                    'size_name' => $item->productVariant->sizes->name ?? 'N/A',
                    'addonArray' => $addonArray,
                    'addon_details' => $addonDetail,
                    'product_name' => $item->productVariant->product->name ?? 'N/A',
                    'category_name' => $item->productVariant->product->category->name ?? 'N/A',
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'notes' => $item->notes,
                ];
            }),
            'tax' => $order->tax,
            'discount' => $order->discount,
            'final_amount' => $order->final_amount,
            'payment' => [
                'payment_method' => $order->payment->payment_method ?? 'N/A',
            ],
            'order_note' => $order->order_note,
            'change_return' => $order->change_return,
            'tax_percent' => $order->tax_percent,

        ];

        return $orderData;
        // Send GET request to local printer script
        //      $response = Http::get('http://localhost/PrinEscpos/Billrecipt.php', [
        //         'order' => json_encode($orderData)
        //     ]);
        // dd($response->body());

        // $printerService->printReceipt($order, $ip);

        return redirect()->back()->with(['success' => 'Receipt printed successfully', 'message' => 'Receipt printed successfully']);

    }

    public function printReceiptOrder($id)
    {

        $printerService = new StationPrintService;
        $getIP = BranchSetting::where('branch_id', Auth::user()->branchstaff()->first()?->branch_id ?? null)->first();
        $ip = $getIP->printer_ip ?? '192.168.18.200';
        $order = $this->orderRepository->find($id);
        $cart = $order->items;

        $printerService->printReceipt($order, $ip);

        return redirect()->back()->with(['success' => 'Receipt printed successfully', 'message' => 'Receipt printed successfully']);

    }

    public function printKitchenOrderLocal($id)
    {
        $printerService = new StationPrintService;
        $order = $this->orderRepository->find($id);
        $cart = $order->items;
        $printerService->printOrderItems($order);

        // return redirect()->back()->with(['success' => 'Kitchen order printed successfully', 'message' => 'Kitchen order printed successfully']);

    }

    public function printKitchenOrder($id)
    {
        $printerService = new StationPrintService;
        $order = $this->orderRepository->find($id);
        $cart = $order->items;
        $printerService->printOrderItems($order);

        return redirect()->back()->with(['success' => 'Kitchen order printed successfully', 'message' => 'Kitchen order printed successfully']);

    }
    // public function printStickerOrder($id)
    // {
    //          $printerService = new StationPrintService();
    //         $order = $this->orderRepository->find($id);
    //          $cart = $order->items;
    //       $printerService->printOrderItems($order);
    //       return redirect()->back()->with(['success' => "Kitchen order printed successfully", 'message' => 'Kitchen order printed successfully']);

    // }
    public function printReceipt()
    {
        $ip = '192.168.18.200';
        $port = 9100;
        $order_id = 4;

        $order = $this->orderRepository->find($order_id);
        $cart = $order->items;

        try {
            $connector = new NetworkPrintConnector($ip, $port);
            $printer = new Printer($connector);
            // dd($printer);
            /* === LOGO (Safe Version) === */
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            // $logoPath = public_path('001.png'); // use converted PNG

            // if (file_exists($logoPath)) {
            //     try {
            //         $logo = EscposImage::load($logoPath, false);
            //         $printer->bitImage($logo);
            //         $printer->feed();
            //     } catch (\Exception $e) {
            //         $printer->text("(Logo not available)\n");
            //     }
            // } else {
            //     $printer->text("(Logo file missing)\n");
            // }

            /* === HEADER === */
            $printer->setEmphasis(true);
            $printer->setTextSize(2, 2);
            $printer->text("LOOP\n");
            $printer->setTextSize(1, 1);
            $printer->setEmphasis(false);
            $printer->text('Order#: '.$order->order_uid."\n");
            $printer->text(str_repeat('-', 40)."\n");

            /* === ORDER INFO === */
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text('Invoice #: '.$order->order_uid."\n");
            $printer->text("Customer : Self Customer\n");
            $printer->text('Date     : '.$order->created_at->format('d M Y  h:i A')."\n");
            $printer->text('Server   : '.($order->user->name ?? 'N/A')."\n");
            $printer->text('Type     : '.strtoupper($order->order_type ?? 'Take Away')."\n");
            $printer->text(str_repeat('-', 40)."\n");

            /* === ITEM TABLE === */
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setEmphasis(true);
            $printer->text(sprintf("%-4s %-18s %6s %8s\n", 'Qty', 'Item', 'Rate', 'Amount'));
            $printer->setEmphasis(false);
            $printer->text(str_repeat('-', 40)."\n");

            $subtotal = 0;
            foreach ($cart as $item) {
                $name = strtoupper($item->productVariant->product->name ?? 'N/A');
                $qty = $item->quantity ?? 1;
                $rate = number_format($item->price, 0);
                $amount = number_format($item->price * $qty, 0);
                $subtotal += ($item->price * $qty);

                $printer->text(sprintf("%-4s %-18s %6s %8s\n", $qty, substr($name, 0, 18), $rate, $amount));

                // Print Addons
                $addonDetail = getIngredientDetails($item->addon_id, true, $item->productVariant->sizes->id ?? null);
                if (! empty($addonDetail)) {
                    foreach ($addonDetail as $addon) {
                        $printer->text('     + '.strtoupper($addon['name'])."\n");
                    }
                }

                // Print Notes
                if (! empty($item->notes)) {
                    $printer->text('     📝 '.$item->notes."\n");
                }
            }

            $printer->text(str_repeat('-', 40)."\n");

            /* === TOTALS === */
            $gst = $order->tax ?? ($subtotal * 0.15);
            $discount = $order->discount_amount ?? 0;
            $net = $order->final_amount ?? ($subtotal + $gst - $discount);

            $printer->setJustification(Printer::JUSTIFY_RIGHT);
            $printer->text(sprintf("%-25s %13.2f\n", 'SubTotal:', $subtotal));
            $printer->text(sprintf("%-25s %13.2f\n", 'GST (15%):', $gst));
            $printer->text(sprintf("%-25s %13.2f\n", 'Discount:', $discount));
            $printer->text(str_repeat('-', 40)."\n");
            $printer->setEmphasis(true);
            $printer->text(sprintf("%-25s %13.2f\n", 'Net Bill:', $net));
            $printer->setEmphasis(false);
            $printer->feed();

            /* === PAYMENT INFO === */
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text('Cash Received : '.number_format($net, 0)."\n");
            $printer->text('Payment Mode  : '.ucfirst($order->payment_mode ?? 'Cash')."\n");
            $printer->feed();

            /* === FOOTER === */
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("!!! FOR THE LOVE OF FOOD !!!\n");
            $printer->setEmphasis(false);
            $printer->text("Powered by: LOOP Technologies\n");
            $printer->text("+92 300 1234567 | www.loop.pk\n");
            $printer->feed(2);
            $printer->cut();
            $printer->close();

            return response()->json(['status' => true, 'message' => 'Receipt printed successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    public function printKitchenReceipt()
    {
        $orderId = 4;
        $ip = '192.168.18.200'; // your kitchen printer IP
        $port = 9100;

        $order = $this->orderRepository->find($orderId);
        $cart = $order->items;

        if (! $order) {
            return response()->json(['status' => false, 'error' => 'Order not found']);
        }

        try {
            $connector = new NetworkPrintConnector($ip, $port);
            $printer = new Printer($connector);

            /* === HEADER === */
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->setTextSize(2, 2);
            $printer->text("KITCHEN ORDER\n");
            $printer->setTextSize(2, 2);
            $printer->text('TOKEN # '.str_pad($order->token_no ?? $order->id, 3, '0', STR_PAD_LEFT)."\n");
            $printer->setTextSize(1, 1);
            $printer->setEmphasis(false);
            $printer->text(str_repeat('=', 40)."\n");

            /* === BASIC INFO === */
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text('Order#: '.$order->order_uid."\n");
            $printer->text('Date   : '.$order->created_at->format('d M Y  h:i A')."\n");
            $printer->text('Server : '.($order->user->name ?? 'N/A')."\n");
            $printer->text('Type   : '.strtoupper($order->order_type ?? 'Take Away')."\n");
            $printer->text(str_repeat('-', 40)."\n");

            /* === ITEMS === */
            $printer->setEmphasis(true);
            $printer->text(sprintf("%-4s %-24s %-8s\n", 'Qty', 'Item', 'Size'));
            $printer->setEmphasis(false);
            $printer->text(str_repeat('-', 40)."\n");

            foreach ($order->items as $item) {
                $name = strtoupper($item->productVariant->product->name ?? 'N/A');
                $qty = $item->quantity ?? 1;
                $size = strtoupper($item->productVariant->sizes->name ?? '-');

                $printer->text(sprintf("%-4s %-24s %-8s\n", $qty, substr($name, 0, 24), $size));

                // Addons
                $addonDetail = getIngredientDetails($item->addon_id, true, $item->productVariant->sizes->id ?? null);
                if (! empty($addonDetail)) {
                    foreach ($addonDetail as $addon) {
                        $printer->text('     + '.strtoupper($addon['name'])."\n");
                    }
                }

                // Notes
                if (! empty($item->notes)) {
                    $printer->text('     📝 '.$item->notes."\n");
                }

                $printer->text(str_repeat('-', 40)."\n");
            }

            /* === FOOTER === */
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("*** KITCHEN COPY ***\n");
            $printer->setEmphasis(false);
            $printer->feed(2);
            $printer->cut();
            $printer->close();

            return response()->json(['status' => true, 'message' => 'Kitchen receipt printed successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Helper: Convert image to PNG for ESC/POS (GD fallback)
     */
    private function convertToPng($path)
    {
        try {
            $image = @imagecreatefromstring(file_get_contents($path));
            if (! $image) {
                return $path; // Return original if GD not available
            }

            $tmpPath = storage_path('app/tmp_logo.png');
            imagepng($image, $tmpPath);
            imagedestroy($image);

            return $tmpPath;
        } catch (\Exception $e) {
            return $path;
        }
    }

    public function searchcustomer(Request $request)
    {
        $query = $request->input('query');

        // Step 1️⃣ — Search in Customers table first
        $customers = Customer::where('first_name', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->get();

        // Step 2️⃣ — If no customers found, search in Orders table
        if ($customers->isEmpty()) {
            $orders = Order::where('customer_name', 'like', "%{$query}%")
                ->orWhere('customer_phone', 'like', "%{$query}%")
                ->get(['customer_name as first_name', 'customer_phone as phone'])
                ->unique('phone') // avoid duplicates if same customer ordered multiple times
                ->values();

            // Convert order results to same format as customers for consistency
            $customers = $orders->map(function ($order) {
                return [
                    'first_name' => $order->first_name,
                    'phone' => $order->phone,
                    'email' => $order->email,
                ];
            });
        }

        return response()->json($customers);
    }

    public function applyCoupon(Request $request)
    {
        $coupon = Coupon::where('code', $request->coupon_code)->first();
        if (! $coupon) {
            return response()->json(['status' => false, 'error' => 'Coupon not found']);
        }
        $cart = $this->cartRepository->all($coupon->code, "pos");

        return response()->json(['status' => true, 'message' => 'Coupon applied successfully.']);
    }

    public function removeCoupon(Request $request)
    {
        $cart = Cart::where('branch_id', Auth::user()->branchstaff()->first()?->branch_id)->first();
        // dd($cart);
        if ($cart == '') {
            return response()->json(['status' => false, 'error' => 'No Cart item found.'], 200);
        }
        $cart->update(['coupon_id' => null]);

        return response()->json(['status' => true, 'message' => 'Coupon removed successfully.']);
    }

    public function closeBranchQueue(Request $request)
    {
        $branchId = $request->branch_id;
        // $shiftId  = $request->shift_id;

        OrderQueue::where('branch_id', $branchId)
            // ->where('shift_id', $shiftId)openShiftBtn
            ->delete();

        return redirect()->back()->with('success', 'Branch queue closed successfully');
    }
    public function orderscounter(Request $request)
    {
       $orders = $this->orderRepository->orderPOS();
       $orders = $orders->where(['branch_id' => Auth::user()->branchstaff()->first()?->branch_id,'status'=>'pending']);
       $count = $orders->count();
       return response()->json(['status' => true, 'count' => $count]);

    }
}
