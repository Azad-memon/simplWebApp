<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\POSController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\AuthController;

use App\Services\StationPrintService;
use App\Models\Order;




/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use Illuminate\Support\Facades\Artisan;

Route::get('clear-cache', function () {
    Artisan::call('view:clear');
   Artisan::call('config:cache');
   Artisan::call('route:cache');
   Artisan::call('cache:clear');
   Artisan::call('route:clear');
   Artisan::call('config:clear');

   return "Cache cleared successfully";
})->name('clear');


Route::get('/', function(){
    return redirect('admin');

});
Route::get('/printertest/{id}',[POSController::class,'testprinter']);
Route::get("getprinter/{data}",[POSController::class,'getprinter']);
Route::get('/printer/test', [POSController::class, 'testPrint'])->name('printer.test');




Route::get('/auto-print', function () {
    $url = "http://localhost/print-test"; // Local page URL
    $chromePath = "C:\Program Files\Google\Chrome\Application\chrome.exe";

    // Open Chrome silently in kiosk printing mode
    exec("\"{$chromePath}\" --kiosk-printing {$url}");

    return "Auto print triggered!";
});


Route::get('/test-print/{order}', function ($orderId) {
return view('admin.pages.test-print',compact('orderId'));
});



Route::get('testNotifaction', [AdminController::class, 'testNotifaction']);
Route::get('admin/', [AdminController::class, 'login'])
    ->middleware('throttle:admin_login')
    ->name('login.admin');
Route::get('admin/login', [AdminController::class, 'login'])->name('login');
Route::post('/login_post', [AdminController::class, 'login_post'])->name('login_post');
 Route::get('/logout', [AdminController::class, 'logout'])->name('logout');
 Route::get("privacy-policy",function(){
    return view('admin.pages.privacy-policy');
 })->name('privacy.policy');

// Route::group(['middleware' => ['auth']], function () {
//     //users
//     // Route::get('/admin/users', [AdminController::class, 'users']);
//     // Route::get('admin/user/approve/{id}', [AdminController::class, 'approve'])->name('admin.user.approve');
//     // // posts Route
//     // Route::get('/admin/post/add', [AdminController::class, 'post_add']);
//     // Route::post('/admin/post/add_post', [AdminController::class, 'post_add_post']);
//     // Route::get('/admin/post', [AdminController::class, 'post']);
//     // Route::get('/admin/post/edit/{id}', [AdminController::class, 'post_edit']);
//     // Route::post('/admin/post/edit_post', [AdminController::class, 'post_edit_post']);
//     // Route::get('/admin/post/delete/{id}', [AdminController::class, 'post_delete']);
//     // // crs Route
//     // Route::get('/admin/cr/add', [AdminController::class, 'cr_add']);
//     // Route::post('/admin/cr/add_post', [AdminController::class, 'cr_add_post']);
//     // Route::get('/admin/cr', [AdminController::class, 'cr']);
//     // Route::get('/admin/cr/edit/{id}', [AdminController::class, 'cr_edit']);
//     // Route::post('/admin/cr/edit_post', [AdminController::class, 'cr_edit_post']);
//     // Route::get('/admin/cr/delete/{id}', [AdminController::class, 'cr_delete']);
//     // // fbs Route
//     // Route::get('/admin/fb/add', [AdminController::class, 'fb_add']);
//     // Route::post('/admin/fb/add_post', [AdminController::class, 'fb_add_post']);
//     // Route::get('/admin/fb', [AdminController::class, 'fb']);
//     // Route::get('/admin/fb/edit/{id}', [AdminController::class, 'fb_edit']);
//     // Route::post('/admin/fb/edit_post', [AdminController::class, 'fb_edit_post']);
//     // Route::get('/admin/fb/delete/{id}', [AdminController::class, 'fb_delete']);
//     // // ads Route
//     // Route::get('/admin/ad/add', [AdminController::class, 'ad_add']);
//     // Route::post('/admin/ad/add_post', [AdminController::class, 'ad_add_post']);
//     // Route::get('/admin/ad', [AdminController::class, 'ad']);
//     // Route::get('/admin/ad/edit/{id}', [AdminController::class, 'ad_edit']);
//     // Route::post('/admin/ad/edit_post', [AdminController::class, 'ad_edit_post']);
//     // Route::get('/admin/ad/delete/{id}', [AdminController::class, 'ad_delete']);
//     // // // categorys Route
//     // Route::get('/admin/category/add', [AdminController::class, 'category_add']);
//     // Route::post('/admin/category/add_post', [AdminController::class, 'category_add_post']);
//     // Route::get('/admin/category', [AdminController::class, 'category']);
//     // Route::get('/admin/category/edit/{id}', [AdminController::class, 'category_edit']);
//     // Route::post('/admin/category/edit_post', [AdminController::class, 'category_edit_post']);
//     // Route::get('/admin/category/delete/{id}', [AdminController::class, 'category_delete']);
//     //  // profiles Route


//     // Route::get('/admin/products', [ProductController::class, 'index'])->name('admin.products.index');
//     // Route::get('/admin/product/add', [ProductController::class, 'create'])->name('admin.products.create');
//     // Route::post('/admin/product/store', [ProductController::class, 'store'])->name('admin.products.store');
//     // Route::get('/admin/product/edit/{product}', [ProductController::class, 'edit'])->name('admin.products.edit');
//     // Route::post('/admin/product/update/{product}', [ProductController::class, 'update'])->name('admin.products.update');
//     // Route::get('/admin/product/delete/{product}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
// });
Route::post('/admin/profile/edit_post', [AdminController::class, 'profile_edit_post']);
Route::get('/admin/profile/edit/{id}', [AdminController::class, 'profile_edit']);
Route::get('/verify-email/{token}', [AdminController::class, 'verify_email_submit']);

