@extends('admin.layouts.master')
@section('title', 'Stations')

@section('css')
    <style>
        .page-header-actions {
            display: flex;
            gap: 8px;
            text-align: center;
            justify-content: center;
        }

        .card-header h5 {
            margin: 0;
        }

        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .btn-icon {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .search-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .search-row .form-control {
            max-width: 320px;
        }

        .listing-header {
            align-items: center;
            justify-content: space-between;
            margin: 18px 0 12px;
            padding-top: 10px;
            border-top: 1px solid #eef0f4;
            font-size: 16px;
            text-align: center;
        }

        .listing-title {
            font-size: 14px;
            font-weight: 700;
            color: #475467;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        /* Cards grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 16px;
        }

        .card-station {
            grid-column: span 12;
            position: relative;
            border: 1px solid #eef0f4;
            border-radius: 16px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 6px 14px rgba(16, 24, 40, .10);
            min-height: 240px;
            display: flex;
            flex-direction: column;
        }

        .card-station .top {
            height: 50px;
            background: linear-gradient(135deg, #fce3ff 0%, #e6f0ff 100%);
        }

        .card-station .actions {
            position: absolute;
            top: 8px;
            right: 10px;
            display: flex;
            gap: 8px;
        }

        .card-station .actions .btn {
            padding: 6px 8px;
            border-radius: 8px;
        }

        .card-station .body {
            padding: 35px 25px 30px;
            flex: 1;
        }

        .station-name {
            font-weight: 800;
            font-size: 19px;
            margin: 0 0 10px;
            letter-spacing: .2px;
        }

        .meta {
            color: #667085;
            font-size: 14px;
            margin-bottom: 12px;
        }

        .categories-label {
            font-size: 13px;
            font-weight: 600;
            color: #475467;
            margin-bottom: 6px;
            display: block;
        }

        .chips {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .chip {
            background: #f2f4f7;
            color: #344054;
            border-radius: 999px;
            padding: 7px 14px;
            font-size: 12px;
        }

        .empty-state {
            padding: 32px 0;
            color: #98a2b3;
            text-align: center;
        }

        @media (min-width: 576px) {
            .card-station {
                grid-column: span 6;
            }
        }

        @media (min-width: 992px) {
            .card-station {
                grid-column: span 4;
            }
        }

        @media (min-width: 1400px) {
            .card-station {
                grid-column: span 3;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="mb-3">Stations</h3>
                <div class="card">
                    <div class="card-header ">
                        <div class="page-header-actions">
                            <button type="button" class="btn btn-primary btn-icon" id="btn-create-station">
                                <i class="fa fa-plus"></i>
                                <span>Create Station</span>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="search-row">
                            <input type="text" class="form-control" id="searchByName"
                                placeholder="Search stations by name, IP or category...">
                            <button class="btn btn-light" id="btnClearFilters">Clear</button>
                        </div>
                        <div class="listing-header">
                            <span class="listing-title">Listing</span>
                        </div>

                        <div class="cards-grid" id="stationsGrid"></div>
                        <div class="empty-state d-none" id="emptyState">No stations found.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            const stations = @json($data['formatted']);

            const $grid = $('#stationsGrid');
            const $search = $('#searchByName');
            const $empty = $('#emptyState');

            $(document).on('shown.bs.modal', '#branchuserModal', function() {
                if ($.fn.select2) {
                    $('#categories').select2({
                        dropdownParent: $('#branchuserModal'),
                        placeholder: 'Select categories',
                        width: '100%'
                    });
                }
            });

            function render(items) {
                $grid.empty();
                if (items.length === 0) {
                    $empty.removeClass('d-none');
                    return;
                }
                $empty.addClass('d-none');

                $.each(items, function(_, s) {
                    let chips = s.categories.map(c => `<span class="chip">${c}</span>`).join('');
                    let card = `
                            <div class="card-station">
                                <div class="top"></div>
                                <div class="actions">
                                    <button id="edit-station"
                                            class="btn btn-light btn-sm"
                                            title="Edit"
                                            data-id="${s.id}">
                                        <i class="fa fa-pen"></i>
                                    </button>
                                    <a href="javascript:void(0);"
                                    class="btn btn-light btn-sm text-danger delete-btn"
                                    data-id="${s.id}"
                                    data-action="{{ route('badmin.station.delete', '') }}/${s.id}">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </div>
                                <div class="body">
                                    <h6 class="station-name">${s.name}</h6>
                                    <div class="meta">IP Address: <strong>${s.ip}</strong></div>
                                    <span class="categories-label">Categories:</span>
                                    <div class="chips">${chips}</div>
                                </div>
                            </div>`;


                    $grid.append(card);
                });
            }

            function applyFilter() {
                const q = ($search.val() || '').toLowerCase();
                const filtered = stations.filter(function(s) {
                    return !q ||
                        s.name.toLowerCase().includes(q) ||
                        s.ip.toLowerCase().includes(q) ||
                        s.categories.join(' ').toLowerCase().includes(q);
                });
                render(filtered);
            }

            $search.on('input keyup change', applyFilter);
            $('#btnClearFilters').on('click', function() {
                $search.val('');
                applyFilter();
            });

            $('#btn-create-station').on('click', function() {
                $('#branchuserModal').modal('show');
            });

            render(stations);
        });
     $(document).on('click', '.delete-btn', function() {
    const encryptedId = $(this).data('id');
    let action = $(this).data('action');

    if (encryptedId) {
        action = action.replace(':id', encryptedId);
    }

        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Create and submit a form dynamically
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = action;

                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';

                form.appendChild(csrf);
                document.body.appendChild(form);
                form.submit();
            }
        });

});
    </script>
@endsection
