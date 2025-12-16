<?php

namespace App\Repositories;

use App\Models\ModelImages;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;
use App\Models\Language;
use App\Models\ModelUserActivityLog;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Auth;


class CategoryRepository implements CategoryRepositoryInterface
{
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }
    public function all()
    {
        return Category::with('children', 'parent')
            // ->where('parent_id', 0)
            ->OrderBy('id', 'ASC')
            ->get();
    }
    public function getProductCategories($catId = null, $active = null, $is_parent = null)
    {
        $query = Category::with('children', 'parent');
        if ($catId !== null) {
            $query->where("id", $catId);
        }
        if ($active !== null) {
            $query->where("status", $active);
        }
        if ($is_parent === true) {
            $query->where("parent_id", 0);
        }
        return $query->orderBy('id', 'desc')->get()->toArray();
    }


    //APi
    public function allactive($id, $language = 'en')
    {
        if ($id) {
            $categories = Category::with([
                'images',
                'products' => function ($query) {
                    $query->where('is_active', 1);
                }
            ])->where('status', 'active')
                ->where('parent_id', $id)
                ->orderBy('series', 'ASC')
                ->get();
        } else {
            $categories = Category::with([
                'images',
                'products' => function ($query) {
                    $query->where('is_active', 1);
                }
            ])->where('status', 'active')
                ->where('parent_id', "!=", 0)
                ->orderBy('series', 'ASC')
                ->get();
        }
        //  dd( $categories[0]->products);
        //translate language


        $language_id = Language::where('code', $language)->first();
        $categoriesData = [];

        $categoriesData = [];

        foreach ($categories as $category) {
            $titleTranslation = $category->translations
                ->where('language_id', $language_id->id)
                ->where('meta_key', 'title')
                ->first();

            $descTranslation = $category->translations
                ->where('language_id', $language_id->id)
                ->where('meta_key', 'description')
                ->first();

            if ($titleTranslation) {
                $category->name = $titleTranslation->meta_value;
            }

            if ($descTranslation) {
                $category->desc = $descTranslation->meta_value;
            }

            $category->has_child = $category->children->isNotEmpty();
            $category->unsetRelation('children');
            $category->unsetRelation('translations');
            $categoryVideo = null;
            $categoryVideo = $category->main_video;


            // if ($category->images && $category->images->isNotEmpty()) {
            //     $imageMap = [];
            //     foreach ($category->images as $image) {
            //         // /dump($image);
            //        // $imageMap[$image->image_type] = $image->image;
            //         $categoryVideo = $image->image_type==="category_video" ? $image->image : null;
            //        // dump( $categoryVideo);
            //     }
            //     $category->images = $imageMap;
            // } else {
            //     unset($category->images);
            // }

            // Products of this category
            $products = $category->products;

            $productData = [];
            foreach ($products as $product) {
                $minPrice = $product->variants()->min('price') ?? 0;
                $maxPrice = $product->variants()->max('price') ?? 0;

                $productData[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'min_price' => $minPrice,
                    'max_price' => $maxPrice,
                    'image' => $product->main_image,
                    'image_type' => $product->images->isNotEmpty() ? $product->images->first()->image_type : null,
                    'desc' => $product->desc,
                ];
            }

            // Now handle based on category name
            if ($category->name == "For you") {
                $banners = $this->productRepository->BannersList();
                $recomended = $this->productRepository->findproductApiFeatured();
                // $video = $category->images->isNotEmpty() ? $category->images->first()->category_video : null;

                $categoriesData[$category->name] = [
                    'video' => $categoryVideo ?? null,
                    "desc" => $category->desc,
                    'banners' => $banners,
                    'recomended' => $recomended ?? null,
                    'products' => $productData ?? null,
                ];
            } elseif ($category->name == "New") {
                $categoriesData[$category->name] = [
                    'video' => $categoryVideo ?? null,
                    "desc" => $category->desc,
                    'products' => $this->productRepository->findproductApiNew() ?? null,
                ];
            } else {
                $categoriesData[$category->name] = [
                    'video' => $categoryVideo ?? null,
                    "desc" => $category->desc,
                    'products' => $productData,
                ];

            }
        }



        return $categoriesData;

    }

    public function find($id)
    {
        return Category::findOrFail($id);
    }


    public function create(array $data)
    {

        $saveData = new Category;
        $saveData->name = $data['name'];
        $saveData->desc = $data['desc'];
        $saveData->series = $data['series'];
        $saveData->parent_id = $data['parent_id'];
        $saveData->status = $data['status'];
        if ($saveData->save()) {
            ModelUserActivityLog::logActivity(
                Auth::user()->id,
                "has added new category with name " . $saveData->name
            );
        }
        if (!empty($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $key => $imageUrl) {
                $image = new ModelImages();
                $image->image = $imageUrl;
                $image->image_type = $key;
                $imageSaved = $saveData->images()->save($image);

                // if (!$imageSaved) {
                //     cd('Error saving image: ' . $image->image_type);
                // }
            }
        }
        return $saveData;

    }

    public function update($id, array $data)
    {
        if (empty($data)) {
            return null;
        }

        $category = Category::find($id); // Assumes 'id' is category ID
        if (!$category) {
            return null;
        }

        $changeMessages = [];
        $hasChanges = false;

        // Compare and update fields
        if (
            $category->name !== $data['name'] ||
            $category->desc !== $data['desc'] ||
            $category->parent_id != $data['parent_id'] ||
            $category->status !== $data['status']
            || $category->series != $data['series']
        ) {
            $oldName = $category->name;

            $category->update([
                'name' => $data['name'],
                'desc' => $data['desc'],
                'parent_id' => $data['parent_id'],
                "series" => $data['series'],
                'status' => $data['status'],
            ]);

            if ($oldName !== $data['name']) {
                $changeMessages[] = "Changed category name from '{$oldName}' to '{$data['name']}'";
            } else {
                $changeMessages[] = "Updated category details";
            }

            $hasChanges = true;
        }

        // Handle removed images from hidden_* keys
        $hiddenImages = array_filter($data, function ($key) {
            return strpos($key, 'hidden_') === 0;
        }, ARRAY_FILTER_USE_KEY);

        $removedImages = [];
        $addedImages = [];

        foreach ($hiddenImages as $key => $image) {
            $imageType = str_replace('hidden_', '', $key);

            if (empty($image)) {
                $deletedImage = $category->images()->where('image_type', $imageType)->first();
                if ($deletedImage) {
                    $category->images()->where('image_type', $imageType)->delete();
                    $removedImages[] = ucfirst($imageType) . ' image';
                }
            }
        }

        // Handle added/updated images
        if (!empty($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $key => $imageUrl) {

                // if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                // dd($imageUrl);
                $category->images()->where('image_type', $key)->delete(); // Replace existing
                $image = new ModelImages();
                $image->image = $imageUrl;
                $image->image_type = $key;

                $category->images()->save($image);
                $addedImages[] = ucfirst($key) . ' image';
                //}
            }
        }

        // Collect messages
        if (!empty($removedImages)) {
            $changeMessages[] = 'Removed ' . implode(', ', $removedImages);
        }

        if (!empty($addedImages)) {
            $changeMessages[] = 'Added ' . implode(', ', $addedImages);
        }

        // Log activity if anything changed
        if ($hasChanges || !empty($removedImages) || !empty($addedImages)) {
            $activityMessage = "Updated category: {$category->name}. " . implode(', ', $changeMessages);

            ModelUserActivityLog::logActivity(
                Auth::user()->id,
                $activityMessage
            );

            return true;
        }

        return false;
    }

    public function delete($id)
    {
        $category = Category::with('children')->find($id);
        if ($category) {
            foreach ($category->children as $child) {
                $this->delete($child->id);
            }
            return $category->delete();
        }
        return false;
    }
    public function dropdowndata()
    {
        return response()->json(
            Category::select('id', 'name')->get()
        );
    }
    public function getTranslation($id)
    {

        return Category::with('translations')->findOrFail($id);
    }
}
