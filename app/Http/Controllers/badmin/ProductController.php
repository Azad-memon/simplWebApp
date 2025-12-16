<?php
namespace App\Http\Controllers\badmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\ProductVariantRepositoryInterface;

class ProductController extends Controller
{
      protected $productRepository;
      protected $productVariantRepo;


    public function __construct(ProductRepositoryInterface $productRepository, ProductVariantRepositoryInterface $productVariantRepo)
    {
        $this->productRepository = $productRepository;
        $this->productVariantRepository = $productVariantRepo;
    }
    public function index()
    {
        $products = $this->productRepository->all();
        return view('admin.badmin.products.index', compact('products'));

    }
    public function view($id)
    {
        $product = $this->productRepository->find($id);

        return view('admin.badmin.products.productdetail', compact('product'));
    }
}
