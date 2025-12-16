<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BranchUsers extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'branch_id',
        'user_id',
    ];

}
