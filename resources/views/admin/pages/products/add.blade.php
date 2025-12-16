<x-modal id="productModal" title="Add New Product">
  <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" id="productForm">
    @csrf

    <input type="hidden" id="product-id" name="product_id">
     <x-image-upload id="full" name="full" :value="$product->full ?? null" />
     <x-video-upload id="product_video" name="product_video" :value="$product->product_video ?? null" />


    <div class="mb-3">
      <label for="name">Product Name</label>
      <input type="text" class="form-control" id="product-name" name="name" required>
    </div>

    <div class="mb-3">
      <label for="cat_id">Category</label>
      <select class="form-control" name="cat_id" id="cat_id" required>
        <option value="">-- Select Category --</option>
        @foreach ($categories as $category)
          <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
      </select>
    </div>
     <div class="mb-3">
      <label for="product_type">Product Type</label>
        <select name="product_type" id="product_type" class="form-control" required>
            <option value="indoor">Indoor</option>
            <option value="outdoor">Outdoor</option>
        </select>
    </div>

    <div class="mb-3">
      <label for="slug">Slug</label>
      <input type="text" class="form-control" id="slug" name="slug" readonly>
    </div>

    <div class="mb-3">
      <label for="desc">Description</label>
      <textarea class="form-control " id="summernote" name="desc" rows="3"></textarea>
    </div>
    <div class="mb-3">
    <label class="form-label d-block">Product Flags</label>

    <div class="form-check form-switch d-inline-block me-4">
        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1">
        <label class="form-check-label" for="is_featured">Featured Product</label>
    </div>

    <div class="form-check form-switch d-inline-block">
        <input class="form-check-input" type="checkbox" id="is_best_selling" name="is_best_selling" value="1">
        <label class="form-check-label" for="is_best_selling">Best Selling Product</label>
        </div>
    </div>

    <div class="mb-3">
            <label for="status">Status</label>
            <select class="form-control" id="status" name="is_active" required>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
    </div>


    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">Save</button>
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
          //  ['insert', ['link', 'picture']],
            ['view', ['fullscreen', 'codeview']]
        ]
    });

});
</script>

