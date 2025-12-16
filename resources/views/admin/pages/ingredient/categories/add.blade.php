<x-modal id="ingcategoryModal" title="Add New Category">
    <form method="POST" action="{{ route('admin.ingredient.category.store') }}" id="ingcategory-form">
        @csrf
        <input type="hidden" name="id" id="category-id">
        <x-image-upload id="full" name="full" :value="$category->full ?? null" />

        <div class="mb-3">
            <label for="name">Category Title</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>

        <div class="mb-3">
            <label for="desc">Description</label>
            <textarea class="form-control" id="desc" name="desc" required></textarea>
        </div>
        <input type="hidden" name="parent_id" id="parent_id">
        {{-- <div class="mb-3">
            <label for="parent_id">Parent Category</label>
            <select class="form-control" id="parent_id" name="parent_id">

            </select>
        </div> --}}

        {{-- <div class="mb-3">
            <label for="type">Category Type</label>
            <select class="form-control" id="type" name="type" required>
                <option value="{{ constant('App\Models\Category::IN_HOUSE_CATEGORY') }}">Our Category</option>
                <option value="{{ constant('App\Models\Category::VENDOR_CATEGORY') }}" >Vendor Category</option>
            </select>
        </div> --}}
        <div class="mb-3">
            <label for="status">Status</label>
            <select class="form-control" id="status" name="status" required>
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

            getCategories();
    });

    function getCategories() {
        const $idSelect = $('#parent_id'); // Corrected: Use jQuery object
        $idSelect.html('<option value="">Loading...</option>');

        $.ajax({
            url: "{{ route('admin.category.dropdowndata') }}",
            method: 'GET',
            success: function (data) {
                $idSelect.html('<option value="">-- Select Category --</option>');
                $.each(data, function (index, item) {
                    $idSelect.append('<option value="' + item.id + '">' + item.name + '</option>');
                });
            },
            error: function () {
                $idSelect.html('<option value="">Failed to load data</option>');
            }
        });
    }





</script>
