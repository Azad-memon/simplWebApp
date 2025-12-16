<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Validator;
use Illuminate\Support\Facades\Crypt;
use App\Models\Banner;
use App\Models\Product;
use App\Models\AppHomePageProduct;
use Illuminate\Validation\Rule;

class BannerController extends Controller
{
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;

    }
    public function index()
    {
        $appBanners = $this->productRepository->getAllBanners();
        return view('admin.pages.banners.list', compact('appBanners'));
    }
    public function create()
    {
        $categories = Category::where('status', 'active')->get();
        $products = Product::where('is_active', 1)->get();
        return view('admin.pages.banners.add', compact('categories', 'products'));
    }
    public function edit($id)
    {
        $categories = Category::where('status', 'active')->get();
        $products = Product::where('is_active', 1)->get();
        $banner = $this->productRepository->getbanner($id);
        return view('admin.pages.banners.edit', compact('banner', 'categories', 'products'));

    }

    public function store(Request $request)
    {

        $data = $request->validate([
            'banner_title' => 'required|string|max:255',
            'banner_description' => 'nullable|string',
            'type' => 'required|in:category,product,default',
            'category_id' => 'required_if:type,category|nullable|exists:categories,id',
            'product_id' => 'required_if:type,product|nullable|exists:products,id',
            'full' => 'required|nullable|image|mimes:jpeg,png,jpg',
            // 'banner_video' => 'nullable|mimetypes:video/mp4,video/avi,video/mpeg',
        ]);

        $data1 = $data;
        $data1 = array_diff_key($data1, $request->file());
        //  $datavideo = saveVideo($request->file('product_video'));
        $data = saveImages($request->file());
        if (!empty($data) || !empty($data1)) {
            $mergedArray = array_merge($data, $data1);
        }
        $data = $mergedArray;


        $this->productRepository->saveBanner($data);

        return response()->json(['message' => 'Banner created successfully.']);
    }



    public function update(Request $request, $id)
    {
        //dd($request->all());
        $request->validate([
            'banner_title' => 'required|string|max:255',
            'banner_description' => 'nullable|string',
            'type' => 'required|in:category,product,default',
            'category_id' => 'required_if:type,category|nullable|exists:categories,id',
            'product_id' => 'required_if:type,product|nullable|exists:products,id',
        ]);

        // Manually validate media if type is default
//    if ($request->type === 'default') {
//     $mediaErrors = [];

        //     if (!$request->hasFile('full') && !$request->input('existing_full')) {
//         $mediaErrors['full'] = ['Banner image is required for default type.'];
//     }

        //     if (!$request->hasFile('banner_video') && !$request->input('existing_banner_video')) {
//         $mediaErrors['banner_video'] = ['Banner video is required for default type.'];
//     }

        //     if (!empty($mediaErrors)) {
//         return response()->json(['errors' => $mediaErrors], 422);
//     }
// }
        $data = $request->all();
        $mergedArray = null;
        $data1 = $request->all();
        $data1 = array_diff_key($data1, $request->file());
        $data = saveImages($request->file());


        if (!empty($data) || !empty($data1)) {
            $mergedArray = array_merge($data, $data1);
        }
        $data = $mergedArray;



        $this->productRepository->updateBanner($id, $data);

        return response()->json(['message' => 'Banner updated successfully.']);
    }

    public function destroy($id)
    {

        $this->productRepository->deleteBanner($id);
        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted successfully.');
    }
    public function bannerToogle(Request $request)
    {
        $data = $this->productRepository->bannerTooglestatus($request->all());
        if ($data) {
            $message = "Status Updated";
            return response()->json(['message' => $message]);
        }
    }
    //App Home page product
    public function HomepageProducts()
    {
        $appBanners = $this->productRepository->getAllHomeProduct();
        return view('admin.pages.HomePageProduct.list', compact('appBanners'));
    }
    public function createHomeProduct()
    {
        $products = Product::where('is_active', 1)->get();
        return view('admin.pages.HomePageProduct.add', compact('products'));
    }
    public function editeHomeProduct($id)
    {
        $banner = $this->productRepository->getHomeBanner($id);
        $products = Product::where('is_active', 1)->get();
        return view('admin.pages.HomePageProduct.edit', compact('banner', 'products'));

    }
    public function saveHomeProduct(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|unique:app_home_page_products,product_id',
        ]);
        $data = $request->all();
        $mergedArray = null;
        $data1 = $request->all();
        $data1 = array_diff_key($data1, $request->file());
        $data = saveImages($request->file());


        if (!empty($data) || !empty($data1)) {
            $mergedArray = array_merge($data, $data1);
        }
        $data = $mergedArray;
        $this->productRepository->saveHomeProduct($data);
        return response()->json(['message' => 'Product added successfully.']);
    }
    public function updateHomeProduct(Request $request)
    {
        $existing = AppHomePageProduct::where('product_id', $request->product_id)->first();

        $data = $request->validate([
            'product_id' => [
                'required',
                Rule::unique('app_home_page_products', 'product_id')->ignore(optional($existing)->id),
            ],
        ]);
        $data = $request->all();
        $mergedArray = null;
        $data1 = $request->all();
        $data1 = array_diff_key($data1, $request->file());
        $data = saveImages($request->file());


        if (!empty($data) || !empty($data1)) {
            $mergedArray = array_merge($data, $data1);
        }
        $data = $mergedArray;
        $this->productRepository->updateHomeProduct($data);
        return response()->json(['message' => 'Product added successfully.']);
    }
    public function deleteHomeProduct($id)
    {

        $this->productRepository->deleteHomeProduct($id);
        return redirect()->route('admin.home.product.index')->with('success', 'Product deleted successfully.');
    }
}

