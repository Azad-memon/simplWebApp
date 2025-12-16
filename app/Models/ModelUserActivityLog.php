<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelUserActivityLog extends Model
{
    use HasFactory;
    protected $table="user_activity_log";
    protected $primaryKey="id";
    protected $fillable = ['user_id', 'user_activity'];


    public static function logActivity($user_id, $user_activity)
    {
        return self::create([
            'user_id' => $user_id,
            'user_activity' => $user_activity
        ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
