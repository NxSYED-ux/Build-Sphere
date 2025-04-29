<?php

use App\Http\Controllers\GeneralControllers\AuthController;
use App\Http\Controllers\GeneralControllers\ForgotPasswordController;
use App\Http\Controllers\WebControllers\SignUpController;
use Illuminate\Support\Facades\Route;

// Route for Pusher Authentication
Route::post('/pusher/auth', [AuthController::class, 'authenticatePusher'])->name('pusher.auth');

Route::prefix('auth')->group(function () {

    Route::prefix('login')->group(function () {

        Route::get('/', [AuthController::class, 'index'])->name('login');
        Route::post('/', [AuthController::class, 'login'])->name('login');

    });

    Route::prefix('signUp')->group(function () {

        Route::get('/', [SignUpController::class, 'index'])->name('signUp');
        Route::post('/', [SignUpController::class, 'register'])->name('signUp');
        Route::post('/otp', [SignUpController::class, 'send_otp'])->name('send_signup_otp');

    });

    Route::prefix('forget-password')->group(function () {

        Route::get('/', [ForgotPasswordController::class, 'showForgetForm'])->name('password.request');
        Route::post('/mail', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');

    });

    Route::prefix('reset-password')->group(function () {

        Route::get('/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
        Route::post('/', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

    });

});

Route::post('/logout', [AuthController::class, 'logOut'])->name('logout');
