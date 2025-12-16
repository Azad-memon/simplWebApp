@extends('admin.layouts.master')
@section('title', 'Posts')

@section('css')


@endsection

@section('style')
    <style>
        body.dark-only .page-wrapper .page-body-wrapper .page-body .card:not(.email-body) .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        body.dark-only .page-wrapper .page-body-wrapper .page-body .card:not(.email-body) .dataTables_wrapper .dataTables_paginate .paginate_button:active {
            border-color: var(--theme-deafult);
            /* color: black; */
            background: #3a3e4a;
        }
    </style>

@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col d-flex justify-content-end gap-2">
                <a href="#" class="btn btn-primary add-addon-btn hide" data-product-id="{{ $product->id }}"
                    id="add-addon-product">Add Addon Product</a>
                <a href="#" class="btn btn-primary add-addon-btn" data-product-id="{{ $product->id }}"
                    id="add-addon">Add Addon Ingredient</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>{{ $product->name ?? 'Product Name' }}</h4>
                <p class="text-muted">Here are the available addons for this product.</p>
            </div>
            <div class="card-body">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="ingredients-tab" data-bs-toggle="tab" href="#ingredients"
                            role="tab" aria-controls="ingredients" aria-selected="true">
                            <i class="fas fa-mug-hot"></i> Ingredients
                        </a>
                    </li>
                    <li class="nav-item hide" role="presentation">
                        <a class="nav-link" id="products-tab" data-bs-toggle="tab" href="#products" role="tab"
                            aria-controls="products" aria-selected="false">
                            <i class="fas fa-cube"></i> Products
                        </a>
                    </li>
                </ul>


                <!-- Tab Content -->
                <div class="tab-content" id="myTabContent">

                    <!-- Ingredients Tab -->
                    <div class="tab-pane fade show active" id="ingredients" role="tabpanel"
                        aria-labelledby="ingredients-tab">
                        <div class="table-responsive">
                            <div id="example-style-4_wrapper" class="dataTables_wrapper">
                                <table class="hover dataTable" id="example-style-4" role="grid"
                                    aria-describedby="example-style-4_info">
                                    <thead>
                                        <tr role="row">
                                            <th>#</th>
                                            <th>Ingredient Category</th>
                                            <th>Price</th>
                                            <th>Qty</th>

                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($addons as $i => $item)
                                            @if ($item->addonable_type == 'App\Models\IngredientCategory')
                                                <tr role="row" class="odd">
                                                    <td>{{ $i + 1 }}</td>
                                                    <td>{{ $item->addonable->name ?? 'N/A' }}</td>
                                                    <td>{{ number_format($item->price, 2) }}</td>
                                                    <td>{{ $item->qty }}</td>

                                                    <td>{{ $item->created_at }}</td>
                                                    <td>
                                                        <a href="#" class="btn btn-success edit-addon"
                                                            data-id="{{ $item->id }}"
                                                            data-product_id="{{ $item->product_id }}"
                                                            data-price="{{ $item->price }}"
                                                            data-qty="{{ $item->qty }}">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                        <a class="btn btn-danger theme delete-btn"
                                                            href="javascript:void(0);" data-id=""
                                                            data-action="{{ route('admin.addons.delete', $item->id) }}">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Products Tab -->
                    <div class="tab-pane fade" style="display: none;" id="products" role="tabpanel"
                        aria-labelledby="products-tab">
                        <div class="table-responsive">
                            <div id="example-style-5_wrapper" class="dataTables_wrapper">
                                <table class="hover dataTable" id="example-style-5" role="grid"
                                    aria-describedby="example-style-5_info">
                                    <thead>
                                        <tr role="row">
                                            <th>#</th>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Qty</th>

                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($addons as $i => $item)
                                            @if ($item->addonable_type == 'App\Models\ProductVariant')
                                                <tr role="row" class="odd">
                                                    <td>{{ $i + 1 }}</td>
                                                    <td>{{ $item->addonable->sizes->name ?? 'N/A' }}</td>
                                                    <td>{{ number_format($item->price, 2) }}</td>
                                                    <td>{{ $item->qty }}</td>

                                                    <td>{{ $item->created_at }}</td>
                                                    <td>
                                                        <a href="#" class="btn btn-success edit-addon-product"
                                                            data-id="{{ $item->id }}"
                                                            data-product_id="{{ $item->product_id }}"
                                                            data-price="{{ $item->price }}"
                                                            data-qty="{{ $item->qty }}">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                        <a class="btn btn-danger theme delete-btn"
                                                            href="javascript:void(0);" data-id=""
                                                            data-action="{{ route('admin.addons.delete', $item->id) }}">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    <!-- Modal Component -->
@endsection


@section('script')
@endsection
