@extends('admin.layouts.master')
@section('title', 'Orders')

@section('style')
    <style>
        /* ====== Dashboard Stats ====== */
        .stats-card {
            border-radius: 14px;
            padding: 20px;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: 0.3s;
        }

        .stats-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        .stats-card .icon {
            font-size: 32px;
            opacity: 0.8;
        }

        .bg-gradient-primary {
            background: linear-gradient(45deg, #3f51b5, #6573c3);
        }

        .bg-gradient-success {
            background: linear-gradient(45deg, #28a745, #5dd067);
        }

        .bg-gradient-warning {
            background: linear-gradient(45deg, #ffc107, #ffda6a);
            color: #5d4300;
        }

        .bg-gradient-danger {
            background: linear-gradient(45deg, #dc3545, #ff6b81);
        }

        /* ====== Orders Table ====== */
        .orders-table {
            border-radius: 12px;
            overflow: hidden;
        }

        .orders-table thead {
            background: linear-gradient(45deg, #3f51b5, #6573c3);
            color: #fff;
        }

        .orders-table tbody tr {
            transition: all 0.2s ease-in-out;
        }

        .orders-table tbody tr:hover {
            background: #f8f9ff;
            transform: translateY(-2px);
        }

        /* Status Badges */
        .order-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">

        {{-- Top Stats --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card bg-gradient-primary shadow-sm">
                    <div>
                        <h5>Total Orders</h5>
                        <h3 class="fw-bold">{{ $orders->count() }}</h3>
                    </div>
                    <i class="fa fa-shopping-cart icon"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card bg-gradient-success shadow-sm">
                    <div>
                        <h5>Completed</h5>
                        <h3 class="fw-bold">{{ $orders->where('status', 'completed')->count() }}</h3>
                    </div>
                    <i class="fa fa-check-circle icon"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card bg-gradient-warning shadow-sm">
                    <div>
                        <h5>Pending</h5>
                        <h3 class="fw-bold">{{ $orders->where('status', 'pending')->count() }}</h3>
                    </div>
                    <i class="fa fa-hourglass-half icon"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card bg-gradient-danger shadow-sm">
                    <div>
                        <h5>Revenue</h5>
                        <h3 class="fw-bold">Rs{{ number_format($orders->sum('total_amount'), 2) }}</h3>
                    </div>
                    <i class="fa fa-dollar-sign icon"></i>
                </div>
            </div>
        </div>

        {{-- Orders Table --}}
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0"><i class="fa fa-shopping-basket me-2 text-primary"></i> Orders List</h4>
                {{-- <a href="#" class="btn btn-primary btn-sm"><i class="fa fa-plus me-1"></i> Add Order</a> --}}
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="hover dataTable" id="example-style-4" role="grid"
                        aria-describedby="example-style-4_info">
                        <thead>
                            <tr><th hidden>IDS</th>
                                <th>#ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th> {{-- New Column --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td hidden>{{ $order->id }}</td>
                                    <td><strong>#{{ $order->order_uid }}</strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($order->customer_name ?? 'N A') }}&background=random"
                                                class="rounded-circle me-2" width="32" height="32">
                                            <span>{{ $order->customer_name ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td><span
                                            class="fw-bold text-success">Rs{{ number_format($order->final_amount, 2) }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = match ($order->status) {
                                                'pending' => 'status-pending',
                                                'completed' => 'status-completed',
                                                'cancelled' => 'status-cancelled',
                                                default => '',
                                            };
                                            $statusIcon = match ($order->status) {
                                                'pending' => 'fa-hourglass-half',
                                                'completed' => 'fa-check',
                                                'cancelled' => 'fa-times',
                                                default => 'fa-circle',
                                            };
                                        @endphp
                                        <span class="order-status {{ $statusClass }}">
                                            <i class="fa {{ $statusIcon }}"></i> {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('d M, Y') }}</td>
                                    <td>

                                        @if(Auth::user()->role->name=='branchadmin')
                                          <a href="{{ route('badmin.order.show', $order->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                        @else
                                          <a href="{{ route('admin.order.show', $order->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                        @endif

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#example-style-4').DataTable({
                destroy: true,
                "order": [
                    [0, "desc"]
                ]
            });
        });
    </script>
@endpush
