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

        .action-col {
            width: 25% !important;
            min-width: 200px;
            /* optional, chhoti screens ke liye */
            text-align: center;
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
                        <a href="#" class="btn btn-primary" id="add-ingredients" style="float: right">Add</a>

                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="ingredientTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="standard-tab" data-bs-toggle="tab"
                                    data-bs-target="#standard" type="button" role="tab" aria-controls="standard"
                                    aria-selected="true">
                                    Standard Ingredients
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="custom-tab" data-bs-toggle="tab" data-bs-target="#custom"
                                    type="button" role="tab" aria-controls="custom" aria-selected="false">
                                    Custom Ingredients
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content mt-3" id="ingredientTabsContent">

                            {{-- Standard Ingredients --}}
                            <div class="tab-pane fade show active" id="standard" role="tabpanel"
                                aria-labelledby="standard-tab">
                                <div class="table-responsive">
                                    <table class="hover dataTable" id="standard-table">
                                        <thead>
                                            <tr>
                                                <th>Sr. No.</th>
                                                <th>Image</th>
                                                <th>Name</th>
                                                <th>Category</th>
                                                <th>Unit</th>
                                                <th>Unit Price</th>
                                                <th>Status</th>
                                                <th>Last Updated</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $serial = 1; @endphp
                                            @foreach ($ingredient->where('ing_type', 'standard') as $value)
                                                <tr>
                                                    <td>{{ $serial++ }}</td>
                                                    <td>
                                                        <img src="{{ $value->main_image }}" class="img-thumbnail"
                                                            width="50" height="50" />
                                                    </td>
                                                    <td>{{ $value->ing_name }}</td>
                                                    <td>{{ $value->category ? $value->category->name : '-' }}</td>
                                                    <td>{{ $value->unit ? $value->unit->name . ' (' . $value->unit->symbol . ')' : '-' }}
                                                    </td>
                                                    <td>{{ $value->unit_price }}</td>
                                                    <td>
                                                        <x-status-toggle :id="$value->ing_id" :status="$value->is_active"
                                                            :url="route('admin.ingredient.toggleStatus')" />
                                                    </td>
                                                    <td>{{ $value->updated_at->diffForHumans() }}</td>
                                                    <td>
                                                        <a href="{{ route('admin.ingredient.translate', ['id' => $value->ing_id]) }}"
                                                            class="btn btn-info" title="Translate">
                                                            <i class="fas fa-language"></i>
                                                        </a>
                                                        <a href="#" class="btn btn-success" id="edit-ingredients"
                                                            data-id="{{ $value->ing_id }}">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                        <a class="btn btn-danger theme delete-btn"
                                                            href="javascript:void(0);"
                                                            data-action="{{ route('admin.ingredient.delete', ['id' => $value->ing_id]) }}">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Custom Ingredients --}}
                            <div class="tab-pane fade" id="custom" role="tabpanel" aria-labelledby="custom-tab">
                                <div class="table-responsive">
                                    <table class="hover dataTable" id="custom-table">
                                        <thead>
                                            <tr>
                                                <th>Sr. No.</th>
                                                <th>Image</th>
                                                <th>Name</th>
                                                <th>Category</th>
                                                <th>Unit</th>
                                                <th>Unit Price</th>
                                                <th>Status</th>
                                                <th>Last Updated</th>
                                                <th class="text-center action-col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $serial = 1; @endphp
                                            @foreach ($ingredient->where('ing_type', 'custom') as $value)
                                                <tr>
                                                    <td>{{ $serial++ }}</td>
                                                    <td>
                                                        <img src="{{ $value->main_image }}" class="img-thumbnail"
                                                            width="50" height="50" />
                                                    </td>
                                                    <td>{{ $value->ing_name }}</td>
                                                    <td>{{ $value->category ? $value->category->name : '-' }}</td>
                                                    <td>{{ $value->unit ? $value->unit->name . ' (' . $value->unit->symbol . ')' : '-' }}
                                                    </td>
                                                    <td>{{ $value->unit_price }}</td>
                                                    <td>
                                                        <x-status-toggle :id="$value->ing_id" :status="$value->is_active"
                                                            :url="route('admin.ingredient.toggleStatus')" />
                                                    </td>
                                                    <td>{{ $value->updated_at->diffForHumans() }}</td>
                                                    <td>
                                                        <a href="{{ route('admin.ingredient.translate', ['id' => $value->ing_id]) }}"
                                                            class="btn btn-info" title="Translate">
                                                            <i class="fas fa-language"></i>
                                                        </a>

                                                        <a href="#" class="btn btn-success" id="edit-ingredients"
                                                            data-id="{{ $value->ing_id }}">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>

                                                        <a class="btn btn-danger theme delete-btn"
                                                            href="javascript:void(0);"
                                                            data-action="{{ route('admin.ingredient.delete', ['id' => $value->ing_id]) }}">
                                                            <i class="fa fa-trash"></i>
                                                        </a>

                                                        {{-- NEW: Add Ingredient Button --}}
                                                        <button type="button" class="btn btn-primary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#standardIngredientsModal"
                                                            data-id="{{ $value->ing_id }}"
                                                            data-standard-ingredients='@json($value->standardIngredients->pluck('ing_id')->toArray())'>
                                                            <i class="fa fa-plus"></i> Add Recipe
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
    <!-- Standard Ingredients Modal -->
    <div class="modal fade" id="standardIngredientsModal" tabindex="-1" aria-labelledby="standardIngredientsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="standardIngredientsModalLabel">Select Ingredients</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Select</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Unit</th>
                                    <th>Unit Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ingredient->where('ing_type', 'standard') as $stdIng)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="selected_ingredients[]"
                                                value="{{ $stdIng->ing_id }}"
                                                @if (!empty($stdIng->standardIngredients) && $stdIng->standardIngredients->pluck('ing_id')->contains($stdIng->ing_id)) checked @endif>
                                        </td>
                                        <td>{{ $stdIng->ing_name }}</td>
                                        <td>{{ $stdIng->category ? $stdIng->category->name : '-' }}</td>
                                        <td>{{ $stdIng->unit ? $stdIng->unit->name : '-' }}</td>
                                        <td>{{ $stdIng->unit_price }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="saveSelectedIngredients">Save Selection</button>
                </div>
            </div>
        </div>
    </div>

@endsection


@section('script')
@endsection
