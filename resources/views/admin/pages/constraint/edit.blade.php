<x-modal id="constraintModal" title="Edit Constraint">
    <form method="POST" action="{{  route('admin.constraint.update', $constraint->id) }}"  id="constraintForm">
        @csrf
        <input type="hidden" name="id" id="constraint-id" value="{{ $constraint->id }}">
       @if(isset($constraint))
        @method('PUT')
        @endif
        <div class="mb-3">
            <label for="title">Constraint Title</label>
            <input type="text" class="form-control" id="title" name="title" required value="{{  $constraint->title }}">
        </div>

        <div class="mb-3">
            <label for="status">Status</label>
            <select class="form-control" id="status" name="status" required>
                <option value="active"{{ (isset($constraint) && $constraint->status == "active") ? 'selected' : '' }}>Active</option>
                <option value="inactive"{{ (isset($constraint) && $constraint->status == "inactive") ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Close</button>
        </div>
    </form>
</x-modal>
