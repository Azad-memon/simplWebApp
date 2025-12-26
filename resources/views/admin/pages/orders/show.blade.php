@extends('admin.layouts.master')
@section('title', 'Order Details')

@section('style')
    <style>
        /* ====== Order Detail Page ====== */
        .order-header {
            border-radius: 14px;
            padding: 20px;
            background: linear-gradient(45deg, #3f51b5, #6573c3);
            color: #fff;
            margin-bottom: 25px;
        }

        .order-info p {
            margin: 4px 0;
            font-size: 15px;
        }

        .card {
            border-radius: 14px;
            overflow: hidden;
        }

        .card-header {
            background: #f8f9ff;
            border-bottom: 1px solid #eee;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .items-table thead {
            background: linear-gradient(45deg, #3f51b5, #6573c3);
            color: #fff;
        }

        .items-table tbody tr:hover {
            background: #f8f9ff;
        }

        .order-total {
            font-size: 15px;
        }

        .order-total .label {
            font-weight: 600;
        }

        .order-total .amount {
            text-align: right;
        }

        .product-img {
            width: 45px;
            height: 45px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #eee;
            margin-right: 8px;
        }

        .product-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }
        .timeline-container {
    position: relative;
    padding-left: 40px;
}

.timeline-modern::before {
    content: "";
    position: absolute;
    top: 0;
    left: 18px;
    width: 3px;
    height: 100%;
    background: #e0e0e0;
}

.timeline-item-modern {
    position: relative;
    margin-bottom: 25px;
    display: flex;
    align-items: flex-start;
}

.timeline-item-modern:last-child {
    margin-bottom: 0;
}

.timeline-icon-modern {
    position: absolute;
    left: -5px;
    top: 0;
}

.timeline-icon-modern .icon-wrapper {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    color: #fff;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.timeline-content-modern {
    margin-left: 45px;
    background: #fff;
    border-radius: 10px;
    padding: 10px 15px;
    border: 1px solid #f0f0f0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.timeline-item-modern.completed .icon-wrapper {
    background-color: #28a745 !important;
}

.timeline-item-modern.active .icon-wrapper {
    background-color: #ffc107 !important;
}

.timeline-item-modern.cancelled .icon-wrapper {
    background-color: #dc3545 !important;
}

.timeline-item-modern.completed .timeline-content-modern {
    border-left: 3px solid #28a745;
}

.timeline-item-modern.active .timeline-content-modern {
    border-left: 3px solid #ffc107;
}

.timeline-item-modern.cancelled .timeline-content-modern {
    border-left: 3px solid #dc3545;
}
.queue-number-box {
    background: linear-gradient(135deg, #0d6efd, #084298);
    color: #fff;
    padding: 12px 22px;
    border-radius: 12px;
    text-align: center;
    min-width: 110px;
}

.queue-label {
    display: block;
    font-size: 12px;
    letter-spacing: 1px;
    opacity: 0.85;
}

.queue-number {
    display: block;
    font-size: 34px;
    font-weight: 800;
    line-height: 1;
}

    </style>
@endsection

@section('content')
    <div class="container-fluid">

        {{-- Order Header --}}
        <div class="order-header shadow-sm d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h3 class="fw-bold mb-2">
                    <i class="fa fa-receipt me-2"></i> Order #{{ $order->order_uid }}
                </h3>
                <div class="order-info">
                    <p><strong>Date:</strong> {{ $order->created_at->format('d M, Y H:i A') }}</p>
                    <p>
                        <strong>Current Status:</strong>
                        @php
                            $statusClass = match ($order->status) {
                                'pending' => 'status-pending',
                                'completed' => 'status-completed',
                                'cancelled' => 'status-cancelled',
                                default => '',
                            };
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </p>
                </div>
            </div>
             {{-- RIGHT SIDE : QUEUE NUMBER --}}
    @if(!empty($order->queue_number))
        <div class="text-end">
            <div class="queue-number-box">
                <span class="queue-label">QUEUE</span>
                <span class="queue-number">{{ $order->queue_number }}</span>
            </div>
        </div>
    @endif


            {{-- 🔥 Status Update Form --}}
            <div>
                  {{-- @if(Auth::user()->role->name=='branchadmin')
                   <form action="{{ route('badmin.order.updateStatus', $order->id) }}" method="POST"  class="d-flex align-items-center bg-white p-2 rounded shadow-sm">
                  @else
                   <form action="{{ route('admin.order.updateStatus', $order->id) }}" method="POST"  class="d-flex align-items-center bg-white p-2 rounded shadow-sm">
                  @endif


                    @csrf
                    <select name="status" class="form-select form-select-sm me-2" style="min-width:150px;">
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing
                        </option>
                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fa fa-sync-alt me-1"></i> Update
                    </button>
                </form> --}}

            </div>
        </div>

        <div class="row">
            {{-- Customer Details --}}
            <div class="col-md-5 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="fw-bold mb-0"><i class="fa fa-user me-2 text-primary"></i> Customer Details</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong> {{ $order->customer_name ?? 'N/A' }}</p>
                        <p><strong>Phone:</strong> {{ $order->customer_phone ?? 'N/A' }}</p>

                        <p><strong>Address:</strong>
                            @if ($order->address && $order->address->address)
                                {{ $order->address->address->street_address ?? '' }},
                                {{ $order->address->address->city ?? '' }},
                                {{ $order->address->address->state ?? '' }},
                                {{ $order->address->address->zipcode ?? '' }}
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                </div>
                              {{-- Order Tracking --}}
<div class="card shadow-sm border-0 mt-4 p-4 rounded-4">
    <h5 class="fw-bold mb-4 text-center text-dark">
        <i class="fa fa-route text-primary me-2"></i> Order Tracking
    </h5>
                         @php
    use Carbon\Carbon;

    // Define all possible order statuses with labels and icons
        $allStatuses = [
            'pending' => ['label' => 'Pending', 'icon' => 'fa-clock'],
            'accepted' => ['label' => 'Accepted', 'icon' => 'fa-check-circle'],
            'processing' => ['label' => 'Processing', 'icon' => 'fa-cogs'],
            'preparing' => ['label' => 'Preparing', 'icon' => 'fa-utensils'],
            'ready' => ['label' => 'Ready', 'icon' => 'fa-box'],
            'dispatched' => ['label' => 'Dispatched', 'icon' => 'fa-truck'],
            'completed' => ['label' => 'Completed', 'icon' => 'fa-check-double'],
            'cancelled' => ['label' => 'Cancelled', 'icon' => 'fa-times-circle'],
        ];

    // Convert tracking data into associative array for quick access
    $trackData = collect($order->ordertracking)->keyBy('status');

    // Determine last completed status for highlighting progression
    $completedUntil = $trackData->keys()->last();
@endphp

    <div class="timeline-container position-relative">
        <ul class="timeline-modern list-unstyled m-0">
            @foreach($allStatuses as $status => $meta)
                @php
                    $entry = $trackData->get($status);
                    $isCompleted = $entry && $status !== 'cancelled';
                    $isCancelled = $status === 'cancelled' && $entry;
                    $isActive = !$isCancelled && !$isCompleted && ($completedUntil === $status);
                @endphp
                @if($entry)
                    <li class="timeline-item-modern
                        {{ $isCompleted ? 'completed' : '' }}
                        {{ $isActive ? 'active' : '' }}
                        {{ $isCancelled ? 'cancelled' : '' }}">
                        <div class="timeline-icon-modern">
                            <div class="icon-wrapper {{ $isCancelled ? 'bg-danger' : ($isCompleted ? 'bg-success' : ($isActive ? 'bg-warning' : 'bg-light')) }}">
                                <i class="fa {{ $meta['icon'] }} text-white"></i>
                            </div>
                        </div>
                        <div class="timeline-content-modern">
                            <h6 class="fw-semibold mb-1 {{ $isCancelled ? 'text-danger' : ($isCompleted ? 'text-success' : 'text-dark') }}">
                                {{ $meta['label'] }}
                            </h6>
                            <small class="text-muted d-block">
                                {{ Carbon::parse($entry->created_at)->format('h:i A · M d, Y') }}
                            </small>
                            @if(!empty($entry->note))
                                <div class="small text-secondary mt-1 fst-italic">{{ $entry->note }}</div>
                            @endif
                        </div>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</div>
            </div>


            {{-- Order Items --}}
            <div class="col-md-7 mb-4">
                <div class="card border-0 shadow-lg rounded-3">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="fa fa-box me-2"></i> Order Items
                        </h5>
                        <span class="badge bg-light text-dark">
                            {{ $order->items->count() }} Items
                        </span>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table align-middle mb-0">
                                <thead class="bg-light text-secondary">
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $addonSubtotal_all = 0; @endphp
                                    @foreach ($order->items as $item)
                                        @php
                                            $itemSubtotal = $item->price * $item->quantity;

                                            // Addon subtotal (quantity included)
                                            $addonSubtotal = 0;
                                            $sizeid=$item->productVariant->sizes->id;

                                        @endphp

                                        <tr class="border-bottom">
                                            <td>
                                                <div class="d-flex align-items-start">
                                                    {{-- Product Image --}}
                                                    <img src="{{ $item->productVariant->product->main_image ?? asset('images/no-image.png') }}"
                                                        class="rounded me-3 shadow-sm"
                                                        style="width: 60px; height: 60px; object-fit: cover;"
                                                        alt="Product">

                                                    <div>
                                                        <span class="fw-semibold d-block">
                                                            {{ $item->productVariant->product->name ?? 'N/A' }}
                                                        </span>
                                                        <small class="text-muted d-block">
                                                            Size: {{ $item->productVariant->sizes->name ?? 'N/A'  }}
                                                        </small>

                                                        {{-- Addons + Removed Ingredients --}}
                                                        <ul class="list-unstyled mt-2 mb-0 small">
                                                         @php   $addonDetail =  getIngredientDetails($item->addon_id, true ,$sizeid);
                                                                $removedIngredientsDetails =getIngredientDetails($item->removed_ingredient_ids,true,$sizeid);
                                                         @endphp
                                                            {{-- Addons --}}
                                                           @php
                                                            $addonArray = json_decode($item->addon_id, true) ?? [];
                                                            $addonSubtotal = 0;
                                                        @endphp

                                                        @if (!empty($addonDetail) && count($addonDetail) > 0)
                                                            <li class="fw-bold text-success mb-1">Addons:</li>
                                                            @foreach ($addonDetail as $addon)
                                                                @php

                                                                    $match = collect($addonArray)->firstWhere('ing_id', $addon['id']);
                                                                    $price = $match['price'] ?? 0;
                                                                @endphp

                                                                @if($match)
                                                                    <li class="d-flex align-items-center ps-3 text-success">
                                                                        <i class="fa fa-plus-circle me-1"></i>
                                                                        {{ $addon['name'] }}
                                                                        <span class="ms-auto fw-semibold text-dark">
                                                                            Rs{{ number_format($price, 2) }}
                                                                        </span>
                                                                    </li>
                                                                    @php
                                                                        $addonSubtotal += $price * ($item->quantity ?? 1);
                                                                        $addonSubtotal_all=$addonSubtotal;
                                                                    @endphp
                                                                @endif
                                                            @endforeach
                                                        @endif

                                                            {{-- Removed Ingredients --}}
                                                            @if (!empty($removedIngredientsDetails) && count($removedIngredientsDetails) > 0)
                                                                <li class="fw-bold text-danger mt-2 mb-1">Removed
                                                                    Ingredients:</li>
                                                                @foreach ($removedIngredientsDetails as $removed)
                                                                    <li class="d-flex align-items-center ps-3 text-danger">
                                                                        <i class="fa fa-times-circle me-1"></i>
                                                                        {{ $removed['name'] }}
                                                                    </li>
                                                                @endforeach
                                                            @endif

                                                        </ul>

                                                    </div>
                                                </div>
                                            </td>

                                            <td class="text-center fw-bold">{{ $item->quantity }}</td>
                                            <td class="text-end">Rs{{ number_format($item->price, 2) }}</td>
                                            <td class="text-end fw-bold text-success">
                                                Rs{{ number_format($itemSubtotal, 2) }}
                                            </td>
                                        </tr>

                                        {{-- Addon Subtotal Row --}}
                                        @if ($addonSubtotal > 0)
                                            <tr>
                                                <td colspan="3" class="text-end text-muted small">Addon Subtotal
                                                    (x{{ $item->quantity }})</td>
                                                <td class="text-end fw-bold text-primary">
                                                    Rs{{ number_format($addonSubtotal, 2) }}
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
@php $maintotal=number_format(
                                $order->items->sum(function ($i) use ($addonSubtotal_all) {
                                    $base = $i->price * $i->quantity;

                                    return $base + $addonSubtotal_all;
                                }),
                                2,
                            ) @endphp
                    {{-- Footer: Order Total --}}
                    <div class="card-footer bg-light d-flex justify-content-between">
                       <span class="fw-bold text-dark">Total:</span>
                        <span class="fw-bold text-primary">
                            Rs{{ $maintotal }}
                        </span>
                    </div>
                </div>

            </div>
        </div>

        {{-- Order Totals --}}
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-header bg-primary text-white d-flex align-items-center">
                <i class="fa fa-receipt me-2"></i>
                <h5 class="mb-0 fw-bold">Order Summary</h5>
            </div>
                 @php
                            $subtotal = $order->total_amount ?? 0;
                            $tax = $order->tax ?? 0;
                            $taxPercent = ($subtotal > 0) ? floor(($tax / ($subtotal - $tax)) * 100) : 0;
                        @endphp
            <div class="card-body p-4">
                <div class="row mb-3">
                    <div class="col-6 text-muted">Subtotal</div>
                    <div class="col-6 text-end fw-semibold">
                        Rs{{ number_format($order->total_amount ?? 0, 2) }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6 text-muted">Tax ({{ $order->tax_percent }}%)</div>
                    <div class="col-6 text-end fw-semibold">
                        Rs{{ number_format($order->tax ?? 0, 2) }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-6 text-muted">Discount</div>
                    <div class="col-6 text-end fw-semibold text-danger">
                        - Rs{{ number_format($order->discount ?? 0, 2) }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-6 text-muted">Delivery Charges</div>
                    <div class="col-6 text-end fw-semibold">
                        Rs{{ number_format($order->delivery_charges ?? 0, 2) }}
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-6 fw-bold fs-5">Grand Total</div>
                    <div class="col-6 text-end fw-bold fs-5 text-success">
                        Rs{{ number_format($order->final_amount ?? 0, 2) }}
                    </div>
                </div>
            </div>
            {{-- <div class="card-footer bg-light text-center">
                <small class="text-muted">
                    Thank you for your purchase! <i class="fa fa-heart text-danger"></i>
                </small>
            </div> --}}
        </div>

    </div>
@endsection
