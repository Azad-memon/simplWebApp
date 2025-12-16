<x-modal id="addonModal" title="Add New Addon">
    <form method="POST" action="{{ route('admin.addons.store') }}" id="addon-form">
        @csrf

        {{-- Required for identifying product --}}
        <input type="hidden" name="product_id" id="product_id" value="{{ $products }}">

        {{-- Optional: For polymorphic relation --}}
        <input type="hidden" name="addonable_type" value="{{ \App\Models\IngredientCategory::class }}">

        <!-- Category Dropdown -->
        <div class="mb-3">
            <label for="category_id" class="form-label">Category</label>
            <select class="form-select" id="category_id_addon" name="addonable_id" required>
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Multi-select Ingredients Dropdown (Initially Empty) -->
        <div class="mb-3">
            <label for="ingredient_ids" class="form-label">Select Ingredients</label>
            <select class="form-select" id="ingredient_ids" name="ingredient_ids[]" multiple required>
                <!-- Options will be populated dynamically -->
            </select>
        </div>

        <div class="mb-3 hide">
            <label for="price" class="form-label">Price</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" value="0" required>
        </div>

        <div class="mb-3">
            <label for="qty" class="form-label">Quantity</label>
            <input type="number" class="form-control" id="qty" name="qty" value="1" required>
        </div>

        <div class="mb-3">
            <label for="desc" class="form-label">Description</label>
            <textarea class="form-control" id="desc" name="desc" rows="3"></textarea>
        </div>
     <div class="form-check form-switch d-inline-block me-4">
        <input class="form-check-input" type="checkbox" id="is_replace" name="is_replace">
        <label class="form-check-label" for="is_replace">Replace Addon With Existing Ingredients</label>
    </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Close</button>
        </div>
    </form>
</x-modal>
