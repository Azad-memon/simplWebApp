<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LanguageTranslation;
use App\Repositories\Interfaces\LanguageTranslationRepositoryInterface;
use App\Models\Language;


class LanguageTranslationController extends Controller
{
    protected $translationRepo;

    public function __construct(LanguageTranslationRepositoryInterface $translationRepo)
    {
        $this->translationRepo = $translationRepo;
    }

    public function index()
    {
        $translations = $this->translationRepo->all();
        $translations=  $translations->where('translatable_type',"App\Models\Constraint");
         $languages = Language::all(); // Optional: for multi-language dropdown


         return view('admin.pages.language_translation.list', compact('translations','languages'));
    }
    public function addtranslation(Request $request)
    {
         $type = $request->query('type');
         $translatable_id = $request->query('translatable_id');
         return view('admin.pages.language_translation.add',compact('type' ,'translatable_id'))->render();
    }
      public function edittranslation(Request $request,$id)
    {

         $translation = $this->translationRepo->find($id);
         $type = $request->query('type');

         return view('admin.pages.language_translation.edit',compact('translation','type'))->render();
    }
    public function store(Request $request)
    {


        $data = $request->validate([
            'language_id' => 'required|exists:languages,id',
            'translatable_type' => 'required|string',
            'translatable_id' => 'nullable|integer',
            'meta_key' => 'required|string',
            'meta_value' => 'required|string',
        ]);

        $translation = $this->translationRepo->create($data);
        return response()->json(['message', 'created successfully.']);



    }

    public function show($id)
    {
        $translation = $this->translationRepo->find($id);
        return response()->json($translation);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'language_id' => 'sometimes|exists:languages,id',
            'translatable_type' => 'sometimes|string',
            'translatable_id' => 'nullable|integer',
            'meta_key' => 'sometimes|string',
            'meta_value' => 'sometimes|string',
        ]);

        $translation = $this->translationRepo->update($id, $data);
         return response()->json(['message', 'update successfully.']);
    }

    public function destroy($id)
    {

        $this->translationRepo->delete($id);
        return redirect()->back()->with('success', 'deleted successfully.');
    }

public function updateAll(Request $request)
{

    //dd($request->all());
$keys = ['title', 'description'];
foreach ($keys as $key) {
    if ($request->filled($key)) {
    // dump($request->$key);
        LanguageTranslation::updateOrCreate(
            [
                'language_id'       => $request->language_id,
                'translatable_id'   => $request->translatable_id,
                'translatable_type' => $request->translatable_type,
                'meta_key'          => $key,
            ],
            [
                'meta_value'        => $request->$key,
            ]
        );
    }
}

}


}
