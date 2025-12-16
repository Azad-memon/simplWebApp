<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerAddress extends Model
{
    use HasFactory;

    protected $table = "customer_addresses";


    protected $fillable = ['customer_id', 'address_id', 'title', 'longitude', 'latitude', 'is_default', 'latdelta', 'longdelta'];
    /**
     * Relation with customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function address()
    {
        return $this->belongsTo(CustomerAddressDetail::class, 'address_id');
    }

}
