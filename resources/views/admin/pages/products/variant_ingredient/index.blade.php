@extends('admin.layouts.master')
@section('title', 'Ingredients for Variant')

@section('content')
<style>
/* 🎨 Modern Indigo Theme (#7366FF) */
body {
    background-color: #f8f9fc !important;
}
.card {
    border: none;
    border-radius: 14px;
    box-shadow: 0 3px 12px rgba(115, 102, 255, 0.15);
}
.card-header {
    background: linear-gradient(135deg, #7366ff, #9a90ff);
    color: #fff;
    font-weight: 600;
    border-top-left-radius: 14px;
    border-top-right-radius: 14px;
}
.table thead th {
    background-color: #f0edff;
    color: #4a3fd0;
    font-weight: 600;
    border-bottom: 2px solid #ddd9ff;
}
.table-hover tbody tr:hover {
    background-color: #f9f8ff;
}
.btn-theme {
    background: linear-gradient(135deg, #7366ff, #8c7aff);
    color: #fff;
    border: none;
    transition: all 0.3s ease;
}
.btn-theme:hover {
    background: linear-gradient(135deg, #6254e8, #7366ff);
    transform: translateY(-1px);
}
.btn-outline-theme {
    color: #7366ff;
    border: 1px solid #7366ff;
}
.btn-outline-theme:hover {
    background: #7366ff;
    color: #fff;
}
.badge {
    font-size: 0.85rem;
    border-radius: 8px;
}
.variant-info {
    background: linear-gradient(145deg, #ffffff, #f5f4ff);
    border-left: 6px solid #7366ff;
    transition: 0.3s;
}
.variant-info:hover {
    box-shadow: 0 4px 10px rgba(115, 102, 255, 0.2);
}
.variant-info h6 {
    color: #7366ff;
    font-weight: 600;
}
.variant-info h5 {
    color: #2b2470;
    font-weight: 700;
}
.text-theme {
    color: #7366ff !important;
}
/* 🧾 DataTable Theme */
.dataTables_wrapper .dataTables_filter input {
    border-radius: 8px;
    border: 1px solid #d4d0ff;
    padding: 5px 10px;
}
.dataTables_wrapper .dataTables_paginate .paginate_button {
    background: #f3f2ff;
    border-radius: 6px;
    color: #7366ff !important;
    margin: 0 3px;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #7366ff !important;
    color: #fff !important;
}
</style>

<div class="container-fluid py-3">

    {{-- 🔙 Header Bar --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0 text-theme">
            <i class="fa fa-layer-group me-2"></i> Variant Details
        </h3>
        <a href="{{ route('admin.product.view-details', $variant->product_id) }}" class="btn btn-outline-theme">
            <i class="fa fa-arrow-left me-1"></i> Back
        </a>
    </div>

    {{-- 🧾 Variant Info Card --}}
    <div class="card variant-info mb-4">
        <div class="card-body">
            <div class="row align-items-center text-center text-md-start">
                <div class="col-md-3 mb-3 mb-md-0">
                    <h6>Variant SKU</h6>
                    <h5>{{ $variant->sku }}</h5>
                </div>
                <div class="col-md-2 mb-3 mb-md-0">
                    <h6>Size</h6>
                    <h5>{{ $variant->size ?? '--' }}</h5>
                </div>
                <div class="col-md-2 mb-3 mb-md-0">
                    <h6>Unit</h6>
                    <h5>{{ $variant->unit ?? '--' }}</h5>
                </div>
                <div class="col-md-2 mb-3 mb-md-0">
                    <h6>Price</h6>
                    <h5 class="text-success">Rs {{ number_format($variant->price, 2) }}</h5>
                </div>
                <div class="col-md-2">
                    <h6>Status</h6>
                    @if ($variant->is_active)
                        <span class="badge bg-success px-3 py-2">Active</span>
                    @else
                        <span class="badge bg-danger px-3 py-2">Inactive</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{--  Ingredient List Section --}}
    <div class="card shadow-sm border-0">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fa fa-flask me-2"></i> Product Recipe</h5>
            <button class="btn btn-theme shadow-sm" id="add-variant-ingredient"
                data-variant-id="{{ $variant->id }}"
                data-url="{{ route('admin.product.variants.ingredients.add', $variant->id) }}">
                <i class="fa fa-plus me-1"></i> Add Ingredient
            </button>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-hover align-middle" id="ingredientTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Ingredient Category</th>
                        <th>Default Ingredient</th>
                        <th>Quantity</th>
                        <th>Type</th>
                        <th>Unit</th>
                        <th>Visible</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ingredients as $i => $item)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->pivot->defaultIngredient ? $item->pivot->defaultIngredient->ing_name : '-' }}</td>
                            <td>{{ $item->pivot->quantity }}</td>
                            <td>{{ $item->pivot->type }}</td>
                            <td>{{ isset($item->pivot->defaultIngredient->unit)? '('.$item->pivot->defaultIngredient->unit->symbol.')': '--' }}</td>
                            <td>
                                {{-- @if($item->pivot->status == 1)
                                    <span class="badge bg-success px-3 py-2">Active</span>
                                @else
                                    <span class="badge bg-danger px-3 py-2">Inactive</span>
                                @endif --}}
                                 <x-status-toggle :id="$item->pivot->id" :status="$item->pivot->status ? 1 : 0" :url="route('admin.product.variants.ingredients.toggleStatus')" />
                            </td>
                            <td class="text-center">
                                <a href="javascript:void(0);" class="btn btn-sm btn-success edit-variant-ingredient"
                                    data-id="{{ $item->id }}"
                                    data-ingredient-id="{{ $item->pivot->id }}"
                                    data-variant-id="{{ $variant->id }}"
                                    data-quantity="{{ $item->pivot->quantity }}">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <a class="btn btn-sm btn-danger delete-btn" href="javascript:void(0);"
                                    data-toggle='tooltip' title='Delete Ingredient'
                                    data-id=""
                                    data-action='{{ route('admin.product.variants.ingredients.delete', ['variant' => $variant->id, 'id' => $item->pivot->id]) }}'>
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- 📊 DataTable Scripts --}}
@push('scripts')

<script>
$(document).ready(function() {
     $('#ingredientTable').DataTable({
            pageLength: 5,
            order: [[0, 'asc']],
        });
});
</script>
@endpush
@endsection
