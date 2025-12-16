<x-modal id="languageModal" title="Add New Language">
  <form method="POST" action="{{ route('admin.languages.store') }}"  id="languageForm">
    @csrf
   <div class="mb-3">
      <label for="name" class="form-label">Language Name</label>
      <input type="text" class="form-control" id="name" name="name" required>
      <input type="hidden" id="language-id" >
    </div>
    <div class="mb-3">
      <label for="code" class="form-label">Language Code</label>
      <input type="text" class="form-control" id="code" name="code" required>
    </div>
    <div class="m3 form-check">
       <input type="hidden" name="is_default"  id="is_default_name" value="0">
      <input type="checkbox" class="form-check-input" id="is_default" >
      <label class="form-check-label" for="is_default">Set as Default</label>
    </div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">Save</button>
      <button type="button" class="btn btn-secondary"  data-bs-dismiss="modal" aria-label="Close">Close</button>
    </div>
  </form>
</x-modal>

