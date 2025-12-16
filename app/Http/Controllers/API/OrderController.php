<?php

namespace App\Http\Controllers\API;
use App\Repositories\Interfaces\BranchRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\CartRepositoryInterface;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Helpers\MessageHelper;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class OrderController extends BaseController
{
    protected $OrderRepository;
    protected $cartRepository;
    protected $language;
    protected $branchRepository;

    public function __construct(Request $request, OrderRepositoryInterface $OrderRepository, CartRepositoryInterface $cartRepository, BranchRepositoryInterface $branchRepository)
    {
        $this->OrderRepository = $OrderRepository;
        $this->cartRepository = $cartRepository;
        $this->language = $request->input('language', 'EN') ?? 'EN';
        $this->branchRepository = $branchRepository;
    }
    public function store(Request $request)
    {
        $language = $this->language;
        try {
            $request->validate([
                'address_id' => 'required',
                'dining_type' => 'required',
                'delivery_type' => 'required',
                // 'total_amount' => 'required|numeric|min:1',
            ]);
        } catch (\Throwable $e) {
            $errors = MessageHelper::formatErrors($e);
            $fields = ['validation.error'];
            $successMessages = MessageHelper::defaultSuccessMessages($language, $fields);
            $flatMessages = MessageHelper::formatMessages($successMessages);
            return $this->sendError($flatMessages[0] ?? "Errors", $errors, 200);
        }
        DB::beginTransaction();
        try {
            $cart = $this->cartRepository->all();
            $response = $this->OrderRepository->create($request->all(), $cart);

            DB::commit();
            return $this->sendResponse($response, "Order created successfully");

        } catch (\Throwable $e) {
            DB::rollBack();
            // dd($e);
            return $this->sendError("Order creation failed", [$e->getMessage()], 500);
        }
    }
    public function myorders(Request $request)
    {
        try {
            $response = $this->OrderRepository->myorders();
            return $this->sendResponse($response, "Order retrieved successfully");

        } catch (\Throwable $e) {
           // DB::rollBack();
            // dd($e);
            return $this->sendError("Order creation failed", [$e->getMessage()], 500);
        }

    }
     public function orderDetail(Request $request, $id)
    {
        try {
            $response = $this->OrderRepository->orderDetail($id);
            return $this->sendResponse($response, "Order detail retrieved successfully");

        } catch (\Throwable $e) {
          //  DB::rollBack();
            // dd($e);
            return $this->sendError("Order detail retrieval failed", [$e->getMessage()], 500);
        }
    }
    public function reorder(Request $request)
    {
        // dd($request->all());
        try {
            $request->validate([
                'order_id' => 'required',
            ]);
            DB::beginTransaction();
            $response = $this->OrderRepository->reorder($request->all());
            DB::commit();
            return $this->sendResponse($response, "Reorder created successfully");

        } catch (\Throwable $e) {
            DB::rollBack();
            // dd($e);
            return $this->sendError("Order creation failed", [$e->getMessage()], 500);
        }

    }
    public function getBranch(Request $request)
    {
        //dd($request->all());
        try {
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);
        } catch (\Throwable $e) {
            $errors = MessageHelper::formatErrors($e);
            $fields = ['validation.error'];
            $successMessages = MessageHelper::defaultSuccessMessages( $this->language, $fields);
            $flatMessages = MessageHelper::formatMessages($successMessages);
            return $this->sendError($flatMessages[0] ?? "Errors", $errors, 200);
        }
        $response = $this->branchRepository->getBranch($request->all());
        return $this->sendResponse($response, "Branch retrieved successfully");
    }
    public function getBranchList(Request $request)
    {
        $response = $this->branchRepository->allactive();
        return $this->sendResponse($response, "Branch list retrieved successfully");
    }

}
