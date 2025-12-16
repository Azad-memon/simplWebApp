<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cat_id',
        'slug',
        'desc',
        'product_type',
        'is_active',
        'is_featured',
        'is_best_selling'
        ,'is_new',
    ];


    public function category()
    {
        return $this->belongsTo(Category::class, 'cat_id');
    }
    public function images()
    {
        return $this->morphMany(ModelImages::class, 'imageable');
    }
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
    public function translations()
    {
        return $this->morphMany(LanguageTranslation::class, 'translatable');
    }
    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    public function addons()
    {
        return $this->hasMany(AddonIngredient::class, 'product_id');
    }
    public function wishlistedBy()
    {
        return $this->belongsToMany(User::class, 'wishlists')->withTimestamps();
    }
    public function getMainImageAttribute()
    {
        $image = $this->images()->first();

        if ($image && $image->image) {
            return asset('storage/' . $image->image);
        }

        return asset('uploads/placeholder.png');
    }
     public function getMainVideoAttribute()
    {
        $image = $this->images()->firstWhere('image_type', 'product_video');

        if ($image && $image->image) {
            return asset('storage/' . $image->image);
        }

        return asset('uploads/placeholder.png');
    }


}
