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

    Route::get('orders/supply', [App\Http\Controllers\Admin\Orders\OrderController::class, 'supplyIndex'])->name('orders.supply');
    Route::get('orders', [App\Http\Controllers\Admin\Orders\OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [App\Http\Controllers\Admin\Orders\OrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [App\Http\Controllers\Admin\Orders\OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::patch('orders/{order}/send-to-supply', [App\Http\Controllers\Admin\Orders\OrderController::class, 'sendToSupply'])->name('orders.send-to-supply');
});
