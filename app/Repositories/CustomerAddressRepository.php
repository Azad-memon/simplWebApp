<?php

namespace App\Repositories;

use App\Repositories\Interfaces\CustomerAddressRepositoryInterface;
use App\Models\CustomerAddress;
use App\Models\CustomerAddressDetail;
use App\Models\ModelUserActivityLog;
use Auth;

class CustomerAddressRepository implements CustomerAddressRepositoryInterface
{
    public function all()
    {
        return  CustomerAddress::get();
    }

    // public function find($id)
    // {
    //     return CustomerAddress::findOrFail($id);
    // }


    public function create(array $data)
{
       $customerAddressdetail=[
           'street_address' => $data['street_address'],   // or $data['customer_id']
           'city' => $data['city'] ?? null,
           'state' => $data['state'] ?? null,
           'zip_code' => $data['zip_code'] ?? null,
           'country' => $data['country'] ?? "Pakistan",
           'additional_detail' => $data['additional_detail'] ?? null,
           'nearest_landmark' => $data['nearest_landmark'] ?? null,
       ];
     $customerAddressDetail = CustomerAddressDetail::create($customerAddressdetail);
    if ($customerAddressDetail) {
    CustomerAddress::where('customer_id', Auth::id())->update(['is_default' => false]);
    $detailData = [
        'customer_id' => Auth::id(), // or $data['customer_id']
        'title' => $data['title'] ?? 'Home',
        'address_id' => $customerAddressDetail->id,
        "is_default" => true,
        "latitude"   =>$data['latitude'] ?? '',
        "longitude"  =>$data['longitude'] ?? '',
        "latdelta"   =>$data['latdelta'] ?? '',
        "longdelta"  =>$data['longdelta'] ?? ''
    ];
   $customerAddress=CustomerAddress::create($detailData);

    ModelUserActivityLog::logActivity(
        Auth::id(),
        "has added new address titled " . ($data['title'] ?? 'Home')
    );
    }else {
        return false; // Handle error
    }
    return $customerAddress;
}
  public function getDefaultAddress($customerId)
    {
        return CustomerAddress::where('customer_id', $customerId)
            ->where('is_default', true)
            ->first();
    }
  public function getAddressesByCustomerId($customerId)
    {
        $customerAddresses=CustomerAddress::where(['customer_id' => $customerId])
                                           ->with('address')->get();
      $defaultAddress = $customerAddresses->where('is_default', true)->first() ?? null;
      $userLat = $defaultAddress?->latitude ?? '';
      $userLng = $defaultAddress?->longitude ?? '';

      $defaultAddress['branch']=$userLat!="" && $userLng!="" ? getbranchidByaddress($defaultAddress):null;

        return [
            'default_address' => $defaultAddress,
            'list' => $customerAddresses,
        ];
    }

    public function titleExists($title, $customerId)
    {
        return CustomerAddress::where('customer_id', $customerId)
            ->where('title', $title)
            ->exists();
    }

     public function findByCustomer($id, $customerId)
    {
        return CustomerAddress::where(['customer_id' => $customerId, 'id' => $id])->first();
    }

    public function updateAddressByCustomer($customerId,array $data)
    {

        $addressDetail = CustomerAddressDetail::find($data['address_id']);
        if ($addressDetail) {
            $addressDetail->street_address = $data['street_address'] ?? $addressDetail->street_address;
            $addressDetail->city = $data['city'] ?? $addressDetail->city;
            $addressDetail->state = $data['state'] ?? $addressDetail->state;
            $addressDetail->zip_code = $data['zip_code'] ?? $addressDetail->zip_code;
            $addressDetail->country = $data['country'] ?? $addressDetail->country;
            $addressDetail->additional_detail = $data['additional_detail'] ?? $addressDetail->additional_detail;
            $addressDetail->nearest_landmark = $data['nearest_landmark'] ?? $addressDetail->nearest_landmark;
            $addressDetail->save();
        }
         $customerAddress = CustomerAddress::where('address_id', $data['address_id'])->first();

            if (!empty($data['latitude']) && !empty($data['longitude'])) {
                $detailData = [
                    "latitude"  => $data['latitude'],
                    "longitude" => $data['longitude'],
                ];

                $customerAddress->update($detailData);
            }
         $customerAddress->latdelta = $data['latdelta'] ?? $customerAddress->latdelta;
         $customerAddress->longdelta = $data['longdelta'] ?? $customerAddress->longdelta;
         $customerAddress->save();
        $updatedFields = [];
        if (isset($data['street_address'])) {
            $updatedFields[] = "Street: " . $addressDetail->street_address;
        }
        if (isset($data['city'])) {
            $updatedFields[] = "City: " . $addressDetail->city;
        }
        if (isset($data['state'])) {
            $updatedFields[] = "State: " . $addressDetail->state;
        }
        if (isset($data['zip_code'])) {
            $updatedFields[] = "Zip: " . $addressDetail->zip_code;
        }
        if (isset($data['country'])) {
            $updatedFields[] = "Country: " . $addressDetail->country;
        }
        if (isset($data['additional_detail'])) {
            $updatedFields[] = "Additional Detail: " . $addressDetail->additional_detail;
        }
        if (isset($data['nearest_landmark'])) {
            $updatedFields[] = "Landmark: " . $addressDetail->nearest_landmark;
        }

        if (!empty($updatedFields)) {
            ModelUserActivityLog::logActivity(
            Auth::id(),
            "has updated address detail: " . implode(', ', $updatedFields)
            );
        }
        return $addressDetail;
    }
    public function setDefaultAddress($customerId, $addressId)
    {

        CustomerAddress::where('customer_id', $customerId)
            ->update(['is_default' => false]);
        $address = CustomerAddress::where('customer_id', $customerId)
            ->where('address_id', $addressId)
            ->first();
        if ($address) {
            $address->is_default = true;
            $address->save();
            ModelUserActivityLog::logActivity(
                Auth::id(),
                "has set address titled '{$address->title}' as default"
            );
            return $address;
        }

        return false;
    }

    public function deleteAddressByCustomer($id, $customerId)
    {

        $address = CustomerAddress::where('address_id', $id)
            ->where('customer_id', $customerId)
            ->first();
           if ($address) {

            if ($address->address_id) {
                CustomerAddressDetail::where('id', $address->address_id)->delete();
            }
            ModelUserActivityLog::logActivity(
                Auth::id(),
                "has deleted address titled '{$address->title}'"
            );
            return $address->delete();
        }
        return false;
    }

}
