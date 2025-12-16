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
                 @if(auth()->user()->role_id != 4)
                @if ($order->status === Order::STATUS_PROCESSING)

                        <button class="mark-ready-btn btn-processing"
                            data-id="{{ $order->id }}" data-status="{{ Order::STATUS_PROCESSING }}">
                            <i class="fas fa-coffee"></i> {{ strtoupper(Order::STATUS_PROCESSING) }}
                        </button>

                @elseif ($order->status === Order::STATUS_PREPARING)
                    <button class="mark-ready-btn btn-preparing" data-id="{{ $order->id }}"
                        data-status="{{ Order::STATUS_PREPARING }}">
                        <i class="fas fa-coffee"></i> {{ strtoupper(Order::STATUS_PREPARING) }}
                    </button>
                @endif
                @endif
                @if(auth()->user()->role_id == 4)
                 <button class="mark-ready-btn btn-processing"
                            >
                            <i class="fas fa-coffee"></i> {{ strtoupper($order->status) }}
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
                            $removedIngredientsDetails = getIngredientDetails(
                                $item->removed_ingredient_ids,
                                true,
                                $sizeid,
                            );
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
    <input type="hidden" id="order_id" value="{{ $order->id ?? '' }}">
@else
    {{-- <p class="no-orders">No orders available.</p> --}}
@endif
