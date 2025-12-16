<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddonIngredientItem extends Model
{
    protected $fillable = ['addon_ingredient_id', 'ingredient_id'];

    public function addon()
    {
        return $this->belongsTo(AddonIngredient::class, 'addon_ingredient_id');
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
}
