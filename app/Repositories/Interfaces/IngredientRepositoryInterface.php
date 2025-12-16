<?php
namespace App\Repositories\Interfaces;

interface IngredientRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function toggleStatus(array $data);
    public function getTranslation($id);
    public function getBranchIngredients($id);
    public function BranchIngredientsQuantity($id,$branchid, $quantity);

    //category
    public function createCategory(array $data);
    public function updateCategory($id, array $data);
      public function allCategories();
     public function findCategory($id);
     public function deleteCategory($id);

     public function toggleCategoryStatus(array $data);

    public function updatecustom($id,array $data);

    // pos methods
     public function getCashoutTransactions($branchId);


    // public function addUserToBranchByBranchId(array $data,$id);
    // public function finduser($id);
    // public function updateuser(array $data,$id);
}

