<x-modal id="productModal" title="Edit Product">
  <form method="POST" action="{{ route('admin.products.update', $product->id ?? 0) }}" enctype="multipart/form-data" id="productForm">
    @csrf
    @method('PUT')

    <input type="hidden" id="product-id" name="product_id" value="{{ $product->id ?? '' }}">

  <x-image-upload id="full" name="full" :value="getImageByType($product->images, 'full') ?? null" />
     <x-video-upload id="product_video" name="product_video" :value="getImageByType($product->images, 'product_video') ?? null" />

    <div class="mb-3">
      <label for="name">Product Name</label>
      <input type="text" class="form-control" id="product-name" name="name" value="{{ $product->name ?? '' }}" required>
    </div>

    <div class="mb-3">
      <label for="cat_id">Category</label>
      <select class="form-control" name="cat_id" id="cat_id" required>
        <option value="">-- Select Category --</option>
        @foreach ($categories as $category)
          <option value="{{ $category->id }}" {{ (isset($product) && $product->cat_id == $category->id) ? 'selected' : '' }}>
            {{ $category->name }}
          </option>
        @endforeach
      </select>
    </div>
    <div class="mb-3">
      <label for="product_type">Product Type</label>
        <select name="product_type" id="product_type" class="form-control" required>
            <option value="indoor"  {{ (isset($product) && $product->product_type=="indoor") ? 'selected' : '' }}>Indoor</option>
            <option value="outdoor"  {{ (isset($product) && $product->product_type=="outdoor") ? 'selected' : '' }}>Outdoor</option>
        </select>
    </div>

    <div class="mb-3">
      <label for="slug">Slug</label>
      <input type="text" class="form-control" id="slug" name="slug" value="{{ $product->slug ?? '' }}" readonly>
    </div>

    <div class="mb-3">
      <label for="desc">Description</label>
      <textarea class="form-control summernote" id="edit-desc" name="desc" rows="3">{{ $product->desc ?? '' }}</textarea>
    </div>
    <div class="mb-3">
        <label class="form-label d-block">Product Flags</label>

        <div class="form-check form-switch d-inline-block me-4">
            <input
                class="form-check-input"
                type="checkbox"
                id="is_featured"
                name="is_featured"
                value="1"
                {{ isset($product) && $product->is_featured ? 'checked' : '' }}
            >
            <label class="form-check-label" for="is_featured">Featured Product</label>
        </div>

        <div class="form-check form-switch d-inline-block">
            <input
                class="form-check-input"
                type="checkbox"
                id="is_best_selling"
                name="is_best_selling"
                value="1"
                {{ isset($product) && $product->is_best_selling ? 'checked' : '' }}
            >
            <label class="form-check-label" for="is_best_selling">Best Selling Product</label>
        </div>
    </div>


    <div class="mb-3">
      <label for="status">Status</label>
      <select class="form-control" id="edit-status" name="is_active" required>
        <option value="1" {{ (isset($product) && $product->is_active) ? 'selected' : '' }}>Active</option>
        <option value="0" {{ (isset($product) && !$product->is_active) ? 'selected' : '' }}>Inactive</option>
      </select>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">Update</button>
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Close</button>
    </div>
  </form>
</x-modal>
<script>
$(document).ready(function () {
    $('.summernote').summernote({
        placeholder: 'Write description...',
        height: 200,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['fontsize', 'color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['view', ['fullscreen', 'codeview']]
        ]
    });

});
</script>
