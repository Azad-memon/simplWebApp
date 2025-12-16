<x-modal id="cmsModal" title="Add / Edit CMS Page">
    <form method="POST" action="{{ route('admin.cms.store') }}" id="cms-form">
        @csrf
        <div class="row g-3">

            {{-- Title --}}
            <div class="col-md-6">
                <label for="title" class="form-label">Page Title</label>
                <input type="text" class="form-control"id="product-name" name="title"
                    placeholder="Enter page title" required>
            </div>

            {{-- Slug --}}
            <div class="col-md-6">
                <label for="slug" class="form-label">Slug</label>
               <input type="text" class="form-control" id="slug" name="slug" readonly  placeholder="e.g. about-us">
            </div>
            {{-- Content --}}
            <div class="col-12">
                <label for="content" class="form-label">Content</label>
                <textarea class="form-control summernote" id="" name="content" rows="5" placeholder="Enter page content"></textarea>
            </div>

            {{-- Meta Title --}}
            {{-- <div class="col-md-6">
                <label for="meta_title" class="form-label">Meta Title</label>
                <input type="text" class="form-control" id="meta_title" name="meta_title"
                    placeholder="SEO Title">
            </div> --}}

            {{-- Meta Description --}}
            {{-- <div class="col-md-6">
                <label for="meta_desc" class="form-label">Meta Description</label>
                <textarea class="form-control" id="meta_desc" name="meta_desc" rows="2" placeholder="SEO Description"></textarea>
            </div> --}}

            {{-- Status --}}
            <div class="col-md-6">
                <label for="is_active" class="form-label">Status</label>
                <select class="form-select" id="status" name="is_active" required>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>

        </div>

        <div class="modal-footer mt-3">
            <button type="submit" class="btn btn-primary">Save Page</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                aria-label="Close">Close</button>
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
