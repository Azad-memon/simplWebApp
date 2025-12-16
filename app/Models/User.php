<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
  protected $fillable = [
    'first_name',
    'last_name',
    'email',
    'password',
    'role_id',
    'status',
    'user_status',
    'employee_id',

];
    public const ROLE_ADMIN        = 1;
    public const ROLE_BRANCHADMIN  = 2;
    public const ROLE_CUSTOMER     = 3;
    public const ROLE_WAITER     = 4;
    public const ROLE_ACCOUNTANT     = 5;
    public const ROLE_DISPATCHER     = 6;



    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_users', 'user_id', 'branch_id');
    }
    public function shift(){
        return $this->belongsToMany(StaffShift::class, 'branch_staff', 'user_id', 'shift_id');
    }
    public function branchstaff(){
        return $this->hasMany(BranchStaff::class, 'user_id');
    }
    public function shiftusers(){
        return $this->hasMany(ShiftUser::class, 'user_id');
    }


}
