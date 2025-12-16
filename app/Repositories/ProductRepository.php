<?php
namespace App\Repositories;

use App\Models\AppHomePageProduct;
use App\Models\Banner;
use App\Models\Ingredient;
use App\Models\ModelImages;
use App\Models\ModelUserActivityLog;
use App\Models\Product;
use App\Models\Size;
use App\Models\Unit;
use Illuminate\Support\Str;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Auth;

class ProductRepository implements ProductRepositoryInterface
{
    public function all()
    {
        return Product::all();
    }

    public function find($id)
    {
        return Product::findOrFail($id);
    }

    public function create(array $data)
    {
        try {
            $saveData = new Product;
            $saveData->name = $data['name'];
            $saveData->cat_id = $data['cat_id'];
            $saveData->slug = $data['slug'];
            $saveData->product_type = $data['product_type'];
            $saveData->is_active = $data['is_active'] ?? 1;
            $saveData->is_featured = isset($data['is_featured']) ? 1 : 0;
            $saveData->is_best_selling = isset($data['is_best_selling']) ? 1 : 0;

            //  $saveData->created_by =Auth::user()->id;
            if (isset($data['desc']) && !empty($data['desc'])) {
                $saveData->desc = $data['desc'];
            }

            $saveData->save();
            $id = $saveData->id;

            ModelUserActivityLog::logActivity(
                Auth::user()->id,
                "has added new product variant with name " . $saveData->name
            );

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
        } catch (\Exception $e) {
            dd($e->getMessage());
            \Log::error("Error saving product variant: " . $e->getMessage());
            return false;
        }

        //return  $product;
    }

    public function update($id, array $data)
    {


        $product = Product::find($id);
        if ($product) {
            $update = $product->update($data);
            if ($update) {
                ModelUserActivityLog::logActivity(
                    Auth::user()->id,
                    "has updated product with ID " . $id
                );
            }
            $hiddenImages = array_filter($data, function ($key) {
                return strpos($key, 'hidden_') === 0; // Only keys that start with 'hidden_'
            }, ARRAY_FILTER_USE_KEY);

            $removedImages = [];
            $addedImages = [];

            foreach ($hiddenImages as $key => $image) {

                $imageType = str_replace('hidden_', '', $key); // Extract image type (e.g., full, left, right)

                if (empty($image)) {
                    $deletedImage = $product->images()->where('image_type', $imageType)->first();

                    if ($deletedImage) {
                        $product->images()->where('image_type', $imageType)->delete();
                        $removedImages[] = ucfirst($imageType) . ' image';
                    }
                }
            }

            if (!empty($data['images']) && is_array($data['images'])) {
                foreach ($data['images'] as $key => $imageUrl) {

                  //  if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                        $product->images()->where('image_type', $key)->delete();
                        $image = new ModelImages();
                        $image->image = $imageUrl;
                        $image->image_type = $key;

                        $product->images()->save($image);
                        $addedImages[] = ucfirst($key) . ' image';
                  //  }
                }
            }
            if (!empty($removedImages)) {
                $changeMessages[] = 'Removed ' . implode(', ', $removedImages);
            }

            if (!empty($addedImages)) {
                $changeMessages[] = 'Added ' . implode(', ', $addedImages);
            }

            return $update;
        }
        return false;
    }

    public function delete($id)
    {

        $product = Product::find($id);
        if ($product) {
            $delete = $product->delete();
            if ($delete) {
                ModelUserActivityLog::logActivity(
                    Auth::user()->id,
                    "has deleted product with ID " . $id
                );
            }
            return $delete;
        }
        return false;
    }

    public function toggleStatus(array $data)
    {

        $product = Product::where("id", $data['id'])->first();
        if (!$product) {
            return false;
        }
        $newStatus = $data['status'];
        $statusText = $newStatus == 1 ? 'activated' : 'deactivated';
        $update = Product::where("id", $data['id'])->update([
            'is_active' => $newStatus,
        ]);
        if ($update) {
            ModelUserActivityLog::logActivity(
                Auth::user()->id,
                "has $statusText the product with name <b>$product->name</b>"
            );
        }
        return $product;
    }
    public function toggleFlag(array $data)
    {

        $product = Product::where("id", $data['id'])->first();
        if (!$product) {
            return false;
        }
        $newStatus = $data['status'];
        $statusText = $newStatus == 1 ? 1 : 0;
        if ($data['type'] == "is_best_selling") {
            $update = Product::where("id", $data['id'])->update([
                'is_best_selling' => $newStatus,
            ]);
            if ($update) {
                ModelUserActivityLog::logActivity(
                    Auth::user()->id,
                    "has is_best_selling $statusText the product with name <b>$product->name</b>"
                );
            }
        }
        if ($data['type'] == "is_featured") {
            $update = Product::where("id", $data['id'])->update([
                'is_featured' => $newStatus,
            ]);
            if ($update) {
                ModelUserActivityLog::logActivity(
                    Auth::user()->id,
                    "has is_featured $statusText the product with name <b>$product->name</b>"
                );
            }
        }
        if ($data['type'] == "is_new") {
            $update = Product::where("id", $data['id'])->update([
                'is_new' => $newStatus,
            ]);
            if ($update) {
                ModelUserActivityLog::logActivity(
                    Auth::user()->id,
                    "has is_new $statusText the product with name <b>$product->name</b>"
                );
            }
        }

        return $product;
    }
    public function getTranslation($id)
    {

        return Product::with('translations')->findOrFail($id);
    }
    //Size
    public function manageSize()
    {

        return Size::with('translations')->get();
    }
    public function saveSize($data)
    {
        try {
            $saveData = new Size;
            $saveData->name = $data['name'];
            $saveData->code = $data['code'] ?? "";
            $saveData->save();
            $id = $saveData->id;

            ModelUserActivityLog::logActivity(
                Auth::user()->id,
                "has added new Size name " . $saveData->name
            );
            return $saveData;
        } catch (\Exception $e) {
            \Log::error("Error saving Size: " . $e->getMessage());
            return false;
        }
    }
    public function getSize($id)
    {

        return Size::with('translations')->find($id);
    }
    public function updateSize($id, $data)
    {

        $Size = Size::find($id);
        if ($Size) {
            $update = $Size->update($data);
            if ($update) {
                ModelUserActivityLog::logActivity(
                    Auth::user()->id,
                    "has updated Size with ID " . $id
                );
            }
        }
    }
    public function deleteSize($id)
    {
        $size = Size::find($id);
        if ($size) {
            $delete = $size->delete();
            if ($delete) {
                ModelUserActivityLog::logActivity(
                    Auth::user()->id,
                    "has deleted size with ID " . $id
                );
            }
            return $delete;
        }
        return false;
    }
    //Unit
    public function manageUnit()
    {
        return Unit::with('translations')->get();
    }
    public function saveUnit($data)
    {

        try {
            $saveData = new Unit;
            $saveData->name = $data['name'];
            $saveData->symbol = $data['symbol'] ?? "";
            $saveData->save();
            $id = $saveData->id;

            ModelUserActivityLog::logActivity(
                Auth::user()->id,
                "has added new Unit name " . $saveData->name
            );
            return $saveData;
        } catch (\Exception $e) {
            \Log::error("Error saving Unit: " . $e->getMessage());
            return false;
        }
    }
    public function getUnit($id)
    {

        return Unit::with('translations')->find($id);
    }
    public function updateUnit($id, $data)
    {

        $Unit = Unit::find($id);
        if ($Unit) {
            $update = $Unit->update($data);
            if ($update) {
                ModelUserActivityLog::logActivity(
                    Auth::user()->id,
                    "has updated Unit with ID " . $id
                );
            }
        }
    }
    public function deleteUnit($id)
    {
        $Unit = Unit::find($id);
        if ($Unit) {
            $delete = $Unit->delete();
            if ($delete) {
                ModelUserActivityLog::logActivity(
                    Auth::user()->id,
                    "has deleted Unit with ID " . $id
                );
            }
            return $delete;
        }
        return false;
    }
    //Api Product
    public function findproductApi($id)
    {
        $product = Product::with([
            'category',
            'images',
            'variants.sizes',
            'addons',
            'variants.ingredientCategories.ingredients.images',
            'variants.ingredientCategories.ingredients.sizes',
        ])->find($id);


        if ($product) {
            $variants = $product->variants
                ->filter(fn($variant) => $variant->is_active == 1)
                ->sortBy(fn($variant) => $variant->sizes?->id ?? PHP_INT_MAX)
                ->map(function ($variant) use ($product) {
                    $ingredients = $variant->ingredientCategories->map(function ($cat) use ($variant) {
                         if ($cat->pivot->status == 1) {
                        $defaultIngId = $cat->pivot->default_ing;
                        return [
                            'id' => $cat->id,
                            'name' => $cat->name,
                            //  'unit' => $cat->pivot->unit ?? '',
                            // 'price' => $cat->pivot->price ?? 0,
                            'quantity' => $cat->pivot->quantity ?? '0',
                             'type' => $cat->pivot->type,
                            'deftault' => $cat->ingredients
                                ->where('ing_id', $defaultIngId)
                                ->map(fn($def) => [
                                    'ing_id' => $def->ing_id,
                                    'name' => $def->ing_name,
                                    'image' => $def->main_image,
                                    "unit" => $def->unit ? $def->unit->symbol ?? '' : '',
                                    'price' => 0,
                                    // 'price' => optional($def->sizes)
                                    //     ->where('size_id', $variant->sizes?->id)
                                    //     ->first()
                                    //     ->price ?? 0,
                                ])->first(),
                            'others' => $cat->ingredients->map(function ($ing) use ($variant, $defaultIngId) {
                                return [
                                    'ing_id' => $ing->ing_id,
                                    'name' => $ing->ing_name,
                                    "unit" => $ing->unit ? $ing->unit->symbol ?? '' : '',
                                    'image' => $ing->main_image,
                                    'price' => $ing->ing_id == $defaultIngId
                                        ? 0
                                        : optional($ing->sizes->firstWhere('size_id', $variant->sizes?->id))
                                            ->price ?? 0,
                                ];
                            })->values(),
                        ];
                    }
                    })->filter()->values();


                    $addonIngredients = $product->addons->map(function ($addon) use ($variant) {
                        $addonable = $addon->addonable;
                        if (!$addonable) {
                            return null;
                        }

                        return [
                            'id' => $addonable->id,
                            'addon_id' => $addon->id,
                            'name' => $addonable->name,
                            "quantity" => $addon->qty ?? '0',
                            "type" =>"optional",
                            'deftault' =>null,
                            'others' => $addon->items->map(function ($item) use ($variant) {
                                $ingredient = Ingredient::find($item->ingredient_id);

                            return [
                                    'ing_id' => $ingredient?->ing_id,
                                    'name' => $ingredient?->ingredient_label ?? $ingredient?->ing_name,
                                    'size_id' => $variant->sizes?->id,
                                    'unit' => $ingredient?->unit?->symbol ?? '',
                                    'image' => $ingredient?->main_image ?? '',
                                    'price' => $ingredient && $ingredient->sizes
                                        ? optional($ingredient->sizes->where('size_id', $variant->sizes?->id)->first())->price ?? 0
                                        : 0,
                                ];
                          })->filter()->values(),
                        ];
                    })->filter()->values();
                    $allIngredients = collect(array_merge($ingredients->toArray(), $addonIngredients->toArray()))->values();


                    return [
                        'variant_id' => $variant->id,
                        'type' => $variant->sizes ? $variant->sizes->name : 'No Size',
                        'symbol' => $variant->sizes ? Str::substr($variant->sizes->name, 0, 1) : '',
                        'price' => $variant->price,
                        'size_id' => $variant->sizes?->id,
                        'serving_quantity' => $variant->unit,
                        'ingredients' => $allIngredients ?? [],
                        //'addon_ingredients' => $addonIngredients ?? [],
                    ];
                })->values();

            $suggested = $this->findproductApiRecommended($product->id);
            $recommended =$this->findproductApiFeatured();
            return [
                'id' => $product->id,
                'name' => $product->name,
                'desc' => $product->desc,
               // 'video' => optional($product->images->firstWhere('image_type', 'product_video'))->image ?? '',
                 'video' => $product->main_video,
                'variants' => $variants,
                'suggested' => $suggested,
                'recommended' => $recommended,
            ];


        }


    }
    public function findproductApiRecommended($productid)
    {
     $suggested = Product::where('id', '!=', $productid)
            ->inRandomOrder()
            ->take(3)
            ->get();
               $productData = [];

        foreach ($suggested as $product) {
            // dump($product->variants);
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
        return $productData;

    }

    public function findproductApiFeatured()
    {

        $products = Product::where('is_featured', 1,)->where('is_active', 1)->get();

        $productData = [];

        foreach ($products as $product) {
            // dump($product->variants);
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
        return $productData;

    }
    public function findproductApiBestSeller()
    {

        $products = Product::where('is_best_selling', 1)->where('is_active', 1)->get();

        $productData = [];

        foreach ($products as $product) {
            // dump($product->variants);
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
        return $productData;

    }
      public function findproductApiNew()
    {

        $products = Product::where('is_new', 1)->where('is_active', 1)->get();

        $productData = [];

        foreach ($products as $product) {
            // dump($product->variants);
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
        return $productData;

    }
    public function getwishlist()
    {
        $customer = Auth::guard('customer')->user();
        $wishlist = $customer->wishlist()->with('images')->where('is_active', 1)->get();
        foreach ($wishlist as $banner) {
            $banner->image = $banner->main_image;
            unset($banner->images);
        }
        return $wishlist;
    }
    public function savewishlist($productId)
    {
        $customer = Auth::guard('customer')->user();
        $user = $customer;
        $message = "";
        if ($user->wishlist()->where('product_id', $productId)->exists()) {
            $user->wishlist()->detach($productId);
            $message = 'Product removed from wishlist.';
        } else {
            $user->wishlist()->attach($productId);
            $message = 'Product added to wishlist.';
        }
        return $message;

    }

    public function BannersList()
    {
        $banners = Banner::with([
            'category:id,name',
            'product:id,name', // Step 1: load product basic info
            'product.images',  // Step 2: nested productImages relation
            'images',          // Banner images (if you have)
        ])->where('status', 1)->get();
        foreach ($banners as $banner) {

            // Default: assign all images to banner_image
            // if ($banner->type === 'default') {
            //     $banner->banner_image = $banner->main_image;
            //     if( $banner->banner_image !=""){
            //         // foreach ( $banner->banner_image as  $getBannervalue) {

            //         //    $banner->banner_image = $getBannervalue->get('image');
            //         // }
            //     }
            // }

            // Product: assign first image from both banner and product
            // if ($banner->type === 'product') {
            //      $banner->banner_image = $banner->main_image;
            // //     $banner->banner_image = $banner->product->main_image ;
            // //   unset($banner->product->images);
            // }
            $banner->banner_image = $banner->main_image;

            // Optional: unset original images if you want clean output
            unset($banner->images);
        }
        return $banners;
    }

    public function HomeProductList()
    {
        $banners = AppHomePageProduct::with([
            'product:id,name',
            'product.images',
        ])->where('status', 1)->get();
        foreach ($banners as $banner) {
            $minPrice = $banner->product->variants()->min('price') ?? 0;
            $maxPrice = $banner->product->variants()->max('price') ?? 0;
            $banner->banner_image = $banner->images[0]->image ?? $banner->product->main_image;
            $type = $banner->images[0]->image_type ?? $banner->product->image_type;
            if ($type == "banner_video") {
                $type = "video";
            } else {
                $type = "image";
            }
            $banner->type = $type;
            $banner->price = $minPrice . " - " . $maxPrice;
            unset($banner->images);
            unset($banner->product->images);
        }
        return $banners;
    }
    //End Api
    //Banner

    public function getAllBanners()
    {
        return Banner::get();
    }

    public function saveBanner($data)
    {
        $banner = new Banner();
        $banner->banner_title = $data['banner_title'] ?? null;
        $banner->banner_description = $data['banner_description'] ?? null;
        $banner->type = $data['type'] ?? 'default';
        if ($data['type'] === 'category') {
            $banner->category_id = $data['category_id'] ?? null;
        } else if ($data['type'] === 'product') {
            $banner->product_id = $data['product_id'] ?? null;
        }
        // $banner->full = $data['full'] ?? null; // image path ya url
        // $banner->banner_video = $data['banner_video'] ?? null; // video path or url

        $banner->save();

        if (!empty($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $key => $imageUrl) {
                $image = new ModelImages();
                $image->image = $imageUrl;
                $image->image_type = $key;
                $imageSaved = $banner->images()->save($image);

                // if (!$imageSaved) {
                //     cd('Error saving image: ' . $image->image_type);
                // }
            }
        }

        // Activity log create karwana
        ModelUserActivityLog::logActivity(
            Auth::id(), // current logged in user id
            "has added new banner titled '" . $banner->banner_title . "' with type '" . $banner->type . "'"
        );

        return $banner; // ya return jo chahiye
    }

    public function getbanner($id)
    {
        return $banner = Banner::find($id);
    }
    public function deleteBanner($id)
    {
        return Banner::where('id', $id)->delete();
    }
    public function updateBanner($id, array $data)
    {
        $banner = Banner::find($id);
        if ($banner) {
            $banner->banner_title = $data['banner_title'] ?? $banner->banner_title;
            $banner->banner_description = $data['banner_description'] ?? $banner->banner_description;
            $banner->type = $data['type'];
            if ($data['type'] === 'category') {
                $banner->category_id = $data['category_id'] ?? null;
                $banner->product_id = null;
            } else if ($data['type'] === 'product') {
                $banner->product_id = $data['product_id'] ?? null;
                $banner->category_id = null;
            }
            if ($data['type'] === 'default') {
                $banner->product_id = null;
                $banner->category_id = null;
            }
            $update = $banner->save();
            if ($update) {
                ModelUserActivityLog::logActivity(
                    Auth::user()->id,
                    "has updated Banner with ID " . $id
                );
            }
            $hiddenImages = array_filter($data, function ($key) {
                return strpos($key, 'hidden_') === 0; // Only keys that start with 'hidden_'
            }, ARRAY_FILTER_USE_KEY);

            $removedImages = [];
            $addedImages = [];

            foreach ($hiddenImages as $key => $image) {

                $imageType = str_replace('hidden_', '', $key); // Extract image type (e.g., full, left, right)

                if (empty($image)) {
                    $deletedImage = $banner->images()->where('image_type', $imageType)->first();

                    if ($deletedImage) {
                        $banner->images()->where('image_type', $imageType)->delete();
                        $removedImages[] = ucfirst($imageType) . ' image';
                    }
                }
            }

            if (!empty($data['images']) && is_array($data['images'])) {
                foreach ($data['images'] as $key => $imageUrl) {
                   // if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                        $banner->images()->where('image_type', $key)->delete();
                        $image = new ModelImages();
                        $image->image = $imageUrl;
                        $image->image_type = $key;

                        $banner->images()->save($image);
                        $addedImages[] = ucfirst($key) . ' image';
                    //}
                }
            }
            if (!empty($removedImages)) {
                $changeMessages[] = 'Removed ' . implode(', ', $removedImages);
            }

            if (!empty($addedImages)) {
                $changeMessages[] = 'Added ' . implode(', ', $addedImages);
            }

            return $banner;
        }
        //End Banners
    }
    public function bannerTooglestatus(array $data)
    {

        $banner = Banner::where("id", $data['id'])->first();
        if (!$banner) {
            return false;
        }
        $newStatus = $data['status'];
        $statusText = $newStatus == 1 ? 'activated' : 'deactivated';
        $update = Banner::where("id", $data['id'])->update([
            'status' => $newStatus,
        ]);
        if ($update) {
            ModelUserActivityLog::logActivity(
                Auth::user()->id,
                "has $statusText the Banner with name <b>$banner->name</b>"
            );
        }
        return $banner;
    }

    public function getAllHomeProduct()
    {
        return AppHomePageProduct::with('product')->where('status', 1)->get();
    }
    public function saveHomeProduct($data)
    {
        $product = AppHomePageProduct::create([
            'product_id' => $data['product_id'],
        ]);
        if (!empty($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $key => $imageUrl) {
                $image = new ModelImages();
                $image->image = $imageUrl;
                $image->image_type = $key;
                $imageSaved = $product->images()->save($image);

                // if (!$imageSaved) {
                //     cd('Error saving image: ' . $image->image_type);
                // }
            }
        }

        ModelUserActivityLog::logActivity(
            Auth::id(),
            'added product ID ' . $product->product_id . ' to home page products'
        );
        return $product->save();
    }
    public function updateHomeProduct($data)
    {
        // Try to find existing product by product_id
        $product = AppHomePageProduct::where('product_id', $data['product_id'])->first();

        // If not found, create new
        if (!$product) {
            $product = new AppHomePageProduct();
            $action = 'added';
        } else {
            $action = 'updated';
        }

        // Assign or update product fields
        $product->product_id = $data['product_id'];
        $product->save();

        $hiddenImages = array_filter($data, function ($key) {
            return strpos($key, 'hidden_') === 0; // Only keys that start with 'hidden_'
        }, ARRAY_FILTER_USE_KEY);

        $removedImages = [];
        $addedImages = [];

        foreach ($hiddenImages as $key => $image) {

            $imageType = str_replace('hidden_', '', $key); // Extract image type (e.g., full, left, right)

            if (empty($image)) {
                $deletedImage = $product->images()->where('image_type', $imageType)->first();

                if ($deletedImage) {
                    $product->images()->where('image_type', $imageType)->delete();
                    $removedImages[] = ucfirst($imageType) . ' image';
                }
            }
        }

        if (!empty($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $key => $imageUrl) {
               // if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                    $product->images()->where('image_type', $key)->delete();
                    $image = new ModelImages();
                    $image->image = $imageUrl;
                    $image->image_type = $key;

                    $product->images()->save($image);
                    $addedImages[] = ucfirst($key) . ' image';
               // }
            }
        }
        if (!empty($removedImages)) {
            $changeMessages[] = 'Removed ' . implode(', ', $removedImages);
        }

        if (!empty($addedImages)) {
            $changeMessages[] = 'Added ' . implode(', ', $addedImages);
        }
        // ModelUserActivityLog::logActivity(
        //     user_id: Auth::id(),
        //      user_activity: $changeMessages
        // );

        // Log activity
        ModelUserActivityLog::logActivity(
            Auth::id(),
            "{$action} product ID {$product->product_id} on home page"
        );

        return $product;
    }

    public function getHomeBanner($id)
    {
        return $product = AppHomePageProduct::find($id);
    }
    public function deleteHomeProduct($id)
    {
        return AppHomePageProduct::where('id', $id)->delete();
    }
}
