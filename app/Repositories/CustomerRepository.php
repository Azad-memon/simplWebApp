<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Models\ModelImages;
use App\Models\ModelUserActivityLog;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use Auth;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function all()
    {
        return Customer::all();
    }

    public function find($id)
    {
        return Customer::with('loyaltyHistories')->findOrFail($id);
    }

    public function update($id, array $data)
    {
        // dd($data);
        $customer = Customer::with('images')->find($id);
        if ($customer) {
            $update = $customer->update([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
            ]);
            //  if (isset($data['images']) && !empty($data['images'])) {
        //         foreach ($data['images'] as $key => $imageUrl) {
        //             $image = $customer->images()->where('image_type', $key)->first();
        //             if ($image) {
        //                 // Update existing image
        //                 $image->image = $imageUrl;
        //                 $image->save();
        //             } else {
        //                 // Create new image
        //                 $image = new ModelImages();
        //                 $image->image = $imageUrl;
        //                 $image->image_type = $key;
        //                 $customer->images()->save($image);
        //             }

        //             // if (!$imageSaved) {
        //             //     cd('Error saving image: ' . $image->image_type);
        //             // }
        //         }
        //     }
            if ($update) {
                ModelUserActivityLog::logActivity(
                    Auth::user()->id,
                    "has updated customer from <b>{$customer->first_name} {$customer->last_name}</b> to <b>".$data['first_name'].' '.$data['last_name'].'</b>'
                );

                return $customer;
            } else {
                return false;
            }
        }

        return false;
    }

    public function updatepassword($id, array $data)
    {
        $customer = Customer::find($id);
        if ($customer) {
            $update = $customer->update(['password' => bcrypt($data['password'])]);
            if ($update) {
                ModelUserActivityLog::logActivity(
                    Auth::user()->id,
                    "has updated password for customer <b>{$customer->first_name} {$customer->last_name}</b>"
                );

                return $customer;
            } else {
                return false;
            }
        }

        return false;
    }

  public function createinvoiceCustomer(array $data)
  {
    //dd($data);
       $customer = Customer::where('phone', $data['customer_phone'])->first();

    if ($customer) {
        return $customer->id;
    }
    // $customer = Customer::create([
    //     'first_name' => $data['customer_name'],
    //     'last_name'  => $data['last_name'] ?? '',
    //     'email'      => $data['customer_email'] ?? '',
    //     'password'   => bcrypt(123),
    //     'phone'      => $data['customer_phone'],
    // ]);

    return $customer->id ?? 0;
  }
}
