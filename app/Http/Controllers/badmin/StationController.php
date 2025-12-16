<?php

namespace App\Http\Controllers\badmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User,Station};
use App\Repositories\Interfaces\{BranchRepositoryInterface, CategoryRepositoryInterface};
use Auth;
use Illuminate\Validation\Rule;

class StationController extends Controller
{
    protected $BranchRepository, $categoryRepository;

    public function __construct(BranchRepositoryInterface $BranchRepository, CategoryRepositoryInterface $categoryRepository)
    {
        $this->BranchRepository = $BranchRepository;
        $this->categoryRepository = $categoryRepository;

    }


    public function index()
    {
        $branchId = Auth::user()->branches[0]->id;
        $data = $this->BranchRepository->getBranchStationData($branchId);
        return view('admin.badmin.stations.list', compact('data', 'branchId'));
    }

    public function create()
    {
        $productCategories = $this->categoryRepository->getProductCategories(null, 'active');
        return view('admin.badmin.stations.ajax.create', compact('productCategories'));
    }

    public function store(Request $request)
    {
        $branchId = Auth::user()->branches[0]->id;
        $request->validate([
            's_name' => [
                'required',
                'string',
                'max:191',
                Rule::unique('stations', 's_name')->where(fn($q) => $q->where('branch_id', $branchId)),
            ],
            'categories' => ['required', 'array', 'min:1', 'distinct'],
            'categories.*' => [
                'integer',
                Rule::exists('categories', 'id'),
                Rule::unique('station_category', 'category_id')
                    ->where(function ($q) use ($branchId) {
                        $q->whereIn('station_id', function ($sub) use ($branchId) {
                            $sub->select('id')
                                ->from('stations')
                                ->where('branch_id', $branchId);
                        });
                    }),
            ],
            'ip' => ['required'],
        ], [
            'categories.*.unique' => 'This category is already assigned to another station in this branch.',
            'categories.distinct' => 'You cannot select the same category more than once.',
        ]);
        $data = $request->all();
        $data['branch_id'] = $branchId;
        $station = $this->BranchRepository->createStation($data);
        if ($request->filled('categories')) {
            $station->categories()->sync($request->input('categories'));
        }

        return redirect()->back()->with('success', 'Station created successfully!');
    }

    public function delete($id)
    {

        $station = $this->BranchRepository->findStation($id);

        if (!$station) {
            return redirect()->back()->with('error', 'Station not found.');
        }
        $station->delete();
        return back()->with('success', 'Station deleted successfully.');
    }

    public function edit($id)
    {
        $productCategories = $this->categoryRepository->getProductCategories(null, 'active');
        $branchId = Auth::user()->branches[0]->id;
        $data = $this->BranchRepository->getBranchStationData($branchId, $id);
        return view('admin.badmin.stations.ajax.edit', compact('productCategories', 'data'));
    }

    public function update(Request $request, $id)
    {
        $branchId = Auth::user()->branches[0]->id;
        $station = Station::findOrFail($id);

        $request->validate([
            's_name' => [
                'required',
                'string',
                'max:191',
                Rule::unique('stations', 's_name')
                    ->where(fn($q) => $q->where('branch_id', $branchId))
                    ->ignore($station->id),
            ],
            'categories' => ['required', 'array', 'min:1', 'distinct'],
            'categories.*' => [
                'integer',
                Rule::exists('categories', 'id'),
                Rule::unique('station_category', 'category_id')
                    ->where(function ($q) use ($branchId, $station) {
                        $q->whereIn('station_id', function ($sub) use ($branchId) {
                            $sub->select('id')
                                ->from('stations')
                                ->where('branch_id', $branchId);
                        })
                            ->where('station_id', '<>', $station->id);
                    }),
            ],
            'ip' => ['required'],
        ], [
            'categories.*.unique' => 'This category is already assigned to another station in this branch.',
            'categories.distinct' => 'You cannot select the same category more than once.',
        ]);

        $data = $request->all();
        $data['branch_id'] = $branchId;

        $this->BranchRepository->updateStation($station, $data);

        return redirect()->back()->with('success', 'Station updated successfully!');
    }



}
