<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchSetting extends Model
{
    protected $fillable = [
        'branch_id',
        'printer_ip',
        'printer_port',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
