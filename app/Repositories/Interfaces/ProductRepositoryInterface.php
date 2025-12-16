<?php
namespace App\Repositories\Interfaces;

interface ProductRepositoryInterface

{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getTranslation($id);
     public function toggleStatus(array $data);

     public function toggleFlag(array $data);
    // public function findByCategory($id);






    //Api
    public function findproductApi($id);
    public function findproductApiBestSeller();
    public function findproductApiFeatured();
    public function findproductApiNew();
    public function findproductApiRecommended($id);
     public function getwishlist();
    public function savewishlist($id);
    public function HomeProductList();
     public function BannersList();



   //Size
    public function manageSize();
    public function saveSize(array $data);
    public function getSize($id);
    public function deleteSize($id);
    public function updateSize($id,array $data);

    //unit
    public function manageUnit();
    public function saveUnit(array $data);
    public function getUnit($id);
    public function deleteUnit($id);
    public function updateUnit($id,array $data);

    //App Banner
    public function getAllBanners();
    public function saveBanner(array $data);
    public function getbanner($id);
    public function deleteBanner($id);
    public function updateBanner($id,array $data);
   public function bannerTooglestatus(array $data);

   //App Home page product
    public function getAllHomeProduct();
     public function getHomeBanner($id);
    public function saveHomeProduct(array $data);
    public function updateHomeProduct(array $data);
    public function deleteHomeProduct($id);





}

