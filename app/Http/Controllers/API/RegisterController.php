<?php

namespace App\Http\Controllers\API;

use App\Models\Customer;
use App\Models\ModelOtp;
use App\Models\UnverifiedCustomer;
use App\Services\OtpService;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Http\JsonResponse;
use App\Helpers\MessageHelper;
use bycrypt;
use Hash;

class RegisterController extends BaseController
{

    /**
     * Register a new user
     *
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Auth"},
     *     summary="Register a new user Customer",
     *     operationId="registerUser",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name", "last_name", "password", "c_password", "role_id"},
     *             @OA\Property(property="first_name", type="string", example="John"),
     *             @OA\Property(property="last_name", type="string", example="Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="phone", type="string", example="1234567890"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="c_password", type="string", format="password", example="password123"),
     *             @OA\Property(
     *                 property="role_id",
     *                 type="integer",
     *                 example=2,
     *                 description="Customer"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful registration",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User register successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string", example="1|abcd1234token"),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="first_name", type="string", example="John"),
     *                     @OA\Property(property="last_name", type="string", example="Doe"),
     *                     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *                     @OA\Property(property="phone", type="string", example="1234567890"),
     *                     @OA\Property(property="role_id", type="integer", example=2),
     *                     @OA\Property(property="status", type="integer", example=0),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-06-23T12:34:56Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-06-23T12:34:56Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation Error."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="The email has already been taken."))
     *             )
     *         )
     *     )
     * )
     */

    public function register(Request $request)
    {


        try {
           $language = $this->language;
           $validated = $request->validate(
                [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'email' => 'nullable|email|unique:customers,email',
                   // 'phone' =>  ['required', 'integer', 'unique:customers,phone'],
                    'phone' => 'required|digits_between:10,15|unique:customers,phone',
                    'password' => 'required',
                    'c_password' => 'required|same:password',
                    'role_id' => 'required',
                ],
                MessageHelper::defaultValidationMessages($language)
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = MessageHelper::formatErrors($e);

            $fields = ['validation.error'];
            $successMessages = MessageHelper::defaultSuccessMessages($language, $fields);
            $flatMessages = MessageHelper::formatMessages($successMessages);
            return $this->sendError($flatMessages[0] ?? "Errors", $errors, 200);
        }

        $existingUnverified = UnverifiedCustomer::where('email', $validated['email'])
            ->orWhere('phone', $validated['phone'])
            ->first();

        if ($existingUnverified) {
            $existingUnverified->delete();
        }

        $input = $request->all();
        $input['role_id'] = constant('App\Models\Customer::CUSTOMER');
      //  $input['status'] = constant('App\Models\Customer::INACTIVE_STATUS');
        $input['status'] = constant('App\Models\Customer::ACTIVE_STATUS');
        $input['password'] = bcrypt($input['password']);
       // $input['c_password'] = bcrypt($input['c_password']);

         try {
            $unverifiedCustomer = Customer::create($input);
            $success['token'] = $unverifiedCustomer->createToken('MyApp')->accessToken;
            $success['user'] = $unverifiedCustomer;
            return $this->sendResponse($success, $flatMessages[0] ?? "User register successfully.", 200);

        } catch (\Illuminate\Validation\ValidationException $e) {

            $errors = MessageHelper::formatErrors($e);

            $fields = ['validation.error'];
            $successMessages = MessageHelper::defaultSuccessMessages($language, $fields);
            $flatMessages = MessageHelper::formatMessages($successMessages);
            return $this->sendError($flatMessages[0] ?? "Errors", $errors, 422);
        }
//OTP Code
        // try {
        //     $unverifiedCustomer = UnverifiedCustomer::create($input);
        //     $success['token'] = $unverifiedCustomer->createToken('MyApp')->accessToken;
        //     $success['user'] = $unverifiedCustomer;

        //     if ($unverifiedCustomer) {
        //         try {
        //             $otpService = new OtpService();
        //             $otpService->generateAndSaveOtp(UnverifiedCustomer::class, $unverifiedCustomer->id);

        //             $fields = ['otp.success'];
        //             $successMessages = MessageHelper::defaultSuccessMessages($language, $fields);
        //             $flatMessages = MessageHelper::formatMessages($successMessages);

        //             return $this->sendResponse($success, $flatMessages[0], 200);

        //         } catch (\Exception $e) {
        //             $errors = MessageHelper::formatErrors($e);

        //             $fields = ['validation.error'];
        //             $successMessages = MessageHelper::defaultSuccessMessages($language, $fields);
        //             $flatMessages = MessageHelper::formatMessages($successMessages);
        //             return $this->sendError($flatMessages[0] ?? "Errors", $errors, 422);
        //         }
        //     } else {

        //     }

        // } catch (\Illuminate\Validation\ValidationException $e) {

        //     $errors = MessageHelper::formatErrors($e);

        //     $fields = ['validation.error'];
        //     $successMessages = MessageHelper::defaultSuccessMessages($language, $fields);
        //     $flatMessages = MessageHelper::formatMessages($successMessages);
        //     return $this->sendError($flatMessages[0] ?? "Errors", $errors, 422);
        // }
    }

    /**
     * verify-otp of unverified customer
     *
     * @OA\Post(
     *     path="/api/verify-otp",
     *     tags={"Auth"},
     *     summary="Verify Otp",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"language","otp"},
     *             @OA\Property(property="language", type="string", format="language", example="EN"),
     *             @OA\Property(property="otp", type="string", format="otp", example="123456")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Otp verified successful"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function verifyOtp(Request $request)
    {
        $input = $request->all();
      //  dd( $input);
        $otpValue = $input['otp'] ?? null;
        $language = $this->language;

        // Validate OTP input
        try {
            $validated = $request->validate(
                ['otp' => 'required'],
                MessageHelper::defaultValidationMessages($language)
            );
        } catch (\Illuminate\Validation\ValidationException $e) {

            $errors = MessageHelper::formatErrors($e);

            $fields = ['validation.error'];
            $successMessages = MessageHelper::defaultSuccessMessages($language, $fields);
            $flatMessages = MessageHelper::formatMessages($successMessages);
            return $this->sendError($flatMessages[0] ?? "Errors", $errors, 422);

        }

        $unverifiedCustomer = Auth::user();

        if ($unverifiedCustomer) {


            $userId = $unverifiedCustomer->id;

            // Find OTP linked to user
            $otp = ModelOtp::where("otpable_id", $userId)->first();

            if ($otp && $otp->otp_code == $otpValue) {
                // Prepare data for Customer creation
                $data = $unverifiedCustomer->toArray();
                $data['password'] = bcrypt($data['password']);
                $data['status'] = constant('App\Models\Customer::ACTIVE_STATUS');

                // Ensure password is set
                if (empty($data['password'])) {

                    return $this->sendError("Password is missing from unverified user record.", [], 500);
                }

                // Create verified customer
                $registerCustomer = Customer::create($data);

                if ($registerCustomer) {
                    // Optionally delete or mark OTP as used
                    $otp->delete();

                    // Delete unverified user (clean up)
                    $unverifiedCustomer->delete();

                    // Generate token
                    $success['token'] = $registerCustomer->createToken('MyApp')->accessToken;
                    $success['user'] = $registerCustomer;

                    // Prepare success message
                    $fields = ['otp.verify'];
                    $successMessages = MessageHelper::defaultSuccessMessages($language, $fields);
                    $flatMessages = MessageHelper::formatMessages($successMessages);

                    return $this->sendResponse($success, $flatMessages[0] ?? "OTP verified successfully", 200);
                } else {
                    $fields = ['error', 'error.unknown'];
                    $successMessages = MessageHelper::defaultSuccessMessages($language, $fields);
                    $flatMessages = MessageHelper::formatMessages($successMessages);

                    return $this->sendError($flatMessages[0] ?? "Error", $flatMessages[1] ?? "", 200);
                }
            } else {
                // OTP mismatch or not found
                $fields = ['otp.invalid', 'otp'];
                $successMessages = MessageHelper::defaultSuccessMessages($language, $fields);
                $flatMessages = MessageHelper::formatMessages($successMessages);

                return $this->sendError($flatMessages[1] ?? "OTP", $flatMessages[0] ?? "", 200);
            }
        } else {
            $fields = ['user.invalid', 'error'];
            $successMessages = MessageHelper::defaultSuccessMessages($language, $fields);
            $flatMessages = MessageHelper::formatMessages($successMessages);

            return $this->sendError($flatMessages[1] ?? "Error", $flatMessages[0] ?? "", 200);
        }
    }

    /**
     * resend Otp
     *
     * @OA\get(
     *     path="/api/resend-otp",
     *     tags={"Auth"},
     *     summary="Resend Otp",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *
     *         )
     *     ),
     *     @OA\Response(response=200, description="otp sent to your phone"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */


    public function resendOtp(Request $request)
    {
        $language = $this->language;

        $unverifiedCustomer = Auth::user();

        if ($unverifiedCustomer) {
            $otpService = new OtpService();
            $otpService->generateAndSaveOtp(UnverifiedCustomer::class, $unverifiedCustomer->id);

            $fields = ['otp.success', 'otp'];
            $successMessages = MessageHelper::defaultSuccessMessages($language, $fields);
            $flatMessages = MessageHelper::formatMessages($successMessages);

            return $this->sendResponse($flatMessages[1] ?? "OTP", $flatMessages[0], 200);
        } else {

            $fields = ['user.invalid', 'error'];
            $successMessages = MessageHelper::defaultSuccessMessages($language, $fields);
            $flatMessages = MessageHelper::formatMessages($successMessages);

            return $this->sendError($flatMessages[1] ?? "Error", $flatMessages[0] ?? "", 200);
        }
    }
    /**
     * login a user
     *
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Auth"},
     *     summary="Login user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="123456")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Successful login"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */


public function login(Request $request)
{
    $language = $this->language;
    $customer = Customer::where(['phone' => $request->phone, 'status' => 1]) ->first();
     try{
        $validated = $request->validate(
            [
                'phone' => 'required',
                'password' => 'required',
            ],
            MessageHelper::defaultValidationMessages($language)
        );
    }catch (\Illuminate\Validation\ValidationException $e) {
        $errors = MessageHelper::formatErrors($e);
        $fields = ['validation.error'];
        $successMessages = MessageHelper::defaultSuccessMessages($language, $fields);
        $flatMessages = MessageHelper::formatMessages($successMessages);
        return $this->sendError($flatMessages[0] ?? "Errors", $errors, 200);
    }
    if (!$customer) {
        return $this->sendError('Validation Error', [
            ['field' => 'phone', 'message' => 'Phone number not found.']
        ], 200);
    }

    // Check password
    if (!Hash::check($validated['password'], $customer->password)) {
        return $this->sendError('Validation Error', [
            ['field' => 'password', 'message' => 'Incorrect password.']
        ], 200);
    }
    $success['token'] = $customer->createToken('CustomerApp')->accessToken;
    $success['user'] = $customer;
    return $this->sendResponse($success, 'Customer login successfully.');
}
/**
 * reset password
 *
 * @OA\Post(
 *     path="/api/customer/reset-password",
 *     tags={"Auth"},
 *     summary="Reset Password",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"otp", "password", "confirm_password"},
 *             @OA\Property(property="otp", type="string", example="123456"),
 *             @OA\Property(property="password", type="string", format="password", example="newpassword123"),
 *             @OA\Property(property="confirm_password", type="string", format="password", example="newpassword123")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Password reset successfully"),
 *     @OA\Response(response=404, description="User not found")
 * )
 */

public function resetCustomerPassword(Request $request)
{
     $language = $this->language;
     try{
        $validated =$request->validate([
        'otp' => 'required',
        'password' => 'required',
        'c_password' => 'required|same:password',
    ], MessageHelper::defaultValidationMessages($language));
    }catch (\Illuminate\Validation\ValidationException $e) {
        $errors = MessageHelper::formatErrors($e);
        $fields = ['validation.error'];
        $successMessages = MessageHelper::defaultSuccessMessages($language, $fields);
        $flatMessages = MessageHelper::formatMessages($successMessages);
        return $this->sendError($flatMessages[0] ?? "Errors", $errors, 200);
    }
    // $verifyOtpPassword=$this->verifyOtpPassword($request); // Call the verifyOtpPassword method to validate OTP
    // $responseArray = $verifyOtpPassword instanceof \Illuminate\Http\JsonResponse
    // ? $verifyOtpPassword->getData(true) // true = associative array
    // : $verifyOtpPassword;

    // if (!isset($responseArray['Check_status']) || $responseArray['Check_status'] !== true) {
    //     return $verifyOtpPassword; // Return error response if OTP verification fails
    // }

          $otp = ModelOtp::where("otp_code", $request->otp)->first();
          if (!$otp) {
                $fields = ['otp.invalid', 'otp'];
                $successMessages = MessageHelper::defaultSuccessMessages($language, $fields);
                $flatMessages = MessageHelper::formatMessages($successMessages);

                return $this->sendError($flatMessages[1] ?? "OTP", $flatMessages[0] ?? "", 200);
            }

      $customer=customer::where('id',$otp->otpable_id )->first();
    // Fetch customer using reference from UnverifiedCustomer
   // $customer =$verifyOtpPassword['user'] ?? null;
    if (!$customer) {
        // If customer is not found, return error response
        $fields = ['user.invalid', 'error'];
        $successMessages = MessageHelper::defaultSuccessMessages($this->language, $fields);
        $flatMessages = MessageHelper::formatMessages($successMessages);
        return $this->sendError($flatMessages[1] ?? "Error", $flatMessages[0] ?? "", 404);
    }

        $customer->password = Hash::make($request->password);
        $customer->save();

        // Remove all tokens for this customer (revoke Passport tokens)
        $customer->tokens()->delete();

        // Optionally: Invalidate OTP
        $unverified = ModelOtp::where('otpable_id', $customer->id)->first();
        $unverified->delete(); // or mark as used


    // Return success response
    $fields = ['reset-password.success'];
    $successMessages = MessageHelper::defaultSuccessMessages($this->language, $fields);
    $flatMessages = MessageHelper::formatMessages($successMessages);
    $success['user'] = $customer;
    return $this->sendResponse($success, $flatMessages[0] ?? "Password reset successfully", 200);
}

    /**
     * Forgot password
     *
     * @OA\Post(
     *     path="/api/forgot-password",
     *     summary="Forgot Password",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *
     *         )
     *     ),
     *     @OA\Response(response=200, description="Successful login"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function forgotPassword(Request $request)
    {
        $input = $request->all();
        $language = $this->language;

        // Validate OTP input
        try {
            $validated = $request->validate(
                [
                    'phone' => 'required',
                    'language' => 'required'
                ],
                MessageHelper::defaultValidationMessages($language)
            );
        } catch (\Illuminate\Validation\ValidationException $e) {

            $errors = MessageHelper::formatErrors($e);

            $fields = ['validation.error'];
            $successMessages = MessageHelper::defaultSuccessMessages($language, $fields);
            $flatMessages = MessageHelper::formatMessages($successMessages);
            return $this->sendError($flatMessages[0] ?? "Errors", $errors, 200);

        }
        $customer = Customer::where(['phone' => $validated['phone']])->first();
        if ($customer) {
            $otpService = new OtpService();
            $otpService->generateAndSaveOtp(Customer::class, $customer->id);

            $fields = ['otp.success', 'otp'];
            $successMessages = MessageHelper::defaultSuccessMessages($language, $fields);
            $flatMessages = MessageHelper::formatMessages($successMessages);

            return $this->sendResponse($flatMessages[1] ?? "OTP", $flatMessages[0], 200);
        } else {
            // $fields = ['user.invalid', 'error'];
            // $successMessages = MessageHelper::defaultSuccessMessages($language, $fields);
            // $flatMessages = MessageHelper::formatMessages($successMessages);

            // return $this->sendError("Validation Error" ?? "Error", $flatMessages[0] ?? "", 200);
             return $this->sendError('Validation Error', [
            ['field' => 'phone', 'message' => 'Phone number not found.']
        ], 200);
        }

    }
    public function verifyOtpPassword(Request $request)
    {
        $input = $request->all();
        $otpValue = $input['otp'] ?? null;
        $language = $this->language;
        // Validate OTP input
        try {
            $validated = $request->validate(
                ['otp' => 'required'],
                MessageHelper::defaultValidationMessages($language)
            );
        } catch (\Illuminate\Validation\ValidationException $e) {

            $errors = MessageHelper::formatErrors($e);

            $fields = ['validation.error'];
            $successMessages = MessageHelper::defaultSuccessMessages($language, $fields);
            $flatMessages = MessageHelper::formatMessages($successMessages);
            return $this->sendError($flatMessages[0] ?? "Errors", $errors, 422);

        }
            $otp = ModelOtp::where("otp_code", $input['otp'])->first();
            if (!$otp) {
                $fields = ['otp.invalid', 'otp'];
                $successMessages = MessageHelper::defaultSuccessMessages($language, $fields);
                $flatMessages = MessageHelper::formatMessages($successMessages);

                return $this->sendError($flatMessages[1] ?? "OTP", $flatMessages[0] ?? "", 200);
            }
            $registerCustomer = false;
            $registerCustomer=customer::where('id',$otp->otpable_id )->first();
            if ($otp && $otp->otp_code == $otpValue) {
                // Prepare data for Customer creation
                if ($registerCustomer) {
                    // Optionally delete or mark OTP as used
                   // $otp->delete();

                    $fields = ['otp.verify'];
                    $successMessages = MessageHelper::defaultSuccessMessages($language, $fields);
                    $flatMessages = MessageHelper::formatMessages($successMessages);
                    $success['user'] = $registerCustomer;
                    $success['Check_status'] = true;

                    //return $success;
                    return $this->sendResponse($success, $flatMessages[0] ?? "OTP verified successfully", 200);
                } else {
                    $fields = ['error', 'error.unknown'];
                    $successMessages = MessageHelper::defaultSuccessMessages($language, $fields);
                    $flatMessages = MessageHelper::formatMessages($successMessages);

                    return $this->sendError($flatMessages[0] ?? "Error", $flatMessages[1] ?? "", 200);
                }
            } else {
                // OTP mismatch or not found
                $fields = ['otp.invalid', 'otp'];
                $successMessages = MessageHelper::defaultSuccessMessages($language, $fields);
                $flatMessages = MessageHelper::formatMessages($successMessages);

                return $this->sendError($flatMessages[1] ?? "OTP", $flatMessages[0] ?? "", 200);
            }

    }

}
