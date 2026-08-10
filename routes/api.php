<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\DiscountController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProvinceController;
use App\Http\Controllers\Api\ShippingController;
use App\Http\Controllers\Api\TypeOfWeightController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Ani-Roz Customer Frontend
|--------------------------------------------------------------------------
|
| All routes are prefixed with /api/v1 via RouteServiceProvider or direct prefix.
| Public routes require no authentication.
| Protected routes require a valid Sanctum token (Bearer token).
|
*/

Route::prefix('v1')->group(function () {

    // ─── Public Routes ───────────────────────────────────────────────
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{category}', [CategoryController::class, 'show']);

    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{product}', [ProductController::class, 'show']);

    Route::get('type-of-weights', [TypeOfWeightController::class, 'index']);

    Route::get('provinces', [ProvinceController::class, 'provinces']);
    Route::get('provinces/{province}/cities', [ProvinceController::class, 'cities']);

    Route::get('shipping-methods', [ShippingController::class, 'methods']);

    Route::get('articles', [ArticleController::class, 'index']);
    Route::get('articles/{article}', [ArticleController::class, 'show']);

    Route::get('contact-settings', [ContactController::class, 'index']);

    // ─── Authentication (Mobile + OTP) ──────────────────────────────
    Route::post('auth/send-otp', [AuthController::class, 'sendOtp']);
    Route::post('auth/verify-otp', [AuthController::class, 'verifyOtp']);

    // ─── Cart Routes (Session-based, no DB storage) ────────────────
    Route::middleware([
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    ])->prefix('cart')->group(function () {

        Route::get('/', [CartController::class, 'index']);
        Route::get('count', [CartController::class, 'itemCount']);
        Route::post('items', [CartController::class, 'addItem']);
        Route::put('items/{itemId}', [CartController::class, 'updateItem']);
        Route::delete('items/{itemId}', [CartController::class, 'removeItem']);
        Route::delete('/', [CartController::class, 'clear']);
        Route::post('apply-discount', [CartController::class, 'applyDiscount']);
        Route::post('remove-discount', [CartController::class, 'removeDiscount']);
        Route::post('select-shipping', [CartController::class, 'selectShipping']);
        Route::post('sync', [CartController::class, 'sync']);

        // Checkout requires authentication
        Route::middleware('auth:sanctum')->post('checkout', [CartController::class, 'checkout']);
    });

    // ─── Protected Routes (Sanctum) ─────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('user/profile', [AuthController::class, 'profile']);
        Route::put('user/profile', [AuthController::class, 'updateProfile']);

        Route::get('user/addresses', [AddressController::class, 'index']);
        Route::post('user/addresses', [AddressController::class, 'store']);
        Route::put('user/addresses/{id}', [AddressController::class, 'update']);
        Route::delete('user/addresses/{id}', [AddressController::class, 'destroy']);
        Route::post('user/addresses/{id}/default', [AddressController::class, 'setDefault']);

        Route::get('user/orders', [OrderController::class, 'index']);
        Route::get('user/orders/{order}', [OrderController::class, 'show']);
        Route::post('user/orders', [OrderController::class, 'store']);
        Route::post('user/orders/{order}/cancel', [OrderController::class, 'cancel']);

        Route::post('discount/validate', [DiscountController::class, 'validateCode']);
    });
});
