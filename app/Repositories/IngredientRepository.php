<?php

namespace App\Repositories;

use App\Models\BranchIngredientQuantity;
use App\Models\BranchStockManagment;
use App\Models\CashoutTransaction;
use App\Models\Ingredient;
use App\Models\IngredientCategory;
use App\Models\ModelImages;
use App\Models\ModelUserActivityLog;
use App\Repositories\Interfaces\IngredientRepositoryInterface;
use Auth;

class IngredientRepository implements IngredientRepositoryInterface
{
    public function all()
    {
        return Ingredient::with('translations')->get();
    }

    public function find($id)
    {
        return Ingredient::findOrFail($id);
    }

    public function create(array $data)
    {
        //dd($data['sizes']);
        try {
            $saveData = new Ingredient;
            $saveData->ing_name = $data['ing_name'];
            $saveData->ing_unit = $data['ing_unit'];
            $saveData->ing_type = $data['ing_type'];
            $saveData->min_quantity = $data['min_quantity'];
            $saveData->category_id = $data['category_id'];
            $saveData->unit_price = $data['unit_price'] ?? 0;
            $saveData->is_quantify = $data['is_quantify'] ?? 0;
            $saveData->ingredient_label = $data['ingredient_label'] ?? '';

            $saveData->created_by = Auth::user()->id;
            if (isset($data['ing_desc']) && ! empty($data['ing_desc'])) {
                $saveData->ing_desc = $data['ing_desc'];
            }
            $saveData->save();
            $id = $saveData->ing_id;
            if (! empty($data['sizes']) && is_array($data['sizes'])) {
                foreach ($data['sizes'] as $sizeData) {
                    // dump($sizeData);
                    if (! empty($sizeData['size_id']) && ! empty($sizeData['price'])) {
                        $sizesData = [
                            'ingredient_id' => $id,
                            'size_id' => $sizeData['size_id'],
                            'price' => $sizeData['price'],
                        ];
                        // dump($sizesData);
                        $saveData->sizes()->create($sizesData);
                    }
                }
            }
            ModelUserActivityLog::logActivity(
                Auth::user()->id,
                'has added new ingredient with name '.$saveData->ing_name
            );

            if (! empty($data['images']) && is_array($data['images'])) {
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
            dd($e);
            \Log::error('Error saving ingredient: '.$e->getMessage());

            return false;
        }
    }

    public function update($id, array $data)
    {
        // dd($data);
        if (empty($data)) {
            return null;
        }
        $ingredient = Ingredient::find($data['ing_id']);
        if (! $ingredient) {
            return null;
        }
        $changeMessages = [];
        $hasChanges = false;

        if ($ingredient->pb_name !== $data['ing_name']) {
            $oldName = $ingredient->pb_name;
            $ingredient->update([
                'ing_name' => $data['ing_name'],
                'ing_unit' => $data['ing_unit'],
                'ing_desc' => $data['ing_desc'],
                'ing_type' => $data['ing_type'],
                'unit_price' => $data['unit_price'] ?? 0,
                'min_quantity' => $data['min_quantity'],
                'category_id' => $data['category_id'],
                'is_quantify' => $data['is_quantify'] ?? 0,
                'ingredient_label' => $data['ingredient_label'] ?? '',
            ]);
            if (! empty($data['sizes']) && is_array($data['sizes'])) {
                $ingredient->sizes()->delete();

                foreach ($data['sizes'] as $sizeData) {
                    if (! empty($sizeData['size_id']) && ! empty($sizeData['price'])) {
                        $ingredient->sizes()->create([
                            'ingredient_id' => $id,
                            'size_id' => $sizeData['size_id'],
                            'price' => $sizeData['price'],
                        ]);
                    }
                }
            }
            $changeMessages[] = "Changed ingredient name from '{$oldName}' to '{$data['ing_name']}'";
            $hasChanges = true;
        }
        $hiddenImages = array_filter($data, function ($key) {
            return strpos($key, 'hidden_') === 0; // Only keys that start with 'hidden_'
        }, ARRAY_FILTER_USE_KEY);

        $removedImages = [];
        $addedImages = [];

        foreach ($hiddenImages as $key => $image) {
            $imageType = str_replace('hidden_', '', $key); // Extract image type (e.g., full, left, right)

            if (empty($image)) {
                $deletedImage = $ingredient->images()->where('image_type', $imageType)->first();

                if ($deletedImage) {
                    $ingredient->images()->where('image_type', $imageType)->delete();
                    $removedImages[] = ucfirst($imageType).' image';
                }
            }
        }

        if (! empty($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $key => $imageUrl) {
               // if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                    $ingredient->images()->where('image_type', $key)->delete();
                    $image = new ModelImages();
                    $image->image = $imageUrl;
                    $image->image_type = $key;

                    $ingredient->images()->save($image);
                    $addedImages[] = ucfirst($key).' image';
               // }
            }
        }
        if (! empty($removedImages)) {
            $changeMessages[] = 'Removed '.implode(', ', $removedImages);
        }

        if (! empty($addedImages)) {
            $changeMessages[] = 'Added '.implode(', ', $addedImages);
        }
        if ($hasChanges || ! empty($removedImages) || ! empty($addedImages)) {
            $activityMessage = "Updated ingredient: {$ingredient->ing_name}. ".implode(', ', $changeMessages);
            ModelUserActivityLog::logActivity(
                Auth::user()->id,
                $activityMessage
            );

            return true;
        }

        return false;
    }

    public function delete($id)
    {
        $ingredient = Ingredient::find($id);
        if ($ingredient) {
            $delete = $ingredient->delete();
            if ($delete) {
                ModelUserActivityLog::logActivity(
                    Auth::user()->id,
                    'has deleted Ingredient with name '.$ingredient->pb_name
                );
            }

            return $delete;
        }

        return false;
    }

    public function toggleStatus(array $data)
    {
        $Ingredient = Ingredient::where('ing_id', $data['id'])->first();
        if (! $Ingredient) {
            return false;
        }
        $newStatus = $data['status'];
        $statusText = $newStatus == 1 ? 'activated' : 'deactivated';
        $update = Ingredient::where('ing_id', $data['id'])->update([
            'is_active' => $newStatus,
        ]);
        if ($update) {
            ModelUserActivityLog::logActivity(
                Auth::user()->id,
                "has $statusText the ingredient with name <b>$Ingredient->ing_name</b>"
            );
        }

        return $Ingredient;
    }

    public function getTranslation($id)
    {
        return Ingredient::with('translations')->findOrFail($id);
    }

    //for branch ingredient
    public function getBranchIngredients($branchId)
    {
        $query = Ingredient::with('translations');
        $query->where('is_active', 1);
        if ($branchId !== null) {
            $query->with([
                'branchQuantities' => function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                },
            ]);
        }
        $ingredients = $query->get();

        $finalIngredients = [];
        foreach ($ingredients as $ingredient) {
            // Fetch 'in' quantity
            $inQty = BranchStockManagment::where('branch_id', $branchId)
                ->where('ingredient_id', $ingredient->ing_id)
                ->where('type', 'in')
                ->sum('quantity');

            // Fetch 'out' quantity
            $outQty = BranchStockManagment::where('branch_id', $branchId)
                ->where('ingredient_id', $ingredient->ing_id)
                ->where('type', 'out')
                ->sum('quantity');
            // Inject custom attribute directly
            $ingredient->setAttribute('quantity_balance', $inQty - $outQty);

            $finalIngredients[] = $ingredient;
            // Add custom property directly to the model object
            // $ingredient->quantity_balance = $inQty - $outQty;
        }

        // Dump full ingredient list with quantity_balance
        return $finalIngredients;
    }

    public function BranchIngredientsQuantity($id, $branchId, $quantity)
    {
        $ingredient = Ingredient::find($id);
        if (! $ingredient) {
            return false;
        }

        $branchQuantity = BranchIngredientQuantity::where('ing_id', $id)
            ->where('branch_id', $branchId)
            ->first();

        if (! $branchQuantity) {
            $branchQuantity = new BranchIngredientQuantity();
            $branchQuantity->ing_id = $id;
            $branchQuantity->branch_id = $branchId;
        }

        $branchQuantity->qty = $quantity;
        $branchQuantity->updated_by = Auth::user()->id;
        $branchQuantity->save();

        BranchStockManagment::create([
            'branch_id' => $branchId,
            'ingredient_id' => $id,
            'quantity' => $quantity,
            'type' => 'in',
            'updated_by' => Auth::user()->id,
        ]);

        ModelUserActivityLog::logActivity(
            Auth::user()->id,
            "updated branch ingredient quantity for ingredient ID $id at branch ID $branchId to $quantity"
        );

        return $branchQuantity;
    }

    //category
    public function allCategories()
    {
        return IngredientCategory::all();
    }

    public function createCategory($data)
    {
        $saveData = new IngredientCategory();
        $saveData->name = $data['name'];
        $saveData->description = $data['desc'];
        $saveData->parent_id = $data['parent_id'];
        $saveData->is_active = $data['status'];
        if ($saveData->save()) {
            ModelUserActivityLog::logActivity(
                Auth::user()->id,
                'has added new Ingredient Category with name '.$saveData->name
            );
        }
        if (! empty($data['images']) && is_array($data['images'])) {
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
    }

    public function updateCategory($id, $data)
    {
       // dd($data);
        $category = IngredientCategory::find($id); // Assumes 'id' is category ID
        if (! $category) {
            return null;
        }

        $changeMessages = [];
        $hasChanges = false;

        // Compare and update fields
        if (
            $category->name !== $data['name'] ||
            $category->description !== $data['desc'] ||
            $category->parent_id != $data['parent_id'] ||
            $category->is_active !== $data['status']
        ) {
            $oldName = $category->name;

            $category->update([
                'name' => $data['name'],
                'description' => $data['desc'],
                'parent_id' => $data['parent_id'],
                'is_active' => $data['status'],
            ]);

            if ($oldName !== $data['name']) {
                $changeMessages[] = "Changed category name from '{$oldName}' to '{$data['name']}'";
            } else {
                $changeMessages[] = 'Updated category details';
            }

            $hasChanges = true;
        }

        // Handle removed images from hidden_* keys
        $hiddenImages = array_filter($data, function ($key) {
            return strpos($key, 'hidden_') === 0;
        }, ARRAY_FILTER_USE_KEY);

        $removedImages = [];
        $addedImages = [];

        foreach ($hiddenImages as $key => $image) {
            $imageType = str_replace('hidden_', '', $key);

            if (empty($image)) {
                $deletedImage = $category->images()->where('image_type', $imageType)->first();
                if ($deletedImage) {
                    $category->images()->where('image_type', $imageType)->delete();
                    $removedImages[] = ucfirst($imageType).' image';
                }
            }
        }

        // Handle added/updated images
        if (! empty($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $key => $imageUrl) {
                if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                    $category->images()->where('image_type', $key)->delete(); // Replace existing
                    $image = new ModelImages();
                    $image->image = $imageUrl;
                    $image->image_type = $key;

                    $category->images()->save($image);
                    $addedImages[] = ucfirst($key).' image';
                }
            }
        }

        // Collect messages
        if (! empty($removedImages)) {
            $changeMessages[] = 'Removed '.implode(', ', $removedImages);
        }

        if (! empty($addedImages)) {
            $changeMessages[] = 'Added '.implode(', ', $addedImages);
        }

        // Log activity if anything changed
        if ($hasChanges || ! empty($removedImages) || ! empty($addedImages)) {
            $activityMessage = "Updated Ingredient category: {$category->name}. ".implode(', ', $changeMessages);

            ModelUserActivityLog::logActivity(
                Auth::user()->id,
                $activityMessage
            );

            return true;
        }

        return false;
    }

    public function findCategory($id)
    {
        return IngredientCategory::find($id);
    }

    public function deleteCategory($id)
    {
        $category = IngredientCategory::with('children')->find($id);
        if ($category) {
            foreach ($category->children as $child) {
                $child->parent_id = 0; // Or set to null if your schema allows it
                $child->save();
            }
            $delete = $category->delete();
            if ($delete) {
                ModelUserActivityLog::logActivity(
                    Auth::user()->id,
                    'has deleted Ingredient Category with name '.$category->name
                );
            }

            return $delete;
        }

        return false;
    }

    public function toggleCategoryStatus(array $data)
    {
        $category = IngredientCategory::where('id', $data['id'])->first();
        if (! $category) {
            return false;
        }
        $newStatus = $data['status'];
        $statusText = $newStatus == 1 ? 'activated' : 'deactivated';
        $update = IngredientCategory::where('id', $data['id'])->update([
            'is_active' => $newStatus,
        ]);
        if ($update) {
            ModelUserActivityLog::logActivity(
                Auth::user()->id,
                "has $statusText the ingredient category with name <b>$category->name</b>"
            );
        }

        return $category;
    }

    public function updatecustom($id, $data)
    {
         $id = $data['id'];
        $branchId = $data['branchid'];
        $quantity = $data['quantity'];
        if(isset($data['std_quantity'])){
        foreach ($data['std_quantity'] as $key => $value) {
            $stdId = $key;
            $stdQty = $value;
            $ingredient = Ingredient::find($stdId);
            if (! $ingredient) {
                return false;
            }

            $branchQuantity = BranchIngredientQuantity::where('ing_id', $stdId)
                ->where('branch_id', $data['branchid'])
                ->first();
           if (!$branchQuantity) {
            $branchQuantity = new BranchIngredientQuantity();
            $branchQuantity->ing_id = $stdId;
            $branchQuantity->branch_id = $data['branchid'];
            }

            $branchQuantity->qty = $stdQty;
            $branchQuantity->updated_by = Auth::user()->id;
            $branchQuantity->save();

           BranchStockManagment::create([
            'branch_id' => $branchId,
            'ingredient_id' => $stdId,
            'quantity' => $stdQty,
            'type' => 'out',
            'updated_by' => Auth::user()->id,
        ]);
        }
     }

if( $id!=null){
        $branchQuantity = BranchIngredientQuantity::where('ing_id', $id)
            ->where('branch_id', $branchId)
            ->first();

        if (! $branchQuantity) {
            $branchQuantity = new BranchIngredientQuantity();
            $branchQuantity->ing_id = $id;
            $branchQuantity->branch_id = $branchId;
        }

        $branchQuantity->qty = $quantity;
        $branchQuantity->updated_by = Auth::user()->id;
        $branchQuantity->save();

        BranchStockManagment::create([
            'branch_id' => $branchId,
            'ingredient_id' => $id,
            'quantity' => $quantity,
            'type' => 'in',
            'updated_by' => Auth::user()->id,
        ]);
    }

        ModelUserActivityLog::logActivity(
            Auth::user()->id,
            "updated branch ingredient quantity for ingredient ID $id at branch ID $branchId to $quantity"
        );

        return $branchQuantity;
        // if (empty($data)) {
        //     return null;
        // }
        // $ingredient = Ingredient::find($data['ing_id']);
        // if (!$ingredient) {
        //     return null;
        // }
        // $changeMessages = [];
        // $hasChanges = false;
    }

    // pos methods


    public function getCashoutTransactions($id)
    {
        $cashout_data = CashoutTransaction::where('branch_id',$id)->latest()->get();
        if($cashout_data)
        {
            return $cashout_data;
        }
        else
        {
            return false;
        }
    }
}
