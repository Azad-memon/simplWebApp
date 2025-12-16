<x-modal id="addonProductModal" title="Add Product Addon">
    <form method="POST" action="{{ route('admin.addons.store') }}" id="addon-form">
        @csrf

        <input type="hidden" name="addonable_type" value="App\Models\ProductVariant">
        <input type="hidden" name="product_id"  value="{{ $productId }}">


        {{-- Product Dropdown --}}
        <div class="mb-3">
            <label for="product_dropdown" class="form-label">Product</label>
            <select class="form-select" id="product_dropdown" required >
                <option value="">Select Product</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>


        {{-- Variant Dropdown (depends on Product) --}}
        <div class="mb-3">
            <label for="variant_dropdown" class="form-label">Product Variant</label>
            <select class="form-select" id="variant_dropdown" name="addonable_id" required>
                <option value="">Select Variant</option>
                {{-- Options loaded via JS --}}
            </select>
        </div>

        {{-- Price Auto-filled --}}
        <div class="mb-3">
            <label for="variant_price" class="form-label">Price</label>
            <input type="number" step="0.01" class="form-control" id="variant_price" name="price" value="0" required>
        </div>

        <div class="mb-3">
            <label for="variant_qty" class="form-label">Quantity</label>
            <input type="number" class="form-control" id="variant_qty" name="qty" value="1" required>
        </div>

        <div class="mb-3">
            <label for="variant_desc" class="form-label">Description</label>
            <textarea class="form-control" id="variant_desc" name="desc" rows="3"></textarea>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
    </form>
</x-modal>
<script>
      $(document).ready(function () {
        const $productSelect = $('#product_dropdown');
        const $variantSelect = $('#variant_dropdown');
        const $priceInput = $('#variant_price');

        // When product changes → load variants via AJAX
        $productSelect.on('change', function () {
            const productId = $(this).val();

            $variantSelect.html('<option value="">Loading...</option>');
            $priceInput.val(0);

            if (productId) {
                $.ajax({
                    url: "{{ route('admin.products.addon.variants', '') }}/" + productId,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $variantSelect.empty().append('<option value="">Select Variant</option>');
                        $.each(data, function (index, data) {
                            $variantSelect.append(
                                $('<option>', {
                                    value: data.id,
                                    text: data.name,
                                    'data-price': data.price
                                })
                            );
                        });
                    },
                    error: function () {
                        $variantSelect.html('<option value="">Error loading variants</option>');
                    }
                });
            }
        });

        // When variant changes → set price
        $variantSelect.on('change', function () {
            const price = $('option:selected', this).data('price') || 0;
            $priceInput.val(price);
        });
        });
</script>
