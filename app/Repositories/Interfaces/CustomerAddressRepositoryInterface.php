<?php
namespace App\Repositories\Interfaces;

interface CustomerAddressRepositoryInterface
{
    public function all();
    // public function find($id);
     public function create(array $data);
     public function getDefaultAddress($id);
     public function getAddressesByCustomerId($id);
     public function titleExists($title, $customerId);
     public function findByCustomer($id,$customerId);
     public function deleteAddressByCustomer($id,$customerId);

    // public function update($id, array $data);
    // public function delete($id);


}

