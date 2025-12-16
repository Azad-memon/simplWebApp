<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CartItem extends Model
{
    use HasFactory;


    protected $table="cart_item";

    protected $fillable = [
        'cart_id',
        'addon_id',
        'ing_id',
        'removed_ingredient_ids',
        'product_variant_id',
        'quantity',
        'notes',
    ];

    protected $casts = [
        'removed_ingredient_ids' => 'array',
        "addon_id"=>"array"
    ];

    /**
     * Relationships
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    // public function addon()
    // {
    //     return $this->belongsTo(AddonIngredient::class ,"addon_id");
    // }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class, 'ing_id');
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}

