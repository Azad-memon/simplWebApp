<x-modal id="StationModal" title="Add New Station">

    <form id="create-station-form" action="{{ route('badmin.station.store') }}" method="POST">
        @csrf
        <div class="col-md-12">
            <div class="mb-3">
                <label for="station_name">Station Name</label>
                <input type="text" id="station_name" name="s_name" class="form-control"
                       placeholder="Enter Station Name" value="{{ old('s_name') }}" required>
                @error('s_name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="col-md-12">
            <div class="mb-3">
                <label for="printer_ip">Printer IP</label>
                <input type="text" id="printer_ip" name="ip" class="form-control"
                       placeholder="e.g., 10.0.0.12" value="{{ old('ip') }}" required>
                @error('ip')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="col-md-12">
            <div class="mb-3">
                <label for="categories">Categories</label>
                <select id="categories" name="categories[]" class="form-control" multiple required>
                    @foreach ($productCategories as $key => $value)
                        <option value="{{ $value['id'] }}"
                            {{ in_array($value['id'], old('categories', [])) ? 'selected' : '' }}>
                            {{ $value['name'] }}
                        </option>
                    @endforeach
                </select>
                @error('categories')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
                @error('categories.*')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="col-sm-12 mt-3 text-center">
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Create Station</button>
            </div>
        </div>
    </form>
</x-modal>
