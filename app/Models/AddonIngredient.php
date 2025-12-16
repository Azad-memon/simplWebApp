<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddonIngredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'price',
        'qty',
        'desc',
        'is_active',
        'is_replace',
    ];

    // Relationships
    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class, 'ing_id', 'ing_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function addonable()
    {
        return $this->morphTo();
    }
    public function items()
    {
        return $this->hasMany(AddonIngredientItem::class, 'addon_ingredient_id');
    }

}
