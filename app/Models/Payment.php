<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
        const STATUS_PENDING = 'pending';
        const STATUS_SUCCESS = 'success';
        const STATUS_FAILED = 'failed';
        const STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'order_id',
        'payment_method', // e.g. cash, card, online
        'transaction_id',
        'amount',
        'card_number',
        'status', // pending, completed, failed, refunded
    ];

    // Relation with Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
