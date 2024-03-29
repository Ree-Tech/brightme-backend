<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\SurveyController;
use App\Http\Controllers\Api\ArtisanController;
use App\Http\Controllers\Api\DetectionController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\GlowUpPlanController;
use App\Http\Controllers\Api\LikedProductController;
use App\Http\Controllers\Api\ProductCategoryController;
use App\Http\Controllers\Api\ProductVariationController;

Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
    Route::get('/google', [AuthController::class, 'redirectToGoogle'])->middleware('guest')->name('google.login');
    Route::get('/google/callback', [AuthController::class, 'handleGoogleCallback'])->middleware('guest')->name('google.callback');
    Route::post('/firebase', [AuthController::class, 'firebase'])->middleware('guest')->name('google.firebase');
    Route::post('/confirm', [AuthController::class, 'confirmCode'])->middleware('guest')->name('confirm');
    Route::post('/forgot', [AuthController::class, 'sendEmailForgot'])->middleware('guest')->name('forgot');
    Route::post('/forgot/submit', [AuthController::class, 'confirmCode'])->middleware('guest')->name('forgot.submit');
    Route::post('/login', [AuthController::class, 'login'])->middleware('guest')->name('login');
    Route::post('/register', [AuthController::class, 'register'])->middleware('guest')->name('register');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('user')->name('logout');
});

Route::group(['prefix' => 'user', 'as' => 'user.', 'middleware' => 'user'], function () {
    Route::get('/profile', [UserController::class, 'getUserProfile'])->name('profile');
    Route::post('/update', [UserController::class, 'update'])->name('update');
});

Route::group(['prefix' => 'survey', 'as' => 'survey.', 'middleware' => 'user'], function () {
    Route::post('/create', [SurveyController::class, 'store'])->name('create');
});

Route::group(['prefix' => 'detection', 'as' => 'detection.', 'middleware' => 'user'], function () {
    Route::post('/', [DetectionController::class, 'detection'])->name('index');
});

Route::group(['prefix' => 'glow-up', 'as' => 'glow-up.', 'middleware' => 'user'], function () {
    Route::get('/', [GlowUpPlanController::class, 'index'])->name('index');
    Route::get('/{id}', [GlowUpPlanController::class, 'show'])->name('show');
    Route::post('/create', [GlowUpPlanController::class, 'create'])->name('create');
    Route::delete('/delete/{id}', [GlowUpPlanController::class, 'delete'])->name('delete');
});

Route::group(['prefix' => 'product-categories', 'as' => 'product-categories.', 'middleware' => 'user'], function () {
    Route::get('/', [ProductCategoryController::class, 'index'])->name('index');
    Route::get('/{productCategory:slug}', [ProductCategoryController::class, 'show'])->name('show');
});

Route::group(['prefix' => 'product-variations', 'as' => 'product-variations.', 'middleware' => 'user'], function () {
    Route::post('/create', [ProductVariationController::class, 'store'])->name('create');
    Route::get('/{id}', [ProductVariationController::class, 'show'])->name('show');
});

Route::group(['prefix' => 'liked-products', 'as' => 'liked-products.', 'middleware' => 'user'], function () {
    Route::get('/', [LikedProductController::class, 'index'])->name('index');
    Route::post('/create', [LikedProductController::class, 'create'])->name('create');
    Route::delete('/delete/{likedProduct:id}', [LikedProductController::class, 'delete'])->name('delete');
});

Route::group(['prefix' => 'products', 'as' => 'product.'], function () {
    Route::group(['middleware' => 'user'], function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/{product:slug}', [ProductController::class, 'show'])->name('show');
    });
    Route::group(['middleware' => 'admin'], function () {
        Route::post('/create', [ProductController::class, 'create'])->name('create');
        Route::patch('/update/{product:slug}', [ProductController::class, 'update'])->name('update');
        Route::delete('/delete/{product:slug}', [ProductController::class, 'delete'])->name('delete');
    });
});

Route::group(['prefix' => 'carts', 'as' => 'cart.', 'middleware' => 'user'], function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/create', [CartController::class, 'create'])->name('create');
    Route::patch('/update/{cart}', [CartController::class, 'update'])->name('update');
    Route::delete('/delete/{cart}', [CartController::class, 'delete'])->name('delete');
});

Route::group(['prefix' => 'orders', 'as' => 'order.', 'middleware' => 'user'], function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::post('/create', [OrderController::class, 'create'])->name('create');
    Route::patch('/update/{order}', [OrderController::class, 'update'])->name('update');
    Route::delete('/delete/{order}', [OrderController::class, 'delete'])->name('delete');
});
Route::post('/orders/midtrans/callback', [OrderController::class, 'callback']);

Route::group(['prefix' => 'artisan'], function () {
    Route::get('/env', [ArtisanController::class, 'getEnv']);
    Route::get('/key', [ArtisanController::class, 'key']);
    Route::get('/seed', [ArtisanController::class, 'seed']);
    Route::get('/fresh', [ArtisanController::class, 'fresh']);
    Route::get('/cache', [ArtisanController::class, 'cache']);
    Route::get('/storage', [ArtisanController::class, 'storage']);
    Route::get('/migrate', [ArtisanController::class, 'migrate']);
    Route::get('/optimize', [ArtisanController::class, 'optimize']);
});
