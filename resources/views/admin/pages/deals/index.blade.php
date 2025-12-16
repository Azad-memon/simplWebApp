@extends('admin.layouts.master')
@section('title', 'Deals')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <a href="#" class="btn btn-primary" id="add-deal" style="float: right">Add Deal</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header"><h5>Deals List</h5></div>
        <div class="card-body table-responsive">
             <table class="hover dataTable" id="example-style-4" role="grid"
                                    aria-describedby="example-style-4_info">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Price</th>
                        <th>Original Price</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($deals as $i => $deal)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $deal->title }}</td>
                            <td>Rs. {{ number_format($deal->price, 2) }}</td>
                            <td>
                                {{ $deal->original_price ? 'Rs. ' . number_format($deal->original_price, 2) : '-' }}
                            </td>
                            <td>
                                {{ $deal->start_date ? \Carbon\Carbon::parse($deal->start_date)->format('d M Y') : '-' }}
                                {{ $deal->start_time ? \Carbon\Carbon::parse($deal->start_time)->format('h:i A') : '' }}
                            </td>
                            <td>
                                {{ $deal->end_date ? \Carbon\Carbon::parse($deal->end_date)->format('d M Y') : '-' }}
                                {{ $deal->end_time ? \Carbon\Carbon::parse($deal->end_time)->format('h:i A') : '' }}
                            </td>
                            <td>
                                <x-status-toggle
                                    :id="$deal->id"
                                    :status="$deal->is_active"
                                    :url="route('admin.deals.toggleStatus')" />
                            </td>
                            <td>
                                <a href="#" class="btn btn-success btn-sm edit-deal" data-id="{{ $deal->id }}">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-danger btn-sm delete-btn"
                                    data-id="{{ $deal->id }}"
                                    data-action="{{ route('admin.deals.delete', $deal->id) }}">
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
@endsection
