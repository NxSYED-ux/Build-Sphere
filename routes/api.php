<?php

use App\Http\Controllers\GeneralControllers\AuthController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\GeneralControllers\ProfileController;
use App\Http\Controllers\AppControllers\HomePageController;
use App\Http\Controllers\AppControllers\UnitDetailsController;
use App\Http\Controllers\AppControllers\BuildingUnitsController;
use App\Http\Controllers\AppControllers\OrganizationDetailsController;
use App\Http\Controllers\AppControllers\FavouritesController;
use App\Http\Controllers\AppControllers\MyPropertiesController;
use App\Http\Controllers\AppControllers\QueryController;
use App\Http\Controllers\AppControllers\DropdownController;

Route::post('auth/user-login', [AuthController::class, 'login']);

Route::prefix('user')->middleware(['auth.jwt'])->group(function () {

    Route::middleware('check.permission:View User Profile')->group(function () {
        Route::get('/profile', [ProfileController::class, 'getProfile']);
    });
    Route::middleware('check.permission:Update User Profile')->group(function () {
        Route::put('/profile', [ProfileController::class, 'updateProfileData']);
    });
    Route::middleware('check.permission:Upload User Profile Picture')->group(function () {
        Route::post('/update-profile-pic', [ProfileController::class, 'uploadProfilePic']);
    });
    Route::middleware('check.permission:Remove User Profile Picture')->group(function () {
        Route::put('/remove-profile-pic', [ProfileController::class, 'deleteProfilePic']);
    });
    Route::middleware('check.permission:Remove User Profile Picture')->group(function () {
        Route::put('/change-password', [ProfileController::class, 'changePassword']);
    });


    Route::middleware('check.permission:User Homepage Access,json')->group(function () {
        Route::get('/home', [HomePageController::class, 'homePage']);
        Route::get('/unit-details/{id}', [UnitDetailsController::class, 'unitDetails']);
        Route::get('/organization-details/{id}', [OrganizationDetailsController::class, 'organizationDetails']);
        Route::get('/building-units/{id}', [BuildingUnitsController::class, 'specificBuildingUnits']);
    });

    Route::get('/favorites-list', [FavouritesController::class, 'favouritesList']);
    Route::middleware('check.permission:Show Favorites Access,json')->group(function () {
        Route::get('/favorites', [FavouritesController::class, 'showFavourites']);
    });
    Route::middleware('check.permission:Add Favorites Access,json')->group(function () {
        Route::post('/favorites', [FavouritesController::class, 'insertFavorite']);
    });
    Route::middleware('check.permission:Remove Favorites Access,json')->group(function () {
        Route::delete('/favorites/{unit_id}', [FavouritesController::class, 'deleteFavorite']);
    });

    Route::middleware('check.permission:Show My Properties Access,json')->group(function () {
        Route::get('/my-properties', [MyPropertiesController::class, 'showMyProperties']);
        Route::get('/my-properties/{id}', [MyPropertiesController::class, 'myPropertyDetails']);
    });

    Route::middleware('check.permission:Log Queries Access,json')->group(function () {
        Route::post('/log-query', [QueryController::class, 'logQuery']);
    });

    Route::middleware('check.permission:View User Queries Access,json')->group(function () {
        Route::get('/get-queries', [QueryController::class, 'getQueriesByField']);
        Route::get('/query/{id}', [QueryController::class, 'getQueryDetails']);
    });

    Route::get('/my-unit-names', [QueryController::class, 'userUnitNames']);
    Route::get('/corresponding-departments/{organizationId}', [QueryController::class, 'correspondingDepartments']);

    Route::get('/values-by-type/{type}', [DropdownController::class, 'getDropdownValuesByType']);
    Route::get('/values-by-value/{value}', [DropdownController::class, 'getDropdownValuesByValue']);
});
