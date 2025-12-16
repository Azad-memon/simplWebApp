<x-modal id="unitModal" title="Add / Edit Unit">
    <form method="POST" action="{{ route('admin.unit.update', $unit->id) }}" id="unit-form">
        @csrf

        <!-- Hidden ID field for updating -->
        <input type="hidden" name="id" id="unit_id" value="{{ $unit->id }}">

        <div class="mb-3">
            <label for="unit-name">Unit Name</label>
            <input type="text" class="form-control" id="unit-name" name="name" value="{{ $unit->name }}" placeholder="e.g. piece, bottle" required>
        </div>

        <div class="mb-3">
            <label for="unit-code">Symbol (Optional)</label>
            <input type="text" class="form-control" id="unit-Symbol" name="symbol" value="{{ $unit->symbol }}" placeholder="e.g. pc, btl">
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" id="unit-submit-btn">Save</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
    </form>
</x-modal>
