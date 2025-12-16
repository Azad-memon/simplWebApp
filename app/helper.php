<?php
use App\Models\Branch;
use App\Models\BranchStaff;
use App\Models\User;
use Illuminate\Support\Collection;
use App\Models\Ingredient;
use App\Models\ShiftCashNote;
use Illuminate\Support\Facades\Auth;

use App\Models\Coupon;
if (!function_exists('saveImages')) {
    function saveImages($data, $path = "images")
    {
        $finalResult = null;
        $result = [];
        if (!empty($data) && is_array($data)) {
           foreach ($data as $key => $eImage) {
            if ($key != "full") {
                $path = "videos";
            }
            $upload_filename = "image_{$key}" . Auth::user()->id . time() . "-ws." . $eImage->getClientOriginalExtension();
            $eImage->storeAs("public/uploads/{$path}/", $upload_filename);
            // Only return the relative path, not the full URL
            $result[$key] = "uploads/{$path}/" . $upload_filename;
            }
        }
       // dd( $result);

        $finalResult['images'] = $result;
        return $finalResult;
    }

}
if (!function_exists('saveImagesApp')) {
    function saveImagesApp($data, $path = "images")
    {

        $finalResult = null;
        $result = [];
        $eImage = $data;

        if (is_string($eImage) && preg_match('/^data:image\/(\w+);base64,/', $eImage, $type)) {
            $imageData = substr($eImage, strpos($eImage, ',') + 1);
            $imageData = base64_decode($imageData);
            $extension = $type[1];
            $upload_filename = "image_" . Auth::user()->id . time() . "-ws." . $extension;
            $upload_path = storage_path("app/public/uploads/{$path}/" . $upload_filename);
            // Ensure directory exists
            if (!file_exists(dirname($upload_path))) {
            mkdir(dirname($upload_path), 0777, true);
            }
            file_put_contents($upload_path, $imageData);
            // Only return the relative path, not the full URL
            $result = "uploads/{$path}/" . $upload_filename;
        }

        $finalResult['images'] = $result;
        return $finalResult;
    }
}
function getImageByType($images, $type)
{
    $image = collect($images)->firstWhere('image_type', $type);
        return $image ? asset('storage/' . $image['image']) : ''; // If found, return the image URL, otherwise return an empty string
}
if (!function_exists('getIngredientDetails')) {
    /**
     * Get ingredient details (for removed or addons)
     *
     * @param array|string|null $items
     * @param bool $isAddon
     * @return \Illuminate\Support\Collection
     */
    function getIngredientDetails($items, bool $isAddon = false, $sizeId = null,$quantity = 1,$ispos = false): Collection
    {
        $items = is_array($items) ? $items : json_decode($items, true);
        $items = array_filter((array) $items);

        if (empty($items)) {
            return collect();
        }

        // collect all ing_ids
        $ingIds = array_column($items, column_key: 'ing_id');
        $ingredients = Ingredient::whereIn('ing_id', $ingIds)
            ->with('images', 'sizes')
            ->get();

        return collect($items)->map(function ($item) use ($ingredients, $isAddon, $sizeId,$quantity,$ispos) {
            $ingredient = $ingredients->firstWhere('ing_id', $item['ing_id']);

            if (!$ingredient) {
                return null;
            }

         //  $quantity = $item['quantity'] ?? 1;
          //  $sizeId = $sizeId ?? $item['size_id'] ?? null;

            // Get price according to size_id (if available)
            $price = optional(
                $ingredient->sizes->firstWhere('size_id', $sizeId)
            )->price ?? 0;

            $data = [
                'id' => $ingredient->ing_id,
                'name' => $ispos ? $ingredient->ingredient_label : $ingredient->ing_name,
                "label_name" => $ingredient->ingredient_label,
                'image' => $ingredient->main_image,
                'size_id' => $sizeId,
                'quantity' =>$item['quantity'],
                'price' => $price,
            ];
           // dump( $data);

            if ($isAddon) {
                $data['total'] = $price * $quantity;
            }

            return $data;
        })->filter();
    }
    if (!function_exists('applyCoupon')) {
        function applyCoupon(float $totalSubtotal, ?string $couponCode, $cart): array
        {
            $discount = 0;
            $discountMessage = "";

            $coupon = $couponCode
                ? Coupon::where('code', $couponCode)->first()
                : Coupon::where('id', $cart->coupon_id)->first();

            if ($coupon && $coupon->isValid()) {
                if ($totalSubtotal >= ($coupon->min_amount ?? 0)) {
                    // Percentage or Fixed
                    $discount = $coupon->type === 'percentage'
                        ? ($totalSubtotal * $coupon->discount) / 100
                        : $coupon->discount;

                    // Max discount cap
                    if ($coupon->max_amount) {
                        $discount = min($discount, $coupon->max_amount);
                    }

                    $discountMessage = "Get {$coupon->discount}" .
                    ($coupon->type === "percentage" ? "%" : " Rs") . " off!";

                    $cart->update(['coupon_id' => $coupon->id]);
                } else {
                    $discountMessage = "Coupon requires a minimum order of Rs.{$coupon->min_amount}";
                }
            }

            return [
                'discount' => $discount,
                'message' => $discountMessage,
                'coupon' => $coupon,
            ];
        }
    }
}

if (!function_exists('cd')) {
    function cd(...$data)
    {
        echo "<pre >";

        foreach ($data as $item) {
            if (is_array($item) || is_object($item)) {
                try {
                    print_r(json_decode(json_encode($item), true));
                } catch (\Throwable $e) {
                    var_dump($item);
                }
            } else {
                echo $item . PHP_EOL;
            }
        }

        echo "</pre>";
        die;
    }
}
if (!function_exists('getShiftStartTime')) {
    function getShiftStartTime()
    {
        $staffId = Auth::id();

        if (!$staffId) return null;


        $activeShift = ShiftCashNote::where('user_id', $staffId)
            ->where('entry_type', 'opening')
            ->whereDate('created_at', now()->toDateString())
            ->latest()
            ->first();

        return $activeShift ? $activeShift->created_at : null;
    }
}
if (!function_exists('getBranchId')) {
    function getBranchId()
    {

        $staffId = Auth::id();
        if ($staffId) {
            $branchId = BranchStaff::where('user_id', $staffId)
                ->first()?->branch_id;
          $mainbranch=  Auth::user()->branches[0]->id ?? null;
    //    User::where('id', $staffId)->


            if ($branchId ) {
                return $branchId;
            }elseif($mainbranch!==null){
                return $mainbranch;
            }
        }


        if (Request::route() && Request::route('id')) {
            return Request::route('id');
        }

        if (Request::route() && Request::route('branch_id')) {
            return Request::route('branch_id');
        }

        // 3️⃣ If branch_id exists in GET query (?branch_id=1)
        if (Request::has('branch_id') && !empty(Request::get('branch_id'))) {
            return Request::get('branch_id');
        }

        // 4️⃣ If stored in session
        if (session()->has('branch_id')) {
            return session('branch_id');
        }

        // 5️⃣ Default fallback
        return null;
    }
    if (!function_exists('getbranchidByaddress')) {
        function getbranchidByaddress($defaultAddress)
        {
           if ($defaultAddress) {
            $userLat = $defaultAddress->latitude;
            $userLng = $defaultAddress->longitude;

            // Haversine Formula to find nearest branch
            $nearestBranch = Branch::select(
                '*',
                DB::raw("6371 * acos(
                    cos(radians(?))
                    * cos(radians(lat))
                    * cos(radians(`long`) - radians(?))
                    + sin(radians(?))
                    * sin(radians(lat))
                ) AS distance")
            )
                ->addBinding([$userLat, $userLng, $userLat], 'select')
                ->whereNotNull('lat')
                ->whereNotNull('long')
                ->where('lat', '!=', 0)
                ->where('long', '!=', 0)
                ->having('distance', '<=', 5) //
                ->orderBy('distance', 'ASC')
                ->first();
            if ($nearestBranch) {
               return  $nearestBranch;
            }
        }
        }

    }
    if(!function_exists('getTaxData')){
    function getTaxData($total = null)
{
    $data = [
        'card' => 8,
        'cash' => 10,
    ];

    // Total na milay → simple list return
    if ($total === null) {
        return $data;
    }

    $final = [];

    foreach ($data as $type => $percentage) {
        $final[$type] = [
            'type'       => $type,                    // Added type
            'percentage' => $percentage,
            'tax_amount' => ($total * $percentage) / 100,
            'total'      => $total + ($total * $percentage) / 100,
        ];
    }

    return $final;
}

    }

}

