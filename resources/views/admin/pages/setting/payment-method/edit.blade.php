<x-modal id="paymentMethodModal" title="Edit Payment Method">
  <form method="POST" action="{{ route('admin.paymentmethod.update', $paymentMethod->id ?? 0) }}" id="payment-form">
    @csrf


    <input type="hidden" name="id" value="{{ $paymentMethod->id ?? '' }}">

    <div class="mb-3">
      <label for="name">Method Name</label>
      <input type="text" class="form-control" id="name" name="name"
             value="{{ $paymentMethod->name ?? '' }}" required>
    </div>

    <div class="mb-3">
      <label for="code">Method Code</label>
      <input type="text" class="form-control" id="code" name="code"
             value="{{ $paymentMethod->code ?? '' }}" required>
    </div>

    <div class="mb-3">
      <label for="is_enabled">Status</label>
      <select class="form-control" id="is_enabled" name="is_enabled" required>
        <option value="1" {{ (isset($paymentMethod) && $paymentMethod->is_enabled) ? 'selected' : '' }}>Enabled</option>
        <option value="0" {{ (isset($paymentMethod) && !$paymentMethod->is_enabled) ? 'selected' : '' }}>Disabled</option>
      </select>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">Update</button>
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Close</button>
    </div>
  </form>
</x-modal>
