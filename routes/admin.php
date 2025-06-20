<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PluginController;
use App\Http\Controllers\Admin\ThemeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SocialController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Plugin Management
    Route::prefix('plugins')->name('plugins.')->group(function () {
        Route::get('/', [PluginController::class, 'index'])->name('index');
        Route::get('/upload', [PluginController::class, 'upload'])->name('upload');
        Route::post('/', [PluginController::class, 'store'])->name('store');
        Route::post('/{plugin}/activate', [PluginController::class, 'activate'])->name('activate');
        Route::post('/{plugin}/deactivate', [PluginController::class, 'deactivate'])->name('deactivate');
        Route::delete('/{plugin}', [PluginController::class, 'destroy'])->name('destroy');
    });
    
    // Theme Management
    Route::prefix('themes')->name('themes.')->group(function () {
        Route::get('/', [ThemeController::class, 'index'])->name('index');
        Route::get('/upload', [ThemeController::class, 'upload'])->name('upload');
        Route::post('/', [ThemeController::class, 'store'])->name('store');
        Route::post('/{theme}/activate', [ThemeController::class, 'activate'])->name('activate');
        Route::delete('/{theme}', [ThemeController::class, 'destroy'])->name('destroy');
    });
    
    // User Management
    Route::resource('users', UserController::class);
    
    // Social Networking Management
    Route::prefix('social')->name('social.')->group(function () {
        Route::get('/users', [SocialController::class, 'users'])->name('users');
        Route::get('/posts', [SocialController::class, 'posts'])->name('posts');
        Route::get('/activities', [SocialController::class, 'activities'])->name('activities');
        Route::get('/relationships', [SocialController::class, 'relationships'])->name('relationships');
        Route::patch('/posts/{post}/moderate', [SocialController::class, 'moderatePost'])->name('posts.moderate');
    });
});
