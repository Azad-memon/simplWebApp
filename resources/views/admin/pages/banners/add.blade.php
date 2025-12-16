<x-modal id="bannerModal" title="Add New Banner">
    <form method="POST" action="{{ route('admin.banners.store') }}" id="bannerForm">
        @csrf
        <div class="mb-3" id="mediaFields">
            <x-image-upload id="full" name="full" :value="$banner->full ?? null" />
            {{-- <x-video-upload id="banner_video" name="banner_video" :value="$banner->banner_video ?? null" /> --}}
        </div>
        <!-- Banner Title -->
        <div class="mb-3">
            <label for="banner_title">Banner Title</label>
            <input type="text" class="form-control" id="banner_title" name="banner_title" required>
        </div>

       <div class="mb-3">
            <label for="banner_description" class="form-label">Description</label>
            <textarea
              class="form-control"
              id="banner_description"
              name="banner_description"
              rows="3"
              placeholder="Enter description"
            ></textarea>
       </div>
        <!-- Type (Category or Product) -->
        <div class="mb-3">
            <label for="type">Type</label>
            <select class="form-control" id="type" name="type" required>
                <option value="category">Category</option>
                <option value="product">Product</option>
                <option value="default">Default</option>
            </select>
        </div>

        <!-- Category ID (Visible only if 'Category' is selected) -->
            <div class="mb-3" id="categorySelect" style="display: none;">
            <label for="category_id">Select Category</label>
            <select class="form-control" id="category_id" name="category_id">
                <option value="">-- Select Category --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <!-- Product ID (Visible only if 'Product' is selected) -->
        <div class="mb-3" id="productSelect" style="display: none;">
            <label for="product_id">Select Product</label>
            <select class="form-control" id="product_id" name="product_id">
                <option value="">-- Select Product --</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
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

        const $typeDropdown = $('#type');
        const $categorySelect = $('#categorySelect');
        const $productSelect = $('#productSelect');
        const $mediaFields = $('#mediaFields');


        function toggleFields() {
            const selectedType = $typeDropdown.val();

            // Hide all
            $categorySelect.hide();
            $productSelect.hide();
            //$mediaFields.hide();

            // Show based on selected type
            if (selectedType === 'category') {
                $categorySelect.show();
            } else if (selectedType === 'product') {
                $productSelect.show();
            } else if (selectedType === 'default') {
               // $mediaFields.show();
            }
        }

        // Initial toggle
        toggleFields();

        // On change
        $typeDropdown.on('change', toggleFields);
    });
</script>



