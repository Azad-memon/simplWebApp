<x-modal id="sizeModal" title="Add / Edit Size">
    <form method="POST" action="{{ route('admin.size.update', $size->id) }}" id="size-form">
        @csrf

        <!-- Hidden ID field for updating -->
        <input type="hidden" name="id" id="size_id" value="{{ $size->id  }}">

        <div class="mb-3">
            <label for="size">Size</label>
            <input type="text" class="form-control" id="size" name="name" value="{{ $size->name  }}" placeholder="e.g. small, medium" required>
        </div>

        <div class="mb-3">
            <label for="sku">Code (Optional)</label>
            <input type="text" class="form-control" id="code" name="code" value="{{ $size->code  }}" placeholder="e.g. s, m">
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" id="size-submit-btn">Save</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
    </form>
</x-modal>
