<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppHomePageProduct extends Model
{
    protected $table = 'app_home_page_products'; // Table name

    protected $fillable = [
        'product_id',
        'status',
    ];

    // Product relation (optional)
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
   public function images()
    {
        return $this->morphMany(ModelImages::class, 'imageable');
    }
}
