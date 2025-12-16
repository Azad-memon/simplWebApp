<?php

namespace App\Http\Controllers\API;
use App\Models\ProductVariant;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Auth;


class ProductController extends BaseController
{
      protected $productRepository;
      protected $productVariantRepository;


    public function __construct(Request $request,ProductRepository $productRepository, ProductVariant $productVariantRepository)
    {
        $this->productRepository = $productRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->language = $request->input('language', 'EN') ?? 'EN';
    }
    public function show($id)
    {
     $language = $this->language;
     $product = $this->productRepository->findproductApi($id);
     if ($product=="") {
            return $this->sendError('No product found.', [], 404);
        }
        return $this->sendResponse($product, 'product retrieved successfully.');
    }
      public function featured()
    {
        $language = $this->language;
        $product = $this->productRepository->findproductApiFeatured();
          if ($product=="") {
            return $this->sendError('No product found.', [], 404);
        }
        return $this->sendResponse($product, 'product retrieved successfully.');
    }
       public function bestSeller()
    {
       $language = $this->language;
       $product = $this->productRepository->findproductApiBestSeller();
          if ($product=="") {
            return $this->sendError('No product found.', [], 404);
        }
        return $this->sendResponse($product, 'product retrieved successfully.');
    }

     public function wishlist(Request $request)
    {
        $language = $this->language;
        $product = $this->productRepository->getwishlist();
         if ($product=="") {
            return $this->sendError('No product found.', [], 404);
        }
        return $this->sendResponse($product, 'product retrieved successfully.');

    }

    public function togglewishlist(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);
        $productId = $request->product_id;
        $product = $this->productRepository->findproductApi( $productId);
        $message = $this->productRepository->savewishlist($productId);
       if ($product=="") {
            return $this->sendError('No product found.', [], 404);
        }
        return $this->sendResponse($product,  $message);
    }

 public function banners(Request $request)
    {
  $language = $this->language;
        $product = $this->productRepository->BannersList();
         if ($product=="") {
            return $this->sendError('No product found.', [], 404);
        }
        return $this->sendResponse($product, 'product retrieved successfully.');

    }
    public function homeproducts(Request $request)
    {
        $language = $this->language;
        $product = $this->productRepository->HomeProductList();
         if ($product=="") {
            return $this->sendError('No product found.', [], 404);
        }
        return $this->sendResponse($product, 'product retrieved successfully.');

    }

}
