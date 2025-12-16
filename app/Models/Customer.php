<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\ModelImages;


class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    const ADMIN = 1;
    const BRANCH_ADMIN = 2;
    const CUSTOMER = 3;


    const INACTIVE_STATUS = 0;
    const ACTIVE_STATUS = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'status',
    ];


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
    public function images()
    {
        return $this->morphMany(ModelImages::class, 'imageable');
    }

    public function getImagesOrDefaultAttribute()
    {
        if ($this->images->isNotEmpty()) {
            return $this->images;
        }

        return collect([
            (object) [
                // 'url' => asset('images/default.jpg'),
                'url' => "",
                'is_default' => true,
            ]
        ]);
    }
    public function wishlist()
    {
        return $this->belongsToMany(Product::class, 'wishlists', 'user_id')->withTimestamps();
    }
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);

    }
    public function loyaltyHistories()
    {
        return $this->hasMany(LoyaltyPoint::class, 'customer_id');
    }
    public function addresses()
    {
        return $this->hasOne(CustomerAddress::class, 'customer_id')->latestOfMany();
    }
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }
     public function getTotalRevenueAttribute()
    {
        return $this->orders->sum('final_amount');
    }

    //  public function primaryAddress()
//     {
//         return $this->hasOne(CustomerAddress::class, 'customer_id')
//             ->where('title', 'home')
//             ->orWhere('title', 'office')
//             ->first(); // ya first() le sakte ho
//     }
}
