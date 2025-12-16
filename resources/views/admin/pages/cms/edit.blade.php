<x-modal id="cmsModal" title="Add / Edit CMS Page">
     <form action="{{ route('admin.cms.update', $cms->id) }}" method="POST" id="cms-form">
        @csrf
        @method('PUT')

        {{-- Title --}}
        <div class="mb-3">
            <label for="title" class="form-label">Page Title</label>
           <input type="text" class="form-control"id="product-name" name="title"
                    placeholder="Enter page title" required value="{{ $cms->title }}">
        </div>

        {{-- Slug --}}
        <div class="mb-3">
            <label for="slug" class="form-label">Page Slug</label>
            <input type="text" name="slug" id="slug"
                   class="form-control @error('slug') is-invalid @enderror"
                   value="{{ old('slug', $cms->slug) }}" required readonly>
            @error('slug')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Content --}}
        <div class="mb-3">
            <label for="content" class="form-label">Page Content</label>
          <textarea class="form-control summernote" id="" name="content" rows="5" placeholder="Enter page content">{{ $cms->content }}</textarea>
        </div>

        {{-- Status --}}
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="is_active" id="status"
                    class="form-select @error('is_active') is-invalid @enderror">
                <option value="1" {{ old('is_active', $cms->is_active) == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('is_active', $cms->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- SEO Meta Title --}}
        {{-- <div class="mb-3">
            <label for="meta_title" class="form-label">Meta Title</label>
            <input type="text" name="meta_title" id="meta_title"
                   class="form-control"
                   value="{{ old('meta_title', $cms->meta_title) }}">
        </div> --}}

        {{-- SEO Meta Description --}}
        {{-- <div class="mb-3">
            <label for="meta_description" class="form-label">Meta Description</label>
            <textarea name="meta_description" id="meta_description" rows="3"
                      class="form-control">{{ old('meta_description', $cms->meta_description) }}</textarea>
        </div> --}}

        {{-- SEO Meta Keywords --}}
        {{-- <div class="mb-3">
            <label for="meta_keywords" class="form-label">Meta Keywords</label>
            <input type="text" name="meta_keywords" id="meta_keywords"
                   class="form-control"
                   value="{{ old('meta_keywords', $cms->meta_keywords) }}">
        </div> --}}


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
