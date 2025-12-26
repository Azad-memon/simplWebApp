<!DOCTYPE html>
<html>
<head>
<style>

  @page {
    size: 58mm 30mm;   /* smaller height */
    margin: 0;


  }
  .page-break {
    page-break-after: always !important;
    break-after: page !important;
}



    html, body {
        width: 218px !important;   /* 58mm */
        height: 113px !important;  /* 30mm */
        margin: 0 !important;
        padding: 0 !important;
        overflow: hidden !important;
        font-family: Arial, sans-serif;
    }

    /* LABEL BOX */
    .label {
       width: 200px !important;
        height: 95px !important;
        padding: 0px 5px !important;
        margin: 0 !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
        position: relative;
        top: 0 !important;
        left: 0 !important;
    }

    /* TEXT STYLES */
    .top-row,
    .code-line,
    .item-name,
    .mid-row,
    .footer {
        margin: 0 !important;
        padding: 0 !important;
        line-height: 1.1 !important;
    }

    .top-row {
        font-size: 9px !important;
        font-weight: bold !important;
        display: flex !important;
        justify-content: space-between !important;
    }

    .code-line {
        font-size: 9px !important;
        font-weight: bold !important;
        text-align: right !important;
    }

    .item-name {
        font-size: 11px !important;
        font-weight: bold !important;
        margin-top: 2px !important;
    }

    .mid-row {
        font-size: 9px !important;
        display: flex !important;
        justify-content: space-between !important;
        margin-top: 2px !important;
    }

    .footer {
        font-size: 8px !important;
        display: flex !important;
        justify-content: space-between !important;
        margin-top: 3px !important;
    }
@media print {

    /* FORCE EXACT LABEL SIZE */
     html, body {
        width: auto !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: hidden !important;
    }


    .label {
        width: 200px !important;
        height: auto !important;
        padding: 0 5px !important;
        margin: 0 auto 5px !important;
        position: static !important;     /* FIX */
        transform: none !important;      /* FIX */
        page-break-before: always !important; /* EACH LABEL NEW PAGE */
    }
        .order-queue {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 18px;
        height: 18px;
        background-color: #333; /* dark color */
        color: #fff; /* text color */
        border-radius: 50%; /* circle */
        font-weight: bold;
        font-size: 10px;
        text-align: center;
    }


    /* TEXT STYLES */
    .top-row,
    .code-line,
    .item-name,
    .mid-row,
    .footer {
        margin: 0 !important;
        padding: 0 !important;
        line-height: 1.1 !important;
    }

    .top-row {
        font-size: 9px !important;
        font-weight: bold !important;
        display: flex !important;
        justify-content: space-between !important;
    }

    .code-line {
        font-size: 12px !important;
        font-weight: bold !important;
        text-align: right !important;
    }

    .item-name {
        font-size: 11px !important;
        font-weight: bold !important;
        margin-top: 2px !important;
    }

    .mid-row {
        font-size: 10px !important;
        display: flex !important;
        justify-content: space-between !important;
        margin-top: 2px !important;
    }

    .footer {
        font-size: 8px !important;
        display: flex !important;
        justify-content: space-between !important;
        margin-top: 10px !important;
    }
     .powerby {
        font-size: 6px !important;
        justify-content: space-between !important;
        margin-top: 10px !important;
        text-align: center !important;
    }
    .customer-name{
    font-size: 10px;
    font-weight: bold;
}
}

</style>
</head>

<body>



{{-- @for ($i = 1; $i < $quantity; $i++) --}}
<div class="label">

    <!-- Logo -->
    <div class="label-logo" style="text-align: center; margin-bottom: 3px;">
        <img src="https://pos.eatsimpl.co/Simpl.png" alt="Logo" style="max-width: 20px; height: auto;">
    </div>

    <div class="top-row">
        <div class="order-queue">{{ $order->queue_number ?? '' }}</div>
        <div>{{ $order->order_type_label }}</div>
        <div> <span id="total-quantity_{{ $i }}">{{ $i }}</span>/{{ $quantity }}</div>
    </div>

    <div class="item-name">
        {{ $item->productVariant->product->name }}
        ({{ $item->productVariant->product->category->name ?? '-' }})
    </div>

    <div class="mid-row">
        <div>{{ $item->productVariant->sizes->name ?? '-' }}</div>
    </div>

    <div class="mid-row">
        @php
            $addonDetail = getIngredientDetails($item->addon_id, true ,$item->productVariant->sizes->name);
        @endphp
        @if (!empty($addonDetail))
            @foreach ($addonDetail as $addon)
           <div>{{ $addon['label_name'] }}</div>
            @endforeach
        @endif
    </div>

    <div class="footer">
        <div>{{ now()->format('d/m/Y H:i') }}</div>
        <div>{{ $item->order->order_uid ?? '' }}</div>
    </div>
     <div class="customer-name">
    {{ $order->customer_name ?? '' }}
    </div>
     <div class="powerby" style="text-align: center">
        <div>Powered by DoodlenDash</div>

    </div>

</div>
{{-- @endfor --}}



<script>
window.onload = function () {
    const labels = document.querySelectorAll('.label');
    let index = 1;

    function printNext() {
        if (index > {{ $quantity }}) {
            window.close();
            //window.location.href = "{{ route('pos.index') }}";
            return;
        }

        let currentLabel = labels[index - 1];

        // show only current label
        currentLabel.style.display = "block";

        // correct element from INSIDE the label
        let el = currentLabel.querySelector('#total-quantity_' + index);
        if (el) {
            el.textContent = index; // correct counter
        }

        window.print();

        setTimeout(() => {

            currentLabel.style.display = "none"; // hide after print
            index++;
            printNext();

        }, 700);
    }

    // hide all labels first
    labels.forEach(l => l.style.display = "none");

    printNext();
};


</script>
</body>
</html>
