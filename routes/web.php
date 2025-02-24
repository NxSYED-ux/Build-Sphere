<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\RoleController; 
use App\Http\Controllers\AdminDashboardController; 
use App\Http\Controllers\OwnerDashboardController; 
use App\Http\Controllers\BuildingController; 
use App\Http\Controllers\BuildingLevelController; 
use App\Http\Controllers\BuildingUnitController; 
use App\Http\Controllers\DropdownTypeController; 
use App\Http\Controllers\DropdownValueController; 
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrganizationController; 
use App\Http\Controllers\BuildingTreeController; 
use App\Http\Controllers\OwnerBuildingController; 

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

Route::get('/building/unit/details/{id}', [BuildingUnitController::class, 'getUnitData'])->name('building.unit.details');

Route::middleware(['auth.jwt:cookie'])->group(function () {

    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');  
    Route::get('/admin_dashboard', [AdminDashboardController::class, 'index'])->name('admin_dashboard');   
    Route::get('/owner_manager_dashboard', [OwnerDashboardController::class, 'index'])->name('owner_manager_dashboard');   
    Route::get('/admin_dashboard_data', [AdminDashboardController::class, 'data'])->name('admin_dashboard.data'); 

    Route::get('/admin_profile', [ProfileController::class, 'index'])->name('admin_profile');
    Route::put('/profile/update/{id}', [ProfileController::class, 'updatePersonal'])->name('users.profile.update');

    Route::resource('users', UsersController::class);  
    Route::resource('roles', RoleController::class);  
    Route::resource('types', DropdownTypeController::class);
    Route::resource('values', DropdownValueController::class);
    Route::resource('buildings', BuildingController::class);
    Route::resource('levels', BuildingLevelController::class);
    Route::resource('units', BuildingUnitController::class);
    Route::resource('organizations', OrganizationController::class);
    Route::resource('owner_buildings', OwnerBuildingController::class);

    Route::get('/organizations/{id}/buildings', [BuildingUnitController::class, 'getBuildings'])->name('organization.buildings'); 
    Route::get('/buildings/{id}/levels', [BuildingUnitController::class, 'getLevels'])->name('building.levels');
    Route::get('/buildings/{id}/tree', [BuildingTreeController::class, 'tree'])->name('building.tree');
    Route::put('/building_document/{id}', [BuildingController::class, 'updateDocument'])->name('building_document.update'); 
    Route::get('/building_document/{id}', [BuildingController::class, 'getDocument'])->name('building_document.edit'); 

    Route::delete('/organizations/{id}/remove-picture', [OrganizationController::class, 'destroyImage'])->name('organizations.remove_picture'); 
    Route::delete('/buildings/{id}/remove-picture', [BuildingController::class, 'destroyImage'])->name('buildings.remove_picture'); 
    Route::delete('/units/{id}/remove-picture', [BuildingUnitController::class, 'destroyImage'])->name('units.remove_picture'); 
    Route::delete('/building_documents/{id}', [BuildingController::class, 'removeDocument'])->name('building_documents.removeDocument'); 

});
 

require __DIR__.'/auth.php';  
 