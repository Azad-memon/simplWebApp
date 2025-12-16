<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashoutTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'type',           // cashout / refund
        'category',       // ingredient / other / refund
        'ingredient_id',
        'item_name',
        'amount',
        'order_ref',
        'remarks',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

   public function getIngredientNamesAttribute()
    {
        if ($this->category !== 'ingredient' || !$this->ingredient_id) {
            return null;
        }

        $ids = json_decode($this->ingredient_id, true);
        if (!is_array($ids)) return null;

        return Ingredient::whereIn('ing_id', $ids)
            ->pluck('ing_name')
            ->implode(', ');
    }

     public function order()
    {
        return $this->belongsTo(Order::class, 'order_ref', 'order_uid');
    }
}
