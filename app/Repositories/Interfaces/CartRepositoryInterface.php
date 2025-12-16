<?php
namespace App\Repositories\Interfaces;

interface CartRepositoryInterface
{
     public function all($code=null,$order_platform = null);
     public function find($id);
     public function create(array $data);
     public function delete($id);
     public function update(array $data);
     public function updateCartQuantity(array $data);
     public function finditem($id);
     public function updateCartNote(array $data);
    public function updateCartOrderNote(array $data);
    public function updateTax(array $data);



}
