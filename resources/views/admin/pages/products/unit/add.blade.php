<x-modal id="unitModal" title="Add Unit">
  <form method="POST" action="{{ route('admin.unit.save') }}" id="unit-form">
    @csrf

    <!-- Hidden ID for Edit -->
    <input type="hidden" name="id" id="unit_id">

    <div class="mb-3">
      <label for="unit-name">Unit Name</label>
      <input type="text" class="form-control" id="unit-name" name="name" placeholder="e.g. Gram" required>
    </div>

    <div class="mb-3">
      <label for="unit-code">Symbol (Optional)</label>
      <input type="text" class="form-control" id="unit-code" name="symbol" placeholder="e.g. g">
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">Save</button>
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    </div>
  </form>
</x-modal>
