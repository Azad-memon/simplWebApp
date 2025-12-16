  <!-- Cart Items -->
  <div class="flex-grow-1 overflow-auto" style="max-height: 60vh;">
      <div class="list-group">
          <input type="hidden" name="cart_id" id="cart_id" value="{{ $cart['cart_id'] }}">
          @foreach ($cart['cart'] as $index => $item)

              <div
                  class="list-group-item d-flex justify-content-between align-items-start border-0 shadow-sm mb-2 rounded">

                  <!-- Left: Item Info -->
                  <div class="me-2">
                      <div class="fw-semibold">{{ $item['product_name'] }}</div>
                      <small class="text-muted">{{ $item['variant_name'] }}</small>
                      <p class="text-muted">{{ $item['notes'] }}</p>

                      <!-- Addons -->
                      @if (isset($item['addon']) && count($item['addon']) > 0)
                          <ul class="list-unstyled mb-0 mt-1 small">
                              @foreach ($item['addon'] as $addon)
                                  <li class="text-success">
                                      <i class="fas fa-plus-circle me-1"></i>
                                      {{ $addon['name'] }}
                                      <span class="text-muted">(Rs: {{ number_format($addon['total'], 2) }})</span>
                                    </li>
                              @endforeach
                          </ul>
                      @endif

                      <!-- Removed Ingredients -->
                      {{-- @if (!empty($item['removed_ingredients_details']) && count($item['removed_ingredients_details']) > 0)
                          <ul class="list-unstyled mb-1 small">
                              <strong class="text-danger d-block mb-1">Removed:</strong>
                              @foreach ($item['removed_ingredients_details'] as $removed)
                                  <li class="text-danger">
                                      <i class="fas fa-minus-circle me-1"></i> {{ $removed['name'] }}
                                  </li>
                              @endforeach
                          </ul>
                      @endif --}}
                  </div>


                  <!-- Right: Controls -->
                  <div class="text-end">
                      <!-- Qty Controls -->
                      <div class="d-flex align-items-center justify-content-end mb-1">
                          <button class="btn btn-sm btn-light border ms-1 decrease-qty-btn"
                              data-index="{{ $index }}" data-cart-id="{{ $item['id'] }}">
                              <i class="fas fa-minus fa-xs"></i>
                          </button>
                          <span class="fw-bold">{{ $item['quantity'] }}</span>
                          <button class="btn btn-sm btn-light border ms-1 increase-qty"
                              data-index="{{ $index }}" data-cart-id="{{ $item['id'] }}">
                              <i class="fas fa-plus fa-xs"></i>
                          </button>
                      </div>

                      <!-- Price -->
                      <div class="fw-bold text-primary">Rs: {{ number_format($item['subtotal'], 2) }}
                      </div>

                      <!-- Action Buttons -->
                      <div class="d-flex justify-content-end mt-1">
                          <button class="btn btn-sm btn-warning me-1 edit-item"
                              data-product-id="{{ $item['product_id'] }}" data-index="{{ $index }}"
                              data-cart-id="{{ $item['id'] }}" data-variant-id="{{ $item['product_variant_id'] }}"
                              data-size_id="{{ $item['size_id'] }}" data-addon='@json($item['addon'])'
                              data-ingredients='@json($item['ingredients'])'>
                              <i class="fas fa-edit"></i>
                          </button>

                          <button class="btn btn-sm btn-info me-1 item-note" data-index="{{ $index }}"
                              data-cart-id="{{ $item['id'] }}" data-bs-toggle="modal"
                              data-note="{{ $item['notes'] }}" data-bs-target="#itemNoteModal">
                              <i class="fas fa-sticky-note"></i>
                          </button>
                          <button class="btn btn-sm btn-danger remove-item" data-index="{{ $index }}"
                              data-cart-id="{{ $item['id'] }}">
                              <i class="fas fa-trash-alt"></i>
                          </button>
                      </div>
                  </div>
              </div>
          @endforeach
      </div>
  </div>

  <!-- Cart Footer -->
  <div class="border-top pt-3 mt-2">
      <div class="d-flex justify-content-between mb-1">
          <span>Subtotal:</span>
          <span class="fw-bold">Rs: {{ number_format($cart['grand_total'], 2) }}</span>
      </div>

      <div class="d-flex justify-content-between mb-1">
          <span>Tax: {{ $cart['tax_rate'] }}%</span>
          <span class="fw-bold">Rs: {{ number_format($cart['tax'], 2) }}</span>
      </div>

      <!-- Coupon / Discount Section -->
      @if (!empty($cart['discount']) && $cart['discount'] > 0)
          <!-- Show Discount Info -->
          <div class="d-flex justify-content-between mb-1 text-success fw-semibold">
              <span>Discount:</span>
              <span>- Rs: {{ number_format($cart['discount'], 2) }}</span>
          </div>

          @if (!empty($cart['discount_message']))
              <small class="text-success d-block mb-2 ms-1">

                  <i class="fas fa-tag me-1"></i>{{ $cart['discount_message'] }}
              </small>
          @endif

          <!-- Remove Discount Button -->
          <div class="text-end mb-2">
              <button type="button" class="btn btn-outline-danger btn-sm" id="remove_discount_btn">
                  <i class="fas fa-times me-1"></i> Remove Discount
              </button>
          </div>
      @else
          @if ($cart['cart'] != null)
              <!-- Coupon Input (Only visible if no discount applied) -->
              <div class="mt-3 p-2 bg-light rounded">
                  <label for="coupon_code" class="form-label small fw-semibold text-secondary mb-1">
                      Have a Coupon?
                  </label>
                  <div class="input-group">
                      <input type="text" id="coupon_code" class="form-control form-control-sm"
                          placeholder="Enter coupon code">
                      <button class="btn btn-success btn-sm" id="apply_coupon_btn">
                          Apply
                      </button>
                  </div>
                  <small class="text-success d-none mt-1" id="coupon_success_msg">Coupon applied successfully!</small>
                  <small class="text-danger d-none mt-1" id="coupon_error_msg">Invalid or expired coupon.</small>
              </div>
          @endif
      @endif

      <hr>

      <input type="hidden" id="get-final-total" value="{{ $cart['final_total'] }}">
      <div class="d-flex justify-content-between fs-5 mb-3">
          <span>Total:</span>
          <b class="text-success">Rs: {{ number_format($cart['final_total'], 2) }}</b>
      </div>

      <!-- Order Note Button -->
      <button class="btn btn-outline-secondary w-100 mb-2 add-order-note" data-note="{{ $cart['order_note'] ?? '' }}"
          data-bs-toggle="modal" data-cart-id="{{ $cart['cart_id'] }}" data-bs-target="#orderNoteModal">
          <i class="fas fa-sticky-note me-2"></i> Add Order Note
      </button>

      <!-- Checkout -->
      <button class="btn btn-primary w-100 py-2" id="proceed-checkout-btn" @if(isset($cart['cart']) && count($cart['cart']) > 0) @else disabled @endif>
          <i class="fas fa-credit-card me-2"></i> Pay Now
      </button>
  </div>
  <script>

document.addEventListener('DOMContentLoaded', function() {
    const cart = @json($cart);
    const taxRate = cart?.tax_rate;
    //console.log('Tax Rate:', taxRate);

    if (taxRate) {
        const paymentTypeSelect = document.getElementById('paymentType');
        const options = paymentTypeSelect.options;

        for (let i = 0; i < options.length; i++) {
            const option = options[i];
            if (option.dataset.tax == taxRate) {
                option.selected = true;
                break;
            }
        }
    }
});
  </script>
