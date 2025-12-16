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

        .modal-body dt {
            font-size: 0.92rem;
        }

        .modal-body dd {
            font-size: 0.96rem;
        }

        .form-control::placeholder {
            color: #adb5bd;
        }
    </style>


@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5 style="display: inline">Ingredients</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div id="example-style-4_wrapper" class="dataTables_wrapper">
                                {{-- {{ dump($ingredient)  }} --}}
                                <table class="hover dataTable" id="example-style-4" role="grid"
                                    aria-describedby="example-style-4_info">
                                    <thead>
                                        <tr>
                                            <th style="max-width:50px">Sr. No.</th>
                                            <th style="max-width:100px">Image</th>
                                            <th style="max-width:100px">Name</th>
                                            <th style="max-width:100px">Available Quantity</th>
                                            <th style="max-width:100px">Update By</th>
                                            <th style="max-width:100px">Last Updated</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!empty($ingredient))

                                            <?php $serial = 1; ?>
                                            @foreach ($ingredient as $value)
                                                <tr>
                                                    <td>{{ $serial++ }}</td>
                                                    <td>

                                                        <div class="gallery my-gallery" itemscope="">
                                                            <figure class="col-xl-3 col-md-4 col-6 custom-image-container"
                                                                itemprop="associatedMedia" itemscope>
                                                                <a class="image-popup-no-margins"
                                                                    href="{{ $value['main_image'] }}" itemprop="contentUrl"
                                                                    data-size="800x800">
                                                                    <img class="img-thumbnail custom-img-responsive"
                                                                        alt="{{ ucfirst($value['ing_name']) }}"
                                                                        src="{{ $value['main_image'] }}" width="50"
                                                                        height="50" itemprop="thumbnail">
                                                                </a>
                                                                <figcaption itemprop="caption description">
                                                                    {{ ucfirst($value['ing_name']) }}</figcaption>
                                                            </figure>

                                                        </div>


                                                    </td>
                                                    <td>{{ $value['ing_name'] }}
                                                        ({{ $value->unit ? $value->unit->name : '' }})
                                                    </td>
                                                    <td>
                                                        @php
                                                            $availableQty = isset($value->quantity_balance)
                                                                ? $value->quantity_balance
                                                                : 0;
                                                            $minQty = $value->min_quantity ?? 0;
                                                        @endphp

                                                        {{ $availableQty }}

                                                        @if ($availableQty <= $minQty)
                                                            <div class="text-danger fw-semibold small mt-1">
                                                                Low Stock (Min: {{ $minQty }})
                                                            </div>
                                                        @endif
                                                    </td>

                                                    <td>{{ isset($value->branchQuantities[0]) ? $value->branchQuantities[0]->updater->first_name : '' }}
                                                    </td>
                                                    <td>{{ isset($value->branchQuantities[0]) ? $value->branchQuantities[0]->updated_at->diffForHumans() : $value['updated_at']->diffForHumans() }}
                                                    </td>

                                                    <td class="d-flex align-items-center">
                                                        <!-- Quantity Input -->
                                                        @if ($value['ing_type'] == 'custom')
                                                            <a href="#" id="update-branch-ingredient"
                                                                class="btn btn-primary mt-3 mb-3"
                                                                data-branchId="{{ $BranchId }}"
                                                                data-id="{{ $value['ing_id'] }}">
                                                                <i class="mdi mdi-upload-outline"></i> <b> Add custom ingredient</b>
                                                            </a>
                                                        @else
                                                            <input type="number" class="form-control form-control-sm me-2"
                                                                name="quantity" placeholder="Qty" min="0"
                                                                step="any" style="width:100px"
                                                                id="quantity-{{ $value['ing_id'] }}">
                                                            <button type="button" class="btn btn-dark btn-sm"
                                                                id="ingredientFormqty" data-branchId="{{ $BranchId }}"
                                                                data-ingId="{{ $value['ing_id'] }}">
                                                                Update
                                                            </button>
                                                        @endif
                                                    </td>

                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th style="max-width:50px">Sr. No.</th>
                                            <th style="max-width:100px">Image</th>
                                            <th style="max-width:100px">Name</th>
                                            <th style="max-width:100px">Available Quantity</th>
                                            <th style="max-width:100px">Last Updated</th>
                                            <th>Action</th>

                                        </tr>
                                    </tfoot>
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
