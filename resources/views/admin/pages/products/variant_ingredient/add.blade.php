<x-modal id="ingredientModal" title="Assign Ingredient to Variant">
    <form method="POST" action="{{ route('admin.product.variants.ingredients.store') }}" id="ingredientForm">
        @csrf

        <input type="hidden" name="product_variant_id" id="ingredient-variant-id" value="{{ $variant->id }}">

        {{-- <div class="mb-3">
      <label for="ingredient_id">Select Ingredient</label>
      <select class="form-control" name="ingredient_id" id="ingredient_id" required>
        <option value="">-- Choose Ingredient --</option>
        @foreach ($ingredients as $ingredient)
          <option value="{{ $ingredient->ing_id }}">
            {{ $ingredient->ing_name }}{{ $ingredient->unit ? ' (' . $ingredient->unit->name . ')' : '' }}
          </option>
        @endforeach
      </select>
    </div> --}}

        <div class="mb-3">
            <label for="ing_category_id">Ingredient Category</label>
            <select class="form-control" name="ing_category_id" id="ing_category_id">
                <option value="">-- Choose Category --</option>
                @foreach ($ingredientCategories as $category)
                    <option
                        value="{{ $category->id }}"

                    >
                        {{ $category->name }}
                        {{-- @if(!empty($addedIngCatIds) && in_array($category->id, $addedIngCatIds))
                            (Already Added)
                        @endif --}}

                    </option>
                @endforeach
            </select>
        </div>
         <div class="mb-3">
            <label for="type">Type</label>
            <select class="form-control" name="type" id="type" required>
                <option value="">-- Choose Type --</option>
                <option value="required">Required</option>
                <option value="optional">Optional</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="ingredient_id">Select Default Ingredient</label>
            <select class="form-control" name="default_ing" id="ingredient_id">
                <option value="">-- Choose Ingredient --</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="quantity">Quantity</label>
            <input type="number" class="form-control" id="quantity" name="quantity" min="0" step="0.01"
                required placeholder="Enter quantity">
        </div>



        {{-- <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="default_ing" name="default_ing" value="1">
            <label class="form-check-label" for="default_ing">Set as Default Ingredient</label>
        </div> --}}

        <div class="mb-3" style="display: none;">
            <label for="Unit">Unit</label>
            <input type="hidden" class="form-control" id="unit" name="unit" placeholder="e.g. grams, ml"
                required>
        </div>
         <div class="mb-3">
          <label for="visible">Visible</label>
          <select class="form-control" name="status" id="visible" required>
              <option value="1">Yes</option>
              <option value="0">No</option>
          </select>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Assign</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
    </form>
</x-modal>
<script>
    $(document).ready(function() {
        $('#ing_category_id').on('change', function() {
            let categoryId = $(this).val();
            let ingredientSelect = $('#ingredient_id');
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
