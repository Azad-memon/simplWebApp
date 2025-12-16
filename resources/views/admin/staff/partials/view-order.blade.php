<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Order Details</title>
    @include('admin.staff.partials.layouts.header')

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9f9f9;
        }

        .card {
            border-radius: 12px;
            border: none;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.06);
        }

        .badge {
            font-size: 0.85rem;
            padding: 6px 10px;
        }

        .order-header h4 {
            font-weight: 600;
        }

        .addon-list li {
            color: #0d6efd;
            font-size: 0.9rem;
        }

        .ingredient-list li {
            color: #198754;
            font-size: 0.9rem;
        }

        .removed-ingredient-list li {
            color: #dc3545;
            text-decoration: line-through;
            font-size: 0.9rem;
        }



        .tracking-horizontal {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            flex-wrap: wrap;
            gap: 10px;
        }

        .tracking-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            flex: 1;
            position: relative;
        }

        .tracking-step .circle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #e0e0e0;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            transition: all 0.4s ease;
        }

        .tracking-step.active .circle {
            background: #0d6efd;
            box-shadow: 0 0 10px rgba(13, 110, 253, 0.5);
        }

        .tracking-step .label {
            margin-top: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #888;
        }

        .tracking-step.active .label {
            color: #0d6efd;
        }

        .tracking-horizontal .line {
            flex: 1;
            height: 3px;
            background: #e0e0e0;
            transition: all 0.4s ease;
        }

        .tracking-horizontal .line.active {
            background: #0d6efd;
            box-shadow: 0 0 10px rgba(13, 110, 253, 0.5);
        }

        @media (max-width: 768px) {
            .tracking-horizontal {
                flex-direction: column;
                align-items: flex-start;
            }

            .tracking-horizontal .line {
                width: 3px;
                height: 40px;
            }
        }


        /*order Tracking*/
        .timeline-container {
            position: relative;
            padding-left: 45px;
        }

        .timeline-modern::before {
            content: "";
            position: absolute;
            left: 28px;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(to bottom, #0d6efd, #dee2e6);
            border-radius: 2px;
        }

        .timeline-item-modern {
            position: relative;
            margin-bottom: 35px;
            display: flex;
            align-items: flex-start;
        }

        .timeline-icon-modern {
            position: absolute;
            left: 0;
            top: 0;
        }

        .icon-wrapper {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .icon-wrapper.bg-light {
            background: #f8f9fa;
            color: #6c757d;
        }

        .timeline-content-modern {
            padding-left: 60px;
            transition: all 0.3s ease;
        }

        .timeline-item-modern.completed .timeline-content-modern h6 {
            color: #198754 !important;
        }

        .timeline-item-modern.active .icon-wrapper {
            background: #ffc107 !important;
            color: #212529;
            transform: scale(1.1);
            box-shadow: 0 0 10px rgba(255, 193, 7, 0.5);
        }

        .timeline-item-modern.cancelled .icon-wrapper {
            background: #dc3545 !important;
            box-shadow: 0 0 10px rgba(220, 53, 69, 0.4);
        }

        .timeline-item-modern:last-child {
            margin-bottom: 0;
        }
    </style>
</head>

<body>
    <div class="container-fluid py-4">
        @include('admin.staff.partials.top-nev')
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center order-header mb-3">
            <h4>Order #{{ $order->order_uid }} <span class="text-muted">
                    {{ $order->refundTransactions->count() > 0 ? ' (Refunded)' : '' }}</span></h4>


            {{-- <div class="d-flex gap-2">
                <button class="btn btn-sm btn-danger"><i class="fas fa-times"></i> Cancel</button>
                <button class="btn btn-sm btn-success"><i class="fas fa-check"></i> Accept</button>
                <button class="btn btn-sm btn-info"><i class="fab fa-whatsapp"></i> WhatsApp Customer</button>
            </div> --}}
        </div>
        <div class="row">
            {{-- Customer Details --}}
            <div class="col-md-4">
                <div class="card shadow-sm mb-3">
                    <div class="card-body text-center">
                        @if ($order->user_id)
                            <h5>{{ $order->customer->first_name . ' ' . $order->customer->last_name }}</h5>

                            @if (!empty($order->customer->phone))
                                <p class="text-muted mb-1">
                                    <i class="fa fa-phone"></i> {{ $order->customer->phone }}
                                </p>
                            @endif

                            @if (!empty($order->customer->email))
                                <p class="text-muted">
                                    <i class="fa fa-envelope"></i> {{ $order->customer->email }}
                                </p>
                            @endif
                        @else
                            <h5>{{ $order->customer_name . ' ' . $order->customer_last_name }}</h5>

                            @if (!empty($order->customer_phone))
                                <p class="text-muted mb-1">
                                    <i class="fa fa-phone"></i> {{ $order->customer_phone }}
                                </p>
                            @endif

                            @if (!empty($order->customer_email))
                                <p class="text-muted">
                                    <i class="fa fa-envelope"></i> {{ $order->customer_email }}
                                </p>
                            @endif
                        @endif

                        @if ($order->staff_id)
                            <div class="mt-3 border-top pt-2">
                                <small class="text-muted d-block mb-1" style="font-size: 13px;">Order Created By</small>
                                <h6 class="mb-0">
                                    <i class="fa fa-user text-primary"></i>
                                    {{ $order->staff->first_name . ' ' . $order->staff->last_name }}
                                </h6>
                            </div>
                        @endif
                    </div>


                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            @if ($order->address)
                                <tr>
                                    <th>Delivery Address</th>
                                    <td>{{ $order->address->address->street_address ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Nearest Landmark</th>
                                    <td>{{ $order->address->address->nearest_landmark ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>City</th>
                                    <td>{{ $order->address->address->city ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Branch</th>
                                    <td>{{ $order->branch->name }}</td>
                                </tr>
                                {{-- <tr>
                                    <th>Map</th>
                                    <td><button class="btn btn-sm btn-success">View Map</button></td>
                                </tr> --}}
                            @endif

                        </table>
                    </div>
                </div>
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

                <div class="card shadow-sm border-0 mt-3 p-4 rounded-4">
                    <h5 class="fw-bold mb-4 text-center text-dark">
                        <i class="fa fa-route text-primary me-2"></i>Order Tracking
                    </h5>

                    <div class="timeline-container">
                        <ul class="timeline-modern list-unstyled m-0 position-relative">
                            @foreach ($allStatuses as $status => $meta)
                                @php
                                    $entry = $trackData->get($status);
                                    $isCompleted = $entry && $status !== 'cancelled';
                                    $isCancelled = $status === 'cancelled' && $entry;
                                    $isActive = !$isCancelled && !$isCompleted && $completedUntil === $status;
                                @endphp
                                @if ($entry)
                                    <li
                                        class="timeline-item-modern
                                        {{ $isCompleted ? 'completed' : '' }}
                                        {{ $isActive ? 'active' : '' }}
                                        {{ $isCancelled ? 'cancelled' : '' }}
                                      ">
                                        <div class="timeline-icon-modern">
                                            <div
                                                class="icon-wrapper {{ $isCancelled ? 'bg-danger' : ($isCompleted ? 'bg-success' : ($isActive ? 'bg-warning' : 'bg-light')) }}">
                                                <i class="fa {{ $meta['icon'] }}"></i>
                                            </div>
                                        </div>
                                        <div class="timeline-content-modern">
                                            <h6
                                                class="fw-semibold mb-1 {{ $isCancelled ? 'text-danger' : ($isCompleted ? 'text-success' : 'text-dark') }}">
                                                {{ $meta['label'] }}
                                            </h6>

                                            <small class="text-muted">
                                                {{ Carbon::parse($entry->created_at)->format('h:i A · M d, Y') }}
                                            </small>
                                            @if (!empty($entry->note))
                                                <div class="small text-secondary mt-1">{{ $entry->note }}</div>
                                            @endif

                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Order Details --}}
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">

                        {{-- Ref & Status --}}
                        <div class="d-flex justify-content-between flex-wrap align-items-start">
                            <div>
                                <p><strong>Ref #</strong>: {{ $order->order_uid }}
                                    {{ $order->refundTransactions->count() > 0 ? ' (Refunded)' : '' }}</p>
                                {{-- <p><strong>Customer Ref #</strong>: {{ $order->customer_ref }}</p> --}}
                            </div>

                            <div class="text-end">
                                @php
                                    $status = strtolower($order->status ?? '');

                                    $statusColors = [
                                        'pending' => 'bg-secondary',
                                        'accepted' => 'bg-primary',
                                        'processing' => 'bg-warning text-dark',
                                        'preparing' => 'bg-info text-dark',
                                        'ready' => 'bg-success',
                                        'dispatched' => 'bg-primary',
                                        'completed' => 'bg-success',
                                        'delivered' => 'bg-success',
                                        'cancelled' => 'bg-danger',
                                    ];

                                    $badgeClass = $statusColors[$status] ?? 'bg-secondary';
                                @endphp

                                <p class="mb-0">
                                    <strong>Status:</strong>
                                    <span class="badge {{ $badgeClass }}">{{ ucfirst($status) }}</span>
                                </p>

                                <p><strong>Created At:</strong> {{ $order->created_at->format('d M Y, h:i A') }}</p>

                                {{-- ✅ Buttons --}}
                                <div class="mt-2 @if(auth()->user()->role_id == 5) d-block @else d-none @endif">
                                    {{-- <a href="{{ route('staff.receipt.print.local', $order->id) }}" target="_blank"
                                        class="btn btn-sm btn-primary me-2">
                                        <i class="bi bi-printer"></i> Test Local
                                    </a> --}}
                                   @php
    // Add current page URL to the data
                                        $getreciptData['redirect_url'] = url()->current();
                                        $getKOT['redirect_url'] = url()->current();
                                    @endphp

                                <form action="http://localhost/PrinEscpos/Billrecipt.php" method="POST" target="_blank" class="d-inline">
                                            @csrf <!-- Laravel CSRF token -->
                                            <input type="hidden" name="order" value="{{ htmlspecialchars(json_encode($getreciptData), ENT_QUOTES) }}">
                                            <button type="submit" class="btn btn-sm btn-primary me-2">
                                                <i class="bi bi-printer"></i> Bill Receipt
                                            </button>
                                </form>

                                        <form action="http://localhost/PrinEscpos/KotBill.php" method="POST"  class="d-inline">
                                            @csrf
                                            <input type="hidden" name="order" value="{{ htmlspecialchars(json_encode($getKOT), ENT_QUOTES) }}">
                                            <button type="submit" class="btn btn-sm btn-warning text-white me-2">
                                                <i class="bi bi-printer-fill"></i> Kitchen
                                            </button>
                                        </form>
                                                                            {{-- <a href="{{ route('staff.kitchen.print.local', $order->id) }}" target="_blank"
                                        class="btn btn-sm btn-warning text-white me-2">
                                        <i class="bi bi-printer-fill"></i> Test Kitchen
                                    </a> --}}


                                    <a href="{{ route('staff.receipt.print', $order->id) }}"
                                        class="btn btn-sm btn-primary me-2 d-none">
                                        <i class="bi bi-printer"></i> Bill Receipt
                                    </a>
                                    <a href="{{ route('staff.kitchen.print', $order->id) }}"
                                        class="btn btn-sm btn-warning text-white d-none">
                                        <i class="bi bi-printer-fill"></i> Kitchen
                                    </a>
                                    <a href="{{ route('staff.sticker.print', $order->id) }}"
                                        class="btn btn-sm btn-success text-white" target="_blank">
                                        <i class="bi bi-ticket-detailed"></i> Sticker
                                    </a>
                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- More Info --}}
                        <table class="table table-borderless">
                            <tr>
                                <th>Branch</th>
                                <td>{{ $order->branch->name }}</td>
                                <th>Payment</th>

                                <td>

                                    {{ ucfirst($order->payment->payment_method ?? 'N/A') }}</td>
                            </tr>

                            <tr>
                                <th>Platform</th>
                                <td>{{ $order->platform ?? 'N/A' }}</td>
                                <th>Order Type</th>
                                <td>{{ $order->order_type_label }}
                                </td>
                            </tr>
                        </table>

                        {{-- <div class="text-center bg-light p-3 my-3 rounded">
                            <h6 class="mb-1">Delivery Time</h6>
                            <h5 class="fw-bold">{{ $order->delivery_time }}</h5>
                            <button class="btn btn-sm btn-outline-primary">+ 5 Mins</button>
                            <button class="btn btn-sm btn-outline-primary">- 5 Mins</button>
                        </div> --}}

                        {{-- <div class="alert alert-info text-center">
                            <strong>This is a Delivery Order</strong>
                        </div> --}}

                        {{-- <button class="btn btn-outline-primary w-100 mb-3">Assign a Rider (Manual)</button> --}}

                        {{-- Items --}}
                        <h6 class="fw-bold">Items</h6>
                        <table class="table table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Details</th>
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
                                        $sizeid = $item->productVariant->sizes->id;

                                    @endphp

                                    <tr>
                                        <td>
                                            <span class="fw-semibold d-block">
                                                {{ $item->productVariant->product->name ?? 'N/A' }}
                                                ({{ $item->productVariant->product->category->name ?? '' }})
                                            </span>
                                            <small class="text-muted d-block">
                                                Size: {{ $item->productVariant->sizes->name ?? 'N/A' }}
                                            </small>
                                            <p class="text-danger">Item Note: {{ $item->notes }}</p>
                                        </td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ number_format($item->price * $item->quantity, 2) }}</td>
                                        <ul class="list-unstyled mt-2 mb-0 small">
                                            @php
                                                $addonDetail = getIngredientDetails($item->addon_id, true, $sizeid);
                                                $removedIngredientsDetails = getIngredientDetails(
                                                    $item->removed_ingredient_ids,
                                                    true,
                                                    $sizeid,
                                                );
                                            @endphp
                                            {{-- Addons --}}
                                            @php
                                                $addonArray = json_decode($item->addon_id, true) ?? [];
                                                $addonSubtotal = 0;
                                            @endphp
                                            <td>
                                                {{-- Addons --}}
                                                @if (!empty($addonDetail) && count($addonDetail) > 0)
                                                    <ul class="addon-list mb-1">
                                                        <strong>Addons:</strong>
                                                        @foreach ($addonDetail as $addon)
                                                            @php
                                                                //dump( $addon);
                                                                $match = collect($addonArray)->firstWhere(
                                                                    'ing_id',
                                                                    $addon['id'],
                                                                );
                                                                //dump($match );
                                                                $price = $match['price'] ?? 0;
                                                            @endphp

                                                            @if ($match)
                                                                <li class="d-flex align-items-center text-success">
                                                                    <i class="fa fa-plus-circle me-1"></i>
                                                                    {{ $addon['label_name'] }}

                                                                    Rs {{ number_format($price, 2) }}

                                                                </li>
                                                                @php
                                                                    $addonSubtotal += $price * ($item->quantity ?? 1);
                                                                    $addonSubtotal_all += $addonSubtotal;
                                                                @endphp
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                @endif


                                                {{-- Ingredients --}}
                                                {{-- @if ($item->ingredients && count($item->ingredients) > 0)
                                                <ul class="ingredient-list mb-1">
                                                    <strong>Ingredients:</strong>
                                                    @foreach ($item->ingredients as $ing)
                                                        <li>{{ $ing->name }}</li>
                                                    @endforeach
                                                </ul>
                                            @endif --}}

                                                {{-- Removed Ingredients --}}
                                                {{-- @if ($removedIngredientsDetails && count($removedIngredientsDetails) > 0)
                                                <ul class="removed-ingredient-list mb-1">
                                                    <strong>Removed:</strong>
                                                    @foreach ($removedIngredientsDetails as $removed)
                                                        <li>{{ $removed['name'] }}</li>
                                                    @endforeach
                                                </ul>
                                            @endif --}}
                                            </td>

                                            <td class="text-end fw-bold text-success">
                                                Rs{{ number_format($itemSubtotal, 2) }}
                                            </td>

                                    </tr>
                                    @if ($addonSubtotal > 0)
                                        <tr>
                                            <td colspan="4" class="text-end text-muted small">Addon Subtotal
                                                (x{{ $item->quantity }}) </td>
                                            <td class="text-end fw-bold text-primary">
                                                Rs{{ number_format($addonSubtotal, 2) }}
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                        <p class="text-danger">Order Note: {{ $order->order_note ?? 'N/A' }} </p>

                        <hr>

                        {{-- Totals --}}
                        <div class="d-flex justify-content-end">
                            <table class="table table-sm w-auto">
                                @php
                                    $subtotal = $order->total_amount ?? 0;
                                    // Default tax percent
                                    $taxPercent = 0;
                                @endphp

                                <tr>
                                    <th>Subtotal</th>
                                    <td>{{ number_format($subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Tax ({{ $order->tax_percent }}%)</th>
                                    <td>{{ number_format($order->tax, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Delivery Fee</th>
                                    <td>{{ number_format($order->delivery_charges, 2) }}</td>
                                </tr>

                                {{-- ✅ Coupon Information --}}
                                @if (!empty($order->coupon))
                                    <tr class="bg-light">
                                        <th class="text-success">
                                            <i class="fa fa-ticket-alt me-1"></i> Coupon
                                        </th>
                                        <td>
                                            @if (!empty($order->coupon))
                                                <div class="d-flex flex-column">
                                                    <strong
                                                        class="text-primary">{{ strtoupper($order->coupon->code) }}</strong>
                                                    @if (!empty($order->coupon->discount_type))
                                                        <small class="text-muted">
                                                            <i class="fa fa-tag me-1 text-secondary"></i>
                                                            {{ ucfirst($order->coupon->discount_type) }}
                                                            — <span
                                                                class="fw-semibold">{{ $order->coupon->discount_value }}{{ $order->coupon->discount_type == 'percent' ? '%' : ' PKR' }}</span>
                                                        </small>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Discount</th>
                                        <td>-{{ number_format($order->discount ?? 0, 2) }}</td>
                                    </tr>
                                @endif
                                <tr class="table-light">
                                    <th>Total</th>
                                    <td><strong>{{ number_format($order->final_amount ?? 0, 2) }}</strong></td>
                                </tr>
                            </table>

                        </div>


                        {{-- <button class="btn btn-primary btn-sm float-end">Edit Order</button> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.staff.partials.layouts.script')
</body>

</html>
