@extends('admin.layouts.master')
@section('title', 'Products')

@section('content')
<style>
    .action-btns .btn {
        font-size: 12px;
        padding: 4px 8px;
        border-radius: 6px;
    }

    .toggle-btn-sm {
        transform: scale(0.8);
    }

    .table thead {
        background-color: #f8f9fc;
        font-weight: 600;
    }

    .table tbody tr:hover {
        background-color: #f4f4ff;
    }
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

<div class="container-fluid">
   {{-- TOP BAR --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0">
            <i class="fa fa-box-open me-2 text-theme"></i> Products
        </h4>
        <small class="text-muted">Manage all products — click Details to view or edit</small>
    </div>

    <div class="d-flex align-items-center gap-2">
        {{-- Refresh Button --}}
        <button id="refreshProducts" class="btn btn-outline-secondary btn-sm d-flex align-items-center" title="Refresh">
            <i class="fa fa-sync me-1"></i> Refresh
        </button>

        {{-- Add Product Button --}}
        <a href="javascript:void(0)" id="add-product" class="btn btn-theme btn-sm d-flex align-items-center">
            <i class="fa fa-plus me-1"></i> Add Product
        </a>
    </div>
</div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body table-responsive">
            <table class="hover dataTable" id="example-style-4" role="grid" aria-describedby="example-style-4_info">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Image</th>
                        <th>Category</th>
                        <th>Product Type</th>
                        <th>Slug</th>
                        <th>New</th>
                        <th>Featured</th>
                        <th>Best Selling</th>
                        <th>Status</th>
                        <th style="max-width:100px">Last Updated</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($products as $i => $product)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $product->name }}</td>
                            <td>
                                <img src="{{ $product->main_image }}" width="50" height="50"
                                     alt="{{ $product->name }}" class="rounded">
                            </td>
                            <td>{{ $product->category->name ?? '-' }}</td>
                            <td>{{ $product->product_type }}</td>
                            <td>{{ $product->slug }}</td>

                            <td class="toggle-btn-sm">
                                <x-product-flage-toggle :id="$product->id" :status="$product->is_new ? 1 : 0" type="is_new"
                                    :url="route('admin.product.toggleFlag')" />
                            </td>

                            <td class="toggle-btn-sm">
                                <x-product-flage-toggle :id="$product->id" :status="$product->is_featured ? 1 : 0" type="is_featured"
                                    :url="route('admin.product.toggleFlag')" />
                            </td>

                            <td class="toggle-btn-sm">
                                <x-product-flage-toggle :id="$product->id" :status="$product->is_best_selling ? 1 : 0" type="is_best_selling"
                                    :url="route('admin.product.toggleFlag')" />
                            </td>

                            <td class="toggle-btn-sm">
                                <x-status-toggle :id="$product->id" :status="$product->is_active ? 1 : 0"
                                    :url="route('admin.product.toggleStatus')" />
                            </td>

                            <td>{{ $product->updated_at ? $product->updated_at->diffForHumans() : '-' }}</td>

                            <td class="text-center action-btns">
                                {{-- <a href="javascript:void(0)" class="btn btn-sm btn-outline-primary"
                                   data-id="{{ $product->id }}" id="edit-product" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </a>

                                <a href="javascript:void(0)" class="btn btn-sm btn-outline-danger delete-btn"
                                   data-action="{{ route('admin.product.delete', $product->id) }}" title="Delete">
                                    <i class="fa fa-trash"></i>
                                </a> --}}

                                <a href="{{ route('admin.product.view-details', $product->id) }}"
                                   class="btn btn-sm btn-outline-success" title="View Details">
                                    <i class="fa fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Image</th>
                        <th>Category</th>
                        <th>Product Type</th>
                        <th>Slug</th>
                        <th>New</th>
                        <th>Featured</th>
                        <th>Best Selling</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@push('scripts')
<script>

      document.getElementById('refreshProducts')?.addEventListener('click', function () {
        location.reload();
    });
</script>
@endpush
@endsection



