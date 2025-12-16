<x-modal id="IngredientsModal" title="Add New Ingredients">
    <form method="POST" id="ingredientForm" class="ingredient-form" action="{{ route('admin.ingredient.store') }}"
        enctype="multipart/form-data">
        @csrf
        <div class="modal-body">

            <!-- Image Upload -->
            <div class="mb-3">
                <x-image-upload id="full" name="full" :value="$ingredient->full ?? null" />
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="ing_name" class="form-label">Ingredient Name</label>
                    <input type="text" class="form-control" id="ing_name" name="ing_name"
                        placeholder="Enter Ingredient Name" required>
                </div>
                 <div class="col-md-6 mb-3">
                    <label for="ingredient_label" class="form-label">Ingredient Label</label>
                    <input type="text" class="form-control" id="ingredient_label" name="ingredient_label"
                        placeholder="Enter Ingredient Label" required>
                </div>

                  <div class="col-md-6 mb-3">
                    <label for="ing_type" class="form-label">Ingredient Type</label>
                    <select class="form-select select2" id="ing_type" name="ing_type" required>
                        <option value="">-- Choose Type --</option>
                        <option value="standard">Standard</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>
            </div>


            <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="category_id" class="form-label">Category</label>
                    <select class="form-select select2" id="category_id" name="category_id" required>
                        <option value="">-- Choose Category --</option>
                        @foreach ($ingredientCategory as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="ing_unit">Select Unit</label>
                    <select class="form-select select2" id="ing_unit" name="ing_unit">
                        <option value="">-- Choose Unit --</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}">
                                {{ $unit->name }}
                                @if ($unit->symbol != '')
                                    ({{ $unit->symbol }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- <div class="col-md-6 mb-3">
                    <label for="unit_price" class="form-label">Unit Price</label>
                    <input type="number" class="form-control" id="unit_price" name="unit_price"
                        placeholder="Enter unit price" min="0" step="any" required>
                </div> --}}

              <div class="col-md-6 mb-3">
                    <label for="min_quantity" class="form-label">Minimum Branch Stock</label>
                    <input type="number" class="form-control" id="min_quantity" name="min_quantity"
                        placeholder="Enter minimum quantity" min="0" step="any" required>
                </div>
                 <div class="col-md-6 mb-3">
                    <label for="is_quantify" class="form-label">Quantifiable</label>
                    <select class="form-select select2" id="is_quantify" name="is_quantify">
                        <option value="">-- Choose --</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
 </div>

            <!-- Prices by Size -->
            <div class="mb-3">
                <label class="form-label">Ingredient Prices by Size</label>
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40%">Size</th>
                            <th style="width: 60%">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sizes as $size)
                            <tr>
                                <td>
                                    <input type="hidden" name="sizes[{{ $loop->index }}][size_id]"
                                        value="{{ $size->id }}">
                                    <strong>{{ $size->name }}</strong>
                                </td>
                                <td>
                                    <input type="number" name="sizes[{{ $loop->index }}][price]" class="form-control"
                                        step="0.01" min="0"
                                        placeholder="Enter price for {{ $size->name }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="ing_desc" class="form-label">Description (Optional)</label>
                    <textarea class="form-control" id="ing_desc" name="ing_desc" rows="3" placeholder="Enter Ingredient description"></textarea>
                </div>
            </div>

        </div>

        <!-- Modal Footer -->
        <div class="modal-footer">
            <button type="submit" class="btn btn-dark" id="custom-save-button">Add Ingredient</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
        </div>
    </form>
</x-modal>
