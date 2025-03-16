<?php

use App\Http\Controllers\GeneralControllers\AuthController;
use App\Models\User;
use App\Notifications\UserNotification;
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

Route::get('/send-notification/{id}', function ($id) {
    $user = User::find($id);

    if ($user) {
        $picture = "http://localhost:8000/uploads/units/images/Apartment_{$id}.jpeg";
        $user->notify(new UserNotification(
            $picture,
            'Apna kam kr 🤭',
            'Lagta ha tera sara kam ho gaya ha jo mera kam check kr raha ha',
            'http://localhost:8000/dashboard'
        ));
        return response()->json(['message' => 'Notification sent to user ID: ' . $id]);
    }

    return response()->json(['message' => 'No user found with ID: ' . $id], 404);
});


Route::get('/user/values-by-type/{type}', [DropdownController::class, 'getDropdownValuesByType']);
Route::get('/user/values-by-value/{value}', [DropdownController::class, 'getDropdownValuesByValue']);

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
//    Route::middleware('check.permission:Remove User Profile Picture')->group(function () {
    Route::put('/remove-profile-pic', [ProfileController::class, 'deleteProfilePic']);
//    });
    Route::middleware('check.permission:Change Password')->group(function () {
        Route::put('/change-password', [ProfileController::class, 'changePassword']);
    });

    Route::middleware('check.permission:User Homepage,json')->group(function () {
        Route::get('/home', [HomePageController::class, 'homePage']);
        Route::get('/unit_details/{id}', [UnitDetailsController::class, 'unitDetails']);
        Route::get('/organization_details/{id}', [OrganizationDetailsController::class, 'organizationDetails']);
        Route::get('/building_units/{id}', [BuildingUnitsController::class, 'specificBuildingUnits']);
    });

    Route::get('/favorites-list', [FavouritesController::class, 'favouritesList']);

    Route::middleware('check.permission:Show Favorites,json')->group(function () {
        Route::get('/favorites', [FavouritesController::class, 'showFavourites']);
    });
    Route::middleware('check.permission:Add Favorites,json')->group(function () {
        Route::post('/favorites', [FavouritesController::class, 'insertFavorite']);
    });
    Route::middleware('check.permission:Remove Favorites,json')->group(function () {
        Route::delete('/favorites/{unit_id}', [FavouritesController::class, 'deleteFavorite']);
    });

    Route::middleware('check.permission:Show My Properties,json')->group(function () {
        Route::get('/my_properties', [MyPropertiesController::class, 'showMyProperties']);
        Route::get('/my_properties/{id}', [MyPropertiesController::class, 'myPropertyDetails']);
    });

    Route::middleware('check.permission:Log Queries,json')->group(function () {
        Route::post('/log-query', [QueryController::class, 'logQuery']);
    });

    Route::middleware('check.permission:View User Queries,json')->group(function () {
        Route::get('/get-queries', [QueryController::class, 'getUserQueries']);
        Route::get('/query/{id}', [QueryController::class, 'getQueryDetails']);
    });

    Route::get('/my-unit-names', [QueryController::class, 'userUnitNames']);
    Route::get('/corresponding-departments/{organizationId}', [QueryController::class, 'correspondingDepartments']);
});
