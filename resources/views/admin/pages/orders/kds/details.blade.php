@extends('admin.layouts.master')

@section('content')
<style>
    body {
        background-color: #121212;
        color: #f5f5f5;
        font-family: 'Poppins', sans-serif;
        overflow-x: hidden;
    }

    .kds-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 30px;
        background: #1e1e1e;
        border-bottom: 2px solid #333;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .kds-header h2 {
        font-size: 26px;
        font-weight: 600;
        letter-spacing: 1px;
    }

    .order-card {
        background: #1b1b1b;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        padding: 25px;
        margin: 25px;
        transition: transform 0.2s ease, background 0.3s ease;
     height: 300px;
    overflow: scroll;
    }

    .order-card:hover {
        transform: scale(1.02);
        background: #222;
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #333;
        margin-bottom: 15px;
        padding-bottom: 10px;
    }

    .order-header h3 {
        color: #00ffb3;
        font-size: 24px;
        margin: 0;
    }

    .order-time {
        font-size: 15px;
        color: #aaa;
    }

    .items-container {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .item-card {
        background: #262626;
        border-radius: 12px;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        transition: all 0.2s ease;
        border-left: 4px solid #00ffb3;
    }

    .item-card:hover {
        background: #2f2f2f;
    }

    .item-name {
        font-size: 20px;
        font-weight: 600;
        color: #fff;
        margin-bottom: 6px;
    }

    .item-note {
        font-size: 15px;
        color: #f1c40f;
        margin-bottom: 5px;
    }

    .item-addons, .item-removed {
        font-size: 15px;
        color: #ccc;
        margin-top: 5px;
    }

    .item-addons div, .item-removed div {
        margin-left: 10px;
        font-size: 14px;
    }

    .item-qty {
        font-size: 22px;
        font-weight: 700;
        background: #00ffb3;
        color: #000;
        border-radius: 8px;
        padding: 8px 18px;
    }

    .footer-bar {
        text-align: center;
        padding: 20px;
        color: #888;
        border-top: 1px solid #333;
        margin-top: 30px;
    }

    .ready-btn {
        background: #00ffb3;
        border: none;
        color: #000;
        font-weight: 600;
        padding: 12px 25px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .ready-btn:hover {
        background: #00cc8a;
    }
</style>

<div class="kds-header">
    <h2>☕ Kitchen Display</h2>
    <button class="ready-btn" onclick="window.location.reload()">🔄 Refresh</button>
</div>

<div class="order-card">
    <div class="order-header">
        <h3>Order #{{ $order->id }}</h3>
        <div class="order-time">{{ $order->created_at->format('h:i A') }}</div>
    </div>

    <div class="items-container">
        @foreach ($order->items as $item)
            @php
                $addons = json_decode($item->addon_id, true) ?? [];
                $removed = json_decode($item->removed_ingredient_ids, true) ?? [];
                $variant = $item->productVariant ?? null;

                $productName = $variant?->product?->name ?? 'Unknown Item';
            @endphp

            <div class="item-card">
                <div class="item-left">
                    <div class="item-name">{{ $productName }}</div>

                    @if(!empty($item->notes))
                        <div class="item-note">📝 {{ $item->notes }}</div>
                    @endif

                    @if(!empty($addons))
                        <div class="item-addons">
                            ➕ Addons:
                            @foreach ($addons as $addon)
                                <div>- {{ $addon['quantity'] }}x Addon #{{ $addon['addon_id'] }}</div>
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($removed))
                        <div class="item-removed">
                            🚫 Removed:
                            @foreach ($removed as $rem)
                                <div>- Ingredient #{{ $rem['ing_id'] }}</div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="item-qty">x{{ $item->quantity }}</div>
            </div>
        @endforeach
    </div>
</div>

<div class="footer-bar">
    Kitchen Display Screen • Last updated {{ now()->format('h:i:s A') }}
</div>
@endsection
