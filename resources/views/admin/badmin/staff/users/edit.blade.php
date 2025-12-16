 @php
    $routeName = Auth::user()->role->name === 'branchadmin'
        ? 'badmin.staff.update'
        : 'admin.staff.update';
 @endphp
<x-modal id="editBranchUserModal" title="Edit Staff Details">
  <form action="{{ route($routeName, $staff->id) }}" method="POST" id="saveuserform">
    @csrf

<input type="hidden" name="user_id" value="{{ $staff->id }}">
    <!-- First Name -->
    <div class="col-md-12">
      <div class="mb-3">
        <label for="first_name">First Name</label>
        <input type="text" id="first_name" name="first_name" value="{{ $staff->first_name }}" class="form-control" placeholder="Enter First Name">
      </div>
    </div>

    <!-- Last Name -->
    <div class="col-md-12">
      <div class="mb-3">
        <label for="last_name">Last Name</label>
        <input type="text" id="last_name" name="last_name" value="{{ $staff->last_name }}" class="form-control" placeholder="Enter Last Name">
      </div>
    </div>

    <!-- Email -->
    <div class="col-md-12">
      <div class="mb-3">
        <label for="email">Enter Email</label>
        <input type="email" id="email" name="email" value="{{ $staff->email }}" class="form-control" placeholder="Enter Email">
      </div>
    </div>

    <!-- Employee ID -->
    <div class="col-md-12">
      <div class="mb-3">
        <label for="employee_id">Employee ID</label>
        <input type="text" id="employee_id" name="employee_id" value="{{ $staff->employee_id }}" class="form-control" placeholder="Enter Employee ID e.g. EMP0000">
      </div>
    </div>

    <!-- Phone -->
    <div class="col-md-12">
      <div class="mb-3">
        <label for="phone">Enter Number</label>
        <input type="text" id="phone" name="phone" value="{{ $staff->phone }}" class="form-control" placeholder="Enter Phone Number">
      </div>
    </div>

    <!-- Shift -->
    <div class="col-md-12">
      <div class="mb-3">
        <label for="shift_id">Select Shift</label>
        <select id="shift_id" name="shift_id" class="form-control">
          <option value="">Select Shift</option>
          @foreach($shifts as $shift)
            <option value="{{ $shift->id }}" {{ isset($staff->shift[0]->id) && $staff->shift[0]->id == $shift->id ? 'selected' : '' }}>
              {{ $shift->name }}
            </option>
          @endforeach
        </select>
      </div>
    </div>

    <!-- Role -->
    <div class="col-md-12">
      <div class="mb-3">
        <label for="role_id">Select Role</label>
        <select id="role_id" name="role_id" class="form-control">
          <option value="">Select Role</option>
          <option value="4" {{ $staff->role_id == 4 ? 'selected' : '' }}>Waiters</option>
          <option value="5" {{ $staff->role_id == 5 ? 'selected' : '' }}>Accountant</option>
          <option value="6" {{ $staff->role_id == 6 ? 'selected' : '' }}>Dispatcher</option>
        </select>
      </div>
    </div>

    <!-- Optional Password Update -->
    <div class="col-md-12">
      <div class="mb-3">
        <label for="password">Set New Pincode (optional)</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
      </div>
    </div>

    <div class="col-md-12">
      <div class="mb-3">
        <label for="password_confirmation">Confirm New Pincode</label>
        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Confirm New Password">
      </div>
    </div>

    <!-- Submit -->
    <div class="col-sm-12 mt-4 text-center">
      <div class="form-group">
        <button type="submit" class="btn btn-success">Update Staff</button>
      </div>
    </div>
  </form>
</x-modal>
