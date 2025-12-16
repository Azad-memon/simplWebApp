<?php

namespace App\Repositories;

use App\Repositories\Interfaces\AddonsRepositoryInterface;
use App\Models\AddonIngredient;
use App\Models\ModelUserActivityLog;
use Auth;

class AddonsRepository implements AddonsRepositoryInterface
{
    public function all()
    {

        return AddonIngredient::get();
    }
     public function find($id)
    {

        return AddonIngredient::find($id);

    }

    public function getAddonsByProductId($id)
    {

        return AddonIngredient::where('product_id', $id)->get();

    }


    public function create(array $data)
    {


        try {
            $saveData = new AddonIngredient;
            $saveData->addonable_id = $data['addonable_id'];
            $saveData->addonable_type = $data['addonable_type'];
            $saveData->product_id = $data['product_id'];
            $saveData->price = $data['price'];
            $saveData->qty = $data['qty'];
            $saveData->desc = $data['desc'];
            $saveData->is_replace = $data['is_replace'];
            $saveData->save();

              if (!empty($data['ingredient_ids'])) {
            foreach ($data['ingredient_ids'] as $ingredientId) {
                $saveData->items()->create(['ingredient_id' => $ingredientId]);
            }
        }

            ModelUserActivityLog::logActivity(
            Auth::user()->id,
            "has created Addon with id {$saveData->id}"
            );

            return $saveData;
        } catch (\Exception $e) {
            \Log::error("Error saving addon: " . $e->getMessage());
            return false;
        }
    }


    public function update($id, array $data)
    {
       // dd($data);
      if (empty($data)) {
            return null;
        }
        $ingredient = AddonIngredient::find($id);
        if (!$ingredient) {
            return null;
        }
        $changeMessages = [];
        $hasChanges = false;
        foreach ($data as $key => $value) {
            if (isset($ingredient->$key) && $ingredient->$key != $value) {
            $changeMessages[] = "changed $key from '{$ingredient->$key}' to '$value'";
            $ingredient->$key = $value;
            $hasChanges = true;
            }
        }

        if ($hasChanges) {
            $ingredient->save();
            ModelUserActivityLog::logActivity(
            Auth::user()->id,
            "has updated AddonIngredient with id $id: " . implode(', ', $changeMessages)
            );
        }
          if (!empty($data['ingredient_ids'])) {

        $ingredient->items()->delete();

        foreach ($data['ingredient_ids'] as $ingredientId) {
            $ingredient->items()->create([
                'ingredient_id' => $ingredientId
            ]);
        }

        $changeMessages[] = "updated ingredients: " . implode(', ', $data['ingredient_ids']);
         if (!empty($changeMessages)) {
        ModelUserActivityLog::logActivity(
            Auth::id(),
            "has updated Addon with id $id: " . implode(', ', $changeMessages)
        );
    }

    }


        return $ingredient;



    }

    public function delete($id)
    {

        $addon = AddonIngredient::find($id);
        if ($addon) {
            $delete = $addon->delete();
            if ($delete) {
                ModelUserActivityLog::logActivity(
                    Auth::user()->id,
                    "has deleted addon with ID " . $id
                );
            $addon->items()->delete();
            }

            return $delete;
        }
        return false;

    }

    public function toggleStatus(array $data)
    {
        $addon = AddonIngredient::where("id", $data['id'])->first();
        if (!$addon) {
            return false;
        }

        $addon->status = $data['status'];
        if ($addon->save()) {
            ModelUserActivityLog::logActivity(
                Auth::user()->id,
                "has toggled status of addon with ID " . $data['id'] . " to " . ($data['status'] ? 'active' : 'inactive')
            );
            return true;
        }
        return false;
    }

}
