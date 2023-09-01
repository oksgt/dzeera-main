<?php

use App\Http\Controllers\WishlistController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/auth/google', [LoginController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [LoginController::class, 'handleGoogleCallback']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/signout', [LoginController::class, 'logout'])->name('signout');

Route::get('lang/{locale}', [App\Http\Controllers\LocalizationController::class , 'lang'])->name('lang');

Route::get('/wishlist', [WishlistController::class, 'getWishlist'])->name('wishlist.index');
Route::get('/search', [HomeController::class, 'search'])->name('search');

Route::get('/{brandslug?}', [HomeController::class, 'index'])->name('home');
Route::get('/{brandslug?}/new-arrivals/', [HomeController::class, 'newArrivals'])->name('newArrivals');



// Public routes

Route::post('/wishlist', [WishlistController::class, 'addToWishlist']);

// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'getWishlist']);
    Route::post('/wishlist/sync', [WishlistController::class, 'syncWishlist']);
});
