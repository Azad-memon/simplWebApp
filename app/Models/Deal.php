<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'price',
        'original_price',
        'is_active',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'start_date'  => 'datetime',
        'end_date'    => 'datetime',
        'start_time'  => 'datetime:H:i',
        'end_time'    => 'datetime:H:i',
    ];
}

