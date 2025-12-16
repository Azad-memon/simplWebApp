<?php

use App\Http\Controllers\Admin\AddonsController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CmsPageController;
use App\Http\Controllers\Admin\ConstraintController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DealsController;
use App\Http\Controllers\Admin\IngredientController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\LanguageTranslationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\badmin\StaffController;
use App\Http\Controllers\badmin\StaffShiftController;
use Illuminate\Support\Facades\Route;

//use App\Http\Controllers\Admin\DealItemController;
// use in badmin and admin
Route::get('ing-inventory/view/{id}/{bid}', [IngredientController::class, 'view'])->name('admin.ing-inventory.view');

Route::group(['middleware' => ['auth', 'role:admin']], function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard2', [DashboardController::class, 'index2'])->name('dashboard2');
        Route::get('/load-product-report', [DashboardController::class, 'loadProductReport'])->name('loadProductReport');
        Route ::get('/load-sales-report', [DashboardController::class, 'loadSalesReport'])->name('loadSalesReport');
        Route::get('/load-dashboard-summary', [DashboardController::class, 'loadDashboardSummary'])->name('loadDashboardSummary');

        //Languages
        Route::get('languages', [LanguageController::class, 'index'])->name('languages.index');
        Route::get('languages/create', [LanguageController::class, 'create'])->name('languages.create');
        Route::post('languages', [LanguageController::class, 'store'])->name('languages.store');
        Route::get('languages/{language}/edit', [LanguageController::class, 'edit'])->name('languages.edit');
        Route::post('languages/{language}', [LanguageController::class, 'update'])->name('languages.update');
        Route::get('languages/delete/{id}', [LanguageController::class, 'destroy'])->name('languages.delete');
        Route::get('add-languages', [LanguageController::class, 'add'])->name('language.add');
        Route::get('edit-languages/{id}', [LanguageController::class, 'edit'])->name('language.edit');

        //Languages Translation
        Route::resource('language-translations', LanguageTranslationController::class)->parameters(['languages' => 'translation']);
        Route::get('language-translations/delete/{id}', [LanguageTranslationController::class, 'destroy'])->name('language-translations.delete');
        Route::get('addtranslation', [LanguageTranslationController::class, 'addtranslation'])->name('language-translation.add');
        Route::get('edittranslation/{id}', [LanguageTranslationController::class, 'edittranslation'])->name('language-translation.edit');
        Route::post('updateAll', [LanguageTranslationController::class, 'updateAll'])->name('language-translation.updateAll');

        Route::resource('branches', BranchController::class);
        Route::get('branches/delete/{id}', [BranchController::class, 'destroy'])->name('branches.delete');
        Route::get('add-branch', [BranchController::class, 'addbranch'])->name('branch.add');
        Route::get('edit-branch/{id}', [BranchController::class, 'editbranch'])->name('branch.edit');
        Route::get('branch/view/{id}', [BranchController::class, 'view'])->name('branch.view');
        Route::get('branch/adduser/{id}', [BranchController::class, 'adduser'])->name('branch.branchadmin.add');
        Route::post('branch/toggle-status', [BranchController::class, 'toggleStatus'])->name('branch.toggleStatus');
        Route::get('branch/edituser/{id}', [BranchController::class, 'edituser'])->name('branch.branchadmin.edituser');
        Route::post('branch/saveuser', [BranchController::class, 'saveuser'])->name('branch.branchadmin.save');
        Route::put('/branch-admins/{user}', [BranchController::class, 'updateuser'])->name('branch.branchadmin.update');
        Route::get('/branch-admins/delete/{user}/{branchid}', [BranchController::class, 'deleteuser'])->name('branch.branchadmin.delete');
        Route::post('/toggle-status', [BranchController::class, 'superadminToggleUserStatus'])->name('branch.branchadmin.user.toggleStatus');

        Route::resource('categories', CategoryController::class);
        Route::get('add-category', [CategoryController::class, 'addcategory'])->name('category.add');
        Route::get('edit-category/{id}', [CategoryController::class, 'edit'])->name('category.edit');
        Route::get('dropdown', [CategoryController::class, 'dropdowndata'])->name('category.dropdowndata');
        Route::get('categories/delete/{id}', [CategoryController::class, 'destroy'])->name('category.delete');
        Route::get('categories/translate/{id}', [CategoryController::class, 'translate'])->name('categories.translate');
        Route::get('categories/view-details/{id}', [CategoryController::class, 'ViewDetails'])->name('categories.view-details');

        Route::resource('constraint', ConstraintController::class);
        Route::get('add-constraint', [ConstraintController::class, 'addconstraint'])->name('constraint.add');
        Route::get('constraints/delete/{id}', [ConstraintController::class, 'destroy'])->name('constraints.delete');
        Route::get('constraints/dropdown', [ConstraintController::class, 'dropdown'])->name('constraints.dropdown');
        Route::get('constraints/translate/{id}', [ConstraintController::class, 'translate'])->name('constraints.translate');

        //category
        Route::get('ingredient/categories', [IngredientController::class, 'categories'])->name('ingredient.categories');
        Route::get('ingredients/view/{cat_id?}', [IngredientController::class, 'ViewIngrediants'])->name('ingredients.view');
        Route::get('ingredient/add-category', [IngredientController::class, 'cretecategories'])->name('ingredient.category.add');
        Route::post('ingredient/store-category', [IngredientController::class, 'storeCategories'])->name('ingredient.category.store');
        Route::post('ingredient/updateCategories/{id}', [IngredientController::class, 'updateCategories'])->name('ingredient.category.update');
        Route::get('ingredient/edit-category/{id}', [IngredientController::class, 'editcategories'])->name('ingredient.category.edit');
        Route::get('ingredient/delete-category/{id}', [IngredientController::class, 'deleteCategory'])->name('ingredient.category.delete');
        Route::post('ingredient/toggle-ingredient-status', [IngredientController::class, 'toggleStatusCategory'])->name('ingredient.category.toggleStatus');
        Route::post('/ingredients/attach-standard', [IngredientController::class, 'attachStandardIngredients'])
     ->name('ingredient.attachStandard');

        Route::resource('ingredient', IngredientController::class);
        Route::get('add-ingredient', [IngredientController::class, 'add'])->name('ingredient.add');
        Route::get('edit-ingredient/{id}', [IngredientController::class, 'edit'])->name('ingredient.edit');
        Route::post('toggle-ingredient-status', [IngredientController::class, 'toggleStatus'])->name('ingredient.toggleStatus');
        Route::get('ingredient/delete/{id}', [IngredientController::class, 'destroy'])->name('ingredient.delete');
        Route::get('ingredient/translate/{id}', [IngredientController::class, 'translate'])->name('ingredient.translate');

        Route::resource('products', ProductController::class);
        Route::get('add-product', [ProductController::class, 'create'])->name('product.add');
        Route::get('edit-product/{id}', [ProductController::class, 'edit'])->name('product.edit');
        Route::post('toggle-product-status', [ProductController::class, 'toggleStatus'])->name('product.toggleStatus');
        Route::get('product/delete/{id}', [ProductController::class, 'destroy'])->name('product.delete');
        Route::get('products/view/{id}', [ProductController::class, 'view'])->name('product.view');
        // Route::get('products/variants/{id}', [ProductController::class, 'variants'])->name('product.variants');
        Route::get('products/translate/{id}', [ProductController::class, 'translate'])->name('product.translate');
        Route::post('product/toggle-flag', [ProductController::class, 'toggleFlag'])
            ->name('product.toggleFlag');

        //Size
        Route::get('managesize', [ProductController::class, 'ManageSize'])->name('size.index');
        Route::get('createsize', [ProductController::class, 'CreateSize'])->name('size.create');
        Route::post('save-size', [ProductController::class, 'SaveSize'])->name('size.save');
        Route::get('edit-size/{id}', [ProductController::class, 'editSize'])->name('size.edit');
        Route::get('size/delete/{id}', [ProductController::class, 'deleteSizes'])->name('size.delete');
        Route::post('size/update/{id}', [ProductController::class, 'updateSizes'])->name('size.update');
        Route::get('size/translate/{id}', [ProductController::class, 'sizetranslate'])->name('size.translate');

        //unit
        Route::get('manageunit', [ProductController::class, 'ManageUnit'])->name('unit.index');
        Route::get('createunit', [ProductController::class, 'CreateUnit'])->name('unit.create');
        Route::post('save-unit', [ProductController::class, 'SaveUnit'])->name('unit.save');
        Route::get('edit-unit/{id}', [ProductController::class, 'editUnit'])->name('unit.edit');
        Route::get('unit/delete/{id}', [ProductController::class, 'deleteUnit'])->name('unit.delete');
        Route::post('unit/update/{id}', [ProductController::class, 'updateUnit'])->name('unit.update');
        Route::get('unit/translate/{id}', [ProductController::class, 'unittranslate'])->name('unit.translate');

        //Product variants
        Route::get('products/{product}/view-details', [ProductController::class, 'ViewDetails'])->name('product.view-details');
        Route::get('products/variants/add', [ProductController::class, 'addVariant'])->name('product.variants.add');
        Route::post('products/variants/store', [ProductController::class, 'storeVariant'])->name('product.variants.store');
        Route::get('products/variants/edit/{product}/{variant}', [ProductController::class, 'editVariant'])->name('product.variants.edit');
        Route::post('products/variants/update/{product}/{variant}', [ProductController::class, 'updateVariant'])->name('product.variants.update');
        Route::get('products/variants/delete/{variant}', [ProductController::class, 'destroyVariant'])->name('product.variants.delete');
        Route::post('products/variants/toggle-status', [ProductController::class, 'toggleVariantStatus'])->name('product.variants.toggleStatus');

        // Product Ingredients
        Route::get('products/variants/ingredients/{variant}', [ProductController::class, 'getVariantIngredients'])->name('product.variants.ingredients');
        Route::get('products/variants/ingredients/add/{variant}', [ProductController::class, 'addVariantIngredient'])->name('product.variants.ingredients.add');
        Route::post('products/variants/ingredients/toggle-status', [ProductController::class, 'toggleVariantIngredientStatus'])->name('product.variants.ingredients.toggleStatus');


        Route::post('products/variants/ingredients/store', [ProductController::class, 'storeVariantIngredient'])->name('product.variants.ingredients.store');
        Route::get('products/variants/ingredients/edit/{variant}/{ingredient}', [ProductController::class, 'editVariantIngredient'])->name('product.variants.ingredients.edit');
        Route::post('products/variants/ingredients/update/{variant}/{ingredient}', [ProductController::class, 'updateVariantIngredient'])->name('product.variants.ingredients.update');
        Route::get('products/variants/ingredients/delete/{variant}/{id}', [ProductController::class, 'destroyVariantIngredient'])->name('product.variants.ingredients.delete');
        Route::get('ingredients_category/by-category', [ProductController::class, 'getByCategory'])->name('ingredients.byCategory');


        //addons
        Route::prefix('product/addons')->group(function () { //Start product
        // Addon Ingredients
            Route::get('create', [AddonsController::class, 'add'])->name('addons.create');
            Route::post('store', [AddonsController::class, 'store'])->name('addons.store');
            Route::get('edit/{id}', [AddonsController::class, 'edit'])->name('addons.edit');
            Route::post('update/{id}', [AddonsController::class, 'update'])->name('addons.update');
            // Addon Products
            Route::get('add-product', [AddonsController::class, 'addProduct'])->name('addons.addProduct');
            Route::post('store-product', [AddonsController::class, 'storeProduct'])->name('addons.storeProduct');
            Route::get('edit-product/{id}', [AddonsController::class, 'editProduct'])->name('addons.editProduct');
            Route::post('update-product/{id}', [AddonsController::class, 'updateProduct'])->name('addons.updateProduct');
            Route::get('product-addon-variants/{id}', [AddonsController::class, 'getprodcutvariants'])->name('products.addon.variants');
            Route::get('edit-product-addon/{id}', [AddonsController::class, 'editProductAddon'])->name('addons.editProductAddon');

            Route::get('delete/{id}', [AddonsController::class, 'destroy'])->name('addons.delete');
            Route::post('toggle-status', [AddonsController::class, 'toggleAddonStatus'])->name('addons.toggleStatus');
            Route::get('translate/{id}', [AddonsController::class, 'addonTranslate'])->name('addons.translate');
            Route::get('/{id}', [AddonsController::class, 'index'])->name('addons.index');
            Route::get('get-ingredients/{categoryId}', [AddonsController::class, 'getIngredientsByCategory'])->name('addons.getIngredientsByCategory');
        }); // End product

        Route::prefix('coupons')->group(function () {
            Route::get('/', [CouponController::class, 'index'])->name('coupons.index');
            Route::post('/', [CouponController::class, 'store'])->name('coupons.store');
            Route::get('/add', [CouponController::class, 'add'])->name('coupons.add');
            Route::get('edit/{id}', [CouponController::class, 'edit'])->name('coupons.edit');
            Route::put('/{id}', [CouponController::class, 'update'])->name('coupons.update');
            Route::get('/{id}', [CouponController::class, 'destroy'])->name('coupons.destroy');
            Route::get('Coupon/getVarient', [CouponController::class, 'getproductVarient'])->name('coupons.products.variants');
            Route::post('toggle-status', [CouponController::class, 'toggleAddonStatus'])->name('coupons.toggleStatus');
        }); //end coupon
        Route::get('banners', [BannerController::class, 'index'])->name('banners.index');
        Route::get('banners/create', [BannerController::class, 'create'])->name('banners.create');
        Route::post('banners', [BannerController::class, 'store'])->name('banners.store');
        Route::get('banners/{id}', [BannerController::class, 'show'])->name('banners.show');
        Route::get('banners/edit/{id}', [BannerController::class, 'edit'])->name('banners.edit');
        Route::post('banners/{id}', [BannerController::class, 'update'])->name('banners.update');
        Route::get('banners/delete/{id}', [BannerController::class, 'destroy'])->name('banners.destroy');
        Route::post('banner/toggle-status', [BannerController::class, 'bannerToogle'])->name('banner.toggleStatus');
        // Home page Product
        Route::get('app/home-products', [BannerController::class, 'HomepageProducts'])->name('home.product.index');
        Route::get('app/home-products/create', [BannerController::class, 'createHomeProduct'])->name('home.product.create');
        Route::get('app/home-products/edit/{id}', [BannerController::class, 'editeHomeProduct'])->name('home.product.edit');
        Route::post('app/home-products/save', [BannerController::class, 'saveHomeProduct'])->name('home.product.save');
        Route::post('app/home-products/update', [BannerController::class, 'updateHomeProduct'])->name('home.product.update');
        Route::get('app/home-products/delete/{id}', [BannerController::class, 'deleteHomeProduct'])->name('home.product.delete');

        //cms pages
        Route::prefix('cms')->name('cms.')->group(function () {
            Route::get('/', [CmsPageController::class, 'index'])->name('index');
            Route::get('/create', [CmsPageController::class, 'create'])->name('add');
            Route::post('/', [CmsPageController::class, 'store'])->name('store');
            Route::get('/{id}', [CmsPageController::class, 'show'])->name('show');
            Route::get('/edit/{id}', [CmsPageController::class, 'edit'])->name('edit');
            Route::put('/{id}', [CmsPageController::class, 'update'])->name('update');
            Route::get('delete/{id}', [CmsPageController::class, 'destroy'])->name('destroy');
            Route::post('/toggle-status', [CmsPageController::class, 'ToggleStatus'])->name('toggleStatus');
        });
        Route::get('/createpopup', [CmsPageController::class, 'createpopup'])->name('createpopup');
        Route::post('/createpopup/save', [CmsPageController::class, 'storePopup'])->name('popup.store');

        Route::prefix('orders')->name('order.')->group(function () {
            Route::get('live', [OrderController::class, 'liveorders'])->name('liveorders');
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/{id}', [OrderController::class, 'show'])->name('show');
            Route::post('updateStatus/{id}', [OrderController::class, 'updateStatus'])->name('updateStatus');

        });
        Route::get('kdsorders', [OrderController::class, 'kdsOrders'])->name('kdsorders');
        Route::get('kdsorder/details/{id}', [OrderController::class, 'kdsOrdersDetails'])->name('kdsorder.details');
        Route::prefix('customers')->name('customer.')->group(function () {
            Route::get('/', [CustomerController::class, 'index'])->name('list');
            Route::get('/show/{id}', [CustomerController::class, 'show'])->name('show');
            Route::get('/showloyalty/{id}', [CustomerController::class, 'showloyalty'])->name('show.showloyalty');
        });

        // Deals
        Route::get('deals', [DealsController::class, 'index'])->name('deals.index');
        Route::get('deals/create', [DealsController::class, 'create'])->name('deals.create');
        Route::post('deals', [DealsController::class, 'store'])->name('deals.store');
        Route::get('deals/edit/{id}', [DealsController::class, 'edit'])->name('deals.edit');
        Route::post('deals/{deal}', [DealsController::class, 'update'])->name('deals.update');
        Route::get('deals/delete/{id}', [DealsController::class, 'destroy'])->name('deals.delete');
        Route::get('deals/view/{id}', [DealsController::class, 'view'])->name('deals.view');
        Route::post('deals/toggle-status', [DealsController::class, 'toggleStatus'])->name('deals.toggleStatus');
        Route::get('deals/add-item', [DealsController::class, 'addItem'])->name('deals.addItem');
        Route::post('deals/store-item', [DealsController::class, 'storeItem'])->name('deals.storeItem');
        Route::get('deals/edit-item/{id}', [DealsController::class, 'editItem'])->name('deals.editItem');
        Route::post('deals/update-item/{id}', [DealsController::class, 'updateItem'])->name('deals.updateItem');
        Route::get('deals/delete-item/{id}', [DealsController::class, 'destroyItem'])->name('deals.deleteItem');
        Route::get('deals/items/{dealId}', [DealsController::class, 'getDealItems'])->name('deals.items');

        //Fcm token save
        Route::post('save-fcm-token', [AdminController::class, 'saveFcmToken'])->name('save.fcm.token');

        //Settings
        Route::get('loyaltysettings', [SettingController::class, 'loyaltysettings'])->name('loyaltysettings');
        Route::get('loyaltysettings/createloyalty', [SettingController::class, 'createloyalty'])->name('loyalty.create');
        Route::post('loyaltysettings/storeloyalty', [SettingController::class, 'storeloyalty'])->name('loyalty.store');
        Route::get('loyaltysettings/editloyalty/{id}', [SettingController::class, 'editloyalty'])->name('loyalty.edit');
        Route::post('loyaltysettings/updateloyalty/{id}', [SettingController::class, 'updateloyalty'])->name('loyalty.update');
        //Payment Method
        Route::get('paymentmethod', [SettingController::class, 'PaymentMethod'])->name('paymentmethod');
        Route::get('paymentmethod/create', [SettingController::class, 'PaymentMethodcreate'])->name('paymentmethod.create');
        Route::post('paymentmethod/store', [SettingController::class, 'PaymentMethodstore'])->name('paymentmethod.store');
        Route::get('paymentmethod/edit/{id}', [SettingController::class, 'PaymentMethodedit'])->name('paymentmethod.edit');
        Route::post('paymentmethod/update/{id}', [SettingController::class, 'PaymentMethodupdate'])->name('paymentmethod.update');
        Route::get('paymentmethod/delete/{id}', [SettingController::class, 'PaymentMethoddelete'])->name('paymentmethod.delete');
        Route::post('paymentmethod/toggle-status', [SettingController::class, 'PaymentMethodtoggleStatus'])->name('paymentmethod.toggleStatus');

        Route::post('ingredients/update-quantity/{id}/{branchid}', [IngredientController::class, 'updateQuantity'])->name('ingredient.updateQuantity');
        Route::post('ingredients/add/custom/{id}', [IngredientController::class, 'addCustom'])->name('ingredient.custom.update');


          // Staff shifts management routes
        Route::get('/shifts', [StaffShiftController::class, 'index'])->name('badmin.shifts.index');
        Route::get('shifts/create/{branchid}', [StaffShiftController::class, 'create'])->name('shifts.create');
        Route::post('/shifts', [StaffShiftController::class, 'store'])->name('shifts.store');
        Route::get('/shifts/edit/{id}', [StaffShiftController::class, 'edit'])->name('shifts.edit');
        Route::post('/shifts/{shift}', [StaffShiftController::class, 'update'])->name('shifts.update');
        Route::get('/shifts/{id}', [StaffShiftController::class, 'destroy'])->name('shifts.destroy');

         // Branch staff management routes
        Route::get('staff', [StaffController::class, 'index'])->name('staff.index');
        Route::get('staff/create/{branchid}', [StaffController::class, 'create'])->name('staff.create');
        Route::post('staff/store', [StaffController::class, 'store'])->name('staff.store');
        Route::get('staff/edit/{id}/{branchid?}', [StaffController::class, 'edit'])->name('staff.edit');
        Route::post('staff/update/{id}', [StaffController::class, 'update'])->name('staff.update');
        Route::get('staff/delete/{id}', [StaffController::class, 'destroy'])->name('staff.delete');
        Route::post('staff/toggle-status', [StaffController::class, 'toggleStatus'])->name('toggleStatus');
    });
});
