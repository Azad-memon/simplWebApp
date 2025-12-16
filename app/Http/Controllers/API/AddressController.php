<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Http\JsonResponse;
use App\Helpers\MessageHelper;
use bycrypt;
use Hash;
use App\Models\CustomerAddress;
use App\Repositories\Interfaces\CustomerAddressRepositoryInterface;
use App\Repositories\CustomerAddressRepository;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
/**
 * Class AddressController
 * @package App\Http\Controllers\API
 */

class AddressController extends BaseController
{

    protected $customeraddressRepository;
    protected $customerRepository;
    protected $language;

    /**
     * AddressController constructor.
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(Request $request, CustomerRepositoryInterface $customerRepository, CustomerAddressRepositoryInterface $customeraddressRepository)
    {
        $this->customerRepository = $customerRepository;
        $this->customeraddressRepository = $customeraddressRepository;
        $this->language = $request->input('language', 'EN');
    }

    /**
     * Get the authenticated customer profile.
     *
     * @return JsonResponse
     */
    public function add(Request $request)
    {

        try{
            $customer = Auth::guard('customer')->user();
            $customer = $this->customerRepository->find($customer->id);
            if (!$customer) {
                return $this->sendError('Customer not found.', [], 404);
            }
            $data =$request->all();
             $validated = $request->validate([
                'title' => 'required|string|max:255|unique:customer_addresses,title,NULL,id,customer_id,' . $customer->id,
                'street_address' => 'required|string|max:255',
                'address' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'zip_code' => 'nullable|string|max:20',
                'country' => 'nullable|string|max:100',
                'additional_instructions' => 'nullable|string|max:255',
                'nearest_landmark' => 'nullable|string|max:255',
            ],
           MessageHelper::defaultValidationMessages($this->language)
          );
           }catch (\Illuminate\Validation\ValidationException $e) {
            $errors = MessageHelper::formatErrors($e);
            $fields = ['validation.error'];
            $successMessages = MessageHelper::defaultSuccessMessages($this->language, $fields);
            $flatMessages = MessageHelper::formatMessages($successMessages);
            return $this->sendError($flatMessages[0] ?? "Errors", $errors, 200);
           }
            $data['customer_id'] = $customer->id;
            $addrgetAddressesByCustomerIdess = $this->customeraddressRepository->titleExists($data['title'],$customer->id);
            if($addrgetAddressesByCustomerIdess){
                return $this->sendError('Validation Error', [
                    ['field' => 'title', 'message' => 'Address with this title already exists.']
                ], 200);;
            }
            $address = $this->customeraddressRepository->create($data);
            if (!$address) {
                return $this->sendError('Failed to add address.', [], 200);
            }
            return $this->sendResponse($address, 'Address added successfully.');
    }
    /**
     * Update the specified address for the authenticated customer.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        try {
            $customer = Auth::guard('customer')->user();
            $customer = $this->customerRepository->find($customer->id);
            if (!$customer) {
                return $this->sendError('Customer not found.', [], 404);
            }
            $data = $request->all();
           $validated = $request->validate([
                //'addreess_id'=>'required|integer',
                'street_address' => 'required|string|max:255',
                'address' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'zip_code' => 'nullable|string|max:20',
                'country' => 'nullable|string|max:100',
                'additional_instructions' => 'nullable|string|max:255',
                'nearest_landmark' => 'nullable|string|max:255',
            ],
            MessageHelper::defaultValidationMessages($this->language)
            );
            // if ($validator->fails()) {
            //     $errors = MessageHelper::formatErrors($validator);
            //     $fields = ['validation.error'];
            //     $successMessages = MessageHelper::defaultSuccessMessages($this->language, $fields);
            //     $flatMessages = MessageHelper::formatMessages($successMessages);
            //     return $this->sendError($flatMessages[0] ?? "Errors", $errors, 200);
            // }

            $updated = $this->customeraddressRepository->updateAddressByCustomer($customer->id, $data);
            if (!$updated) {
                return $this->sendError('Failed to update address.', [], 200);
            }

            return $this->sendResponse($updated, 'Address updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = MessageHelper::formatErrors($e);
            $fields = ['validation.error'];
            $successMessages = MessageHelper::defaultSuccessMessages($this->language, $fields);
            $flatMessages = MessageHelper::formatMessages($successMessages);
            return $this->sendError($flatMessages[0] ?? "Errors", $errors, 200);
        } catch (\Exception $e) {
            return $this->sendError('An error occurred.', [$e->getMessage()], 500);
        }
    }
    /**
     * Get all addresses for the authenticated customer.
     *
     * @return JsonResponse
     */
    public function getAll()
    {
        try {
            $customer = Auth::guard('customer')->user();
            $customer = $this->customerRepository->find($customer->id);
            if (!$customer) {
                return $this->sendError('Customer not found.', [], 404);
            }
            $addresses = $this->customeraddressRepository->getAddressesByCustomerId($customer->id);

            return $this->sendResponse($addresses, 'Addresses retrieved successfully.');
        } catch (\Exception $e) {
            return $this->sendError('An error occurred.', [$e->getMessage()], 500);
        }
    }



/**
 * Set the specified address as default for the authenticated customer.
 *
 * @param Request $request
 * @return JsonResponse
 */
public function setDefault(Request $request)
{
    try {
        $customer = Auth::guard('customer')->user();
        $customer = $this->customerRepository->find($customer->id);
        if (!$customer) {
            return $this->sendError('Customer not found.', [], 404);
        }

        $data = $request->all();
        $validated = $request->validate([
            'address_id' => 'required|integer',
        ], MessageHelper::defaultValidationMessages($this->language));
        $addressId = $data['address_id'];
        $updated = $this->customeraddressRepository->setDefaultAddress($customer->id, $addressId);

        if (!$updated) {
           return $this->sendError('Validation Error', [
            ['field' => 'address', 'message' => 'Failed to set default address.']
        ], 200);
        }

        return $this->sendResponse(null, 'Default address set successfully.');
   } catch (\Illuminate\Validation\ValidationException $e) {
        $errors = MessageHelper::formatErrors($e);
        $fields = ['validation.error'];
        $successMessages = MessageHelper::defaultSuccessMessages($this->language, $fields);
        $flatMessages = MessageHelper::formatMessages($successMessages);
       return $this->sendError($flatMessages[0] ?? "Errors", $errors, 200);
    } catch (\Exception $e) {
        return $this->sendError('An error occurred.', [$e->getMessage()], 500);
    }
}
/**
 * Delete the specified address for the authenticated customer.
 *
 * @param Request $request
 * @return JsonResponse
 */
public function destroy(Request $request)
{
    try {
        $customer = Auth::guard('customer')->user();
         $customer = $this->customerRepository->find($customer->id);
        if (!$customer) {
            return $this->sendError('Customer not found.', [], 404);
        }

        $validated = $request->validate([
            'address_id' => 'required|integer',
        ], MessageHelper::defaultValidationMessages($this->language));

        $addressId = $request->input('address_id');
        $deleted = $this->customeraddressRepository->deleteAddressByCustomer($addressId,$customer->id);

        if (!$deleted) {
            return $this->sendError('Failed to delete address.', [], 200);
        }

        return $this->sendResponse(null, 'Address deleted successfully.');
    } catch (\Illuminate\Validation\ValidationException $e) {
        $errors = MessageHelper::formatErrors($e);
        $fields = ['validation.error'];
        $successMessages = MessageHelper::defaultSuccessMessages($this->language, $fields);
        $flatMessages = MessageHelper::formatMessages($successMessages);
        return $this->sendError($flatMessages[0] ?? "Errors", $errors, 200);
    } catch (\Exception $e) {
        return $this->sendError('An error occurred.', [$e->getMessage()], 500);
    }
}

}
