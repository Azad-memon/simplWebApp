<?php

namespace App\Repositories;

use App\Models\ModelImages;
use App\Repositories\Interfaces\CouponRepositoryInterface;
use App\Models\Coupon;
use App\Models\ModelUserActivityLog;
use Illuminate\Support\Facades\Auth;

class CouponRepository implements CouponRepositoryInterface
{
    public function all()
    {
       $coupons = Coupon::orderByRaw("CASE WHEN status = 1 THEN 0 ELSE 1 END")
            ->orderBy('id', 'desc')
            ->get();
        //    / dd( $coupons);
        return $coupons;
    }

    public function find($id)
    {
        $coupon = Coupon::findOrFail($id);
        return $coupon;
    }

    public function create(array $data)
    {
    //    $data['product_id'] = json_decode($data['product_id'], true);
    //    $data['product_variant_id'] = json_decode($data['product_variant_id'] ??  "", true);

        $coupon = Coupon::create($data);
         if (!empty($data['images']) && is_array($data['images'])) {
                foreach ($data['images'] as $key => $imageUrl) {
                    $image = new ModelImages();
                    $image->image = $imageUrl;
                    $image->image_type = $key;
                    $imageSaved =  $coupon->images()->save($image);

                    // if (!$imageSaved) {
                    //     cd('Error saving image: ' . $image->image_type);
                    // }
                }
            }
        ModelUserActivityLog::logActivity(
            Auth::id(),
            "created a new coupon with ID {$coupon->id}"
        );

        return $coupon;
    }

    public function update($id, array $data)
    {

        $coupon = Coupon::findOrFail($id);
        // $data['product_id'] = json_decode($data['product_id'], true);
        // $data['product_variant_id'] = json_decode($data['product_variant_id'], true);
        $coupon->update($data);

        ModelUserActivityLog::logActivity(
            Auth::id(),
            "updated coupon with ID {$id}"
        );

        return $coupon;
    }

    public function delete($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        ModelUserActivityLog::logActivity(
            Auth::id(),
            "deleted coupon with ID {$id}"
        );

        return true;
    }

    public function toggleStatus(array $data)
    {
        $coupon = Coupon::findOrFail($data['id']);
        $coupon->status = $data['status'];
        $coupon->save();

        ModelUserActivityLog::logActivity(
            Auth::id(),
            "has toggled status of coupon with ID {$coupon->id} to " . ($coupon->status ? 'active' : 'inactive')
        );

        return $coupon;
    }
}
