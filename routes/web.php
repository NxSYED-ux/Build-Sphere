<?php

use App\Http\Controllers\WebControllers\AdminDashboardController;
use App\Http\Controllers\WebControllers\AuthController;
use App\Http\Controllers\WebControllers\BuildingController;
use App\Http\Controllers\WebControllers\BuildingLevelController;
use App\Http\Controllers\WebControllers\BuildingTreeController;
use App\Http\Controllers\WebControllers\BuildingUnitController;
use App\Http\Controllers\WebControllers\DropdownTypeController;
use App\Http\Controllers\WebControllers\DropdownValueController;
use App\Http\Controllers\WebControllers\ForgotPasswordController;
use App\Http\Controllers\WebControllers\OrganizationController;
use App\Http\Controllers\WebControllers\OwnerBuildingController;
use App\Http\Controllers\WebControllers\OwnerDashboardController;
use App\Http\Controllers\WebControllers\ProfileController;
use App\Http\Controllers\WebControllers\RoleController;
use App\Http\Controllers\WebControllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

// Authentication routes
Route::get('login', [AuthController::class, 'create'])->name('login');
Route::post('login', [AuthController::class, 'store'])->name('login');

Route::get('forget-password', [ForgotPasswordController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('forget-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');

Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

Route::fallback(function () {
    return back();
});

Route::middleware(['auth.jwt:cookie'])->group(function () {

    // Admin & Owner
    Route::post('logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('admin_profile', [ProfileController::class, 'index'])->name('admin_profile');
    Route::put('profile/update/{id}', [ProfileController::class, 'updatePersonal'])->name('users.profile.update');

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
    Route::put('users/{user}', [UsersController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UsersController::class, 'destroy'])->name('users.destroy');

    // Route::resource('roles', RoleController::class);
    Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show');
    Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
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
    Route::get('buildings', [BuildingController::class, 'index'])->name('buildings.index');
    Route::get('buildings/create', [BuildingController::class, 'create'])->name('buildings.create');
    Route::post('buildings', [BuildingController::class, 'store'])->name('buildings.store');
    Route::get('buildings/{building}', [BuildingController::class, 'show'])->name('buildings.show');
    Route::get('buildings/{building}/edit', [BuildingController::class, 'edit'])->name('buildings.edit');
    Route::put('buildings/{building}', [BuildingController::class, 'update'])->name('buildings.update');
    Route::delete('buildings/{building}', [BuildingController::class, 'destroy'])->name('buildings.destroy');
    Route::delete('/buildings/{id}/remove-picture', [BuildingController::class, 'destroyImage'])->name('buildings.remove_picture');
    Route::delete('/building_documents/{id}', [BuildingController::class, 'removeDocument'])->name('building_documents.removeDocument');
    Route::put('building_document/{id}', [BuildingController::class, 'updateDocument'])->name('building_document.update');
    Route::get('building_document/{id}', [BuildingController::class, 'getDocument'])->name('building_document.edit');
    Route::get('buildings/{id}/levels', [BuildingController::class, 'getLevels'])->name('buildings.levels');

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

});


require __DIR__.'/auth.php';
require __DIR__.'/api.php';
