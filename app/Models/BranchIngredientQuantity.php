<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BranchIngredientQuantity extends Model
{
    use HasFactory;

    protected $table = 'branch_ingredient_quantity';

    protected $fillable = [
        'ing_id',
        'branch_id',
        'updated_by',
        'qty',
    ];

    /**
     * Get the ingredient associated with this quantity.
     */
    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class, 'ing_id', 'ing_id');
    }

    /**
     * Get the user who updated the quantity.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
       public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

}
