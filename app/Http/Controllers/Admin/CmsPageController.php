<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\CmsPageRepository;
use Illuminate\Http\Request;
use App\Models\Popup;

class CmsPageController extends Controller
{
    protected $cmsPageRepository;

    public function __construct(CmsPageRepository $cmsPageRepository)
    {
        $this->cmsPageRepository = $cmsPageRepository;
    }

    public function index()
    {
        $pages = $this->cmsPageRepository->all();
        return view('admin.pages.cms.list', compact('pages'));
    }
    public function create()
    {

        return view('admin.pages.cms.add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:cms_pages,slug',
            'content' => 'required',
        ]);

        $cmsPage = $this->cmsPageRepository->create($request->all());
        return response()->json(['message' => 'CMS Page created successfully.']);

    }

    public function edit($id)
    {
        $cms = $this->cmsPageRepository->find($id);

        // if (!$pages) {
        //     return $this->sendError('CMS Page not found.');
        // }

        return view('admin.pages.cms.edit', compact('cms'));
    }

    public function update(Request $request, $id)
    {
        $cmsPage = $this->cmsPageRepository->find($id);
        //dd( $cmsPage);

        // if (!$cmsPage) {
        //     return $this->sendError('CMS Page not found.');
        // }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:cms_pages,slug,' . $id,
            'content' => 'sometimes|required',
        ]);

        $this->cmsPageRepository->update($id, $request->all());
       // return response()->json(['message' => 'CMS Page updated successfully.']);
        return redirect()->route('admin.cms.index')->with('success', 'CMS Page updated successfully.');

    }

    public function destroy($id)
    {
        $cmsPage = $this->cmsPageRepository->find($id);

        // if (!$cmsPage) {
        //     return $this->sendError('CMS Page not found.');
        // }

        $this->cmsPageRepository->delete($id);
        // return response()->json(['message' => 'CMS Page deleted successfully.']);
          return redirect()->route('admin.cms.index')->with('success', 'CMS Page deleted successfully.');

    }
    public function ToggleStatus(Request $request)
    {

        $data = $this->cmsPageRepository->toggleStatus($request->all());
        if ($data) {
            $message = $request->input('status') == "active" ? "User is activated" : "User is deactivated";
            return response()->json(['message' => $message]);
        }

    }
     public function createpopup(Request $request)
    {
        // $request->validate([
        //     'title' => 'required|string|max:255',
        //     'slug' => 'required|string|max:255|unique:cms_pages,slug',
        //     'content' => 'required',
        // ]);

        // $cmsPage = $this->cmsPageRepository->create($request->all());
        // return response()->json(['message' => 'CMS Page created successfully.']);
          $popup = Popup::first();
          return view('admin.pages.cms.popup',compact('popup'));
  }
   public function storePopup(Request $request)
    {

      $data1 = $request->all();
      $data = saveImages($request->file());
        if (!empty($data) || !empty($data1)) {
            $mergedArray = array_merge($data, $data1);
        }
        $data = $mergedArray;
        $cmsPage = $this->cmsPageRepository->storePopup($data);
        return redirect()->back()
            ->with('success', 'Popup created successfully.');




    }
}
