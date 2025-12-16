<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelOtp extends Model
{
    use HasFactory;
    protected $table = 'otp';
    protected $primaryKey = "otp_id";
    protected $fillable = ['otpable_id', 'otpable_type', 'otp_code'];
    public function otpable()
    {
        return $this->morphTo();
    }
}
