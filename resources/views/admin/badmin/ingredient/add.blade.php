@php
    $routeName = Auth::user()->role->name === 'branchadmin' || Auth::user()->role->name === 'accountant'
        ? 'badmin.ingredient.custom.update'
        : 'admin.ingredient.custom.update';
@endphp
<x-modal id="IngredientsModal" title="Update Ingredient Quantity">
    <form method="POST" class="ingredient-form-update"  action="{{ route($routeName, $ingredient->ing_id) }}">
        @csrf
        <div class="modal-body">

            <!-- Ingredient Info Box -->
            <div class="rounded shadow-sm p-3 mb-4" style="background-color: #f8f9fa;">
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted fw-semibold">Ingredient Name:</dt>
                    <dd class="col-sm-7 text-dark fw-bold mb-2">{{ $ingredient->ing_name }}</dd>

                    <dt class="col-sm-5 text-muted fw-semibold">Unit:</dt>
                    <dd class="col-sm-7 text-primary fw-semibold mb-0">{{ $ingredient->unit?->name ?? '-' }}</dd>
                </dl>
            </div>
            <input type="hidden" name="id" value="{{ $ingredient->ing_id }}">
            <input type="hidden" name="branchid" value="{{isset($BranchId)?$BranchId:Auth::user()->branches[0]->id}}">

            <!-- Standard Ingredients Section -->
            @if (!empty($ingredient->standardIngredients) && $ingredient->standardIngredients->count() > 0)
                <div class="mb-4">
                    <h6 class="fw-bold mb-3">Standard Ingredients</h6>

                    @foreach ($ingredient->standardIngredients as $std)
                        <div class="row align-items-center mb-3 p-2 rounded border bg-light">
                            <!-- Name -->
                            <div class="col-md-4 fw-semibold text-dark">
                                {{ $std->ing_name }}
                            </div>

                            <!-- Quantity Input -->
                            <div class="col-md-4">
                                <input type="number" class="form-control" name="std_quantity[{{ $std->ing_id }}]"
                                    placeholder="Enter qty" min="0" step="any">
                            </div>

                            <!-- Unit (only label) -->
                            <div class="col-md-4 text-primary fw-semibold">
                                {{ $std->unit?->name ?? '-' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Main Ingredient Quantity Input -->
            <div class="mb-3">
                <label for="quantity" class="form-label fw-semibold">Update Stock Quantity</label>
                <input type="number" class="form-control form-control-lg" id="quantity" name="quantity"
                    placeholder="Enter quantity" min="0" step="any" value="" required>
            </div>

        </div>


        <!-- Modal Footer -->
        <div class="modal-footer d-flex justify-content-between">
            <button type="button" class="btn btn-outline-danger px-4" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary custom-save-button">Update Quantity</button>
        </div>
    </form>
</x-modal>
