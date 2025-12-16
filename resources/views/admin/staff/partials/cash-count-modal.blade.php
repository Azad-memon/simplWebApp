<h6 class="fw-bold mb-3">Today’s Cash Count</h6>
@if ($todayNotes->isNotEmpty())
    <table class="table table-sm table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>Note Value</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($todayNotes as $note)
                <tr>
                    <td>{{ number_format($note->note_value) }}</td>
                    <td>{{ $note->quantity }}</td>
                    <td>{{ number_format($note->note_value * $note->quantity) }}</td>
                </tr>
            @endforeach
            <tr class="fw-bold table-success">
                <td colspan="2" class="text-end">Total</td>
                <td>{{ number_format($total) }}</td>
            </tr>
        </tbody>
    </table>
@else
    <div class="text-muted">No cash notes found for today.</div>
@endif

{{-- ============== PREVIOUS SHIFT SECTION ============== --}}
@if ($previousNotes && $previousNotes->isNotEmpty())
    <hr>
    <h6 class="fw-bold mt-3">Previous Shift Record</h6>

    <table class="table table-sm table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>Note Value</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $previousTotal = 0;
            @endphp
            @foreach ($previousNotes as $note)
                @php
                    $rowTotal = $note->note_value * $note->quantity;
                    $previousTotal += $rowTotal;
                @endphp
                <tr>
                    <td>{{ number_format($note->note_value) }}</td>
                    <td>{{ $note->quantity }}</td>
                    <td>{{ number_format($rowTotal) }}</td>
                </tr>
            @endforeach
            <tr class="fw-bold table-info">
                <td colspan="2" class="text-end">Total</td>
                <td>{{ number_format($previousTotal) }}</td>
            </tr>
        </tbody>
    </table>

    {{-- 💰 DIFFERENCE SECTION --}}
    @php
        $difference = $total - $previousTotal;
        $diffClass = $difference >= 0 ? 'text-success' : 'text-danger';
    @endphp
    <div class="mt-3 fw-bold">
        <span>💵 Cash Difference:</span>
        <span class="{{ $diffClass }}">
            {{ $difference >= 0 ? '+' : '' }}{{ number_format($difference) }}
        </span>
    </div>
@else
    <div class="text-muted mt-3">No previous records found for this branch.</div>
@endif
