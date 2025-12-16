<?php

namespace App\Repositories;

use App\Models\Branch;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\CustomerAddress;
use App\Models\LoyaltyPoint;
use App\Models\ModelUserActivityLog;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderTracking;
use App\Models\Payment;
use App\Models\User;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Services\FcmService;
use App\Services\LoyaltyService;
use App\Services\StationPrintService;
use Auth;
use Illuminate\Support\Facades\DB;

class OrderRepository implements OrderRepositoryInterface
{
    public function all()
    {
        return Order::with(['customer', 'address', 'branch', 'coupon', 'items'])->orderBy('id', 'desc')->latest()->get();
    }

    public function orderPOS()
    {
        return Order::with(['customer', 'address', 'branch', 'coupon', 'items'])->orderBy('id', 'desc');
    }

    public function find($id)
    {
        return Order::with(['customer', 'address', 'branch', 'coupon', 'items', 'staff'])->findOrFail($id);
    }

    public function create($data, $cartData)
    {

        // dd($data);
        // dd($cartData);
        // dd($data);
        // dd($cartData);
        // $address = CustomerAddress::where('id', $data['address_id'])->first();
        // if ($address) {
        //     $userLat = $address->latitude;
        //     $userLng = $address->longitude;
        //     $branch_id = 1;
        //     // Haversine Formula to find nearest branch
        //     $nearestBranch = Branch::select(
        //         '*',
        //         DB::raw("6371 * acos(
        //             cos(radians(?))
        //             * cos(radians(lat))
        //             * cos(radians(`long`) - radians(?))
        //             + sin(radians(?))
        //             * sin(radians(lat))
        //         ) AS distance")
        //     )
        //         ->addBinding([$userLat, $userLng, $userLat], 'select')
        //         ->whereNotNull('lat')
        //         ->whereNotNull('long')
        //         ->where('lat', '!=', 0)
        //         ->where('long', '!=', 0)
        //         // ->having('distance', '<=', 10) //
        //         ->orderBy('distance', 'ASC')
        //         ->first();
        //     if ($nearestBranch) {
        //         $branch_id = $nearestBranch->id;
        //     }
        // }
        $branch_id = $data['branch_id'] ?? 1;
        // dd($branch_id );
        //  DB::beginTransaction();
        $userId = auth()->id();
        if (isset($data['platform']) && $data['platform'] == 'pos') {
            $userId = $data['customer_id'] != 0 ? $data['customer_id'] : null;
        }
        if ($data['platform'] == 'pos') {
            $cartItems = Cart::with(['items'])->where('branch_id', $branch_id)->first();
        } else {
            $cartItems = Cart::with(['items'])->where('user_id', $userId)->first();
        }

        // dd( $cartItems);
        $orderdata = [
            'user_id' => $userId,
            'address_id' => $data['address_id'],
            'staff_id' => $data['staff_id'] ?? null,
            'platform' => $data['platform'] ?? null,
            'branch_id' => $branch_id,
            'coupon_id' => $cartItems->coupon_id ?? null,
            'dining_type' => isset($cartData['dining_type']) ? $cartData['dining_type'] : $data['dining_type'],
            'delivery_type' => isset($cartData['delivery_type']) ? $cartData['delivery_type'] : $data['delivery_type'],
            'total_amount' => $cartData['grand_total'],
            'discount' => $cartData['discount'],
            'tax' => $cartData['tax'],
            'delivery_charges' => ($data['platform'] != 'pos') ? $cartData['delivery_charges'] : 0,
            'final_amount' => $cartData['final_total'],
            'change_return' => $data['change_return'] ?? 0,
            'order_note' => $cartData['order_note'] ?? null,
            'status' => $data['platform'] == 'pos' && auth()->user()->role_id == User::ROLE_ACCOUNTANT ? Order::STATUS_PROCESSING : Order::STATUS_PENDING,
        ];
        if ($userId == null) {
            if (isset($data['customer_name']) && $data['customer_name'] != '') {
                $orderdata['customer_name'] = $data['customer_name'];
            }
            if (isset($data['customer_phone']) && $data['customer_phone'] != '') {
                $orderdata['customer_phone'] = $data['customer_phone'];
            }
            if (isset($data['customer_email']) && $data['customer_email'] != '') {
                $orderdata['customer_email'] = $data['customer_email'];
            }
        }
        // dd( $orderdata);
        $order = Order::create($orderdata);
        if ($order) {
            event(new \App\Events\OrderAccepted($order)); // Queue number assignment

            if ($data['platform'] == 'pos') {
                $cartItems = Cart::with(['items'])->where('branch_id', $branch_id)->update(['coupon_id' => null]);
                Payment::create([
                    'order_id' => $order->id,
                    'payment_method' => $data['paymentMethod'],
                    'amount' => $order->final_amount,
                    'card_number' => $data['card_number'] ?? null,
                    'status' => 'paid',
                ]);
            } else {
                $cartItems = Cart::with(['items'])->where('user_id', $userId)->update(['coupon_id' => null]);
                Payment::create([
                    'order_id' => $order->id,
                    'payment_method' => $data['paymentMethod'],
                    'amount' => $order->final_amount,
                    'status' => Payment::STATUS_PENDING,
                ]);
            }
            // $cartid= $cartItems->id;

            foreach ($cartData['cart'] as $cartItem) {
                $cartItemsingle = CartItem::where('id', $cartItem['id'])->first();
                $sizeid = $cartItemsingle->productVariant->sizes->id ?? null;

                // addon details (with price info)
                $addonDetail = getIngredientDetails($cartItemsingle->addon_id, true, $sizeid);
                $addonArray = is_array($cartItemsingle->addon_id)
                ? $cartItemsingle->addon_id
                : json_decode($cartItemsingle->addon_id ?? '[]', true);

                $addonTotal = 0;

                foreach ($addonArray as &$addon) {
                    $ingId = $addon['ing_id'] ?? null;
                    $price = null;

                    if ($ingId) {
                        foreach ($addonDetail as $detail) {
                            if (isset($detail['id']) && $detail['id'] == $ingId) {
                                $price = $detail['price'] ?? null;
                                break;
                            }
                        }
                    }

                    $addon['price'] = $price;

                    if ($price !== null) {
                        $addonTotal += ($price * ($addon['quantity'] ?? 1));
                    }
                }

                $cartItemsingle->addon_id = json_encode($addonArray);
                // $cartItemsingle->save();

                $basePrice = $cartItem['product_price'];
                $quantity = $cartItem['quantity'];
                $subtotal = ($basePrice + $addonTotal) * $quantity;

                $orderItemdata = [
                    'order_id' => $order->id,
                    'product_variant_id' => $cartItem['product_variant_id'],
                    'quantity' => $quantity,
                    'price' => $basePrice,
                    'total_price' => $subtotal,
                    'notes' => $cartItem['notes'],
                    'addon_id' => $cartItemsingle->addon_id,
                    'ing_id' => $cartItemsingle->ing_id ?? null,
                    'removed_ingredient_ids' => $cartItemsingle->removed_ingredient_ids ?? [],
                ];
                $this->updateStatus($order->id, ['status' => $order->status]);

                OrderItem::create($orderItemdata);
                $cartItemsingle->delete(); // delete cartitem

                if (isset($cartItems->coupon_id) && $cartItems->coupon_id != '') {
                    $coupondata = [
                        'user_id' => Auth::id(),
                        'order_id' => $order->id,
                        'coupon_id' => $cartItems->coupon_id,
                    ];
                    CouponUsage::create($coupondata);
                    ModelUserActivityLog::logActivity(
                        Auth::id(),
                        'used Coupon with ID '.$cartItems->coupon_id.' on Order ID '.$order->id
                    );
                }

                // Activity log for coupon usage

            }
            // $cartItems->delete(); //delete cart
            ModelUserActivityLog::logActivity(
                Auth::id(),
                'has created Order with ID '.$order->id
            );
            Cart::where('id', $cartData['cart_id'])->delete();
            // $userdata = User::where("role_id", 1)->first("fcm_token");
            // //dd($userdata->fcm_token);
            // if ($userdata != "") {
            //     app(FcmService::class)->sendNotification(
            //         $userdata->fcm_token,
            //         "New Order Received!",
            //         "Order #{$order->id} has been created.",
            //         [
            //             "order_id" => (string) $order->id,
            //             "type" => "new_order"
            //         ]
            //     );
            // }
            // $points = 66.15; // calculate as per your logic
            // $currentBalance = LoyaltyPoint::where('user_id', $user->id)->latest('id')->value('points_balance') ?? 0;

            // LoyaltyPoint::create([
            //     'user_id' => $user->id,
            //     'order_id' => $order->id,
            //     'points_updated' => $points,
            //     'points_balance' => $currentBalance + $points,
            //     'transaction_type' => 'CREDIT',
            //     'transaction_date' => now(),
            // ]);
            // $points = 66.15; // calculate as per your logic
            // $currentBalance = LoyaltyPoint::where('user_id', $userId)->latest('id')->value('points_balance') ?? 0;

            // LoyaltyPoint::create([
            //     'user_id' => $userId,
            //     'order_id' => $order->id,
            //     'points_updated' => $points,
            //     'points_balance' => $currentBalance + $points,
            //     'transaction_type' => 'CREDIT',
            //     'transaction_date' => now(),
            // ]);
            // Loyalty Points Add
            // app(LoyaltyService::class)->addPoints($userId, $order->id, $order->final_total);

            return $order;
        }
    }

    public function myorders()
    {
        $orders = Order::with(['items'])->where('user_id', Auth::id())->orderBy('id', 'desc')->get();

        if ($orders->isEmpty()) {
            return [
                'orders' => [],
                'total_subtotal' => 0,
            ];
        }

        $response = [];
        foreach ($orders as $order) {
            $orderItems = $order->items->map(function ($item) {
                $productPrice = $item->productVariant->price ?? 0;
                $quantity = $item->quantity ?? 1;
                $sizeId = $item->productVariant->sizes->id ?? null;

                // ✅ Removed Ingredients
                $removedIngredientsDetails = [];
                if (! empty($item->removed_ingredient_ids)) {
                    $removedIngredientsDetails = getIngredientDetails($item->removed_ingredient_ids, true, $sizeId);
                }

                // ✅ Addons
                $addonDetail = [];
                if (! empty($item->addon_id)) {
                    $addonDetail = getIngredientDetails($item->addon_id, true, $sizeId);
                }

                // ✅ Addon subtotal
                $addonArray = json_decode($item->addon_id, true) ?? [];
                $addonSubtotal = 0;

                foreach ($addonDetail as $addon) {
                    $match = collect($addonArray)->firstWhere('ing_id', $addon['id']);
                    $price = $match['price'] ?? 0;
                    $addonSubtotal += $price * ($item->quantity ?? 1);
                }

                return [
                    'id' => $item->id,
                    'product_id' => $item->productVariant->product->id,
                    'product_variant_id' => $item->productVariant->id,
                    'product_price' => $productPrice,
                    'quantity' => $quantity,
                    'notes' => $item->notes,
                    'product' => $item->productVariant->product->name,
                    'product_image' => $item->productVariant->product->main_image,
                    'product_variant' => $item->productVariant->sizes->name,
                    'addon_details' => $addonDetail,
                    'addon_subtotal' => $addonSubtotal,
                    'removed_ingredients_details' => $removedIngredientsDetails,
                    'subtotal' => $item->total_price,
                ];
            });

            // ✅ Order-level data
            $response[] = [
                'order#' => $order->order_uid,
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'created_at' => $order->created_at,
                'dining_type' => $order->dining_type,
                'delivery_type' => $order->delivery_type,
                'grand_total' => $order->total_amount,
                'discount' => $order->discount,
                'tax' => $order->tax,
                'delivery_charges' => $order->delivery_charges,
                'final_total' => $order->final_amount,
                'status' => $order->status,
                'customer_address' => $order->address,
                'items' => $orderItems->values()->all(),
            ];
        }

        return [
            'orders' => $response,

        ];
    }

    public function orderDetail($id)
    {
        $orders = Order::with(['items'])->where('id', $id)->where('user_id', Auth::id())->get();
        // dd($orders);
        $response = [];
        if ($orders->count() == 0) {
            throw new \Exception('Order not found');
        }
        foreach ($orders as $order) {
            $orderItems = $order->items->map(function ($item) {
                $productPrice = $item->productVariant->price ?? 0;
                $quantity = $item->quantity ?? 1;
                $sizeId = $item->productVariant->sizes->id ?? null;

                // ✅ Removed Ingredients
                $removedIngredientsDetails = [];
                if (! empty($item->removed_ingredient_ids)) {
                    $removedIngredientsDetails = getIngredientDetails($item->removed_ingredient_ids, true, $sizeId);
                }

                // ✅ Addons
                $addonDetail = [];
                if (! empty($item->addon_id)) {
                    $addonDetail = getIngredientDetails($item->addon_id, true, $sizeId);
                }

                // ✅ Addon subtotal
                $addonArray = json_decode($item->addon_id, true) ?? [];
                $addonSubtotal = 0;

                foreach ($addonDetail as $addon) {
                    $match = collect($addonArray)->firstWhere('ing_id', $addon['id']);
                    $price = $match['price'] ?? 0;
                    $addonSubtotal += $price * ($item->quantity ?? 1);
                }

                return [
                    'id' => $item->id,
                    'product_id' => $item->productVariant->product->id,
                    'product_variant_id' => $item->productVariant->id,
                    'product_price' => $productPrice,
                    'quantity' => $quantity,
                    'notes' => $item->notes,
                    'product' => $item->productVariant->product->name,
                    'product_image' => $item->productVariant->product->main_image,
                    'product_variant' => $item->productVariant->sizes->name,
                    'addon_details' => $addonDetail,
                    'addon_subtotal' => $addonSubtotal,
                    'removed_ingredients_details' => $removedIngredientsDetails,
                    'subtotal' => $item->total_price,
                ];
            });

            // ✅ Order-level data
            $response[] = [
                'order#' => $order->order_uid,
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'created_at' => $order->created_at,
                'dining_type' => $order->dining_type,
                'delivery_type' => $order->delivery_type,
                'grand_total' => $order->total_amount,
                'discount' => $order->discount,
                'tax' => $order->tax,
                'delivery_charges' => $order->delivery_charges,
                'final_total' => $order->final_total,
                'status' => $order->status,
                'customer_address' => $order->address,
                'items' => $orderItems->values()->all(),
            ];
        }

        return [
            'orders' => $response,

        ];

    }

    public function reorder($data)
    {
        // dd($data);
        $oldOrder = Order::with('items')->where('id', $data['order_id'])->where('user_id', Auth::id())->first();
        if (! $oldOrder) {
            throw new \Exception('Order not found');
        }
        // Create new cart or get existing cart for user
        $cart = Cart::firstOrCreate(
            ['user_id' => Auth::id()],
            ['coupon_id' => null]
        );

        foreach ($oldOrder->items as $orderItem) {
            // Check if the item already exists in the cart
            $existingCartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_variant_id', $orderItem->product_variant_id)
                ->where('notes', $orderItem->notes)
                ->first();

            if ($existingCartItem) {
                // If it exists, update the quantity
                $existingCartItem->quantity += $orderItem->quantity;
                $existingCartItem->save();
            } else {
                // If it doesn't exist, create a new cart item
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_variant_id' => $orderItem->product_variant_id,
                    'quantity' => $orderItem->quantity,
                    'notes' => $orderItem->notes,
                    'addon_id' => $orderItem->addon_id,
                    'ing_id' => $orderItem->ing_id,
                    'removed_ingredient_ids' => $orderItem->removed_ingredient_ids,
                ]);
            }
        }

        ModelUserActivityLog::logActivity(
            Auth::id(),
            'has reordered Order with ID '.$oldOrder->id.' and created/updated Cart with ID '.$cart->id
        );

        return $cart->load('items');
    }

    public function updateStatus($id, $data)
    {
        //    / dd( $data['status']);

        $order = Order::find($id);
        // /dd( $order);
        if (! $order) {
            return null;
        }
        // if (isset($data['status']) && $data['status'] === 'completed') {
        //     if (!empty($order->user_id)) {
        //         app(\App\Services\LoyaltyService::class)
        //             ->addPoints($order->user_id, $order->id, $order->final_amount);
        //     }
        // }

        $order->update([
            'status' => $data['status'] ?? $order->status,
        ]);
        if (($data['status'] ?? $order->status) === 'ready') {
            app(\App\Services\BranchStockService::class)->handleIngredientOut($order);
        }
        //  $order = Order::find($id);
        //  event(new \App\Events\OrderAccepted($order));

        // $this->printStickers($id);

        ModelUserActivityLog::logActivity(
            Auth::id(),
            'has updated Order with ID '.$order->id
        );
        OrderTracking::create([
            'order_id' => $order->id,
            'status' => $data['status'] ?? $order->status,
            'changed_by' => Auth::id(),
            'note' => $data['reason'] ?? ('Status updated to '.ucfirst($data['status'] ?? $order->status)),
        ]);

        return $order;
    }

    public function printStickers($orderId)
    {
        // dd($orderId);
        $order = Order::findOrFail($orderId);
        //  dd($order);
        $printerService = new StationPrintService;
        $filePath = $printerService->printOrderItems($order);

        return null; // ❌ Return null if printing failed
    }

    public function liveorders()
    {
        return Order::with(['customer', 'address', 'branch', 'coupon', 'items'])
            ->whereIn('status', ['pending', 'processing'])
            ->orderBy('id', 'desc')
            ->get();
    }

    public function kdsOrders()
    {
        return Order::with(['customer', 'address', 'branch', 'coupon', 'items'])
            ->where('status', 'accepted')
            ->orderBy('id', 'desc')
            ->get();
    }

    // branch admin
    public function branchorders($id)
    {
        return Order::with(['customer', 'address', 'branch', 'coupon', 'items'])->where('branch_id', $id)->orderBy('id', 'desc')->get();
    }

    public function liveordersbranch($id)
    {
        return Order::with(['customer', 'address', 'branch', 'coupon', 'items'])
            ->where('branch_id', $id)
            ->whereIn('status', ['pending', 'processing'])
            ->orderBy('id', 'desc')
            ->get();
    }
}
