<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller as Controller;
use App\Models\{Category, Product, ProductVariant, Ingredient, Language, Unit, Size};
use App\Models\IngredientCategory;
use App\Models\IngredientProductVariant;
use App\Repositories\Interfaces\AddonsRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\ProductVariantRepositoryInterface;

class ProductController extends Controller
{
    protected $productRepository;
    protected $productVariantRepository;

    protected $addonsRepository;
    public function __construct(ProductRepositoryInterface $productRepository, ProductVariantRepositoryInterface $productVariantRepository,AddonsRepositoryInterface $addonsRepository)
    {
        $this->productRepository = $productRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->addonsRepository = $addonsRepository;
    }
    public function index()
    {
        $products = $this->productRepository->all();
        return view('admin.pages.products.index', compact('products'));

    }

    public function create()
    {
        $categories = Category::all();

        return view('admin.pages.products.add', compact('categories'));

    }

    public function store(Request $request)
    {
        // dd($request->file('product_video'));
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cat_id' => 'required|exists:categories,id',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'desc' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $mergedArray = null;

        //$validated['is_active'] = $validated['is_active'];

        //  unset($validated['status']);
        $data1 = $request->all();
        $data1 = array_diff_key($data1, $request->file());
        //  $datavideo = saveVideo($request->file('product_video'));
        $data = saveImages($request->file());
        if (!empty($data) || !empty($data1)) {
            $mergedArray = array_merge($data, $data1);
        }
        //dd($mergedArray);
        $product = $this->productRepository->create($mergedArray);


        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit($id)
    {

        $categories = Category::all();
        $product = $this->productRepository->find($id);

        return view('admin.pages.products.edit', compact('product', 'categories'));
    }
    public function update(Request $request, Product $product)
    {
        //dd($request->all());
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cat_id' => 'required|exists:categories,id',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $product->id,
            'desc' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);
        $mergedArray = null;
        $data1 = $request->all();
        $data1 = array_diff_key($data1, $request->file());
        $data = saveImages($request->file());


        if (!empty($data) || !empty($data1)) {
            $mergedArray = array_merge($data, $data1);
        }

        $this->productRepository->update($product->id, $mergedArray);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }


    public function destroy($id)
    {

        $this->productRepository->delete($id);
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
    public function toggleFlag(Request $request)
    {

        $data = $this->productRepository->toggleFlag($request->all());
        if ($data) {
            $message = "Status Updated";
            return response()->json(['message' => $message]);
        }
    }
    public function toggleStatus(Request $request)
    {

        $data = $this->productRepository->toggleStatus($request->all());
        if ($data) {
            $message = $request->input('status') ? "Product $data->name is activated" : "Product $data->name is deactivated";
            return response()->json(['message' => $message]);
        }
    }
    public function ViewDetails($id)
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return redirect()->route('admin.products.index')->with('error', 'Product not found.');
        }

        $variants = $this->productVariantRepository->findByProductId($id);
        $addons = $this->addonsRepository->getAddonsByProductId($id);


        return view('admin.pages.products.details', compact('product', 'variants','addons'));
    }
    public function addVariant()
    {
        $sizes = Size::all();
        return view('admin.pages.products.variants.add', compact('sizes'));

    }
    public function storeVariant(Request $request)
    {
        //dd($request->all());
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'unit' => 'required|string|max:255',
            'size' => 'required|max:255',
            'sku' => 'nullable|string|max:255|unique:product_variants,sku',
            'price' => 'required|numeric|min:0',
        ]);
        $data1 = $request->all();
        $data1 = array_diff_key($data1, $request->file());
        $data = saveImages($request->file());
        if (!empty($data) || !empty($data1)) {
            $mergedArray = array_merge($data, $data1);
        }
        //  dd( $mergedArray);
        $variant = $this->productVariantRepository->create($mergedArray);
        // return redirect()->route('admin.product.variants', $validated['product_id'])
        //     ->with('success', 'Product variant created successfully.');
        return back()->with('success', 'Product variant created successfully.');
    }
    public function editVariant($productId, $variantId)
    {
        $variant = $this->productVariantRepository->find($variantId);
        if (!$variant) {
            return redirect()->route('admin.products.index')->with('error', 'Product variant not found.');
        }
        $sizes = Size::all();
        return view('admin.pages.products.variants.edit', compact('variant', 'sizes'));
    }
    public function updateVariant(Request $request)
    {
        $variantId = $request->input('variant_id');
        $variant = $this->productVariantRepository->find($variantId);

        $validated = $request->validate([
            'unit' => 'required|max:255',
            'size' => 'required|max:255',
            'sku' => 'nullable|string|max:255|unique:product_variants,sku,' . $variant->id,
            'price' => 'required|numeric|min:0',
        ]);
        $data1 = $request->all();
        $data1 = array_diff_key($data1, $request->file());
        $data = saveImages($request->file());
        if (!empty($data) || !empty($data1)) {
            $mergedArray = array_merge($data, $data1);
        }
        $this->productVariantRepository->update($variantId, $mergedArray);

        // return redirect()->route('admin.product.variants', $variant->product_id)
        //     ->with('success', 'Product variant updated successfully.');
        return back()->with('success', 'Product variant updated successfully.');
    }

    public function destroyVariant($variantId)
    {
        $variant = $this->productVariantRepository->find($variantId);
        if (!$variant) {
            return redirect()->route('admin.products.index')->with('error', 'Product variant not found.');
        }

        $this->productVariantRepository->delete($variantId);

        // return redirect()->route('admin.product.variants', $variant->product_id)
        //     ->with('success', 'Product variant deleted successfully.');
        return back()->with('success', 'Product variant deleted successfully.');
    }
    public function toggleVariantStatus(Request $request)
    {
        $data = $this->productVariantRepository->toggleStatus($request->all());
        if ($data) {
            $message = $request->input('status') ? "Product variant is activated" : "Product variant is deactivated";
            return response()->json(['message' => $message]);
        }
    }
    /*Variant Ingredients*/
    public function getVariantIngredients($variantId)
    {
        $variant = $this->productVariantRepository->find($variantId);
        if (!$variant) {
            return response()->json(['error' => 'Product variant not found'], 404);
        }

        $ingredients = $variant->ingredientCategories()->get();
        //dd( $ingredients);
        return view('admin.pages.products.variant_ingredient.index', compact('variant', 'ingredients'));
    }
    public function addVariantIngredient(Request $request)
    {
        $variantId = $request->input('variant_id');
        $variant = $this->productVariantRepository->find($variantId);
        if (!$variant) {
            return response()->json(['error' => 'Product variant not found'], 404);
        }

        $ingredients = Ingredient::where('is_active', 1)->get();
        $ingredientCategories = IngredientCategory::where('is_active', 1)->get();
        $ingCatsIds =  $ingredientCategories->pluck('id')->toArray();
        $ingProVari = IngredientProductVariant::whereIn('ing_category_id',$ingCatsIds)->where('product_variant_id',$variant->id)->get();
        $addedIngCatIds = $ingProVari->pluck('ing_category_id')->unique()->values()->toArray();

        return view('admin.pages.products.variant_ingredient.add', compact('variant', 'ingredients', 'ingredientCategories','addedIngCatIds'));
    }
    public function storeVariantIngredient(Request $request)
    {


        $validated = $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            // 'ingredient_id' => 'required|exists:ingredients,ing_id',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:50', // Or required
            'type' => 'required'
        ]);

        $variant = $this->productVariantRepository->find($validated['product_variant_id']);
        if (!$variant) {
            return response()->json(['error' => 'Product variant not found'], 404);
        }
        IngredientProductVariant::create([
            'product_variant_id' => $variant->id,
            'ingredient_id' => null, // allowed null
            'ing_category_id' => $request->ing_category_id,
            'default_ing' => $request->default_ing ?? 0,
            'quantity' => $validated['quantity'],
            'unit' => $validated['unit'],
            'type' => $validated['type'],
            'status' => $request->status,
        ]);


        // $variant->ingredients()->attach($validated['ingredient_id'], [
        //     'ing_category_id' => $request->ing_category_id,
        //     'default_ing' => $request->default_ing,
        //     'quantity' => $validated['quantity'],
        //     'unit' => $validated['unit'],
        //     'type' => $validated['type']
        // ]);

        return redirect()->route('admin.product.variants.ingredients', $validated['product_variant_id'])
            ->with('success', 'Ingredient added to variant successfully.');
    }
    public function editVariantIngredient($variantId, $ingredientId)
    {
        $variant = $this->productVariantRepository->find($variantId);
        if (!$variant) {
            return response()->json(['error' => 'Product variant not found'], 404);
        }
         $ingredient="";
        // $ingredient = $variant->ingredients()->where('ing_id', $ingredientId)->first();
        // if (!$ingredient) {
        //     return response()->json(['error' => 'Ingredient not found'], 404);
        // }
        $ingredient = $variant->ingredientCategories->where('id', $ingredientId)->first();

        $ingredientCategories = IngredientCategory::where('is_active', 1)->get();


        return view('admin.pages.products.variant_ingredient.edit', compact('variant', 'ingredient', 'ingredientCategories'));
    }
    public function updateVariantIngredient(Request $request, $variantId, $ingredientId)
    {
        $validated = $request->validate([
            'quantity' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:50', // Or required

        ]);

        $variant = $this->productVariantRepository->find($variantId);
        if (!$variant) {
            return response()->json(['error' => 'Product variant not found'], 404);
        }

        // $ingredient = $variant->ingredients()->where('ing_id', $ingredientId)->first();
        // if (!$ingredient) {
        //     return response()->json(['error' => 'Ingredient not found'], 404);
        // }

        // Update the pivot data
        // $variant->ingredients()->updateExistingPivot($ingredientId, [
        //     'ing_category_id' => $request->ing_category_id,
        //     'default_ing' => $request->default_ing,
        //     'quantity' => $validated['quantity'],
        //     'unit' => $validated['unit'],
        //     'type' => $request->type
        // ]);
      //  dd( $request->all());
        IngredientProductVariant::updateOrCreate(
            [
                'product_variant_id' => $variant->id,
                 'ing_category_id' => $request->ing_category_id, // since it's always null
            ],
            [
                'ing_category_id' => $request->ing_category_id,
                'default_ing' => $request->default_ing,
                'quantity' => $validated['quantity'],
                'unit' => $validated['unit'],
                "status" => $request->status,
                'type' => $request->type,
            ]
        );


        return redirect()->route('admin.product.variants.ingredients', $variantId)
            ->with('success', 'Ingredient updated successfully.');
    }
    public function destroyVariantIngredient($variantId, $pivotId)
    {
        $variant = $this->productVariantRepository->find($variantId);
        if (!$variant) {
            return response()->json(['error' => 'Product variant not found'], 404);
        }

        // $ingredient = $variant->ingredients()->where('ing_id', $id)->first();
        // if (!$ingredient) {
        //     return response()->json(['error' => 'Ingredient not found'], 404);
        // }


        // $variant->ingredientCategories()->detach($id);
        $variant->ingredientCategories()
            ->wherePivot('id', $pivotId)
            ->detach();

        return redirect()->route('admin.product.variants.ingredients', $variantId)
            ->with('success', 'Ingredient removed from variant successfully.');
    }
    public function toggleVariantIngredientStatus(Request $request)
    {
       // dd($request->all());
        $data = $this->productVariantRepository->toggleIngredientStatus($request->all());
        if ($data) {
            $message = $request->input('status') ? "Variant ingredient is activated" : "Variant ingredient is deactivated";
            return response()->json(['message' => $message]);
        }
    }
    public function translate($id)
    {

        $translations = $this->productRepository->getTranslation($id);
        $languages = Language::all();

        return view('admin.pages.products.translate', compact('translations', 'languages'));
    }
    public function getByCategory(Request $request)
    {
        $ingredients = Ingredient::with('unit')
            ->where('category_id', $request->category_id)
            ->get();

        return response()->json($ingredients);
    }
    //Manage Size
    public function ManageSize()
    {
        $sizes = $this->productRepository->manageSize();
        return view('admin.pages.products.size.index', compact('sizes'));
    }
    public function CreateSize()
    {
        return view('admin.pages.products.size.add');
    }
    public function SaveSize(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:sizes,name,',
            'code' => 'nullable|string|max:50', // Or required
        ]);
        return $sizes = $this->productRepository->saveSize($validatedData);
    }
    public function editSize($id)
    {
        $size = $this->productRepository->getSize($id);
        if (!$size) {
            return response()->json(['error' => 'Size not found'], 404);
        }

        return view('admin.pages.products.size.edit', compact('size'));
    }
    public function deleteSizes($id)
    {
        $size = $this->productRepository->deleteSize($id);
        return redirect()->route('admin.size.index')->with('success', 'Size deleted successfully.');
    }
    public function updateSizes($id, Request $request)
    {

        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:sizes,name,' . $id,
            'code' => 'nullable|string|max:50', // Or required
        ]);
        return $sizes = $this->productRepository->updateSize($id, $validatedData);
    }
    public function sizetranslate($id)
    {
        $translations = $this->productRepository->getSize($id);
        $languages = Language::all();
        return view('admin.pages.products.size.translate', compact('translations', 'languages'));
    }


    //Manage unit
    public function ManageUnit()
    {
        $units = $this->productRepository->manageUnit();
        return view('admin.pages.products.unit.index', compact('units'));
    }

    public function CreateUnit()
    {
        return view('admin.pages.products.unit.add');
    }

    public function SaveUnit(Request $request)
    {

        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:units,name',
            'symbol' => 'nullable|string|max:50',
        ]);

        return $this->productRepository->saveUnit($validatedData);
    }

    public function editUnit($id)
    {
        $unit = $this->productRepository->getUnit($id);

        if (!$unit) {
            return response()->json(['error' => 'Unit not found'], 404);
        }

        return view('admin.pages.products.unit.edit', compact('unit'));
    }

    public function updateUnit($id, Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:units,name,' . $id,
            'symbol' => 'nullable|string|max:50',
        ]);

        return $this->productRepository->updateUnit($id, $validatedData);
    }

    public function deleteUnit($id)
    {
        $this->productRepository->deleteUnit($id);
        return redirect()->route('admin.unit.index')->with('success', 'Unit deleted successfully.');
    }

    public function unittranslate($id)
    {
        $translations = $this->productRepository->getUnit($id);
        $languages = Language::all();
        return view('admin.pages.products.unit.translate', compact('translations', 'languages'));
    }



}
