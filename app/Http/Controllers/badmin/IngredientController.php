<?php
namespace App\Http\Controllers\badmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\IngredientRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use App\Models\{Language,Unit,Size};
use Auth;

class IngredientController extends Controller
{
   protected $ingredientRepository;

    public function __construct(IngredientRepositoryInterface $ingredientRepository)
    {
        $this->ingredientRepository = $ingredientRepository;

    }

    public function index(Request $request)
    {
        $BranchId = Auth::user()->branches[0]->id ?? null;
        $ingredient = $this->ingredientRepository->getBranchIngredients($BranchId);

        return view('admin.badmin.ingredient.list', compact('ingredient','BranchId'));
    }
      public function view($id)
    {
        // $BranchId = Auth::user()->branches[0]->id ?? null;
        $BranchId = auth()->user()->branchstaff()->first()?->branch_id;
        $ingredient = $this->ingredientRepository->find($id);

        return view('admin.badmin.ingredient.add', compact('ingredient','BranchId'));
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

}
