<?php

use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/page/{page:slug}', [PageController::class, 'show'])->name('page.show');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Public profile routes
Route::get('/profile/{user:username}', [ProfileController::class, 'show'])->name('profile.show');

// Social actions (AJAX)
Route::middleware('auth')->group(function () {
    Route::post('/users/{user}/follow', [ProfileController::class, 'follow'])->name('users.follow');
    Route::delete('/users/{user}/follow', [ProfileController::class, 'unfollow'])->name('users.unfollow');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
