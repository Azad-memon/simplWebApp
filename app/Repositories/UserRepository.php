<?php

namespace App\Repositories;

use App\Repositories\Interfaces\UsersRepositoryInterface;
use App\Models\ModelUserActivityLog;
use App\Models\User;
use Hash;
use Auth;
use Log;

class UserRepository implements UsersRepositoryInterface
{
    public function all()
    {
        return User::all();
    }

      public function delete($id)
    {
        $user = User::find($id);
        if (!$user) {
            return false;
        }
        $userId = $user->id;
        $userName = $user->name;
        $deleteResult = $user->delete();
        if ($deleteResult) {
            ModelUserActivityLog::logActivity(
                Auth::user()->id,
                "has deleted User <b>" . ucfirst($userName) . "</b> with ID " . $userId
            );
        }
        return $deleteResult;
    }
       public function toggleUserStatus($data)
    {
        $branch = User::where("id", $data['id'])->first();
        if (!$branch) {
            return false;
        }
        $newStatus = $data['status'];
        $statusText = $newStatus == "active" ? 'active' : 'inactive';

        $update = User::where("id", $data['id'])->update([
            'user_status' => $newStatus,
        ]);

        if ($update) {
            ModelUserActivityLog::logActivity(
                 Auth::user()->id,
                "has $statusText the User with ID " . $data['id']
            );
        }
        return $update;
    }

}
