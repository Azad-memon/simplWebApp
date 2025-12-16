@extends('admin.layouts.master')
@section('title', 'Orders')

@section('style')
    <style>
        /* ====== Dashboard Stats ====== */

    </style>
@endsection

@section('content')
    <div class="container-fluid">
       {{-- Orders Table --}}
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0"><i class="fa fa-shopping-basket me-2 text-primary"></i> KDS Orders List</h4>
                {{-- <a href="#" class="btn btn-primary btn-sm"><i class="fa fa-plus me-1"></i> Add Order</a> --}}
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="hover dataTable" id="example-style-4" role="grid"
                        aria-describedby="example-style-4_info">
                        <thead>
                            <tr>
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
                                    <td><strong>#{{ $order->order_uid }}</strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($order->customer->first_name ?? 'N A') }}&background=random"
                                                class="rounded-circle me-2" width="32" height="32">
                                            <span>{{ $order->customer->first_name ?? 'N/A' }}</span>
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
                                          <a href="{{ route('admin.kdsorder.details', $order->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fa fa-eye"></i> View
                                        </a>
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
