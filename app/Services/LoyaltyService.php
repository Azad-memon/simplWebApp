<?php

namespace App\Services;

use App\Models\LoyaltySetting;
use App\Models\LoyaltyPoint;

class LoyaltyService
{
    /**
     * Add loyalty points when order is created
     *
     * @param int $userId
     * @param int $orderId
     * @param float $finalTotal
     * @return LoyaltyPoint|false
     */
    public function addPoints(int $userId, int $orderId, float $finalTotal)
    {
        $loyaltySetting = LoyaltySetting::first();

        if (!$loyaltySetting) {
            return false; // No settings found
        }

        // Calculate points
        $rupees = $loyaltySetting->rupees;
        $points = $loyaltySetting->points;

        $calculatedPoints = ($finalTotal / $rupees) * $points;

        // Apply max points per order
        if (!empty($loyaltySetting->max_points_per_order)) {
            $calculatedPoints = min($calculatedPoints, $loyaltySetting->max_points_per_order);
        }


        $currentBalance = LoyaltyPoint::where('customer_id', $userId)
            ->latest('id')
            ->value('points_balance') ?? 0;
//dd( $calculatedPoints );
        // Save transaction
       return LoyaltyPoint::firstOrCreate(
            ['order_id' => $orderId],
            [
                'customer_id'          => $userId,
                'points_updated'   => $calculatedPoints,
                'points_balance'   => $currentBalance + $calculatedPoints,
                'transaction_type' => 'CREDIT',
                'transaction_date' => now(),
            ]
        );
    }
}
