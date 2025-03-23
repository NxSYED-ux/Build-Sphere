<?php

use App\Http\Controllers\GeneralControllers\AuthController;
use App\Http\Controllers\GeneralControllers\ForgotPasswordController;
use App\Http\Controllers\GeneralControllers\ProfileController;
use App\Http\Controllers\WebControllers\AdminDashboardController;
use App\Http\Controllers\WebControllers\BuildingController;
use App\Http\Controllers\WebControllers\BuildingLevelController;
use App\Http\Controllers\WebControllers\BuildingTreeController;
use App\Http\Controllers\WebControllers\BuildingUnitController;
use App\Http\Controllers\WebControllers\DropdownTypeController;
use App\Http\Controllers\WebControllers\DropdownValueController;
use App\Http\Controllers\WebControllers\OrganizationController;
use App\Http\Controllers\WebControllers\OwnerDashboardController;
use App\Http\Controllers\WebControllers\RolePermissionController;
use App\Http\Controllers\WebControllers\RoleController;
use App\Http\Controllers\WebControllers\UsersController;
use Illuminate\Support\Facades\Route;


// Route for Pusher Authentication
Route::post('/pusher/auth', [AuthController::class, 'authenticatePusher'])->name('pusher.auth');

Route::get('/', function () { return view('landing-views.index'); })->name('index');
Route::get('/index', function () { return view('landing-views.index'); });

// Authentication routes
Route::get('/admin-login', [AuthController::class, 'adminLoginIndex'])->name('admin-login-index');
Route::get('/owner-login', [AuthController::class, 'ownerLoginIndex'])->name('owner-login-index');

Route::get('login', [AuthController::class, 'index'])->name('login');
Route::post('login', [AuthController::class, 'adminLogin'])->name('login');


Route::prefix('auth')->group(function () {

    Route::post('/admin-login', [AuthController::class, 'adminLogin'])->name('admin-login');
    Route::post('/owner-login', [AuthController::class, 'ownerLogin'])->name('owner-login');
    Route::get('/forget-password', [ForgotPasswordController::class, 'showForgetForm'])->name('password.request');
    Route::post('/forget-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

});



Route::fallback(function () {
    return back();
});

Route::middleware(['auth.jwt'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logOut'])->name('logout');
    Route::delete('/buildings/{id}/remove-picture', [BuildingController::class, 'destroyImage'])->name('buildings.remove_picture');
    Route::delete('/building_documents/{id}', [BuildingController::class, 'removeDocument'])->name('building_documents.removeDocument');
    Route::put('/building_document/{id}', [BuildingController::class, 'updateDocument'])->name('building_document.update');
    Route::get('/building_document/{id}', [BuildingController::class, 'getDocument'])->name('building_document.edit');

    Route::get('/buildings/{id}/levels', [BuildingController::class, 'getLevels'])->name('buildings.levels');

    Route::delete('/units/{id}/remove-picture', [BuildingUnitController::class, 'destroyImage'])->name('units.remove_picture');

    Route::delete('/organizations/{id}/remove-picture', [OrganizationController::class, 'destroyImage'])->name('organizations.remove_picture');
    Route::get('/organizations/{id}/buildings', [OrganizationController::class, 'getBuildings'])->name('organizations.buildings');

});

Route::prefix('admin')->middleware(['auth.jwt'])->group(function () {

    Route::prefix('dashboard')->group(function () {

        Route::get('/', [AdminDashboardController::class, 'index'])->name('admin_dashboard');
        Route::get('/data', [AdminDashboardController::class, 'data'])->name('admin_dashboard.data');

    });

    Route::prefix('profile')->group(function () {

        Route::get('/', [ProfileController::class, 'adminProfile'])->name('admin.profile');
        Route::put('/', [ProfileController::class, 'updateProfileData'])->name('admin.profile.update');
        Route::put('/picture', [ProfileController::class, 'uploadProfilePic'])->name('admin.profile.picture.update');
        Route::delete('/picture', [ProfileController::class, 'deleteProfilePic'])->name('admin.profile.picture.delete');
        Route::put('/password', [ProfileController::class, 'changePassword'])->name('admin.profile.password.update');

    });

    Route::prefix('users')->group(function () {

        Route::get('/', [UsersController::class, 'index'])->name('users.index');
        Route::get('/create', [UsersController::class, 'create'])->name('users.create');
        Route::post('/', [UsersController::class, 'store'])->name('users.store');
        Route::get('/{user}', [UsersController::class, 'show'])->name('users.show');
        Route::get('/{user}/edit', [UsersController::class, 'edit'])->name('users.edit');
        Route::put('/', [UsersController::class, 'update'])->name('users.update');

    });

    Route::prefix('roles')->group(function () {

        Route::get('/', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/{role}', [RoleController::class, 'show'])->name('roles.show');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/update', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

    });

    Route::prefix('/types')->group(function () {

        Route::get('/', [DropdownTypeController::class, 'index'])->name('types.index');
        Route::get('/create', [DropdownTypeController::class, 'create'])->name('types.create');
        Route::post('/', [DropdownTypeController::class, 'store'])->name('types.store');
        Route::get('/{type}', [DropdownTypeController::class, 'show'])->name('types.show');
        Route::get('/{type}/edit', [DropdownTypeController::class, 'edit'])->name('types.edit');
        Route::put('/{type}', [DropdownTypeController::class, 'update'])->name('types.update');
        Route::delete('/{type}', [DropdownTypeController::class, 'destroy'])->name('types.destroy');

    });

    Route::prefix('values')->group(function () {

        Route::get('/', [DropdownValueController::class, 'index'])->name('values.index');
        Route::get('/create', [DropdownValueController::class, 'create'])->name('values.create');
        Route::post('/', [DropdownValueController::class, 'store'])->name('values.store');
        Route::get('/{value}', [DropdownValueController::class, 'show'])->name('values.show');
        Route::get('/{value}/edit', [DropdownValueController::class, 'edit'])->name('values.edit');
        Route::put('/{value}', [DropdownValueController::class, 'update'])->name('values.update');
        Route::delete('/{value}', [DropdownValueController::class, 'destroy'])->name('values.destroy');

    });

    Route::prefix('buildings')->group(function () {

        Route::get('/', [BuildingController::class, 'adminIndex'])->name('buildings.index');
        Route::get('/create', [BuildingController::class, 'adminCreate'])->name('buildings.create');
        Route::post('/', [BuildingController::class, 'adminStore'])->name('buildings.store');
        Route::get('/{building}/show', [BuildingController::class, 'adminShow'])->name('buildings.show');
        Route::get('/{building}/edit', [BuildingController::class, 'adminEdit'])->name('buildings.edit');
        Route::put('/', [BuildingController::class, 'adminUpdate'])->name('buildings.update');
        Route::delete('/{building}', [BuildingController::class, 'destroy'])->name('buildings.destroy');
        Route::post('/reject', [BuildingController::class, 'rejectBuilding'])->name('buildings.reject');
        Route::post('/approve', [BuildingController::class, 'approveBuilding'])->name('buildings.approve');

    });

    Route::prefix('levels')->group(function () {

        Route::get('/', [BuildingLevelController::class, 'adminIndex'])->name('levels.index');
        Route::get('/create', [BuildingLevelController::class, 'adminCreate'])->name('levels.create');
        Route::post('/', [BuildingLevelController::class, 'adminStore'])->name('levels.store');
        Route::get('/{level}', [BuildingLevelController::class, 'adminShow'])->name('levels.show');
        Route::get('/{level}/edit', [BuildingLevelController::class, 'adminEdit'])->name('levels.edit');
        Route::put('/{level}', [BuildingLevelController::class, 'adminUpdate'])->name('levels.update');

    });

    Route::prefix('units')->group(function () {

        Route::get('/', [BuildingUnitController::class, 'index'])->name('units.index');
        Route::get('/create', [BuildingUnitController::class, 'create'])->name('units.create');
        Route::post('/', [BuildingUnitController::class, 'store'])->name('units.store');
        Route::get('/{unit}', [BuildingUnitController::class, 'show'])->name('units.show');
        Route::get('/{unit}/edit', [BuildingUnitController::class, 'edit'])->name('units.edit');
        Route::put('/{unit}', [BuildingUnitController::class, 'update'])->name('units.update');
        Route::delete('/{unit}', [BuildingUnitController::class, 'destroy'])->name('units.destroy');
        Route::get('/details/{id}', [BuildingUnitController::class, 'getUnitData'])->name('units.details');

    });

    Route::prefix('organizations')->group(function () {

        Route::get('/', [OrganizationController::class, 'index'])->name('organizations.index');
        Route::get('/create', [OrganizationController::class, 'create'])->name('organizations.create');
        Route::post('/', [OrganizationController::class, 'store'])->name('organizations.store');
        Route::get('/{organization}', [OrganizationController::class, 'show'])->name('organizations.show');
        Route::get('/{organization}/edit', [OrganizationController::class, 'edit'])->name('organizations.edit');
        Route::put('/{organization}', [OrganizationController::class, 'update'])->name('organizations.update');

    });

    Route::prefix('/role-Permissions')->group(function () {

        Route::get('/', [RolePermissionController::class, 'showRolePermissions'])->name('role.permissions');
        Route::post('/toggle', [RolePermissionController::class, 'toggleRolePermission'])->name('toggle.role.permission');

    });

});


Route::prefix('owner')->middleware(['auth.jwt'])->group(function () {

    Route::prefix('dashboard')->group(function () {
        Route::get('/', [OwnerDashboardController::class, 'index'])->name('owner_manager_dashboard');
    });

    Route::prefix('profile')->group(function () {

        Route::get('/', [ProfileController::class, 'ownerProfile'])->name('owner.profile');
        Route::put('/', [ProfileController::class, 'updateProfileData'])->name('owner.profile.update');
        Route::put('/picture', [ProfileController::class, 'uploadProfilePic'])->name('owner.profile.picture.update');
        Route::delete('/picture', [ProfileController::class, 'deleteProfilePic'])->name('owner.profile.picture.delete');
        Route::put('/password', [ProfileController::class, 'changePassword'])->name('owner.profile.password.update');

    });

    Route::prefix('buildings')->group(function () {

        Route::get('/', [BuildingController::class, 'ownerIndex'])->name('owner.buildings.index');
        Route::get('/create', [BuildingController::class, 'ownerCreate'])->name('owner.buildings.create');
        Route::post('/', [BuildingController::class, 'ownerStore'])->name('owner.buildings.store');
        Route::get('/tree', [BuildingTreeController::class, 'tree'])->name('owner.buildings.tree');
        Route::get('/{building}/show', [BuildingController::class, 'ownerShow'])->name('owner.buildings.show');
        Route::get('/{building}/edit', [BuildingController::class, 'ownerEdit'])->name('owner.buildings.edit');
        Route::put('/', [BuildingController::class, 'ownerUpdate'])->name('owner.buildings.update');
        Route::post('/submit', [BuildingController::class, 'submitBuilding'])->name('owner.buildings.submit');
        Route::post('/reminder', [BuildingController::class, 'approvalReminder'])->name('owner.buildings.reminder');

    });

    Route::prefix('levels')->group(function () {

        Route::get('/', [BuildingLevelController::class, 'ownerIndex'])->name('owner.levels.index');
        Route::get('/create', [BuildingLevelController::class, 'ownerCreate'])->name('owner.levels.create');
        Route::post('/', [BuildingLevelController::class, 'ownerStore'])->name('owner.levels.store');
        Route::get('/{level}', [BuildingLevelController::class, 'ownerShow'])->name('owner.levels.show');
        Route::get('/{level}/edit', [BuildingLevelController::class, 'ownerEdit'])->name('owner.levels.edit');
        Route::put('/{level}', [BuildingLevelController::class, 'ownerUpdate'])->name('owner.levels.update');

    });

});

require __DIR__.'/auth.php';
require __DIR__.'/api.php';
