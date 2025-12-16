<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\ConstraintRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use App\Models\Language;

class ConstraintController extends Controller
{
    protected $constraintRepo;

    public function __construct(ConstraintRepositoryInterface $constraintRepo)
    {
        $this->constraintRepo = $constraintRepo;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $constraints = $this->constraintRepo->all();

        return view('admin.pages.constraint.list', compact('constraints'));
    }

    public function addconstraint()
    {
        return view('admin.pages.constraint.add');
    }

    public function create()
    {
        return view('admin.constraints.create');
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'status' => 'in:active,inactive'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $this->constraintRepo->create($request->all());

         return response()->json(['message' => 'Constraint created successfully.']);
    }


    public function edit($id)
    {
        $constraint = $this->constraintRepo->find($id);
        return view('admin.pages.constraint.edit', compact('constraint'));
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'status' => 'in:active,inactive'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $this->constraintRepo->update($id, $request->all());

        return response()->json(['message' =>'Constraint updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->constraintRepo->delete($id);
        return redirect()->route('admin.constraint.index')->with('success', 'Constraint deleted successfully.');
    }
    public function dropdown()
    {

        return $this->constraintRepo->dropdown();

    }
     public function translate($id)
{

    $translations = $this->constraintRepo->getTranslation($id);

    $languages = Language::all();

    return view('admin.pages.constraint.translate', compact('translations', 'languages'));
}

}
