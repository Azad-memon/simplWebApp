@extends('admin.layouts.master')

@section('title', 'Payment Methods')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-3">
            <div class="col">
                <h4 class="mb-0">Payment Methods</h4>
            </div>
            <div class="col text-end">
                <a href="#" class="btn btn-primary add-payment">
                    <i class="fa fa-plus me-2"></i> Add Payment Method
                </a>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Table -->
        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0">Payment Method List</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="hover dataTable" id="example-style-4" role="grid" aria-describedby="example-style-4_info">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Status</th>
                            <th >Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($paymentMethods as $i => $method)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $method->name }}</td>
                                <td>{{ $method->code }}</td>
                                <td>
                                     <x-status-toggle :id="$method->id" :status="$method->is_enabled ? 1 : 0" :url="route('admin.paymentmethod.toggleStatus')" />
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-success btn-sm edit-payment"
                                        data-id="{{ $method->id }}">
                                        <i class="fa fa-edit"></i>
                                    </a>

                                    <a class="btn btn-danger theme delete-btn" href="javascript:void(0);" data-id=""
                                        data-action="{{ route('admin.paymentmethod.delete', $method->id) }}">
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
@endsection
