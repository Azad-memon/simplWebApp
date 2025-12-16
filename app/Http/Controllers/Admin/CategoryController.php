<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Models\Language;

class CategoryController extends Controller
{
    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function index()
    {
        $flatCategories = $this->categoryRepository->all()->toArray();;
        $nestedCategories = $this->buildCategoryTree($flatCategories);
        return view('admin.pages.category.list', compact('nestedCategories'));
    }
  public function buildCategoryTree($categories, $parentId = 0)
{
      $tree = [];

    foreach ($categories as $category) {
       if ($category['parent_id'] == $parentId) {
            $children = $this->buildCategoryTree($categories, $category['id']);

            $tree[] = [
                'parent_id'=> $category['parent_id'],
                'id' => $category['id'],
                'name' => $category['name'],
                'status' => $category['status'],
                'children' => $children
            ];
        }
    }

    return $tree;
}
    public function addcategory()
    {

        return view('admin.pages.category.add')->render();
    }
    public function editcategori($id)
    {

        $categories = $this->categoryRepository->find($id);

        return view('admin.pages.category.edit', compact('categories'))->render();
    }

    public function create()
    {
        return view('admin.pages.category.add');
    }

    public function store(Request $request)
    {

        $data = $request->validate([
            'name' => 'required|string',
            'desc' => 'nullable|string',
            'parent_type' => 'nullable',
         //   'type' => 'required',
            'parent_id' => 'nullable|integer',
            'status' => 'required|string',
            'series' => 'nullable|integer',

        ]);
        $data['parent_id']= ($data['parent_id']!="") ? $data['parent_id']: 0;
            $mergedArray = null;
            $data1 =$data;
            $data1 = array_diff_key($data1, $request->file());
            $data = saveImages($request->file());
            if (!empty($data) || !empty($data1)) {
                $mergedArray = array_merge($data, $data1);
            }
        $this->categoryRepository->create($mergedArray);
        return response()->json(['message' => 'Category created successfully.']);

    }

    public function show($id)
    {
        $category = $this->categoryRepository->find($id);
        return view('admin.pages.category.show', compact('category'));
    }

    public function edit($id)
    {
        $category = $this->categoryRepository->find($id);
        return view('admin.pages.category.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'desc' => 'nullable|string',
            'parent_type' => 'nullable',
          //  'type' => 'required',
            'parent_id' => 'nullable|integer',
            'status' => 'required|string',
            'series' => 'nullable|integer',

        ]);
        $data['parent_id']= ($data['parent_id']!="") ? $data['parent_id']: 0;
          $mergedArray = null;
            $data1 =  $data;
            $data1 = array_diff_key($data1, $request->file());
            $data = saveImages($request->file());
            if (!empty($data) || !empty($data1)) {
                $mergedArray = array_merge($data, $data1);
            }
        $this->categoryRepository->update($id, $mergedArray);
        return response()->json(['message' => 'Category updated successfully.']);
    }

    public function destroy($id)
    {
        $this->categoryRepository->delete($id);
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');

    }
    public function dropdowndata()
    {
        $categories = $this->categoryRepository->dropdowndata();

        return $categories;

    }
    public function translate($id)
    {

        $translations = $this->categoryRepository->getTranslation($id);

        $languages = Language::all(); // Optional: for multi-language dropdown

        return view('admin.pages.category.translate', compact('translations', 'languages'));
    }

     public function ViewDetails($id)
    {

        $category = $this->categoryRepository->find($id);
        $products = $category->products;

        return view('admin.pages.category.details', compact('category','products'));
    }

}
