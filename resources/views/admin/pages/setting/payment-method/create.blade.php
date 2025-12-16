<x-modal id="paymentMethodModal" title="Add New Payment Method">
  <form method="POST" action="{{ route('admin.paymentmethod.store') }}" id="payment-form">
    @csrf

    <div class="mb-3">
      <label for="name">Method Name</label>
      <input type="text" class="form-control" id="name" name="name" placeholder="e.g. PayPal, Stripe" required>
    </div>

    <div class="mb-3">
      <label for="code">Method Code</label>
      <input type="text" class="form-control" id="code" name="code" placeholder="e.g. paypal, stripe" required>
    </div>

    <div class="mb-3">
      <label for="is_enabled">Status</label>
      <select class="form-control" id="status" name="is_enabled" required>
        <option value="1">Enabled</option>
        <option value="0">Disabled</option>
      </select>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">Save</button>
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Close</button>
    </div>
  </form>
</x-modal>
