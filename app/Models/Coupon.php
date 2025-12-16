<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount',
        'type',
        'expire_at',
        'start_date',
        'max_usage',
        'status',
        'min_amount',
        'max_amount',
        'product_id',         // product link
        'product_variant_id', // product variant link
    ];

    protected $casts = [
        'expire_at' => 'datetime',
        'status' => 'boolean',
        'product_id' => 'array',
        'product_variant_id' => 'array',
    ];


    /**
     * Relationship: Coupon has many usages
     */
    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Check if coupon is expired
     */
    public function isExpired(): bool
    {
        return $this->expire_at && Carbon::now()->greaterThan($this->expire_at);
    }

    /**
     * Count total times coupon has been used
     */
    public function totalUsages(): int
    {
        return $this->usages()->count();
    }

    /**
     * Check if coupon usage limit is exceeded
     */
    public function isUsageLimitExceeded(): bool
    {
        return $this->max_usage > 0 && $this->totalUsages() >= $this->max_usage;
    }
    public function isAlreadyUsedByUser(int $userId): bool
    {
        return $this->usages()
            ->where('user_id', $userId)
            ->exists();
    }


    /**
     * Check if coupon is valid
     */
    public function isValid(int $userId = null): bool
    {
        $userId = $userId ?? auth()->id();

        return $this->status
            && !$this->isExpired()
            && !$this->isUsageLimitExceeded()
            && !$this->isAlreadyUsedByUser($userId);
    }

    /**
     * Get remaining usage count
     */
    public function remainingUsage(): int
    {
        if ($this->max_usage == 0) {
            return PHP_INT_MAX; // unlimited
        }

        return max(0, $this->max_usage - $this->totalUsages());
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Coupon belongs to a Product Variant
     */
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
    /**
     * Apply validation for given cart/order
     */
    public function isApplicable($cartTotal, $productId = null, $variantId = null): array
    {
        if (!$this->isValid()) {
            return [false, "Coupon is expired or inactive."];
        }

        if ($this->min_order_amount && $cartTotal < $this->min_order_amount) {
            return [false, "Minimum order amount must be at least {$this->min_order_amount}."];
        }

        // Decode stored JSON arrays
        $couponProducts = $this->product_id ? json_decode($this->product_id, true) : [];
        $couponVariants = $this->product_variant_id ? json_decode($this->product_variant_id, true) : [];

        // Product check
        if (!empty($couponProducts) && $productId && !in_array($productId, $couponProducts)) {
            return [false, "This coupon is only valid for selected products."];
        }

        // Variant check
        if (!empty($couponVariants) && $variantId && !in_array($variantId, $couponVariants)) {
            return [false, "This coupon is only valid for selected product variants."];
        }

        return [true, "Coupon is applicable."];
    }


    /**
     * Calculate discount amount
     */
    public function calculateDiscount($cartTotal): float
    {
        $discount = 0;

        if ($this->discount_type === 'percentage') {
            $discount = ($cartTotal * $this->discount_value) / 100;
        } else {
            $discount = $this->discount_value;
        }

        if ($this->max_discount && $discount > $this->max_discount) {
            $discount = $this->max_discount;
        }

        return min($discount, $cartTotal); // discount never > total
    }
    public function images()
    {
        return $this->morphMany(ModelImages::class, 'imageable');
    }

     public function getMainImageAttribute()
    {
        $image = $this->images()->first();

        if ($image && $image->image) {
            return asset('storage/' . $image->image);
        }

        return asset('uploads/placeholder.png');
    }

}
