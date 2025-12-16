<x-modal id="couponModal" title="Add / Edit Coupon">
    <form method="POST" action="{{ route('admin.coupons.store') }}" id="coupon-form">
        <div class="row g-3">

        <div class="mb-3" id="mediaFields">
        <x-image-upload id="full" name="full" :value="$banner->full ?? null"  :is_banner="true"/>
        </div>
            {{-- Coupon Code --}}
            <div class="col-md-6">
                <label for="code" class="form-label">Coupon Code</label>
                <input type="text" class="form-control" id="code" name="code"
                    placeholder="Enter coupon code" required>
            </div>

            {{-- Discount --}}
            <div class="col-md-6">
                <label for="discount" class="form-label">Discount</label>
                <input type="number" step="0.01" class="form-control" id="discount" name="discount"
                    placeholder="Enter discount" required>
            </div>

            {{-- Discount Type --}}
            <div class="col-md-6">
                <label for="type" class="form-label">Discount Type</label>
                <select class="form-select" id="type" name="type" required>
                    <option value="">Select Type</option>
                    <option value="percentage">Percentage</option>
                    <option value="fixed">Fixed Amount</option>
                </select>
            </div>

            {{-- Product --}}
            <div class="col-md-6">
                <label for="product_id" class="form-label">Product</label>
                <select class="form-select" id="product_id" name="product_id[]" multiple>
                    <option value="">Select Product</option>
                    <option value="all">All</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Variant --}}
            <div class="col-md-6" id="variant-wrapper">
                <label for="product_variant_id" class="form-label">Product Variant</label>
                <select class="form-select" id="product_variant_id" name="product_variant_id[]" multiple>
                    <option value="">Select Variant</option>
                </select>
            </div>

            {{-- Min & Max Amount --}}
            <div class="col-md-6">
                <label for="min_amount" class="form-label">Minimum Amount</label>
                <input type="number" step="0.01" class="form-control" id="min_amount" name="min_amount" value="0">
            </div>

            <div class="col-md-6">
                <label for="max_amount" class="form-label">Maximum Discount Amount</label>
                <input type="number" step="0.01" class="form-control" id="max_amount" name="max_amount" value="0">
            </div>

            {{-- Dates --}}
            <div class="col-md-6">
                <label for="start_date">Start Date</label>
                <input type="datetime-local" name="start_date" id="start_date" class="form-control"
                    value="{{ old('start_date', isset($coupon) ? \Carbon\Carbon::parse($coupon->start_date)->format('Y-m-d\TH:i') : '') }}">
            </div>

            <div class="col-md-6">
                <label for="expire_at" class="form-label">Expire Date</label>
                <input type="datetime-local" class="form-control" id="expire_at" name="expire_at" required
                    value="{{ old('expire_at', isset($coupon) ? \Carbon\Carbon::parse($coupon->start_date)->format('Y-m-d\TH:i') : '') }}">
            </div>

            {{-- Max Usage --}}
            <div class="col-md-6">
                <label for="max_usage" class="form-label">Max Usage</label>
                <input type="number" class="form-control" id="max_usage" name="max_usage" value="1" required>
            </div>

            {{-- Status --}}
            <div class="col-md-6">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>

            {{-- Description --}}
            <div class="col-12">
                <label for="desc" class="form-label">Description</label>
                <textarea class="form-control" id="desc" name="desc" rows="2"></textarea>
            </div>

        </div>

        <div class="modal-footer mt-3">
            <button type="submit" class="btn btn-primary">Save Coupon</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                aria-label="Close">Close</button>
        </div>
    </form>
</x-modal>


<script>
    $(document).ready(function() {
    $('#product_id').on('change', function() {
    let productIds = $(this).val();
    if (productIds && productIds.length === 1) {
        let productId = productIds[0];
        $.ajax({
            url: "{{ route('admin.coupons.products.variants') }}",
            type: "GET",
            data: {
                product_id: productId
            },
            success: function(res) {
                //console.log(res);
                let $variantSelect = $('#product_variant_id');
                $variantSelect.empty().append('<option value="">Select Variant</option>');
                if (res && res.length > 0) {
                    $.each(res, function(index, variant) {
                        $variantSelect.append(
                            `<option value="${variant.id}">${variant.sizes.name}</option>`
                        );
                    });
                    $('#variant-wrapper').show();
                } else {
                    $('#variant-wrapper').hide();
                }
            }
        });
    } else {

        $('#variant-wrapper').hide();
        $('#product_variant_id').empty();
    }
});
//  $('#product_id').select2();

//     $('#product_id').on('change', function () {
//         let selected = $(this).val();

//         if (selected && selected.includes("all")) {
//             // Select all values (All + Products)
//             let allValues = [];
//             $('#product_id option').each(function () {
//                 allValues.push($(this).val());
//             });

//             $('#product_id').val(allValues).trigger('change.select2');

//             // Disable all except "All"
//             $('#product_id option').not('[value="all"]').prop('disabled', true);
//         } else {
//             // Enable all again
//             $('#product_id option').prop('disabled', false);
//         }

//         // Refresh select2
//         $('#product_id').select2();
//     });

    });

</script>
