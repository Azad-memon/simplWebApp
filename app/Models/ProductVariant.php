<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'unit',
        'size',
        'sku',
        'price',
    ];

    // Relationship: Variant belongs to Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function images()
    {
        return $this->morphMany(ModelImages::class, 'imageable');
    }
    public function ingredientCategories()
    {
        return $this->belongsToMany(
            IngredientCategory::class,
            'ingredient_product_variant',
            'product_variant_id',
            'ing_category_id'
        )
            ->using(IngredientProductVariant::class)
            ->withPivot('ingredient_id', 'default_ing', 'quantity', 'unit', 'type','status')
            ->withPivot('id as pivot_id')
            ->withTimestamps();
    }
    public function ingredients()
    {
        return $this->belongsToMany(
            Ingredient::class,
            'ingredient_product_variant',
            'product_variant_id',   // local key
            'ingredient_id'         // foreign key
        )
            //    / ->using(IngredientProductVariant::class)
            ->withPivot('ing_category_id', 'default_ing', 'quantity', 'unit', 'type', 'id')
            ->withTimestamps();
    }






    public function sizes()
    {
        return $this->belongsTo(Size::class, 'size');
    }
    public function getFilteredIngredientsAttribute()
    {
        return $this->ingredients
            ->filter(fn($ingredient) => $ingredient->is_active == 1 && $ingredient->pivot->type === 'optional')
            ->map(fn($ingredient) => $ingredient->setAttribute('pivot_id', $ingredient->pivot->id))
            ->values();
        ;
    }
    public function getDefaultIngredientsAttribute()
    {
        return $this->ingredientCategories
            ->flatMap(function ($category) {
                return $category->ingredients
                    ->filter(fn($ingredient) => $ingredient->is_active == 1 && $ingredient->pivot->type === 'required')
                    ->map(function ($ingredient) {
                        return $ingredient->setAttribute('pivot_id', $ingredient->pivot->id);
                    });
            })
            ->values();
    }
    public function getMainImageAttribute()
    {
        $image = $this->images()->first();

        if ($image && $image->image) {
            return asset('storage/' . $image->image);
        }

        return asset('uploads/placeholder.png');
    }

}
