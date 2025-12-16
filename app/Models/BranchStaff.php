<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class BranchStaff extends Model
{
   // use SoftDeletes;

    protected $fillable = [
        'branch_id',
        'user_id',
        'shift_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

