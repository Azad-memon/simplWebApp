<?php
namespace App\Repositories\Interfaces;

interface CategoryRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function dropdowndata();
    public function getTranslation($id);
    public function allactive($id, $language);
    public function getProductCategories($catId = null, $active = null, $is_parent = null);
}

