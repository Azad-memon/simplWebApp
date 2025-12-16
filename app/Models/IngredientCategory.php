<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngredientCategory extends Model
{
    use HasFactory;

    protected $table = 'ingredient_categories';

    protected $fillable = [
        'name',
        'description',
        'parent_id',
        'is_active',
    ];

    /**
     * Category -> Ingredients
     */
    public function ingredients()
    {
        return $this->hasMany(Ingredient::class, 'category_id');
    }

    /**
     * Parent Category
     */
    public function parent()
    {
        return $this->belongsTo(IngredientCategory::class, 'parent_id');
    }

    /**
     * Sub Categories
     */
    public function children()
    {
        return $this->hasMany(IngredientCategory::class, 'parent_id');
    }
      public function images()
    {
        return $this->morphMany(ModelImages::class, 'imageable');
    }

}
