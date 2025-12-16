<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\CouponRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\ProductVariantRepositoryInterface;


class CouponController extends Controller
{
    protected $couponRepository;

    protected $productRepository;
    protected $productVariantRepository;
    public function __construct(CouponRepositoryInterface $couponRepository, ProductRepositoryInterface $productRepository, ProductVariantRepositoryInterface $productVariantRepository)
    {
        $this->couponRepository = $couponRepository;
        $this->productRepository = $productRepository;
        $this->productVariantRepository = $productVariantRepository;
    }

    // List all coupons
    public function index()
    {
        $coupons = $this->couponRepository->all();
        return view('admin.pages.coupon.list', compact('coupons'));
    }

    public function add()
    {
        $products = Product::all();
        return view('admin.pages.coupon.add', compact(('products')));
    }

    // Show a single coupon
    public function edit($id)
    {
        $products = Product::all();
        $variants = ProductVariant::all();
        $coupon = $this->couponRepository->find($id);
        return view('admin.pages.coupon.edit', compact('coupon', 'products','variants'));
    }

    // Store new coupon
    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:coupons,code|max:50',
            'discount' => 'required|numeric|min:1',
            'type' => 'required|in:percentage,fixed', // percentage ya fixed
            'expire_at' => 'required|date|after:today',
            'max_usage' => 'required|integer|min:1',
            'status' => 'required|boolean',
        ]);
        $data1 = $request->all();
        $data1 = array_diff_key($data1, $request->file());
        //  $datavideo = saveVideo($request->file('product_video'));
        $data = saveImages($request->file());
        if (!empty($data) || !empty($data1)) {
            $mergedArray = array_merge($data, $data1);
        }
        $data = $mergedArray;

        $coupon = $this->couponRepository->create($data);
        return response()->json([
            'success' => true,
            'message' => 'Coupon created successfully.',
            'data' => $coupon
        ]);
    }

    // Update coupon
    public function update(Request $request, $id)
    {
        $coupon = $this->couponRepository->find($id);

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon not found.'
            ], 404);
        }

        $data = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'discount' => 'required|numeric|min:1',
            'type' => 'required|in:percentage,fixed',
            'expire_at' => 'required|date|after:today',
            'max_usage' => 'required|integer|min:1',
            'status' => 'required|boolean',
        ]);
        $updatedCoupon = $this->couponRepository->update($id, $request->all());
        return response()->json([
            'success' => true,
            'message' => 'Coupon updated successfully.',
            'data' => $updatedCoupon
        ]);
    }


    // Delete coupon
    public function destroy($id)
    {
        $this->couponRepository->delete($id);
        return redirect()->back()->with('success', 'Coupon deleted successfully.');

    }

    // Toggle status (active/inactive)
    public function toggleStatus(Request $request)
    {
        $data = $request->only(['id', 'status']);
        $coupon = $this->couponRepository->toggleStatus($data);

        return response()->json([
            'message' => 'Coupon status updated successfully',
            'data' => $coupon
        ]);
    }

    public function getproductVarient(Request $request)
    {
        //dd( $request->product_id);
        $variants = $this->productVariantRepository->findByProductId($request->product_id);

        return response()->json($variants);
    }
    public function toggleAddonStatus(Request $request)
    {
        $data = $this->couponRepository->toggleStatus($request->all());
        if ($data) {
            $message = $request->input('status') == "active" ? "Coupon is activated" : "Coupon is deactivated";
            return response()->json(['message' => $message]);
        }
    }
}
