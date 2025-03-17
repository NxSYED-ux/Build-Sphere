<?php

use App\Http\Controllers\GeneralControllers\AuthController;
use App\Http\Controllers\GeneralControllers\ForgotPasswordController;
use App\Http\Controllers\GeneralControllers\NotificationController;
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



// Route for Pusher Authentication
Route::post('/pusher/auth', [AuthController::class, 'authenticatePusher'])->name('pusher.auth');

// Without Authentication
Route::prefix('api')->group(function () {

    Route::get('/values-by-type/{type}', [DropdownController::class, 'getDropdownValuesByType']);
    Route::get('/values-by-value/{value}', [DropdownController::class, 'getDropdownValuesByValue']);

    Route::prefix('auth')->group(function () {

        Route::post('/user-login', [AuthController::class, 'login']);
        Route::post('/user-staff', [AuthController::class, 'login']);
        Route::post('/forget-password', [ForgotPasswordController::class, 'sendResetLink']);
        Route::post('/logout', [AuthController::class, 'logout']);

    });

});

// With Authentication
Route::prefix('api')->middleware(['auth.jwt'])->group(function () {

    Route::prefix('notifications')->group(function () {

        Route::get('/', [NotificationController::class, 'getNotifications'])->name('notifications');
        Route::get('/unread', [NotificationController::class, 'getUnReadNotifications'])->name('notifications');
        Route::get('/unread-count', [NotificationController::class, 'getUnreadNotificationsCount'])->name('notifications.unread.count');
        Route::post('/mark-all-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-all-as-read');
        Route::post('/mark-single-as-read', [NotificationController::class, 'markAsReadSingle'])->name('notifications.mark-single-as-read');
        Route::post('/mark-all-as-unread', [NotificationController::class, 'markAsUnRead'])->name('notifications.mark-all-as-unread');
        Route::post('/remove-all', [NotificationController::class, 'removeAll'])->name('notifications.remove-all');
        Route::post('/remove-single', [NotificationController::class, 'removeSingle'])->name('notifications.remove-single');

        Route::get('/pusher-config', [NotificationController::class, 'pusherCredentials']);

    });

    Route::prefix('user')->group(function () {

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

    Route::prefix('staff')->group(function () {

        Route::get('/profile', [ProfileController::class, 'getProfile']);
        Route::put('/profile', [ProfileController::class, 'updateProfileData']);
        Route::post('/update-profile-pic', [ProfileController::class, 'uploadProfilePic']);
        Route::put('/remove-profile-pic', [ProfileController::class, 'deleteProfilePic']);
        Route::put('/change-password', [ProfileController::class, 'changePassword']);

        Route::get('/get-queries', [QueryController::class, 'getStaffQueries']);
        Route::get('/query/{id}', [QueryController::class, 'getQueryDetails']);
        Route::put('/reject-query', [QueryController::class, 'rejectQuery']);
        Route::put('/accept-query', [QueryController::class, 'acceptQuery']);
        Route::get('/query-count', [QueryController::class, 'getYearlyQueryStats']);
        Route::get('/query-chart', [QueryController::class, 'getMonthlyQueryStats']);

    });

});
