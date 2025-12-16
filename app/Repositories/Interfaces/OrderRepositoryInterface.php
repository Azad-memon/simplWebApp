<?php
namespace App\Repositories\Interfaces;

interface OrderRepositoryInterface
{
    public function all();
    public function orderPOS();
    public function liveorders();
    public function find($id);
    public function create(array $data, array $cartData);
    public function myorders();
    public function orderDetail($id);
    public function kdsOrders();
    public function reorder(array $data);
    public function updateStatus($id, array $data);


    //Branch Admin
        public function branchorders($id);
        public function liveordersbranch($id);

    //  public function delete($id);
    //  public function update($id,array $data);
    //  public function updateCartQuantity($id,array $data);


}
