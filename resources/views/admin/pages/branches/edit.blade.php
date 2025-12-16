<x-modal id="branchesModal" title="Edit branch">
<script
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&callback=loadMap"
    async defer></script>
    <style>

#pac-input {
    left: 0px !important;
    top: 10px !important;
    padding: 9px !important;
    border-radius: 10px;
}
#pac-input {
    background-color: #fff !important;
    font-family: Roboto;
    font-size: 15px;
    font-weight: 300;
    margin-left: 12px;
    padding: 0 11px 0 13px;
    text-overflow: ellipsis;
    width: 400px;
}
</style>
 <form method="POST" action="{{ isset($branches) ? route('admin.branches.update', $branches->id) : route('language-branches.store') }}" id="branchForm">
    @csrf
    @if(isset($branches))
        @method('PUT')
    @endif

    <input type="hidden" name="id" id="branches-id" value="{{ $branches->id ?? '' }}">

    <div class="mb-3">
        <label for="name">Branch Name</label>
        <input type="text" class="form-control" id="name" name="name" value="{{ $branches->name ?? '' }}">
    </div>
      <div class="mb-3">
    <label for="branch_code">Branch Code</label>
    <input type="text" class="form-control" id="branch_code" name="branch_code" value="{{ $branches->branch_code ?? '' }}">
    </div>
     <div class="mb-3">
        <label for="name">Branch Phone</label>
        <input type="text" class="form-control" id="phone" name="phone" value="{{ $branches->phone ?? '' }}">
    </div>
    <div class="mb-3">
        <label for="address">Branch Address</label>
        <input type="text" class="form-control map-input" id="location-input" name="address" value="{{ $branches->address ?? '' }}">
    </div>
    <div class="mb-3">
        <label for="city">City</label>
        <select class="form-control" id="city" name="city_id">
            @foreach ($cities as $city)
                <option value="{{ $city->id }}" {{ $city->id == $branches->city_id ? 'selected' : '' }}>{{ $city->city_name }}</option>
            @endforeach
        </select>
    </div>


    <!-- Google Map -->
     <div class="mb-3">
     <input id="pac-input" class="controls form-control" type="text" placeholder="Enter your address" />

        <div id="map" style="height: 400px; width: 100%; margin-top: 10px;"></div>

        <div id="selected-address" name="slected_address" style="font-weight: bold; margin-top: 10px;"></div>


        <input type="hidden" name="lat" id="latitude" value="{{ $branches->lat ?? '' }}">
        <input type="hidden" name="long" id="longitude" value="{{ $branches->long ?? '' }}">
     </div>

   <div class="mb-3">
        <label for="description">Branch Description</label>
        <textarea class="form-control" id="description" name="description">{{ $branches->description ?? '' }}</textarea>
    </div>
    <div class="mb-3">
    <label>Open Days</label>
    <div class="d-flex flex-wrap">
        @php
            $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
            $selectedDays = old('open_days', isset($branches) && is_array($branches->open_days) ? $branches->open_days : []);
        @endphp

        @foreach ($days as $day)
            <div class="form-check me-3">
                <input
                    class="form-check-input"
                    type="checkbox"
                    id="day_{{ $day }}"
                    name="open_days[]"
                    value="{{ $day }}"
                    {{ in_array($day, $selectedDays) ? 'checked' : '' }}
                >
                <label class="form-check-label text-capitalize" for="day_{{ $day }}">{{ $day }}</label>
            </div>
        @endforeach
    </div>
</div>


    <div class="mb-3">
        <label for="open_time">Open Time</label>
        <input type="time" class="form-control" id="open_time" name="open_time" value="{{ $branches->open_time ?? '' }}">
    </div>

    <div class="mb-3">
        <label for="close_time">Close Time</label>
        <input type="time" class="form-control" id="close_time" name="close_time" value="{{ $branches->close_time ?? '' }}">
    </div>

    <div class="mb-3">
        <label for="status">Status</label>
        <select class="form-control" id="status" name="status">
            <option value="active" {{ (isset($branches) && $branches->status == 'active') ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ (isset($branches) && $branches->status == 'inactive') ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Close</button>
    </div>
</form>

 </x-modal>
<script>
    var user_longitude = "<?= $branches->long ?? '' ?>";
    var user_latitude = "<?= $branches->lat ?? '' ?>";
    function loadMap() {
        initMap(user_longitude, user_latitude);
    }
</script>
