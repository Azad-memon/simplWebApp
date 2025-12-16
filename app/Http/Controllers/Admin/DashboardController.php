<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Order;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // dump($user->branches[0]->id);
        // $totalPurchases = Order::count();
        // $totalSales = Order::sum('final_amount');
        $branches=Branch::all();
        return view('admin.pages.dashboard_updated', compact('branches'));
    }

    public function index2(Request $request)
    {
        $branches=Branch::all();
        return view('admin.pages.dashboard_updated',compact('branches'));
    }

    public function loadProductReport(Request $request)
    {
        // dump($request->all());
        $user = Auth::user();

        $ordersQuery = Order::query();
        $ordersQuery->where('status', 'completed');

        if (isset($request->branch_id) && ! empty($request->branch_id)) {
            $ordersQuery->where('branch_id', $request->branch_id);
        }

        // Apply daily or monthly filter
        if ($request->has('daily_date') && $request->daily_date) {
            $ordersQuery->whereDate('created_at', $request->daily_date);
        }

        if ($request->has('month_date') && $request->month_date) {
            $month = Carbon::parse($request->month_date)->month;
            $year = Carbon::parse($request->month_date)->year;
            $ordersQuery->whereYear('created_at', $year)->whereMonth('created_at', $month);
        }

        // Eager load items and variants
        $orders = $ordersQuery->with('items.productVariant.product')->get();

        // Aggregate product-wise data
        $productData = [];
        if (! empty($orders)) {
            foreach ($orders as $order) {
                foreach ($order->items as $item) {
                    $productName = $item->productVariant->product->name ?? 'Unknown Product';

                    if (! isset($productData[$productName])) {
                        $productData[$productName] = ['qty' => 0, 'revenue' => 0];
                    }

                    $productData[$productName]['qty'] += $item->quantity;
                    $productData[$productName]['revenue'] += $item->price; // or total_price
                }
            }
        }

        // Prepare response data
        $labels = array_keys($productData);
        $qty = array_map(fn ($p) => $p['qty'], $productData) ?? [];
        $revenue = array_map(fn ($p) => $p['revenue'], $productData) ?? [];
        $total_qty = array_sum($qty);
        $total_revenue = array_sum($revenue);
        $top_product = ! empty($qty) ? array_search(max($qty), $qty) : 'N/A';

        return response()->json([
            'labels' => $labels,
            'qty' => $qty,
            'revenue' => $revenue,
            'total_qty' => $total_qty,
            'total_revenue' => $total_revenue,
            'top_product' => $top_product,
        ]);
    }

    public function loadSalesReport(Request $request)
    {
        $ordersQuery = Order::query()
            ->where('status', 'completed');

        // Filter by Branch
        if (! empty($request->branch_id)) {
            $ordersQuery->where('branch_id', $request->branch_id);
        }

        // Daily Filter
        if ($request->daily_date) {
            $ordersQuery->whereDate('created_at', $request->daily_date);
        }

        // Monthly Filter
        if ($request->month_date) {
            $month = Carbon::parse($request->month_date)->month;
            $year = Carbon::parse($request->month_date)->year;
            $ordersQuery->whereYear('created_at', $year)
                ->whereMonth('created_at', $month);
        }

        // Load orders with relations
        $orders = $ordersQuery
            ->with(['items.productVariant.product', 'payment', 'refundTransactions'])
            ->get();

        // ------------------------------
        // Summary Calculations
        // ------------------------------

        $total_orders = $orders->count();
        $total_revenue = $orders->sum('final_amount');
        $gross_sale = $total_revenue;
        $total_tax = $orders->sum('tax');
        $total_discount = $orders->sum('discount');
        $total_refund = $orders->sum(fn ($o) => $o->refundTransactions->sum('amount'));



        // ------------------------------
        // Payment Breakdown (Relation)
        // ------------------------------

        $cash_sale = $orders->filter(fn ($o) => $o->payment?->payment_method === 'cash')
            ->sum('final_amount');

        $card_sale = $orders->filter(fn ($o) => $o->payment?->payment_method === 'card')
            ->sum('final_amount');
        $online_sale = $orders->filter(fn ($o) => $o->payment?->payment_method === 'online')
            ->sum('final_amount');

        $credit_sale = $orders->filter(fn ($o) => $o->payment?->payment_method === 'credit')
            ->sum('final_amount');

        // ------------------------------
        // Product-Wise Aggregation
        // ------------------------------

        $orderData = [];

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $name = $item->productVariant->product->name ?? 'Unknown';

                if (! isset($orderData[$name])) {
                    $orderData[$name] = ['qty' => 0, 'revenue' => 0];
                }

                $orderData[$name]['qty'] += $item->quantity;
                $orderData[$name]['revenue'] += ($item->price * $item->quantity);
            }
        }

        $labels = array_keys($orderData);
        $qty = array_map(fn ($x) => $x['qty'], $orderData);
        $revenue = array_map(fn ($x) => $x['revenue'], $orderData);

        // ------------------------------
        // Top Product
        // ------------------------------

        // $top_product = (! empty($qty))
        //     ? $labels[array_search(max($qty), $qty)]
        //     : 'N/A';

        // ------------------------------
        // Response
        // ------------------------------

        return response()->json([
            'total_orders' => $total_orders,
            'total_revenue' => $total_revenue,
            'gross_sale' => $gross_sale,
            'total_tax' => $total_tax,

            'cash_sale' => $cash_sale,
            'card_sale' => $card_sale,
            'online_sale' => $online_sale,
            'credit_sale' => $credit_sale,

            'labels' => $labels,
            'qty' => $qty,
            'revenue' => $revenue,
            'total_discount' => $total_discount,
            'total_refund' => $total_refund,
           // 'top_product' => $top_product,
        ]);
    }
    public function loadDashboardSummary(Request $request)
{
    $branchId = $request->branch_id;

    // Base query for completed orders
    $ordersQuery = Order::query()->where('status', 'completed');

    if (!empty($branchId)) {
        $ordersQuery->where('branch_id', $branchId);
    }

    // Count new orders today
    $newOrders = (clone $ordersQuery)
        ->whereDate('created_at', Carbon::today())
        ->count();

    // Count new customers today (assuming 'customer' type in users table)
    $newCustomers = Customer::query()
       // ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
        ->whereDate('created_at', Carbon::today())
       // ->where('type', 'customer') // adjust according to your schema
        ->count();

    // Average sale (for completed orders in the month)
    $monthStart = Carbon::now()->startOfMonth();
    $monthEnd   = Carbon::now()->endOfMonth();

    $averageSale = (clone $ordersQuery)
        ->whereBetween('created_at', [$monthStart, $monthEnd])
        ->avg('final_amount') ?? 0;

    // Gross Profit (assuming cost_price available)
    // $grossProfit = (clone $ordersQuery)
    //     ->whereBetween('created_at', [$monthStart, $monthEnd])
    //     ->sum(function($order) {
    //         return ($order->final_amount - $order->cost_amount); // adjust cost_amount field
    //     }) ?? 0;
    $totalEarning = $ordersQuery->sum('final_amount');
    $grossProfit= 0;

    return response()->json([
        'new_orders' => $newOrders,
        'new_customers' => $newCustomers,
        'average_sale' => round($averageSale, 2),
        'gross_profit' => round($grossProfit, 2),
        'total_earnings' => round($totalEarning, 2),
    ]);
}
}
