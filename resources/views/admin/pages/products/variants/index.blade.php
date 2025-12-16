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
    </style>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">Product Details</h3>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>
        <div class="card shadow-sm mb-4 border-0 rounded-3">
            <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center flex-wrap gap-3">
                    <div class="gallery my-gallery_new my-gallery" itemscope="">
                        @if (!empty($product->images) && $product->images->count() > 0)
                            @foreach ($product->images as $media)
                                @if ($media->image_type == 'product_video')
                                    {{-- Show video --}}
                                    <video width="200" height="150" controls class="img-thumbnail">
                                        <source src="{{ $product->main_video }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                @else
                                    {{-- Show image --}}
                                    <figure itemprop="associatedMedia" itemscope="">
                                        <a href="{{ $product->main_image }}" itemprop="contentUrl" data-size="1600x950">
                                            <img class="img-thumbnail" src="{{ $product->main_image }}" itemprop="thumbnail"
                                                alt="{{ $product->name }}" width="100" height="100">
                                        </a>
                                        <figcaption itemprop="caption description">{{ ucfirst($product->name) }}
                                        </figcaption>
                                    </figure>
                                @endif
                            @endforeach
                        @endif

                    </div>

                    <div>
                        <h4 class="mb-1">{{ $product->name }}</h4>
                        <p class="mb-1">
                            <strong>Category:</strong> {{ $product->category->name ?? 'N/A' }}
                        </p>
                        <p class="mb-0">
                            <strong>Status:</strong>
                            <span class="badge bg-{{ $product->is_active ? 'success' : 'danger' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                        @if ($product->desc)
                            <p class="text-muted mt-2 mb-0" style="max-width: 400px;">
                                {!!  \Illuminate\Support\Str::limit($product->desc, 120) !!}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <a href="#" class="btn btn-primary" id="add-variant" data-product-id="{{ $product->id }}" data-slug="{{ $product->slug }}"
                    style="float: right">Add Variant</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-header">
                <h5>Product Variants</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="hover dataTable" id="example-style-4" role="grid" aria-describedby="example-style-4_info">
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


                                    <div class="gallery my-gallery" itemscope="">

                                        <figure class="col-xl-3 col-md-4 col-6 custom-image-container"
                                            itemprop="associatedMedia" itemscope>
                                            <a class="image-popup-no-margins" href="{{ $variant->main_image }}"
                                                itemprop="contentUrl" data-size="800x800">
                                                <img class="img-thumbnail custom-img-responsive"
                                                    alt="{{ ucfirst($variant->name) }}" src="{{ $variant->main_image }}"
                                                    width="50" height="50" itemprop="thumbnail">
                                            </a>
                                            <figcaption itemprop="caption description">
                                                {{ ucfirst($variant->name) }}</figcaption>
                                        </figure>

                                    </div>

                                </td>
                                <td>{{ $variant->sku }}</td>
                                <td>{{ number_format($variant->price, 2) }}</td>
                                <td>
                                    <x-status-toggle :id="$variant->id" :status="$variant->is_active ? 1 : 0" :url="route('admin.product.variants.toggleStatus')" />
                                <td>
                                    <a href="{{ route('admin.product.variants.ingredients', $variant->id) }}"
                                        class="btn btn-info" title="View Ingredients">
                                        <i class="fa fa-spoon"></i>
                                    </a>

                                    <a href="javascript:void(0);" class="btn btn-success" title="Edit Variant"
                                        id="edit-variant" data-id="{{ $variant->id }}"
                                        data-product-id="{{ $product->id }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a class="btn btn-danger theme delete-btn" href="javascript:void(0);" data-id=""
                                        data-action='{{ route('admin.product.variants.delete', $variant->id) }}'>
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
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
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
