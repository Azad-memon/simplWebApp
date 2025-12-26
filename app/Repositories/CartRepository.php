<?php

namespace App\Repositories;

use App\Models\Coupon;
use App\Models\Ingredient;
use App\Models\ModelImages;
use App\Repositories\Interfaces\CartRepositoryInterface;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\AddonIngredient;
use App\Models\ModelUserActivityLog;
use Auth;
use Illuminate\Support\Facades\DB;
use App\Models\IngredientProductVariant;
use Illuminate\Support\Str;

class CartRepository implements CartRepositoryInterface
{
    public function all($couponCode = null,$order_platform = null)
    {
        //dump($order_platform);
        $ispos = false;
        if($order_platform=="pos"){
           // dd(Auth::user()->branchstaff()->first()?->branch_id);
             $carts = Cart::with(['items.productVariant.product.addons.addonable.images', 'items.productVariant.sizes'])
            ->where("user_id", Auth::id())
            ->where("branch_id", Auth::user()->branchstaff()->first()?->branch_id)
            ->first();
           // dd( $carts );
           $ispos = true;
        }else{
        $carts = Cart::with(['items.productVariant.product.addons.addonable.images', 'items.productVariant.sizes'])
            ->where("user_id", Auth::id())
            ->first();
        }

        if (!$carts) {
            return [
                'cart' => [],
                "cart_id" =>"",
                'grand_total' => 0,
                'discount' => 0,
                'discount_message' => '',
                'tax' => 0,
                "tax_rate" => 0,
                'delivery_charges' => 0,
                'final_total' => 0,
                "cash_amount" => getTaxData()['cash'] ,
                "card_amount" => getTaxData()['card'],
                "order_note" => "",
            ];
        }
        // dd($carts);
        $items = $carts->items->map(function ($item) use ($ispos) {
            $productPrice = $item->productVariant->price ?? 0;
            $sizeid = $item->productVariant->sizes->id ?? null;
            $quantity = $item->quantity ?? 1;
            $subtotal = $productPrice * $quantity;

            /** ---------------- Removed Ingredients ---------------- */
            $removedIngredientsDetails = getIngredientDetails($item->removed_ingredient_ids,$sizeid,$quantity,$ispos);

            /** ---------------- Addons ---------------- */
            $addonDetail = getIngredientDetails($item->addon_id, true,$sizeid,$quantity,$ispos);
            $totalAddonPrice = $addonDetail->sum('total');
            /** ---------------- Subtotal ---------------- */
            $subtotal = ($productPrice * $quantity) + $totalAddonPrice;
            return [
                'id' => $item->id,
                'size_id' => $sizeid,
                'product_id' => $item->productVariant->product->id,
                'product_name' => $item->productVariant->product->name,
                'product_image' => $item->productVariant->product->main_image,
                'product_variant_id' => $item->productVariant->id,
                'serving quantity' => $item->productVariant->unit,
                'variant_name' => $item->productVariant->sizes->name ?? 'N/A',
                'symbol' => $item->productVariant->sizes ? Str::substr($item->productVariant->sizes->name, 0, 1) : 'N/A',
                'product_price' => $productPrice,
                'quantity' => $quantity,
                'notes' => $item->notes,
                'addon' => $addonDetail,
                "ingredients" => $item->ing_id,
                // 'addonlist' => $ingredientsAddons,
                'removed_ingredients_details' => $removedIngredientsDetails,
                // "removed_ingredients_list" => $item->productVariant->filtered_ingredients,
                'subtotal' => $subtotal,
            ];
        });

        /** ---------------- Totals & Coupon ---------------- */
        $totalSubtotal = $items->sum('subtotal');

        $result = applyCoupon($totalSubtotal, $couponCode, $carts);

        $discount = $result['discount'];
        $discount_applied_amount = $result['message'];

         $taxRate = $carts->tax_rate ?? 10; // %
        if($order_platform == 'pos'){
            $deliveryCharges = 0; // fixed
        }else{
            if($carts->is_delivery){
                $deliveryCharges = 200; // fixed
            }else{
                $deliveryCharges = 0; // fixed
            }
        }
        $subTotalAfterDiscount = max(0, $totalSubtotal - $discount);
        $taxAmount = ($totalSubtotal * $taxRate) / 100;
        $finalTotal = $subTotalAfterDiscount + $taxAmount + $deliveryCharges;

        /** ---------------- Final Response ---------------- */

        if(count($items) > 0){
        return [
            "cart_id" => $carts->id,
            'order_note' => $carts->order_note,
            'cart' => $items->values()->all(),
            'grand_total' => $totalSubtotal,
            'discount' => round($discount, 2),
            "discount_message" => $discount_applied_amount,
            'tax' => round($taxAmount, 2),
            "tax_rate" => $taxRate,
            "cash_amount" => getTaxData($totalSubtotal)['cash']['total']+$deliveryCharges,
            "card_amount" => getTaxData($totalSubtotal)['card']['total']+$deliveryCharges,
            'delivery_charges' => round($deliveryCharges, 2),
            'final_total' => round($finalTotal, 2),
        ];
      }else{
        return [
            "cart_id" => $carts->id,
            'order_note' => $carts->order_note,
            'cart' => [],
            'grand_total' => 0,
            'discount' => 0,
            'discount_message' => '',
            'tax' => 0,
            "tax_rate" => 0,
            "cash_amount" => getTaxData()['cash'],
            "card_amount" => getTaxData()['card'],
            'delivery_charges' => 0,
            'final_total' => 0,
        ];
      }
    }


    public function find($id)
    {
        return Cart::findOrFail($id);
    }
    public function create($data)
    {
        $userId = Auth::user()->id;
        $checkcart = Cart::where('user_id', $userId)->first();

        if ($checkcart) {
            $existingCart = Cart::with('items')->where('user_id', $userId)->first();
            $found = false;

            foreach ($checkcart->items as $resulitem) {
                // DB values (already decoded by cast)
                $addonsDb = $resulitem->addon_id ?? [];
                $removedDb = $resulitem->removed_ingredient_ids ?? [];

                // Request values
                $addonsReq = $data['addons'] ?? [];
                $removedReq = $data['removed_ingredients'] ?? [];

                // Comparison
                if (
                    $resulitem->product_variant_id == $data['product_variant_id'] &&
                    json_encode($addonsDb) == json_encode($addonsReq) &&
                    json_encode($removedDb) == json_encode($removedReq)
                ) {
                    $resulitem->increment('quantity', $data['quantity'] ?? 1);
                    $found = true;
                    break;
                }
            }

            if ($found) {
                return $existingCart->fresh(['items']);
            } else {
                $itemData = [
                    'cart_id' => $checkcart->id,
                    'addon_id' => !empty($data['addons']) ? json_encode($data['addons']) :null,
                    'removed_ingredient_ids' => !empty($data['removed_ingredients']) ? json_encode($data['removed_ingredients']) : null,
                    'product_variant_id' => $data['product_variant_id'],
                    'quantity' => $data['quantity'] ?? 1,
                    'notes' => $data['notes'] ?? null,
                    "ing_id"=>!empty($data['ing_id']) ? json_encode($data['ing_id']) :null,
                ];
               // dd( $itemData);
                $checkcart->items()->create($itemData);

            }
            //dd(  $itemData);

            return $checkcart->fresh(['items']);

        } else {
            $datacart=[
                'user_id' => $userId,
                "branch_id" => isset($data['branch_id']) ? $data['branch_id'] : null,
            ];
            $cart = Cart::create($datacart);
            $itemData = [
                'cart_id' => $cart->id,
                'addon_id' => (!empty($data['addons']))
            ? json_encode(is_array($data['addons']) ? $data['addons'] : $data['addons']->toArray())
            : null,
                'removed_ingredient_ids' => (!empty($data['removed_ingredients']))
                    ? json_encode(is_array($data['removed_ingredients']) ? $data['removed_ingredients'] : $data['removed_ingredients']->toArray())
                    : null,
                'product_variant_id' => $data['product_variant_id'],
                'quantity' => $data['quantity'] ?? 1,
                'notes' => $data['notes'] ?? null,
                "ing_id"=>!empty($data['ing_id']) ? json_encode($data['ing_id']) :null,
            ];
            $cart->items()->create($itemData);

            return $cart->fresh(['items']);
        }



    }
    public function delete($id)
    {
        $cart = CartItem::findOrFail($id);
        return $cart->delete();

    }
    public function update($data)
    {
       // dd($data);
        $cart = CartItem::find($data['id']);
        if (!$cart) {
            return null;
        }
        if (isset($data['quantity']) && $data['quantity'] == 0) {
            $cart->delete();
            return null;
        }
        $updateData=[
            'quantity' => $data['quantity'] ?? $cart->quantity,
            'notes' => $data['notes'] ?? $cart->notes,
            'addon_id' => $data['addons'] ?? $cart->addons,
            'ing_id' => $data['ingredients'] ?? $cart->ing_id,
            'removed_ingredient_ids' => $data['removed_ingredients'] ?? $cart->removed_ingredients,
            'product_variant_id' => $data['product_variant_id'] ?? $cart->product_variant_id,
        ];
        $cart->update($updateData);
        // if ($cart->items()->exists()) {
        //     $cart->items()->update([
        //         'addon_id' => $data['addons'] ?? null,
        //         'ing_id' => null,
        //         'removed_ingredient_ids' => $data['removed_ingredients'] ?? null,
        //     ]);
        // } else {
        //     $cart->items()->create([
        //         'addon_id' => $data['addons'] ?? null,
        //         'ing_id' => null,
        //         'removed_ingredient_ids' => $data['removed_ingredients'] ?? null,
        //     ]);
        // }

        return $this->all();
    }
    public function updateCartQuantity($data)
    {

        // dd($data);
        $cart = CartItem::find($data['id']);

        if (!$cart) {
            return null;
        }
        if (isset($data['action'])) {
            if ($data['action'] === 'add') {
                $cart->increment('quantity', $data['quantity'] ?? 1);
            } elseif ($data['action'] === 'remove') {
                $cart->decrement('quantity', $data['quantity'] ?? 1);
                if ($cart->quantity <= 0) {
                    $cart->delete();
                    return null;
                }
            }
        }
        return $this->all();
    }
    public function finditem($id)
    {
        return CartItem::find($id);
    }
    public function updateCartNote($data)
    {
        //dd($data);
        $cart = CartItem::find($data['id']);
        if (!$cart) {
            return null;
        }
        $cart->update([
            'notes' => $data['notes'],
        ]);
        return $this->all();
    }
    public function updateCartOrderNote($data)
    {
        $cart = Cart::find($data['id']);
        if (!$cart) {
            return null;
        }
        $cart->update([
            'order_note' => $data['order_note'],
        ]);
        return $this->all();
    }
     public function updateTax($data)
    {
        //dd($data);
        $cart = Cart::find($data['cart_id']);
        //dd($cart);
        if (!$cart) {
            return null;
        }
        $cart->update([
            'tax_rate' => $data['tax'] ?? $cart->tax_rate,
            'paymentType' => $data['paymentType'] ?? $cart->paymentType,
        ]);
       // dd($this->all());
        return $this->all();
    }




}


