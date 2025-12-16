<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'branch_code',
        'city_id',
        'phone',
        'address',
        'lat',
        'long',
        'description',
        'status',
        'open_time',
        'close_time',
         'open_days',
    ];
    protected $casts = [
    'open_days' => 'array',
];
    public function users()
    {
        return $this->belongsToMany(User::class, 'branch_users', 'branch_id', 'user_id');
    }
    public function branchStaff()
    {
        return $this->hasMany(BranchStaff::class, 'branch_id');
    }
    public function shifts()
    {
        return $this->hasMany(StaffShift::class, 'branch_id');
    }
     public function orders()
    {
        return $this->hasMany(Order::class, 'branch_id');
    }
    public function stations()
    {
        return $this->hasMany(Station::class, 'branch_id');
    }
     public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
     public function branchSettings()
    {
        return $this->hasOne(BranchSetting::class, 'branch_id');
    }
}
