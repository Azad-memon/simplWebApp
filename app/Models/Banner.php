<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $table = "app_banners";
    protected $fillable = ['banner_title', 'banner_description', 'type', 'category_id', 'product_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
      public function images()
    {
        return $this->morphMany(ModelImages::class, 'imageable');
    }

  public function getMainImageAttribute()
    {
        $image = $this->images()->first();

        if ($image && $image->image) {
            return asset('storage/' . $image->image);
        }

        return asset('uploads/placeholder.png');
    }
}
