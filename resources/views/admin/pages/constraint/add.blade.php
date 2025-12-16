<x-modal id="constraintModal" title="Add New Constraint">
    <form method="POST" action="{{ route('admin.constraint.store') }}" id="constraintForm">
        @csrf
        <input type="hidden" name="id" id="constraint-id">

        <div class="mb-3">
            <label for="title">Constraint Title</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>

        <div class="mb-3">
            <label for="status">Status</label>
            <select class="form-control" id="status" name="status" required>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Close</button>
        </div>
    </form>
</x-modal>
