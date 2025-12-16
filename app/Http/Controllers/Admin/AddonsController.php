<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AddonIngredient;
use App\Models\IngredientCategory;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Ingredient;
use App\Repositories\Interfaces\AddonsRepositoryInterface;
use Illuminate\Http\Request;

class AddonsController extends Controller
{
   protected $addonsRepository;


    public function __construct(AddonsRepositoryInterface $addonsRepository)
    {
        $this->addonsRepository = $addonsRepository;
    }
    public function index($id)
    {



        // Fetch addons for the specific product
        $product = Product::find($id);
        if (!$product) {
            // Handle the case where the product does not exist
            return redirect()->route('admin.products.index')->withErrors(['errors' => 'Product not found.']);
        }
         $addons = $this->addonsRepository->getAddonsByProductId($id);

        return view('admin.pages.addons.list', compact('addons','product'));
    }

    public function add(Request $request)
    {
        $products = $request->productid;
        $ingredients = Ingredient::all();
        $categories = IngredientCategory::where('is_active',1)->get();
        return view('admin.pages.addons.add', compact('products', 'ingredients','categories'))->render();

    }
    public function edit($id)
    {
        $addons = $this->addonsRepository->find($id);
        $products = Product::all();
        $ingredients = Ingredient::all();
        $categories = IngredientCategory::where('is_active',1)->get();
        return view('admin.pages.addons.edit', compact('addons', 'products', 'ingredients','categories'))->render();
    }
    public function store(Request $request)
    {


        $data = $request->validate([
            'price' => 'required|numeric',
            'product_id' => 'required|exists:products,id',
            'addonable_id' => 'required',
            'addonable_type' => 'required',
            'qty' => 'required|numeric',
            'desc' => 'nullable|string|max:255',
            'ingredient_ids' => 'nullable|array',
            'ingredient_ids.*' => 'exists:ingredients,ing_id',
            //'is_replace' => 'nullable|boolean',
        ]);
         $data['is_replace'] = $request->has('is_replace') ? 1 : 0;

        $addon = $this->addonsRepository->create($data);

    return redirect()->route('admin.addons.index', ['id' => $addon->product_id])->with('success', 'Addon created successfully.');

    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'price' => 'required|numeric',
            'addonable_id' => 'required',
            'addonable_type' => 'required',
            'qty' => 'required|numeric',
            'desc' => 'nullable|string|max:255',
            'ingredient_ids' => 'nullable|array',
            'is_replace' => 'nullable',
            // 'ingredient_ids.*' => 'exists:ingredients,ing_id',
        ]);
        $data['is_replace'] = $request->has('is_replace') ? 1 : 0;
        $addon = $this->addonsRepository->update($id, $data);

        if ($addon) {
            return redirect()->back()->with('success', 'Addon updated successfully.');
        } else {
            return redirect()->back()->withErrors(['error' => 'Failed to update addon.']);
        }
    }
    public function destroy($id)
    {
        //dd($id);
        $addon = $this->addonsRepository->delete($id);
        if ($addon) {
             return redirect()->back()->with('success', 'Addon deleted successfully.');
        } else {
            return redirect()->back()->withErrors(['error' => 'Failed to delete addon.']);
        }
    }
    public function getIngredientsByCategory($categoryId)
    {
        $ingredients = Ingredient::where('category_id', $categoryId)->where('is_active', 1)->get();

        return response()->json($ingredients);
    }

    public function addProduct(Request $request)
    {

        $products = Product::all();
         $productId = $request->input('productid');
        // $selectedProduct = Product::find($productId);

        // if (!$selectedProduct) {
        //     return redirect()->back()->withErrors(['error' => 'Product not found.']);
        // }

        return view('admin.pages.addons.add_product', compact('products','productId'))->render();
    }
  public function getprodcutvariants($id)
    {


        // Get the main product and its variants for dropdown
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => 'Product not found.'], 404);
        }
        // Assuming you have a relationship 'variants' in Product model
         $variants = $product->variants()->get();

        // // Prepare data for dropdown (id => name)
        $dropdown = [];
      //  $dropdown[] = ['id' => $product->id, 'name' => $product->name . ' (Main Product)'];
        foreach ($variants as $variant) {
            $dropdown[] = ['id' => $variant->id, 'name' => $variant->sizes->name , 'price' => $variant->price];
        }
        return response()->json($dropdown);
        // // $productId = $request->input('product_id');
        // // $selectedProduct = Product::find($productId);

        // // if (!$selectedProduct) {
        // //     return redirect()->back()->withErrors(['error' => 'Product not found.']);
        // // }

        // return view('admin.pages.addons.add_product', compact('products'))->render();
    }

  public function editProductAddon($id)
    {
          $addon = $this->addonsRepository->find($id);
          if (!$addon) {
          return response()->jso(['error' => 'Addon not found.']);
          }
         $product = ProductVariant::find($addon->addonable_id);
        if (!$product) {
            return response()->json(['error' => 'Product not found.'], 404);
        }
         return view('admin.pages.addons.edit_product', compact('product','addon'))->render();

    }



}
