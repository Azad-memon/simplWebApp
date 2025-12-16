@php
    $routeName = Auth::user()->role->name === 'branchadmin'
        ? 'badmin.shifts.update'
        : 'admin.shifts.update';
@endphp
<!-- Edit Modal -->
            <x-modal id="addShiftModal" title="Edit Shift">
                <form  action="{{ route($routeName, $shift) }}" method="POST" id="addShiftForm">
                    @csrf
                    <input type="hidden" name="shift_id" value="{{ $shift->id }}">
                    <div class="mb-3">
                        <label>Shift Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $shift->name }}" required>
                    </div>

                    <div class="mb-3">
                        <label>Start Time</label>
                        <input type="time" name="start_time" class="form-control" value="{{ $shift->start_time }}" required>
                    </div>

                    <div class="mb-3">
                        <label>End Time</label>
                        <input type="time" name="end_time" class="form-control" value="{{ $shift->end_time }}" required>
                    </div>


                <div class="col-sm-12 mt-4 text-center">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update Shift</button>
                    </div>
                </div>
                </form>
            </x-modal>
