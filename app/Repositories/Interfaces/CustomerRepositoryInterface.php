<?php
namespace App\Repositories\Interfaces;

interface CustomerRepositoryInterface
{
    public function all();
    public function find($id);
    public function update($id, array $data);
    public function updatepassword($id, array $data);
    public function createinvoiceCustomer(array $data);
    // public function delete($id);

}

