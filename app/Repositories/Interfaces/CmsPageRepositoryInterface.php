<?php

namespace App\Repositories\Interfaces;

use App\Models\CmsPage;

interface CmsPageRepositoryInterface
{
    public function all();
    public function find($id): ?CmsPage;
    public function create(array $data): CmsPage;
    public function update($id, array $data): bool;
    public function delete($id): bool;
    public function toggleStatus(array $data);

    //Api
    public function findwithslug($slug): ?CmsPage;
    public function savecontact($data);

    public function storePopup($data);
    public function getPopup();
}
