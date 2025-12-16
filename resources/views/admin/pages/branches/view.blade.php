@extends('admin.layouts.master')
@section('title', 'View Branch')

<style>
    #map_display {
        width: 100%;
        height: 280px;
        border-radius: 12px;
    }

    .branch-overview-card {
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        border-radius: 12px;
        overflow: hidden;
    }

    .branch-overview-card .card-header {
        background: linear-gradient(135deg, #2563eb, #3b82f6);
        color: #fff;
        border: none;
        padding: 16px 20px;
    }

    .branch-info p {
        margin-bottom: 8px;
        font-size: 0.95rem;
    }

    .info-label {
        font-weight: 600;
        color: #555;
    }

    .info-value {
        color: #222;
    }

    .branch-status {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .branch-status.active {
        background-color: #d1fae5;
        color: #065f46;
    }

    .branch-status.inactive {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .nav-tabs {
        border-bottom: 2px solid #e5e7eb;
    }

    .nav-tabs .nav-link {
        border: none;
        color: #555;
        font-weight: 500;
        padding: 10px 18px;
        border-radius: 8px 8px 0 0;
        transition: all 0.2s ease;
    }

    .nav-tabs .nav-link:hover {
        background: #f3f4f6;
        color: #111;
    }

    .nav-tabs .nav-link.active {
        background: #2563eb;
        color: #fff;
        border: none;
        box-shadow: 0 -2px 6px rgba(0, 0, 0, 0.1) inset;
    }

    .dataTables_wrapper .dataTables_filter input {
        border-radius: 8px;
        border: 1px solid #ddd;
        padding: 5px 10px;
        outline: none;
    }

    .dataTables_wrapper .dataTables_length select {
        border-radius: 8px;
        border: 1px solid #ddd;
        padding: 4px 8px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 6px !important;
    }

    .table-hover tbody tr:hover {
        background: #f9fafb;
    }

    #branchOrdersTable,
    thead th {
        background-color: white;
        /* Bootstrap primary color */
        color: black;
        /* White text */
    }

    #branchOrdersTable thead th {
        vertical-align: middle;
    }
</style>

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Left Column -->
            <div class="col-md-9">

                <!-- Branch Overview -->
                <div class="card branch-overview-card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fa fa-store me-2"></i> {{ $branch->name }}
                        </h5>
                        <a href="#" class="btn btn-primary btn-sm d-flex align-items-center gap-1 px-3 py-1 shadow-sm"
                            id="edit-branch" data-id="{{ $branch->id }}">
                            <i class="fa fa-edit"></i> Edit Branch
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                               <p><i class="fa fa-tag text-primary me-2"></i><span
                                        class="info-label">Branch Code:</span> <span
                                        class="info-value">{{ $branch->branch_code }}</span></p>
                                <p><i class="fa fa-map-marker-alt text-danger me-2"></i><span
                                        class="info-label">Address:</span> <span
                                        class="info-value">{{ $branch->address }}</span></p>
                                <p><i class="fa fa-phone text-success me-2"></i><span class="info-label">Phone:</span> <span
                                        class="info-value">{{ $branch->phone }}</span></p>
                                <p><i class="fa fa-clock text-warning me-2"></i><span class="info-label">Open Time:</span>
                                    <span class="info-value">{{ $branch->open_time }}</span></p>
                            </div>

                            <div class="col-md-6">
                                <p><i class="fa fa-clock text-warning me-2"></i><span class="info-label">Close Time:</span>
                                    <span class="info-value">{{ $branch->close_time }}</span></p>
                                <p><i class="fa fa-info-circle text-secondary me-2"></i><span
                                        class="info-label">Status:</span>
                                    <span class="branch-status {{ $branch->status == 'active' ? 'active' : 'inactive' }}">
                                        {{ $branch->status == 'active' ? 'Active' : 'Inactive' }}
                                    </span>
                                </p>
                                <p><i class="fa fa-align-left text-info me-2"></i><span
                                        class="info-label">Description:</span> <span
                                        class="info-value">{{ $branch->description ?? 'N/A' }}</span></p>
                            </div>
                        </div>

                        {{-- ✅ Open Days Section --}}
                        @php
                            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                            $openDays = $branch->open_days ?? [];
                        @endphp

                        <div class="mt-3">
                            <h6><i class="fa fa-calendar-day text-primary me-2"></i> Open Days:</h6>
                            <div class="d-flex flex-wrap mt-2">
                                @foreach ($days as $day)
                                    @if (in_array($day, $openDays))
                                        <span class="badge bg-success me-2 mb-2 text-capitalize">
                                            {{ $day }} (Open)
                                        </span>
                                    @else
                                        <span class="badge bg-danger me-2 mb-2 text-capitalize">
                                            {{ $day }} (Closed)
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Tabs Section -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0">
                        <ul class="nav nav-tabs card-header-tabs" id="branchTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="orders-tab" data-bs-toggle="tab" href="#orders"
                                    role="tab">
                                    <i class="fa fa-cog me-1"></i> Orders
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="inventory-tab" data-bs-toggle="tab" href="#inventory"
                                    role="tab">
                                    <i class="fa fa-cog me-1"></i> Inventory
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="admins-tab" data-bs-toggle="tab" href="#admins" role="tab">
                                    <i class="fa fa-user-shield me-1"></i> Branch Admins
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" id="staff-tab" data-bs-toggle="tab" href="#staff" role="tab">
                                    <i class="fa fa-cog me-1"></i> Staff
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="shifts-tab" data-bs-toggle="tab" href="#shifts" role="tab">
                                    <i class="fa fa-cog me-1"></i> Shifts
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body tab-content">
                        <!-- Branch Admins Tab -->
                        <div class="tab-pane fade" id="admins" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0 fw-bold"><i class="fa fa-users me-2 text-primary"></i> Manage Branch Admins
                                </h6>
                                <button class="btn btn-sm btn-primary" id="add-branch-user" data-id="{{ $branch->id }}">
                                    <i class="fa fa-user-plus me-1"></i> Add Admin
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table id="branchAdminsTable" class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                            <th>Last Updated</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($branch->users as $index => $user)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $user->first_name }}</td>
                                                <td>{{ $user->last_name }}</td>
                                                <td>{{ $user->phone }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>
                                                    <x-status-toggle :id="$user->id" :status="$user->user_status"
                                                        :url="route('admin.branch.branchadmin.user.toggleStatus')" />
                                                </td>
                                                <td>{{ $user->updated_at->diffForHumans() }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-info" id="edit-user"
                                                        data-id="{{ $user->id }}">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger delete-btn"
                                                        data-action="{{ route('admin.branch.branchadmin.delete', ['user' => $user->id, 'branchid' => $branch->id]) }}">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Orders Tab -->
                        <div class="tab-pane fade show active" id="orders" role="tabpanel">

                            {{-- ====== Top Stats ====== --}}
                            {{-- <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="stats-card bg-gradient-primary shadow-sm">
                                    <div>
                                        <h6>Total Orders</h6>
                                        <h4 class="fw-bold">{{ $orders->count() }}</h4>
                                    </div>
                                    <i class="fa fa-shopping-cart icon"></i>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card bg-gradient-success shadow-sm">
                                    <div>
                                        <h6>Completed</h6>
                                        <h4 class="fw-bold">{{ $orders->where('status', 'completed')->count() }}</h4>
                                    </div>
                                    <i class="fa fa-check-circle icon"></i>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card bg-gradient-warning shadow-sm">
                                    <div>
                                        <h6>Pending</h6>
                                        <h4 class="fw-bold">{{ $orders->where('status', 'pending')->count() }}</h4>
                                    </div>
                                    <i class="fa fa-hourglass-half icon"></i>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card bg-gradient-danger shadow-sm">
                                    <div>
                                        <h6>Revenue</h6>
                                        <h4 class="fw-bold">Rs{{ number_format($orders->sum('total_amount'), 2) }}</h4>
                                    </div>
                                    <i class="fa fa-dollar-sign icon"></i>
                                </div>
                            </div>
                        </div> --}}

                            {{-- ====== Orders Table ====== --}}
                            <div class="card shadow-sm orders-table">
                                <div class="card-header d-flex justify-content-between align-items-center"
                                    style="padding: 0px">
                                    <h5 class="fw-bold mb-0">
                                        <i class="fa fa-shopping-basket me-2 text-primary"></i> Orders List
                                    </h5>
                                </div>

                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle" id="branchOrdersTable">
                                            <thead>
                                                <tr>
                                                    <th>#Order</th>
                                                    <th>Customer</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($orders as $order)
                                                    <tr>
                                                        <td><strong>#{{ $order->order_uid }}</strong></td>
                                                        <td>
                                                           <div class="d-flex align-items-center">
                                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($order->customer_name ?? 'N A') }}&background=random"
                                                                class="rounded-circle me-2" width="32" height="32">
                                                            <span>{{ $order->customer_name ?? 'N/A' }}</span>
                                                        </div>
                                                        </td>
                                                        <td><span
                                                                class="fw-bold text-success">Rs{{ number_format($order->final_amount, 2) }}</span>
                                                        </td>
                                                        <td>
                                                            @php
                                                                $statusClass = match ($order->status) {
                                                                    'pending' => 'status-pending',
                                                                    'completed' => 'status-completed',
                                                                    'cancelled' => 'status-cancelled',
                                                                    default => '',
                                                                };
                                                                $statusIcon = match ($order->status) {
                                                                    'pending' => 'fa-hourglass-half',
                                                                    'completed' => 'fa-check',
                                                                    'cancelled' => 'fa-times',
                                                                    default => 'fa-circle',
                                                                };
                                                            @endphp
                                                            <span class="order-status {{ $statusClass }}">
                                                                <i class="fa {{ $statusIcon }}"></i>
                                                                {{ ucfirst($order->status) }}
                                                            </span>
                                                        </td>
                                                        <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d M, Y') }}
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('admin.order.show', $order->id) }}"
                                                                class="btn btn-sm btn-primary">
                                                                <i class="fa fa-eye"></i> View
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

                        <!-- Inventory Tab -->
                        <div class="tab-pane fade" id="inventory" role="tabpanel">
                            <div class="mt-3">
                                <div class="card-header" style="padding: 0px">
                                    <h5 class="mb-0">Inventory</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="hover dataTable" id="inventory-table" role="grid">
                                            <thead style="background-color:white; color:black;">
                                                <tr>
                                                    <th style="max-width:50px">Sr. No.</th>
                                                    <th style="max-width:100px">Image</th>
                                                    <th style="max-width:100px">Name</th>
                                                    <th style="max-width:100px">Available Quantity</th>
                                                    <th style="max-width:100px">Status</th>
                                                    <th style="max-width:100px">Updated By</th>
                                                    <th style="max-width:100px">Last Updated</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (!empty($inventories))
                                                    @php $serial = 1; @endphp
                                                    @foreach ($inventories as $value)
                                                        @php
                                                            $availableQty = $value->quantity_balance ?? 0;
                                                            $minQty = $value->min_quantity ?? 0;
                                                            $isLowStock = $availableQty <= $minQty;
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $serial++ }}</td>
                                                            <td>
                                                                <div class="gallery my-gallery" itemscope="">
                                                                    <figure
                                                                        class="col-xl-3 col-md-4 col-6 custom-image-container"
                                                                        itemprop="associatedMedia" itemscope>
                                                                        <a class="image-popup-no-margins"
                                                                            href="{{ $value['main_image'] ?? asset('assets/images/no-img.png') }}"
                                                                            itemprop="contentUrl" data-size="800x800">
                                                                            <img class="img-thumbnail custom-img-responsive"
                                                                                alt="{{ ucfirst($value['ing_name']) }}"
                                                                                src="{{ $value['main_image'] ?? asset('assets/images/no-img.png') }}"
                                                                                width="50" height="50"
                                                                                itemprop="thumbnail">
                                                                        </a>
                                                                    </figure>
                                                                </div>
                                                            </td>
                                                            <td>{{ $value['ing_name'] }}
                                                                ({{ $value->unit ? $value->unit->name : '' }})</td>
                                                            <td>{{ $availableQty }}</td>
                                                            <td>
                                                                @if ($isLowStock)
                                                                    <span class="badge bg-danger">Low Stock</span>
                                                                @else
                                                                    <span class="badge bg-success">In Stock</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ isset($value->branchQuantities[0]) ? $value->branchQuantities[0]->updater->first_name : '' }}
                                                            </td>
                                                            <td>{{ isset($value->branchQuantities[0]) ? $value->branchQuantities[0]->updated_at->diffForHumans() : $value['updated_at']->diffForHumans() }}
                                                            </td>
                                                            <td class="d-flex align-items-center">
                                                                @if ($value['ing_type'] == 'custom')
                                                                    <a href="#" id="update-branch-ingredient"
                                                                        class="btn btn-primary mt-3 mb-3"
                                                                        data-branchId="{{ $branch->id }}"
                                                                        data-id="{{ $value['ing_id'] }}">
                                                                        <i class="mdi mdi-upload-outline"></i>
                                                                        <b>Update</b>
                                                                    </a>
                                                                @else
                                                                    <input type="number"
                                                                        class="form-control form-control-sm me-2"
                                                                        name="quantity" placeholder="Qty" min="0"
                                                                        step="any" style="width:100px"
                                                                        id="quantity-{{ $value['ing_id'] }}">
                                                                    <button type="button" class="btn btn-dark btn-sm"
                                                                        id="ingredientFormqty"
                                                                        data-branchId="{{ $branch->id }}"
                                                                        data-ingId="{{ $value['ing_id'] }}">
                                                                        Update
                                                                    </button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Shifts Tab -->

                        <div class="tab-pane fade" id="shifts" role="tabpanel">
                            <div class="mt-3">
                                <div class="card-header d-flex justify-content-between align-items-center"
                                    style="padding: 0px">
                                    <h5 style="display: inline">Shifts</h5>
                                    <a href="#" class="btn btn-primary btn-sm" id="addBranchShift"
                                        data-branchid="{{ $branch->id }}" style="float: right">Add</a>
                                </div>

                                <div class="card-body">
                                    <div class="table-responsive">
                                        <div id="shift-table-wrapper" class="dataTables_wrapper">
                                            <table class="table hover dataTable" id="shift-table" role="grid">
                                                <thead style="background-color:white; color:black;">
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
                                                            <td>{{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }}
                                                            </td>
                                                            <td>{{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}
                                                            </td>
                                                            <td>
                                                                <button class="btn btn-sm btn-outline-info"
                                                                    id="editBranchShift" data-id="{{ $shift->id }}">
                                                                    <i class="fa fa-edit"></i>
                                                                </button>

                                                                <button class="btn btn-sm btn-outline-danger delete-btn"
                                                                    data-action="{{ route('admin.shifts.destroy', ['id' => $shift->id]) }}"
                                                                    data-id="">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
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


                        <!-- Staff Tab -->
                        <div class="tab-pane fade" id="staff" role="tabpanel">
                            <div class="mt-3">
                                <div class="card-header" style="padding: 0px">
                                    <h5 style="display: inline">Staff</h5>
                                    <a href="#" class="btn btn-primary btn-sm" id="addBranchStaff"
                                        data-branchid="{{ $branch->id }}" style="float: right">Add</a>
                                </div>

                                <div class="card-body">
                                    <div class="table-responsive">
                                        <div id="staff-table-wrapper" class="dataTables_wrapper">
                                            <table class="table hover dataTable" id="staff-table" role="grid">
                                                <thead style="background-color:white; color:black;">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>First Name</th>
                                                        <th>Last Name</th>
                                                        <th>Phone</th>
                                                        <th>Email</th>
                                                        <th>Shift</th>
                                                        <th>Role</th>
                                                        <th>Emp.ID</th>
                                                        <th>Status</th>
                                                        {{-- <th>Last Updated</th> --}}
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($staffs as $index => $getstaff)
                                                        @php $user = $getstaff->user; @endphp
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ $user->first_name }}</td>
                                                            <td>{{ $user->last_name }}</td>
                                                            <td>{{ $user->phone }}</td>
                                                            <td>{{ $user->email }}</td>
                                                            <td>
                                                                @if ($user->shift && count($user->shift) > 0)
                                                                    {{ $user->shift[0]->name }}
                                                                @else
                                                                    N/A
                                                                @endif
                                                            </td>
                                                            <td>
                                                              {{ strtoupper($user->role->name) }}
                                                            </td>
                                                            <td>{{ $user->employee_id }}</td>
                                                            <td>
                                                                <x-status-toggle :id="$user->id" :status="$user->user_status"
                                                                    :url="route('admin.toggleStatus')" />
                                                            </td>
                                                            {{-- <td>{{ $user->updated_at->diffForHumans() }}</td> --}}
                                                            <td>
                                                                <button class="btn btn-sm btn-outline-info"
                                                                    id="editdBranchStaff" data-id="{{ $user->id }}"
                                                                    data-branchid="{{ $branch->id }}">
                                                                    <i class="fa fa-edit"></i>
                                                                </button>

                                                                <button class="btn btn-sm btn-outline-danger delete-btn"
                                                                    data-action="{{ route('admin.staff.delete', $user->id) }}"
                                                                    data-id="" data-branchid="">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
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
            </div>

            <!-- Right Column - Map -->
            <div class="col-md-3">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-header bg-white fw-bold">
                        <i class="fa fa-map me-2 text-primary"></i> Location
                    </div>
                    <div class="card-body p-2">
                        <input type="hidden" id="longitude_location" value="{{ $branch->long }}" />
                        <input type="hidden" id="latitude_location" value="{{ $branch->lat }}" />
                        <div id="map_display"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=marker,places"
        async defer></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#branchAdminsTable').DataTable({
                pageLength: 5,
                order: [
                    [0, 'asc']
                ],
            });
            $('#branchOrdersTable').DataTable({
                destroy: true,
                order: [
                    [0, 'desc']
                ],
            });
            $('#inventory-table').DataTable({
                pageLength: 10,
                order: [
                    [0, 'asc']
                ],
            });
            $('#shift-table').DataTable({
                pageLength: 5,
                order: [
                    [0, 'asc']
                ],
            });
            $('#staff-table').DataTable({
                pageLength: 5,
                order: [
                    [0, 'asc']
                ],
            });
            // Google Map
            const longitude = parseFloat($('#longitude_location').val());
            const latitude = parseFloat($('#latitude_location').val());
            setTimeout(() => initializeMap(longitude, latitude, "map_display"), 1000);
        });
    </script>
@endpush
