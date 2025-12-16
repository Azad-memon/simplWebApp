  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <style>
      body {
          font-family: 'Poppins', sans-serif;
      }
  </style>
  @php
      use App\Models\Order;
  @endphp


  <div class="kds-row" id="kdsContainer">
    @if(count($orders)!=0)
      @foreach ($orders as $order)
          <div class="order-card" id="order_{{ $order->id }}">
              <div class="order-header">
                  <div class="order-id">
                      <h3>Order #{{ $order->order_uid }}</h3>
                      <small>{{ $order->created_at->format('h:i A') }}</small>
                  </div>
                  @if ($order->status === Order::STATUS_PROCESSING)
                      <button class="mark-ready-btn btn-processing" data-id="{{ $order->id }}"
                          data-status="{{ Order::STATUS_PROCESSING }}">
                          <i class="fas fa-coffee"></i> {{ strtoupper(Order::STATUS_PROCESSING) }}
                      </button>
                  @elseif ($order->status === Order::STATUS_PREPARING)
                      <button class="mark-ready-btn btn-preparing" data-id="{{ $order->id }}"
                          data-status="{{ Order::STATUS_PREPARING }}">
                          <i class="fas fa-coffee"></i> {{ strtoupper(Order::STATUS_PREPARING) }}
                      </button>
                  @endif
              </div>


             <div class="order-body">
                @foreach ($order->items as $item)
                    <div class="order-item">
                        <div class="item-main">
                            <span class="item-name">{{ $item->productVariant->product->name ?? 'N/A' }}</span>
                            <span class="item-qty">Qty: <strong>{{ $item->quantity }}</strong></span>
                            <span class="item-size">
                                Size: <strong>{{ $item->productVariant->sizes->name ?? 'N/A' }}</strong>
                            </span>
                        </div>

                        @php
                            $sizeid = $item->productVariant->sizes->id ?? null;
                            $addonDetail = getIngredientDetails($item->addon_id, true, $sizeid);
                            $removedIngredientsDetails = getIngredientDetails($item->removed_ingredient_ids, true, $sizeid);
                        @endphp

                        @if (!empty($addonDetail) && count($addonDetail) != 0)
                            <ul class="addon-list">
                                <strong style="color: red;">Addons:</strong>
                                @foreach ($addonDetail as $addon)
                                    <li>+ {{ $addon['name'] }}</li>
                                @endforeach
                            </ul>
                        @endif

                        {{-- Uncomment if you want to show removed ingredients --}}
                        {{--
                        @if (!empty($removedIngredientsDetails))
                            <ul class="removed-ingredient-list">
                                <strong>Removed:</strong>
                                @foreach ($removedIngredientsDetails as $removed)
                                    <li>- {{ $removed['name'] }}</li>
                                @endforeach
                            </ul>
                        @endif
                        --}}

                        @if (!empty($item->notes))
                            <p class="item-note"><strong>Note:</strong> {{ $item->notes }}</p>
                        @endif
        </div>
    @endforeach
     {{-- Overall Order Note --}}
    @if (!empty($order->order_note))
        <div class="order-note">
            <strong>Order Note:</strong>
            <p>{{ $order->order_note }}</p>
        </div>
    @endif
</div>

          </div>
      @endforeach
      @else
        <p class="no-orders">No orders available.</p>
      @endif
  <input type="hidden" id="order_id" value="{{ $order->id ?? '' }}">

  </div>

  <style>


        .item-note {
        word-wrap: break-word;
        /* white-space: pre-wrap; */
        overflow-wrap: break-word;
        margin-top: 5px;
        font-size: 13px;
        color: #333;
        line-height: 1.4;
    }

    .order-note {
        margin-top: 15px;
        padding: 8px 10px;
        border-top: 1px dashed #aaa;
        background: #f9f9f9;
        font-size: 14px;
        word-wrap: break-word;
        /* white-space: pre-wrap; */
        overflow-wrap: break-word;
    }

    .order-note strong {
        display: block;
        margin-bottom: 4px;
        color: #d9534f;
    }
.kds-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr); /* <-- exactly 4 boxes per row */;
    gap: 1.2rem;
    padding: 1.5rem;
    background: #f6f7fb;
}
      .order-card {
            height: 300px;
          overflow: scroll;
          background: #fff;
          border-left: 10px solid #ff9800;
          border-radius: 15px;
          padding: 1.2rem 1.5rem;
          box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
          transition: transform 0.2s ease, box-shadow 0.2s ease;
      }

      .order-card:hover {
          transform: scale(1.01);
          box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
      }

      .order-header {
          display: flex;
          justify-content: space-between;
          align-items: center;
          background: #fff8e1;
          border-radius: 10px;
          padding: 10px 15px;
          margin-bottom: 15px;
      }

      .order-id h3 {
          margin: 0;
          font-weight: 700;
          color: #222;
      }

      .order-id small {
          color: #777;
          font-size: 13px;
      }

      .order-body {
          display: flex;
          flex-direction: column;
          gap: 8px;
      }

      .order-item {
          background: #f9f9f9;
          padding: 10px 12px;
          border-radius: 10px;
          border: 1px solid #e3e3e3;
      }

      .item-main {
          display: flex;
          justify-content: space-between;
          align-items: center;
          font-size: 15px;
          font-weight: 600;
          color: #333;
      }

      .item-name {
          flex: 1;
          font-size: 16px;
      }

      .item-qty,
      .item-size {
          margin-left: 10px;
          color: #555;
          font-size: 14px;
      }

      .addon-list,
      .removed-ingredient-list {
          margin: 5px 0 0 15px;
          padding: 0;
          font-size: 13px;
          color: #444;
      }

      .addon-list li,
      .removed-ingredient-list li {
          list-style: none;
      }

      /* .mark-ready-btn {
          background-color: #6f4e37;
          border: none;
          color: white;
          font-weight: bold;
          font-size: 22px;
          padding: 18px 35px;
          border-radius: 12px;
          cursor: pointer;
          transition: background 0.3s ease;
      } */

      .mark-ready-btn:hover {
          background-color: #6f4e37;
      }


        .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fff8e1;
        border-radius: 8px;
        padding: 8px 12px;
        margin-bottom: 10px;
    }

    .order-id {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .order-id h3 {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
        color: #333;
    }

    .order-id h3 span {
        display: block;
        font-size: 16px;
        font-weight: 700;
        color: #000;
        margin-top: 2px;
    }

    .order-id small {
        color: #666;
        font-size: 12px;
        margin-top: 2px;
    }

.mark-ready-btn {
    border: none;
    color: #fff;
    font-weight: 600;
    font-size: 16px; /* 🔹 Font size barhaya */
    padding: 10px 18px; /* 🔹 Padding barhayi */
    border-radius: 8px; /* 🔹 Thoda zyada rounded look */
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-processing {
    background-color: #f4b400;
}

.btn-processing:hover {
    background-color: #dba100;
}

.btn-preparing {
    background-color: #4285f4;
}

.btn-preparing:hover {
    background-color: #2b63c0;
}
/* --------------------------------------
   TABLET VIEW (Width ≤ 1024px)
--------------------------------------- */
@media (max-width: 1024px) {
    .kds-row {
        grid-template-columns: repeat(2, 1fr);  /* 2 cards per row */
        gap: 1rem;
        padding: 1rem;
    }

    .order-card {
        height: 260px;          /* Slightly smaller height */
        padding: 1rem;
        border-left-width: 8px;
    }

    .order-header {
        padding: 6px 10px;
    }

    .order-id h3 span {
        font-size: 15px;
    }

    .mark-ready-btn {
        font-size: 15px;
        padding: 8px 14px;
        border-radius: 6px;
    }

    .item-main {
        font-size: 14px;
    }
}

/* --------------------------------------
   SMALL TABLET / LARGE MOBILE (≤ 768px)
--------------------------------------- */
@media (max-width: 768px) {
    .kds-row {
        grid-template-columns: repeat(1, 1fr); /* 1 card per row */
        gap: 0.8rem;
        padding: 0.8rem;
    }

    .order-card {
        height: auto;          /* Allow auto height on smaller screens */
        overflow: visible;
        padding: 0.8rem;
    }

    .order-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }

    .order-id h3 span {
        font-size: 14px;
    }

    .mark-ready-btn {
        width: 100%;
        justify-content: center;
        padding: 10px;
        font-size: 14px;
    }

    .item-main {
        font-size: 13px;
    }

    .order-item {
        padding: 8px 10px;
    }
}
@media (min-width: 1024px) and (max-width: 1280px) {
    .kds-row {
        grid-template-columns: repeat(3, 1fr); /* 3 cards per row */
        gap: 1.2rem;
        padding: 1.2rem;
    }

    .order-card {
        height: auto;
        padding: 1rem;
    }

    .mark-ready-btn {
        font-size: 15px;
        padding: 12px;
    }.

    .item-main {
        font-size: 14px;
    }
}


  </style>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
      function getKdsOrders() {
    // Get current branch ID (assuming it's available globally or in a hidden input)
     let branchId = $('#branch_id').val() || "{{ auth()->user()->branchstaff->first()->branch_id ?? '' }}";
     let lastOrderId = $('input[id^="order_id"]').last().val();
    //  if(lastOrderId!=""){
    $.get("{{ route('staff.kds-refresh') }}", { branch_id: branchId, order_id: lastOrderId }, function(res) {
        $('#kdsContainer').html(res.html);
    });
 //}
}

      // Optional auto-refresh every 30s
      getKdsOrders()
      setInterval(() => {
          getKdsOrders();
      }, 2000);
      // $(document).on('click', '.mark-ready-btn', function() {
      //     const id = $(this).data('id');
      //     const btn = $(this);

      //     btn.prop('disabled', true).text('Updating...');

      //     $.ajax({
      //         url: "{{ route('staff.mark-ready') }}",
      //         method: "POST",
      //         data: {
      //             id: id,
      //             _token: "{{ csrf_token() }}"
      //         },
      //         success: function(res) {
      //             if (res.success) {
      //                 btn.removeClass('btn-outline-success')
      //                    .addClass('btn-success')
      //                    .text('✅ Ready');
      //             } else {
      //                 btn.prop('disabled', false).text('Mark Ready');
      //                 alert('Failed to update status.');
      //             }
      //         },
      //         error: function() {
      //             btn.prop('disabled', false).text('Mark Ready');
      //             alert('Error updating order.');
      //         }
      //     });
      // });
      $(document).on('click', '.mark-ready-btn', function() {
          let button = $(this);
          let id = button.data('id');
          let currentStatus = button.data('status');
          let nextStatus = '';

          // Determine next status
          if (currentStatus === 'processing') {
              nextStatus = 'preparing';
             // printKitchenOrder(id);
          } else if (currentStatus === 'preparing') {
              nextStatus = 'ready';
          } else {
              return; // No further update allowed
          }

            $.ajax({
                url: "{{ route('staff.mark-ready') }}",
                method: 'POST',
                data: {
                    id: id,
                    _token: '{{ csrf_token() }}',
                    status: nextStatus
                },
                success: function(response) {
                    if (response.status === true) {
                        button.data('status', nextStatus);
                        button.text(nextStatus.toUpperCase());

                        // Reset any previous color classes
                        button.removeClass(
                            'btn-warning btn-primary btn-success btn-processing btn-preparing');

                        // Apply new color based on next status
                        if (nextStatus === 'processing') {
                            button.addClass('btn-processing');
                        } else if (nextStatus === 'preparing') {
                            button.addClass('btn-preparing');
                        } else if (nextStatus === 'ready') {
                            button.addClass('btn-success');
                        }

                        // Animate remove if status becomes "dispatched"
                        if (nextStatus === 'ready') {
                            let orderBox = $('#order_' + id);
                            orderBox.css('background-color', '#d4edda'); // light green flash

                            // Small delay before fade-out for a smoother effect
                            setTimeout(() => {
                                orderBox.animate({
                                        opacity: 0,
                                        marginLeft: '30px',
                                    },
                                    800,
                                    function() {
                                        orderBox.slideUp(400, function() {
                                            $(this).remove();
                                        });
                                    }
                                );
                            }, 700);
                        }
                        // Refresh order list if needed
                        getKdsOrders();
                    }
                }
            });
      });
      function printKitchenOrder(id) {
    let printContent = `
    <div class="receipt" style="width:75mm; font-family:Arial, sans-serif; font-size:14px; line-height:1.4; padding:8px;">
        <h3 style="text-align:center; margin:0 0 5px 0;">LOOP STORE - KITCHEN</h3>
        <p style="margin:2px 0;">Order #: ${id}</p>
        <p style="margin:2px 0;">Status: <strong>PREPARING</strong></p>
        <hr style="border:none; border-top:1px dashed #000; margin:4px 0;">
    `;

    // Loop through items
    $('#order_' + id + ' .order-item').each(function() {
        let itemName = $(this).find('.item-name').text().trim();
        let itemQty = $(this).find('.item-qty').text().trim();
        let itemSize = $(this).find('.item-size').text().trim();

        printContent += `
        <div style="margin-bottom:5px;">
            <div><strong>${itemName}</strong></div>
            <div style="margin-left:5px;">${itemQty} | ${itemSize}</div>
        `;

        // Addons
        if ($(this).find('.addon-list li').length > 0) {
            printContent += `<div style="margin-left:10px;">Addons:</div><ul style="margin:0; padding-left:15px;">`;
            $(this).find('.addon-list li').each(function() {
                printContent += `<li>${$(this).text().trim()}</li>`;
            });
            printContent += `</ul>`;
        }

        // Removed Ingredients
        if ($(this).find('.removed-ingredient-list li').length > 0) {
            printContent += `<div style="margin-left:10px;">Removed:</div><ul style="margin:0; padding-left:15px;">`;
            $(this).find('.removed-ingredient-list li').each(function() {
                printContent += `<li>${$(this).text().trim()}</li>`;
            });
            printContent += `</ul>`;
        }

        printContent += `<hr style="border:none; border-top:1px dashed #000; margin:4px 0;">`;
    });

    printContent += `
        <p style="text-align:center; margin-top:5px;">--- THANK YOU ---</p>
    </div>`;

    // Print
    let printWindow = window.open('', '_blank', 'width=400,height=600');
    printWindow.document.write(printContent);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
}

  </script>
