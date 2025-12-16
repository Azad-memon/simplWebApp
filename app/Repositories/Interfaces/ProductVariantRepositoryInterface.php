<?php
namespace App\Repositories\Interfaces;

interface ProductVariantRepositoryInterface
{
    public function findByProductId($id);
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function toggleStatus(array $data);
    public function toggleIngredientStatus(array $data);


}

