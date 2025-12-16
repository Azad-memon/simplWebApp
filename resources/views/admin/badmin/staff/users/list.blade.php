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
                        <h5 style="display: inline">Staff</h5>
                      <a href="#" class="btn btn-primary" id="addBranchStaff" style="float: right">Add</a>

                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div id="example-style-4_wrapper" class="dataTables_wrapper">

                                 <table class="hover dataTable" id="example-style-4" role="grid"
                        aria-describedby="example-style-4_info">
                        <thead>
                            <tr>
                                <th>Sr. No.</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Phone number</th>
                                <th>Email</th>
                                <td>Shift</td>
                                <td>Role</td>
                                <th>Employee id</th>
                                <th>Status</th>
                                <th>Last Updated</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($staff as $index => $getstaff)
                            @php $user=$getstaff->user @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $user->first_name }}</td>
                                    <td>{{ $user->last_name }}</td>
                                    <td>{{ $user->phone }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->shift)
                                            {{ $user->shift[0]->name }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->role_id == 4)
                                            Waiter / Service Staff
                                        @elseif($user->role_id == 5)
                                            Accountant
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                       <td>
                                        {{ $user->employee_id }}
                                       </td>

                                        <td>
                                      <x-status-toggle
                                        :id="$user->id"
                                        :status="$user->user_status"
                                        :url="route('badmin.toggleStatus')" />

                                       </td>
                                    <td>{{ $user->updated_at->diffForHumans() }}</td>
                                    <td>
                                        <a class="btn btn-sm btn-info" id="editdBranchStaff" data-id="{{ $user->id }}"><i class="fa fa-edit"></i></a>
                                        <a class="btn btn-danger theme delete-btn" href="javascript:void(0);" data-id="" data-action="{{ route('badmin.staff.delete', $user->id) }}">
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
        </div>
    </div>
@endsection


@section('script')
@endsection
