<x-modal id="ingredientModal" title="Edit Ingredient Assignment">
    <form method="POST"
        action="{{ route('admin.product.variants.ingredients.update', [
            'variant' => $variant->id,
            'ingredient' => $variant->id,
        ]) }}"
        id="ingredientForm">
        @csrf


        <input type="hidden" name="product_variant_id" id="edit-ingredient-variant-id" value="{{ $variant->id }}">
        <input type="hidden" name="ingredient_id" id="edit-ingredient-id" value="">

        {{-- Ingredient Category --}}
        <div class="mb-3">
            <label for="edit-ing-category">Ingredient Category</label>
            <select class="form-control" name="ing_category_id" id="edit-ing-category">
                <option value="">-- Choose Category --</option>
                @foreach ($ingredientCategories as $category)
                    <option value="{{ $category->id }}" @if ($ingredient->pivot->ing_category_id == $category->id) selected @endif>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="edit-type">Type</label>
            <select class="form-control" name="type" id="edit-type" required>
                <option value="">-- Choose Type --</option>
                <option value="required" @if ($ingredient->pivot->type == 'required') selected @endif>Required</option>
                <option value="optional" @if ($ingredient->pivot->type == 'optional') selected @endif>Optional</option>
            </select>
        </div>
        {{-- Ingredient dropdown (with default) --}}
        <div class="mb-3">
            <label for="edit-ingredient-select">Select Default Ingredient</label>
            <select class="form-control" name="default_ing" id="edit-ingredient-select">
                <option value="">-- Choose Ingredient --</option>
                @foreach ($ingredient->ingredients->where('category_id', $ingredient->pivot->ing_category_id) as $ing)
                    <option value="{{ $ing->ing_id }}" @if ($ingredient->pivot->default_ing == $ing->ing_id) selected @endif>
                        {{ $ing->ing_name }}{{ $ing->unit ? ' (' . $ing->unit->name . ')' : '' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="edit-quantity">Quantity</label>
            <input type="number" class="form-control" id="edit-quantity" name="quantity" min="0" step="0.01"
                required value="{{ $ingredient->pivot->quantity ?? '' }}" placeholder="Enter quantity">
        </div>


        <div class="mb-3" style="display: none;">
            <label for="edit-unit">Unit</label>
            <input type="text" class="form-control" id="edit-unit" name="unit"
                value="{{ $ingredient->pivot->unit ?? '' }}" placeholder="e.g. grams, ml">
        </div>
        <div class="mb-3">
            <label for="edit-visible">Visible</label>
            <select class="form-control" name="status" id="edit-visible" required>
                <option value="1" @if ($ingredient->pivot->status == 1) selected @endif>Yes</option>
                <option value="0" @if ($ingredient->pivot->status == 0) selected @endif>No</option>
            </select>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Update</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
    </form>
</x-modal>
<script>
    $(document).ready(function() {
        $('#edit-ing-category').on('change', function() {
            let categoryId = $(this).val();
            let ingredientSelect = $('#edit-ingredient-select');
            ingredientSelect.empty().append('<option value="">-- Loading... --</option>');

            if (categoryId) {
                $.ajax({
                    url: "{{ route('admin.ingredients.byCategory') }}",
                    type: "GET",
                    data: {
                        category_id: categoryId
                    },
                    success: function(response) {
                        ingredientSelect.empty().append(
                            '<option value="">-- Choose Ingredient --</option>');
                        $.each(response, function(key, ingredient) {
                            let unit = ingredient.unit ? ' (' + ingredient.unit
                                .name + ')' : '';
                            ingredientSelect.append('<option value="' + ingredient
                                .ing_id + '">' + ingredient.ing_name + unit +
                                '</option>');
                        });
                    }
                });
            } else {
                ingredientSelect.empty().append('<option value="">-- Choose Ingredient --</option>');
            }
        });
    });
</script>
