<x-modal id="branchuserModal" title="Add New user">
<form  action="{{ route('admin.branch.branchadmin.save') }}" id="saveuserform">
    @csrf
    <input type="hidden" name="branch_id" value="<?= $id ?>" />
     <input type="hidden" name="role_id" value="2" />
    <div class="col-md-12">
        <div class="mb-3">
            <label for="user_name">First Name</label>
            <input type="text" id="user_name" name="first_name" class="form-control" placeholder="Enter First Name">
        </div>
    </div>
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

    <div class="col-md-12">
        <div class="mb-3">
            <label for="phone_number">Enter Number</label>
            <input type="number" id="phone_number" name="phone" class="form-control" placeholder="Enter Number">
        </div>
    </div>

    <div class="col-md-12">
        <div class="mb-3">
            <label for="password">Set Password</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Enter Password">
        </div>
    </div>

    <div class="col-md-12">
        <div class="mb-3">
            <label for="c_password">Enter Password again</label>
            <input type="password" id="c_password" name="confirm_password" class="form-control" placeholder="Enter Password again">
        </div>
    </div>

    <div class="col-sm-12 mt-4 text-center">
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Add Admin</button>
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
