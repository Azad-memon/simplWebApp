<?php

namespace App\Repositories;

use App\Repositories\Interfaces\DealRepositoryInterface;

use App\Models\Deal;
use App\Models\ModelUserActivityLog;
use Auth;
use App\Models\ModelImages;

class DealRepository implements DealRepositoryInterface
{
    public function all()
    {

        return Deal::all();
    }

    public function find($id)
    {
        return Deal::findOrFail($id);
    }


    public function create(array $data)
    {
           try {
            $saveData = new Deal;
            $saveData->name = $data['name'];
            $saveData->cat_id = $data['cat_id'];
            $saveData->slug = $data['slug'];
          //  $saveData->created_by =Auth::user()->id;
            if (isset($data['desc']) && !empty($data['desc']))
            $saveData->desc = $data['desc'];
            $saveData->save();
            $id = $saveData->id;

            ModelUserActivityLog::logActivity(
                 Auth::user()->id,
                "has added new product variant with name " . $saveData->name
            );

            if (!empty($data['images']) && is_array($data['images'])) {
                foreach ($data['images'] as $key => $imageUrl) {
                    $image = new ModelImages();
                    $image->image = $imageUrl;
                    $image->image_type = $key;
                    $imageSaved = $saveData->images()->save($image);

                    // if (!$imageSaved) {
                    //     cd('Error saving image: ' . $image->image_type);
                    // }
                }
            }
            return $saveData;
        } catch (\Exception $e) {
            \Log::error("Error saving product variant: " . $e->getMessage());
            return false;
        }

      return  $product;
    }

    public function update($id, array $data)
    {

        $product = Product::find($id);
        if ($product) {
           $update= $product->update($data);
            if ($update) {
                ModelUserActivityLog::logActivity(
                    Auth::user()->id,
                    "has updated product with ID " . $id
                );
        }
        return $update;
        }
        return false;
    }

    public function delete($id)
    {

        $product = Product::find($id);
        if ($product) {
            $delete=$product->delete();
            if ($delete) {
                ModelUserActivityLog::logActivity(
                     Auth::user()->id,
                    "has deleted product with ID " .$id
                );
        }
        return $delete;
        }
        return false;
    }

     public function toggleStatus(array $data)
    {

        $product = Product::where("id", $data['id'])->first();
        if (!$product) {
            return false;
        }
        $newStatus = $data['status'];
        $statusText = $newStatus == 1 ? 'activated' : 'deactivated';
        $update = Product::where("id", $data['id'])->update([
            'is_active' => $newStatus,
        ]);
        if ($update) {
            ModelUserActivityLog::logActivity(
                 Auth::user()->id,
                "has $statusText the product with name <b>$product->name</b>"
            );
        }
    return $product;
    }
}
