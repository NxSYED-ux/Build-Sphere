<?php

use App\Http\Controllers\GeneralControllers\CardController;
use App\Http\Controllers\GeneralControllers\ProfileController;
use App\Http\Controllers\WebControllers\AdminDashboardController;
use App\Http\Controllers\WebControllers\BuildingController;
use App\Http\Controllers\WebControllers\BuildingLevelController;
use App\Http\Controllers\WebControllers\BuildingTreeController;
use App\Http\Controllers\WebControllers\BuildingUnitController;
use App\Http\Controllers\WebControllers\CheckOutController;
use App\Http\Controllers\WebControllers\DepartmentController;
use App\Http\Controllers\WebControllers\DropdownTypeController;
use App\Http\Controllers\WebControllers\DropdownValueController;
use App\Http\Controllers\WebControllers\FinanceController;
use App\Http\Controllers\WebControllers\landingController;
use App\Http\Controllers\WebControllers\OrganizationController;
use App\Http\Controllers\WebControllers\OwnerDashboardController;
use App\Http\Controllers\WebControllers\PlanController;
use App\Http\Controllers\WebControllers\RolePermissionController;
use App\Http\Controllers\WebControllers\RoleController;
use App\Http\Controllers\WebControllers\AssignUnitController;
use App\Http\Controllers\WebControllers\UsersController;
use Illuminate\Support\Facades\Route;

// Wrong Route
Route::fallback(function () {
    abort(404, 'Page Not Found');
});

// Website Home Screen
Route::prefix('')->group(function () {

    Route::get('/', [landingController::class, 'index'])->name('index');
    Route::get('/index', [landingController::class, 'index']);
    Route::get('/plans/active/{planCycle}', [PlanController::class, 'activePlans'])->name('plans');

});

// Checkout
Route::prefix('checkout')->group(function () {

    Route::get('/', [CheckOutController::class, 'checkoutIndex'])->name('checkout');
    Route::post('/', [CheckOutController::class, 'createCheckOut'])->name('checkout.processing');
    Route::post('/complete', [CheckOutController::class, 'createCheckoutComplete'])->name('checkout.processing.complete');

});

Route::middleware(['auth.jwt'])->group(function () {

    Route::delete('/buildings/{id}/remove-picture', [BuildingController::class, 'destroyImage'])->name('buildings.remove_picture');
    Route::delete('/building_documents/{id}', [BuildingController::class, 'removeDocument'])->name('building_documents.removeDocument');
    Route::put('/building_document/{id}', [BuildingController::class, 'updateDocument'])->name('building_document.update');
    Route::get('/building_document/{id}', [BuildingController::class, 'getDocument'])->name('building_document.edit');

    Route::get('/buildings/{id}/levels', [BuildingController::class, 'getLevels'])->name('buildings.levels');

    Route::delete('/units/{id}/remove-picture', [BuildingUnitController::class, 'destroyImage'])->name('units.remove_picture');

    Route::delete('/organizations/{id}/remove-picture', [OrganizationController::class, 'destroyImage'])->name('organizations.remove_picture');

});

Route::prefix('admin')->middleware(['auth.jwt'])->group(function () {

    Route::prefix('dashboard')->group(function () {

        Route::get('/', [AdminDashboardController::class, 'index'])->name('admin_dashboard');
        Route::get('/data', [AdminDashboardController::class, 'data'])->name('admin_dashboard.data');

    });

    Route::prefix('plans')->group(function () {

        Route::get('/', [PlanController::class, 'index'])->name('plans.index');
        Route::get('/create', [PlanController::class, 'create'])->name('plans.create');
        Route::post('/', [PlanController::class, 'store'])->name('plans.store');
        Route::get('/{id}/show', [PlanController::class, 'show'])->name('plans.show');
        Route::get('/{id}/edit', [PlanController::class, 'edit'])->name('plans.edit');
        Route::put('/', [PlanController::class, 'update'])->name('plans.update');
        Route::delete('/{id}', [PlanController::class, 'destroy'])->name('plans.destroy');

        Route::get('/custom/{planCycle}', [PlanController::class, 'activeAndCustomPlans'])->name('plans.custom');
        Route::get('/organization/{planCycle}', [PlanController::class, 'organizationPlans'])->name('plans.organization');
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
        Route::post('/report-remarks', [BuildingController::class, 'reportBuildingRemarks'])->name('buildings.reportRemarks');

    });

    Route::prefix('levels')->group(function () {

        Route::get('/', [BuildingLevelController::class, 'adminIndex'])->name('levels.index');
        Route::get('/create', [BuildingLevelController::class, 'adminCreate'])->name('levels.create');
        Route::post('/', [BuildingLevelController::class, 'adminStore'])->name('levels.store');
        Route::get('/{level}/show', [BuildingLevelController::class, 'show'])->name('levels.show');
        Route::get('/{level}/edit', [BuildingLevelController::class, 'adminEdit'])->name('levels.edit');
        Route::put('/', [BuildingLevelController::class, 'adminUpdate'])->name('levels.update');

    });

    Route::prefix('units')->group(function () {

        Route::get('/', [BuildingUnitController::class, 'adminIndex'])->name('units.index');
        Route::get('/create', [BuildingUnitController::class, 'adminCreate'])->name('units.create');
        Route::post('/', [BuildingUnitController::class, 'adminStore'])->name('units.store');
        Route::get('/{unit}/show', [BuildingUnitController::class, 'adminShow'])->name('units.show');
        Route::get('/{unit}/edit', [BuildingUnitController::class, 'adminEdit'])->name('units.edit');
        Route::put('/', [BuildingUnitController::class, 'adminUpdate'])->name('units.update');
        Route::get('/details/{id}', [BuildingUnitController::class, 'unitDetails'])->name('units.details');

    });

    Route::prefix('organizations')->group(function () {

        Route::get('/', [OrganizationController::class, 'index'])->name('organizations.index');
        Route::get('/create', [OrganizationController::class, 'create'])->name('organizations.create');
        Route::post('/', [OrganizationController::class, 'store'])->name('organizations.store');
        Route::get('/{organization}/show', [OrganizationController::class, 'show'])->name('organizations.show');
        Route::get('/{organization}/edit', [OrganizationController::class, 'edit'])->name('organizations.edit');
        Route::put('/{organization}', [OrganizationController::class, 'adminUpdate'])->name('organizations.update');
        Route::put('/logo/update', [OrganizationController::class, 'adminUpdateLogo'])->name('organizations.logo.update');
        Route::put('/online-payment-status/update', [OrganizationController::class, 'adminOnlinePaymentStatus'])->name('organizations.onlinePaymentStatus.update');
        Route::post('/mark-payment-received', [OrganizationController::class, 'planPaymentReceived'])->name('organizations.planPaymentReceived');
        Route::put('/plan/cancel', [OrganizationController::class, 'adminCancelPlanSubscription'])->name('organizations.planSubscription.cancel');
        Route::put('/plan/resume', [OrganizationController::class, 'adminResumePlanSubscription'])->name('organizations.planSubscription.resume');
        Route::get('/plan/{organization}/upgrade', [CheckOutController::class, 'updatePlanAdminIndex'])->name('organizations.plan.upgrade.index');
        Route::put('/plan/upgrade', [CheckOutController::class, 'adminUpgradePlan'])->name('organizations.plan.upgrade.complete');

        Route::get('/organizations/{id}/buildings', [OrganizationController::class, 'getBuildingsAdmin'])->name('organizations.buildings');

    });

    Route::prefix('finance')->group(function () {

        Route::get('/', [FinanceController::class , 'index'])->name('finance.index');
        Route::get('/{department}/show', [FinanceController::class, 'show'])->name('finance.show');
        Route::get('/latest', [FinanceController::class , 'latestPlatformOrganizationTransactions'])->name('finance.latest');

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

    Route::prefix('plan/upgrade')->group(function () {

        Route::get('/', [CheckOutController::class, 'updatePlanOwnerIndex'])->name('owner.plan.upgrade.index');
        Route::post('/', [CheckOutController::class, 'updateCheckOut'])->name('owner.plan.upgrade.processing');
        Route::post('/complete', [CheckOutController::class, 'updateCheckoutComplete'])->name('owner.plan.upgrade.processing.complete');

    });

    Route::prefix('cards')->group(function () {

        Route::get('/', [CardController::class, 'index'])->name('owner.cards.index');
        Route::post('/', [CardController::class, 'store'])->name('owner.cards.store');
        Route::put('/', [CardController::class, 'update'])->name('owner.cards.update.default');
        Route::delete('/', [CardController::class, 'destroy'])->name('owner.cards.delete');

    });

    Route::middleware(['plan'])->group(function () {

        Route::prefix('organization')->group(function () {

            Route::get('/', [OrganizationController::class , 'organizationProfile'])->name('owner.organization.profile');
            Route::get('/edit', [OrganizationController::class , 'ownerEdit'])->name('owner.organization.edit');
            Route::put('/', [OrganizationController::class , 'ownerUpdate'])->name('owner.organization.update');
            Route::put('/logo/update', [OrganizationController::class, 'ownerUpdateLogo'])->name('owner.organization.logo.update');
            Route::put('/online-payment-status/update', [OrganizationController::class, 'ownerOnlinePaymentStatus'])->name('owner.organization.onlinePaymentStatus.update');
            Route::put('/plan/cancel', [OrganizationController::class, 'ownerCancelPlanSubscription'])->name('owner.organization.planSubscription.cancel');
            Route::put('/plan/resume', [OrganizationController::class, 'ownerResumePlanSubscription'])->name('owner.organization.planSubscription.resume');

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
            Route::get('/{building}/available', [BuildingUnitController::class, 'getAvailableBuildingUnits'])->name('owner.buildings.units.available');

        });

        Route::prefix('levels')->group(function () {

            Route::get('/', [BuildingLevelController::class, 'ownerIndex'])->name('owner.levels.index');
            Route::get('/create', [BuildingLevelController::class, 'ownerCreate'])->name('owner.levels.create');
            Route::post('/', [BuildingLevelController::class, 'ownerStore'])->name('owner.levels.store');
            Route::get('/{level}/show', [BuildingLevelController::class, 'show'])->name('owner.levels.show');
            Route::get('/{level}/edit', [BuildingLevelController::class, 'ownerEdit'])->name('owner.levels.edit');
            Route::put('/', [BuildingLevelController::class, 'ownerUpdate'])->name('owner.levels.update');

        });

        Route::prefix('units')->group(function () {

            Route::get('/', [BuildingUnitController::class, 'ownerIndex'])->name('owner.units.index');
            Route::get('/create', [BuildingUnitController::class, 'ownerCreate'])->name('owner.units.create');
            Route::post('/', [BuildingUnitController::class, 'ownerStore'])->name('owner.units.store');
            Route::get('/{unit}/show', [BuildingUnitController::class, 'ownerShow'])->name('owner.units.show');
            Route::get('/{unit}/edit', [BuildingUnitController::class, 'ownerEdit'])->name('owner.units.edit');
            Route::put('/', [BuildingUnitController::class, 'ownerUpdate'])->name('owner.units.update');
            Route::get('/details/{id}', [BuildingUnitController::class, 'unitDetails'])->name('owner.units.details');
            Route::get('/details/{id}/contract', [BuildingUnitController::class, 'getUnitDetailsWithActiveContract'])->name('owner.units.details.contract');

        });

        Route::prefix('assign-units')->group(function () {

            Route::get('/', [AssignUnitController::class, 'index'])->name('owner.assignunits.index');
            Route::post('/', [AssignUnitController::class, 'create'])->name('owner.assignunits.store');

        });

        Route::prefix('departments')->group(function () {

            Route::get('/', [DepartmentController::class , 'index'])->name('owner.departments.index');
            Route::post('/', [DepartmentController::class , 'store'])->name('owner.departments.store');
            Route::get('/{department}/edit', [DepartmentController::class , 'edit'])->name('owner.departments.edit');
            Route::put('/', [DepartmentController::class , 'update'])->name('owner.departments.update');
            Route::get('/{department}/show', [DepartmentController::class, 'show'])->name('owner.departments.show');
            Route::delete('/', [DepartmentController::class, 'destroy'])->name('owner.departments.destroy');

        });

        Route::prefix('finance')->group(function () {

            Route::get('/', [FinanceController::class , 'index'])->name('owner.finance.index');
            Route::get('/{department}/show', [FinanceController::class, 'show'])->name('owner.finance.show');
            Route::get('/latest', [FinanceController::class , 'latestOrganizationTransactions'])->name('owner.finance.latest');

        });

    });

});

require __DIR__.'/auth.php';
require __DIR__.'/api.php';
