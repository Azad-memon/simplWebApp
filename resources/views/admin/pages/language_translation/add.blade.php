<x-modal id="translationModal" title="Add New Translation">

    <form method="POST" action="{{ route('admin.language-translations.store') }}" id="translationForm">
        @csrf
        <input type="hidden" name="id" id="translation-id">
        <div class="mb-3">
            <label for="language_id" class="form-lebel">Language</label>
            <select class="form-control" id="language_id" name="language_id" required>
                @foreach(App\Models\Language::all() as $language)
                    <option value="{{ $language->id }}">{{ $language->name }}</option>
                @endforeach
            </select>
        </div>
        @if($type==="constraint")
        <div class="mb-3">
            <label for="translatable_type">Type </label>
            <select class="form-control" id="translatable_type" name="translatable_type" required>
                <option value="">-- Select Type --</option>
                <option value="App\Models\Constraint">Constraint</option>
                {{-- <option value="App\Models\Product">Product</option> --}}
            </select>
        </div>

        <div class="mb-3" id="translatable-id-group" style="display:none;">
            <label for="translatable_id">Select Item</label>
            <select class="form-control" id="translatable_id" name="translatable_id" required>
                <option value="">-- Select Item --</option>
                {{-- Options will be populated by JS --}}
            </select>
        </div>
        @else
          <input type="hidden" class="form-control" id="" name="translatable_type" value="{{ $type }}">
          <input type="hidden" class="form-control" id="" name="translatable_id" value="{{ $translatable_id }}">
        @endif

        {{-- <div class="form-group">
            <label for="translatable_id">Translatable ID</label>
            <input type="number" class="form-control" id="translatable_id" name="translatable_id">
        </div> --}}
        <div class="mb-3">
           <label for="meta_key">Choose Content Type</label>
            <select class="form-control" id="meta_key" name="meta_key" required>
                <option value="">-- Select Content Type --</option>
                <option value="title">Title</option>
                <option value="description">Description</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="meta_value">Value</label>
            <input type="text" class="form-control" id="meta_value" name="meta_value" required>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Close</button>
        </div>
    </form>
 </x-modal>
