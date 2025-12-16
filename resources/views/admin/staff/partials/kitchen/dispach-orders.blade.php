
    @php
    use App\Models\Order;
@endphp
       @if (!empty($orders))
       @foreach ($orders as $order)
          <div class="order-card" id="order_{{ $order->id }}">
               <div class="order-header">
                  <div class="order-id">
                      <h3>Order #{{ $order->order_uid }}</h3>
                      <small>{{ $order->created_at->format('h:i A') }}</small>
                  </div>
                  @if ($order->status === Order::STATUS_READY)
                      <button class="mark-ready-btn btn-preparing" data-id="{{ $order->id }}"
                          data-status="{{ Order::STATUS_READY }}">
                          <i class="fas fa-coffee"></i> {{ strtoupper('Ready') }}
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
                              <span class="item-size">Size:
                                  <strong>{{ $item->productVariant->sizes->name ?? 'N/A' }}</strong></span>
                          </div>

                          @php
                              $sizeid = $item->productVariant->sizes->id ?? null;
                              $addonDetail = getIngredientDetails($item->addon_id, true, $sizeid);
                              $removedIngredientsDetails = getIngredientDetails(
                                  $item->removed_ingredient_ids,
                                  true,
                                  $sizeid,
                              );
                          @endphp

                          @if (!empty($addonDetail))
                              <ul class="addon-list">
                                  <strong style="color: red;">Addons:</strong>
                                  @foreach ($addonDetail as $addon)
                                      <li>+ {{ $addon['name'] }}</li>
                                  @endforeach
                              </ul>
                          @endif

                          {{-- @if (!empty($removedIngredientsDetails))
                              <ul class="removed-ingredient-list">
                                  <strong>Removed:</strong>
                                  @foreach ($removedIngredientsDetails as $removed)
                                      <li>- {{ $removed['name'] }}</li>
                                  @endforeach
                              </ul>
                          @endif --}}
                           @if (!empty($item->notes))
                            <p class="item-note"><strong>Note:</strong> {{ $item->notes }}</p>
                        @endif
                      </div>
                  @endforeach
              </div>
               @if (!empty($order->order_note))
        <div class="order-note">
            <strong>Order Note:</strong>
            <p>{{ $order->order_note }}</p>
        </div>
    @endif
        <input type="hidden" id="order_id" value="{{ $order->id ?? '' }}">

          </div>

      @endforeach
       @endif
