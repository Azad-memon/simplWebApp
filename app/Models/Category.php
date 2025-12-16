<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;
    # const IN_HOUSE_CATEGORY = 0;
    # const VENDOR_CATEGORY = 1;
    protected $appends = ['has_child'];

    protected $fillable = ['name', 'desc', 'parent_id', 'type', 'status', 'series'];


    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
    public function translations()
    {
        return $this->morphMany(LanguageTranslation::class, 'translatable');
    }
    public function getHasChildAttribute()
    {
        return $this->children()->where('status', 'active')->exists();
    }

    public function images()
    {
        return $this->morphMany(ModelImages::class, 'imageable');
    }
    public function products()
    {
        return $this->hasMany(Product::class, 'cat_id');
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
        $image = $this->images()->firstWhere('image_type', 'category_video');

        if ($image && $image->image) {
            return asset('storage/' . $image->image);
        }

        return asset('uploads/placeholder.png');
    }
    public function stations()
    {
        return $this->belongsToMany(Station::class, 'station_category', 'category_id', 'station_id')
            ->withTimestamps();
    }
}
