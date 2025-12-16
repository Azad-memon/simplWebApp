
<x-modal id="branchModal" title="Add New Branch">
<script
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&callback=initMap"
    async defer></script>
   <form method="POST" action="{{ route('admin.branches.store') }}" id="branchForm">
    @csrf
 <input type="hidden" name="id" id="branch-id">

    <div class="mb-3">
        <label for="name">Branch Name</label>
        <input type="text" class="form-control" id="name" name="name">
    </div>
    <div class="mb-3">
    <label for="branch_code">Branch Code</label>
    <input type="text" class="form-control" id="branch_code" name="branch_code">
    </div>

    <div class="mb-3">
        <label for="name">Branch Phone</label>
        <input type="text" class="form-control" id="phone" name="phone">
    </div>
    <div class="mb-3">
        <label for="address">Branch Address</label>
        <input type="text" class="form-control map-input" id="location-input" name="address">
    </div>
    <div class="mb-3">
        <label for="city">City</label>
        <select class="form-control" id="city" name="city_id">
            @foreach ($cities as $city)
                <option value="{{ $city->id }}">{{ $city->city_name }}</option>
            @endforeach
        </select>
    </div>
    <!-- Google Map -->
     <div class="mb-3">
     <input id="pac-input" class="controls form-control" type="text" placeholder="Enter your address" />

        <div id="map" style="height: 400px; width: 100%; margin-top: 10px;"></div>

        <div id="selected-address" name="slected_address" style="font-weight: bold; margin-top: 10px;"></div>


        <input type="hidden" name="lat" id="latitude">
        <input type="hidden" name="long" id="longitude">
     </div>


    <div class="mb-3">
        <label for="description">Branch Description</label>
        <textarea class="form-control" id="description" name="description"></textarea>
    </div>
    <div class="mb-3">
    <label>Open Days</label>
    <div class="d-flex flex-wrap">
        @php
            $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
        @endphp
        @foreach ($days as $day)
            <div class="form-check me-3">
                <input class="form-check-input" type="checkbox" id="day_{{ $day }}" name="open_days[]" value="{{ $day }}">
                <label class="form-check-label text-capitalize" for="day_{{ $day }}">{{ $day }}</label>
            </div>
        @endforeach
    </div>
</div>


    <div class="mb-3">
        <label for="open_time">Open Time</label>
        <input type="time" class="form-control" id="open_time" name="open_time">
    </div>

    <div class="mb-3">
        <label for="close_time">Close Time</label>
        <input type="time" class="form-control" id="close_time" name="close_time">
    </div>

    <div class="mb-3">
        <label for="status">Status</label>
        <select class="form-control" id="status" name="status">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Close</button>
    </div>
</form>

 </x-modal>
