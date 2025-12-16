<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Repositories\CmsPageRepository;
use App\Repositories\SettingRepository;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use App\Helpers\MessageHelper;

/**
 * Class AddressController
 * @package App\Http\Controllers\API
 */

class SettingController extends BaseController
{


    protected $cmsPageRepository;

    protected $settingRepository;

    protected $language;

    public function __construct(Request $request, CmsPageRepository $cmsPageRepository, SettingRepository $settingRepository)
    {
        $this->cmsPageRepository = $cmsPageRepository;
        $this->settingRepository = $settingRepository;
        $this->language = $request->input('language', 'EN');
    }
    public function cmspages(Request $request)
    {
        $pages = $this->cmsPageRepository->all();
        return $this->sendResponse($pages, 'Pages retrive successfully.');

    }
    public function pages($slug)
    {
        $pages = $this->cmsPageRepository->findwithslug($slug);
        return $this->sendResponse($pages, 'Pages retrive successfully.');

    }
    public function contactus(Request $request)
    {
         $language = $this->language;
      try {
            $data = $request->validate([
                'subject' => 'required',
                'email' => 'required|email',
                'description' => 'required'
            ]);
        $pages = $this->cmsPageRepository->savecontact( $data );

        return $this->sendResponse($pages, 'Contact submitted successfully.');
          } catch (\Throwable $e) {
           // dd( $e);
            $errors = MessageHelper::formatErrors($e);
            $fields = ['validation.error'];
            $successMessages = MessageHelper::defaultSuccessMessages($language, $fields);
            $flatMessages = MessageHelper::formatMessages($successMessages);
            return $this->sendError($flatMessages[0] ?? "Errors", $errors, 200);
        }

    }
     public function appPopup(Request $request)
    {
        $pages = $this->cmsPageRepository->getPopup();
        return $this->sendResponse($pages, 'Popup retrive successfully.');
    }
    public function getpaymentmethod(Request $request)
    {
        $setting = $this->settingRepository->PaymentMethodActiveList();
        return $this->sendResponse($setting, 'Payment method retrive successfully.');
    }



}
