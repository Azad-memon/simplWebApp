

<x-modal id="categoryModal" title="➕ Add New Category">
    <form method="POST" action="{{ route('admin.categories.store') }}" id="categoryForm">
        @csrf
        <input type="hidden" name="id" id="category-id">

        {{-- Upload Section --}}
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm rounded-3">
                    <div class="card-header bg-primary text-white fw-semibold rounded-top">
                        📷 Category Image
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center">
                        <x-image-upload id="full" name="full" :value="null" />
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm rounded-3">
                    <div class="card-header bg-success text-white fw-semibold rounded-top">
                        🎥 Category Video
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center">
                        <x-video-upload id="category_video" name="category_video" :value="null" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Information Section --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body">
                <h5 class="mb-3 text-primary fw-bold">📝 Category Information</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-semibold">Category Name</label>
                        <input type="text" class="form-control form-control-lg rounded-3"
                               id="name" name="name" placeholder="Enter category name" required>
                    </div>

                    <div class="col-md-6">
                        <label for="series" class="form-label fw-semibold">List Order Number</label>
                        <input type="number" class="form-control form-control-lg rounded-3"
                               id="series" name="series" placeholder="Enter order number" required>
                    </div>

                    <div class="col-12">
                        <label for="desc" class="form-label fw-semibold">Description</label>
                        <textarea class="form-control rounded-3" id="desc" name="desc"
                                  rows="3" placeholder="Write description..." required></textarea>
                    </div>

                    <div class="col-md-6">
                        <label for="parent_id" class="form-label fw-semibold">Parent Category</label>
                        <select class="form-select rounded-3" id="parent_id" name="parent_id"></select>
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label fw-semibold">Status</label>
                        <select class="form-select rounded-3" id="status" name="status" required>
                            <option value="active">✅ Active</option>
                            <option value="inactive">⛔ Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="modal-footer d-flex justify-content-between">
            <button type="button" class="btn btn-light px-4 border" data-bs-dismiss="modal">❌ Cancel</button>
            <button type="submit" class="btn btn-success px-5 fw-semibold shadow-sm">➕ Add Category</button>
        </div>
    </form>
</x-modal>

<script>
    $(document).ready(function () {
        getCategories();
    });

    function getCategories() {
        const $idSelect = $('#parent_id');
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
