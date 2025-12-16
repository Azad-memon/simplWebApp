@extends('admin.layouts.master')
@section('title', 'CMS Pages')

@section('css')
@endsection

@section('style')
    <style>
        body.dark-only .page-wrapper .page-body-wrapper .page-body .card:not(.email-body)
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        body.dark-only .page-wrapper .page-body-wrapper .page-body .card:not(.email-body)
        .dataTables_wrapper .dataTables_paginate .paginate_button:active {
            border-color: var(--theme-deafult);
            background: #3a3e4a;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col d-flex justify-content-end gap-2">
                 <a href="#" class="btn btn-primary" id="add-page" style="float: right">Add New Page</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>CMS Pages</h4>
                <p class="text-muted">Here are all the CMS pages.</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <div id="cms-table_wrapper" class="dataTables_wrapper">

                        <table class="hover dataTable" id="cms-table" role="grid"
                            aria-describedby="cms-table_info">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Slug</th>
                                    {{-- <th>Status</th> --}}
                                    {{-- <th>Created At</th> --}}
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pages as $page)
                                    <tr>
                                        <td>{{ $page->id }}</td>
                                        <td>{{ $page->title }}</td>
                                        <td>{{ $page->slug }}</td>
                                        {{-- <td>
                                            <x-status-toggle
                                                :id="$page->id"
                                                :status="$page->status"
                                                :url="route('admin.cms.toggleStatus')" />
                                        </td> --}}
                                        {{-- <td>{{ $page->created_at->format('d M Y h:i A') }}</td> --}}
                                        <td>
                                            <a href="#" data-id="{{ $page->id }}"
                                               class="btn btn-success edit-page">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a class="btn btn-danger theme delete-btn" href="javascript:void(0);"
                                                data-id=""
                                                data-action="{{ route('admin.cms.destroy', $page->id) }}">
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
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#cms-table').DataTable({
            destroy: true,
            "order": [[0, "desc"]]
        });
    });
</script>
@endpush
