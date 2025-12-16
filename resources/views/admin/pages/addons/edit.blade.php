<x-modal id="addonmodel" title="edit addon">
  <form method="POST"
      action="{{ route('admin.addons.update', $addons->id) }}" method="POST"  id="addon-form">
     @csrf

        <!-- Hidden ID field for updating -->
    <input type="hidden" name="id" id="addon_id" value="{{ $addons->id }}">
     <input type="hidden" name="addonable_type" value="{{ \App\Models\IngredientCategory::class }}">
     <input type="hidden" name="addonable_id" value="{{ $addons->addonable_id }}">

    {{-- <div class="mb-3">
        <label for="ing_id" class="form-label">Ingredient</label>
        <select name="addonable_id" id="ing_id" class="form-control">
            @foreach($ingredients as $ingredient)
                <option value="{{ $ingredient->ing_id }}" {{ $addons->addonable_id == $ingredient->ing_id ? 'selected' : '' }}>
                    {{ $ingredient->ing_name }}
                </option>
            @endforeach
        </select>
    </div> --}}
    <div class="mb-3">
        <label for="category_id" class="form-label">Category</label>
        <select class="form-select" id="category_id_edit" name="category_id" required>
            <option value="">Select Category</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ $addons->addonable->id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
     <!-- Ingredients Dropdown -->
        <div class="mb-3">
            <label for="ingredient_ids_edit" class="form-label">Select Ingredients</label>
            <select class="form-select" id="ingredient_ids_edit" name="ingredient_ids[]" multiple required>
                @foreach($ingredients as $ingredient)
                    <option value="{{ $ingredient->ing_id }}"
                        {{ in_array($ingredient->ing_id, $addons->items->pluck('ingredient_id')->toArray()) ? 'selected' : '' }}>
                        {{ $ingredient->ing_name }}
                    </option>
                @endforeach
            </select>
        </div>
    <div class="mb-3 hide">
        <label for="price" class="form-label">Price</label>
        <input type="number" step="0.01" name="price" id="price" class="form-control" value="{{ $addons->price ?? 0 }}">
    </div>

    <div class="mb-3">
        <label for="qty" class="form-label">Quantity</label>
        <input type="number" name="qty" id="qty" class="form-control" value="{{ $addons->qty }}">
    </div>

    <div class="mb-3">
        <label for="desc" class="form-label">Description</label>
        <textarea name="desc" id="desc" class="form-control">{{ $addons->desc }}</textarea>
    </div>
      <div class="form-check form-switch d-inline-block me-4">
        <input class="form-check-input" type="checkbox" id="is_replace" name="is_replace" {{ $addons->is_replace ? 'checked' : '' }}>
        <label class="form-check-label" for="is_replace">Replace Addon With Existing Ingredients</label>
    </div>

   <div class="modal-footer">
      <button type="submit" class="btn btn-primary">Update</button>
      <button type="button" class="btn btn-secondary"  data-bs-dismiss="modal" aria-label="Close">Close</button>
    </div>
  </form>
</x-modal>

