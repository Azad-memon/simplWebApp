<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StaffShift;
class ShiftIngredient   extends Model {
    protected $fillable = [
        'shift_id',
        'user_id',
        'branch_id',
        'ingredient_id',
        'quantity',
        'entry_type',
    ];
      public function user() {
        return $this->belongsTo(User::class);
    }
    public function shift() {
        return $this->belongsTo(StaffShift::class);
    }
    public function ingredient()
    {
        return $this->hasMany(Ingredient::class, 'ing_id', 'ingredient_id');
    }

}
