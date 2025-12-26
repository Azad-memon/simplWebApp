<h6 class="fw-bold mb-3">Stock Count Summary</h6>

@if ($summary!="")
    <table class="table table-sm table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>Ingredient</th>
                <th>Previous Qty</th>
                <th>Current Qty</th>
                <th>Difference</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($summary as $row)
                <tr>
                    <td>{{ $row['ingredient_name'] }}</td>
                    <td>{{ $row['previous_qty'] }}</td>
                    <td>{{ $row['current_qty'] }}</td>
                    <td class="{{ $row['difference'] >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $row['difference'] >= 0 ? '+' : '' }}{{ $row['difference'] }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div class="text-muted">No stock data found for this shift.</div>
@endif
