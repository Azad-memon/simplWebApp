@extends('admin.layouts.master')
@section('title', 'Coupons')

@section('css')
@endsection

@section('style')
    <style>
        body.dark-only .page-wrapper .page-body-wrapper .page-body .card:not(.email-body) .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        body.dark-only .page-wrapper .page-body-wrapper .page-body .card:not(.email-body) .dataTables_wrapper .dataTables_paginate .paginate_button:active {
            border-color: var(--theme-deafult);
            background: #3a3e4a;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col d-flex justify-content-end gap-2">
                <a href="#" class="btn btn-primary" id="add-coupon" style="float: right">
                    Add New Coupon
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>Coupons</h4>
                <p class="text-muted">Here are all the available coupons.</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <div id="coupon-table_wrapper" class="dataTables_wrapper">

                        <table class="hover dataTable" id="example-style-4" role="grid"
                            aria-describedby="example-style-4_info">
                            <thead>
                                <tr>
                                    <th >ID</th>
                                    <th>Code</th>
                                    <th>Discount</th>
                                    <th>Type</th>
                                    <th>Start Date</th>
                                    <th>Expire At</th>
                                    <th>Max Usage</th>
                                    {{-- <th>Product</th> --}}
                                    <th>Status</th>
                                    {{-- <th>Min Price</th>
                                    <th>Max  Price</th> --}}
                                    {{-- <th>Created At</th> --}}
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($coupons as $coupon)
                                    <tr>
                                        <td>{{ $coupon->id }}</td>
                                        <td>{{ $coupon->code }}</td>
                                        <td>{{ $coupon->discount }}</td>
                                        <td>{{ ucfirst($coupon->type) }}</td>
                                        <td>
                                            {{ $coupon->start_date ? \Carbon\Carbon::parse($coupon->start_date)->format('d M Y h:i A') : '-' }}
                                        </td>
                                        </td>
                                        <td>{{ $coupon->expire_at ? \Carbon\Carbon::parse($coupon->expire_at)->format('d M Y h:i A') : '-' }}
                                        </td>
                                        <td>{{ $coupon->max_usage }}</td>
                                        {{-- <td>{{ $coupon->product?->name ?? '-' }}</td> --}}
                                        {{-- <td>{{ $coupon->created_at->format('d M Y') }}</td> --}}
                                        <td> <x-status-toggle
                                        :id="$coupon->id"
                                        :status="$coupon->status"
                                        :url="route('admin.coupons.toggleStatus')" /></td>
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-success edit-coupon"
                                                data-id="{{ $coupon->id }}" >
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a class="btn btn-danger theme delete-btn" href="javascript:void(0);"
                                                data-id=""
                                                data-action='{{ route('admin.coupons.destroy', $coupon->id) }}'>
                                                <i class="fa fa-trash"></i>
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
    </div>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {
    $('#example-style-4').DataTable({
        destroy:true,
        "order": [[0, "desc"]]
    });
});
</script>
@endpush
