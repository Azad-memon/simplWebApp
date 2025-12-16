<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Controller as Controller;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Repositories\SettingRepository;

/**
 * Class AddressController
 * @package App\Http\Controllers\API
 */

class SettingController extends Controller
{


    protected $SettingRepository;

    public function __construct(Request $request, SettingRepository $SettingRepository)
    {
        $this->SettingRepository = $SettingRepository;

    }
    public function loyaltysettings()
    {
        $setting = $this->SettingRepository->Loyaltylist();
        return view('admin.pages.setting.loyalty.create', compact('setting'));
    }


    public function storeloyalty(Request $request)
    {
       // dd($request->all());
        $data = $request->validate([
            'points' => 'required|numeric',
            'rupees' => 'required|numeric',
            "max_points_per_order" =>"nullable",
          //  'status' => 'required|in:0,1',
        ]);

        $loyalty = $this->SettingRepository->Loyaltycreate($data);
        return redirect()->route('admin.loyaltysettings')->with('success', 'Loyalty setting created successfully.');
    }

    public function updateloyalty(Request $request, $id)
    {
        $data = $request->validate([
            'points' => 'required|numeric',
            'rupees' => 'required|numeric',
            "max_points_per_order" =>"nullable",
          //  'status' => 'required|in:0,1',
        ]);
        $loyalty = $this->SettingRepository->Loyaltyupdate($id, $data);
        return redirect()->route('admin.loyaltysettings')->with('success', 'Loyalty setting updated successfully.');
    }
    public function PaymentMethod()
    {
        $paymentMethods = $this->SettingRepository->PaymentMethodList();
        return view('admin.pages.setting.payment-method.index', compact('paymentMethods'));
    }
    public function PaymentMethodcreate()
    {
        return view('admin.pages.setting.payment-method.create');
    }
    public function PaymentMethodedit($id)
    {
        $paymentMethod = $this->SettingRepository->PaymentMethodfind($id);
        return view('admin.pages.setting.payment-method.edit', compact('paymentMethod'));
    }
    public function PaymentMethodstore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            "code" => "required|string",
            'is_enabled' => 'required|in:0,1',
        ]);
        $paymentMethod = $this->SettingRepository->PaymentMethodcreate($data);
        return redirect()->route('admin.paymentmethod')->with('success', 'Payment method created successfully.');
    }
    public function PaymentMethodupdate(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string',
            "code" => "required|string",
            'is_enabled' => 'required|in:0,1',
        ]);
       // dd($data);
        $paymentMethod = $this->SettingRepository->PaymentMethodupdate($id, $data);
        return redirect()->route('admin.paymentmethod')->with('success', 'Payment method updated successfully.');
    }
    public function PaymentMethoddelete($id)
    {
        $paymentMethod = $this->SettingRepository->PaymentMethoddelete($id);
        if ($paymentMethod) {
            return redirect()->route('admin.paymentmethod')->with('success', 'Payment method deleted successfully.');
        }
        return redirect()->route('admin.paymentmethod')->with('error', 'Payment method not found.');
    }
    public function PaymentMethodtoggleStatus(Request $request)
    {
        $id = $request->input('id');
        $status = $request->input('status');
        $paymentMethod = $this->SettingRepository->PaymentMethodtoggleStatus($id, $status);
        if ($paymentMethod) {
            return response()->json(['success' => 'Payment method status updated successfully.']);
        }
        return response()->json(['error' => 'Payment method not found.'], 404);
    }



}
