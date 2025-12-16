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
                            <span class="badge bg-success">{{ $customer->loyalty_points ?? 0 }}</span>
                        </p>
                        {{-- <p class="mb-1">
                            <strong>💰 Wallet:</strong>
                            <span class="badge bg-warning text-dark">{{ $customer->wallet ?? 0 }}</span>
                        </p> --}}
                    </div>

                    <!-- Address -->
                    <div class="col-md-4">
                        <p class="mb-1">
                             {{-- {{ dump($customer->id) }}
                            {{ dump($customer->addresses->address->street_address) }} --}}
                            <strong>📍 Address:</strong> {{ $customer->addresses->address->street_address ?? 'N/A' }}
                        </p>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <!-- Loyalty Points History -->
    <h5 class="fw-bold text-secondary mb-3">
        <i class="bi bi-clock-history me-2"></i> Loyalty Points History
    </h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th># Order ID</th>
                    <th>Date</th>
                    <th>Points Updated</th>
                    <th>Points Balance</th>
                    <th>Transaction Type</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customer->loyaltyHistories as $history)
                    <tr>
                        <td class="fw-semibold">#{{ $history->order_id ?? '—' }}</td>
                        <td>{{ $history->created_at->format('d M, Y h:i A') }}</td>
                        <td>
                            <span class="badge {{ $history->points_updated > 0 ? 'bg-success' : 'bg-danger' }}">
                                {{ $history->points_updated }}
                            </span>
                        </td>
                        <td><span class="fw-bold">{{ $history->points_balance }}</span></td>
                        <td>{{ ucfirst($history->transaction_type) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            No loyalty history found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    </div>
</x-modal>
