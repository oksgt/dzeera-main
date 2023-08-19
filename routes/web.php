<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('lang/{locale}', [App\Http\Controllers\LocalizationController::class , 'lang']);

// Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/{brandslug?}', [HomeController::class, 'index'])->name('home');
