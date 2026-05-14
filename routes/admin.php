<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('dashboard', App\Http\Controllers\Admin\Dashboard::class)->name('dashboard');
    Route::get('dashboard/accounting', App\Http\Controllers\Admin\DashboardAccounting::class)->name('dashboard.accounting');

    Route::prefix('accounting')->name('accounting.')->group(function () {
        Route::get('professional', [App\Http\Controllers\Admin\Accounting\AccountingProfessionalController::class, 'index'])->name('professional.index');
        Route::get('professional/export', [App\Http\Controllers\Admin\Accounting\AccountingProfessionalController::class, 'export'])->name('professional.export');
        Route::get('site-sales', [App\Http\Controllers\Admin\Accounting\AccountingSiteSalesController::class, 'index'])->name('site-sales.index');
        Route::get('site-sales/export', [App\Http\Controllers\Admin\Accounting\AccountingSiteSalesController::class, 'export'])->name('site-sales.export');
    });

    Route::resource('category', App\Http\Controllers\Admin\Products\CategoryController::class);
});
