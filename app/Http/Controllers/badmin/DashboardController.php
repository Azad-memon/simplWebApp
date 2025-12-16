<?php

namespace App\Http\Controllers\badmin;

use App\Http\Controllers\Controller;
use Auth;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\OrderRepository;
use App\Models\BranchSetting;
use Illuminate\Http\Request;



class DashboardController extends Controller
{
    protected $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function index()
    {
        $user = Auth::user();
        //$orders = $this->orderRepository->branchorders(Auth::user()->branches[0]->id ?? null);
        $orders = $this->orderRepository->branchorders(Auth::user()->branches[0]->id ?? null);

    // filter completed orders only
    $completedOrders = $orders->where('status', 'completed');

    // === Today Sales ===
    $todaySales = $completedOrders
        ->where('created_at', '>=', today()->startOfDay())
        ->where('created_at', '<=', today()->endOfDay())
        ->sum('final_amount');

    // === Monthly Sales ===
    $monthSales = $completedOrders
        ->filter(function ($order) {
            return $order->created_at->month == now()->month &&
                   $order->created_at->year  == now()->year;
        })
        ->sum('final_amount');
    // === Total Sales ===
    $totalSales = $completedOrders->sum('final_amount');

    return view('admin.badmin.dashboard', compact(
        'todaySales',
        'monthSales',
        'totalSales'
    ));
       // dump($user->branches[0]->id);
        return view('admin.badmin.dashboard');
    }
    public function settings()
    {
        $settings = BranchSetting::where('branch_id', Auth::user()->branches[0]->id ?? null)->first();
        return view('admin.badmin.settings', compact('settings'));
    }
    public function updateSettings(Request $request)
    {

        $request->validate([
            'printer_ip' => 'required|ip',
            'printer_port' => 'required|integer|min:1|max:65535',
        ]);


        $branchId = Auth::user()->branches[0]->id ?? null;

        BranchSetting::updateOrCreate(
            ['branch_id' => $branchId], // Find by branch_id
            [
                'printer_ip'    => $request->printer_ip,
                'printer_port'  => $request->printer_port,
               // 'setting_value' => $request->settings, // JSON/string both OK
            ]
        );

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
}
