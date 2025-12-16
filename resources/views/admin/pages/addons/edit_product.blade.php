<x-modal id="addonProductModal" title="Edit Product Addon">
    <form method="POST" action="{{ route('admin.addons.update', $addon->id) }}" id="addon-form" method="POST">
     @csrf

        <input type="hidden" name="addonable_type" value="App\Models\ProductVariant">
        <input type="hidden" name="product_id" id="edit_product_id" value="{{ $addon->product_id }}">
        <input type="hidden" name="id" id="addon_id" value="{{ $addon->id }}">
        <input type="hidden" name="addonable_id" id="addonable_id" value="{{ $addon->addonable_id }}">



        {{-- Price Auto-filled --}}
        <div class="mb-3">
            <label for="variant_price" class="form-label">Price</label>
            <input type="number" step="0.01" class="form-control" id="variant_price" name="price" value="{{ $addon->price }}" required>
        </div>

        <div class="mb-3">
            <label for="variant_qty" class="form-label">Quantity</label>
            <input type="number" class="form-control" id="variant_qty" name="qty" value="{{ $addon->qty }}" required>
        </div>

        <div class="mb-3">
            <label for="variant_desc" class="form-label">Description</label>
            <textarea class="form-control" id="variant_desc" name="desc" rows="3">{{ $addon->desc }}</textarea>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
    </form>
</x-modal>


<script>


</script>
