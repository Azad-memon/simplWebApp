<x-modal id="dealModal" title="Add New Deal">
  <form method="POST" action="{{ route('admin.deals.store') }}" id="dealForm">
    @csrf
     <x-image-upload id="full" name="full" :value="$product->full ?? null" />
    <div class="form-group">
      <label for="title">Deal Title</label>
      <input type="text" class="form-control" id="title" name="title" required>
    </div>

    <div class="form-group">
      <label for="description">Description</label>
      <textarea class="form-control" id="description" name="description" rows="3"></textarea>
    </div>

    <div class="form-group">
      <label for="price">Deal Price</label>
      <input type="number" step="0.01" class="form-control" id="price" name="price" required>
    </div>

    <div class="form-group">
      <label for="original_price">Original Price</label>
      <input type="number" step="0.01" class="form-control" id="original_price" name="original_price">
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for="start_date">Start Date</label>
          <input type="date" class="form-control" id="start_date" name="start_date">
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label for="start_time">Start Time</label>
          <input type="time" class="form-control" id="start_time" name="start_time">
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for="end_date">End Date</label>
          <input type="date" class="form-control" id="end_date" name="end_date">
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label for="end_time">End Time</label>
          <input type="time" class="form-control" id="end_time" name="end_time">
        </div>
      </div>
    </div>

    <div class="form-group">
      <label for="is_active">Status</label>
      <select class="form-control" name="is_active" id="is_active" required>
        <option value="1" selected>Active</option>
        <option value="0">Inactive</option>
      </select>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">Save</button>
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    </div>
  </form>
</x-modal>
