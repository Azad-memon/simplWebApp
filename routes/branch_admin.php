<?php

use App\Http\Controllers\badmin\DashboardController;
use App\Http\Controllers\badmin\IngredientController;
use App\Http\Controllers\badmin\OrderController;
use App\Http\Controllers\badmin\ProductController;
use App\Http\Controllers\badmin\StaffController;
use App\Http\Controllers\badmin\StaffShiftController;
use App\Http\Controllers\badmin\StationController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\PrinterController;
use Illuminate\Support\Facades\Route;

Route::get('/kds/{id}', [KitchenController::class, 'kdsView'])->name('staff.kds');
Route::post('/mark-ready', [KitchenController::class, 'markReady'])->name('staff.mark-ready');
Route::get('/kds-refresh', [KitchenController::class, 'refreshKds'])->name('staff.kds-refresh');
Route::get('/dispach-view/{id}', [KitchenController::class, 'dispachView'])->name('staff.dispach-view');
Route::get('/dispach-refresh', [KitchenController::class, 'refreshDispatch'])->name('staff.dispach-refresh');

// Route::get('/print-receipt/{order_id}', [PrinterController::class, 'printReceipt']);
// Route::get('/print-kitchen-receipt', [POSController::class, 'printKitchenReceipt']);
Route::get('/settings', [DashboardController::class, 'settings'])->name('badmin.settings');
Route::post('/settings/update', [DashboardController::class, 'updateSettings'])->name('badmin.settings.update');

Route::group(['middleware' => ['auth', 'role:branchadmin']], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('badmin.dashboard');
    // ingredients Quantity management
    Route::get('ingredients', [IngredientController::class, 'index'])->name('badmin.ingredient.index');
    Route::get('ingredients/view/{id}', [IngredientController::class, 'view'])->name('badmin.ingredient.view');
    Route::post('ingredients/update-quantity/{id}/{branchid}', [IngredientController::class, 'updateQuantity'])->name('badmin.ingredient.updateQuantity');
    Route::post('ingredients/add/custom/{id}', [IngredientController::class, 'addCustom'])->name('badmin.ingredient.custom.update');

    // Branch staff management routes
    Route::get('staff', [StaffController::class, 'index'])->name('badmin.staff.index');
    Route::get('staff/create', [StaffController::class, 'create'])->name('badmin.staff.create');
    Route::post('staff/store', [StaffController::class, 'store'])->name('badmin.staff.store');
    Route::get('staff/edit/{id}', [StaffController::class, 'edit'])->name('badmin.staff.edit');
    Route::post('staff/update/{id}', [StaffController::class, 'update'])->name('badmin.staff.update');
    Route::get('staff/delete/{id}', [StaffController::class, 'destroy'])->name('badmin.staff.delete');
    Route::post('staff/toggle-status', [StaffController::class, 'toggleStatus'])->name('badmin.toggleStatus');

    // Staff shifts management routes
    Route::get('/shifts', [StaffShiftController::class, 'index'])->name('badmin.shifts.index');
    Route::get('shifts/create', [StaffShiftController::class, 'create'])->name('badmin.shifts.create');
    Route::post('/shifts', [StaffShiftController::class, 'store'])->name('badmin.shifts.store');
    Route::get('/shifts/edit/{id}', [StaffShiftController::class, 'edit'])->name('badmin.shifts.edit');
    Route::post('/shifts/{shift}', [StaffShiftController::class, 'update'])->name('badmin.shifts.update');
    Route::get('/shifts/{id}', [StaffShiftController::class, 'destroy'])->name('badmin.shifts.destroy');

    // Product management routes
    Route::get('/products', [ProductController::class, 'index'])->name('badmin.products.index');
    Route::get('product/{id}', [ProductController::class, 'view'])->name('badmin.products.view');
    // Add more product management routes as needed
    Route::prefix('orders')->name('badmin.order.')->group(function () {
        Route::get('live', [OrderController::class, 'liveorders'])->name('liveorders');
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{id}', [OrderController::class, 'show'])->name('show');
        Route::post('updateStatus/{id}', [OrderController::class, 'updateStatus'])->name('updateStatus');
    });

    // Product management routes
    Route::get('/products', [ProductController::class, 'index'])->name('badmin.products.index');
    Route::get('product/{id}', [ProductController::class, 'view'])->name('badmin.products.view');
    // Add more product management routes as needed
    Route::prefix('orders')->name('badmin.order.')->group(function () {
        Route::get('live', [OrderController::class, 'liveorders'])->name('liveorders');
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{id}', [OrderController::class, 'show'])->name('show');
        Route::post('updateStatus/{id}', [OrderController::class, 'updateStatus'])->name('updateStatus');

    });

    Route::prefix('stations')->name('badmin.station.')->group(function () {
        Route::get('/', [StationController::class, 'index'])->name('list');
        Route::get('/create', [StationController::class, 'create'])->name('create');
        Route::post('/create', [StationController::class, 'store'])->name('store');
        Route::post('/delete/{id}', [StationController::class, 'delete'])->name('delete');
        Route::get('/edit/{id}', [StationController::class, 'edit'])->name('edit');
        Route::put('/edit/{id}', [StationController::class, 'update'])->name('update');
    });

});


Route::get('employee/login', [StaffController::class, 'showLoginForm'])->name('staff.login');
Route::post('employee/login', [StaffController::class, 'login'])->name('staff.login.post');
Route::get('pos/ingredients/view/{id}', [IngredientController::class, 'view'])->name('badmin.ingredient.view');
Route::post('pos/ingredients/update-quantity/{id}/{branchid}', [IngredientController::class, 'updateQuantity'])->name('badmin.ingredient.updateQuantity');
Route::post('pos/ingredients/add/custom/{id}', [IngredientController::class, 'addCustom'])->name('badmin.ingredient.custom.update');



Route::group(['middleware' => ['auth', 'role:waiter,accountant,dispatcher']], function () {
    Route::middleware(['auth', 'check.shift'])->group(function () {
        Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
        Route::get('/pos/category/{id}', [POSController::class, 'getProductsByCategory'])->name('pos.category');
        Route::get('/variants/options', [POSController::class, 'loadVariantOptions'])->name('variants.options');
        Route::post('/cart/add', [POSController::class, 'addcart'])->name('pos.cart.add');
        Route::post('/cart/remove', [POSController::class, 'removecart'])->name('pos.cart.remove');
        Route::post('/cart/update', [POSController::class, 'updatecart'])->name('pos.cart.update');
        Route::get('/pos/cart', [POSController::class, 'getCart'])->name('pos.cart.index');
        Route::get('/pos/cart/recipt', [POSController::class, 'getCartRecipt'])->name('pos.cart.recipt');
        Route::post('/pos/payment/complete', [POSController::class, 'completePayment'])->name('pos.payment.complete');
        Route::post('/pos/cart/updatequantity', [POSController::class, 'updatequantity'])->name('pos.cart.updatequantity');
        Route::post('/pos/cart/edit', [POSController::class, 'editcart'])->name('pos.cart.edit');
        Route::post('/pos/cart/updateNote', [POSController::class, 'updateNote'])->name('pos.cart.updateNote');
        Route::post('/pos/cart/updateOrderNote', [POSController::class, 'updateOrderNote'])->name('pos.cart.updateOrderNote');
        Route::get('/staff/cash-count', [POSController::class, 'cashCount'])->name('staff.cash-count');
        Route::get('/staff/stock-count', [POSController::class, 'stockCount'])->name('staff.stock-count');
         Route::post('/cart/updateTax', [POSController::class, 'updateTax'])->name('pos.cart.updateTax');

        // order routes
        Route::get('/pos/orders', [POSController::class, 'orders'])->name('pos.orders');
        // routes/web.php
        Route::post('/orders/{order}/status', [POSController::class, 'updateStatus'])->name('pos.orders.updateStatus');
        Route::get('/pos/orders/list', [POSController::class, 'orderlist'])->name('pos.orders.table');
        Route::get('/pos/orders/counter', [POSController::class, 'orderscounter'])->name('pos.orders.counter');
        Route::get('/pos/orders/{id}', [POSController::class, 'vieworder'])->name('pos.orders.view');
        Route::get('/pos/inventories', [POSController::class, 'inventories'])->name('pos.inventories');

        Route::get('/pos/receipt/print/{id}', [POSController::class, 'printReceiptOrder'])->name('staff.receipt.print');
        Route::get('/pos/receipt/print/local/{id}', [POSController::class, 'printReceiptOrderLocal'])->name('staff.receipt.print.local');
        Route::get('/pos/kitchen/print/local/{id}', [POSController::class, 'printKitchenOrderLocal'])->name('staff.kitchen.print.local');



        Route::get('/pos/kitchen/print/{id}', [POSController::class, 'printKitchenOrder'])->name('staff.kitchen.print');
        Route::get('/pos/sticker/print/{id}', [POSController::class, 'printStickerOrder'])->name('staff.sticker.print');
        // web.php or api.php
        Route::get('/customers/search', [POSController::class, 'searchcustomer'])->name('pos.customers.search');
        // Apply Coupon
        Route::post('/pos/apply-coupon', [POSController::class, 'applyCoupon'])->name('apply.coupon');
            // Remove Coupon
        Route::post('/pos/remove-coupon', [POSController::class, 'removeCoupon'])->name('pos.remove.coupon');

        Route::post('/pos/close-queue', [POSController::class, 'closeBranchQueue'])->name('pos.close.branch');
 });
    Route::get('/pos/logout', [POSController::class, 'logoutpos'])->name('pos.logout');
    Route::get('pos/shift-inventory', [POSController::class, 'shiftInventory'])->name('shift.inventory');
    Route::post('pos/shift-inventory/store', [POSController::class, 'storeShiftInventory'])->name('shift.inventory.store');

    // mange pos inventory
    Route::get('pos/ingredients', [IngredientController::class, 'index']);



    // mange pos cashout
    Route::get('pos/cashout', [POSController::class, 'CashoutRefund'])->name('pos.cashout');
    Route::get('/pso/cashout-refund', [POSController::class, 'CashoutRefund'])->name('pos.cashout.refund');
    Route::post('/pos/cashout-refund/store', [POSController::class, 'storeCashoutRefund'])->name('pos.cashout.store');
    Route::get('/pso/order/amount', [POSController::class, 'getOrderAmount'])->name('pos.order.amount');

});



// Route::group(['middleware' => ['auth', 'role:waiter']], function () {
//   //  Route::middleware(['auth', 'check.shift'])->group(function () {
//         Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
//         Route::get('/pos/category/{id}', [POSController::class, 'getProductsByCategory'])->name('pos.category');
//         Route::get('/variants/options', [POSController::class, 'loadVariantOptions'])->name('variants.options');
//         Route::post('/cart/add', [POSController::class, 'addcart'])->name('pos.cart.add');
//         Route::post('/cart/remove', [POSController::class, 'removecart'])->name('pos.cart.remove');
//         Route::post('/cart/update', [POSController::class, 'updatecart'])->name('pos.cart.update');
//         Route::get('/pos/cart', [POSController::class, 'getCart'])->name('pos.cart.index');
//         Route::get('/pos/cart/recipt', [POSController::class, 'getCartRecipt'])->name('pos.cart.recipt');
//         Route::post('/pos/payment/complete', [POSController::class, 'completePayment'])->name('pos.payment.complete');
//         Route::post('/pos/cart/updatequantity', [POSController::class, 'updatequantity'])->name('pos.cart.updatequantity');
//         Route::post('/pos/cart/edit', [POSController::class, 'editcart'])->name('pos.cart.edit');
//         Route::post('/pos/cart/updateNote', [POSController::class, 'updateNote'])->name('pos.cart.updateNote');
//         Route::post('/pos/cart/updateOrderNote', [POSController::class, 'updateOrderNote'])->name('pos.cart.updateOrderNote');
//         Route::get('/staff/cash-count', [POSController::class, 'cashCount'])->name('staff.cash-count');
//         Route::get('/staff/stock-count', [POSController::class, 'stockCount'])->name('staff.stock-count');
//          Route::post('/cart/updateTax', [POSController::class, 'updateTax'])->name('pos.cart.updateTax');

//         // order routes
//         Route::get('/pos/orders', [POSController::class, 'orders'])->name('pos.orders');
//         // routes/web.php
//         Route::post('/orders/{order}/status', [POSController::class, 'updateStatus'])->name('pos.orders.updateStatus');
//         Route::get('/pos/orders/list', [POSController::class, 'orderlist'])->name('pos.orders.table');
//         Route::get('/pos/orders/{id}', [POSController::class, 'vieworder'])->name('pos.orders.view');
//         Route::get('/pos/inventories', [POSController::class, 'inventories'])->name('pos.inventories');

//         Route::get('/pos/receipt/print/{id}', [POSController::class, 'printReceiptOrder'])->name('staff.receipt.print');
//         Route::get('/pos/receipt/print/local/{id}', [POSController::class, 'printReceiptOrderLocal'])->name('staff.receipt.print.local');
//         Route::get('/pos/kitchen/print/local/{id}', [POSController::class, 'printKitchenOrderLocal'])->name('staff.kitchen.print.local');



//         Route::get('/pos/kitchen/print/{id}', [POSController::class, 'printKitchenOrder'])->name('staff.kitchen.print');
//         Route::get('/pos/sticker/print/{id}', [POSController::class, 'printStickerOrder'])->name('staff.sticker.print');
//         // web.php or api.php
//         Route::get('/customers/search', [POSController::class, 'searchcustomer'])->name('pos.customers.search');
//         // Apply Coupon
//         Route::post('/pos/apply-coupon', [POSController::class, 'applyCoupon'])->name('apply.coupon');
//             // Remove Coupon
//         Route::post('/pos/remove-coupon', [POSController::class, 'removeCoupon'])->name('pos.remove.coupon');

//         Route::post('/pos/close-queue', [POSController::class, 'closeBranchQueue'])->name('pos.close.branch');
//  //});
//     Route::get('/pos/logout', [POSController::class, 'logoutpos'])->name('pos.logout');
//     Route::get('pos/shift-inventory', [POSController::class, 'shiftInventory'])->name('shift.inventory');
//     Route::post('pos/shift-inventory/store', [POSController::class, 'storeShiftInventory'])->name('shift.inventory.store');

//     // mange pos inventory
//     Route::get('pos/ingredients', [IngredientController::class, 'index']);



//     // mange pos cashout
//     Route::get('pos/cashout', [POSController::class, 'CashoutRefund'])->name('pos.cashout');
//     Route::get('/pso/cashout-refund', [POSController::class, 'CashoutRefund'])->name('pos.cashout.refund');
//     Route::post('/pos/cashout-refund/store', [POSController::class, 'storeCashoutRefund'])->name('pos.cashout.store');
//     Route::get('/pso/order/amount', [POSController::class, 'getOrderAmount'])->name('pos.order.amount');

// });
// Route::group(['middleware' => ['auth', 'role:branchadmin']], function () {

//         Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
//         Route::get('/pos/category/{id}', [POSController::class, 'getProductsByCategory'])->name('pos.category');
//         Route::get('/variants/options', [POSController::class, 'loadVariantOptions'])->name('variants.options');
//         Route::post('/cart/add', [POSController::class, 'addcart'])->name('pos.cart.add');
//         Route::post('/cart/remove', [POSController::class, 'removecart'])->name('pos.cart.remove');
//         Route::post('/cart/update', [POSController::class, 'updatecart'])->name('pos.cart.update');
//         Route::get('/pos/cart', [POSController::class, 'getCart'])->name('pos.cart.index');
//         Route::get('/pos/cart/recipt', [POSController::class, 'getCartRecipt'])->name('pos.cart.recipt');
//         Route::post('/pos/payment/complete', [POSController::class, 'completePayment'])->name('pos.payment.complete');
//         Route::post('/pos/cart/updatequantity', [POSController::class, 'updatequantity'])->name('pos.cart.updatequantity');
//         Route::post('/pos/cart/edit', [POSController::class, 'editcart'])->name('pos.cart.edit');
//         Route::post('/pos/cart/updateNote', [POSController::class, 'updateNote'])->name('pos.cart.updateNote');
//         Route::post('/pos/cart/updateOrderNote', [POSController::class, 'updateOrderNote'])->name('pos.cart.updateOrderNote');
//         Route::get('/staff/cash-count', [POSController::class, 'cashCount'])->name('staff.cash-count');
//         Route::get('/staff/stock-count', [POSController::class, 'stockCount'])->name('staff.stock-count');

//         // order routes
//         Route::get('/pos/orders', [POSController::class, 'orders'])->name('pos.orders');
//         // routes/web.php
//         Route::post('/orders/{order}/status', [POSController::class, 'updateStatus'])->name('pos.orders.updateStatus');
//         Route::get('/pos/orders/list', [POSController::class, 'orderlist'])->name('pos.orders.table');
//         Route::get('/pos/orders/{id}', [POSController::class, 'vieworder'])->name('pos.orders.view');
//         Route::get('/pos/inventories', [POSController::class, 'inventories'])->name('pos.inventories');

//         Route::get('/pos/receipt/print/{id}', [POSController::class, 'printReceiptOrder'])->name('staff.receipt.print');
//         Route::get('/pos/kitchen/print/{id}', [POSController::class, 'printKitchenOrder'])->name('staff.kitchen.print');
//         Route::get('/pos/sticker/print/{id}', [POSController::class, 'printStickerOrder'])->name('staff.sticker.print');

//     Route::get('/pos/logout', [POSController::class, 'logoutpos'])->name('pos.logout');
//     Route::get('pos/shift-inventory', [POSController::class, 'shiftInventory'])->name('shift.inventory');
//     Route::post('pos/shift-inventory/store', [POSController::class, 'storeShiftInventory'])->name('shift.inventory.store');

//     // mange pos inventory
//     Route::get('ingredients', [IngredientController::class, 'index'])->name('badmin.ingredient.index');
//     Route::get('ingredients/view/{id}', [IngredientController::class, 'view'])->name('badmin.ingredient.view');
//     Route::post('ingredients/update-quantity/{id}/{branchid}', [IngredientController::class, 'updateQuantity'])->name('badmin.ingredient.updateQuantity');
//     Route::post('ingredients/add/custom/{id}', [IngredientController::class, 'addCustom'])->name('badmin.ingredient.custom.update');

//     // mange pos cashout
//     Route::get('pos/cashout', [POSController::class, 'CashoutRefund'])->name('pos.cashout');
//     Route::get('/pso/cashout-refund', [POSController::class, 'CashoutRefund'])->name('pos.cashout.refund');
//     Route::post('/pos/cashout-refund/store', [POSController::class, 'storeCashoutRefund'])->name('pos.cashout.store');
//     Route::get('/pso/order/amount', [POSController::class, 'getOrderAmount'])->name('pos.order.amount');
// });


// Waiter / Service Staff routes
Route::group(['middleware' => ['auth', 'role:waiter']], function () {
    // Route::get('/waiter/dashboard', [App\Http\Controllers\WaiterController::class, 'dashboard'])
    //     ->name('waiter.dashboard');

    Route::get('/waiter/dashboard', function () {
        return 'waiter';
    })
        ->name('waiter.dashboard');
});
