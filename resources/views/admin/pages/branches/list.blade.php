@extends('admin.layouts.master')
@section('title', 'Branches')

@section('style')
<style>
    #pac-input {
        left: 0px !important;
        top: 10px !important;
        padding: 9px !important;
        border-radius: 10px;
        background-color: #fff !important;
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
        margin-left: 12px;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        width: 400px;
    }

    .action-btns .btn {
        padding: 4px 8px !important;
    }

    .btn-theme {
        background-color: var(--theme-color, #7367f0);
        color: #fff;
    }

    .btn-theme:hover {
        background-color: #5a4fd4;
        color: #fff;
    }
     .toggle-btn-sm {
        transform: scale(0.8);
    }
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
@endsection

@section('content')
<div class="container-fluid">
    {{-- TOP BAR --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">
                <i class="fa fa-code-branch me-2 text-theme"></i> Branches
            </h4>
            <small class="text-muted">Manage company branches — edit, view, or delete below</small>
        </div>

        <div class="d-flex align-items-center gap-2">
            {{-- Refresh Button --}}
            <button id="refreshBranches" class="btn btn-outline-secondary btn-sm d-flex align-items-center" title="Refresh">
                <i class="fa fa-sync me-1"></i> Refresh
            </button>

            {{-- Add Branch Button --}}
            <a href="javascript:void(0)" id="add-branch" class="btn btn-theme btn-sm d-flex align-items-center">
                <i class="fa fa-plus me-1"></i> Add Branch
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if (session()->has('green'))
        <div class="alert alert-success">{{ session('green') }}</div>
    @elseif(session()->has('red'))
        <div class="alert alert-danger">{{ session('red') }}</div>
    @endif

    {{-- Branches Table --}}
    <div class="card">
        <div class="card-body table-responsive">
            <table class="hover dataTable" id="example-style-4" role="grid" aria-describedby="example-style-4_info">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Branch Code</th>
                        <th>Address</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th>Description</th>
                        <th>Open Time</th>
                        <th>Close Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($branches as $item)
                        <tr>
                            <td>{{ $item->name ?? '' }}</td>
                            <td>{{ $item->branch_code ?? '' }}</td>
                            <td title="{{ $item->address ?? '' }}">{{ \Illuminate\Support\Str::limit($item->address ?? '', 25, '...') }}</td>
                            <td>{{ $item->lat ?? '' }}</td>
                            <td>{{ $item->long ?? '' }}</td>
                            <td title="{{ $item->description ?? '' }}">{{ \Illuminate\Support\Str::limit($item->description ?? '', 25, '...') }}</td>

                            <td>{{ $item->open_time ?? '' }}</td>
                            <td>{{ $item->close_time ?? '' }}</td>
                            <td class="toggle-btn-sm">
                                <x-status-toggle
                                    :id="$item->id"
                                    :status="$item->status"
                                    :url="route('admin.branch.toggleStatus')" />
                            </td>
                            <td class="text-center action-btns">
                                <a href="{{ route('admin.branch.view', ['id' => $item->id]) }}" class="btn btn-sm btn-outline-success" title="View">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="#" class="btn btn-sm btn-outline-primary" id="edit-branch" data-id="{{ $item->id }}" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-danger delete-btn"
                                   data-action="{{ route('admin.branches.delete', ['id' => $item->id]) }}" title="Delete">
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

{{-- JS for Refresh --}}
<script>
    document.getElementById('refreshBranches').addEventListener('click', function () {
        location.reload();
    });
</script>
@endsection
