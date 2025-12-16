<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltySetting extends Model
{
    protected $fillable = ['name', 'rupees', 'points','max_points_per_order'];
}
