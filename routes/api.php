<?php

use App\Http\Controllers\AppControllers\HomePageController;
use App\Http\Controllers\AppControllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->middleware(['auth.jwt'])->group(function () {
    Route::get('/home', [HomePageController::class, 'homePage']);
    Route::get('/profile', [ProfileController::class, 'userProfile']);
    Route::put('/profile', [ProfileController::class, 'updateProfileData']);
    
});


