<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\IngredientRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use App\Models\{Language, Unit, Size, Ingredient};
use App\Models\IngredientCategory;
use Illuminate\Support\Facades\Auth;

class IngredientController extends Controller
{
    protected $ingredientRepository;

    public function __construct(IngredientRepositoryInterface $ingredientRepository)
    {
        $this->ingredientRepository = $ingredientRepository;

    }

    public function index()
    {
        $ingredient = $this->ingredientRepository->all();
        return view('admin.pages.ingredient.list', compact('ingredient'));
    }
    public function add()
    {
        $units = Unit::all();
        $ingredientCategory = IngredientCategory::all();
        $sizes = Size::all();
        return view('admin.pages.ingredient.add', compact('units','ingredientCategory','sizes'))->render();
    }
    public function edit($id)
    {

        $ingredient = $this->ingredientRepository->find($id);
        $units = Unit::all();
        $ingredientCategory = IngredientCategory::all();
        $sizes = Size::all();
        return view('admin.pages.ingredient.edit', compact('ingredient', 'units','ingredientCategory','sizes'))->render();
    }


    public function store(Request $request)
    {
        // dd($request->file());
        $data = $request->validate(
            [

                'ing_name' => 'required',
                'ing_unit' => 'required',
                'min_quantity' => 'required',
                'ing_type' => 'required',
                'full' => 'nullable|mimes:jpeg,png,webp',
                'unit_price' => 'nullable|numeric|min:0',
                'sizes' => 'nullable|array',
                'sizes.*.size' => 'nullable|exists:sizes,id',
                'sizes.*.price' => 'nullable|numeric|min:0',
                'is_quantify' => 'required|boolean',
                "ingredient_label" => 'required|string|max:255',
            ],
            [
                'ing_name.required' => 'Ingredient name is Required',
                "ing_unit.required" => 'Ingredient Unit is Required',
                'full.mimes' => 'The image must be a file of type: jpeg, png.'
            ]
        );


        $mergedArray = null;
        $data1 = $request->all();
        $data1 = array_diff_key($data1, $request->file());
        $data = saveImages($request->file());
        if (!empty($data) || !empty($data1)) {
            $mergedArray = array_merge($data, $data1);
        }
        $result = $this->ingredientRepository->create($mergedArray);
        if ($result) {
            return response()->json(['message' => 'ingredient created successfully.']);
        } else {
            return response()->json(['error' => 'Error saving ingredient.']);
        }

    }


    public function update(Request $request, $id)
    {
        $data = $request->validate(
            [

                'ing_name' => 'required',
                'ing_unit' => 'required',
                'min_quantity' => 'required',
                'ing_type' => 'required',
                'full' => 'nullable|mimes:jpeg,png,webp',
                'unit_price' => 'nullable|numeric|min:0',
                'sizes' => 'nullable|array',
                'sizes.*.size' => 'nullable|exists:sizes,id',
                'sizes.*.price' => 'nullable|numeric|min:0',
                'is_quantify' => 'required|boolean',
                "ingredient_label" => 'required|string|max:255',
            ],
            [
                'ing_name.required' => 'Ingredient name is Required',
                "ing_unit.required" => 'Ingredient Unit is Required',
                'full.mimes' => 'The image must be a file of type: jpeg, png.'
            ]
        );
        $mergedArray = null;
        $data1 = $request->all();
        $data1 = array_diff_key($data1, $request->file());
        $data = saveImages($request->file());
        if (!empty($data) || !empty($data1)) {
            $mergedArray = array_merge($data, $data1);
        }
        $mergedArray['ing_id'] = $id;

        $this->ingredientRepository->update($id, $mergedArray);
        return response()->json(['message' => 'ingredient updated successfully.']);
    }

    public function destroy($id)
    {
        $this->ingredientRepository->delete($id);
        return redirect()->back()->with('success', 'ingredient deleted successfully.');
    }

    public function toggleStatus(Request $request)
    {
        $data = $this->ingredientRepository->toggleStatus($request->all());
        if ($data) {
            $message = $request->input('status') ? "Ingredient $data->ing_name is activated" : "Ingredient $data->ing_name is deactivated";
            return response()->json(['message' => $message]);
        }
    }
    public function translate($id)
    {

        $translations = $this->ingredientRepository->getTranslation($id);

        $languages = Language::all(); // Optional: for multi-language dropdown

        return view('admin.pages.ingredient.translate', compact('translations', 'languages'));
    }
    public function attachStandardIngredients(Request $request)
{
    $request->validate([
        'ingredients' => 'required|array',
    ]);
    $customIngId = $request->input('custom_ing_id');
    $customIngredient = Ingredient::where('ing_type', 'custom')
                                  ->findOrFail($customIngId);
                               //   dd($customIngredient);
    // $customIngredient->standardIngredients()->delete();

    $customIngredient->standardIngredients()->sync($request->ingredients);
    return response()->json(['message' => 'ingredients linked successfully!']);
}

    public function categories()
    {
        $categories = $this->ingredientRepository->allCategories();
        return view('admin.pages.ingredient.categories.list', compact('categories'));

    }
    public function ViewIngrediants($id)
    {
         $category = $this->ingredientRepository->findCategory($id);
         $ingredients = $this->ingredientRepository->all();

        return view('admin.pages.ingredient.categories.category-ingrediants', compact('category','ingredients'));
    }
    public function cretecategories()
    {

        return view('admin.pages.ingredient.categories.add');

    }
    public function editcategories($id)
    {
        $category = $this->ingredientRepository->findCategory($id);
        return view('admin.pages.ingredient.categories.edit', compact('category'));

    }
    public function storeCategories(Request $request)
    {

        $data = $request->validate([
            'name' => 'required|string',
            'desc' => 'nullable|string',
            'parent_type' => 'nullable',
            //   'type' => 'required',
            'parent_id' => 'nullable|integer',
            'status' => 'required|string',

        ]);
        $data['parent_id'] = ($data['parent_id'] != "") ? $data['parent_id'] : 0;
        $mergedArray = null;
        $data1 = $data;
        $data1 = array_diff_key($data1, $request->file());
        $data = saveImages($request->file());
        if (!empty($data) || !empty($data1)) {
            $mergedArray = array_merge($data, $data1);
        }
        $this->ingredientRepository->createCategory($mergedArray);
        return response()->json(['message' => 'Category created successfully.']);

    }
    public function updateCategories(Request $request, $id)
    {
        //dd($request->all());
        $data = $request->validate([
            'name' => 'required|string',
            'desc' => 'nullable|string',
            'parent_type' => 'nullable',
            //  'type' => 'required',
            'parent_id' => 'nullable|integer',
            'status' => 'required|string',

        ]);
        $data['parent_id'] = ($data['parent_id'] != "") ? $data['parent_id'] : 0;
        $mergedArray = null;
        $data1 = $data;
        $data1 = array_diff_key($data1, $request->file());
        $data = saveImages($request->file());
        if (!empty($data) || !empty($data1)) {
            $mergedArray = array_merge($data, $data1);
        }
        $this->ingredientRepository->updateCategory($id, $mergedArray);
        return response()->json(['message' => 'Category updated successfully.']);
    }
    public function deleteCategory($id)
    {
        $this->ingredientRepository->deleteCategory($id);
        return redirect()->route('admin.ingredient.categories')->with('success', 'Category deleted successfully.');
    }
    public function toggleStatusCategory(Request $request)
    {
        $data = $this->ingredientRepository->toggleCategoryStatus($request->all());
        if ($data) {
            $message = $request->input('status') ? "Category $data->name is activated" : "Category $data->name is deactivated";
            return response()->json(['message' => $message]);
        }
    }

     public function updateQuantity($id,$branchid, Request $request)
    {

        $data = $request->validate([
            'quantity' => 'required|numeric|min:0',
        ], [
            'quantity.required' => 'Quantity is required.',
            'quantity.numeric' => 'Quantity must be a number.',
            'quantity.min' => 'Quantity must be at least 0.',
        ]);

        $ingredient = $this->ingredientRepository->BranchIngredientsQuantity($id, $branchid, $data['quantity']);

        if ($ingredient) {
            return response()->json(['success' =>'Ingredient quantity updated successfully.']);
        } else {
            return response()->json(['errors' => 'Failed to update ingredient quantity.']);
        }

    //     return view('admin.badmin.ingredient.add', compact('ingredient','BranchId'));
    }

     public function addCustom($id,Request $request)
    {

        $BranchId = Auth::user()->branches[0]->id ?? null;
        $ingredient = $this->ingredientRepository->find($id);
       if (!$ingredient) {
            return response()->json(['errors' => 'Failed to update ingredient quantity.']);
        } else {
         $this->ingredientRepository->updatecustom($id,$request->all());
            return response()->json(['success' =>'Ingredient quantity updated successfully.']);


        }

    }
    public function view($id,$bid)
    {
        $id = trim($id);
        $BranchId = $bid ?? null;
        $ingredient = $this->ingredientRepository->find($id);
       // dd( $ingredient);

        return view('admin.badmin.ingredient.add', compact('ingredient','BranchId'));
    }

}
