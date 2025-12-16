<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Repositories\Interfaces\CategoryRepositoryInterface;

class CategoryController extends BaseController
{
   protected $categoryRepository;
    protected $language;

    public function __construct(Request $request,CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->language = $request->input('language', 'EN') ?? 'EN';
    }
    /**
     * Get all categories.
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function index(Request $request)
    {
        $language = $this->language;
         $id = $request->id ?? null;;
        $categories = $this->categoryRepository->allactive($id,$language);
        if ($categories=="") {
            return $this->sendError('No categories found.', [], 404);
        }
        return $this->sendResponse($categories, 'Categories retrieved successfully.');
    }
}
