<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\FcmService;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.pages.dashboard');
    }

    public function logout()
    {
        auth()->logout();
        session()->flash('success', 'You have been logged out!');

        return redirect()->route('login.admin');
    }

    public function login()
    {
        if (auth()->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function login_post(Request $req)
    {
        $credentials = $req->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            // dd($user->role->name);
            if ($user->user_status !== 'active') {
                Auth::logout();  // Log the user out if not active
                session()->flash('error', 'Your account is not active.');

                return redirect()->route('login.admin');
            }
            if ($user->role->name === 'admin') { // Role check
                session()->flash('success', 'You are logged in!');

                return redirect()->route('admin.dashboard');
            } elseif ($user->role->name === 'branchadmin') {
                return redirect()->route('badmin.dashboard');
            } else {
                Auth::logout();  // Role not allowed, log out the user
                session()->flash('error', 'You do not have permission to access this area.');

                return redirect()->route('login.admin');
            }
        } else {
            session()->flash('error', 'Invalid username or password');

            return redirect()->route('login.admin');
        }
    }

    // Breadcrumb Route
    public function users()
    {
        $roles = \App\Models\Role::where('id', '!=', 1)
            ->with([
                'users' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                },
            ])->get();

        return view('admin.pages.users.list', compact('roles'));
    }

    // profile_edit
    public function profile_edit($id)
    {
        $dec_id = Crypt::decrypt($id);
        $profile = User::find($dec_id);

        return view('admin.pages.standalone.profile', compact('profile'));
    }

    public function profile_edit_post(Request $req)
    {
        $req->validate([
            'email' => 'required',
            //   'name' => 'required',
            'password' => 'nullable|min:6', // Making password field optional and setting a minimum length
            'confirm_password' => 'nullable|same:password', // Validation for confirming password
        ]);

        // Decrypt the ID
        $dec_id = Crypt::decrypt($req->id);

        // Find the user by ID
        $edit = User::find($dec_id);

        // Update the user fields
        $edit->email = $req->email;
        $edit->first_name = $req->first_name;
        $edit->last_name = $req->last_name;

        // Check if password is provided and update it
        if ($req->filled('password')) {
            $edit->password = Hash::make($req->password);
        }

        // Save the changes
        $edit->update();

        // Flash a success message
        session()->flash('success', 'Profile Updated Successfully');

        // Redirect to the profile page
        return redirect('admin/profile/edit/'.$req->id);
    }

    public function testNotifaction()
    {
       $response = app(FcmService::class)->sendNotification(
             // 'dJcPyk_-Fresh5m7keDPxX:APA91bG0xSEccd3I004EGewhtbaiEMw2yoAS1usoGUF4NcuvNNeMariF7gqOoxrsYvNe0hE4WuBoxBFstbG2wWnJpKrMzox5q_tfK8lJIVQ6u0ckjgmvG8U', 'New Order Received!',
           'cUPLXRtW50QnmXAgqXph80:APA91bG6mWmB6kyN_ywqdW-1Lt3jYZRx5KxP2EkoyttXpmf0RRmiRZkuRqpmCB2E2C9U-s99vSUa17zDAgjzdugRaN6c1QTRQaBSxCfj9lDgJ6sKnNp830g', 'New Order Received!',
            'Order #12121 has been created.',
            [
                'order_id' => (string) 23232,
                'type' => 'new_order',
            ]
        );
          dd($response);
        exit;
        $userFcmToken = env('ADMIN_FCM_TOKEN');
        $accessToken = $this->getAccessToken();
       // dd($accessToken);
        $projectId = 'loop-9c8c8'; // Firebase Project ID

        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
        $payload = [
            'message' => [
                'token' => $userFcmToken,
                'notification' => [
                    'title' => 'New Order Received!',
                    'body' => 'Order #12121 has been created.',
                    //   / "icon"  => asset('assets/loop.gif'),
                    // "image" => "https://your-domain.com/images/order-banner.png"  // (optional large image)
                ],
                'data' => [
                    'order_id' => '154548485',
                    'type' => 'new_order',
                ],
            ],
        ];

        $response = Http::withToken($accessToken)
            ->post($url, $payload);

        return response()->json($response->json());
    }

  public function getAccessToken()
  {
      $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
      $jsonKeyFile = storage_path('app/firebase/firebase-key.json');

      $credentials = new ServiceAccountCredentials($scopes, $jsonKeyFile);
      $token = $credentials->fetchAuthToken();

      return $token['access_token'];
  }

    public function saveFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user = Auth::user();
        $user->fcm_token = $request->fcm_token;
        $user->save();

        return response()->json(['message' => 'FCM token saved successfully.']);
    }
}
