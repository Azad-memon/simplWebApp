@extends('admin.layouts.master')
@section('title', 'Ingredients for ' . ucfirst($category->name??''))

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

.badge {
    font-size: 0.85rem;
    border-radius: 8px;
}
.category-info {
    background: linear-gradient(145deg, #ffffff, #f5f4ff);
    border-left: 6px solid #7366ff;
    transition: 0.3s;
}
.category-info:hover {
    box-shadow: 0 4px 10px rgba(115, 102, 255, 0.2);
}
.category-info h6 {
    color: #7366ff;
    font-weight: 600;
}
.category-info h5 {
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
/* Actions container: fixed width to control spacing */
.actions-container {
    display: flex;
    align-items: center;
    justify-content: space-between; /* left group left, right group right inside container */
    width: 140px;          /* <-- adjust this width (120-180) to increase/decrease separation */
    margin: 0 auto;        /* center the container inside table cell */
}

/* left group keeps buttons close together */
.actions-left .btn {
    margin-right: 6px;
}

/* if there's no right group, left group will appear at left of the container.
   To center left-group when no right button exists, add this small rule: */
.actions-container:empty { justify-content: center; }

/* reuse your outline theme styling (ensure this exists) */
.btn-outline-theme {
    color: #7366ff !important;
    border: 1px solid #7366ff;
    background-color: transparent;
    border-radius: 8px;
    padding: 6px 14px;
    font-weight: 500;
    transition: all 0.18s ease;
}

.btn-outline-theme:hover {
    background: #7366ff;
    color: #fff !important;
    box-shadow: 0 3px 6px rgba(115, 102, 255, 0.18);
}

/*  Icon-only Buttons (edit, delete, recipe) */
.btn-icon-theme {
    color: #7366ff !important;
    border: 1px solid #7366ff;
    background-color: transparent;
    border-radius: 8px;
    padding: 4px 8px;
    height: 32px;
    width: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.18s ease;
    margin-right: 6px;
}

.btn-icon-theme:hover {
    background: #7366ff;
    color: #fff !important;
    transform: translateY(-1px);
    box-shadow: 0 3px 6px rgba(115, 102, 255, 0.18);
}
</style>

<div class="container-fluid py-3">

    {{-- 🔙 Header Bar --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0 text-theme">
            <i class="fa fa-cubes me-2"></i> Category: {{ ucfirst($category->name??'') }}
        </h3>
        <a href="{{ route('admin.ingredient.categories') }}" class="btn btn-outline-theme">
            <i class="fa fa-arrow-left me-1"></i> Back to Categories
        </a>
    </div>

    {{--  Category Info Card --}}
    <div class="card category-info mb-4">
        <div class="card-body">
            <div class="row align-items-center text-center text-md-start">
                <div class="col-md-3 mb-3 mb-md-0">
                    <h6>Category Name</h6>
                    <h5>{{ ucfirst($category->name??'') }}</h5>
                </div>
                <div class="col-md-3 mb-3 mb-md-0">
                    <h6>Description</h6>
                    <h5>{{ $category->description ?? '--' }}</h5>
                </div>
                <div class="col-md-3 mb-3 mb-md-0">
                    <h6>Ingredients Count</h6>
                    <h5>{{ $category->ingredients ? $category->ingredients->count() : 0     }}</h5>
                </div>
                <div class="col-md-3">
                    <h6>Status</h6>
                    @if ($category->is_active)
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
            <h5 class="mb-0"><i class="fa fa-flask me-2"></i> Ingredient List</h5>
            <a href="javascript:void(0);" class="btn btn-theme shadow-sm" id="add-ingredients"
               data-category-id="{{ $category->id }}"
               data-category-name="{{ $category->name??'' }}">
                <i class="fa fa-plus me-1"></i> Add Ingredient
            </a>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-hover align-middle" id="ingredientTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Ingredient Name</th>
                        <th>Type</th>
                        <th>Unit</th>
                        {{-- <th>Unit Price</th> --}}
                        <th>Min Qty</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($category->ingredients as $i => $ingredient)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td class="fw-semibold text-dark">
                                <i class="fa fa-coffee me-2 text-theme"></i> {{ ucfirst($ingredient->ing_name) }}
                            </td>
                            <td>{{ ucfirst($ingredient->ing_type) }}</td>
                            <td>{{ $ingredient->unit ? $ingredient->unit->name . ' (' . $ingredient->unit->symbol . ')' : '-' }}</td>
                            {{-- <td>Rs {{ number_format($ingredient->unit_price, 2) }}</td> --}}
                            <td>{{ $ingredient->min_quantity ?? '--' }}</td>
                            <td>
                                @if ($ingredient->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                        <td>
                        <div class="actions-container">
                            <div class="actions-left">
                                <a href="javascript:void(0);" id="edit-ingredients"
                                class="btn btn-sm btn-outline-theme edit-ingredient"
                                data-id="{{ $ingredient->ing_id }}"
                                data-category-id="{{ $category->id }}"
                                data-category-name="{{ $category->name??'' }}"
                                title="Edit">
                                    <i class="fa fa-edit"></i>
                                </a>

                                <a href="javascript:void(0);"
                                class="btn btn-sm btn-outline-theme delete-btn"
                                data-action="{{ route('admin.ingredient.delete', $ingredient->ing_id) }}"
                                title="Delete">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </div>

                            @if ($ingredient->ing_type == 'custom')
                                <div class="actions-right">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-theme add-recipe-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#standardIngredientsModal"
                                            data-id="{{ $ingredient->ing_id }}"
                                            data-name="{{ $ingredient->ing_name }}"
                                            data-standard-ingredients='@json($ingredient->standardIngredients->pluck("ing_id")->toArray())'
                                            title="Add Recipe">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
  <!-- Standard Ingredients Modal -->
    <div class="modal fade" id="standardIngredientsModal" tabindex="-1" aria-labelledby="standardIngredientsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="standardIngredientsModalLabel">Select Recipe for </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Select</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Unit</th>
                                    {{-- <th>Unit Price</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ingredients->where('ing_type', 'standard') as $stdIng)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="selected_ingredients[]"
                                                value="{{ $stdIng->ing_id }}"
                                                @if (!empty($stdIng->standardIngredients) && $stdIng->standardIngredients->pluck('ing_id')->contains($stdIng->ing_id)) checked @endif>
                                        </td>
                                        <td>{{ $stdIng->ing_name }}</td>
                                        <td>{{ $stdIng->category ? $stdIng->category->name : '-' }}</td>
                                        <td>{{ $stdIng->unit ? $stdIng->unit->name : '-' }}</td>
                                        {{-- <td>{{ $stdIng->unit_price }}</td> --}}
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="saveSelectedIngredients">Save Selection</button>
                </div>
            </div>
        </div>
    </div>
{{--  DataTable Scripts --}}
@push('scripts')
<script>
$(document).ready(function() {
    $('#ingredientTable').DataTable({
        responsive: true,
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search ingredients...",
            lengthMenu: "Show _MENU_ entries",
            paginate: { previous: "Prev", next: "Next" }
        }
    });
});
   $(document).on("click", ".add-recipe-btn", function () {
        const ingId = $(this).data("id");
        const ingName = $(this).data("name");
        const standardIngredients = $(this).data("standard-ingredients");
        $("#standardIngredientsModalLabel").text(`Select Recipe for ${ingName}`);

        // (Optional) If you need to use ID or ingredients later:
        $("#standardIngredientsModal").data("ingredient-id", ingId);
        $("#standardIngredientsModal").data("standard-ingredients", standardIngredients);
    });
</script>
@endpush
@endsection
