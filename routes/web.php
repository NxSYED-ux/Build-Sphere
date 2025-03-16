<?php

use App\Http\Controllers\GeneralControllers\AuthController;
use App\Http\Controllers\GeneralControllers\ForgotPasswordController;
use App\Http\Controllers\GeneralControllers\NotificationController;
use App\Http\Controllers\GeneralControllers\ProfileController;
use App\Http\Controllers\WebControllers\AdminDashboardController;
use App\Http\Controllers\WebControllers\BuildingController;
use App\Http\Controllers\WebControllers\BuildingLevelController;
use App\Http\Controllers\WebControllers\BuildingTreeController;
use App\Http\Controllers\WebControllers\BuildingUnitController;
use App\Http\Controllers\WebControllers\DropdownTypeController;
use App\Http\Controllers\WebControllers\DropdownValueController;
use App\Http\Controllers\WebControllers\OrganizationController;
use App\Http\Controllers\WebControllers\OwnerBuildingController;
use App\Http\Controllers\WebControllers\OwnerDashboardController;
use App\Http\Controllers\WebControllers\RolePermissionController;
use App\Http\Controllers\WebControllers\RoleController;
use App\Http\Controllers\WebControllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/index', function () {
    return view('layouts.index');
});

// Authentication routes
Route::get('login', [AuthController::class, 'index'])->name('login');
Route::post('login', [AuthController::class, 'adminLogin'])->name('login');
//Route::post('admin-login', [AuthController::class, 'adminLogin'])->name('admin-login');
//Route::post('owner-login', [AuthController::class, 'ownerLogin'])->name('owner-login');

Route::get('forget-password', [ForgotPasswordController::class, 'showForgetForm'])->name('password.request');
Route::post('forget-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');

Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

Route::fallback(function () {
    return back();
});

Route::middleware(['auth.jwt'])->group(function () {

    // Notification
    Route::post('/pusher/auth', [AuthController::class, 'authenticatePusher'])->name('pusher.auth');

    // Admin & Owner
    Route::post('logout', [AuthController::class, 'logOut'])->name('logout');
    Route::get('admin/profile', [ProfileController::class, 'adminProfile'])->name('admin.profile');
    Route::put('admin/profile', [ProfileController::class, 'updateProfileData'])->name('admin.profile.update');
    Route::put('admin/profile/picture', [ProfileController::class, 'uploadProfilePic'])->name('admin.profile.picture.update');
    Route::delete('admin/profile/picture', [ProfileController::class, 'deleteProfilePic'])->name('admin.profile.picture.delete');
    Route::put('admin/profile/password', [ProfileController::class, 'changePassword'])->name('admin.profile.password.update');

    Route::get('owner/profile', [ProfileController::class, 'ownerProfile'])->name('owner.profile');
    Route::put('owner/profile', [ProfileController::class, 'updateProfileData'])->name('owner.profile.update');
    Route::put('owner/profile/picture', [ProfileController::class, 'uploadProfilePic'])->name('owner.profile.picture.update');
    Route::delete('owner/profile/picture', [ProfileController::class, 'deleteProfilePic'])->name('owner.profile.picture.delete');
    Route::put('owner/profile/password', [ProfileController::class, 'changePassword'])->name('owner.profile.password.update');

    //Admin
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('admin_dashboard', [AdminDashboardController::class, 'index'])->name('admin_dashboard');
    Route::get('admin_dashboard_data', [AdminDashboardController::class, 'data'])->name('admin_dashboard.data');


    // Route::resource('users', UsersController::class);
    Route::get('users', [UsersController::class, 'index'])->name('users.index');
    Route::get('users/create', [UsersController::class, 'create'])->name('users.create');
    Route::post('users', [UsersController::class, 'store'])->name('users.store');
    Route::get('users/{user}', [UsersController::class, 'show'])->name('users.show');
    Route::get('users/{user}/edit', [UsersController::class, 'edit'])->name('users.edit');
    Route::put('users', [UsersController::class, 'update'])->name('users.update');

    // Route::resource('roles', RoleController::class);
    Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show');
    Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/update', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

    //Route::resource('types', DropdownTypeController::class);
    Route::get('types', [DropdownTypeController::class, 'index'])->name('types.index');
    Route::get('types/create', [DropdownTypeController::class, 'create'])->name('types.create');
    Route::post('types', [DropdownTypeController::class, 'store'])->name('types.store');
    Route::get('types/{type}', [DropdownTypeController::class, 'show'])->name('types.show');
    Route::get('types/{type}/edit', [DropdownTypeController::class, 'edit'])->name('types.edit');
    Route::put('types/{type}', [DropdownTypeController::class, 'update'])->name('types.update');
    Route::delete('types/{type}', [DropdownTypeController::class, 'destroy'])->name('types.destroy');

    //Route::resource('values', DropdownValueController::class);
    Route::get('values', [DropdownValueController::class, 'index'])->name('values.index');
    Route::get('values/create', [DropdownValueController::class, 'create'])->name('values.create');
    Route::post('values', [DropdownValueController::class, 'store'])->name('values.store');
    Route::get('values/{value}', [DropdownValueController::class, 'show'])->name('values.show');
    Route::get('values/{value}/edit', [DropdownValueController::class, 'edit'])->name('values.edit');
    Route::put('values/{value}', [DropdownValueController::class, 'update'])->name('values.update');
    Route::delete('values/{value}', [DropdownValueController::class, 'destroy'])->name('values.destroy');

    //Route::resource('buildings', BuildingController::class);
    Route::get('buildings', [BuildingController::class, 'adminIndex'])->name('buildings.index');
    Route::get('buildings/create', [BuildingController::class, 'adminCreate'])->name('buildings.create');
    Route::post('buildings', [BuildingController::class, 'adminStore'])->name('buildings.store');
    Route::get('buildings/{building}', [BuildingController::class, 'adminShow'])->name('buildings.show');
    Route::get('buildings/{building}/edit', [BuildingController::class, 'adminEdit'])->name('buildings.edit');
    Route::put('buildings', [BuildingController::class, 'adminUpdate'])->name('buildings.update');
    Route::delete('buildings/{building}', [BuildingController::class, 'destroy'])->name('buildings.destroy');
    Route::delete('/buildings/{id}/remove-picture', [BuildingController::class, 'destroyImage'])->name('buildings.remove_picture');
    Route::delete('/building_documents/{id}', [BuildingController::class, 'removeDocument'])->name('building_documents.removeDocument');
    Route::put('building_document/{id}', [BuildingController::class, 'updateDocument'])->name('building_document.update');
    Route::get('building_document/{id}', [BuildingController::class, 'getDocument'])->name('building_document.edit');
    Route::get('buildings/{id}/levels', [BuildingController::class, 'getLevels'])->name('buildings.levels');
    Route::post('buildings/reject', [BuildingController::class, 'rejectBuilding'])->name('buildings.reject');
    Route::post('buildings/approve', [BuildingController::class, 'approveBuilding'])->name('buildings.approve');

    //Route::resource('levels', BuildingLevelController::class);
    Route::get('levels', [BuildingLevelController::class, 'index'])->name('levels.index');
    Route::get('levels/create', [BuildingLevelController::class, 'create'])->name('levels.create');
    Route::post('levels', [BuildingLevelController::class, 'store'])->name('levels.store');
    Route::get('levels/{level}', [BuildingLevelController::class, 'show'])->name('levels.show');
    Route::get('levels/{level}/edit', [BuildingLevelController::class, 'edit'])->name('levels.edit');
    Route::put('levels/{level}', [BuildingLevelController::class, 'update'])->name('levels.update');
    Route::delete('levels/{level}', [BuildingLevelController::class, 'destroy'])->name('levels.destroy');

    //Route::resource('units', BuildingUnitController::class);
    Route::get('units', [BuildingUnitController::class, 'index'])->name('units.index');
    Route::get('units/create', [BuildingUnitController::class, 'create'])->name('units.create');
    Route::post('units', [BuildingUnitController::class, 'store'])->name('units.store');
    Route::get('units/{unit}', [BuildingUnitController::class, 'show'])->name('units.show');
    Route::get('units/{unit}/edit', [BuildingUnitController::class, 'edit'])->name('units.edit');
    Route::put('units/{unit}', [BuildingUnitController::class, 'update'])->name('units.update');
    Route::delete('units/{unit}', [BuildingUnitController::class, 'destroy'])->name('units.destroy');
    Route::delete('units/{id}/remove-picture', [BuildingUnitController::class, 'destroyImage'])->name('units.remove_picture');
    Route::get('units/details/{id}', [BuildingUnitController::class, 'getUnitData'])->name('units.details');

    //Route::resource('organizations', OrganizationController::class);
    Route::get('organizations', [OrganizationController::class, 'index'])->name('organizations.index');
    Route::get('organizations/create', [OrganizationController::class, 'create'])->name('organizations.create');
    Route::post('organizations', [OrganizationController::class, 'store'])->name('organizations.store');
    Route::get('organizations/{organization}', [OrganizationController::class, 'show'])->name('organizations.show');
    Route::get('organizations/{organization}/edit', [OrganizationController::class, 'edit'])->name('organizations.edit');
    Route::put('organizations/{organization}', [OrganizationController::class, 'update'])->name('organizations.update');
    Route::delete('organizations/{organization}', [OrganizationController::class, 'destroy'])->name('organizations.destroy');
    Route::delete('organizations/{id}/remove-picture', [OrganizationController::class, 'destroyImage'])->name('organizations.remove_picture');
    Route::get('organizations/{id}/buildings', [OrganizationController::class, 'getBuildings'])->name('organizations.buildings');

    //Owner
    Route::get('owner_manager_dashboard', [OwnerDashboardController::class, 'index'])->name('owner_manager_dashboard');
    Route::resource('owner_buildings', OwnerBuildingController::class);
    Route::get('buildings/{id}/tree', [BuildingTreeController::class, 'tree'])->name('building.tree');

    // Owner Buildings
    Route::get('owner/buildings', [BuildingController::class, 'ownerIndex'])->name('owner.buildings.index');
    Route::get('owner/buildings/create', [BuildingController::class, 'ownerCreate'])->name('owner.buildings.create');
    Route::post('owner/buildings', [BuildingController::class, 'ownerStore'])->name('owner.buildings.store');
    Route::get('owner/buildings/{building}', [BuildingController::class, 'ownerShow'])->name('owner.buildings.show');
    Route::get('owner/buildings/{building}/edit', [BuildingController::class, 'ownerEdit'])->name('owner.buildings.edit');
    Route::put('owner/buildings', [BuildingController::class, 'ownerUpdate'])->name('owner.buildings.update');
    Route::post('owner/buildings/submit', [BuildingController::class, 'submitBuilding'])->name('owner.buildings.submit');

    //Role Permissions
    Route::get('/role-permissions', [RolePermissionController::class, 'showRolePermissions'])->name('role.permissions');
    Route::post('/role-permission/toggle', [RolePermissionController::class, 'toggleRolePermission'])->name('toggle.role.permission');


});

require __DIR__.'/auth.php';
require __DIR__.'/api.php';
