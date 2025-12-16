@extends('admin.layouts.master')
@section('title', 'Posts')

@section('css')


@endsection

@section('style')
    <style>
        body.dark-only .page-wrapper .page-body-wrapper .page-body .card:not(.email-body) .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        body.dark-only .page-wrapper .page-body-wrapper .page-body .card:not(.email-body) .dataTables_wrapper .dataTables_paginate .paginate_button:active {
            border-color: var(--theme-deafult);
            /* color: black; */
            background: #3a3e4a;
        }
    </style>

@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5 style="display: inline">Shift</h5>
                        <a href="#" class="btn btn-primary" id="addBranchShift" style="float: right">Add</a>

                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div id="example-style-4_wrapper" class="dataTables_wrapper">

                                <table class="hover dataTable" id="example-style-4" role="grid"
                                    aria-describedby="example-style-4_info">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Shift Name</th>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($shifts as $index => $shift)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $shift->name }}</td>
                                                <td>{{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}</td>
                                                <td>
                                                       <a href="#" class="btn btn-success" id="editBranchShift" data-id="{{ $shift->id }}">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>

                                               <button class="btn btn-sm btn-danger delete-btn"  data-id="" data-branchid=""
                                                    data-action="{{ route('badmin.shifts.destroy', ['id' => $shift->id]) }}"><i class="fa fa-trash"></i></button>
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
        </div>
    </div>
@endsection


@section('script')
@endsection
