<?php

namespace App\Http\Controllers\badmin;
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
    protected $branch_id;

    public function __construct(Request $request, OrderRepositoryInterface $OrderRepository, CartRepositoryInterface $cartRepository)
    {
        $this->OrderRepository = $OrderRepository;
        $this->cartRepository = $cartRepository;
        $this->language = $request->input('language', 'EN') ?? 'EN';
      //  $this->branch_id = Auth::user()->branches;
    }
    public function index()
    {
       // dd(Auth::user()->branches[] );
        $orders = $this->OrderRepository->branchorders(Auth::user()->branches[0]->id ?? null);
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

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }
     public function liveorders()
    {
         $orders=$this->OrderRepository->liveordersbranch(Auth::user()->branches[0]->id ?? null);

        return view('admin.pages.orders.list', compact('orders'));
    }


}
