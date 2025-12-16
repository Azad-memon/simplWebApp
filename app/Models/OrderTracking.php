<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTracking extends Model
{

    use HasFactory;
    protected $table = 'order_tracking';

    protected $fillable = [
        'order_id',
        'status',
        'changed_by',
        'note',
    ];


    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }


    public function admin()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
