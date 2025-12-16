<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\LanguageRepositoryInterface;
use Validator;
use Illuminate\Support\Facades\Crypt;
use App\Models\Language;

class LanguageController extends Controller
{
    protected $languageRepository;

    public function __construct(LanguageRepositoryInterface $languageRepository)
    {
        $this->languageRepository = $languageRepository;
    }

    public function index()
    {
        $languages = $this->languageRepository->all();
        return view('admin.pages.languages.list', compact('languages'));
    }

    public function add()
    {
        return view('admin.pages.languages.add');
    }
     public function edit($id)
    {
         $language = $this->languageRepository->find($id);
         return view('admin.pages.languages.edit', compact('language'));

    }

   public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:10|unique:languages,code',
        'is_default' => [
            'boolean',
            function ($attribute, $value, $fail) {
                if ($value && Language::where('is_default', 1)->exists()) {
                    $fail('There is already a default language set.');
                }
            },
        ],
    ]);
    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $this->languageRepository->create($validator->validated());

    return response()->json(['message' => 'Language created successfully.']);
}

    // public function edit($id)
    // {
    //     $language = $this->languageRepository->find($id);
    //     return view('pages.admin.languages.edit', compact('language'));
    // }

    public function update(Request $request, $id)
    {
    //    dd($request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:languages,code,' . $id,
            'is_default' => [
            'boolean',
            function ($attribute, $value, $fail) {
                if ($value && Language::where('is_default', 1)->exists()) {
                    $fail('There is already a default language set.');
                }
            },
        ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $this->languageRepository->update($id, $validator->validated());

        return response()->json(['message' => 'Language updated successfully.']);
    }

    public function destroy($id)
    {
        $id = Crypt::decrypt($id);
        $this->languageRepository->delete($id);
        return redirect()->route('admin.languages.index')->with('success', 'Language deleted successfully.');
    }
}

