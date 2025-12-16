@extends('admin.layouts.master')
@section('title', 'Sizes')

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

/* actions */
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
                <h4 class="mb-0"><i class="fa fa-ruler-combined me-2 text-theme"></i> Available Sizes</h4>
                <small class="text-muted">Manage your product sizes easily</small>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button id="refreshSizes" class="btn btn-outline-secondary" title="Refresh">
                    <i class="fa fa-sync"></i>
                </button>

                <a href="javascript:void(0)" class="btn btn-theme" id="add-size">
                    <i class="fa fa-plus me-1"></i> Add Size
                </a>
            </div>
        </div>

        {{-- TABLE CARD --}}
        <div class="card p-3">
            <div class="table-responsive">
                <table class="table align-middle table-hover w-100" id="sizesTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Size Name</th>
                            <th>Size Code</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sizes as $index => $size)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-semibold text-dark">{{ ucfirst($size->name) }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $size->code }}</span></td>
                                <td class="text-center action-btns">
                                    {{-- <a href="{{ route('admin.size.translate', ['id' => $size->id]) }}"
                                       class="btn btn-sm btn-outline-info" title="Translate">
                                        <i class="fas fa-language"></i>
                                    </a> --}}
                                    <a href="javascript:void(0);"
                                       class="btn btn-sm btn-outline-success edit-size"
                                       data-id="{{ $size->id }}" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a href="javascript:void(0);"
                                       class="btn btn-sm btn-outline-danger delete-btn"
                                       data-action="{{ route('admin.size.delete', $size->id) }}" title="Delete">
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
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Initialize DataTable
    $('#sizesTable').DataTable({
        responsive: true,
        pageLength: 10,
        lengthMenu: [5,10,25,50],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search sizes...",
            lengthMenu: "Show _MENU_"
        },
        columnDefs: [
            { orderable: false, targets: [3] } // disable ordering on Actions
        ],
        autoWidth: false
    });

    // refresh button
    document.getElementById('refreshSizes')?.addEventListener('click', function () {
        location.reload();
    });
});
</script>
@endpush
@endsection
