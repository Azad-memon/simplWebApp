<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AddonIngredient;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Ingredient;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use Illuminate\Http\Request;

class CustomerController extends Controller
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
    public function index()
    {
        $customer = $this->customerRepository->all();
        return view('admin.pages.customer.list', compact('customer'));
    }
    public function show($id)
    {
        $customer = $this->customerRepository->find($id);

        return view('admin.pages.customer.details', compact('customer'));
    }
        public function showloyalty($id)
    {
        $customer = $this->customerRepository->find($id);
        return view('admin.pages.customer.show_Loyalty', compact('customer'));
    }






}
