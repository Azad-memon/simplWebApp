<?php
namespace App\Http\Controllers\badmin;

use App\Http\Controllers\Controller;
use App\Models\StaffShift;
use App\Models\User;
use App\Repositories\Interfaces\BranchRepositoryInterface;
use Auth;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    protected $BranchRepository;

    public function __construct(BranchRepositoryInterface $BranchRepository)
    {
        $this->BranchRepository = $BranchRepository;

    }
    public function index()
    {
        // Fetch staff members for the branch admin
        $branchid = Auth::user()->branches[0]->id;
        $staff    = $this->BranchRepository->findBranchStaff($branchid);

        return view('admin.badmin.staff.users.list', compact('staff'));
    }

    public function create($branchid=null)
    {
        $branchId = isset($branchid)?$branchid:Auth::user()->branches[0]->id;
        $shifts = StaffShift::where("branch_id", $branchId)->get();
        return view('admin.badmin.staff.users.add', compact('shifts','branchId'));
    }

    public function store(Request $request)
    {

        // Validate and store new staff member
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',

            // Replace password with pincode (confirmed)
            'pincode'    => ['required', 'confirmed', 'digits:6'],

            'role_id'    => 'required|exists:roles,id',
            'shift_id'   => 'required|exists:staff_shifts,id',
            'phone'      => 'required',
            'branchid'      => 'nullable',

        ], [
            // Custom error message for pincode confirmation
            'pincode.confirmed' => 'The pincode confirmation does not match.',
        ]);

        $staff = $this->BranchRepository->addbranchStaff($data);

        if (! $staff) {
            return redirect()->back()->with('error', 'Failed to add staff member.');
        }
        return redirect()->back()->with('success', 'Staff member added successfully');

    }

    public function edit($id,$branchid=null)
    {

        // Edit staff member
        $branchId = isset($branchid)?$branchid:Auth::user()->branches[0]->id;
        $staff  = $this->BranchRepository->findBranchStaffuser($id);
        $shifts = StaffShift::where("branch_id", $branchId)->get();

        return view('admin.badmin.staff.users.edit', compact('staff', 'shifts','branchId'));

    }

    public function update(Request $request, $id)
    {

        $id = $request->input('user_id');

        // Validate and update staff member
        $data = $request->validate([
            'first_name'  => 'required|string|max:255',
            'last_name'   => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email,' . $id,
            'role_id'     => 'required|exists:roles,id',
            'shift_id'    => 'required|exists:staff_shifts,id',
            'phone'       => 'required',
            'employee_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (! preg_match('/^EMP\d{4}$/', $value)) {
                        $fail('The employee ID must be in the format EMP0000.');
                    }
                },
            ],
        ]);

        $staff = $this->BranchRepository->updatebranchStaff($data, $id);
        if (! $staff) {
            return redirect()->back()->with('error', 'Failed to add staff member.');
        }
        return redirect()->back()->with('success', 'Staff member Update successfully');
    }
    public function destroy($id)
    {
        // Delete staff member
        $staff = $this->BranchRepository->deleteBranchStaff($id);
        if ($staff) {
            return redirect()->back()->with('success', 'Staff member deleted successfully.');
        } else {
            return redirect()->back()->withErrors(['error' => 'Failed to delete staff member.']);
        }
    }

    public function toggleStatus(Request $request)
    {

        $data = $this->BranchRepository->toggleUserStatus($request->all());
        if ($data) {
            $message = $request->input('status') == "active" ? "User is activated" : "User is deactivated";
            return response()->json(['message' => $message]);
        }

    }

    public function showLoginForm(Request $request)
    {
        $request->session()->put('url.intended', url()->previous());
        return view('auth.staff-login');
    }
    public function login(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|string',
        ]);

        $employee = User::where('employee_id', $request->employee_id)
            ->where('user_status', 'active')
            ->first();

        if ($employee) {

            Auth::login($employee);

            $request->session()->regenerate();
            $user = Auth::user();
            if ($user->role->name == 'accountant' || $user->role->name == 'waiter' || $user->role->name == 'dispatcher') {
                return redirect()->route('pos.index');
            } else {
                Auth::logout();
                return redirect()->back()->withErrors([
                    'employee_id' => 'Your account does not have a valid role.',
                ]);
            }

        }


        return back()->withErrors([
            'employee_id' => 'Invalid Employee ID. Please try again.',
        ])->onlyInput('employee_id');
    }
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/employee/login');
    }
    public function staffdashboard()
    {
        return view('badmin.staff.dashboard');
    }

}
