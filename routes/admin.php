<?php

use App\Http\Controllers\Admin\AdminManagerController;
use App\Http\Controllers\Admin\Accounting\AccountingJournalEntryController;
use App\Http\Controllers\Admin\Accounting\AccountingProfessionalController;
use App\Http\Controllers\Admin\Accounting\AccountingSiteSalesController;
use App\Http\Controllers\Admin\Accounting\MarketingSaleReviewController;
use App\Http\Controllers\Admin\Articles\ArticleController;
use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Admin\Dashboard;
use App\Http\Controllers\Admin\DashboardAccounting;
use App\Http\Controllers\Admin\DashboardSupply;
use App\Http\Controllers\Admin\Marketing\GiftCodeController;
use App\Http\Controllers\Admin\Marketing\DiscountCodeController;
use App\Http\Controllers\Admin\Marketing\MarketingBuyerController;
use App\Http\Controllers\Admin\Marketing\MarketingLookupController;
use App\Http\Controllers\Admin\Marketing\MarketingPanelController;
use App\Http\Controllers\Admin\Marketing\MarketingSaleController;
use App\Http\Controllers\Admin\Orders\OrderController;
use App\Http\Controllers\Admin\Products\CategoryController;
use App\Http\Controllers\Admin\Products\ProductController;
use App\Http\Controllers\Admin\Products\TypeOfWeightController;
use App\Http\Controllers\Admin\Settings\ContactSettingsController;
use App\Http\Controllers\Admin\Shipping\ShippingConfigController;
use App\Http\Controllers\Admin\Tools\TechnicalBackupController;
use App\Http\Controllers\Admin\Users\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminLoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AdminLoginController::class, 'login']);
    });

    Route::middleware('auth:admin')->group(function () {
        Route::middleware('admin.super')->prefix('managers')->name('managers.')->group(function () {
            Route::get('/', [AdminManagerController::class, 'index'])->name('index');
            Route::get('create', [AdminManagerController::class, 'create'])->name('create');
            Route::post('/', [AdminManagerController::class, 'store'])->name('store');
            Route::get('{manager}/edit', [AdminManagerController::class, 'edit'])->name('edit');
            Route::put('{manager}', [AdminManagerController::class, 'update'])->name('update');
            Route::post('{manager}/toggle-active', [AdminManagerController::class, 'toggleActive'])->name('toggle-active');
        });

        Route::middleware('admin.permission')->group(function () {
            Route::post('logout', [AdminLoginController::class, 'logout'])->name('logout');

            Route::get('dashboard', Dashboard::class)->name('dashboard');
            Route::get('dashboard/accounting', DashboardAccounting::class)->name('dashboard.accounting');
            Route::get('dashboard/supply', DashboardSupply::class)->name('dashboard.supply');
            Route::get('dashboard/marketing', MarketingPanelController::class)->name('dashboard.marketing');

            Route::prefix('accounting')->name('accounting.')->group(function () {
                Route::get('professional', [AccountingProfessionalController::class, 'index'])->name('professional.index');
                Route::get('professional/export', [AccountingProfessionalController::class, 'export'])->name('professional.export');
                Route::resource('journal', AccountingJournalEntryController::class)->except(['show']);
                Route::get('site-sales', [AccountingSiteSalesController::class, 'index'])->name('site-sales.index');
                Route::get('site-sales/export', [AccountingSiteSalesController::class, 'export'])->name('site-sales.export');

                Route::get('marketing-sales', [MarketingSaleReviewController::class, 'index'])->name('marketing-sales.index');
                Route::get('marketing-sales/{marketing_sale}', [MarketingSaleReviewController::class, 'show'])->name('marketing-sales.show');
                Route::post('marketing-sales/{marketing_sale}/approve', [MarketingSaleReviewController::class, 'approve'])->name('marketing-sales.approve');
                Route::post('marketing-sales/{marketing_sale}/reject', [MarketingSaleReviewController::class, 'reject'])->name('marketing-sales.reject');
            });

            Route::get('orders/supply', [OrderController::class, 'supplyIndex'])->name('orders.supply');
            Route::get('orders/supply/{order}', [OrderController::class, 'showSupply'])->name('orders.supply.show');
            Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
            Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
            Route::post('orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
            Route::post('orders/{order}/toggle-supply', [OrderController::class, 'toggleSupply'])->name('orders.toggle-supply');

            Route::resource('category', CategoryController::class);
            Route::post('category/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('category.toggle-status');

            Route::resource('type-of-weights', TypeOfWeightController::class);

            Route::resource('products', ProductController::class);
            Route::post('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
            Route::post('products/{product}/toggle-suggested', [ProductController::class, 'toggleSuggested'])->name('products.toggle-suggested');
            Route::post('products/{product}/images', [ProductController::class, 'storeImage'])->name('products.images.store');
            Route::delete('products/{product}/images/{product_image}', [ProductController::class, 'destroyImage'])->name('products.images.destroy');

            Route::resource('articles', ArticleController::class);
            Route::post('articles/{article}/toggle-publish', [ArticleController::class, 'togglePublish'])->name('articles.toggle-publish');

            Route::resource('gift-codes', GiftCodeController::class);
            Route::post('gift-codes/{gift_code}/toggle-status', [GiftCodeController::class, 'toggleStatus'])->name('gift-codes.toggle-status');

            Route::resource('discount-codes', DiscountCodeController::class);
            Route::post('discount-codes/{discount_code}/toggle-status', [DiscountCodeController::class, 'toggleStatus'])->name('discount-codes.toggle-status');

            Route::get('contact-settings/edit', [ContactSettingsController::class, 'edit'])->name('contact-settings.edit');
            Route::post('contact-settings', [ContactSettingsController::class, 'update'])->name('contact-settings.update');

            Route::resource('shipping-configs', ShippingConfigController::class)->except(['destroy']);
            Route::post('shipping-configs/{shipping_config}/toggle-status', [ShippingConfigController::class, 'toggleStatus'])->name('shipping-configs.toggle-status');

            Route::prefix('technical-backup')->name('technical-backup.')->group(function () {
                Route::get('/', [TechnicalBackupController::class, 'index'])->name('index');
                Route::post('download', [TechnicalBackupController::class, 'download'])->name('download');
                Route::get('progress/{token}', [TechnicalBackupController::class, 'progress'])->name('progress');
                Route::get('file/{token}/{index}', [TechnicalBackupController::class, 'file'])->name('file');
            });

            Route::resource('users', UserController::class);
            Route::post('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');

            Route::prefix('marketing')->name('marketing.')->group(function () {
                Route::prefix('api')->name('api.')->group(function () {
                    Route::get('products', [MarketingLookupController::class, 'products'])->name('products');
                    Route::get('products/{product}', [MarketingLookupController::class, 'productJson'])->name('products.show');
                    Route::get('buyers', [MarketingLookupController::class, 'buyers'])->name('buyers');
                    Route::get('buyers/{buyer}', [MarketingLookupController::class, 'buyerJson'])->name('buyers.show');
                    Route::get('provinces', [MarketingLookupController::class, 'provinces'])->name('provinces');
                    Route::get('cities', [MarketingLookupController::class, 'cities'])->name('cities');
                    Route::get('reverse-geocode', [MarketingLookupController::class, 'reverseGeocode'])->name('reverse-geocode');
                });

                Route::resource('buyers', MarketingBuyerController::class);
                Route::resource('sales', MarketingSaleController::class);
            });
        });
    });
});
