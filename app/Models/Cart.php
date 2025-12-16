<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        "branch_id",
        'coupon_id',
        'order_note',
        'paymentType',
        "is_delivery",
        'tax_rate',
    ];

    /**
     * Relationships
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}

