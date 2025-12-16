<x-modal id="editBannerModal" title="Edit Banner">
    <form method="POST" action="{{ route('admin.banners.update', $banner->id ?? 0) }}" id="bannerForm" enctype="multipart/form-data">
        @csrf


        <div class="mb-3" id="editMediaFields">
            <x-image-upload id="full" name="full" :value="getImageByType($banner->images, 'full') ?? null" />
          {{-- <x-video-upload id="product_video" name="banner_video" :value="getImageByType($banner->images, 'banner_video') ?? null" />
        --}}
        </div>

        <!-- Banner Title -->
        <div class="mb-3">
            <label for="edit_banner_title">Banner Title</label>
            <input type="text" class="form-control" id="edit_banner_title" name="banner_title" value="{{ $banner->banner_title }}" required>
        </div>

        <!-- Banner Description -->
        <div class="mb-3">
            <label for="edit_banner_description">Description</label>
            <textarea class="form-control" id="edit_banner_description" name="banner_description" rows="3">{{ $banner->banner_description }}</textarea>
        </div>

        <!-- Type -->
        <div class="mb-3">
            <label for="edit_type">Type</label>
            <select class="form-control" id="edit_type" name="type" required>
                <option value="category" {{ $banner->type == 'category' ? 'selected' : '' }}>Category</option>
                <option value="product" {{ $banner->type == 'product' ? 'selected' : '' }}>Product</option>
                <option value="default" {{ $banner->type == 'default' ? 'selected' : '' }}>Default</option>
            </select>
        </div>

        <!-- Category Select -->
        <div class="mb-3" id="editCategorySelect" style="display: none;">
            <label for="edit_category_id">Select Category</label>
            <select class="form-control" id="edit_category_id" name="category_id">
                <option value="">-- Select Category --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $banner->category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Product Select -->
        <div class="mb-3" id="editProductSelect" style="display: none;">
            <label for="edit_product_id">Select Product</label>
            <select class="form-control" id="edit_product_id" name="product_id">
                <option value="">-- Select Product --</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ $banner->product_id == $product->id ? 'selected' : '' }}>
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Update</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
    </form>
</x-modal>
<script>
    $(document).ready(function () {
        const $editType = $('#edit_type');
        const $editCategorySelect = $('#editCategorySelect');
        const $editProductSelect = $('#editProductSelect');
        const $editMediaFields = $('#editMediaFields');

        function toggleEditFields() {
            const selectedType = $editType.val();

            $editCategorySelect.hide();
            $editProductSelect.hide();
          //  $editMediaFields.hide();

            if (selectedType === 'category') {
                $editCategorySelect.show();
            } else if (selectedType === 'product') {
                $editProductSelect.show();
            } else if (selectedType === 'default') {
              //  $editMediaFields.show();
            }
        }

        toggleEditFields(); // on load
        $editType.on('change', toggleEditFields);
    });
</script>

