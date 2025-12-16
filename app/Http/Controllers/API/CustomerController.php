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
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use App\Repositories\CustomerRepository;
use bycrypt;
use Hash;

class CustomerController extends BaseController
{



    protected $customerRepository;
    protected $language;

    /**
     * CustomerController constructor.
     * @param CustomerRepositoryInterface $customerRepository
     */

     public function __construct(Request $request,CustomerRepositoryInterface $customerRepository)
    {

        $this->customerRepository = $customerRepository;
        $this->language = $request->input('language', 'EN');
    }


    /**
     * Get the authenticated customer profile.
     *
     * @return JsonResponse
     */
    public function profile()
    {
        $customer = Auth::guard('customer')->user();
        $customer = $this->customerRepository->find($customer->id);

        // Replace 'images' with the main image URL or path
        $customer->image = isset($customer->images_or_default[0])
            ? ($customer->images_or_default[0]->url ?? $customer->images_or_default[0]->image)
            : null;
         unset($customer->images);

        if (!$customer) {
            return $this->sendError('Customer not found.', [], 404);
        }
       return $this->sendResponse($customer, 'Customer profile retrieved successfully.');
    }

    /**
     * Update the authenticated customer profile.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateProfile(Request $request)
    {

        $customer = Auth::guard('customer')->user();
        $language = $this->language;
        try {
            $validated = $request->validate(
                [
                     'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
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
        $data = saveImagesApp($request->image);
        $customer=$this->customerRepository->update($customer->id, [
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'images'     =>$data
        ]);
          $customer->image = isset($customer->images_or_default[0])
            ? ($customer->images_or_default[0]->url ?? $customer->images_or_default[0]->image)
            : null;
         unset($customer->images);
        return $this->sendResponse($customer, 'Customer profile updated successfully.');
    }
    /**
     * Change the authenticated customer's password.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function changePassword(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $language = $this->language;
        try {

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string|min:6',
            'new_password' => 'required|string|min:6|confirmed',
        ]);
        }catch (\Exception $e) {
            $errors = MessageHelper::formatErrors($e);
            $fields = ['validation.error'];
            $successMessages = MessageHelper::defaultSuccessMessages($language, $fields);
            $flatMessages = MessageHelper::formatMessages($successMessages);
            return $this->sendError($flatMessages[0] ?? "Errors", $errors, 422);
        }
       // Step 2: Check Current Password
    if (!Hash::check($request->current_password, $customer->password)) {
        $errors = [
            [
                'field' => 'current_password',
                'message' => $language === 'UR'
                    ? 'موجودہ پاس ورڈ درست نہیں ہے۔'
                    : 'Current password is incorrect.',
            ]
        ];

        $fields = ['validation.error'];
        $successMessages = MessageHelper::defaultSuccessMessages($language, $fields);
        $flatMessages = MessageHelper::formatMessages($successMessages);
        return $this->sendError($flatMessages[0] ?? "Errors", $errors, 422);
    }

           $customer=$this->customerRepository->updatepassword($customer->id, [
            'password' =>$request->new_password,
           ]);

            $customer->tokens()->delete();
             $success['token'] = $customer->createToken('CustomerApp')->accessToken;
             $success['user'] = $customer;
        return $this->sendResponse($success, 'Password changed successfully.');

    }


}
