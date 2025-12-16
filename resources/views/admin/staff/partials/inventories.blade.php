<!DOCTYPE html>
<html lang="en">

<head>
    @include('admin.staff.partials.layouts.header')

    {{-- ✅ Add DataTables CSS --}}
 
    {{-- <link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/bootstrap.css')}}"> --}}
    {{-- @include('admin.layouts.css') --}}
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

        .btn-action {
            padding: 6px 14px;
            font-size: 13px;
            border-radius: 6px;
            margin: 2px;
        }
    </style>
</head>

<body>

    @include('admin.staff.partials.top-nev')

    <div class="page-header">
        <span><i class="fas fa-boxes me-1"></i> Inventories</span>
        <span class="badge bg-success"><i class="fas fa-wifi me-1"></i> Online</span>
    </div>

    <div class="p-3">

        <div class="filter-card">

            <div class="orders-card">
                <div class="table-responsive">
                    <table class="table align-middle table-hover" id="inventoriesTable">
                        <thead>
                            <tr>
                                <th>Sr. No.</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Unit</th>
                                <th>Available Quantity</th>
                                <th>Status</th>
                                <th>Updated By</th>
                                <th>Last Updated</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @include('admin.staff.partials.inventory-list')
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
      <div id="addtranslationModal"></div>

    @include('admin.staff.partials.layouts.script')
    @include('admin.include.badminscript')
    {{--  DataTables JS --}}

    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#inventoriesTable').DataTable({
                pageLength: 10,
                lengthChange: true,
                ordering: true,
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search inventory..."
                },
                columnDefs: [
                    { orderable: false, targets: [1, 7] } // Disable sort on Image & Action
                ]
            });
        });
    </script>

</body>
</html>
