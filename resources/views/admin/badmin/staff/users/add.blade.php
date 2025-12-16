  @php
    $routeName = Auth::user()->role->name === 'branchadmin'
        ? 'badmin.staff.store'
        : 'admin.staff.store';
 @endphp
<x-modal id="branchuserModal" title="Add New Saff">

<form method="POST" action="{{ route($routeName) }}" id="saveuserform">
    @csrf
    <div class="col-md-12">
        <div class="mb-3">
            <label for="user_name">First Name</label>
            <input type="text" id="user_name" name="first_name" class="form-control" placeholder="Enter First Name">
        </div>
    </div>
    <input type="hidden" value="{{$branchId}}" name="branchid">
      <div class="col-md-12">
        <div class="mb-3">
            <label for="user_name">Last Name</label>
            <input type="text" id="user_name" name="last_name" class="form-control" placeholder="Enter last Name">
        </div>
    </div>
    <div class="col-md-12">
        <div class="mb-3">
            <label for="email">Enter Email</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="Enter Email">
        </div>
    </div>

    {{-- <div class="col-md-12">
        <div class="mb-3">
            <label for="user_name">Employee id</label>
            <input type="text" id="" name="employee_id" class="form-control" placeholder="Enter Employee eg:EMP0000">
        </div>
    </div> --}}
    <div class="col-md-12">
        <div class="mb-3">
            <label for="phone_number">Enter Number</label>
            <input type="text" id="phone_number" name="phone" class="form-control" placeholder="Enter Number">
        </div>
    </div>
     <div class="col-md-12">
        <div class="mb-3">
            <label for="shift_id">Select Shift</label>
            <select id="shift_id" name="shift_id" class="form-control">
                <option value="">Select Shift</option>
                @foreach($shifts as $shift)
                    <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-12">
        <div class="mb-3">
            <label for="role_id">Select Role</label>
            <select id="role_id" name="role_id" class="form-control">
                <option value="">Select Role</option>
                <option value="4">Waiter</option>
                <option value="5">Accountant</option>
                <option value="6">Dispatcher</option>
            </select>
        </div>
    </div>

    <div class="col-md-12">
        <div class="mb-3">
            <label for="password">Set Pincode</label>
            <input type="password" id="password" name="pincode" class="form-control" placeholder="Enter Pincode">
        </div>
    </div>

    <div class="col-md-12">
        <div class="mb-3">
            <label for="c_password">Enter Pincode again</label>
            <input type="password" id="c_password" name="pincode_confirmation" class="form-control" placeholder="Enter Pincode again">
        </div>
    </div>

    <div class="col-sm-12 mt-4 text-center">
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Add Staff</button>
        </div>
    </div>
</form>
 </x-modal>
