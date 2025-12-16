<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderQueue extends Model
{
    protected $fillable = [
        'order_id',
        'branch_id',
        'shift_id',
        'queue_number',
        'queue_date',
        'status',
        'shift_closed',
    ];

    public function order()
{
    return $this->belongsTo(Order::class, 'order_id');
}
}
