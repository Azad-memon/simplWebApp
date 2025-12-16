<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_name',
        'province',
        'population',
        'latitude',
        'longitude',
    ];

    // Optional: table name if you want to be explicit
    protected $table = 'cities';

    // Optional: if you don’t want timestamps
    public $timestamps = false;
}
