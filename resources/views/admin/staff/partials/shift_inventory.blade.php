<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f6fa;
        }

        .page-header {
            background: linear-gradient(90deg, #5a3eff, #00c6ff);
            color: #fff;
            padding: 14px 25px;
            font-weight: 600;
            font-size: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .card-header {
            font-weight: 600;
            font-size: 15px;
            padding: 12px 16px;
        }

        .card-body {
            padding: 16px;
            background-color: #fff;
        }

        .table th {
            background: #f9fafb;
            font-weight: 600;
            font-size: 13px;
            color: #555;
            padding: 10px;
        }

        .table td {
            font-size: 13px;
            padding: 10px;
            vertical-align: middle;
        }

        .form-control {
            font-size: 13px;
            border-radius: 6px;
        }

        .fw-semibold {
            font-weight: 500;
        }

        .btn-dark {
            background: linear-gradient(90deg, #5a3eff, #00c6ff);
            border: none;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .btn-dark:hover {
            background: linear-gradient(90deg, #5139f8, #00b8f5);
        }

        .badge.bg-info {
            background-color: #00c6ff !important;
            color: #fff;
            font-size: 12px;
        }

        .text-primary {
            color: #5a3eff !important;
        }

        .fw-bold {
            font-weight: 600 !important;
        }

        .table-bordered td,
        .table-bordered th {
            border: 1px solid #ddd;
        }

        .container-fluid {
            padding: 30px 50px;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <div class="page-header">
        <span><i class="fa-solid fa-boxes-stacked me-2"></i> Shift Inventory</span>
        <span>{{ now()->format('d M, Y') }}</span>
    </div>

    <div class="container-fluid py-5">

        <div class="row g-4">

            <!-- ================= CASH NOTES ================= -->
            <div class="col-lg-6">
                <div class="card h-100 border-0">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fa-solid fa-money-bill-wave me-2"></i> Enter Cash Notes</h6>
                    </div>
                    <div class="card-body">
                        <form id="cashForm">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle mb-0" id="cashTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Note</th>
                                            <th>Quantity</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ([10, 50, 100, 500, 1000, 5000] as $note)
                                            <tr>
                                                <td class="fw-semibold">{{ $note }} PKR</td>
                                                <td>
                                                    <input type="number" class="form-control cash-input"
                                                        data-note="{{ $note }}"
                                                        name="cash_notes[{{ $note }}]" min="0"
                                                        placeholder="Enter quantity">
                                                </td>
                                                <td class="subtotal fw-bold text-success">0</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" class="text-end fw-bold">Total</td>
                                            <td id="grandTotal" class="fw-bold text-primary">0</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ================= INGREDIENTS ================= -->
            <div class="col-lg-6">
                <div class="card h-100 border-0">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="fa-solid fa-carrot me-2"></i> Enter Ingredients</h6>
                    </div>
                    <div class="card-body">
                        <form id="ingredientForm">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Ingredient</th>
                                            <th width="30%">Quantity</th>
                                            <th width="20%">Unit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ingredients as $ingredient)
                                            <tr>
                                                <td class="fw-semibold">{{ $ingredient->ing_name }}</td>
                                                <td>
                                                    <input type="hidden" value="{{ $ingredient->ing_id }}" name="ing_id[]">
                                                    <input type="number" class="form-control form-control-sm"
                                                        name="ingredients[{{ $ingredient->ing_id }}]"
                                                        placeholder="Enter qty" min="0" step="any">
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-info">{{ $ingredient->unit?->name ?? '-' }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        <!-- ================= OPEN SHIFT BUTTON ================= -->
        <div class="text-center mt-5">
             @if(auth()->user()->shiftusers()->where('status', 'open')->exists())
              <a class="btn btn-dark btn-lg px-5"  data-action="closing" href="{{ url()->previous() }}">
                    <i class="fa-solid fa-arrow-left me-2"></i>GO back
                </a>
                <button class="btn btn-danger btn-lg px-5" id="openShiftBtn" data-action="closing">
                    <i class="fa-solid fa-lock me-2"></i> Shift Close
                </button>

            @else
                <button class="btn btn-dark btn-lg px-5" id="openShiftBtn" data-action="opening">
                    <i class="fa-solid fa-unlock me-2"></i> Shift Start
                </button>
            @endif
        </div>

    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).on('input', '.cash-input', function() {
            let row = $(this).closest('tr');
            let noteValue = parseInt($(this).data('note'));
            let qty = parseInt($(this).val()) || 0;

            // Row subtotal
            let subtotal = noteValue * qty;
            row.find('.subtotal').text(subtotal.toLocaleString());

            // Calculate grand total
            let total = 0;
            $('#cashTable .subtotal').each(function() {
                total += parseInt($(this).text().replace(/,/g, '')) || 0;
            });
            console.log(total.toLocaleString());

            $('#grandTotal').text(total.toLocaleString());
        });
        $('#openShiftBtn').on('click', function(e) {
            e.preventDefault();

            if (confirm('Are you sure you want to start the shift with entered Cash & Ingredients?')) {
                let combinedData = new FormData();

                // Cash form data
                $('#cashForm').serializeArray().forEach(function(field) {
                    combinedData.append(field.name, field.value);
                });

                // Ingredient form data
                $('#ingredientForm').serializeArray().forEach(function(field) {
                    combinedData.append(field.name, field.value);
                });

                // Console log to check combined data
                for (let [key, value] of combinedData.entries()) {
                    //   console.log(key, value);
                }
                let action = combinedData.append('action', $(this).data('action'));



                // Example AJAX request (if needed later)

                $.ajax({
                    url: '{{ route('shift.inventory.store') }}',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: 'POST',
                    data: combinedData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        let actionType = combinedData.get('action');
                        let message = actionType === 'opening'
                            ? 'Shift started successfully!'
                            : 'Shift closed successfully!';
                        toastr.success(message, 'Success', {
                            positionClass: 'toast-top-right',
                            timeOut: 2000,
                            progressBar: true,
                            closeButton: true
                        });

                        // Redirect after 2 seconds
                        setTimeout(() => {
                            if (combinedData.get('action') == "opening") {
                                window.location.href = "{{ route('pos.index') }}";
                            } else if (combinedData.get('action') == "closing") {
                                window.location.href = "{{ route('pos.logout') }}";
                            }
                        }, 2000);
                    },
                    error: function(xhr) {
                        toastr.error('Something went wrong, please try again.', 'Error', {
                            positionClass: 'toast-top-right',
                            timeOut: 3000,
                            progressBar: true,
                            closeButton: true
                        });
                    }
                });



            }
        });
    </script>

</body>

</html>
