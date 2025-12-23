<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'staff_id',
        'platform',
        'branch_id',
        'address_id',
        'coupon_id',
        'dining_type',
        'delivery_type',
        'total_amount',
        'discount',
        'tax',
        'delivery_charges',
        'final_amount',
        'status',
        'change_return',
        'order_uid',
        'customer_name',
        'customer_phone',
        'customer_email',
        'order_note',
        'queue_number',
    ];

    const STATUS_PENDING = 'pending';

    const STATUS_ACCEPTED = 'accepted';

    const STATUS_PROCESSING = 'processing';

    const STATUS_PREPARING = 'preparing';

    const STATUS_DISPATCHED = 'dispatched';

    const STATUS_READY = 'ready';

    const STATUS_COMPLETED = 'completed';

    const STATUS_CANCELLED = 'cancelled';

    const STATUS_REFUNDED = 'refunded';

    const DINE_IN = 'dine_in';

    const TAKE_AWAY = 'take_away';

    const PICKUP = 'pickup';

    const DELIVERY = 'delivery';

    /**
     * Return all statuses as array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_ACCEPTED,
            self::STATUS_PROCESSING,
            self::STATUS_PREPARING,
            self::STATUS_DISPATCHED,
            self::STATUS_READY,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
            self::STATUS_REFUNDED,
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            // Get branch and city data directly
            $branch = \App\Models\Branch::with('city')->find($order->branch_id);

            $branchCode = $branch->branch_code ?? 'BR';
            $cityName = $branch->city->citycode ?? 'CITY';

            // Temporary UID (will finalize after created)
            $order->order_uid = "{$branchCode}|{$cityName}|TEMP";
        });

        static::created(function ($order) {
            // Fetch again to make sure branch and city exist
            $branch = \App\Models\Branch::with('city')->find($order->branch_id);

            $branchCode = $branch->branch_code ?? 'BR';
            $cityName = $branch->city->citycode ?? 'CITY';

            // Format with padded ID
            $formattedId = str_pad($order->id, 5, '0', STR_PAD_LEFT);

            $order->order_uid = "{$cityName}{$branchCode}{$formattedId}";
            $order->saveQuietly();
        });

        // static::creating(function ($order) {
        //     // Random unique ID (encrypted style)
        //     $order->order_uid = strtoupper(uniqid('ORD'));
        // });
    }

    // Relations
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'user_id', 'id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'order_id', 'id');
    }

    public function address()
    {
        // return $this->belongsTo(CustomerAddress::class);
        return $this->belongsTo(CustomerAddress::class, 'address_id')
            ->with('address');
    }

    public function getOrderTypeLabelAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->dining_type));
    }

    public function getDeliveryTypeLabelAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->delivery_type));
    }

    public function ordertracking()
    {
        return $this->hasMany(OrderTracking::class, 'order_id', 'id');

    }

    public function getCustomerNameAttribute()
    {
        return $this->customer?->first_name
            ? $this->customer?->first_name.' '.$this->customer?->last_name
            : ($this->getRawOriginal('customer_name') ?? 'N/A');
    }

    public function getCustomerPhoneAttribute()
    {
        return $this->customer?->phone
            ?? ($this->getRawOriginal('customer_phone') ?? 'N/A');
    }

    public function getCustomerEmailAttribute()
    {
        return $this->customer?->email
            ?? ($this->getRawOriginal('customer_email') ?? 'N/A');
    }

    public function getTaxPercentAttribute()
    {
        $paymentMethod = strtolower($this->payment->payment_method ?? '');

        if ($paymentMethod === 'cash') {
            return 15;
        } elseif ($paymentMethod === 'card') {
            return 8;
        }

        return 0;
    }

    public function refundTransactions()
    {
        return $this->hasMany(CashoutTransaction::class, 'order_ref', 'order_uid');
    }
}
