    @foreach ($orders as $order)
        <tr  data-id="{{ $order->id }}">
            <td><input type="checkbox" value="{{ $order->id }}"></td>
            <td>{{ $order->order_uid }}</td>
            <td>{{ $order->branch->name ?? 'N/A' }}</td>
            <td>{{ $order->customer->first_name ?? $order->customer_name }}</td>
            <td>{{ $order->customer->phone ?? $order->customer_phone }}</td>
            <td>{{ $order->coupon_id ? 'Applied' : 'No Voucher' }}</td>
            <td>
                <span class="badge bg-primary">
                  {{ ucfirst($order->delivery_type_label) }}
                </span>
            </td>
            <td>
                <span class="badge bg-info">
                    {{ $order->payment->payment_method ?? 'N/A' }}
                </span>
            </td>
            <td>Rs. {{ number_format($order->total_amount, 2) }}</td>
            <td>Rs. {{ number_format($order->tax, 2) }}</td>
            <td>
               @php
                $statusClasses = [
                    'pending'     => 'status-pending',
                    'accepted'    => 'status-accepted',
                    'processing'  => 'status-processing',
                    'preparing'   => 'status-preparing',
                    'dispatched'  => 'status-dispatched',
                    'ready'       => 'status-ready',
                    'completed'   => 'status-completed',
                    'cancelled'   => 'status-cancelled',
                ];

                $statusClass = $statusClasses[$order->status] ?? 'status-other';
            @endphp

            <span class="status-badge {{ $statusClass }}">
                {{ ucfirst($order->status) }}
            </span>

                        </td>
                        <td>{{ ucfirst($order->platform ?? 'Web') }}</td>
                        <td>{{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y h:i a') }}</td>
                        <td class="text-center">
                        <span id="action-cell-{{ $order->id }}">
                @if ($order->status == 'pending' && auth()->user()->role_id == 5)
                    <button class="btn btn-success btn-action update-status"
                        data-url="{{ route('pos.orders.updateStatus', $order->id) }}"
                        data-status="processing">
                        Accept
                    </button>
                @endif

                {{-- Show Cancel button in all cases except completed/cancelled --}}
                @if (!in_array($order->status, ['completed', 'cancelled']) && auth()->user()->role_id == 5)

                    <button class="btn btn-danger btn-action btn-cancel"
                        data-order-id="{{ $order->id }}"
                        data-url="{{ route('pos.orders.updateStatus', $order->id) }}">
                        Cancel
                    </button>
                @endif

                @if ($order->status == 'accepted' || $order->status == 'processing')
                    <span class="status-badge status-accepted">Accepted</span>
                @elseif ($order->status == 'dispatched')
                    <button class="btn btn-primary btn-action update-status"
                        data-url="{{ route('pos.orders.updateStatus', $order->id) }}"
                        data-status="completed">
                        Mark as Completed
                    </button>
                @elseif ($order->status == 'completed')
                    <span class="status-badge status-completed">Completed</span>
                @elseif ($order->status == 'cancelled')
                    <span class="status-badge status-cancelled">Cancelled</span>
                @endif
            </span>

                <a href="{{ route('pos.orders.view', $order->id) }}" class="btn btn-primary btn-action">
                    <i class="fas fa-eye"></i> View
                </a>
            </td>
        </tr>
    @endforeach
