<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Ingredient extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $primaryKey = 'ing_id';

    protected $fillable = [
        'ing_name',
        'ing_desc',
        'ing_unit',
        'unit_price',
        'min_quantity',
        'category_id',
        'ing_type',
        'is_active',
        'created_by',
        'ingredient_label',
        'is_quantify',
    ];

    /**
     *  created user ingredient.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function images()
    {
        return $this->morphMany(ModelImages::class, 'imageable');
    }

    public function productVariants()
    {
        return $this->belongsToMany(ProductVariant::class, 'ingredient_product_variant')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function translations()
    {
        return $this->morphMany(LanguageTranslation::class, 'translatable');
    }

    // Optional: get specific language translation
    public function translation($lang = 'en')
    {
        return $this->translations()->where('language_code', $lang)->first();
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'ing_unit'); // or 'ing_unit' if using that
    }

    public function branchQuantities()
    {
        return $this->hasMany(BranchIngredientQuantity::class, 'ing_id', 'ing_id');
    }

    public function getActiveIngredientsAttribute()
    {
        return $this->ingredients
            ->filter(fn ($ingredient) => $ingredient->is_active == 1)
            ->values(); // optional: reset index
    }

    public function getMainImageAttribute()
    {
        $image = $this->images()->first();

        if ($image && $image->image) {
            return asset('storage/'.$image->image);
        }

        return asset('uploads/placeholder.png');
    }

    public function category()
    {
        return $this->belongsTo(IngredientCategory::class, 'category_id');
    }

    public function sizes()
    {
        return $this->hasMany(IngredientSize::class, 'ingredient_id', 'ing_id');
    }

    public function standardIngredients()
    {
        return $this->belongsToMany(
            Ingredient::class,
            'custom_ingredient',
            'custom_ing_id',
            'standard_ing_id'
        );
    }

    public function customIngredients()
    {
        return $this->belongsToMany(
            Ingredient::class,
            'custom_ingredient',
            'standard_ing_id',
            'custom_ing_id'
        );
    }

    // public function getIngNameAttribute($value)
    // {
    //     $user = Auth::user();

    //     // User exist & staff hai? (branchstaff relation se)
    //     if ($user && $user->branchstaff()->exists()) {
    //         return $this->ingredient_label ?: $value;
    //     }

    //     // Otherwise original ing_name return
    //     return $value;
    // }
    //     public function getIngredientLabelAttribute($value)
    // {
    //     $user = Auth::user();

    //     // User exist & staff hai? (branchstaff relation se)
    //     if ($user && $user->branchstaff()->exists()) {
    //         return $this->ing_name ?: $value;
    //     }

    //     // Otherwise original ing_name return
    //     return $value;
    // }
}
