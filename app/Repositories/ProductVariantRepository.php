<?php

namespace App\Repositories;

use App\Repositories\Interfaces\ProductVariantRepositoryInterface;
use App\Models\ProductVariant;
use App\Models\ModelUserActivityLog;
use App\Models\ModelImages;
use App\Models\IngredientProductVariant;
use Auth;

class ProductVariantRepository implements ProductVariantRepositoryInterface
{
    public function findByProductId($id)
    {
        return ProductVariant::with('sizes')->where('product_id', $id)->get();
    }

    public function find($id)
    {
        return ProductVariant::findOrFail($id);
    }


    public function create(array $data)
    {
           try {
            $saveData = new ProductVariant;
            $saveData->unit = $data['unit'];
            $saveData->price = $data['price'];
            $saveData->product_id = $data['product_id'];
            $saveData->sku = $data['sku'];
            $saveData->size = $data['size'];
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
   //    return  $product;
    }

    public function update($id, array $data)
    {
       // dd($data);

        $product = ProductVariant::find($id);
        if ($product) {
        $changeMessages = [];
        $hasChanges = false;

        if ($product->unit !== $data['unit']) {
            $oldUnit = $product->unit;
            $product->update(['unit' => $data['unit']]);
            $changeMessages[] = "Changed unit from <b>'{$oldUnit}'</b> to <b>'{$data['unit']}'</b>";
            $hasChanges = true;
        }

        if ($product->size !== $data['size']) {
            $oldSize = $product->size;
            $product->update(['size' => $data['size']]);
            $changeMessages[] = "Changed size from <b>'{$oldSize}' to '{$data['size']}'</b>";
            $hasChanges = true;
        }

        if ($product->sku !== $data['sku']) {
            $oldSku = $product->sku;
            $product->update(['sku' => $data['sku']]);
            $changeMessages[] = "Changed SKU from <b>'{$oldSku}' to '{$data['sku']}'</b>";
            $hasChanges = true;
        }

        if ($product->price !== $data['price']) {
            $oldPrice = $product->price;
            $product->update(['price' => $data['price']]);
            $changeMessages[] = "Changed price from <b>'{$oldPrice}' to '{$data['price']}'</b>";
            $hasChanges = true;
        }

        if ($hasChanges) {
            ModelUserActivityLog::logActivity(
                Auth::user()->id,
                "Updated product variant with ID " . $id . ": " . implode(', ', $changeMessages)
            );

        }
          $hiddenImages = array_filter($data, function ($key) {
            return strpos($key, 'hidden_') === 0; // Only keys that start with 'hidden_'
        }, ARRAY_FILTER_USE_KEY);

        $removedImages = [];
        $addedImages = [];

        foreach ($hiddenImages as $key => $image) {
            $imageType = str_replace('hidden_', '', $key); // Extract image type (e.g., full, left, right)

            if (empty($image)) {
                $deletedImage = $product->images()->where('image_type', $imageType)->first();

                if ($deletedImage) {
                    $product->images()->where('image_type', $imageType)->delete();
                    $removedImages[] = ucfirst($imageType) . ' image';
                }
            }
        }

        if (!empty($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $key => $imageUrl) {
              //  if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                    $product->images()->where('image_type', $key)->delete();
                    $image = new ModelImages();
                    $image->image = $imageUrl;
                    $image->image_type = $key;

                    $product->images()->save($image);
                    $addedImages[] = ucfirst($key) . ' image';
               // }
            }
        }
        if (!empty($removedImages)) {
            $changeMessages[] = 'Removed ' . implode(', ', $removedImages);
        }

        if (!empty($addedImages)) {
            $changeMessages[] = 'Added ' . implode(', ', $addedImages);
        }
        if ($hasChanges || !empty($removedImages) || !empty($addedImages)) {
            $activityMessage = "Updated product variant: {$product->name}. " . implode(', ', $changeMessages);
            ModelUserActivityLog::logActivity(
               Auth::user()->id,
                $activityMessage
            );
            return true;
        }


        return false;
    }

}





    public function delete($id)
    {

        $product = ProductVariant::find($id);
        if ($product) {
            $delete=$product->delete();
            if ($delete) {
                ModelUserActivityLog::logActivity(
                     Auth::user()->id,
                    "has deleted product variant with ID " .$id
                );
        }
        return $delete;
        }
        return false;
    }

     public function toggleStatus(array $data)
    {

        $product = ProductVariant::where("id", $data['id'])->first();
        if (!$product) {
            return false;
        }
        $newStatus = $data['status'];
        $statusText = $newStatus == 1 ? 'activated' : 'deactivated';
        $update = ProductVariant::where("id", $data['id'])->update([
            'is_active' => $newStatus,
        ]);
        if ($update) {
            ModelUserActivityLog::logActivity(
                 Auth::user()->id,
                "has $statusText the product variant with name <b>$product->name</b>"
            );
        }
    return $product;
    }
    public function toggleIngredientStatus(array $data)
    {

        $id = $data['id'];
      //  $ingredientId = $data['ingredient_id'];
        $newStatus = $data['status'];
        IngredientProductVariant::where('id', $id)
           // ->where('ingredient_id', $ingredientId)
            ->update(['status' => $newStatus]);

        // $productVariant = ProductVariant::find($productVariantId);
        // if (!$productVariant) {
        //     return false;
        // }

        // $pivotRecord = $productVariant->ingredients()->where('ingredient_id', $ingredientId)->first();
        // if (!$pivotRecord) {
        //     return false;
        // }

        // $productVariant->ingredients()->updateExistingPivot($ingredientId, ['status' => $newStatus]);

        $statusText = $newStatus == 1 ? 'activated' : 'deactivated';

        ModelUserActivityLog::logActivity(
             Auth::user()->id,
            "has $statusText the ingredient with ID <b>$id</b> for product variant ID <b>$id</b>"
        );

        return true;
    }
}
