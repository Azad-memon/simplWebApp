<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_variant_id',
        'quantity',
        'price',
        'total',
        'notes',
        'addon_id',
        'ing_id',
        'total_price',
        'removed_ingredient_ids',
    ];
    protected $casts = [
        'removed_ingredient_ids' => 'array',
        "addon_id" => "array"
    ];


    // Relations
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
    // public function getAddonDetailsAttribute()
    // {
    //     if (!empty($this->addon_id)) {
    //         return AddonIngredient::whereIn('id', $this->addon_id)
    //             ->with('addonable')
    //             ->get()
    //             ->map(function ($ing) {
    //                 return [
    //                     'id' => $ing->id,
    //                     'name' => optional($ing->addonable)->ing_name ?? optional($ing->addonable)->sizes->name,
    //                     'price' => $ing->price,
    //                     'images' => optional($ing->addonable)->main_image,
    //                 ];
    //             });
    //     }

    //     return collect([]);
    // }
    // In your Model

    public function getRemoveIngredientDetailsAttribute()
    {
        if (!empty($this->removed_ingredient_ids)) {
            return IngredientProductVariant::whereIn('id', $this->removed_ingredient_ids)
                ->with('ingredient')
                ->get()
                ->map(function ($ing) {
                    return [
                        'id' => $ing->ingredient->id,
                        'name' => $ing->ingredient->ing_name,
                        'images' => $ing->ingredient->main_image,
                    ];
                });
        }

        return collect([]);
    }


}
