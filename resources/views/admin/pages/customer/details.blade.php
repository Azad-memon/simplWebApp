@extends('admin.layouts.master')

@section('title', 'Customer Details')

@section('content')
<div class="container-fluid">
    <!-- Back Button & Title -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="page-title"><i class="fa fa-user-circle me-2 text-primary"></i> Customer Details</h4>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <!-- Customer Profile Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-4 border-bottom pb-3">
                <!-- Avatar -->
                <img src="https://ui-avatars.com/api/?name={{ urlencode($customer->full_name ?? 'N/A') }}&background=random&color=fff&size=100"
                    class="rounded-circle shadow-sm me-3" width="90" height="90" alt="Customer Avatar">

                <!-- Customer Info -->
                <div>
                    <h4 class="fw-bold text-primary mb-1">{{ $customer->full_name ?? 'N/A' }}</h4>
                    <p class="mb-1 text-muted">
                        <i class="fa fa-phone text-success me-1"></i>
                        {{ $customer->phone ?? 'N/A' }}
                    </p>
                    <p class="mb-0 text-muted">
                        <i class="fa fa-map-marker-alt text-danger me-1"></i>
                        @if ($customer->addresses && $customer->addresses->address)
                            {{ $customer->addresses->address->street_address ?? '' }},
                            {{ $customer->addresses->address->city ?? '' }},
                            {{ $customer->addresses->address->state ?? '' }},
                            {{ $customer->addresses->address->zipcode ?? '' }}
                        @else
                            N/A
                        @endif
                    </p>
                </div>
            </div>

            <!-- Stats Section -->
            <div class="row text-center">
                <!-- Loyalty Points -->
                <div class="col-md-3 mb-3">
                    <div class="p-3 border rounded-3 bg-light h-100 shadow-sm">
                        <h6 class="text-muted mb-1"><i class="fa fa-star text-warning me-1"></i>Loyalty Points</h6>
                        <h4 class="fw-bold text-success mb-0">{{ $customer->loyaltyHistories->sum('points_balance') ?? 0 }}</h4>
                    </div>
                </div>

                <!-- Total Orders -->
                <div class="col-md-3 mb-3">
                    <div class="p-3 border rounded-3 bg-light h-100 shadow-sm">
                        <h6 class="text-muted mb-1"><i class="fa fa-shopping-cart text-primary me-1"></i>Total Orders</h6>
                        <h4 class="fw-bold text-primary mb-0">{{ $customer->orders->count() ?? 0 }}</h4>
                    </div>
                </div>

                <!-- Total Spent -->
                <div class="col-md-3 mb-3">
                    <div class="p-3 border rounded-3 bg-light h-100 shadow-sm">
                        <h6 class="text-muted mb-1"><i class="fa fa-wallet text-info me-1"></i>Total Spent</h6>
                        <h4 class="fw-bold text-info mb-0">
                            Rs {{ number_format($customer->orders->sum('total_amount'), 2) }}
                        </h4>
                    </div>
                </div>

                <!-- Member Since -->
                <div class="col-md-3 mb-3">
                    <div class="p-3 border rounded-3 bg-light h-100 shadow-sm">
                        <h6 class="text-muted mb-1"><i class="fa fa-calendar text-secondary me-1"></i>Member Since</h6>
                        <h4 class="fw-bold text-secondary mb-0">
                            {{ $customer->created_at ? $customer->created_at->format('d M, Y') : 'N/A' }}
                        </h4>
                    </div>
                </div>
            </div>

            <!-- Additional Order Stats -->
            <div class="row text-center mt-3">
                <!-- First Order -->
                <div class="col-md-6 mb-3">
                    <div class="p-3 border rounded-3 bg-light h-100 shadow-sm">
                        <h6 class="text-muted mb-1">
                            <i class="fa fa-clock text-success me-1"></i>First Order Date
                        </h6>
                        <h5 class="fw-bold text-dark mb-0">
                            {{ optional($customer->orders->sortBy('created_at')->first())->created_at?->format('d M, Y') ?? 'N/A' }}
                        </h5>
                    </div>
                </div>

                <!-- Last Order -->
                <div class="col-md-6 mb-3">
                    <div class="p-3 border rounded-3 bg-light h-100 shadow-sm">
                        <h6 class="text-muted mb-1">
                            <i class="fa fa-history text-primary me-1"></i>Last Order Date
                        </h6>
                        <h5 class="fw-bold text-dark mb-0">
                            {{ optional($customer->orders->sortByDesc('created_at')->first())->created_at?->format('d M, Y') ?? 'N/A' }}
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>



    {{-- ✅ Orders Table --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="fw-bold mb-0"><i class="fa fa-shopping-basket me-2 text-primary"></i> Orders List</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="hover dataTable" id="example-style-4" role="grid" aria-describedby="example-style-4_info">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customer->orders as $order)
                            <tr>
                                <td><strong>#{{ $order->order_uid }}</strong></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($customer->first_name ?? 'N A') }}&background=random"
                                            class="rounded-circle me-2" width="32" height="32">
                                        <span>{{ $customer->first_name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td><span class="fw-bold text-success">Rs{{ number_format($order->total_amount, 2) }}</span></td>
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
                                    @if(Auth::user()->role->name == 'branchadmin')
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
            "order": [[0, "desc"]],
            "pageLength": 10
        });
    });
</script>
@endpush
