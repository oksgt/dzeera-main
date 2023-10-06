<?php

use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Auth\LoginController;
// use App\Http\Controllers\VoucherController;
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

Route::get('/removeCookie/{cookiename}', [CartController::class, 'removeCookie'])->name('removeCookie');
Route::get('/printCartFromCookie', [CartController::class, 'printCartFromCookie'])->name('printCartFromCookie');

Route::get('/checkout/', [CheckoutController::class, 'checkout'])->name('checkout');
Route::get('/getCities/{province_id}', [CheckoutController::class, 'getCities'])->name('getCities');
Route::post('/ongkir/', [CheckoutController::class, 'check_ongkir'])->name('check_ongkir');
Route::post('/checkout/next', [CheckoutController::class, 'checkout_next'])->name('checkout.next');

Route::post('/checkout/finish', [CheckoutController::class, 'checkout_finish'])->name('checkout.finish');
Route::get('/finish/{code}', [CheckoutController::class, 'finish'])->name('finish');



Route::get('/getCityName/{provinceId}/{cityId}', [CheckoutController::class, 'getCityName'])->name('getCityName');
Route::get('/getProvinceName/{id}', [CheckoutController::class, 'getProvinceName'])->name('getProvinceName');
Route::get('/vouchers/{code}', [CheckoutController::class, 'getVouchersByCode'])->name('getVouchersByCode');

Route::post('/store-session', [CheckoutController::class, 'store_voucher']);
Route::get('/print-sessions', [CheckoutController::class, 'printSessions'])->name('printSessions');
Route::get('/remove-voucher', [CheckoutController::class, 'removeVoucher'])->name('remove.voucher');

Route::get('/search', [HomeController::class, 'search'])->name('search');

Route::get('/product/{productslug}', [HomeController::class, 'product'])->name('product');

Route::get('/{brandslug?}', [HomeController::class, 'index'])->name('home');
Route::get('/{brandslug?}/new-arrivals/', [HomeController::class, 'newArrivals'])->name('newArrivals');
Route::get('/{brandslug?}/all-products/', [HomeController::class, 'allProducts'])->name('allProducts');
Route::get('/{brandslug?}/{categoryslug?}/', [HomeController::class, 'ProductByCategory'])->name('ProductByCategory');

// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'getWishlist']);
    Route::post('/wishlist/sync', [WishlistController::class, 'syncWishlist']);
});
