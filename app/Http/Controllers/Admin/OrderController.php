<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\CartRepositoryInterface;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class OrderController extends Controller
{
    protected $OrderRepository;
    protected $cartRepository;
    protected $language;

    public function __construct(Request $request, OrderRepositoryInterface $OrderRepository, CartRepositoryInterface $cartRepository)
    {
        $this->OrderRepository = $OrderRepository;
        $this->cartRepository = $cartRepository;
        $this->language = $request->input('language', 'EN') ?? 'EN';
    }
    public function index()
    {
        $orders = $this->OrderRepository->all();
       // dd( $orders );
        return view('admin.pages.orders.list', compact('orders'));
    }
    public function show($id){
        $order = $this->OrderRepository->find($id);
        return view('admin.pages.orders.show', compact('order'));
    }
    public function updateStatus(Request $request, $id)
    {

        $data = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);
        $order = $this->OrderRepository->updateStatus($id, $data);

        return redirect()->route('admin.order.show', $id)->with('success', 'Order status updated successfully.');
    }
     public function liveorders()
    {
         $orders=$this->OrderRepository->liveorders();

        return view('admin.pages.orders.list', compact('orders'));
    }
      public function kdsOrders()
    {
         $orders=$this->OrderRepository->kdsOrders();

        return view('admin.pages.orders.kds.index', compact('orders'));
    }
      public function kdsOrdersDetails($id){
        $order = $this->OrderRepository->find($id);

        return view('admin.pages.orders.kds.details', compact('order'));
    }


}
