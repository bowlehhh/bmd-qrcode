<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::get('/aset/{asset:asset_code}', [AssetController::class, 'publicShow'])->name('assets.public.show');
Route::get('/aset/{asset:asset_code}/lookup', [AssetController::class, 'publicLookup'])->name('assets.public.lookup');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/assets/selection', [AssetController::class, 'selection'])->name('assets.selection');
    Route::post('/assets/print/bulk', [AssetController::class, 'bulkPrint'])->name('assets.print.bulk');
    Route::get('/assets/{asset:asset_code}/print', [AssetController::class, 'print'])->name('assets.print');
    Route::get('/assets/{asset:asset_code}/download', [AssetController::class, 'download'])->name('assets.download');
    Route::resource('assets', AssetController::class)->parameters(['assets' => 'asset']);
});
