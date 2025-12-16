<?php

use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\SettingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\POSController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\AddressController;
use App\Http\Controllers\API\CategoryController;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);
Route::post('forgot-password', [RegisterController::class, 'forgotPassword'])->name("forgot-password");
Route::post('verify-otp-password', [RegisterController::class, 'verifyOtpPassword'])->name("verify-otp-password");
Route::post('reset-password', [RegisterController::class, 'resetCustomerPassword']);
Route::post("getprinter",[POSController::class,'getprinter']);

Route::get('/test-print/{order}', function ($orderId) {
try {
    $response = Http::post('http://192.168.18.177:7000/print', [
        'text' => "Order #456\n2x Biryani\nTotal: 450 PKR\nThanks!"
    ]);

    // Response check
    $data = $response->json();

    if (isset($data['success']) && $data['success'] === true) {
        echo "Print successful: " . $data['message'];
    } else {
        echo "Print failed: " . ($data['error'] ?? 'Unknown error');
    }

} catch (\Exception $e) {
    // Catch network / connection errors
    echo "Error connecting to print server: " . $e->getMessage();
}
    // Fetch order
//     $order = Order::findOrFail($orderId);

//     // Use your StationPrintService
//     $printerService = new StationPrintService();
//    $htmlFile = $printerService->printOrderItems($order);
//   // dd($htmlFile);
//        if ($htmlFile && file_exists($htmlFile)) {
//         $url = "file:///" . str_replace('\\', '/', $htmlFile);
//         $chromePath = "C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe";

//         // Launch Chrome silently in kiosk mode to print
//         $command = "\"{$chromePath}\" --kiosk-printing {$url}";
//         shell_exec($command);

//         return "✅ Auto print started for Order #{$order->id}";
//     }
   // return "✅ Print request sent for Order #{$order->id}";
});



Route::middleware('auth:customer')->group(function () {

    Route::prefix('customer')->group(function () {
        Route::get('profile', [CustomerController::class, 'profile'])->name("profile");
        Route::post('update-profile', [CustomerController::class, 'updateProfile'])->name("update-profile");
        Route::post('change-password', [CustomerController::class, 'changePassword'])->name("change-password");

        // Uncomment the following line if you want to enable address functionality
        // Route::resource('addresses', AddressController::class);
        Route::prefix('address')->group(function () {
            Route::get('get-list', [AddressController::class, 'getAll'])->name("address.list");
            Route::post('create', [AddressController::class, 'add'])->name("address.create");
            Route::get('show/{id}', [AddressController::class, 'show'])->name("address.show");
            Route::post('update', [AddressController::class, 'update'])->name("address.update");
            Route::post('delete', [AddressController::class, 'destroy'])->name("address.delete");
            Route::post('set-default', [AddressController::class, 'setDefault'])->name("address.set-default");
            Route::get('default', [AddressController::class, 'getDefaultAddress'])->name("address.get-default");
        });
        Route::get('/wishlist', [ProductController::class, 'wishlist']);
        Route::post('save-wishlist', [ProductController::class, 'togglewishlist']);
    });

    Route::prefix('categories')->group(function () {
        Route::get('list', [CategoryController::class, 'index'])->name("category.list");
        Route::get('show/{id}', [CategoryController::class, 'show'])->name("category.show");
    });

    Route::prefix('product')->group(function () {
        Route::get('show/{id}', [ProductController::class, 'show'])->name("product.show");
        Route::get('featured', [ProductController::class, 'featured'])->name("product.featured");
        Route::get('best-seller', [ProductController::class, 'bestSeller'])->name("product.best.seller");

    });
    Route::prefix('banners')->group(function () {
        Route::get('list', [ProductController::class, 'banners'])->name("bannser.list");
    });
    Route::prefix('homePageProducts')->group(function () {
        Route::post('list', [ProductController::class, 'homeproducts'])->name("home.product.list");
    });
    //cart
    Route::prefix('cart')->group(function () {
        Route::post('save', [CartController::class, 'store']);
        Route::post('directsave', [CartController::class, 'directsave']);
        Route::get('list', [CartController::class, 'list']);
        Route::post('delete/{id}', [CartController::class, 'remove']);
        Route::post('update', [CartController::class, 'update']);
        Route::post('quantity', [CartController::class, 'updateQuantity']);
        Route::post('coupon', [CartController::class, 'applycoupon']);
        Route::post('removecoupon', [CartController::class, 'removecoupon']);
        Route::get('couponlist', [CartController::class, 'couponlist']);
        Route::post('updatetax', [CartController::class, 'updatetax']);
        Route::post('updatecartNote', [CartController::class, 'updatecartNote']);
        Route::post('updateorderNote', [CartController::class, 'updateorderNote']);

    });
    Route::prefix('order')->group(function () {
        Route::post('create', [OrderController::class, 'store']);
        Route::get('myorders', [OrderController::class, 'myorders']);
        Route::post('reorder', [OrderController::class, 'reorder']);
        Route::get('detail/{id}', [OrderController::class, 'orderDetail']);

    });
    Route::prefix('branch')->group(function () {
        Route::post('getbranch', [OrderController::class, 'getBranch']);
        Route::get('getbranchlist', [OrderController::class, 'getBranchList']);
    });

    Route::prefix('settings')->group(function () {
        Route::get('cmspages', [SettingController::class, 'cmspages']);
        Route::get('cms/{id}', [SettingController::class, 'pages']);
        Route::post('contactus', [SettingController::class, 'contactus']);
        Route::get('appPopup', [SettingController::class, 'appPopup']);
        Route::get('paymentmethod', [SettingController::class, 'getpaymentmethod']);

    });


});


Route::middleware('auth:unverified_customers')->group(function () {
    Route::post('verify-otp', [RegisterController::class, 'verifyOtp'])->name("verify-otp");
    Route::post('resend-otp', [RegisterController::class, 'resendOtp'])->name("resend-otp");

    // Route::resource('products', ProductController::class);
    // Route::resource('users', App\Http\Controllers\API\UserController::class);
});
