 <!-- Add Shift Modal -->
  @php
    $routeName = Auth::user()->role->name === 'branchadmin'
        ? 'badmin.shifts.store'
        : 'admin.shifts.store';
 @endphp
    <x-modal id="addShiftModal" title="Add New Shift">
        <form action="{{ route($routeName) }}" method="POST" id="addShiftForm">
            @csrf
            <div class="mb-3">
                <label>Shift Name</label>
                <input type="text" name="name" class="form-control" placeholder="Enter Shift Name" required>
            </div>
            <input type="hidden" value="{{$branchid}}" name="branchid">
            <div class="mb-3">
                <label>Start Time</label>
                <input type="time" name="start_time" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>End Time</label>
                <input type="time" name="end_time" class="form-control" required>
            </div>

      <div class="col-sm-12 mt-4 text-center">
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Add Shift</button>
        </div>
    </div>
        </form>
    </x-modal>
