<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Popup extends Model
{
    protected $fillable = [
        'title',
        'image',
        'is_active',
        'start_at',
        'end_at',
    ];
}

