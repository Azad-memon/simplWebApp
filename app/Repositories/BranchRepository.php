<?php

namespace App\Repositories;

use App\Models\BranchStaff;
use App\Models\StaffShift;
use App\Repositories\Interfaces\BranchRepositoryInterface;
use App\Models\Branch;
use App\Models\ModelUserActivityLog;
use App\Models\BranchUsers;
use App\Models\{User, Station};
use Hash;
use Auth;
use Log;
use DB;

class BranchRepository implements BranchRepositoryInterface
{
    public function all()
    {
        return Branch::all();
    }

    public function find($id)
    {
        return Branch::findOrFail($id);
    }


    public function create(array $data)
    {
        $savedata = Branch::create($data);
        if (!empty($savedata)) {
            ModelUserActivityLog::logActivity(
                Auth::user()->id,
                "has added new branch with ID " . $savedata->pb_id
            );
        }
        return $savedata;
    }

    public function update($id, array $data)
    {
        $branch = Branch::find($id);
        if ($branch) {
            return $branch->update($data);
        }
        return false;
    }
    public function toggleStatus($data)
    {
        //    / dd($data['status']);
        $branch = Branch::find($data['id']);
        if ($branch) {
            $branch->status = $data['status']; // Assuming status is stored as 1 for active and 0 for inactive
            if ($branch->save()) {
                ModelUserActivityLog::logActivity(
                    Auth::user()->id,
                    "has updated branch status with ID " . $branch->pb_id . " to " . ($data['status'] ? 'active' : 'inactive')
                );
                return true;
            }
        }
    }

    public function delete($id)
    {
        $branch = Branch::find($id);
        if ($branch) {
            return $branch->delete();
        }
        return false;
    }
    public function saveuser(array $data, $branch_id)
    {
        try {
            $saveUser = new User;
            $saveUser->first_name = $data['first_name'];
            $saveUser->last_name = $data['last_name'];
            $saveUser->email = $data['email'];
            $saveUser->phone = $data['phone'];
            $saveUser->password = $data['password'];
            $saveUser->role_id = User::ROLE_BRANCHADMIN;

            if ($saveUser->save()) {
                ModelUserActivityLog::logActivity(
                    Auth::user()->id,
                    "has registered a new user with Name " . $saveUser->name
                );
                return $saveUser;
            } else {
                return false;
            }
        } catch (\Exception $e) {

            \Log::error("Error saving user: " . $e->getMessage());
            return false;
        }
        //return Branch::saveuser($data);
    }
    public function addUserToBranchByBranchId($data, $branchId)
    {
        $userId = $data->id;
        $saveUserToBranch = new BranchUsers;
        $saveUserToBranch->branch_id = $branchId;
        $saveUserToBranch->user_id = $userId;
        if ($saveUserToBranch->save()) {
            ModelUserActivityLog::logActivity(
                Auth::user()->id,
                "add User <b>ucfirst($data->name)</b> to Branch with ID " . $branchId
            );
            return $saveUserToBranch->pbu_id;
        }

        return false;
    }

    public function finduser($id)
    {
        return User::findOrFail($id);
    }
    public function updateuser($data, $id)
    {
        $user = User::findOrFail($id);
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'];

        if (isset($data['password']) && $data['password'] != "") {
            $user->password = bcrypt($data['password']);
        }

        $updateResult = $user->save();
        if ($updateResult) {
            ModelUserActivityLog::logActivity(
                Auth::user()->id,
                "has updated User <b>" . ucfirst($data['first_name'])
            );
        }
        return $updateResult;

        // return User::findOrFail($id);
    }


    //branch staff
    public function findBranchStaff($id)
    {
        return Branch::findOrFail(id: $id)
            ->branchStaff()
            ->with('user')
            ->get();
    }
    public function findBranchStaffuser($id)
    {

        return User::where("id", $id)
            ->with('shift')
            ->first();

    }

    public function addbranchStaff($data)
    {

        try {
            $saveUser = new User;
            $saveUser->first_name = $data['first_name'];
            $saveUser->last_name = $data['last_name'];
            $saveUser->email = $data['email'];
            $saveUser->phone = $data['phone'] ?? null; // Optional phone number
            $saveUser->password = bcrypt($data['pincode']);
            $saveUser->role_id = $data['role_id'];
            $saveUser->employee_id = $this->generateUniqueEmployeeId(); // Optional employee ID


            if ($saveUser->save()) {
                ModelUserActivityLog::logActivity(
                    Auth::user()->id,
                    "has registered a new user with Name " . $saveUser->name
                );
                $branchStaff = new BranchStaff();
                $branchStaff->user_id = $saveUser->id;
                $branchStaff->branch_id = isset($data['branchid'])?$data['branchid']:Auth::user()->branches[0]->id; // Assuming branch ID is taken from authenticated user
                $branchStaff->shift_id = $data['shift_id'];
                if ($branchStaff->save()) {
                    ModelUserActivityLog::logActivity(
                        Auth::user()->id,
                        "has added a new branch staff with Name " . $saveUser->name
                    );
                    return $branchStaff;
                }

            } else {
                return false;
            }
        } catch (\Exception $e) {

            \Log::error("Error saving user: " . $e->getMessage());
            return false;
        }

    }
    public function updatebranchStaff($data, $id)
    {
        try {
            // Find the existing user and branch staff
            $saveUser = User::findOrFail($id); // Pass user_id in $data
            $branchStaff = BranchStaff::where('user_id', $saveUser->id)->firstOrFail();

            // Update user info
            $saveUser->first_name = $data['first_name'];
            $saveUser->last_name = $data['last_name'];
            $saveUser->email = $data['email'];
            $saveUser->phone = $data['phone'] ?? $saveUser->phone;

            // Update password only if provided
            if (!empty($data['password'])) {
                $saveUser->password = bcrypt($data['password']);
            }

            $saveUser->role_id = $data['role_id'];
            $saveUser->employee_id = $data['employee_id'] ?? $saveUser->employee_id;

            if ($saveUser->save()) {
                ModelUserActivityLog::logActivity(
                    Auth::user()->id,
                    "has updated user info for " . $saveUser->first_name . " " . $saveUser->last_name
                );

                // Update branch staff info
                $branchStaff->branch_id = $data['branch_id'] ?? $branchStaff->branch_id; // optional branch change
                $branchStaff->shift_id = $data['shift_id'] ?? $branchStaff->shift_id;

                if ($branchStaff->save()) {
                    ModelUserActivityLog::logActivity(
                        Auth::user()->id,
                        "has updated branch staff info for " . $saveUser->first_name . " " . $saveUser->last_name
                    );
                    return $branchStaff;
                }
            }

            return false;
        } catch (\Exception $e) {
            \Log::error("Error updating user: " . $e->getMessage());
            return false;
        }

    }
    public function generateUniqueEmployeeId()
    {
        do {
            // Generate a random 4-digit number and pad with leading zeros
            $randomNumber = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $employeeId = 'EMP' . $randomNumber;
        } while (\App\Models\User::where('employee_id', $employeeId)->exists());

        return $employeeId;
    }
    public function deleteBranchStaff($id)
    {
        try {
            $branchStaff = BranchStaff::where('user_id', $id)->firstOrFail();
            $branchStaff->delete();

            $user = User::findOrFail($id);
            $user->delete();

            ModelUserActivityLog::logActivity(
                Auth::user()->id,
                "has deleted branch staff with ID " . $id
            );

            return true;
        } catch (\Exception $e) {
            \Log::error("Error deleting branch staff: " . $e->getMessage());
            return false;
        }
    }
    public function toggleUserStatus($data)
    {
        //    / dd($data['status']);
        $branch = User::find($data['id']);
        if ($branch) {
            $branch->user_status = $data['status']; // Assuming status is stored as 1 for active and 0 for inactive
            if ($branch->save()) {
                ModelUserActivityLog::logActivity(
                    Auth::user()->id,
                    "has updated branch status with ID " . $branch->pb_id . " to " . ($data['status'] ? 'active' : 'inactive')
                );
                return true;
            }
        }
    }
    // End Branch staff

    // BRANCH SHIFT
    public function BranchShiftlist($id)
    {

        return StaffShift::where('branch_id', $id)->get();
    }

    public function findBranchShift($id)
    {
        return StaffShift::where('id', $id)->first();

    }
    public function BranchShiftCreate($data)
    {

        $shift = new StaffShift();
        $shift->name = $data['name'];
        $shift->start_time = $data['start_time'];
        $shift->end_time = $data['end_time'];
        $shift->branch_id = isset($data['branchid'])?$data['branchid']:Auth::user()->branches[0]->id; // Assuming branch ID is taken from authenticated user
        if ($shift->save()) {
            ModelUserActivityLog::logActivity(
                Auth::user()->id,
                "has created a new shift with name " . $data['name']
            );
            return $shift;
        }
        return false;

    }
    public function BranchShiftupdate($data)
    {

        $shift = StaffShift::findOrFail($data['shift_id']);
        $shift->name = $data['name'];
        $shift->start_time = $data['start_time'];
        $shift->end_time = $data['end_time'];
        if ($shift->save()) {
            ModelUserActivityLog::logActivity(
                Auth::user()->id,
                "has updated a shift with ID " . $data['shift_id']
            );
            return $shift;
        }
        return false;


    }
    public function BranchShiftDelete($id)
    {
        $shift = StaffShift::findOrFail($id);
        if ($shift->delete()) {
            ModelUserActivityLog::logActivity(
                Auth::user()->id,
                "has deleted a shift with ID " . $id
            );
            return true;
        }
        return false;
    }
    public function getBranch($data)
    {
        $nearestBranch = Branch::select(
            '*',
            DB::raw("6371 * acos(
            cos(radians(?))
            * cos(radians(lat))
            * cos(radians(`long`) - radians(?))
            + sin(radians(?))
            * sin(radians(lat))
        ) AS distance")
        )
            ->addBinding([$data['latitude'], $data['longitude'], $data['latitude']], 'select')
            ->whereNotNull('lat')
            ->whereNotNull('long')
            ->where('lat', '!=', 0)
            ->where('long', '!=', 0)
            ->where('status', 'active')
            ->having('distance', '<=', 10)
            ->orderBy('distance', 'ASC')
            ->first();

        return $nearestBranch;

    }
    public function allactive()
    {
        return Branch::where('status', 'active')->get();
    }

    public function createStation(array $data)
    {
        $station = Station::create([
            's_name' => $data['s_name'],
            'branch_id' => $data['branch_id'],
            'ip' => $data['ip'],
        ]);

        if (isset($data['categories'])) {
            $station->categories()->attach($data['categories']);
        }

        return $station;
    }

    public function getBranchStationData($branchId = null, $stationId = null)
    {
        $query = Station::with(['branch', 'categories']);

        if (!is_null($branchId)) {
            $query->where('branch_id', $branchId);
        }

        if (!is_null($stationId)) {
            $query->where('id', $stationId);
        }

        $normalData = $query->get();

        $formattedData = $normalData->map(function ($station) {
            return [
                'id' => $station->id,
                'name' => $station->s_name,
                'ip' => $station->ip,
                'categories' => $station->categories->pluck('name')->toArray(),
            ];
        })->toArray();

        return [
            'normal' => $normalData,
            'formatted' => $formattedData,
        ];
    }

    public function updateStation(Station $station, array $data)
    {
        $station->update([
            's_name' => $data['s_name'],
            'branch_id' => $data['branch_id'],
            'ip' => $data['ip'],
        ]);

        if (isset($data['categories'])) {
            $station->categories()->sync($data['categories']);
        }

        return $station;
    }


    public function findStation($id)
    {
        return Station::findOrFail($id);
    }


}
