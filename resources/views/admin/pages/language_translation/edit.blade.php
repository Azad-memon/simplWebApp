<x-modal id="translationModal" title="Edit Translation">

  <form method="POST" action="{{ isset($translation) ? route('admin.language-translations.update', $translation->id) : route('language-translations.store') }}" id="translationForm">
    @csrf
    @if(isset($translation))
        @method('PUT')
    @endif

    <input type="hidden" name="id" id="translation-id" value="{{ $translation->id ?? '' }}">

    <div class="mb-3">
        <label for="language_id">Language</label>
        <select class="form-control" id="language_id" name="language_id" required>
            @foreach(App\Models\Language::all() as $language)
                <option value="{{ $language->id }}" {{ (isset($translation) && $translation->language_id == $language->id) ? 'selected' : '' }}>
                    {{ $language->name }}
                </option>
            @endforeach
        </select>
    </div>
   @if($type==="constraint")
    <div class="mb-3">
        <label for="translatable_type">Type</label>
        <select class="form-control" id="translatable_type" name="translatable_type" required>

            <option value="App\Models\Constraint" {{ (isset($translation) && $translation->translatable_type == 'App\Models\Constraint') ? 'selected' : '' }}>Constraint</option>
            {{-- <option value="App\Models\Product" {{ (isset($translation) && $translation->translatable_type == 'App\Models\Product') ? 'selected' : '' }}>Product</option> --}}
        </select>
    </div>

    <div class="mb-3" id="translatable-id-group" >
        <label for="translatable_id">Select Item</label>
        <select class="form-control" id="translatable_id" name="translatable_id">
            @if(isset($translation) && $translation->translatable_type == 'App\\Models\\Constraint')
                @php
                    $constraints = \App\Models\Constraint::all();
                @endphp
                @foreach($constraints as $item)
                    <option value="{{ $item->id }}" {{ $translation->translatable_id == $item->id ? 'selected' : '' }}>{{ $item->title }}</option>
                @endforeach
            @else
                <option value="">-- Select --</option>
            @endif
        </select>
    </div>
      @else
          <input type="hidden" class="form-control" id="" name="translatable_type" value="{{ $translation->translatable_type }}">
          <input type="hidden" class="form-control" id="" name="translatable_id" value="{{ $translation->translatable_id }}">
    @endif

    <div class="mb-3">
    <label for="meta_key">Choose Content Type</label>
    <select class="form-control" id="meta_key" name="meta_key" required>
        <option value="">-- Select Type --</option>
        <option value="title" {{ (isset($translation) && $translation->meta_key === 'title') ? 'selected' : '' }}>Title</option>
        <option value="description" {{ (isset($translation) && $translation->meta_key === 'description') ? 'selected' : '' }}>Description</option>
    </select>
    </div>

    <div class="mb-3">
        <label for="meta_value">Value</label>
        <input type="text" class="form-control" id="meta_value" name="meta_value" required value="{{ $translation->meta_value ?? '' }}">
    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Close</button>
    </div>
</form>

 </x-modal>
