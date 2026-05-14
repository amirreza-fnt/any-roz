<?php

use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('dashboard', App\Http\Controllers\Admin\Dashboard::class)->name('dashboard');
    Route::get('dashboard/accounting', App\Http\Controllers\Admin\DashboardAccounting::class)->name('dashboard.accounting');
    Route::get('dashboard/supply', App\Http\Controllers\Admin\DashboardSupply::class)->name('dashboard.supply');
    Route::get('dashboard/marketing', App\Http\Controllers\Admin\Marketing\MarketingPanelController::class)->name('dashboard.marketing');

    Route::prefix('marketing')->name('marketing.')->group(function () {
        Route::get('api/products', [App\Http\Controllers\Admin\Marketing\MarketingLookupController::class, 'products'])->name('api.products');
        Route::get('api/products/{product}', [App\Http\Controllers\Admin\Marketing\MarketingLookupController::class, 'productJson'])->name('api.products.show');
        Route::get('api/buyers', [App\Http\Controllers\Admin\Marketing\MarketingLookupController::class, 'buyers'])->name('api.buyers');
        Route::get('api/buyers/{buyer}', [App\Http\Controllers\Admin\Marketing\MarketingLookupController::class, 'buyerJson'])->name('api.buyers.show');
        Route::get('api/provinces', [App\Http\Controllers\Admin\Marketing\MarketingLookupController::class, 'provinces'])->name('api.provinces');
        Route::get('api/cities', [App\Http\Controllers\Admin\Marketing\MarketingLookupController::class, 'cities'])->name('api.cities');
        Route::resource('buyers', App\Http\Controllers\Admin\Marketing\MarketingBuyerController::class);
        Route::resource('sales', App\Http\Controllers\Admin\Marketing\MarketingSaleController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    });

    Route::prefix('accounting')->name('accounting.')->group(function () {
        Route::get('marketing-sales', [App\Http\Controllers\Admin\Accounting\MarketingSaleReviewController::class, 'index'])->name('marketing-sales.index');
        Route::get('marketing-sales/{marketing_sale}', [App\Http\Controllers\Admin\Accounting\MarketingSaleReviewController::class, 'show'])->name('marketing-sales.show');
        Route::patch('marketing-sales/{marketing_sale}/approve', [App\Http\Controllers\Admin\Accounting\MarketingSaleReviewController::class, 'approve'])->name('marketing-sales.approve');
        Route::patch('marketing-sales/{marketing_sale}/reject', [App\Http\Controllers\Admin\Accounting\MarketingSaleReviewController::class, 'reject'])->name('marketing-sales.reject');
    });

    Route::patch('category/{category}/toggle-status', [App\Http\Controllers\Admin\Products\CategoryController::class, 'toggleStatus'])
        ->name('category.toggle-status');
    Route::resource('category', App\Http\Controllers\Admin\Products\CategoryController::class);

    Route::resource('type-of-weights', App\Http\Controllers\Admin\Products\TypeOfWeightController::class);

    Route::patch('products/{product}/toggle-status', [App\Http\Controllers\Admin\Products\ProductController::class, 'toggleStatus'])
        ->name('products.toggle-status');
    Route::patch('products/{product}/toggle-suggested', [App\Http\Controllers\Admin\Products\ProductController::class, 'toggleSuggested'])
        ->name('products.toggle-suggested');
    Route::post('products/{product}/images', [App\Http\Controllers\Admin\Products\ProductController::class, 'storeImage'])
        ->name('products.images.store');
    Route::delete('products/{product}/images/{product_image}', [App\Http\Controllers\Admin\Products\ProductController::class, 'destroyImage'])
        ->name('products.images.destroy');
    Route::resource('products', App\Http\Controllers\Admin\Products\ProductController::class);

    Route::get('orders/supply/{order}', [App\Http\Controllers\Admin\Orders\OrderController::class, 'showSupply'])->name('orders.supply.show');
    Route::get('orders/supply', [App\Http\Controllers\Admin\Orders\OrderController::class, 'supplyIndex'])->name('orders.supply');
    Route::get('orders', [App\Http\Controllers\Admin\Orders\OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [App\Http\Controllers\Admin\Orders\OrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [App\Http\Controllers\Admin\Orders\OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::patch('orders/{order}/toggle-supply', [App\Http\Controllers\Admin\Orders\OrderController::class, 'toggleSupply'])->name('orders.toggle-supply');

    Route::patch('articles/{article}/toggle-publish', [App\Http\Controllers\Admin\Articles\ArticleController::class, 'togglePublish'])
        ->name('articles.toggle-publish');
    Route::resource('articles', App\Http\Controllers\Admin\Articles\ArticleController::class);

    Route::patch('gift-codes/{gift_code}/toggle-status', [App\Http\Controllers\Admin\Marketing\GiftCodeController::class, 'toggleStatus'])
        ->name('gift-codes.toggle-status');
    Route::resource('gift-codes', App\Http\Controllers\Admin\Marketing\GiftCodeController::class);

    Route::patch('discount-codes/{discount_code}/toggle-status', [App\Http\Controllers\Admin\Marketing\DiscountCodeController::class, 'toggleStatus'])
        ->name('discount-codes.toggle-status');
    Route::resource('discount-codes', App\Http\Controllers\Admin\Marketing\DiscountCodeController::class);

    Route::get('contact-settings', [App\Http\Controllers\Admin\Settings\ContactSettingsController::class, 'edit'])->name('contact-settings.edit');
    Route::put('contact-settings', [App\Http\Controllers\Admin\Settings\ContactSettingsController::class, 'update'])->name('contact-settings.update');

    Route::patch('shipping-configs/{shipping_config}/toggle-status', [App\Http\Controllers\Admin\Shipping\ShippingConfigController::class, 'toggleStatus'])
        ->name('shipping-configs.toggle-status');
    Route::resource('shipping-configs', App\Http\Controllers\Admin\Shipping\ShippingConfigController::class)->except(['destroy']);

    Route::get('technical-backup', [App\Http\Controllers\Admin\Tools\TechnicalBackupController::class, 'index'])->name('technical-backup.index');
    Route::post('technical-backup/download', [App\Http\Controllers\Admin\Tools\TechnicalBackupController::class, 'download'])->name('technical-backup.download');

    Route::resource('users', App\Http\Controllers\Admin\Users\UserController::class);
});
