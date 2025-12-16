<x-modal id="couponModal" title="Coupon Details">
    <div class="row g-3">

        {{-- Coupon Image --}}
        <div class="col-12 text-center mb-3">
            @if (getImageByType($coupon->images, 'full'))
                <img src="{{ getImageByType($coupon->images, 'full') }}" alt="Coupon Image"
                    class="img-fluid rounded shadow" style="max-height: 200px;">
            @else
                <p class="text-muted">No Image Available</p>
            @endif
        </div>

        {{-- Coupon Code --}}
        <div class="col-md-6">
            <label class="fw-bold">Coupon Code:</label>
            <p>{{ $coupon->code }}</p>
        </div>

        {{-- Discount --}}
        <div class="col-md-6">
            <label class="fw-bold">Discount:</label>
            <p>{{ $coupon->discount }}
                {{ $coupon->type == 'percentage' ? '%' : "" }}
            </p>
        </div>

        {{-- Product --}}
        {{-- Product --}}
        <div class="col-md-6">
            <label class="fw-bold">Product(s):  </label>
            @if (in_array('all', (array) $coupon->product_id))
                <p>All Products</p>
            @else
                <ul>
                    @foreach ($products as $product)
                        @if (in_array($product->id, (array) $coupon->product_id))
                            <li>{{ $product->name }}</li>
                        @endif
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Product Variant --}}
        <div class="col-md-6">
            <label class="fw-bold">Product Variant(s):</label>
            @if ($coupon->product_variant_id && count((array) $coupon->product_variant_id) > 0)
                <ul>
                    @foreach ($variants as $variant)
                        @if (in_array($variant->id, (array) $coupon->product_variant_id))
                            <li>{{ $variant->sizes->name }}</li>
                        @endif
                    @endforeach
                </ul>
            @else
                <p>-</p>
            @endif
        </div>


        {{-- Min Amount --}}
        <div class="col-md-6">
            <label class="fw-bold">Minimum Amount:</label>
            <p>{{ $coupon->min_amount ?? '-' }}</p>
        </div>

        {{-- Max Amount --}}
        <div class="col-md-6">
            <label class="fw-bold">Maximum Discount:</label>
            <p>{{ $coupon->max_amount ?? '-' }}</p>
        </div>

        {{-- Start Date --}}
        <div class="col-md-6">
            <label class="fw-bold">Start Date:</label>
            <p>{{ \Carbon\Carbon::parse($coupon->start_date)->format('d M Y h:i A') }}</p>
        </div>

        {{-- Expire Date --}}
        <div class="col-md-6">
            <label class="fw-bold">Expire Date:</label>
            <p>{{ \Carbon\Carbon::parse($coupon->expire_at)->format('d M Y h:i A') }}</p>
        </div>

        {{-- Max Usage --}}
        <div class="col-md-6">
            <label class="fw-bold">Max Usage:</label>
            <p>{{ $coupon->max_usage }}</p>
        </div>

        {{-- Status --}}
        <div class="col-md-6">
            <label class="fw-bold">Status:</label>
            <p>
                @if ($coupon->status)
                    <span class="badge bg-success">Active</span>
                @else
                    <span class="badge bg-danger">Inactive</span>
                @endif
            </p>
        </div>

        {{-- Description --}}
        <div class="col-12">
            <label class="fw-bold">Description:</label>
            <p>{{ $coupon->desc ?? 'No description provided.' }}</p>
        </div>
    </div>

    <div class="modal-footer mt-3">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    </div>
</x-modal>

