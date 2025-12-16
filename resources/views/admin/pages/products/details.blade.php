@extends('admin.layouts.master')
@section('title', 'Products')
@section('style')
    <style>
        .my-gallery img {
            width: revert-layer !important;
        }

        .my-gallery_new {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .my-gallery_new figure,
        .my-gallery_new video {
            margin: 0;
            border-radius: 10px;
            overflow: hidden;
        }

        .my-gallery_new img,
        .my-gallery_new video {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .my-gallery_new figcaption {
            text-align: center;
            font-size: 13px;
            margin-top: 5px;
            color: #555;
        }

        /* Addons Styling */
        .addon-card {
            border: 1px solid #eee;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            transition: all 0.3s ease;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .addon-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .addon-card img {
            width: 100%;
            height: 140px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .addon-title {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .addon-price {
            font-size: 14px;
            color: #28a745;
            font-weight: 500;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">

       {{-- Product Card --}}
        <div class="card shadow-sm mb-4 border-0 rounded-3">

            <!-- Card Header with Actions -->
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                {{-- <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Back
                </a> --}}
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Back
                </a>
                <div class="btn-group" role="group" aria-label="Product actions">
                    <!-- Edit (opens modal via AJAX) -->
                    <button type="button" class="btn btn-success edit-product-top"
                        data-id="{{ $product->id }}" id="edit-product">
                        <i class="fa fa-pencil me-1"></i> Edit
                    </button>

                    <!-- Delete -->
                    <a href="javascript:void(0);"
                    class="btn btn-danger ms-2 delete-btn"
                    data-action="{{ route('admin.product.delete', $product->id) }}">
                        <i class="fa fa-trash"></i> Delete
                    </a>
                </div>
            </div>

            <!-- Card Body -->
            <div class="card-body">
                <div class="row align-items-center">

                    <!-- Left Column: Image & Video -->
                    <div class="col-md-4 text-center">
                        <div class="mb-3">
                            @if($product->main_image)
                                <img src="{{ $product->main_image }}"
                                    alt="{{ $product->name }}"
                                    class="img-fluid rounded shadow-sm"
                                    style="max-height: 250px; object-fit: cover;">
                            @endif
                        </div>

                        <div>
                            @if($product->main_video)
                                <video controls
                                    class="w-100 rounded shadow-sm"
                                    style="max-height: 250px; object-fit: cover;">
                                    <source src="{{ $product->main_video }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            @endif
                        </div>
                    </div>

                    <!-- Right Column: Details -->
                    <div class="col-md-8">
                        <h4 class="mb-2">{{ $product->name }}</h4>

                        <p class="mb-2">
                            <strong>Category:</strong> {{ $product->category->name ?? 'N/A' }}
                        </p>

                        <p class="mb-2">
                            <strong>Status:</strong>
                            <span class="badge bg-{{ $product->is_active ? 'success' : 'danger' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>

                        @if ($product->desc)
                            <p class="text-muted mt-2">
                                {!! $product->desc !!}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        {{-- Variants --}}


        <div class="card mb-4">
           <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Product Variants</h5>
                <a href="#"
                class="btn btn-primary btn-sm"
                id="add-variant"
                data-product-id="{{ $product->id }}" data-name="{{ $product->name }}" data-slug="{{ $product->slug }}">
                    <i class="fa fa-plus me-1"></i> Add Variant
                </a>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Serving Quantity</th>
                            <th>Size</th>
                            <th>Image</th>
                            <th>SKU</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($variants as $i => $variant)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $variant->unit }}</td>
                                <td>{{ $variant->sizes->name ?? '-' }}</td>
                                <td>
                                    <img src="{{ $variant->main_image }}" alt="{{ $variant->name }}" width="50"
                                        height="50" class="img-thumbnail">
                                </td>
                                <td>{{ $variant->sku }}</td>
                                <td>{{ number_format($variant->price, 2) }}</td>
                                <td>
                                    <x-status-toggle :id="$variant->id" :status="$variant->is_active ? 1 : 0" :url="route('admin.product.variants.toggleStatus')" />
                                </td>
                                <td>
                                    <a href="{{ route('admin.product.variants.ingredients', $variant->id) }}"
                                        class="btn btn-info btn-sm"><i class="fa fa-spoon"></i></a>
                                    <a href="javascript:void(0);" class="btn btn-success btn-sm" id="edit-variant"
                                        data-id="{{ $variant->id }}" data-product-id="{{ $product->id }}" data-name="{{ $product->name }}" data-slug="{{ $product->slug }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a class="btn btn-danger btn-sm theme delete-btn" href="javascript:void(0);"
                                        data-action='{{ route('admin.product.variants.delete', $variant->id) }}'>
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Addons Section --}}

            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Product Addons</h5>
                        {{-- <small class="text-muted">Manage ingredients and product addons for this product</small> --}}
                    </div>
                    <div>
                        <a href="#" class="btn btn-primary btn-sm add-addon-btn"
                            data-product-id="{{ $product->id }}" id="add-addon">
                            <i class="fa fa-plus"></i> Add Ingredient Addon
                        </a>
                        <a href="#" class="btn btn-primary btn-sm add-addon-btn hide"
                            data-product-id="{{ $product->id }}" id="add-addon-product">
                            <i class="fa fa-plus"></i> Add Product Addon
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs" id="addonTabs" role="tablist">
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
                    <div class="tab-content mt-3" id="addonTabsContent">

                        <!-- Ingredients Tab -->
                        <div class="tab-pane fade show active" id="ingredients" role="tabpanel"
                            aria-labelledby="ingredients-tab">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Ingredient Category</th>
                                            <th>Unit</th>
                                            {{-- <th>Price</th> --}}
                                            <th>Qty</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($addons as $i => $item)
                                            @if ($item->addonable_type == 'App\Models\IngredientCategory')
                                            @if(isset($item->addonable))
                                                <tr>
                                                    <td>{{ $i + 1 }}</td>
                                                    <td>{{ $item->addonable->name ?? 'N/A' }}</td>
                                                    <td>{{  $item->addonable->ingredients->first()->unit->name ?? 'N/A' }}</td>
                                                    {{-- <td>{{ number_format($item->price, 2) }}</td> --}}
                                                    <td>{{ $item->qty }}</td>
                                                    <td>{{ $item->created_at->format('d M Y') }}</td>
                                                    <td>
                                                        <a href="#" class="btn btn-success btn-sm edit-addon"
                                                            data-id="{{ $item->id }}"
                                                            data-product_id="{{ $item->product_id }}"
                                                            data-price="{{ $item->price }}"
                                                            data-qty="{{ $item->qty }}">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                        <a class="btn btn-danger btn-sm theme delete-btn"
                                                            href="javascript:void(0);"
                                                            data-action="{{ route('admin.addons.delete', $item->id) }}">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                @endif
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Products Tab -->
                        <div class="tab-pane fade" id="products" role="tabpanel" aria-labelledby="products-tab">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
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
                                                <tr>
                                                    <td>{{ $i + 1 }}</td>
                                                    <td>{{ $item->addonable->sizes->name ?? 'N/A' }}</td>
                                                    <td>{{ number_format($item->price, 2) }}</td>
                                                    <td>{{ $item->qty }}</td>
                                                    <td>{{ $item->created_at->format('d M Y') }}</td>
                                                    <td>
                                                        <a href="#"
                                                            class="btn btn-success btn-sm edit-addon-product"
                                                            data-id="{{ $item->id }}"
                                                            data-product_id="{{ $item->product_id }}"
                                                            data-price="{{ $item->price }}"
                                                            data-qty="{{ $item->qty }}">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                        <a class="btn btn-danger btn-sm theme delete-btn"
                                                            href="javascript:void(0);"
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

    <script>
        // ensure bootstrap exists and init dropdowns
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof bootstrap !== 'undefined') {
                document.querySelectorAll('.dropdown-toggle').forEach(function(el) {
                    // create instance if not exists
                    bootstrap.Dropdown.getOrCreateInstance(el);
                });
            }

            // Stop parent handlers from intercepting the toggle click
            // NOTE: only stopping propagation for the toggle itself so menu still closes when clicking items.
            $(document).on('click', '.dropdown-toggle', function(e) {
                e.stopPropagation();
            });
            getEventListeners(document.querySelector('.dropdown-toggle'))
        });
    </script>
@endsection
