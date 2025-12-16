@extends('admin.layouts.master')

@section('title', 'Customer List')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-6">
                <h4 class="page-title">Customer List</h4>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                     <table class="hover dataTable" id="example-style-4" role="grid"
                            aria-describedby="example-style-4_info">
                        <thead class="table-light">
                            <tr>
                                <th>Full Name</th>
                                <th>Phone #</th>
                                <th>Total Orders</th>
                                <th>Total Revenue</th>
                                <th>First Ordered At</th>
                                <th>Last Ordered At</th>
                                {{-- <th>Loyalty Points</th> --}}
                                {{-- <th>Loyalty/Wallet Points</th> --}}
                                {{-- <th>Blacklist</th> --}}
                                <th>Profile</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customer as $cust)
                                <tr>
                                    <td>{{ $cust->first_name }}</td>
                                    <td>
                                        <a href="https://wa.me/{{ $cust->phone }}" target="_blank" class="text-success">
                                            <i class="ri-whatsapp-line"></i>
                                        </a>
                                        {{ $cust->phone }}
                                    </td>
                                    <td>{{ count($cust->orders) ?? 0 }}</td>
                                    <td>Rs {{ number_format($cust->total_revenue ?? 0, 2) }}</td>
                                    <td>{{ $cust->orders->isNotEmpty() ? $cust->orders->min('created_at')->format('d M, Y') : '-' }}</td>
                                    <td>{{ $cust->orders->isNotEmpty() ? $cust->orders->max('created_at')->format('d M, Y') : '-' }}</td>

                                    {{-- <td> --}}
                                        {{-- <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span>
                                                <strong>⭐ Loyalty:</strong> {{ $cust->loyaltyHistories->sum('points_balance') ?? 0 }}
                                            </span>
                                            <a href="#" class="btn btn-sm btn-outline-info show-loyalty ms-2"
                                                data-id="{{ $cust->id }}"
                                                data-loyalty="{{ $cust->loyalty_points ?? 0 }}"
                                                data-wallet="{{ number_format($cust->wallet_points ?? 0, 3) }}"
                                                title="View Loyalty Points History">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </div> --}}

                                        {{-- <div class="d-flex justify-content-between align-items-center">
                                            <span>
                                                <strong>💰 Wallet:</strong>
                                                {{ number_format($cust->wallet_points ?? 0, 3) }}
                                            </span>
                                            <a href="#" class="btn btn-sm btn-outline-info show-wallet ms-2"
                                                data-id="{{ $cust->id }}"
                                                data-loyalty="{{ $cust->loyalty_points ?? 0 }}"
                                                data-wallet="{{ number_format($cust->wallet_points ?? 0, 3) }}"
                                                title="View Wallet History">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </div> --}}
                                    {{-- </td> --}}


                                    {{-- <td>
                                    <span class="badge {{ $cust->is_blacklisted ? 'bg-danger' : 'bg-success' }}">
                                        {{ $cust->is_blacklisted ? 'Yes' : 'No' }}
                                    </span>
                                </td> --}}
                                    <td>
                                        {{-- <a data-id="{{ $cust->id }}" href="#"
                                            class="btn btn-sm btn-outline-primary show-customer">
                                            <i class="fa fa-eye"></i>
                                        </a> --}}
                                        <a data-id="{{ $cust->id }}" href="{{ route('admin.customer.show', $cust->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fa fa-eye"></i>
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
