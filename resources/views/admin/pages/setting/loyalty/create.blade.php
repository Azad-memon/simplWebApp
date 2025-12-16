@extends('admin.layouts.master')

@section('content')
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-award me-2"></i> Loyalty Points Setting
                    </h4>
                </div>
                <div class="card-body">
                    <form
                        action="{{ isset($setting) ? route('admin.loyalty.update', $setting->id) : route('admin.loyalty.store') }}"
                        method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="rupees" class="form-label fw-semibold">Rupees (Spent)</label>
                            <input type="number" step="0.01" min="1" class="form-control" name="rupees"
                                id="rupees" value="{{ old('rupees', $setting->rupees ?? '') }}"
                                placeholder="Enter rupees amount" required>
                            <small class="text-muted">Enter the amount of rupees spent to earn points.</small>
                        </div>

                        <div class="mb-3">
                            <label for="points" class="form-label fw-semibold">Points (Earned)</label>
                            <input type="text" min="1" class="form-control" name="points" id="points"
                                value="{{ old('points', $setting->points ?? '') }}" placeholder="Enter points" required>
                            <small class="text-muted">Enter how many points are earned for the above rupees.</small>
                        </div>

                        <div class="mb-3">
                            <label for="max_points_per_order" class="form-label fw-semibold">Max Points Per Order</label>
                            <input type="number" min="1" class="form-control" name="max_points_per_order"
                                id="max_points_per_order"
                                value="{{ old('max_points_per_order', $setting->max_points_per_order ?? '') }}"
                                placeholder="Leave blank for unlimited">
                            <small class="text-muted">Set the maximum points a customer can earn per order
                                (optional).</small>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success px-4">
                                <i class="bi bi-save me-1"></i> Save
                            </button>
                            {{-- <a href="{{ url()->previous() }}" class="btn btn-secondary ms-2 px-4">
                            Cancel
                        </a> --}}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
