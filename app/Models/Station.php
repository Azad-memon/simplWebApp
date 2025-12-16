<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    use HasFactory;

    protected $fillable = [
        's_name',
        'branch_id',
        'ip',
    ];


    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }


    public function categories()
    {
        return $this->belongsToMany(Category::class, 'station_category', 'station_id', 'category_id')
                    ->withTimestamps();
    }
}
