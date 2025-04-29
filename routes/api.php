<?php

use App\Events\UserPermissionUpdated;
use App\Http\Controllers\AppControllers\CheckOutController;
use App\Http\Controllers\AppControllers\DropdownController;
use App\Http\Controllers\AppControllers\FavouritesController;
use App\Http\Controllers\AppControllers\ListingController;
use App\Http\Controllers\AppControllers\MyPropertiesController;
use App\Http\Controllers\AppControllers\QueryController;
use App\Http\Controllers\AppControllers\TransactionController;
use App\Http\Controllers\GeneralControllers\AuthController;
use App\Http\Controllers\GeneralControllers\CardController;
use App\Http\Controllers\GeneralControllers\ForgotPasswordController;
use App\Http\Controllers\GeneralControllers\NotificationController;
use App\Http\Controllers\GeneralControllers\ProfileController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;


Route::get('/test-permissions', function () {
    Log::info("Triggering UserPermissionUpdated event for user 2.");
    event(new UserPermissionUpdated(2));
    return 'ok';
});




// Without Authentication

Route::get('/values-by-type/{type}', [DropdownController::class, 'getDropdownValuesByType']);
Route::get('/values-by-value/{value}', [DropdownController::class, 'getDropdownValuesByValue']);

Route::prefix('auth')->group(function () {

    Route::post('/user-login', [AuthController::class, 'userLogin']);
    Route::post('/staff-login', [AuthController::class, 'staffLogin']);
    Route::post('/forget-password', [ForgotPasswordController::class, 'sendResetLink']);
    Route::post('/logout', [AuthController::class, 'logout']);

});


// With Authentication
Route::middleware(['auth.jwt'])->group(function () {

    Route::prefix('notifications')->group(function () {

        Route::get('/', [NotificationController::class, 'getNotifications'])->name('notifications');
        Route::get('/unread', [NotificationController::class, 'getUnReadNotifications'])->name('notifications.unread');
        Route::get('/unread-count', [NotificationController::class, 'getUnreadNotificationsCount'])->name('notifications.unread.count');
        Route::post('/mark-all-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-all-as-read');
        Route::post('/mark-single-as-read', [NotificationController::class, 'markAsReadSingle'])->name('notifications.mark-single-as-read');
        Route::post('/mark-all-as-unread', [NotificationController::class, 'markAsUnRead'])->name('notifications.mark-all-as-unread');
        Route::post('/remove-all', [NotificationController::class, 'removeAll'])->name('notifications.remove-all');
        Route::post('/remove-single', [NotificationController::class, 'removeSingle'])->name('notifications.remove-single');

    });

    Route::prefix('user')->group(function () {

        Route::middleware('check.permission:User Profile')->group(function () {
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
            Route::get('/home', [ListingController::class, 'homePageListings']);
            Route::get('/unit_details/{id}', [ListingController::class, 'unitDetails']);
            Route::get('/organization_details/{id}', [ListingController::class, 'organizationWithBuildings']);
            Route::get('/building_units/{id}', [ListingController::class, 'specificBuildingUnits']);
            Route::get('/favorites-list', [FavouritesController::class, 'favouritesList']);
        });

        Route::middleware('check.permission:Favorites,json')->group(function () {
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

        Route::middleware('check.permission:User Queries,json')->group(function () {
            Route::get('/get-queries', [QueryController::class, 'getUserQueries']);
            Route::get('/query/{id}', [QueryController::class, 'getQueryDetails']);
        });

        Route::get('/my-unit-names', [QueryController::class, 'userUnitNames']);
        Route::get('/corresponding-departments/{organizationId}', [QueryController::class, 'correspondingDepartments']);

        Route::prefix('cards')->group(function () {

            Route::get('/', [CardController::class, 'index']);
            Route::post('/', [CardController::class, 'store']);
            Route::put('/', [CardController::class, 'update']);
            Route::delete('/', [CardController::class, 'destroy']);

        });

        Route::prefix('transactions')->group(function () {

            Route::get('/', [TransactionController::class, 'index']);
            Route::get('/show/{id}', [TransactionController::class, 'show']);

        });

        Route::post('/unit-checkout', [CheckOutController::class, 'unitsOnlinePayment']);
        Route::post('/unit-complete-checkout', [CheckOutController::class, 'completeUnitPayment']);

    });

    Route::prefix('staff')->middleware(['plan'])->group(function () {

        Route::get('/profile', [ProfileController::class, 'getProfile']);
        Route::put('/profile', [ProfileController::class, 'updateProfileData']);
        Route::post('/update-profile-pic', [ProfileController::class, 'uploadProfilePic']);
        Route::put('/remove-profile-pic', [ProfileController::class, 'deleteProfilePic']);
        Route::put('/change-password', [ProfileController::class, 'changePassword']);

        Route::middleware('check.permission:Staff Queries,json')->group(function () {
            Route::get('/get-queries', [QueryController::class, 'getStaffQueries']);
            Route::get('/query/{id}', [QueryController::class, 'getQueryDetails']);
        });
        Route::get('/get-queries', [QueryController::class, 'getStaffQueries']);
        Route::get('/query/{id}', [QueryController::class, 'getQueryDetails']);
        Route::put('/reject-query', [QueryController::class, 'rejectQuery']);
        Route::put('/accept-query', [QueryController::class, 'acceptQuery']);
        Route::put('/close-query', [QueryController::class, 'closeQuery']);
        Route::get('/query-count', [QueryController::class, 'getYearlyQueryStats']);
        Route::get('/query-chart', [QueryController::class, 'getMonthlyQueryStats']);

    });
});
