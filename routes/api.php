<?php

use App\Http\Controllers\Admin\ProfileController as DashProfileController;
use App\Http\Controllers\Admin\AttributeController as DashAttributeController;
use App\Http\Controllers\Admin\AttributeGroupController as DashAttributeGroupController;
use App\Http\Controllers\Admin\BankCommissionController;
use App\Http\Controllers\Admin\BrandController as DashBrandController;
use App\Http\Controllers\Admin\CategoryController as DashCategoryController;
use App\Http\Controllers\Admin\CityController as DashCityController;
use App\Http\Controllers\Admin\CountryController as DashCountryController;
use App\Http\Controllers\Admin\CurrencyController as DashCurrencyController;
use App\Http\Controllers\Admin\DirectPurchaseController as DashDirectPurchaseController;
use App\Http\Controllers\Admin\HomeController as DashHomeController;
use App\Http\Controllers\Admin\HomeSectionController as DashHomeSectionController;
use App\Http\Controllers\Admin\IntegrationController as DashIntegrationController;
use App\Http\Controllers\Admin\LanguageController as DashLanguageController;
use App\Http\Controllers\Admin\NotificationController as DashNotificationController;
use App\Http\Controllers\Admin\NotificationSettingController as DashNotificationSettingController;
use App\Http\Controllers\Admin\NotificationTokenController as DashNotificationTokenController;
use App\Http\Controllers\Admin\OptionController as DashOptionController;
use App\Http\Controllers\Admin\IntegrationOptionKeyController as DashIntegrationOptionKeyController;
use App\Http\Controllers\Admin\OrderController as DashOrderController;
use App\Http\Controllers\Admin\OrderUserController as DashOrderUserController;
use App\Http\Controllers\Admin\ProductController as DashProductController;
use App\Http\Controllers\Admin\ProductSerialController as DashProductSerialController;
use App\Http\Controllers\Admin\RegionController as DashRegionController;
use App\Http\Controllers\Admin\RoleAndPermissionController as DashRoleAndPermissionController;
use App\Http\Controllers\Admin\SellerController as DashSellerController;
use App\Http\Controllers\Admin\SellerGroupController as DashSellerGroupController;
use App\Http\Controllers\Admin\SellerGroupLevelController as DashSellerGroupLevelController;
use App\Http\Controllers\Admin\SettingController as DashSettingController;
use App\Http\Controllers\Admin\SliderController as DashSliderController;
use App\Http\Controllers\Admin\StaticPageController as DashStaticPageController;
use App\Http\Controllers\Admin\SubAdminController as DashSubAdminController;
use App\Http\Controllers\Admin\ValueAddedTaxController as DashValueAddedTaxController;
use App\Http\Controllers\Admin\VendorController as DashVendorController;
use App\Http\Controllers\Admin\VendorProductController as DashVendorProductController;
use App\Http\Controllers\Admin\File\HandleFileController as DashHandleFileController;
////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Seller\NotificationController as SellerNotificationController;
use App\Http\Controllers\Seller\NotificationTokenController as SellerNotificationTokenController;
use App\Http\Controllers\Seller\CurrencyController as SellerCurrencyController;
use App\Http\Controllers\Seller\AdminListController;
use App\Http\Controllers\Seller\AttachmentController;
use App\Http\Controllers\Seller\AuthController as SellerAuthController;
use App\Http\Controllers\Seller\BankController;
use App\Http\Controllers\Admin\BankController as DashBankController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\SellerComplaintsController;
use App\Http\Controllers\Seller\CityController;
use App\Http\Controllers\Seller\CountryController;
use App\Http\Controllers\Seller\DashboardController;
use App\Http\Controllers\Seller\FavController;
use App\Http\Controllers\Seller\ForgotPasswordController;
use App\Http\Controllers\Seller\PermissionController;
use App\Http\Controllers\Seller\ProductController;
use App\Http\Controllers\Seller\RegionController;
use App\Http\Controllers\Seller\RoleController;
use App\Http\Controllers\Seller\SettingController;
use App\Http\Controllers\Seller\Store\BrandController;
use App\Http\Controllers\Seller\Store\CartController;
use App\Http\Controllers\Seller\Store\CategoryController;
use App\Http\Controllers\Seller\Store\OrderController;
use App\Http\Controllers\Seller\SupportTicketController;
use App\Http\Controllers\Seller\WalletController;
use App\Http\Controllers\Admin\WalletController as DashWalletController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Seller\CurrencyController;
use App\Http\Controllers\Seller\ReportController;
use \App\Http\Controllers\Seller\Auth\SmsVerificationController;
////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////

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




///////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////// Dashboard ////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////

////////////////////////////// login admin /////////////////////////
Route::post('loginAdmin', [AuthController::class, 'loginAdmin']);
Route::post('logout', [AuthController::class, 'logout']);
Route::group(['middleware' => ['check_admin_access', 'authGurad:adminApi', 'getLang'],'prefix' => 'admin', 'as' => 'api.admin.'], function () {

    ////////////////////////// Destroy File ////////////////////////////////
    Route::post('/files/destroy', [DashHandleFileController::class, 'destroyFile']);

    //////////////////////////// Exports ///////////////////////////////
    Route::post('productPricesExport', [DashProductController::class, 'productPricesExport']);

    //////////////////////////// Imports ///////////////////////////////
    Route::post('productTranslationsImports', [DashProductController::class, 'productTranslationsImports']);
    Route::post('newProductsImports', [DashProductController::class, 'newProductsImports']);
    Route::post('productPricesImports', [DashProductController::class, 'productPricesImports']);

    //////////////////////////// profile ///////////////////////////////
    Route::get('profile', [DashProfileController::class, 'index'])->name('profile');

    //////////////////////////// Home ///////////////////////////////
    Route::get('home', [DashHomeController::class, 'index'])->name('home')->middleware(['can:view-home']);

    ////////////////////////// Tokens like ( Firebase ) ///////////////////////////////
    Route::group(['prefix' => 'notificationTokens', 'as' => 'notificationTokens.'], function () {
        Route::post('firebase/store', [DashNotificationTokenController::class, 'firebaseStore'])->name('firebaseStore');
    });

    //////////////////////////// Notifications ///////////////////////////////
    Route::group(['prefix' => 'notifications', 'as' => 'notifications.'], function () {
        Route::get('', [DashNotificationController::class, 'index'])->name('index');
        Route::get('count', [DashNotificationController::class, 'count'])->name('count');
        Route::get('read/{notificationId}', [DashNotificationController::class, 'read'])->name('read');
    });

    ////////////////////////// bank commissions /////////////////////////////////
    Route::group(['prefix' => 'bank_commissions'],function(){
        Route::get('/',[BankCommissionController::class,'index'])->middleware(['can:view-bank-commissions']);
        #set setting
        Route::post('set-setting',[BankCommissionController::class,'setSetting'])->middleware(['can:set-bank-commissions']);
    });

    //////////////////////////// .countries ///////////////////////////////
    Route::get('countries', [DashCountryController::class, 'index'])->middleware(['can:view-countries']);
    Route::post('countries', [DashCountryController::class, 'store'])->middleware(['can:create-countries']);
    Route::get('countries/{id}', [DashCountryController::class, 'show'])->middleware(['can:view-countries']);
    Route::post('countries/{id}', [DashCountryController::class, 'update'])->middleware(['can:update-countries']);
    Route::get('getAllCountriesForm', [DashCountryController::class, 'getAllCountriesForm'])->middleware(['can:view-all-countries-form']);
    Route::delete('countries/{id}', [DashCountryController::class, 'destroy'])->middleware(['can:delete-countries']);

    //////////////////////////// .regions ///////////////////////////////
    Route::get('regions', [DashRegionController::class, 'index'])->middleware(['can:view-regions']);
    Route::post('regions', [DashRegionController::class, 'store'])->middleware(['can:create-regions']);
    Route::get('regions/{id}', [DashRegionController::class, 'show'])->middleware(['can:view-regions']);
    Route::delete('regions/{id}', [DashRegionController::class, 'destroy'])->middleware(['can:delete-regions']);
    Route::post('regions/{id}', [DashRegionController::class, 'update'])->middleware(['can:update-regions']);
    Route::get('getAllRegionsForm', [DashRegionController::class, 'getAllRegionsForm'])->middleware(['can:view-all-regions-form']);

    //////////////////////////// .cities ///////////////////////////////
    Route::get('cities', [DashCityController::class, 'index'])->middleware(['can:view-cities']);
    Route::post('cities', [DashCityController::class, 'store'])->middleware(['can:create-cities']);
    Route::get('cities/{id}', [DashCityController::class, 'show'])->middleware(['can:view-cities']);
    Route::delete('cities/{id}', [DashCityController::class, 'destroy'])->middleware(['can:delete-cities']);
    Route::post('cities/{id}', [DashCityController::class, 'update'])->middleware(['can:update-cities']);
    Route::get('getAllCitiesForm', [DashCityController::class, 'getAllCitiesForm'])->middleware(['can:view-all-cities-form']);

    //////////////////////////// languages ///////////////////////////////
    Route::get('languages', [DashLanguageController::class, 'index'])->middleware(['can:view-languages']);
    Route::post('languages', [DashLanguageController::class, 'store'])->middleware(['can:create-languages']);
    Route::get('languages/{id}', [DashLanguageController::class, 'show'])->middleware(['can:view-languages']);
    Route::delete('languages/{id}', [DashLanguageController::class, 'destroy'])->middleware(['can:delete-languages']);
    Route::post('languages/{id}', [DashLanguageController::class, 'update'])->middleware(['can:update-languages']);

    //////////////////////////// currencies ///////////////////////////////
    Route::get('currencies', [DashCurrencyController::class, 'index'])->middleware(['can:view-currencies']);
    Route::post('currencies', [DashCurrencyController::class, 'store'])->middleware(['can:create-currencies']);
    Route::get('currencies/{id}', [DashCurrencyController::class, 'show'])->middleware(['can:view-currencies']);
    Route::delete('currencies/{id}', [DashCurrencyController::class, 'destroy'])->middleware(['can:delete-currencies']);
    Route::post('currencies/{id}', [DashCurrencyController::class, 'update'])->middleware(['can:update-currencies']);

    //////////////////////////// brands ///////////////////////////////
    Route::get('brands', [DashBrandController::class, 'index'])->middleware(['can:view-brands']);
    Route::post('brands', [DashBrandController::class, 'store'])->middleware(['can:create-brands']);
    Route::get('brands/{id}', [DashBrandController::class, 'show'])->middleware(['can:view-brands']);
    Route::delete('brands/{id}', [DashBrandController::class, 'destroy'])->middleware(['can:delete-brands']);
    Route::post('brands/{id}', [DashBrandController::class, 'update'])->middleware(['can:update-brands']);
    Route::post('brand/changeStatus/{id}', [DashBrandController::class, 'changeStatus'])->middleware(['can:change-brand-status']);
    Route::post('brand/destroy_selected', [DashBrandController::class, 'destroy_selected'])->middleware(['can:destroy-selected-brands']);

    //////////////////////////// vendors ///////////////////////////////
    Route::get('vendors', [DashVendorController::class, 'index'])->middleware(['can:view-vendors']);
    Route::post('vendors', [DashVendorController::class, 'store'])->middleware(['can:create-vendors']);
    Route::get('vendors/{id}', [DashVendorController::class, 'show'])->middleware(['can:view-vendors']);
    Route::delete('vendors/{id}', [DashVendorController::class, 'destroy'])->middleware(['can:delete-vendors']);
    Route::post('vendors/{id}', [DashVendorController::class, 'update'])->middleware(['can:update-vendors']);
    Route::post('vendor/destroy_selected', [DashVendorController::class, 'destroy_selected'])->middleware(['can:destroy-selected-vendors']);
    Route::get('vendor/trashes', [DashVendorController::class, 'trash'])->middleware(['can:view-vendor-trashes']);
    Route::get('vendor/restore/{id}', [DashVendorController::class, 'restore'])->middleware(['can:restore-vendor']);
    Route::post('vendor/update-status/{id}', [DashVendorController::class, 'update_status'])->middleware(['can:update-vendor-status']);

    //////////////////////////// attributeGroups ///////////////////////////////
    Route::get('attributeGroups', [DashAttributeGroupController::class, 'index'])->middleware(['can:view-attribute-groups']);
    Route::post('attributeGroups', [DashAttributeGroupController::class, 'store'])->middleware(['can:create-attribute-groups']);
    Route::get('attributeGroups/{id}', [DashAttributeGroupController::class, 'show'])->middleware(['can:view-attribute-groups']);
    Route::delete('attributeGroups/{id}', [DashAttributeGroupController::class, 'destroy'])->middleware(['can:delete-attribute-groups']);
    Route::post('attributeGroups/{id}', [DashAttributeGroupController::class, 'update'])->middleware(['can:update-attribute-groups']);
    Route::post('attributeGroup/destroy_selected', [DashAttributeGroupController::class, 'destroy_selected'])->middleware(['can:destroy-selected-attribute-groups']);

    //////////////////////////// options ///////////////////////////////
    Route::get('options', [DashOptionController::class, 'index'])->middleware(['can:view-options']);
    Route::post('options', [DashOptionController::class, 'store'])->middleware(['can:create-options']);
    Route::get('options/{id}', [DashOptionController::class, 'show'])->middleware(['can:view-options']);
    Route::delete('options/{id}', [DashOptionController::class, 'destroy'])->middleware(['can:delete-options']);
    Route::post('options/{id}', [DashOptionController::class, 'update'])->middleware(['can:update-options']);
    Route::post('options/destroy_selected', [DashOptionController::class, 'destroy_selected'])->middleware(['can:destroy-selected-options']);
    Route::get('optionKeys', [DashIntegrationOptionKeyController::class, 'index'])->middleware(['can:create-options']);

    //////////////////////////// attributes ///////////////////////////////
    Route::get('attributes', [DashAttributeController::class, 'index'])->middleware(['can:view-attributes']);
    Route::post('attributes', [DashAttributeController::class, 'store'])->middleware(['can:create-attributes']);
    Route::get('attributes/{id}', [DashAttributeController::class, 'show'])->middleware(['can:view-attributes']);
    Route::delete('attributes/{id}', [DashAttributeController::class, 'destroy'])->middleware(['can:delete-attributes']);
    Route::post('attributes/{id}', [DashAttributeController::class, 'update'])->middleware(['can:update-attributes']);
    Route::post('attribute/destroy_selected', [DashAttributeController::class, 'destroy_selected'])->middleware(['can:destroy-selected-attributes']);

    //////////////////////////// categories ///////////////////////////////
    Route::get('categories', [DashCategoryController::class, 'index'])->middleware(['can:view-categories']);
    Route::post('categories', [DashCategoryController::class, 'store'])->middleware(['can:create-categories']);
    Route::get('categories/{id}', [DashCategoryController::class, 'show'])->middleware(['can:view-categories']);
    Route::delete('categories/{id}', [DashCategoryController::class, 'destroy'])->middleware(['can:delete-categories']);
    Route::post('categories/{id}', [DashCategoryController::class, 'update'])->middleware(['can:update-categories']);
    Route::get('category/trashes', [DashCategoryController::class, 'trash'])->name('categories.trashes')->middleware(['can:view-categories-trashes']);
    Route::get('category/restore/{id}', [DashCategoryController::class, 'restore'])->name('category.restore')->middleware(['can:restore-categories']);
    Route::get('getAllCategoriesForm', [DashCategoryController::class, 'getAllCategoriesForm'])->middleware(['can:view-all-categories-form']);
    Route::post('category/destroy_selected', [DashCategoryController::class, 'destroy_selected'])->middleware(['can:destroy-selected-categories']);
    Route::post('category/update-status/{id}', [DashCategoryController::class, 'update_status'])->middleware(['can:change-category-status']);

    //////////////////////////// products ///////////////////////////////
    Route::get('products', [DashProductController::class, 'index'])->middleware(['can:view-products']);
    Route::post('products', [DashProductController::class, 'store'])->middleware(['can:create-products']);
    Route::get('products/{id}', [DashProductController::class, 'show'])->middleware(['can:view-products']);
    Route::delete('products/{id}', [DashProductController::class, 'destroy'])->middleware(['can:delete-products']);
    Route::post('products/{id}', [DashProductController::class, 'update'])->middleware(['can:update-products']);
    Route::post('product/changeStatus/{id}', [DashProductController::class, 'changeStatus'])->name('products.changeStatus')->middleware(['can:change-product-status']);
    Route::get('product/trashes', [DashProductController::class, 'trash'])->name('products.trashes')->middleware(['can:view-products-trashes']);
    Route::get('product/restore/{id}', [DashProductController::class, 'restore'])->name('products.restore')->middleware(['can:restore-products']);
    Route::post('product/multiDelete', [DashProductController::class, 'multiDelete'])->name('products.multiDelete')->middleware(['can:destroy-selected-products']);

    Route::post('product/serials', [DashProductController::class, 'serials'])->name('products.serials')->middleware(['can:view-products-serials']);
    Route::post('product/applyPriceAll', [DashProductController::class, 'applyPriceAll'])->name('products.applyPriceAll')->middleware(['can:apply-products-price-all']);
    Route::post('product/applyPriceAllGroups', [DashProductController::class, 'applyPriceAllGroups'])->name('products.applyPriceAllGroups')->middleware(['can:apply-products-price-all-groups']);
    Route::post('product/prices', [DashProductController::class, 'prices'])->name('products.prices')->middleware(['can:view-products-prices']);
    Route::get('product/get-brand-products/{brand_id}', [DashProductController::class, 'get_brand_products'])->name('products.get_brand_products')->middleware(['can:view-products-by-brands']);
    Route::post('product/delete_image_product/{id}', [DashProductController::class, 'delete_image_product'])->name('products.delete_image_product')->middleware(['can:delete-products-images']);

    //////////////////////////// Filling Serials ///////////////////////////////
    Route::group(['prefix' => 'productSerials', 'as' => 'productSerials.'], function () {
        Route::post('manualFilling', [DashProductSerialController::class, 'manualFilling'])->name('manualFilling')->middleware(['can:manual-filling-products']);
        Route::get('stock-logs', [DashProductSerialController::class, 'stock_logs'])->name('stockLogs')->middleware(['can:stock-logs-products-serials']);
        Route::get('stock-logs-by-invoice/{invoice_id}', [DashProductSerialController::class, 'stock_logs_invoice'])->name('stock_logs_invoice')->middleware(['can:stock-logs-products-serials-invoice']);
        Route::post('stock-logs/{id}', [DashProductSerialController::class, 'update_stock_logs'])->name('updateStockLogs')->middleware(['can:update-stock-logs-products-serials']);

        //Route::post('vendorIntegrate', [DashProductSerialController::class, 'vendorIntegrate'])->name('vendorIntegrate');
        Route::post('autoFilling', [DashProductSerialController::class, 'autoFilling'])->name('autoFilling')->middleware(['can:auto-filling-products']);
        Route::post('changeStatus', [DashProductSerialController::class, 'statusInvoiceSerials'])->name('changeStatus')->middleware(['can:change-status-products-serials']);
    });

    //////////////////////////// sellers ///////////////////////////////
    Route::group(['prefix' => 'sellers', 'as' => 'sellers.'], function () {
        Route::get('', [DashSellerController::class, 'index'])->name('index')->middleware(['can:view-sellers']);
        Route::get('notApproved', [DashSellerController::class, 'notApproved'])->name('notApproved')->middleware(['can:view-not-approved-sellers']);
        Route::get('{id}', [DashSellerController::class, 'show'])->name('show')->middleware(['can:view-sellers']);
        Route::post('store', [DashSellerController::class, 'store'])->name('store')->middleware(['can:create-sellers']);
        Route::post('update/{id}', [DashSellerController::class, 'update'])->name('update')->middleware(['can:update-sellers']);
        Route::post('add-balance/{id}', [DashSellerController::class, 'add_balance'])->name('add_balance')->middleware(['can:add-balance-sellers']);
        Route::post('changeStatus/{id}', [DashSellerController::class, 'changeStatus'])->name('changeStatus')->middleware(['can:change-status-sellers']);
        Route::post('changeApprovalStatus/{id}', [DashSellerController::class, 'changeApprovalStatus'])->name('changeApprovalStatus');
        Route::delete('delete/{id}', [DashSellerController::class, 'destroy'])->name('delete')->middleware(['can:delete-sellers']);
        Route::delete('attachments/delete/{id}', [DashSellerController::class, 'deleteAttachments'])->name('attachments.delete')->middleware(['can:delete-attachments-sellers']);
        Route::get('trashes', [DashSellerController::class, 'trash'])->name('trashes')->middleware(['can:view-trashes-sellers']);
        Route::get('restore/{id}', [DashSellerController::class, 'restore'])->name('restore')->middleware(['can:restore-sellers']);
    });

    //////////////////////////// Orders ///////////////////////////////
    Route::group(['prefix' => 'orders', 'as' => 'orders.'], function () {
        Route::get('', [DashOrderController::class, 'index'])->name('index')->middleware(['can:view-orders']);
        Route::get('{id}', [DashOrderController::class, 'show'])->name('show')->middleware(['can:view-orders']);
        Route::post('store', [DashOrderController::class, 'store'])->name('store')->middleware(['can:create-orders']);
        /////
        Route::get('pullTopUpOrder/{orderId}', [DashOrderUserController::class, 'pullTopUpOrder'])->name('pullTopUpOrder')->middleware(['can:pull-top-up-order-orders']);
        Route::post('changeStatusTopUp/{orderProductId}', [DashOrderUserController::class, 'changeStatusTopUp'])->name('changeStatusTopUp')->middleware(['can:change-status-top-up-orders']);
    });

    //////////////////////////// sellerGroups ///////////////////////////////
    Route::get('sellerGroups', [DashSellerGroupController::class, 'index'])->middleware(['can:view-sellerGroups']);
    Route::post('sellerGroups', [DashSellerGroupController::class, 'store'])->middleware(['can:create-sellerGroups']);
    Route::get('sellerGroups/{id}', [DashSellerGroupController::class, 'show'])->middleware(['can:view-sellerGroups']);
    Route::delete('sellerGroups/{id}', [DashSellerGroupController::class, 'destroy'])->middleware(['can:delete-sellerGroups']);
    Route::post('sellerGroups/{id}', [DashSellerGroupController::class, 'update'])->middleware(['can:update-sellerGroups']);
    Route::get('sellerGroup/trashes', [DashSellerGroupController::class, 'trash'])->name('sellerGroup.trashes')->middleware(['can:view-trashes-sellerGroups']);
    Route::get('sellerGroup/restore/{id}', [DashSellerGroupController::class, 'restore'])->name('sellerGroup.restore')->middleware(['can:restore-sellerGroups']);
    Route::post('sellerGroup/update-status/{id}', [DashSellerGroupController::class, 'update_status'])->middleware(['can:view-sellerGroups']);
    Route::post('sellerGroup/auto-assign/{id}', [DashSellerGroupController::class, 'auto_assign'])->middleware(['can:auto-assign-sellerGroups']);
    Route::post('category/destroy_selected', [DashSellerGroupController::class, 'destroy_selected'])->middleware(['can:destroy-selected-sellerGroups']);


    //////////////////////////// sellerGroupLevels ///////////////////////////////
    Route::get('sellerGroupLevels', [DashSellerGroupLevelController::class, 'index'])->middleware(['can:view-sellerGroupLevels']);
    Route::post('sellerGroupLevels', [DashSellerGroupLevelController::class, 'store'])->middleware(['can:create-sellerGroupLevels']);
    Route::get('sellerGroupLevels/{id}', [DashSellerGroupLevelController::class, 'show'])->middleware(['can:view-sellerGroupLevels']);
    Route::delete('sellerGroupLevels/{id}', [DashSellerGroupLevelController::class, 'destroy'])->middleware(['can:delete-sellerGroupLevels']);
    Route::post('sellerGroupLevels/{id}', [DashSellerGroupLevelController::class, 'update'])->middleware(['can:update-sellerGroupLevels']);
    Route::get('sellerGroupLevel/trashes', [DashSellerGroupLevelController::class, 'trash'])->name('sellerGroupLevel.trashes')->middleware(['can:view-trashes-sellerGroupLevels']);
    Route::get('sellerGroupLevel/restore/{id}', [DashSellerGroupLevelController::class, 'restore'])->name('sellerGroupLevel.restore')->middleware(['can:restore-sellerGroupLevels']);
    Route::post('sellerGroupLevel/update-status/{id}', [DashSellerGroupLevelController::class, 'update_status'])->middleware(['can:change-status-sellerGroupLevels']);

    //////////////////////////// Settings ///////////////////////////////
    Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
        Route::get('main', [DashSettingController::class, 'mainSettings'])->name('main')->middleware(['can:view-main-settings']);
        Route::post('updateMain', [DashSettingController::class, 'updateMainSettings'])->name('update.main')->middleware(['can:update-main-settings']);
        //// Static pages
        Route::group(['prefix' => 'staticPages', 'as' => 'staticPages.'], function () {
            Route::get('', [DashStaticPageController::class, 'index'])->name('index')->middleware(['can:view-static-pages']);
            Route::get('{id}', [DashStaticPageController::class, 'show'])->name('show')->middleware(['can:view-static-pages']);
            Route::post('store', [DashStaticPageController::class, 'store'])->name('store')->middleware(['can:create-static-pages']);
            Route::post('update/{id}', [DashStaticPageController::class, 'update'])->name('update')->middleware(['can:update-static-pages']);
            Route::post('changeStatus/{id}', [DashStaticPageController::class, 'changeStatus'])->name('changeStatus')->middleware(['can:change-status-static-pages']);
            Route::delete('delete/{id}', [DashStaticPageController::class, 'delete'])->name('delete')->middleware(['can:delete-static-pages']);
        });
        //// Notifications
        Route::group(['prefix' => 'notifications', 'as' => 'notifications.'], function () {
            Route::get('', [DashNotificationSettingController::class, 'getNotificationSettings'])->name('getNotificationSettings')->middleware(['can:view-notifications']);
            Route::post('update/{id}', [DashNotificationSettingController::class, 'updateNotificationSettings'])->name('updateNotificationSettings')->middleware(['can:update-notifications']);
        });
        //// Store Appearance
        Route::group(['prefix' => 'storeAppearance', 'as' => 'storeAppearance.'], function () {
            Route::get('sliders', [DashSliderController::class, 'index'])->name('sliders.index')->middleware(['can:view-sliders']);
            Route::get('sliders/{id}', [DashSliderController::class, 'show'])->name('sliders.show')->middleware(['can:view-sliders']);
            Route::post('sliders/store', [DashSliderController::class, 'store'])->name('sliders.store')->middleware(['can:create-sliders']);
            Route::post('sliders/update/{id}', [DashSliderController::class, 'update'])->name('sliders.update')->middleware(['can:update-sliders']);
            Route::post('sliders/changeStatus/{id}', [DashSliderController::class, 'changeStatus'])->name('sliders.changeStatus')->middleware(['can:change-status-sliders']);
            Route::post('sliders/move', [DashSliderController::class, 'changeOrder'])->name('sliders.changeOrder')->middleware(['can:move-sliders']);
            Route::delete('sliders/delete/{id}', [DashSliderController::class, 'delete'])->name('sliders.delete')->middleware(['can:delete-sliders']);
            /////////
            Route::get('homeSections', [DashHomeSectionController::class, 'index'])->name('homeSections.index')->middleware(['can:view-home-sections']);
            Route::get('homeSections/{id}', [DashHomeSectionController::class, 'show'])->name('homeSections.show')->middleware(['can:view-home-sections']);
            Route::post('homeSections/store', [DashHomeSectionController::class, 'store'])->name('homeSections.store')->middleware(['can:create-home-sections']);
            Route::post('homeSections/update/{id}', [DashHomeSectionController::class, 'update'])->name('homeSections.update')->middleware(['can:update-home-sections']);
            Route::post('homeSections/changeStatus/{id}', [DashHomeSectionController::class, 'changeStatus'])->name('homeSections.changeStatus')->middleware(['can:change-status-home-sections']);
            Route::post('homeSections/move', [DashHomeSectionController::class, 'changeOrder'])->name('homeSections.changeOrder')->middleware(['can:move-home-sections']);
            Route::delete('homeSections/delete/{id}', [DashHomeSectionController::class, 'delete'])->name('homeSections.delete')->middleware(['can:delete-home-sections']);
        });
    });

    //////////////////////////// Taxes ///////////////////////////////
    Route::group(['prefix' => 'taxes', 'as' => 'taxes.'], function () {
        Route::get('', [DashValueAddedTaxController::class, 'index'])->name('index')->middleware(['can:view-taxes']);
        Route::post('store', [DashValueAddedTaxController::class, 'store'])->name('store')->middleware(['can:create-taxes']);
        Route::post('update/{id}', [DashValueAddedTaxController::class, 'update'])->name('update')->middleware(['can:update-taxes']);
        Route::post('changeStatus/{id}', [DashValueAddedTaxController::class, 'changeStatus'])->name('changeStatus')->middleware(['can:change-status-taxes']);
        Route::delete('delete/{id}', [DashValueAddedTaxController::class, 'delete'])->name('delete')->middleware(['can:delete-taxes']);
        ////
        Route::post('updatePricesDisplay', [DashValueAddedTaxController::class, 'updatePricesDisplay'])->name('updatePricesDisplay')->middleware(['can:update-prices-display']);
        Route::post('me', [DashValueAddedTaxController::class, 'updateTaxNumber'])->name('updateTaxNumber')->middleware(['can:update-tax-number']);
    });

    //////////////////////////// roles and permissions ///////////////////////////////
    Route::get('roles/permissions', [DashRoleAndPermissionController::class, 'getPermissions'])->name('roles.permissions')->middleware(['can:view-roles-permissions']);
    Route::get('roles', [DashRoleAndPermissionController::class, 'getRoles'])->name('roles')->middleware(['can:view-roles-permissions']);
    Route::get('roles/all', [DashRoleAndPermissionController::class, 'getAllRoles'])->name('roles.all')->middleware(['can:view-roles-permissions']);
    Route::get('roles/{id}', [DashRoleAndPermissionController::class, 'getOneRole'])->name('roles.getOneRole')->middleware(['can:view-roles-permissions']);
    Route::post('roles/store', [DashRoleAndPermissionController::class, 'storeRole'])->name('roles.store')->middleware(['can:create-roles']);
    Route::post('roles/update/{id}', [DashRoleAndPermissionController::class, 'updateRole'])->name('roles.update')->middleware(['can:update-roles']);
    Route::post('roles/changeStatus/{id}', [DashRoleAndPermissionController::class, 'changeStatus'])->name('roles.changeStatus')->middleware(['can:change-status-roles']);
    Route::delete('roles/delete/{id}', [DashRoleAndPermissionController::class, 'deleteRole'])->name('roles.delete')->middleware(['can:delete-roles']);

    //////////////////////////// sub admins ///////////////////////////////
    Route::get('subAdmins', [DashSubAdminController::class, 'getAdmins'])->name('subAdmins.index')->middleware(['can:view-subAdmins']);
    Route::get('subAdmins/{id}', [DashSubAdminController::class, 'getOneAdmin'])->name('subAdmins.getOneAdmin')->middleware(['can:view-subAdmins']);
    Route::post('subAdmins/store', [DashSubAdminController::class, 'storeAdmin'])->name('subAdmins.store')->middleware(['can:create-subAdmins']);
    Route::post('subAdmins/update/{id}', [DashSubAdminController::class, 'updateAdmin'])->name('subAdmins.update')->middleware(['can:update-subAdmins']);
    Route::post('subAdmins/changeStatus/{id}', [DashSubAdminController::class, 'changeStatus'])->name('subAdmins.changeStatus')->middleware(['can:change-status-subAdmins']);
    Route::delete('subAdmins/delete/{id}', [DashSubAdminController::class, 'deleteAdmin'])->name('subAdmins.delete')->middleware(['can:delete-subAdmins']);

    //////////////////////////// integrations ///////////////////////////////
    Route::group(['prefix' => 'integrations', 'as' => 'integrations.'], function () {
        Route::get('', [DashIntegrationController::class, 'index'])->name('index')->middleware(['can:view-integrations']);
        Route::post('update/{id}', [DashIntegrationController::class, 'updateIntegration'])->name('update')->middleware(['can:update-integrations']);
        Route::post('changeStatus/{id}', [DashIntegrationController::class, 'changeStatus'])->name('changeStatus')->middleware(['can:change-status-integrations']);
    });

    //////////////////////////// Vendor Products ///////////////////////////////
    Route::group(['prefix' => 'vendorProducts', 'as' => 'vendorProducts.'], function () {
        Route::get('', [DashVendorProductController::class, 'index'])->name('index')->middleware(['can:view-vendorProducts']);
        Route::post('store', [DashVendorProductController::class, 'store'])->name('store')->middleware(['can:create-vendorProducts']);
        Route::post('update/{id}', [DashVendorProductController::class, 'update'])->name('update')->middleware(['can:update-vendorProducts']);
        Route::delete('delete/{id}', [DashVendorProductController::class, 'delete'])->name('delete')->middleware(['can:delete-vendorProducts']);
        Route::post('provider-cost', [DashVendorProductController::class, 'getProviderCost'])->name('getProviderCost')->middleware(['can:create-vendorProducts']);
    });

    //////////////////////////// Direct Purchase Priorities ///////////////////////////////
    Route::group(['prefix' => 'purchasePriorities', 'as' => 'purchasePriorities.'], function () {
        Route::get('', [DashDirectPurchaseController::class, 'index'])->name('index')->middleware(['can:view-purchasePriorities']);
        Route::post('store', [DashDirectPurchaseController::class, 'store'])->name('store')->middleware(['can:create-purchasePriorities']);
        Route::post('changeStatus/{id}', [DashDirectPurchaseController::class, 'changeStatus'])->name('changeStatus')->middleware(['can:change-status-purchasePriorities']);
        Route::post('delete-vendor', [DashDirectPurchaseController::class, 'deleteVendor'])->name('deleteVendor');
    });
    /////////////////////////// banks //////////////////////////////////////////////////
    Route::group(['prefix' => 'banks'],function(){
        #get all banks
        Route::get('/',[DashBankController::class,'index']);
        #create new bank
        Route::post('/',[DashBankController::class,'store']);
        #update bank data
        Route::post('{id}/update',[DashBankController::class,'update']);
        #delete bank
        Route::delete('{id}/delete',[DashBankController::class,'destroy']);
    });
    Route::group(['prefix' => 'wallets'],function(){
        #wallet balance transactions
        Route::get('balance-transactions',[DashWalletController::class,'index']);
        #show balance transaction
        Route::get('balance-transactions/{id}/show',[DashWalletController::class,'show']);
        #change status transaction to complete
        Route::post('balance-transactions/{id}/complete',[DashWalletController::class,'complete']);
        #change status transaction to refused
        Route::post('balance-transactions/{id}/refused',[DashWalletController::class,'refused']);
    });
    #admin report
    Route::group(['prefix' => 'reports', 'as' => 'reports.'],function(){
        #product report sale
        Route::get('product-sales',[AdminReportController::class,'productReportSale'])->middleware(['can:reports-product-sales']);
        #vendor report sale
        Route::get('order-sales',[AdminReportController::class,'orderReportSale'])->middleware(['can:reports-order-sales']);
        Route::get('detailed-payments',[AdminReportController::class,'paymentsReport'])->middleware(['can:reports-detailed-payments']);
        #total payments report
        Route::get('total-payments',[AdminReportController::class,'totalPaymentReport'])->middleware(['can:reports-total-payments']);
    });
    #get all seller complaints
    Route::get('complaints',[SellerComplaintsController::class,'index']);
    #change complaints status
    Route::post('complaints/changeStatus/{id}',[SellerComplaintsController::class,'changeStatus']);
});



Route::group(['middleware' => ['authGurad:sellerApi', 'getLang', 'checkSellerStatus'],'prefix' => 'seller', 'as' => 'api.seller.'], function () {

    //////////////////////////// Auth ///////////////////////////////
    #homesection
    Route::get('dashboard',[DashboardController::class,'index'])->middleware(['checkSellerStatus','can:home-section']);
    Route::get('currencies',[SellerCurrencyController::class,'index']);
    #store default currencies
    Route::post('currencies/storeDefault', [SellerCurrencyController::class, 'storeDefault'])->name('currencies.storeDefault');
//    #generateG2FAuth
//    Route::get('generateG2FAuth', [SellerAuthController::class, 'generateG2FAuth']);

    #Tokens like ( Firebase )
    Route::group(['prefix' => 'notificationTokens', 'as' => 'notificationTokens.'], function () {
        Route::post('firebase/store', [SellerNotificationTokenController::class, 'firebaseStore'])->name('firebaseStore');
    });

    #Notifications
    Route::group(['prefix' => 'notifications', 'as' => 'notifications.'], function () {
        Route::get('', [SellerNotificationController::class, 'index'])->name('index');
        Route::get('count', [SellerNotificationController::class, 'count'])->name('count');
        Route::get('read/{notificationId}', [SellerNotificationController::class, 'read'])->name('read');
    });
    #verify
    Route::get('verify/2fa', [SellerAuthController::class, 'verify2Fa']);
    #update profile data
    Route::post('update/profile-data',[SellerAuthController::class,'updateProfile']);
    #Balance-recharge
    Route::post('balance-recharge',[WalletController::class,'balanceRecharge'])->middleware(['can:wallet-add-balance']);
    #get all transaction for balance recharge
    Route::get('wallet-transactions',[WalletController::class,'getBalanceList'])->middleware(['can:wallet-balance-list']);
    #banks
    Route::group(['prefix' => 'banks'],function(){
        #get all banks api
        Route::get('/',[BankController::class,'index']);
        #show bank details api
        Route::get('{id}',[BankController::class,'show']);
    });
    #products
    Route::get('products',[ProductController::class,'index'])->middleware(['can:products-list']);
    #search in products
    Route::get('search-products',[ProductController::class,'search'])->middleware([]);
    #store apis
    Route::group(['prefix' => 'store','middleware' => ['can:store']],function(){
        #get all brands
        Route::get('brands',[BrandController::class,'getBrands']);
        #get categories api
        Route::get('categories',[CategoryController::class,'index']);
        #get Main categories api
        Route::get('categories/main',[CategoryController::class,'getMainCategories']);
        #get all category that as brands
        Route::get('categories/subs',[CategoryController::class,'getSubCategories']);
        #get products with that category
        Route::get('products/category/{category_id}',[ProductController::class,'productsByCategoryId']);
        #get product details with category_id
        Route::get('products/{productId}/category/{categoryId}', [ProductController::class, 'productByCategoryId']);
        # check options account
        Route::post('products/check-options-account', [ProductController::class, 'checkOptionsAccount']);
        Route::group(['prefix' => 'favorites'],function(){
            #add-product-to-fav
            Route::post('add-product-to-fav',[FavController::class,'store'])->middleware('can:add-product-fav');
            #get Fav Products
            Route::get('get/products',[FavController::class,'getProducts'])->middleware('can:favorites-products-list');
            #delete product from fav
            Route::delete('delete/product/{product_id}/category/{category_id}',[FavController::class,'destroy'])->middleware('can:delete-product-fav');
        });
        Route::group(['prefix' => 'cart'],function(){
            #add product to cart
            Route::post('add-product-to-cart',[CartController::class,'store']);
            #get cart
            Route::get('get',[CartController::class,'index']);
            #delete product from cart
            Route::delete('deleteProduct/{id}/category/{category_id}', [CartController::class, 'deleteProduct']);
            #delete all products from cart
            Route::delete('deleteAll', [CartController::class, 'deleteAll']);
        });
        #create Order api
        Route::post('create-order',[OrderController::class,'store'])->middleware(['can:add-order']);
        #get Orders api
        Route::get('orders',[OrderController::class,'index'])->middleware(['can:orders-list']);
        #order details
        Route::get('orders/{id}',[OrderController::class,'show'])->middleware(['can:view-order']);
    });
    Route::group(['prefix' => 'support-tickets'],function(){
        #get Support Tickets
        Route::get('/',[SupportTicketController::class,'index'])->middleware('can:tickets-list');
        #add support ticket
        Route::post('store',[SupportTicketController::class,'store'])->middleware('can:add-ticket');
        #get replies for ticket
        Route::get('{id}/replies',[SupportTicketController::class,'getReplies'])->middleware('can:view-ticket');
    });
    #admin list routes
    Route::group(['prefix' => 'admin-list'],function()
    {
        Route::get('/',[AdminListController::class,'index'])->middleware('can:setting-admin-list');
        #store admin
        Route::post('/',[AdminListController::class,'store'])->middleware('can:setting-add-admin-list');
        #show admin
        Route::get('/{id}/show',[AdminListController::class,'show']);
        #update
        Route::post('{id}/update',[AdminListController::class,'update'])->middleware('can:setting-edit-admin-list');
        #delete
        Route::delete('{id}/delete',[AdminListController::class,'destroy'])->middleware('can:setting-delete-admin-list');
        #update status
        Route::put('{id}/update-status',[AdminListController::class,'updateStatus'])->middleware('can:setting-change-status-admin-list');
    });
    Route::group(['prefix' => 'roles'],function(){
        Route::get('/',[RoleController::class,'index'])->middleware('can:setting-roles-list');
        #create role
        Route::post('/',[RoleController::class,'store'])->middleware('can:setting-add-role');
        #role details
        Route::get('/{id}',[RoleController::class,'show'])->middleware('can:setting-show-role');
        #update role
        Route::post('{id}/update',[RoleController::class,'update'])->middleware('can:setting-edit-role');
        #delete role
        Route::delete('{id}/delete',[RoleController::class,'destroy'])->middleware('can:setting-delete-role');
        #update status
        Route::post('{id}/change-status',[RoleController::class,'changeStatus'])->middleware('can:setting-change-status-role');
    });
    Route::group(['prefix' => 'permissions'],function(){
        Route::get('/',[PermissionController::class,'index']);
    });
    #settings
    Route::get('main-settings',[SettingController::class,'mainSetting']);
    #currencies
    Route::get('currencies',[CurrencyController::class,'index']);
    #get order ids
    Route::get('order-ids',[OrderController::class,'orderIds']);
    #create report for products
    Route::get('products-report',[ReportController::class,'reportProducts']);
    #create report for order products report
    Route::get('order-products-report',[ReportController::class,'orderProductsReport']);
    #create orders report
    Route::get('orders-report',[ReportController::class,'orderReport']);
});
Route::group(['middleware' => ['authGurad:sellerApi', 'getLang'],'prefix' => 'seller', 'as' => 'api.seller.'], function () {
    #profile route seller
    Route::get('profile',[SellerAuthController::class,'profile']);
    #logout route seller
    Route::post('logout',[SellerAuthController::class,'logout']);
    Route::group(['prefix' => 'attachment'],function(){
        #show attachment
        Route::get('show/{id}',[AttachmentController::class,'show']);
        #delete attachment
        Route::delete('delete/{id}',[AttachmentController::class,'destroy']);
    });
    #update profile data
    Route::post('update/profile-data',[SellerAuthController::class,'updateProfile']);
});
Route::group(['middleware' => ['getLang'],'prefix' => 'seller', 'as' => 'api.seller.'], function () {

    //////////////////////////// Auth ///////////////////////////////
    Route::post('verify-2FAuth', action: [SellerAuthController::class, 'verifyG2FAuth']);
    Route::post('register', [SellerAuthController::class, 'register']);
    Route::post('verifyOtp', [SmsVerificationController::class, 'verifyOtp'])->name('auth.smsVerification.verifyOtp');
    Route::post('resendOtp', [SmsVerificationController::class, 'resendOtp'])->name('auth.smsVerification.resendOtp');
    Route::post('login', [SellerAuthController::class, 'login']);
    Route::post('password/forgot', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('password/reset', [ForgotPasswordController::class, 'resetPassword']);
    #country api
    Route::get('countries',[CountryController::class,'index']);
    #city route api
    Route::get('cities',[CityController::class,'index']);
    #get cities by region id
    Route::get('cities/{region_id}',[CityController::class,'getCitiesByRegion']);
    #region api
    Route::get('regions',[RegionController::class,'index']);
    #get regions by country id
    Route::get('regions/{country_id}',[RegionController::class,'getRegionsByCountry']);
});






////////////////////////////// just for test /////////////////////////
Route::group(['middleware' => ['authGurad:adminApi']], function () {
    Route::get('test-integration',[\App\Http\Controllers\Seller\TestIntegrationController::class,'test']);
});





