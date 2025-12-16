<!DOCTYPE html>
<html lang="en">

<head>
    @include('admin.staff.partials.layouts.header')

    {{--  DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <!--  Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f6fa;
        }

        .page-header {
            background: linear-gradient(90deg, #000, #d6dddf);
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

        .btn-action {
            padding: 6px 14px;
            font-size: 13px;
            border-radius: 6px;
            margin: 2px;
        }

        .section-title {
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 8px;
            color: #333;
        }

        .divider {
            border-bottom: 1px solid #eaeaea;
            margin: 15px 0;
        }
    </style>
</head>

<body>

    @include('admin.staff.partials.top-nev')

    <div class="page-header">
        <span><i class="fas fa-money-check-alt me-1"></i> Cashout & Refund</span>
        <span class="badge bg-success"><i class="fas fa-wifi me-1"></i> Online</span>
    </div>

    <div class="p-3">
        <div class="filter-card">
            <div class="row g-4">
                   {{-- RIGHT SIDE: FORM --}}
                <div class="col-lg-5">
                    <div class="orders-card">
                        <h6 class="fw-bold mb-3">Add Cashout / Refund</h6>

                        {{-- Checkboxes --}}
                        <div class="mb-3">
                            <input type="checkbox" id="cashoutCheck" class="me-2"> Cashout
                            <input type="checkbox" id="refundCheck" class="ms-3 me-2"> Refund
                        </div>

                        {{-- CASHOUT --}}
                        <div id="cashoutSection" style="display:none;">
                            <label class="section-title">Cashout Category</label>
                            <select class="form-select mb-3" id="cashoutCategory">
                                <option value="">-- Select --</option>
                                <option value="ingredient">Ingredients</option>
                                <option value="other">Others</option>
                            </select>

                        {{-- Ingredient Selection --}}
                            <div id="ingredientSection" style="display:none;">
                                <label class="form-label">Select Ingredients</label>
                                <select class="form-select mb-2" id="ingredientList" multiple>
                                    @foreach($ingredients as $ingredient)
                                        <option value="{{ $ingredient->ing_id }}">
                                            {{ $ingredient->ing_name }} ({{ $ingredient->unit?->name ?? '' }})
                                        </option>
                                    @endforeach
                                </select>

                                <label class="form-label">Price</label>
                                <input type="number" class="form-control mb-3" id="ing-price" placeholder="Enter amount">
                            </div>
                             {{-- Other Items --}}
                            <div id="otherSection" style="display:none;">
                                <label class="form-label">Item Name</label>
                                <input type="text" class="form-control mb-2" id="otherItem" placeholder="Enter item name">
                                <label class="form-label">Price</label>
                                <input type="number" class="form-control mb-3" id="otherPrice" placeholder="Enter amount">
                            </div>

                            <button class="btn btn-primary w-100" id="submitCashout">Submit Cashout</button>
                        </div>

                        {{-- REFUND --}}
                        <div id="refundSection" style="display:none;">
                            <label class="section-title">Order Refund</label>
                            <div class="d-flex mb-3">
                                <input type="text" class="form-control me-2" id="refundOrderId" placeholder="Enter Order Ref">
                                <button class="btn btn-secondary" id="searchOrder">Search</button>
                            </div>

                            <label class="form-label">Refund Amount</label>
                            <input type="text" class="form-control mb-3" id="refundAmount" readonly>

                            <label class="form-label">Remarks</label>
                            <textarea class="form-control mb-3" id="refundRemarks" rows="2" placeholder="Enter remarks"></textarea>

                            <button class="btn btn-danger w-100" id="submitRefund">Submit Refund</button>
                        </div>
                    </div>
                </div>
                {{-- LEFT SIDE: TABLE --}}
                <div class="col-lg-7">
                    <div class="orders-card">
                        <h6 class="fw-bold mb-3">Transaction History</h6>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="transactionsTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Type</th>
                                        <th>Item / Order</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse($cashoutTransactions as $index => $tx)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <span class="badge {{ $tx->type === 'refund' ? 'bg-info' : 'bg-warning' }}">
                                                {{ ucfirst($tx->type) }}
                                            </span>
                                        </td>
                                       <td>
                                            @if($tx->category === 'ingredient')
                                                {{ $tx->ingredient_names ?? '-' }}
                                            @elseif($tx->category === 'refund')
                                                Order #{{ $tx->order_ref ?? '-' }}
                                            @else
                                                {{ $tx->item_name ?? '-' }}
                                            @endif
                                        </td>
                                        <td>Rs {{ number_format($tx->amount, 2) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($tx->created_at)->format('d M Y, h:i A') }}</td>
                                    </tr>
                                @empty

                                @endforelse
                            </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--  Modal container --}}
    <div id="addtranslationModal"></div>

    @include('admin.staff.partials.layouts.script')
    @include('admin.include.badminscript')

    {{--  DataTables --}}
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <!--  Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
                  $('#ingredientList').select2({
                placeholder: "Search & select ingredients...",
                width: '100%',
                allowClear: true
            });

        $('#transactionsTable').DataTable({
                pageLength: 8,
                ordering: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search transaction..."
                }
            });
        });
    </script>

</body>
</html>
