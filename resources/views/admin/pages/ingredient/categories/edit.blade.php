<x-modal id="ingcategoryModal" title="Edit Category">
    <form  action="{{ route('admin.ingredient.category.update', $category->id) }}" method="post" id="ingcategory-form" >
        @csrf
        <input type="hidden" name="id" id="category-id" value="{{ $category->id }}">
        <x-image-upload id="full" name="full" :value="getImageByType($category->images, 'full') ?? null" />
        <div class="mb-3">
            <label for="name">Category Title</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $category->name }}" required>
        </div>

        <div class="mb-3">
            <label for="desc">Description</label>
            <textarea class="form-control" id="desc" name="desc" required >{{ $category->description }}</textarea>
        </div>
        <div class="mb-3 hide">
            <label for="parent_id">Parent Category</label>
            <select class="form-control" id="parent_id" name="parent_id">

            </select>
        </div>



        {{-- <div class="mb-3">
            <label for="type">Category Type</label>
            <select class="form-control" id="type" name="type" required>
                <option value="{{ constant('App\Models\Category::IN_HOUSE_CATEGORY') }}" {{ (isset($category) && $category->type == constant('App\Models\Category::IN_HOUSE_CATEGORY'))  ? 'selected' : '' }}>Our Category</option>
                <option value="{{ constant('App\Models\Category::VENDOR_CATEGORY')   }}" {{ (isset($category) && $category->type == constant('App\Models\Category::VENDOR_CATEGORY'))  ? 'selected' : '' }}>Vendor Category</option>
            </select>
        </div> --}}

        <div class="mb-3">
            <label for="status">Status</label>
            <select class="form-control" id="status" name="status" required>
                <option value="1"{{ (isset($category) && $category->status == "1") ? 'selected' : '' }}>Active</option>
                <option value="0"{{ (isset($category) && $category->status == "0") ? 'selected' : '' }}>Inactive</option>
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
            getCategories();
    });
  function getCategories() {
    const $idSelect = $('#parent_id');
    const currentParentId = {{ $category->parent_id ?? 'null' }};
    const currentCategoryId = {{ $category->id }};

    $idSelect.html('<option value="">Loading...</option>');

    $.ajax({
        url: "{{ route('admin.category.dropdowndata') }}",
        method: 'GET',
        success: function (data) {
            $idSelect.html('<option value="">-- Select Category --</option>');
            $.each(data, function (index, item) {
                //console.log(data);
                // Avoid adding the current category as its own parent
                if (item.id != currentCategoryId) {
                    const isSelected = item.id === currentParentId ? 'selected' : '';
                    $idSelect.append('<option value="' + item.id + '" ' + isSelected + '>' + item.name + '</option>');
                }
            });
        },
        error: function () {
            $idSelect.html('<option value="">Failed to load data</option>');
        }
    });
}





</script>
