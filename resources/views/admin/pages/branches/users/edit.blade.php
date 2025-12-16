<x-modal id="branchuserModal" title="Edit user">
<form action="{{ route('admin.branch.branchadmin.update', $user->id) }}" method="POST" id="saveuserform">
    @csrf
    @method('PUT')
    <input type="hidden" name="branch_id" value="{{ $id }}" />
    <input type="hidden" name="user_id" value="{{ $user->id }}" />

    <div class="col-md-12">
        <div class="mb-3">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" class="form-control"
                value="{{ old('first_name', $user->first_name) }}" placeholder="Enter First Name">
        </div>
    </div>

    <div class="col-md-12">
        <div class="mb-3">
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" class="form-control"
                value="{{ old('last_name', $user->last_name) }}" placeholder="Enter Last Name">
        </div>
    </div>

    <div class="col-md-12">
        <div class="mb-3">
            <label for="email">Enter Email</label>
            <input type="email" id="email" name="email" class="form-control"
                value="{{ old('email', $user->email) }}" placeholder="Enter Email">
        </div>
    </div>

    <div class="col-md-12">
        <div class="mb-3">
            <label for="phone_number">Enter Number</label>
            <input type="number" id="phone_number" name="phone" class="form-control"
                value="{{ old('phone', $user->phone) }}" placeholder="Enter Number">
        </div>
    </div>

    <div class="col-md-12">
        <div class="mb-3">
            <label for="password">Set Password <small class="text-muted">(Leave blank to keep current)</small></label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Enter Password">
        </div>
    </div>

    <div class="col-md-12">
        <div class="mb-3">
            <label for="c_password">Enter Password again</label>
            <input type="password" id="c_password" name="password_confirmation" class="form-control"
                placeholder="Enter Password again">
        </div>
    </div>

    <div class="col-sm-12 mt-4 text-center">
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Update Admin</button>
        </div>
    </div>
</form>
 </x-modal>
  <script>
    $(document).ready(function() {
    $('#saveuserform').on('submit', function(e) {
        e.preventDefault();
        submitFormAjax('#saveuserform', '', {
        modalSelector: '#branchuserModal',
        successMessage: '',
        errorMessage: '',
        resetForm: true,
        reloadPage: true,
        token:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });
 });
});
 </script>
