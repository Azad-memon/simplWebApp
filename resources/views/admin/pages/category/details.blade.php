@extends('admin.layouts.master')

@section('title', 'Category Details')

<style>
    .card-header {
        background-color: #f6f6f6;
        border-bottom: 1px solid #eee;
    }
    .card h5 {
        font-size: 1.2rem;
        font-weight: 600;
    }
    .form-control {
        border-radius: 6px;
    }
    .btn-primary {
        background-color: #3c8dbc;
        border-color: #367fa9;
    }
    .category-image {
        max-width: 200px;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    .table th {
        background-color: #f9f9f9;
    }
</style>

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="page-title-box mb-3">
        <h4 class="page-title">Category: {{ $category->name }}</h4>
    </div>

    <!-- Back Button -->
    <div class="mb-3">
        <a href="{{ route('admin.products.index') }}" class="btn btn-dark">← Back to categories</a>
    </div>

    <!-- Category Details -->

    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-gradient text-white d-flex justify-content-between align-items-center"
            style="background: linear-gradient(45deg, #3c8dbc, #367fa9);">
            <h5 class="mb-0"><i class="bi bi-tags"></i> Category Details</h5>
            <span class="badge bg-light text-dark">{{ ucfirst($category->status) }}</span>
        </div>

        <div class="card-body">
        <div class="row g-4 align-items-start">
                <!-- Image Section -->
                @if(!empty($category->main_image))
                <div class="col-md-6 text-center">
                    <div class="p-2 border rounded shadow-sm bg-light">
                        <div class="media-box rounded overflow-hidden" style="width:100%; height:280px;">
                            <img src="{{ $category->main_image ?? asset('images/no-image.png') }}"
                                alt="Category Image"
                                class="w-100 h-100 rounded"
                                style="object-fit: cover;">
                        </div>
                        <p class="mt-2 text-muted small">Main Image</p>
                    </div>
                </div>
                @endif

                <!-- Video Section -->
                @if(!empty($category->main_video))
                <div class="col-md-6 text-center">
                    <div class="p-2 border rounded shadow-sm bg-light">
                        <div class="media-box rounded overflow-hidden" style="width:100%; height:280px;">
                            <video controls preload="metadata"
                                poster="{{ $category->main_image ?? asset('images/video-thumb.png') }}"
                                class="w-100 h-100 rounded"
                                style="object-fit: cover;">
                                <source src="{{ $category->main_video }}#t=0.5" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                        <p class="mt-2 text-muted small">Category Video</p>
                    </div>
                </div>
                @endif

                <!-- Details Section Full Width -->
                <div class="col-12 mt-4">
                    <div class="p-4 rounded shadow-sm"
                        style="background: linear-gradient(135deg, #f9fafc, #eef1f6); border-left: 5px solid #3c8dbc;">
                        <h4 class="fw-bold mb-3 text-primary">
                            <i class="bi bi-info-circle"></i> {{ $category->name }}
                        </h4>
                        <p class="text-secondary">{{ $category->desc ?? 'No description available' }}</p>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p><strong>Parent:</strong> {{ $category->parent?->name ?? 'N/A' }}</p>
                                <p><strong>Order No:</strong> {{ $category->series }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Created:</strong> {{ $category->created_at->format('d M Y, h:i A') }}</p>
                                <p><strong>Updated:</strong> {{ $category->updated_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
            </div>
        </div>
  <!-- Products List -->
    <div class="card">
        <div class="card-header">
            <h5>{{ $category->name }} Products</h5>
        </div>
        <div class="card-body">
            @if($products->isEmpty())
                <p class="text-muted">No products found in this category.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Image</th>
                                <th>Type</th>
                                <th>Slug</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Updated</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $index => $product)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>
                                       <img src="{{ $product->main_image }}" alt="Product Image" style="width:50px; height:50px; object-fit:cover;" class="rounded">
                                    </td>
                                    <td>{{ $product->product_type ?? '-' }}</td>
                                    <td>{{ $product->slug }}</td>
                                    <td>

                                        @if($product->is_active == 1)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $product->created_at->diffForHumans() }}</td>
                                    <td>{{ $product->updated_at->diffForHumans() }}</td>
                                    <td>
                                        <a class="btn btn-success"
                                                href="{{ route('admin.product.view-details', $product->id) }}">
                                                <i class="fa fa-eye"></i> Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
