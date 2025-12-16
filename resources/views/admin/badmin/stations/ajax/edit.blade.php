<x-modal id="EditStationModal" title="Edit Station">

    <form id="edit-station-form" action="{{ route('badmin.station.update', ['id'=>$data['normal'][0]['id']]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="col-md-12">
            <div class="mb-3">
                <label for="edit_station_name">Station Name</label>
                <input type="text" id="edit_station_name" name="s_name" class="form-control"
                       value="{{ old('s_name', $data['normal'][0]['s_name']) }}" required>
                @error('s_name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="col-md-12">
            <div class="mb-3">
                <label for="edit_printer_ip">Printer IP</label>
                <input type="text" id="edit_printer_ip" name="ip" class="form-control"
                       value="{{ old('ip', $data['normal'][0]['ip']) }}" required>
                @error('ip')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="col-md-12">
            <div class="mb-3">
                <label for="edit_categories">Categories</label>
                <select id="edit_categories" name="categories[]" class="form-control" multiple required>
                    @php
                        $stationCategoryIds = collect($data['normal'][0]['categories'])->pluck('id')->toArray();
                    @endphp

                    @foreach ($productCategories as $category)
                        <option value="{{ $category['id'] }}"
                            {{ in_array($category['id'], old('categories', $stationCategoryIds)) ? 'selected' : '' }}>
                            {{ $category['name'] }}
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
                <button type="submit" class="btn btn-primary">Update Station</button>
            </div>
        </div>
    </form>
</x-modal>
