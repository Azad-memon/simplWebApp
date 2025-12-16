<?php

namespace App\Http\Controllers\badmin;

use App\Http\Controllers\Controller;
use App\Models\ShiftCashNote;
use App\Models\ShiftIngredient;
use App\Repositories\Interfaces\BranchRepositoryInterface;
use App\Repositories\Interfaces\IngredientRepositoryInterface;
use Auth;
use Illuminate\Http\Request;

class StaffShiftController extends Controller
{
    protected $BranchRepository;

    protected $ingredientRepository;

    public function __construct(BranchRepositoryInterface $BranchRepository, ingredientRepositoryInterface $ingredientRepository)
    {
        $this->BranchRepository = $BranchRepository;
        $this->ingredientRepository = $ingredientRepository;
    }

    public function index()
    {
        $branchid = Auth::user()->branches[0]->id;
        $shifts = $this->BranchRepository->BranchShiftlist($branchid);

        return view('admin.badmin.staff.shift.index', compact('shifts'));
    }

        public function create($branchid=null)
        {
            return view('admin.badmin.staff.shift.add',compact('branchid'));
        }

    public function edit($id)
    {
        // Edit staff member
        $shift = $staff = $this->BranchRepository->findBranchShift($id);
        return view('admin.badmin.staff.shift.edit', compact('shift'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $shifts = $this->BranchRepository->BranchShiftCreate($request->only('name', 'start_time', 'end_time','branchid'));

        return redirect()->back()->with('success', 'Shift added successfully!');
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $shifts = $this->BranchRepository->BranchShiftupdate($request->all());

        return redirect()->back()->with('success', 'Shift updated successfully!');
    }

    public function destroy($id)
    {
        $shifts = $this->BranchRepository->BranchShiftDelete($id);

        return redirect()->back()->with('success', 'Shift deleted successfully!');
    }

}
