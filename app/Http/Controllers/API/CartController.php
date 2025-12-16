<?php

namespace App\Http\Controllers\API;

use App\Helpers\MessageHelper;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Cart;
use App\Models\Coupon;
use App\Repositories\Interfaces\CartRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends BaseController
{
    protected $cartRepository;

    protected $language;

    protected $productRepository;

    public function __construct(Request $request, CartRepositoryInterface $cartRepository, ProductRepositoryInterface $productRepository)
    {
        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
        $this->language = $request->input('language', 'EN') ?? 'EN';
    }

    public function store(Request $request)
    {
        $language = $this->language;
        try {
            $data = $request->validate([
                'product_variant_id' => 'required|integer|exists:product_variants,id',
                'quantity' => 'nullable|integer|min:1',
                'notes' => 'nullable|string',
                // 'addons' => 'nullable|array',
                // 'addons.*' => 'integer|min:1',
                // 'removed_ingredients' => 'nullable|array',
                // 'removed_ingredients.*' => 'integer|min:1',
            ]);
            DB::beginTransaction();
            $cart = $this->cartRepository->create(request()->all());
            DB::commit();

            return $this->sendResponse($cart, 'Cart item added successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            $errors = MessageHelper::formatErrors($e);
            $fields = ['validation.error'];
            $successMessages = MessageHelper::defaultSuccessMessages($language, $fields);
            $flatMessages = MessageHelper::formatMessages($successMessages);

            return $this->sendError($flatMessages[0] ?? 'Errors', $errors, 200);
        }
    }

    public function directsave(Request $request)
    {
        $productVariant = $this->productRepository->find($request->product_id);
        if (! $productVariant) {
            return $this->sendError('Product variant not found.', [], 200);
        }
        $data['addons'] = $request->input('addons', []);
        $data['removed_ingredients'] = $request->input('removed_ingredients', default: []);
        $data['product_variant_id'] = $productVariant->variants->first()->id;
        $data['quantity'] = $request->input('quantity', 1);
        $data['notes'] = $request->input('notes', null);
        $cart = $this->cartRepository->create($data);
        if ($cart) {
            return $this->sendResponse($cart, 'Cart item added successfully.');
        }

        return $this->sendError('Failed to add cart item.', [], 200);
    }

    public function list()
    {
        // dd();
        $cart = $this->cartRepository->all();

        if ($cart == '') {
            return $this->sendError('No Cart item found.', [], 200);
        }

        return $this->sendResponse($cart, 'Cart item retrieved successfully.');
    }

    public function remove($id)
    {
        $cart = '';
        DB::beginTransaction();
        try {
            $cart = $this->cartRepository->delete($id);
            DB::commit();

            return $this->sendResponse('Cart and its items deleted successfully.', [], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->sendResponse($cart, 'Failed to delete cart.');
        }
    }

    public function update(Request $request)
    {
        try {
            $cart = $this->cartRepository->update($request->all());
            if (! $cart) {
                return $this->sendError('Cart not found or deleted.', [], 200);
            }

            return $this->sendResponse($cart, 'Cart updated successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Something went wrong.', ['error' => $e->getMessage()], 500);
        }
    }

    public function updateQuantity(Request $request)
    {
        try {
            $cart = $this->cartRepository->updateCartQuantity($request->all());
            if (! $cart) {
                return $this->sendError('Cart not found or deleted.', [], 200);
            }

            return $this->sendResponse($cart, 'Cart updated successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Something went wrong.', ['error' => $e->getMessage()], 500);
        }
    }

    public function applycoupon(Request $request)
    {
        $language = $this->language;
        try {
            $request->validate([
                'code' => 'required',
            ]);

            $coupon = Coupon::where('code', $request->code)->first();
            // dd($coupon );

            if (! $coupon || ! $coupon->isValid(Auth::user()->id)) {
                return $this->sendError('Invalid or expired coupon', [], 422);
            }

            $cart = Cart::where('user_id', Auth::user()->id)->first();
            if (! $cart) {
                return $this->sendError('Cart not found', [], code: 422);
            }

            $cart = $this->cartRepository->all($coupon->code);
            // $cart->coupon_id = $coupon->id;
            // $cart->save();

            return $this->sendResponse($cart, 'Coupon applied successfully.');
        } catch (\Throwable $e) {
            // dd( $e->getMessage());
            $errors = MessageHelper::formatErrors($e);
            $fields = ['validation.error'];
            $successMessages = MessageHelper::defaultSuccessMessages($language, $fields);
            $flatMessages = MessageHelper::formatMessages($successMessages);

            return $this->sendError($flatMessages[0] ?? 'Errors', $errors, 200);
        }
    }

    public function removecoupon(Request $request)
    {
        $language = $this->language;
        // dd();
        $cart = Cart::where('user_id', Auth::user()->id)->first();
        if ($cart == '') {
            return $this->sendError('No Cart item found.', [], 200);
        }
        $cart->update(['coupon_id' => null]);

        return $this->sendResponse([], 'Coupon deleted successfully.');
    }

    public function couponlist()
    {
        $userId = auth()->id();
        try {
            $userId = auth()->id();
            $coupons = Coupon::with('images')
                ->where('status', true)
                ->where('expire_at', '>', now())
                ->whereDoesntHave('usages', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->get();

            if (count($coupons) == 0) {
                return $this->sendError('Coupon Not Found', [], 200);
            }
            $data = [];
            foreach ($coupons as $key => $coupon) {
                $data[] = [
                    'id' => $coupon->id,
                    'product_id' => $coupon->product_id,
                    'product_variant_id' => $coupon->product_variant_id,
                    'code' => $coupon->code,
                    'discount' => $coupon->discount,
                    'type' => $coupon->type,
                    'start_date' => \Carbon\Carbon::parse($coupon->start_date)->format('d M Y h:i A'),
                    'expire_at' => \Carbon\Carbon::parse($coupon->expire_at)->format('d M Y h:i A'),
                    'max_usage' => $coupon->max_usage,
                    'min_amount' => $coupon->min_amount,
                    'max_amount' => $coupon->max_amount,
                    // "status"             => (bool) $coupon->status,

                    'images' => $coupon->main_image,
                ];
                // code...
            }

            return $this->sendResponse($data, 'Coupon retrieved successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Something went wrong.', ['error' => $e->getMessage()], 500);
        }
    }
    public function updatetax(Request $request)
    {
        try {
            $data = $request->validate([
                'tax_type' => 'required|string|in:card,cash',
            ]);
            $taxRates = getTaxData();
            $taxRate = $taxRates[$data['tax_type']];
            $cart = Cart::where('user_id', Auth::user()->id)->first();
            if ($cart == '') {
                return $this->sendError('No Cart item found.', [], 200);
            }

            $cart->update([
            'tax_rate' => $taxRate ?? $cart->tax_rate,
            'paymentType' => $data['tax_type'] ?? $cart->paymentType,
        ]);
        $cart = Cart::where('user_id', Auth::user()->id)->first();
            return $this->sendResponse($cart, 'Cart tax updated successfully.');
          //  $cart = $this->cartRepository->update(data: ['tax_rate' => $taxRate]);

           // return $this->sendResponse($cart, 'Cart tax updated successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Something went wrong.', ['error' => $e->getMessage()], 500);
        }
    }
    public function updatecartNote(Request $request)
    {
        try {
            $data = $request->validate([
                'cart_note' => 'nullable|string',
                "item_id"=>'nullable',
            ]);

       $data = ['notes' => $data['cart_note'], 'id' => $data['item_id'] ?? null];
       $cart= $this->cartRepository->updateCartNote($data);
      return $this->sendResponse($cart, 'Cart note updated successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Something went wrong.', ['error' => $e->getMessage()], 500);
        }
    }
    public function updateorderNote(Request $request)
    {
           $data = $request->validate([
                'order_note' => 'nullable|string',
            ]);

        $cart = Cart::where('user_id', Auth::user()->id)->first();
        $data = ['order_note' => $data['order_note'] ?? $cart->order_note, 'id' => $cart->id];
        $this->cartRepository->updateCartOrderNote($data);
        $cart = Cart::where('user_id', Auth::user()->id)->first();
        return $this->sendResponse($cart, 'Order note updated successfully.');
    }
}
