<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;



class CustomerAddressDetail extends Model
{
    use HasFactory;
    protected $table = 'customer_adress_detail';

    protected $fillable = [
        'street_address',
        'city',
        'zip_code',
        'state',
        'country',
        'additional_detail',
        'nearest_landmark',
    ];


}
