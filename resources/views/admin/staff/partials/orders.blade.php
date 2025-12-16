<!DOCTYPE html>
<html lang="en">

<head>
 @include('admin.staff.partials.layouts.header')
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f6fa;
        }

        .page-header {
            background: linear-gradient(90deg,#000, #d6dddf);;
            color: #fff;
            padding: 14px 25px;
            font-weight: 600;
            font-size: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .filter-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            padding: 15px 20px;
            margin-top: 20px;
        }

        .filter-card .form-control,
        .filter-card .form-select {
            font-size: 14px;
            border-radius: 8px;
        }

        .status-tabs .btn {
            border-radius: 20px;
            font-size: 13px;
            margin-right: 8px;
            padding: 5px 15px;
        }

        .orders-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.06);
            padding: 10px 15px;
        }

        table th {
            background: #f9fafb;
            font-weight: 600;
            font-size: 13px;
            color: #555;
            padding: 12px 10px;
        }

        table td {
            font-size: 14px;
            padding: 14px 10px;
            vertical-align: middle;
        }

        .status-badge {
            font-size: 12px;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 500;
        }

        .status-accepted {
            background: #28a745;
            color: #fff;
        }

        .status-cancelled {
            background: #dc3545;
            color: #fff;
        }
        .status-completed {
            background: #007bff;
            color: #fff;
        }

        .status-paid {
            background: #20c997;
            color: #fff;
        }

        .btn-action {
            padding: 6px 14px;
            font-size: 13px;
            border-radius: 6px;
            margin: 2px;
        }

        .table-custom {
            table-layout: fixed;
            /* sab columns fixed honge */
            width: 100%;
            border-collapse: collapse;
        }

        .table-custom th,
        .table-custom td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            white-space: nowrap;
            /* line break na ho */
            overflow: hidden;
            text-overflow: ellipsis;
            /* ... dikhaye */
        }

        /* Date column ko fixed width do */
        .table-custom th.date-col,
        .table-custom td.date-col {
            width: 160px;
            /* aap chaho to 140px/180px bhi kar sakte ho */
            text-align: center;
        }

        .fa-sync-alt.fa-spin {
            transition: transform 0.3s ease-in-out;
        }


        .status-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 600;
    color: #fff;
    text-transform: capitalize;
}

        /* Status-specific colors */
        .status-pending {
            background-color: #f6c23e; /* yellow */
        }

        .status-accepted {
            background-color: #4e73df; /* blue */
        }

        .status-processing {
            background-color: #36b9cc; /* cyan */
        }

        .status-preparing {
            background-color: #858796; /* gray */
        }

        .status-dispatched {
            background-color: #1cc88a; /* greenish */
        }

        .status-ready {
            background-color: #20c997; /* teal green */
        }

        .status-completed {
            background-color: #198754; /* success green */
        }

        .status-cancelled {
            background-color: #e74a3b; /* red */
        }

        .status-other {
            background-color: #6c757d; /* neutral gray */
        }

    </style>
</head>

<body>

    <!-- Header -->
    @include('admin.staff.partials.top-nev')
    <div class="page-header">
        <span><i class="fas fa-bell me-2"></i> Live Orders</span>
        <span class="badge bg-success"><i class="fas fa-wifi me-1"></i> Online</span>
    </div>

    <div class="" style="padding: 10px;">

        <!-- Filters -->
        <div class="filter-card">
            <form id="orderFilterForm" class="row g-2 mb-3">
                {{-- <div class="col-md-2">
                    <select class="form-select" name="branch_id">
                        <option value="">All Branches</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div> --}}

                <div class="col-md-2"><input type="text" class="form-control" name="ref_no"
                        placeholder="Search by Ref#"  value="{{ request('ref_no') }}"></div>
                {{-- <div class="col-md-2"><input type="text" class="form-control" name="customer_ref"
                        placeholder="Search by Customer Ref"></div> --}}
                <div class="col-md-2"><input type="text" value="{{ request('phone') }}" class="form-control" name="phone"
                        placeholder="Search by Phone"></div>
                <div class="col-md-2"><input type="text" value="{{ request('name') }}" class="form-control" name="name"
                        placeholder="Search by Name"></div>
                <div class="col-md-1">
                    <select class="form-select" name="status">
                        <option value="">All Orders ({{ $statusCounts['all'] ?? 0 }})</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                            New Orders ({{ $statusCounts['new'] ?? 0 }})
                        </option>
                        <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>
                            Processing ({{ $statusCounts['processing'] ?? 0 }})
                        </option>
                        <option value="preparing" {{ request('status') == 'preparing' ? 'selected' : '' }}>
                            Preparing ({{ $statusCounts['preparing'] ?? 0 }})
                        </option>
                        <option value="dispatched" {{ request('status') == 'dispatched' ? 'selected' : '' }}>
                            Dispatched ({{ $statusCounts['dispatched'] ?? 0 }})
                        </option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                            Completed ({{ $statusCounts['completed'] ?? 0 }})
                        </option>
                    </select>
                </div>


                <div class="col-md-1">
                    <select class="form-select" name="payment_status">
                        <option value="">Payment</option>
                        <option value="cash" {{ request('payment_status') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="card" {{ request('payment_status') == 'card' ? 'selected' : '' }}>Card</option>
                        <option value="wallet" {{ request('payment_status') == 'wallet' ? 'selected' : '' }}>Wallet</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </form>


            <div class="mt-3 d-flex justify-content-end">
                <button id="reloadBtn" class="btn btn-primary btn-sm me-2">
                    <i id="reloadIcon" class="fas fa-sync-alt"></i> Reload
                </button>
                <button class="btn btn-success btn-sm">
                    <i class="fas fa-file-export"></i> Export
                </button>
            </div>
        </div>

        <!-- Status Tabs -->
        <div class="mt-3 mb-2 status-tabs">
            <a href="{{ route('pos.orders', ['status' => 'pending']) }}"
                class="btn btn-sm {{ request('status') == 'pending' ? 'btn-primary' : 'btn-outline-primary' }}">
                New Orders <span class="countnum">({{ $statusCounts['new'] ?? 0 }}) </span>
            </a>

            <a href="{{ route('pos.orders', ['status' => 'processing']) }}"
                class="btn btn-sm {{ request('status') == 'processing' ? 'btn-warning text-white' : 'btn-outline-warning' }}">
                Processing ({{ $statusCounts['processing'] ?? 0 }})
            </a>

            <a href="{{ route('pos.orders', ['status' => 'preparing']) }}"
                class="btn btn-sm {{ request('status') == 'preparing' ? 'btn-info text-white' : 'btn-outline-info' }}">
                Preparing ({{ $statusCounts['preparing'] ?? 0 }})
            </a>

            <a href="{{ route('pos.orders', ['status' => 'dispatched']) }}"
                class="btn btn-sm {{ request('status') == 'dispatched' ? 'btn-info text-white' : 'btn-outline-info' }}">
                Dispatched ({{ $statusCounts['dispatched'] ?? 0 }})
            </a>

            <a href="{{ route('pos.orders', ['status' => 'completed']) }}"
                class="btn btn-sm {{ request('status') == 'completed' ? 'btn-success text-white' : 'btn-outline-success' }}">
                Completed ({{ $statusCounts['completed'] ?? 0 }})
            </a>

            <a href="{{ route('pos.orders', ['status' => 'all']) }}"
                class="btn btn-sm {{ request('status') == 'all' || request('status') == null ? 'btn-primary' : 'btn-outline-primary' }}">
                All Orders ({{ $statusCounts['all'] ?? 0 }})
            </a>
        </div>

        <!-- Orders Table -->
        <div class="orders-card">
            <div class="table-responsive">
                <table class="table align-middle table-hover" id="ordersTable">
                    <thead>
                        <tr>
                            <th><input type="checkbox"></th>
                            <th>Ref#</th>
                            <th>Branch</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Voucher</th>
                            <th>Order Type</th>
                            <th>Payment</th>
                            <th>Total</th>
                            <th>Tax</th>
                            <th>Status</th>
                            <th>Platform</th>
                            <th>Date</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @include('admin.staff.partials.order-list')
                    </tbody>
                </table>
            </div>
             <div class="d-flex justify-content-center mt-3">
        {!! $orders->links('pagination::bootstrap-5') !!}
    </div>
        </div>

    </div>

    @include('admin.staff.partials.layouts.script')
<!-- Cancel Reason Modal -->
<div class="modal fade" id="cancelReasonModal" tabindex="-1" aria-labelledby="cancelReasonModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <form id="cancelReasonForm">
            @csrf
            <input type="hidden" id="cancel-order-id">
            <input type="hidden" id="cancel-order-url">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cancel Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="cancel-reason">Reason for cancellation</label>
                        <textarea id="cancel-reason" class="form-control" required></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <!-- Important: use type="button" to prevent default submit -->
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger" id="confirmCancelBtn">Cancel Order</button>
                </div>
            </div>
        </form>
    </div>
</div>


</body>
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script> --}}






</html>
