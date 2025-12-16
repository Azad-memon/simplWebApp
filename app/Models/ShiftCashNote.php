<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\StaffShift;
class ShiftCashNote extends Model {
  protected $fillable = [
        'shift_id',
        'user_id',
        'branch_id',
        'note_value',
        'quantity',
        'entry_type',
        'total',
    ];
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function shift() {
        return $this->belongsTo(StaffShift::class);
    }
}
