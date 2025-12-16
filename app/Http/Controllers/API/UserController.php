<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Validator;

/**
 * @OA\Tag(
 *     name="User Management",
 *     description="APIs for managing users"
 * )
 */
class UserController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/users",
     *     tags={"User Management"},
     *     summary="Get all users",
     *     security={{"passport":{}}},
     *     @OA\Response(response=200, description="Users fetched successfully")
     * )
     */
    public function index()
    {
        $users = User::with('role')->where('role_id',"!=",1)->get(); // Eager load role to display with users
        return $this->sendResponse($users, 'Users retrieved successfully.');
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     tags={"User Management"},
     *     summary="Create new user",
     *     security={{"passport":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string", example="John"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="123456"),
     *             @OA\Property(property="role_id", type="integer", example=2),
     *         )
     *     ),
     *     @OA\Response(response=201, description="User created")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role_id'  => 'required|exists:roles,id|in:2,3' // Only allow non-admin roles (2, 3)
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        // Create new user with role_id
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
            'role_id'  => $request->role_id, // Assign the role_id
        ]);

        return $this->sendResponse($user, 'User created successfully.');
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     tags={"User Management"},
     *     summary="Get user by ID",
     *     security={{"passport":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="User found"),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function show($id)
    {
        $user = User::with('role')->find($id); // Eager load role with user
        if (!$user) return response()->json(['success' => false, 'message' => 'User not found'], 404);

        return $this->sendResponse($user, 'User retrieved successfully.');
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     tags={"User Management"},
     *     summary="Update user by ID",
     *     security={{"passport":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="role_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(response=200, description="User updated")
     * )
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['success' => false, 'message' => 'User not found'], 404);

        $user->update($request->only('name', 'email'));
        return $this->sendResponse($user, 'User updated successfully.');
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     tags={"User Management"},
     *     summary="Delete user by ID",
     *     security={{"passport":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="User deleted")
     * )
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['success' => false, 'message' => 'User not found'], 404);

        $user->delete();
        return $this->sendResponse([], 'User deleted successfully.');
    }
}

