<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller as Controller;
use App\Models\Category;
use App\Models\Deal;
use App\Models\DealItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

use App\Repositories\Interfaces\DealRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\ProductVariantRepositoryInterface;

class DealsController extends Controller
{
    protected $dealRepository;
    protected $productRepository;
    protected $productVariantRepository;

    public function __construct(DealRepositoryInterface $dealRepo, ProductRepositoryInterface $productRepo, ProductVariantRepositoryInterface $productVariantRepo)
    {
        $this->dealRepository = $dealRepo;
        $this->productRepository = $productRepo;
        $this->productVariantRepository = $productVariantRepo;
    }

    public function index()
    {
        $deals = $this->dealRepository->all();
      //  dd( $deals);
        return view('admin.pages.deals.index', compact('deals'));
    }

    public function create()
    {

        return view('admin.pages.deals.add');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'is_active' => 'required|boolean',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after_or_equal:start_time',
        ]);

        // Create the deal
        $deal = $this->dealRepository->create($validated);

        if ($deal) {
            // Handle deal items
            if ($request->has('items')) {
                foreach ($request->input('items') as $item) {
                    if (isset($item['product_id']) && isset($item['quantity'])) {
                        DealItem::create([
                            'deal_id' => $deal->id,
                            'product_id' => $item['product_id'],
                            'product_variant_id' => isset($item['variant_id']) ? $item['variant_id'] : null,
                            'quantity' => $item['quantity'],
                        ]);
                    }
                }
            }
            return redirect()->route('admin.deals.index')->with('success', __('Deal created successfully.'));
        }

        return redirect()->back()->withErrors(__('Failed to create deal.'));
    }
}
