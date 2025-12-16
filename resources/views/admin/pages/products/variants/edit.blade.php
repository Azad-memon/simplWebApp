    <x-modal id="variantModal" title="Edit Product Variant">
  <form method="POST" action="{{ route('admin.product.variants.update', ['product' => $variant->product_id, 'variant' => $variant->id]) }}" id="variantForm">
    @csrf


    <input type="hidden" name="variant_id" id="variant-product-id" value="{{ $variant->id }}">
     <x-image-upload id="full" name="full" :value="getImageByType($variant->images, 'full') ?? null" />

     <div class="mb-3">
        <label for="unit">Serving Quantity</label>
        <input type="text" class="form-control" id="unit" name="unit" placeholder="e.g. 10" required value="{{ $variant->unit ?? '' }}">
    </div>

    <div class="mb-3">
    <label for="size">Select Size</label>
    <select class="form-select" id="size" name="size" required>
        <option value="">-- Choose Size --</option>
        @foreach($sizes as $size)
            <option value="{{ $size->id }}"
                {{ old('size', $selectedSizeId ?? ($variant->size ?? '')) == $size->id ? 'selected' : '' }} data-sizename="{{ $size->name }}">
                {{ $size->name }} @if($size->code) ({{ $size->code }}) @endif
            </option>
        @endforeach
    </select>
</div>


    <div class="mb-3">
      <label for="sku">SKU</label>
      <input type="text" class="form-control" id="sku-varient" name="sku" placeholder="Enter unique SKU" value="{{ $variant->sku ?? '' }}" readonly>
    </div>

    <div class="mb-3">
      <label for="price">Price</label>
      <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" required value="{{ $variant->price ?? '' }}">
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">Save</button>
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    </div>
  </form>
</x-modal>
