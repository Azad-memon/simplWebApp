<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IngredientProductVariant extends \Illuminate\Database\Eloquent\Relations\Pivot
{
    protected $table = 'ingredient_product_variant';

    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'product_variant_id',
        'ingredient_id',
        'ing_category_id',
        'default_ing',
        'quantity',
        'status',
        'unit',
        'type',
    ];
    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id', 'ing_id');
    }
    public function ingredientCategory()
    {
        return $this->belongsTo(IngredientCategory::class, 'ing_category_id', 'id');
    }
    public function defaultIngredient()
    {
        return $this->belongsTo(Ingredient::class, 'default_ing', 'ing_id');
    }
}
