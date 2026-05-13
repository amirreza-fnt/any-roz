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
});
