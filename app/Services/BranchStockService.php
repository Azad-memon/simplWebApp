<?php

namespace App\Services;

use App\Models\BranchStockManagment;
use App\Models\Ingredient;
use App\Models\ModelUserActivityLog;
use Illuminate\Support\Facades\Auth;

class BranchStockService
{
    public static function handleIngredientOut($order)
    {
        //dd($order);
        foreach ($order->items as $item) {

            $variant = $item->productVariant;
            $sizeid = optional($variant->sizes)->id;
            //  Get all ingredient categories for the variant
            $ingredientCategories = $variant->ingredientCategories()->get();
        // dump($ingredientCategories);
            $defaultIngredientIds = $ingredientCategories->pluck('pivot.default_ing')->filter()->toArray();
           // dump($defaultIngredientIds);
            //  $defaultIngredientquantity= $ingredientCategories->pluck('pivot.quantity')->filter()->toArray();
            // dump($defaultIngredientquantity);
            //  Get all removed ingredients for the variant
            $removedIngredientsDetails = ! empty($item->removed_ingredient_ids)
               ? getIngredientDetails($item->removed_ingredient_ids, true, $sizeid)
               : [];
            //  Get all addon ingredients for the variant
            $removedIds = collect($removedIngredientsDetails)->pluck('id')->toArray();

            // removed ingredients from default ingredients to get final ingredient ids
            $defaultminusremove = array_diff($defaultIngredientIds, $removedIds);
            // dump( $defaultminusremove);
            // addon ingredients to final ingredient ids
            $addonDetails = ! empty($item->addon_id)
                       ? getIngredientDetails($item->addon_id, true, $sizeid)
                       : [];
        // dump( $item->addon_id);
            // addon ingredients ids to final ingredient ids
            $addonIds = collect($addonDetails)->pluck('id')->toArray();
            //dump( $addonIds );
            $addonDetailsQuantities = collect($addonDetails)
                ->map(function ($item) {
                    return [
                        'ingredient_id' => $item['id'],
                        'quantity' => $item['quantity'] ?? 0,
                    ];
                })
                ->values()
                ->toArray();
                   // dump( $addonDetailsQuantities );

            $defaultIngredientsqty = $ingredientCategories->map(function ($category) {
                return [
                    'ingredient_id' => $category->pivot->default_ing,
                    'quantity' => $category->pivot->quantity,
                ];
            })->filter(function ($item) {
                return ! empty($item['ingredient_id']);
            })->values()->toArray();

            $removedQuantities = collect($removedIngredientsDetails)
                ->map(function ($item) {
                return [
                    'ingredient_id' => $item['ingredient_id'] ?? $item['id'] ?? null,
                    'quantity' => $item['quantity'] ?? 0,
                ];
            })
            ->values()
            ->toArray();


            $ing_qty_merge = array_merge($defaultIngredientsqty, $removedQuantities, $addonDetailsQuantities);


            $finalIngredientIds = array_unique(array_merge($defaultminusremove, $addonIds));
           // dump($finalIngredientIds);
            if (empty($finalIngredientIds)) {
                continue;
            }
            // final array of ingredients with quantity
            $filteredIngredients = collect($ing_qty_merge)
            ->filter(function ($item) use ($finalIngredientIds) {
                return in_array($item['ingredient_id'], $finalIngredientIds);
            })
            ->values()
            ->toArray();
             //dump($filteredIngredients);

            // dump($item);
            //  dump($finalIngredientIds);

            // Record "out" entries for each ingredient
            foreach ($filteredIngredients as $ingredientId) {
//dump(   $ingredientId);
             if($ingredientId['quantity']!=0){
              $outEntry =  [
                    'branch_id'     => $order->branch_id,
                    'ingredient_id' => $ingredientId['ingredient_id'],
                    'quantity'      => $ingredientId['quantity']*$item->quantity,
                    'type'          => 'out',
                    'updated_by'    => Auth::id(),
              ];
             // dump( $outEntry);
                BranchStockManagment::create( $outEntry);

                ModelUserActivityLog::logActivity(
                    Auth::id(),
                    "Created OUT entry for ingredient ID {$ingredientId['ingredient_id']} in branch ID {$order->branch_id} for order #{$order->id}"
                );
            }
            }
        }
    }
}
