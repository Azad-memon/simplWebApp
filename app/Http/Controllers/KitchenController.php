<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    protected $OrderRepository;

    public function __construct(OrderRepositoryInterface $OrderRepository)
    {
        $this->OrderRepository = $OrderRepository;
    }

    public function kdsView($id)
    {
        $orders = Order::with(['items.productVariant.product', 'items.productVariant.sizes']);

        if (auth()->user()->role_id == User::ROLE_WAITER) {
           $orders = $orders->whereIn('status', [Order::STATUS_PROCESSING, Order::STATUS_PREPARING, Order::STATUS_READY]);
            $orders = $orders->where('staff_id', auth()->user()->id);
        }else{

            $orders = $orders->whereIn('status', [Order::STATUS_PROCESSING, Order::STATUS_PREPARING]);
        }
        $orders = $orders->where('branch_id', $id)
            ->orderBy('id', 'asc') // oldest first, newest at bottom
            ->get();

        return view('admin.staff.partials.kitchen.display', compact('orders'));
    }

    public function refreshKds(Request $request)
    {
        $id = $request->input('branch_id');
        $order_id = $request->input('order_id');
        $orders = '';
        //  if($order_id!=""){
        //     $orders = Order::with(['items.productVariant.product', 'items.productVariant.sizes'])
        //         ->whereIn('status', [Order::STATUS_PROCESSING, Order::STATUS_PREPARING])
        //         ->where('branch_id', $id)
        //         ->when($order_id, function ($query) use ($order_id) {
        //             $query->where('id', '>', $order_id);
        //         })
        //          ->orderBy('id', 'asc')
        //         ->get();
        //     }


         $orders = Order::with(['items.productVariant.product', 'items.productVariant.sizes']);
       if (auth()->user()->role_id == User::ROLE_WAITER) {
           $orders = $orders->whereIn('status', [Order::STATUS_PROCESSING, Order::STATUS_PREPARING, Order::STATUS_READY]);
            $orders = $orders->where('staff_id', auth()->user()->id);
        }else{

            $orders = $orders->whereIn('status', [Order::STATUS_PROCESSING, Order::STATUS_PREPARING]);
        }
        $orders = $orders->where('branch_id', $id)
            ->orderBy('id', 'asc') // oldest first, newest at bottom
            ->get();

        // $orders = Order::with(['items.productVariant.product', 'items.productVariant.sizes'])
        //     ->whereIn('status', [Order::STATUS_PROCESSING, Order::STATUS_PREPARING])
        //     ->where('branch_id', $id)
        //     // ->when($order_id, function ($query) use ($order_id) {
        //     //     $query->where('id', '>', $order_id);
        //     // })
        //     ->orderBy('id', 'asc')
        //     ->get();

        $html = view('admin.staff.partials.kitchen.orders', compact('orders'))->render();

        return response()->json(['html' => $html]);

        //  $orders = Order::with(['items.productVariant.product', 'items.productVariant.sizes'])
        //     ->whereIn('status', [Order::STATUS_PROCESSING, Order::STATUS_PREPARING])
        //     ->where('branch_id', $id)
        //     ->when($lastOrderId, function ($query) use ($lastOrderId) {
        //         $query->where('id', '>', $lastOrderId);
        //     })
        //     ->orderBy('id', 'asc')
        //     ->get();
    }

    public function markReady(Request $request)
    {
        //    / dd($request->all());
        $this->OrderRepository->updateStatus($request->id, ['status' => $request->status]);

        return response()->json(['status' => true]);
    }

    public function refreshDispatch(Request $request)
    {
        $id = $request->input('branch_id');
        $order_id = $request->input('order_id');
        $orders = '';
        if ($order_id != '') {
            $orders = Order::with(['items.productVariant.product', 'items.productVariant.sizes'])
                ->whereIn('status', [Order::STATUS_READY])
                ->where('branch_id', $id)
                ->when($order_id, function ($query) use ($order_id) {
                    $query->where('id', '>', $order_id);
                })
                ->orderBy('id', 'asc')
                ->get();
        }
        $html = view('admin.staff.partials.kitchen.dispach-orders', compact('orders'))->render();

        return response()->json(['html' => $html]);
    }

    public function dispachView()
    {
        $orders = Order::with(['items.productVariant.product', 'items.productVariant.sizes'])
            ->whereIn('status', [Order::STATUS_READY])
            ->orderBy('id', 'asc') // oldest first, newest at bottom
            ->get();

        return view('admin.staff.partials.kitchen.dispach-view', compact('orders'));
    }
}
