<?php

namespace App\Repositories;

use App\Models\Banner;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Models\LoyaltySetting;
use App\Models\PaymentMethod;
class SettingRepository implements SettingRepositoryInterface
{
    public function Loyaltylist()
    {
        return LoyaltySetting::first();
    }

    public function Loyaltyfind($id)
    {
        return LoyaltySetting::find($id);
    }

    public function Loyaltycreate(array $data)
    {
        return LoyaltySetting::create($data);
    }

    public function Loyaltyupdate($id, array $data)
    {
        $loyalty = LoyaltySetting::find($id);
        if ($loyalty) {
            $loyalty->update($data);
            return $loyalty;
        }
        return null;
    }

    public function PaymentMethodList()
    {
        return PaymentMethod::all();
    }
     public function PaymentMethodActiveList()
    {
        return PaymentMethod::where('is_enabled', 1)->get();
    }
    public function PaymentMethodfind($id)
    {
        return PaymentMethod::find($id);
    }

    public function PaymentMethodcreate(array $data)
    {
        return PaymentMethod::create($data);
    }

    public function PaymentMethodupdate($id, array $data)
    {
        $paymentMethod = PaymentMethod::find($id);
        if ($paymentMethod) {
            $paymentMethod->update($data);
            return $paymentMethod;
        }
        return null;
    }
    public function PaymentMethoddelete($id)
    {
        $paymentMethod = PaymentMethod::find($id);
        if ($paymentMethod) {
            $paymentMethod->delete();
            return true;
        }
        return false;
    }
    public function PaymentMethodtoggleStatus($id, $status)
    {
        $paymentMethod = PaymentMethod::find($id);
        if ($paymentMethod) {
            $paymentMethod->update(['is_enabled' => $status]);
            return $paymentMethod;
        }
        return null;
    }

}
