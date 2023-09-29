<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SurveyController;

Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
    Route::get('/google', [AuthController::class, 'redirectToGoogle'])->middleware('guest')->name('google.login');
    Route::get('/google/callback', [AuthController::class, 'handleGoogleCallback'])->middleware('guest')->name('google.callback');
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