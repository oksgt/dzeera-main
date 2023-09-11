<?php

use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/auth/google', [LoginController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [LoginController::class, 'handleGoogleCallback']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/signout', [LoginController::class, 'logout'])->name('signout');

Route::get('lang/{locale}', [App\Http\Controllers\LocalizationController::class , 'lang'])->name('lang');

Route::get('/wishlist/show', [WishlistController::class, 'getWishlist'])->name('wishlist.index');
Route::post('/wishlist', [WishlistController::class, 'addToWishlist']);

Route::get('/cart/show', [CartController::class, 'getCart'])->name('cart.index');
Route::post('/cart', [CartController::class, 'addToCart']);
Route::post('/cart/remove', [CartController::class, 'removeCart'])->name('removecart');
Route::post('/cart/update', [CartController::class, 'updateCart'])->name('updateCart');

Route::get('/checkout/', [CheckoutController::class, 'checkout'])->name('checkout');
Route::get('/getCities/{province_id}', [CheckoutController::class, 'getCities'])->name('getCities');
Route::post('/ongkir/', [CheckoutController::class, 'check_ongkir'])->name('check_ongkir');

// Route::post('/ongkir', 'CheckOngkirController@check_ongkir');
// Route::get('/cities/{province_id}', 'CheckOngkirController@getCities');

Route::get('/search', [HomeController::class, 'search'])->name('search');

Route::get('/product/{productslug}', [HomeController::class, 'product'])->name('product');

Route::get('/{brandslug?}', [HomeController::class, 'index'])->name('home');
Route::get('/{brandslug?}/new-arrivals/', [HomeController::class, 'newArrivals'])->name('newArrivals');
Route::get('/{brandslug?}/all-products/', [HomeController::class, 'allProducts'])->name('allProducts');
Route::get('/{brandslug?}/{categoryslug?}/', [HomeController::class, 'ProductByCategory'])->name('ProductByCategory');

// Public routes

// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'getWishlist']);
    Route::post('/wishlist/sync', [WishlistController::class, 'syncWishlist']);
});
