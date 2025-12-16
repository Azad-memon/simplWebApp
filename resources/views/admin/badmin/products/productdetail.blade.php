@extends('admin.layouts.master')
@section('title', $product->name)

@section('css')
<style>
    /* Generic Card Hover Effect */
    .hover-card {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s ease-in-out;
    }
    .hover-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 22px rgba(0,0,0,0.12);
    }

    /* Product Card */
    .product-card img {
        max-height: 260px;
        object-fit: contain;
        background: #fafafa;
        border-bottom: 1px solid #f1f1f1;
    }

    /* Badges */
    .badge-custom {
        font-size: 0.8rem;
        padding: 6px 12px;
        border-radius: 30px;
    }

    /* Variant Card */
    .variant-card {
        border: 1px solid #f1f1f1;
        border-radius: 12px;
        transition: 0.3s;
    }
    .variant-card:hover {
        border-color: #d6e4ff;
        transform: translateY(-4px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.1);
    }
    .variant-card h5 {
        font-size: 1rem;
    }

    /* Ingredients */
    .ingredients-list li {
        font-size: 0.9rem;
        padding: 6px 0;
        border-bottom: 1px dashed #ececec;
    }

    /* Addons Table */
    .addons-table {
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease-in-out;
    }
    .addons-table thead {
        background: linear-gradient(45deg, #f8f9fa, #f1f3f5);
    }
    .addons-table tbody tr {
        transition: all 0.2s ease-in-out;
    }
    .addons-table tbody tr:hover {
        background: #fefefe;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    /* --- Stylish Headings --- */
    .section-heading {
        font-weight: 800;
        font-size: 1.4rem;
        color: #1e293b;
        position: relative;
        display: inline-block;
        padding-left: 32px;
    }
    .section-heading i {
        position: absolute;
        left: 0;
        top: 2px;
        font-size: 1.3rem;
        color: #0d6efd;
    }
    .section-heading::after {
        content: "";
        position: absolute;
        bottom: -6px;
        left: 32px;
        height: 4px;
        width: 50%;
        border-radius: 2px;
        background: linear-gradient(90deg, #0d6efd, #00c6ff);
        transition: width 0.3s ease;
    }
    .section-heading:hover::after {
        width: 100%;
    }

    .product-title {
        font-size: 1.8rem;
        font-weight: 900;
        color: #111827;
        margin-bottom: 1rem;
        position: relative;
        display: inline-block;
    }
    .product-title::after {
        content: "";
        position: absolute;
        bottom: -8px;
        left: 0;
        height: 3px;
        width: 60%;
        border-radius: 2px;
        background: linear-gradient(90deg, #f43f5e, #f97316);
    }
</style>
@endsection

@section('content')
<div class="container mt-5">
    <div class="row g-4">

        {{-- Product Info --}}
        <div class="col-md-4">
            <div class="card product-card hover-card shadow-sm">
                <img src="{{ getImageByType($product->images, 'full') ? getImageByType($product->images, 'full') : asset('assets/images/default_image.png') }}"
                     class="card-img-top p-4" alt="{{ $product->name }}">
                <div class="card-body text-center">
                    <h3 class="product-title">{{ $product->name }}</h3>
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <span class="badge bg-primary badge-custom">
                            {{ $product->category?->name ?? 'Uncategorized' }}
                        </span>
                        <span class="badge bg-info text-dark badge-custom">
                            {{ ucfirst($product->product_type) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Variants & Ingredients --}}
        <div class="col-md-8">
            <h4 class="section-heading mb-3"><i class="fas fa-box"></i> Variants</h4>
            <div class="row g-3">
                @foreach($product->variants->where('is_active', 1) as $variant)
                    <div class="col-md-6">
                        <div class="card variant-card h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3">
                                    {{ $variant->sizes->name ?? 'N/A' }}
                                    <span class="float-end text-success">Rs {{ number_format($variant->price,2) }}</span>
                                </h5>

                                @php
                                    $activeIngredients = $variant->ingredients->where('is_active', 1);
                                @endphp
                                @if($activeIngredients->count() > 0)
                                    <p class="mb-2 fw-semibold text-muted">Ingredients</p>
                                    <ul class="ingredients-list list-unstyled mb-0">
                                        @foreach($activeIngredients as $ingredient)
                                            <li>
                                                <i class="fas fa-leaf text-success me-2"></i>
                                                {{ $ingredient->ing_name }}
                                                <small class="text-muted">({{ $ingredient->unit?->name ?? '-' }})</small>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Addons --}}
            @php
             $activeAddons = $product->addons;
            @endphp
            @if($activeAddons->count() > 0)
                <h4 class="section-heading mt-5"><i class="fas fa-plus-circle"></i> Addons</h4>
                <div class="table-responsive hover-card shadow-sm">
                    <table class="table table-bordered addons-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th class="text-end">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activeAddons as $addon)
                                @if($addon->addonable_type == 'App\Models\ProductVariant' && $addon->addonable?->is_active)
                                    <tr>
                                        <td>{{ $addon->addonable->sizes->name ?? 'N/A' }}</td>
                                        <td class="text-end text-success">Rs {{ number_format($addon->price,2) }}</td>
                                    </tr>
                                @elseif($addon->addonable_type == 'App\Models\Ingredient' && $addon->addonable?->is_active)
                                    <tr>
                                        <td>{{ $addon->addonable->ing_name ?? 'N/A' }}</td>
                                        <td class="text-end text-success">Rs {{ number_format($addon->price,2) }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
