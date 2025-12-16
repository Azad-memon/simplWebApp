<style>
    .receipt {
        width: 120mm;
        margin: auto;
        font-family: "Arial", "Helvetica", sans-serif;
        font-size: 14px; /* Increased for readability */
        color: #000;
        background: #fff;
        padding: 10px;
        border-radius: 6px;
        line-height: 1.4; /* improves spacing */
    }

    .receipt h5 {
        font-size: 18px;
        font-weight: bold;
        text-align: center;
        margin-bottom: 6px;
        letter-spacing: 1px;
    }

    .receipt p,
    .receipt span,
    .receipt small {
        font-size: 18px;
        margin: 0;
    }

    .receipt small {
        font-size: 16px;
    }

    .receipt .text-center {
        text-align: center;
    }

    .receipt .product-border {
        border-bottom: 1px dashed #999;
        margin: 10px 0;
        padding-bottom: 6px;
    }

    .receipt .d-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .receipt .text-muted {
        color: #555;
    }

    .receipt .text-black {
        color: #000;
    }

    .receipt .text-green {
        color: #008000;
        font-weight: 600;
    }

    .receipt .totals {
        border-top: 1px solid #000;
        margin-top: 10px;
        padding-top: 8px;
    }

    .receipt .note {
        margin-top: 10px;
        border-top: 1px dashed #ccc;
        padding-top: 6px;
        font-size: 13px;
    }

    .receipt .barcode {
        text-align: center;
        margin-top: 10px;
    }

    .receipt .barcode img {
        width: 160px;
        height: auto;
    }

    .receipt .print-btn {
        display: block;
        width: 100%;
        margin-top: 10px;
        padding: 8px;
        background-color: #000;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }

    .receipt .print-btn:hover {
        background-color: #333;
    }

    .receipt table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .receipt th,
    .receipt td {
        padding: 4px 0;
    }

    .receipt th {
        text-align: left;
        border-bottom: 1px solid #999;
    }

    .receipt td p {
        margin: 0;
    }
</style>
<div class="receipt">
    <div>
        <h5>{{ env('APP_NAME') }} STORE</h5>
        {{-- <p class="text-center">
            GST: 29ABCDE1234F1Z5<br>
            FSSAI: 1212HBJHB664A
        </p> --}}
    </div>

    <div class="product-border">
        <p><b>Date:</b> {{ now()->format('d-m-Y H:i') }}</p>
        <p><b>Invoice:</b> #{{  $order->order_uid }}</p>
    </div>

    @php
        $subtotal = 0;
        // dump($cart);
        // exit;
    @endphp
    @foreach ($cart as $item)
        @php
            $itemSubtotal = $item->price * $item->quantity;

            // Addon subtotal (quantity included)
            $addonSubtotal = 0;
            $sizeid = $item->productVariant->sizes->id;

        @endphp
        <div>
            <div class="p-0">
                <div class="d-flex">
                    <div>{{ $item->productVariant->product->name ?? 'N/A' }}</div>
                    @if (!empty($item->productVariant->sizes->name))
                        <small class="text-muted">Size: {{ $item->productVariant->sizes->name ?? 'N/A' }}</small>
                    @endif
                </div>

                <div class="d-flex">
                    <span class="text-black">QTY: {{ $item->quantity }}</span>
                    <span>Product Price: {{ number_format($item->price * $item->quantity, 2) }}</span>
                </div>
                @php
                    $addonDetail = getIngredientDetails($item->addon_id, true, $sizeid);
                    $removedIngredientsDetails = getIngredientDetails($item->removed_ingredient_ids, true, $sizeid);
                @endphp
                {{-- Addons --}}
                @php
                    $addonArray = json_decode($item->addon_id, true) ?? [];
                    $addonSubtotal = 0;
                @endphp

                @if (!empty($addonDetail) && count($addonDetail) > 0)
                    @foreach ($addonDetail as $addon)
                        @php
                            //dump( $addon);
                            $match = collect($addonArray)->firstWhere('ing_id', $addon['id']);
                            //dump($match );
                            $price = $match['price'] ?? 0;
                        @endphp
                        @if ($match)
                            @php
                                $addonSubtotal += $price * ($item->quantity ?? 1);
                              //  $addonSubtotal_all += $addonSubtotal;
                            @endphp
                        @endif

                        <div class="d-flex">
                            <div class="text-green">+  {{ $addon['name'] }}
                                <small>(Qty:{{ $item->quantity }})</small>
                            </div>
                            <div><small class="text-muted">(Rs {{ number_format($price, 2) }} x
                                    {{ $item->quantity }}) </small></div>
                        </div>
                    @endforeach
                @endif
            </div>

            @if (!empty($item->notes))
                <div class="product-note">
                    <small class="text-muted d-block">📝 {{ $item->notes ?? 'N/A' }}</small>
                </div>
            @endif

            <div class="product-border d-flex">
                <span class="text-black">Subtotal:</span>
                <span>{{ number_format( $item->total_price, 2) }}</span>
            </div>
        </div>

        @php $subtotal += $itemSubtotal; @endphp
    @endforeach

    @php
        $tax = $order->tax ?? $subtotal * 0.1;
      //  $grandTotal = $subtotal + $tax;
    @endphp

    <div class="totals">
        <div class="d-flex">
            <p><b>Tax:</b> Rs {{ number_format($tax, 2) }}</p>
            <p><b>Total:</b> Rs {{ number_format($order->final_amount, 2) }}</p>
        </div>

        <table class="mb-0">
            <thead>
                <tr>
                    <th>Paid By</th>
                    <th class="text-end">Amount</th>
                    <th class="text-end">Change Return</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Cash</td>
                    <td class="text-end">
                        <p>Rs {{ number_format($order->final_amount, 2) }}</p>
                    </td>
                    <td class="text-end">
                        <p>Rs {{ number_format($order->change_return, 2) }}</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    @if (!empty($order->order_note))
        <div class="note"><b>Note:</b> {{ $order->order_note }}</div>
    @endif

    <div class="barcode">
        <img src="https://barcode.tec-it.com/barcode.ashx?data={{ $order->order_uid }}" alt="barcode">
        <p>Thank you for visiting!</p>
    </div>


</div>
