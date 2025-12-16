@extends('admin.layouts.master')
@section('title', 'Categories')

@section('content')
<style>
/* Theme: #7366FF */
body {
    background-color: #f8f9fc !important;
}

/* layout */
.container-full {
    max-width: 100%;
    padding-left: 18px;
    padding-right: 18px;
}

/* cards */
.card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(43,37,112,0.04);
}

/* theme button */
.btn-theme {
    background: linear-gradient(135deg, #7366ff, #8c7aff);
    color: #fff;
    border: none;
    padding: 8px 14px;
    border-radius: 8px;
    font-weight:600;
}
.btn-theme:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(115,102,255,0.12);
}

/* table */
.table thead th {
    background: linear-gradient(90deg,#f5f5ff,#ffffff);
    color: #2b2470;
    font-weight: 600;
    border-bottom: 2px solid rgba(115,102,255,0.06);
}
.table-hover tbody tr:hover {
    background-color: #fbfbff;
}
.table.w-100 {
    width: 100% !important;
}

/* badges */
.badge-active {
    background: #7366ff;
    color: #fff;
    border-radius: 10px;
    padding: 6px 10px;
    font-size: 0.85rem;
}
.badge-inactive {
    background: #eef0ff;
    color: #4a3fd0;
    border-radius: 10px;
    padding: 6px 10px;
    font-size: 0.85rem;
}

.action-btns .btn {
    padding: 0.35rem 0.6rem;
    border-radius: 8px;
}

/* theme text */
.text-theme { color: #7366ff !important; }

/* DataTables small style tweaks */
.dataTables_wrapper .dataTables_filter input {
    border-radius: 8px;
    border: 1px solid #e6e3ff;
    padding: 6px 10px;
}
.dataTables_wrapper .dataTables_paginate .paginate_button {
    background: #f5f4ff;
    color: #7366ff !important;
    border-radius: 6px;
    margin: 0 3px;
    padding: 4px 8px;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #7366ff !important;
    color: #fff !important;
}
</style>

<div class="container-fluid py-4">
    <div class="container-full">

        {{-- TOP BAR --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0"><i class="fa fa-layer-group me-2 text-theme"></i> Ingredient Categories</h4>
                <small class="text-muted">Manage categories — click View to open ingredients list</small>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button id="refreshCategories" class="btn btn-outline-secondary" title="Refresh">
                    <i class="fa fa-sync"></i>
                </button>

                {{-- Add Category button uses theme color --}}
                <a href="javascript:void(0)" class="btn btn-theme" id="add-ingredients-category">
                    <i class="fa fa-plus me-1"></i> Add New Category
                </a>
            </div>
        </div>

        {{-- TABLE CARD (full width) --}}
        <div class="card p-3">
            <div class="table-responsive">
                <table class="table align-middle table-hover w-100" id="categoriesTable">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th>Name</th>
                            <th width="15%">Ingredients</th>
                            <th width="10%">Status</th>
                            <th width="20%" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $index => $category)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-semibold text-dark">
                                    <i class="fa fa-coffee me-2 text-theme"></i> {{ ucfirst($category->name) }}
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $category->ingredients->count() }}
                                    </span>
                                </td>
                                <td>
                                    @if($category->is_active)
                                        <span class="badge-active">Active</span>
                                    @else
                                        <span class="badge-inactive">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center action-btns">
                                    <a href="javascript:void(0)" class="btn btn-sm btn-outline-primary edit-ingredients-category"
                                       data-id="{{ $category->id }}" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="btn btn-sm btn-outline-danger delete-btn"
                                       data-action="{{ route('admin.ingredient.category.delete', $category->id) }}" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </a>

                                    {{-- fixed: use route() for viewing ingredients --}}
                                    <a href="{{ route('admin.ingredients.view', $category->id) }}"
                                       class="btn btn-sm btn-outline-success" title="View Ingredients">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted fst-italic">No categories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Initialize DataTable (full width, responsive)
    $('#categoriesTable').DataTable({
        responsive: true,
        pageLength: 10,
        lengthMenu: [5,10,25,50],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search categories...",
            lengthMenu: "Show _MENU_"
        },
        columnDefs: [
            { orderable: false, targets: [2,4] } // disable ordering on Ingredients count and Actions
        ],
        // ensure table stretches full width
        autoWidth: false
    });

    // refresh button
    document.getElementById('refreshCategories')?.addEventListener('click', function () {
        location.reload();
    });

    // Example: hook for add/edit/delete buttons (you already had handlers)
    document.getElementById('add-ingredients-category')?.addEventListener('click', function () {
        // open modal or redirect to add form
        // implement your modal/open logic here
        console.log('Open add category modal');
    });
});
</script>
@endpush
@endsection
