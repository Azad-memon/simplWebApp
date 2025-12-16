<x-modal id="sizeModal" title="Add Size">
  <form method="POST" action="{{ route('admin.size.save')  }}" id="size-form">
    @csrf
    <div class="mb-3">
      <label for="size">Size</label>
      <input type="text" class="form-control" id="size" name="name" placeholder="e.g. small, medium" required>
    </div>
     <div class="mb-3">
      <label for="sku">Code (Optional)</label>
      <input type="text" class="form-control" id="code" name="code" placeholder="e.g. s, m">
    </div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">Save</button>
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    </div>
  </form>
</x-modal>
