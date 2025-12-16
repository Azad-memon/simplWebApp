<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\BranchRepositoryInterface;
use App\Repositories\Interfaces\UsersRepositoryInterface;
use App\Repositories\Interfaces\IngredientRepositoryInterface;
use App\Models\City;

class BranchController extends Controller
{
   protected $branchRepository;
   protected $userRepository;
   protected $ingredientRepository;

    public function __construct(BranchRepositoryInterface $branchRepository,UsersRepositoryInterface $userRepository,IngredientRepositoryInterface $ingredientRepository)
    {
        $this->branchRepository = $branchRepository;
        $this->userRepository = $userRepository;
        $this->ingredientRepository = $ingredientRepository;
    }

    public function index()
    {
        $branches = $this->branchRepository->all();
        return view('admin.pages.branches.list', compact('branches'));
    }
    public function addbranch()
    {
        $cities = City::all();

         return view('admin.pages.branches.add',compact('cities'))->render();
    }
      public function editbranch($id)
    {

         $branches = $this->branchRepository->find($id);
         $cities = City::all();

         return view('admin.pages.branches.edit',compact('branches','cities'))->render();
    }
     public function view($id)
    {   $branch = $this->branchRepository->find($id);
        $orders = $branch->orders;
        $inventories = $this->ingredientRepository->getBranchIngredients($id);
        $shifts = $this->branchRepository->BranchShiftlist($id); //now work for shifts
        $staffs    = $this->branchRepository->findBranchStaff($id);
        // dd($inventories);
        return view('admin.pages.branches.view',compact('branch','orders','inventories','shifts','staffs'));
    }
    // public function create()
    // {
    //     return view('admin.pages.branches.create');
    // }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'lat' => 'required|numeric',
            'long' => 'required|numeric',
            'description' => 'nullable|string',
            'status' => 'required|string',
            'open_time' => 'required|date_format:H:i',
            'close_time' => 'required|date_format:H:i',
            'open_days' => 'nullable|array',
            'branch_code' => 'required|string|unique:branches,branch_code',
            'city_id' => 'required|exists:cities,id',
        ]);

        $this->branchRepository->create($data);
        return response()->json(['message' => 'Branch created successfully.']);

    }
    public function toggleStatus(Request $request)
    {
        //dd($request->all());
       $data = $this->branchRepository->toggleStatus($request->all());
       if ($data) {
            $message = $request->input('status')==1 ? "Branch is activated" : "Branch is deactivated";
            return response()->json(['message' =>  $message]);
        }
    }

    public function show($id)
    {
        $branch = $this->branchRepository->find($id);
        return view('admin.pages.branches.show', compact('branch'));
    }

    public function edit($id)
    {
        $branch = $this->branchRepository->find($id);
        return view('admin.pages.branches.edit', compact('branch'));
    }

    public function update(Request $request, $id)
    {
        $request->merge([
            'open_time' => \Carbon\Carbon::parse($request->open_time)->format('H:i'),
            'close_time' => \Carbon\Carbon::parse($request->close_time)->format('H:i'),
            ]);
       // dd( $request->all());

        $data = $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'lat' => 'required|numeric',
            'long' => 'required|numeric',
            'description' => 'nullable|string',
            'status' => 'required|string',
            'open_time' => 'required|date_format:H:i',
            'close_time' => 'required|date_format:H:i',
            'open_days' => 'nullable|array',
            'branch_code' => 'required|string|unique:branches,branch_code,'.$id,
            'city_id' => 'required|exists:cities,id',
        ]);

        $this->branchRepository->update($id, $data);
        return response()->json(['message' => 'Branch updated successfully.']);
    }

    public function destroy($id)
    {
        $this->branchRepository->delete($id);
        return redirect()->route('admin.pages.branches.index')->with('success', 'Branch deleted successfully.');
    }
     public function adduser($id)
     {
     return view('admin.pages.branches.users.add',compact('id'))->render();
     }
     public function edituser($id)
     {
        $user = $this->branchRepository->finduser($id);
     return view('admin.pages.branches.users.edit',compact('id','user'))->render();
     }
      public function deleteuser($id,$branchid)
     {
          $this->userRepository->delete($id);
   //     $user = $this->branchRepository->finduser($id);
        return redirect()->back()->with('success', 'User deleted successfully.');
     }
      public function updateuser(Request $request,$id)
     {
    $data = $request->validate([
        'first_name'       => 'required|string|max:255',
        'last_name'        => 'required|string|max:255',
        'email'            => 'required|email|unique:users,email,' . $id,
        'phone'            => 'nullable|string|max:20',
        'password'         => 'nullable|min:6|confirmed',
     ]);

    $user = $this->branchRepository->updateuser($data,$id);
    return response()->json(['message' => 'User updated successfully']);
    }
     public function saveuser(Request $request){
    $data = $request->validate([
        'branch_id'        => 'required|exists:branches,id',
        'role_id'          => 'required',
        'first_name'       => 'required|string|max:255',
        'last_name'        => 'required|string|max:255',
        'email'            => 'required|email|unique:users,email',
        'phone'            => 'required|string|max:15',
        'password'         => 'required',
        'confirm_password' => 'required|same:password',
    ]);

          try {
        $userData = [
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'],
            'role_id'      => $data['role_id'],
            'password'   => bcrypt($data['password']),
        ];


        $addUser = $this->branchRepository->saveUser($userData, $data['branch_id']);
        if (!$addUser) {
            return response()->json([
                'message' => 'Failed to create user.',
            ], 500);
        }
        $this->branchRepository->addUserToBranchByBranchId($addUser, $data['branch_id']);

        return response()->json([
            'message' => 'User created successfully.',
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'An error occurred while saving the user.',
            'error'   => $e->getMessage(),
        ], 500);
    }


   }
   public function superadminToggleUserStatus(Request $request)
    {

        $data = $this->userRepository->toggleUserStatus($request->all());
       if ($data) {
            $message = $request->input('status')=="active" ? "User is activated" : "User is deactivated";
            return response()->json(['message' =>  $message]);
        }

    }
}
