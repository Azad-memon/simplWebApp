<!DOCTYPE html>
<html lang="en">

<head>
    @include('admin.staff.partials.layouts.header')
    <style>
        .tender-btn {
  flex: 1 1 calc(20% - 10px);
  font-size: 15px;
  border-radius: 6px;
  padding: 10px 0;
  text-align: center;
  background-color: #fff;
  border: 1px solid #ddd;
  transition: all 0.2s ease;
  min-width: 80px;
}

.tender-btn:hover {
  background-color: #f3f7ff;
  border-color: #0d6efd;
  color: #0d6efd;
}

.tender-btn:active {
  background-color: #0d6efd;
  color: #fff;
  border-color: #0d6efd;
}

    </style>
</head>

<body>

    <div class="container-fluid mt-3">
        @include('admin.staff.partials.top-nev')
         @if (Auth::user()->role_id != 6)
        <div class="row">

            <!-- Sidebar: Categories -->
            <div class="col-md-2 bg-light shadow-sm p-3" style="height: 100vh; overflow-y: auto;">
                <h6 class="fw-bold mb-3">Categories</h6>
                <ul class="list-group category-list">
                    @foreach ($categories as $category)
                        @if (!in_array($category->name, ['Coffee', 'For you', 'New']))
                            <li class="list-group-item category-item" data-category="{{ $category->name }}">
                                <input class="form-check-input category-radio d-none" type="radio" name="category"
                                    value="{{ $category->name }}" id="cat-{{ $category->id }}">
                                <label class="form-check-label w-100 h-100" for="cat-{{ $category->id }}">
                                    <i class="fas fa-tags me-2"></i> {{ $category->name }}
                                </label>
                            </li>
                        @endif
                    @endforeach
                </ul>

            </div>

            <!-- Main Content -->
            <div class="col-md-7">

                <!-- Top Order Type Buttons -->
                <div class="btn-group mb-3 flex-wrap" role="group" aria-label="Order Type">
                    <input type="radio" class="btn-check" value="dine_in" name="order_type" id="dinein"
                        autocomplete="off" checked>
                    <label class="btn btn-outline-primary" for="dinein">Dine-In</label>

                    <input type="radio" class="btn-check" value="take_away" name="order_type" id="takeaway"
                        autocomplete="off">
                    <label class="btn btn-outline-primary" for="takeaway">Takeaway</label>
                    <input type="radio" class="btn-check" value="delivery" name="order_type" id="delivery"
                        autocomplete="off">
                    <label class="btn btn-outline-primary" for="delivery">Delivery</label>

                    {{--
                    <input type="radio" class="btn-check" value="online" name="order_type" id="online"
                        autocomplete="off">
                    <label class="btn btn-outline-primary" for="online">Online Order</label> --}}
                </div>

                <!-- Search -->
                <div class="product-search mb-3">
                    <div class="position-relative">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="form-control search-input" id="product-search"
                            placeholder="Search products...">
                    </div>
                </div>

                <!-- Products -->
                @foreach ($categories as $category)
                    <div class="products mt-4" data-category="{{ $category->name }}">
                        <h5 class="section-title">{{ $category->name }}</h5>
                        <div class="row g-3">
                           @foreach ($category->products->where('is_active', 1) as $product)
                            @php
                                $firstVariant = $product->variants->first();
                                $productVariants = $product->variants ?? collect();
                                $productSizes = $productVariants
                                    ->map(function ($variant) {
                                        $size = $variant->sizes;
                                        return [
                                            'variant_id' => $variant->id,
                                            'size_id' => $size->id ?? null,
                                            'code' => $size->code ?? null,
                                            'price' => $variant->price ?? 0,
                                        ];
                                    })
                                    ->values();

                                $sizeIdToCode = $productSizes
                                    ->filter(fn($ps) => !empty($ps['size_id']))
                                    ->mapWithKeys(fn($ps) => [$ps['size_id'] => $ps['code']])
                                    ->all();
                            @endphp

                            <div class="col-6 col-md-3">
                                <div class="product-card product-{{ $product->id }}"
                                    data-id="{{ $product->id }}"
                                    data-name="{{ $product->name }}"
                                    data-image="{{ asset($product->main_image) }}"
                                    data-price="{{ $firstVariant->price ?? 0 }}"
                                    data-productsizes='@json($productSizes)'>
                                    <div class="card-body p-3">
                                        <h5 class="card-title" style="font-size: 15px; white-space: nowrap;">
                                            {{ $product->name }}
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        </div>
                    </div>
                @endforeach

            </div>
            <div class="col-md-3 bg-white shadow-sm p-3 d-flex flex-column" style="height: 100vh; max-height: 100vh;">
                <!-- Cart Header -->
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <h5 class="fw-bold mb-0">🛒 Cart</h5>

                    <div class="d-flex align-items-center">
                        <select id="paymentType" class="form-select form-select-sm" style="width: 120px;">
                            <option value="cash" data-tax="10">💵 Cash</option>
                            <option value="card" data-tax="8">💳 Card</option>
                        </select>
                    </div>
                </div>

                <!-- Loader -->
                <div id="cart-loader"
                    style="position:absolute;top:0;left:0;right:0;bottom:0;
        background:rgba(255,255,255,0.7);z-index:9999;
        display:none;justify-content:center;align-items:center;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div id="get-cart">
                    @include('admin.staff.partials.cart')
                </div><!-- End cart -->
            </div>

            <!-- Item Note Modal -->
            <div class="modal fade" id="itemNoteModal" tabindex="-1" aria-labelledby="itemNoteModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Note to Item</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input class="save-item-note-id" type="hidden" value="">
                            <textarea id="item-note-text" class="form-control" rows="3" placeholder="Write note for this item..."></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary save-item-note"
                                data-cart-id="">Save</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Note Modal -->
            <div class="modal fade" id="orderNoteModal" tabindex="-1" aria-labelledby="orderNoteModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Note to Order</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input class="order-note-cart-id" type="hidden" value="">
                            <textarea id="order-note-text" class="form-control" rows="3" placeholder="Write note for this order..."></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary save-order-note">Save</button>
                        </div>
                    </div>
                </div>
            </div>


        </div>
        @endif
    </div>


    <!-- Navigation Bar -->
    <!-- Product Modal -->

    <div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="product-title">Product Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
  <div class="row">

    <!-- LEFT SIDE: Addons & Ingredients -->
    <div class="col-md-8">
      <div id="addons-ingredients-section">

      </div>


    </div>
      <!-- RIGHT  SIDE: Product Info -->
    <div class="col-md-4 border-end">
      <div class="text-center mb-3">
        <img id="product-image" src="" alt="Product"
             class="img-fluid rounded shadow-sm"
             style="max-height: 80px; object-fit: cover;">
      </div>
      <h4 id="product-name" class="mb-3 text-center"></h4>

      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5>Price: <span id="product-price" class="text-primary"></span></h5>
        <span class="badge bg-success">In Stock</span>
      </div>

      <div class="d-flex align-items-center justify-content-center mb-4">
        <button class="btn btn-outline-primary me-2" id="decrease-qty">-</button>
        <input type="number" class="form-control text-center" id="product-qty"
               value="1" min="1" style="width: 80px;">
        <button class="btn btn-outline-primary ms-2" id="increase-qty">+</button>
      </div>

      <div class="product-variants mt-3"></div>
       <textarea id="item-note-text-cart" class="form-control" rows="3" placeholder="Write note for this item..."></textarea>
        <div class="alert alert-info d-flex justify-content-between align-items-center mt-4">
                    <span class="fw-bold">Total Price:</span>
                    <span class="fs-5 fw-bold" id="product-total-price" data-base-price="0">Rs:0</span>
        </div>
       <!-- ACTION BUTTONS -->
      <div class="d-flex justify-content-end mt-3 align-items-end  h-100 py-5">
        <button class="btn btn-outline-secondary me-2" data-bs-dismiss="modal" >Cancel</button>
        <button class="btn btn-primary" id="add-to-cart-btn" style="pointer-events: none; opacity: 0.6;" >
          <i class="fas fa-cart-plus me-2"></i>Add to Cart
        </button>
      </div>
    </div>

  </div>
</div>
    </div>
 </div>
</div>
    <div class="modal fade" id="checkoutModal" tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">Checkout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form id="checkoutForm">
                        <div class="mb-3">
                            <label class="form-label">Customer Name</label>
                            <input type="text" class="form-control" name="customer_name" required   id="customer-name">
                            <input type="hidden" name="customer_id" id="customer-id">
                             <small id="name-error" style="color: red; display: none;">Maximum 30 characters allowed.</small>
                        </div>

                        <div class="mb-3 position-relative">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control" name="customer_phone" required
                                id="customer-phone" autocomplete="off">
                            <div id="customer-suggestions" class="list-group position-absolute w-100 shadow-sm"
                                style="z-index: 1000; display:none;"></div>
                        </div>


                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="customer_email" id="customer-email">
                        </div>
                        <div class="mb-3 hide" id="card-number-container">
                            <label class="form-label">Card Number First 5 digits</label>
                            <input type="text" class="form-control" name="card_number" id="card-number">
                        </div>

                        <hr class="my-3">

                        <!-- New Section: Billing Details -->
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Total Amount</label>
                                <input type="number" class="form-control text-end bg-light" id="total-amount"
                                    name="total_amount" readonly disabled>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Amount Received</label>
                                <input type="number" class="form-control text-end" id="amount-received"
                                      step="any"
                                    name="amount_received" placeholder="Enter received amount" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Change to Return</label>
                                <input type="number" class="form-control text-end bg-light" id="change-return"
                                    name="change_return" readonly>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label fw-semibold">Quick Amounts</label>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-secondary sameamount flex-fill tender-btn" data-value="total">Same</button>
                                <button type="button" class="btn btn-outline-secondary flex-fill tender-btn" data-value="100">100</button>
                                <button type="button" class="btn btn-outline-secondary flex-fill tender-btn" data-value="500">500</button>
                                <button type="button" class="btn btn-outline-secondary flex-fill tender-btn" data-value="1000">1000</button>
                                <button type="button" class="btn btn-outline-secondary flex-fill tender-btn" data-value="5000">5000</button>
                            </div>
                            </div>

                        <hr class="my-3">

                        {{-- <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select class="form-select" name="payment_method" required id="payment-method">
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                            </select>
                        </div> --}}

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary w-50 me-2" id="submitAndPrintBtn">
                                <i class="fas fa-print me-1"></i> Submit & Print
                            </button>
                            <button type="button" class="btn btn-secondary w-50"
                                data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- POS Receipt Modal -->




    <input type="hidden" id="cart-item-id" value="">


    <!-- Scripts -->
    @include('admin.staff.partials.layouts.script')
</body>

</html>
