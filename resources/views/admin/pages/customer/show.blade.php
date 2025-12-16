<x-modal id="customerModal" title="Customer Details" size="modal-lg">
    <div class="mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h4 class="fw-bold text-primary mb-3">
                    <i class="bi bi-person-circle me-2"></i> {{ $customer->full_name ?? 'N/A' }}
                </h4>
                <div class="row align-items-start">
                    <!-- Phone -->
                    <div class="col-md-4">
                        <p class="mb-1">
                            <strong>📞 Phone:</strong> {{ $customer->phone ?? 'N/A' }}
                        </p>
                    </div>

                    <!-- Loyalty + Wallet -->
                    <div class="col-md-4">
                        <p class="mb-1">
                            <strong>⭐ Loyalty Points:</strong>
                            <span class="badge bg-success">{{ $customer->loyaltyHistories->sum('points_balance') ?? 0 }}</span>
                        </p>
                        {{-- <p class="mb-1">
                            <strong>💰 Wallet:</strong>
                            <span class="badge bg-warning text-dark">{{ $customer->wallet ?? 0 }}</span>
                        </p> --}}
                    </div>

                    <!-- Address -->
                    <div class="col-md-4">
                        <p class="mb-1">
                            <strong>📍 Address:</strong>
                              @if ($customer->addresses && $customer->addresses->address)
                                {{ $customer->addresses->address->street_address ?? '' }},
                                {{ $customer->addresses->address->city ?? '' }},
                                {{ $customer->addresses->address->state ?? '' }},
                                {{ $customer->addresses->address->zipcode ?? '' }}
                            @else
                                N/A
                            @endif
                            {{-- {{ optional($customer->addresses)->street_address ?? 'N/A' }} --}}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Order History -->
    <h5 class="fw-bold text-secondary mb-3">
        <i class="bi bi-bag-check me-2"></i> Order History
    </h5>
    <div class="table-responsive">
       <table class="table table-hover align-middle shadow-sm datatableorders">
            <thead class="table-dark">
                <tr>
                     <th class="hide">#</th>
                    <th># Order ID</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Total Amount</th>
                    <th>Payment Method</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customer->orders as $order)
                    <tr>
                        <td class="fw-semibold hide ">#</td>
                        <td class="fw-semibold">#{{ $order->order_uid }}</td>
                        <td>{{ $order->created_at->format('d M, Y h:i A') }}</td>
                        <td>
                            <span class="badge
                                @if($order->status == 'completed') bg-success
                                @elseif($order->status == 'pending') bg-warning text-dark
                                @else bg-secondary @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td><strong>{{ number_format($order->total_amount, 2) }}</strong></td>
                        <td>{{ ucfirst($order->payment_method ?? 'N/A') }}</td>
                    </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    </div>
</x-modal>
<script>
 $(document).ready(function () {
    function initOrdersTable() {
        if ($.fn.DataTable.isDataTable('.datatableorders')) {
            $('.datatableorders').DataTable().clear().destroy();
        }

        $('.datatableorders').DataTable({
            pageLength: 10,
            order: [[1, 'desc']],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data, type, row, meta) {
                        return meta.row + 1 + meta.settings._iDisplayStart;
                    }
                }
            ]
        });
    }

    // jab modal open ho to table initialize karo
    $('#customerModal').on('shown.bs.modal', function () {
        initOrdersTable();
    });
});

</script>

