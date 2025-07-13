<?php

use App\Http\Controllers\AppControllers\QueryController;
use App\Http\Controllers\GeneralControllers\CardController;
use App\Http\Controllers\GeneralControllers\ProfileController;
use App\Http\Controllers\WebControllers\AdminDashboardController;
use App\Http\Controllers\WebControllers\BuildingController;
use App\Http\Controllers\WebControllers\BuildingLevelController;
use App\Http\Controllers\WebControllers\BuildingUnitController;
use App\Http\Controllers\WebControllers\CheckOutController;
use App\Http\Controllers\WebControllers\DepartmentController;
use App\Http\Controllers\WebControllers\DropdownTypeController;
use App\Http\Controllers\WebControllers\DropdownValueController;
use App\Http\Controllers\WebControllers\FinanceController;
use App\Http\Controllers\WebControllers\hrController;
use App\Http\Controllers\WebControllers\landingController;
use App\Http\Controllers\WebControllers\MembershipController;
use App\Http\Controllers\WebControllers\OrganizationController;
use App\Http\Controllers\WebControllers\OwnerDashboardController;
use App\Http\Controllers\WebControllers\PlanController;
use App\Http\Controllers\WebControllers\PropertyUsersController;
use App\Http\Controllers\WebControllers\ReportsController;
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
    Route::get('/about', [landingController::class, 'aboutUs'])->name('about');
    Route::get('/contact', [landingController::class, 'contactUs'])->name('contact');
    Route::get('/plans/active/{planCycle}', [PlanController::class, 'activePlans'])->name('plans');

});


// Checkout
Route::prefix('checkout')->group(function () {

    Route::get('/', [CheckOutController::class, 'checkoutIndex'])->name('checkout');
    Route::post('/', [CheckOutController::class, 'createCheckOut'])->name('checkout.processing');
    Route::post('/complete', [CheckOutController::class, 'createCheckoutComplete'])->name('checkout.processing.complete');

});


// Routs without permissions
Route::middleware(['auth.jwt'])->group(function () {

    Route::delete('/buildings/{id}/remove-picture', [BuildingController::class, 'destroyImage'])->name('buildings.remove_picture');
    Route::delete('/building_documents/{id}', [BuildingController::class, 'removeDocument'])->name('building_documents.removeDocument');
    Route::put('/building_document/{id}', [BuildingController::class, 'updateDocument'])->name('building_document.update');
    Route::get('/building_document/{id}', [BuildingController::class, 'getDocument'])->name('building_document.edit');

    Route::get('/buildings/{building}/available-units', [BuildingUnitController::class, 'getAvailableBuildingUnits'])->name('owner.buildings.units.available');
    Route::get('buildings//{building}/{type}/units', [BuildingUnitController::class, 'getBuildingUnitsByType'])->name('owner.buildings.units.byType');
    Route::get('/buildings/units', [BuildingUnitController::class, 'getAllUnits'])->name('owner.buildings.units.all');
    Route::get('/buildings/{id}/levels', [BuildingController::class, 'getLevels'])->name('buildings.levels');

    Route::delete('/organizations/{id}/remove-picture', [OrganizationController::class, 'destroyImage'])->name('organizations.remove_picture');
    Route::get('/organizations/{id}/buildings', [OrganizationController::class, 'getBuildingsAdmin'])->name('organizations.buildings');

    Route::delete('/units/{id}/remove-picture', [BuildingUnitController::class, 'destroyImage'])->name('units.remove_picture');
    Route::get('units/{id}/details', [BuildingUnitController::class, 'unitDetails'])->name('owner.units.details');

});


// Admin
Route::prefix('admin')->middleware(['auth.jwt'])->group(function () {

    Route::prefix('dashboard')->group(function () {

        Route::get('/', [AdminDashboardController::class, 'index'])->name('admin_dashboard');

        Route::middleware('check.permission:Admin Dashboard Stats')->group(function () {
            Route::get('/stats', [AdminDashboardController::class, 'getMonthlyStats'])->name('admin.dashboard.stats');
        });

        Route::middleware('check.permission:Subscription Plans Chart')->group(function () {
            Route::get('/subscriptions', [AdminDashboardController::class, 'getSubscriptionPlans'])->name('admin.dashboard.subscription.plans');
        });

        Route::middleware('check.permission:Approval Requests Chart')->group(function () {
            Route::get('/approvals', [AdminDashboardController::class, 'getApprovalRequests'])->name('admin.dashboard.approval.requests');
        });

        Route::middleware('check.permission:Revenue Growth Chart')->group(function () {
            Route::get('/revenue-growth', [AdminDashboardController::class, 'getRevenueGrowth'])->name('admin.dashboard.revenue.growth');
        });

        Route::middleware('check.permission:Plan Popularity Chart')->group(function () {
            Route::get('/plan-popularity', [AdminDashboardController::class, 'getPlanPopularity'])->name('admin.dashboard.plan.popularity');
        });

        Route::middleware('check.permission:Subscription Distribution Chart')->group(function () {
            Route::get('/subscription-distribution', [AdminDashboardController::class, 'getSubscriptionDistribution'])->name('admin.dashboard.subscription.distribution');
        });

        Route::middleware('check.permission:Approval Timeline Chart')->group(function () {
            Route::get('/approval-timeline', [AdminDashboardController::class, 'getApprovalTimeline'])->name('admin.dashboard.approval.timeline');
        });

    });

    Route::prefix('plans')->group(function () {

        Route::middleware('check.permission:Plans')->group(function () {
            Route::get('/', [PlanController::class, 'index'])->name('plans.index');

            Route::middleware('check.permission:Add Plan')->group(function () {
                Route::get('/create', [PlanController::class, 'create'])->name('plans.create');
                Route::post('/', [PlanController::class, 'store'])->name('plans.store');
            });

            Route::middleware('check.permission:View Plan Details')->group(function () {
                Route::get('/{id}/show', [PlanController::class, 'show'])->name('plans.show');
            });

            Route::middleware('check.permission:Edit Plan')->group(function () {
                Route::get('/{id}/edit', [PlanController::class, 'edit'])->name('plans.edit');
                Route::put('/', [PlanController::class, 'update'])->name('plans.update');
            });

            Route::middleware('check.permission:Delete Plan')->group(function () {
                Route::delete('/{id}', [PlanController::class, 'destroy'])->name('plans.destroy');
            });
        });

        Route::get('/custom/{planCycle}', [PlanController::class, 'activeAndCustomPlans'])->name('plans.custom');
        Route::get('/organization/{planCycle}', [PlanController::class, 'organizationPlans'])->name('plans.organization');
    });

    Route::prefix('users')->middleware(['check.permission:User Management'])->group(function () {

        Route::get('/', [UsersController::class, 'index'])->name('users.index');
        Route::get('/{user}/show', [UsersController::class, 'show'])->name('users.show');
        Route::put('/toggle-status', [UsersController::class, 'toggleStatus'])->name('user.toggleStatus');

        Route::middleware('check.permission:Add User')->group(function () {
            Route::get('/create', [UsersController::class, 'create'])->name('users.create');
            Route::post('/', [UsersController::class, 'store'])->name('users.store');
        });

        Route::middleware('check.permission:Edit User')->group(function () {
            Route::get('/{user}/edit', [UsersController::class, 'edit'])->name('users.edit');
            Route::put('/', [UsersController::class, 'adminUpdate'])->name('users.update');
        });

    });

    Route::prefix('roles')->middleware(['check.permission:Role Management'])->group(function () {

        Route::get('/', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/permissions', [RoleController::class, 'showRolePermissions'])->name('role.permissions');

        Route::middleware('check.permission:Add Role')->group(function () {
            Route::get('/create', [RoleController::class, 'create'])->name('roles.create');
            Route::post('/', [RoleController::class, 'store'])->name('roles.store');
        });

        Route::middleware('check.permission:Edit Role')->group(function () {
            Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
            Route::put('/update', [RoleController::class, 'update'])->name('roles.update');
        });

        Route::middleware('check.permission:Delete Role')->group(function () {
            Route::delete('/{role}/delete', [RoleController::class, 'destroy'])->name('roles.destroy');
        });

        Route::middleware('check.permission:Manage Role Permissions')->group(function () {
            Route::post('/toggle', [RoleController::class, 'toggleRolePermission'])->name('toggle.role.permission');
        });

    });

    Route::prefix('organizations')->middleware(['check.permission:Organization Management'])->group(function () {

        Route::get('/', [OrganizationController::class, 'index'])->name('organizations.index');
        Route::put('/online-payment-status/update', [OrganizationController::class, 'adminOnlinePaymentStatus'])->name('organizations.onlinePaymentStatus.update');

        Route::middleware('check.permission:Add Organization')->group(function () {
            Route::post('/', [OrganizationController::class, 'store'])->name('organizations.store');
        });

        Route::middleware('check.permission:Edit Organization')->group(function () {
            Route::get('/{organization}/edit', [OrganizationController::class, 'edit'])->name('organizations.edit');
            Route::put('/{organization}', [OrganizationController::class, 'adminUpdate'])->name('organizations.update');
        });

        Route::middleware('check.permission:View Organization Details')->group(function () {
            Route::get('/{organization}/show', [OrganizationController::class, 'show'])->name('organizations.show');
            Route::get('finance/{id}/latest', [FinanceController::class , 'latestPlatformOrganizationTransactions'])->name('finance.latest');
            Route::put('/logo/update', [OrganizationController::class, 'adminUpdateLogo'])->name('organizations.logo.update');
            Route::put('/plan/cancel', [OrganizationController::class, 'adminCancelPlanSubscription'])->name('organizations.planSubscription.cancel');
            Route::put('/plan/resume', [OrganizationController::class, 'adminResumePlanSubscription'])->name('organizations.planSubscription.resume');
        });

        Route::middleware('check.permission:Upgrade Organization Plan')->group(function () {
            Route::get('/plan/{organization}/upgrade', [OrganizationController::class, 'adminUpgradePlanView'])->name('organizations.plan.upgrade.index');
            Route::put('/plan/upgrade', [OrganizationController::class, 'adminUpgradePlan'])->name('organizations.plan.upgrade.complete');
        });

        Route::middleware('check.permission:Record Organization Plan Payment')->group(function () {
            Route::post('/mark-payment-received', [OrganizationController::class, 'planPaymentReceived'])->name('organizations.planPaymentReceived');
        });

    });

    Route::prefix('finance')->middleware(['check.permission:Admin Finance'])->group(function () {

        Route::get('/', [FinanceController::class , 'adminIndex'])->name('finance.index');
        Route::get('/trends', [FinanceController::class, 'adminFinancialTrends'])->name('finance.trends');
        Route::get('/chart', [FinanceController::class, 'adminFinancialChartData'])->name('finance.chart');

        Route::middleware('check.permission:Admin Transaction Details')->group(function () {
            Route::get('/{transaction}/show', [FinanceController::class, 'adminShow'])->name('finance.show');
        });

    });

    Route::prefix('buildings')->middleware(['check.permission:Admin Buildings'])->group(function () {

        Route::get('/', [BuildingController::class, 'adminIndex'])->name('buildings.index');

        Route::middleware('check.permission:Admin Add Building')->group(function () {
            Route::get('/create', [BuildingController::class, 'adminCreate'])->name('buildings.create');
            Route::post('/', [BuildingController::class, 'adminStore'])->name('buildings.store');
        });

        Route::middleware('check.permission:Admin Edit Building')->group(function () {
            Route::get('/{building}/edit', [BuildingController::class, 'adminEdit'])->name('buildings.edit');
            Route::put('/', [BuildingController::class, 'adminUpdate'])->name('buildings.update');
        });

        Route::middleware('check.permission:Admin Building Details')->group(function () {
            Route::get('/{building}/show', [BuildingController::class, 'adminShow'])->name('buildings.show');
            Route::get('/{id}/unit/details', [BuildingUnitController::class, 'unitDetails'])->name('units.details');
        });

        Route::middleware('check.permission:Accept Building')->group(function () {
            Route::post('/approve', [BuildingController::class, 'approveBuilding'])->name('buildings.approve');
        });

        Route::middleware('check.permission:Reject Building')->group(function () {
            Route::post('/reject', [BuildingController::class, 'rejectBuilding'])->name('buildings.reject');
        });

        Route::middleware('check.permission:Report Remarks')->group(function () {
            Route::post('/report-remarks', [BuildingController::class, 'reportBuildingRemarks'])->name('buildings.reportRemarks');
        });

    });

    Route::prefix('levels')->middleware(['check.permission:Admin Levels'])->group(function () {

        Route::get('/', [BuildingLevelController::class, 'adminIndex'])->name('levels.index');

        Route::middleware('check.permission:Admin Add Level')->group(function () {
            Route::get('/create', [BuildingLevelController::class, 'adminCreate'])->name('levels.create');
            Route::post('/', [BuildingLevelController::class, 'adminStore'])->name('levels.store');
        });

        Route::middleware('check.permission:Admin Edit Level')->group(function () {
            Route::get('/{level}/edit', [BuildingLevelController::class, 'edit'])->name('levels.edit');
            Route::put('/', [BuildingLevelController::class, 'adminUpdate'])->name('levels.update');
        });

    });

    Route::prefix('units')->middleware(['check.permission:Admin Units'])->group(function () {

        Route::get('/', [BuildingUnitController::class, 'adminIndex'])->name('units.index');
        Route::get('/{unit}/show', [BuildingUnitController::class, 'adminShow'])->name('units.show');

        Route::middleware('check.permission:Admin Add Unit')->group(function () {
            Route::get('/create', [BuildingUnitController::class, 'adminCreate'])->name('units.create');
            Route::post('/', [BuildingUnitController::class, 'adminStore'])->name('units.store');
        });

        Route::middleware('check.permission:Admin Edit Unit')->group(function () {
            Route::get('/{unit}/edit', [BuildingUnitController::class, 'adminEdit'])->name('units.edit');
            Route::put('/', [BuildingUnitController::class, 'adminUpdate'])->name('units.update');
        });

    });

    Route::prefix('types')->middleware(['check.permission:Dropdowns'])->group(function () {

        Route::get('/', [DropdownTypeController::class, 'index'])->name('types.index');

        Route::middleware('check.permission:Add Dropdown Type')->group(function () {
            Route::post('/', [DropdownTypeController::class, 'store'])->name('types.store');
        });

        Route::middleware('check.permission:Edit Dropdown Type')->group(function () {
            Route::get('/{type}/edit', [DropdownTypeController::class, 'edit'])->name('types.edit');
            Route::put('/{type}', [DropdownTypeController::class, 'update'])->name('types.update');
        });

    });

    Route::prefix('values')->middleware(['check.permission:Dropdowns'])->group(function () {

        Route::get('/', [DropdownValueController::class, 'index'])->name('values.index');

        Route::middleware('check.permission:Add Dropdown Value')->group(function () {
            Route::post('/', [DropdownValueController::class, 'store'])->name('values.store');
        });

        Route::middleware('check.permission:Edit Dropdown Value')->group(function () {
            Route::get('/{value}/edit', [DropdownValueController::class, 'edit'])->name('values.edit');
            Route::put('/{value}', [DropdownValueController::class, 'update'])->name('values.update');
        });

    });

    Route::prefix('profile')->group(function () {

        Route::get('/', [ProfileController::class, 'adminProfile'])->name('admin.profile');
        Route::put('/', [ProfileController::class, 'updateProfileData'])->name('admin.profile.update');
        Route::put('/picture', [ProfileController::class, 'uploadProfilePic'])->name('admin.profile.picture.update');
        Route::delete('/picture', [ProfileController::class, 'deleteProfilePic'])->name('admin.profile.picture.delete');
        Route::put('/password', [ProfileController::class, 'changePassword'])->name('admin.profile.password.update');

    });

});


// Owner
Route::prefix('owner')->middleware(['auth.jwt'])->group(function () {

    Route::prefix('dashboard')->group(function () {
        Route::get('/', [OwnerDashboardController::class, 'index'])->name('owner_manager_dashboard');

        Route::middleware('check.permission:Dashboard Stats')->group(function () {
            Route::get('/stats', [OwnerDashboardController::class, 'getStats'])->name('owner_manager_dashboard.stats');
        });

        Route::middleware('check.permission:Finance Stats')->group(function () {
            Route::get('/finance-stats', [FinanceController::class, 'ownerFinancialTrends'])->name('owner_manager_dashboard.finance.stats');
        });

        Route::middleware('check.permission:Unit Occupancy Chart')->group(function () {
            Route::get('/unit-occupancy', [OwnerDashboardController::class, 'getUnitOccupancy'])->name('owner_manager_dashboard.unit.occupancy');
        });

        Route::middleware('check.permission:Membership Subscriptions Chart')->group(function () {
            Route::get('/membership-plans', [OwnerDashboardController::class, 'getMembershipSubscriptionStats'])->name('owner_manager_dashboard.membership.subscription');
        });

        Route::middleware('check.permission:Unit Status Chart')->group(function () {
            Route::get('/unit-status', [OwnerDashboardController::class, 'getUnitStatus'])->name('owner_manager_dashboard.unit.status');
        });

        Route::middleware('check.permission:Staff Distribution Chart')->group(function () {
            Route::get('/staff-distribution', [OwnerDashboardController::class, 'getStaffDistribution'])->name('owner_manager_dashboard.staff.distribution');
        });

        Route::middleware('check.permission:Income & Expense Chart')->group(function () {
            Route::get('/income-expense', [OwnerDashboardController::class, 'getIncomeExpense'])->name('owner_manager_dashboard.income.expense');
        });

        Route::middleware('check.permission:Membership Distribution Chart')->group(function () {
            Route::get('/membership-plan-usage', [OwnerDashboardController::class, 'getMembershipDistribution'])->name('owner_manager_dashboard.membership.subscription.distribution');
        });

    });

    Route::prefix('cards')->group(function () {

        Route::get('/', [CardController::class, 'index'])->name('owner.cards.index');
        Route::post('/', [CardController::class, 'store'])->name('owner.cards.store');
        Route::put('/', [CardController::class, 'update'])->name('owner.cards.update.default');
        Route::delete('/', [CardController::class, 'destroy'])->name('owner.cards.delete');

    });

    Route::prefix('plan/upgrade')->middleware(['check.permission:Update Plan'])->group(function () {

        Route::get('/', [CheckOutController::class, 'updatePlanOwnerIndex'])->name('owner.plan.upgrade.index');
        Route::post('/', [CheckOutController::class, 'updateCheckOut'])->name('owner.plan.upgrade.processing');
        Route::post('/complete', [CheckOutController::class, 'updateCheckoutComplete'])->name('owner.plan.upgrade.processing.complete');

    });

    Route::middleware(['plan'])->group(function () {

        Route::prefix('profile')->group(function () {

            Route::get('/', [ProfileController::class, 'ownerProfile'])->name('owner.profile');
            Route::put('/', [ProfileController::class, 'updateProfileData'])->name('owner.profile.update');
            Route::put('/picture', [ProfileController::class, 'uploadProfilePic'])->name('owner.profile.picture.update');
            Route::delete('/picture', [ProfileController::class, 'deleteProfilePic'])->name('owner.profile.picture.delete');
            Route::put('/password', [ProfileController::class, 'changePassword'])->name('owner.profile.password.update');

        });

        Route::prefix('organization')->middleware(['check.permission:Organization Profile'])->group(function () {

            Route::get('/', [OrganizationController::class , 'organizationProfile'])->name('owner.organization.profile');
            Route::get('/latest', [FinanceController::class , 'latestOrganizationTransactions'])->name('owner.finance.latest');
            Route::put('/logo/update', [OrganizationController::class, 'ownerUpdateLogo'])->name('owner.organization.logo.update');
            Route::put('/plan/cancel', [OrganizationController::class, 'ownerCancelPlanSubscription'])->name('owner.organization.planSubscription.cancel');
            Route::put('/plan/resume', [OrganizationController::class, 'ownerResumePlanSubscription'])->name('owner.organization.planSubscription.resume');

            Route::middleware('check.permission:Update Profile')->group(function () {
                Route::get('/edit', [OrganizationController::class , 'ownerEdit'])->name('owner.organization.edit');
                Route::put('/', [OrganizationController::class , 'ownerUpdate'])->name('owner.organization.update');
            });

            Route::middleware('check.permission:Change Online Payment Status')->group(function () {
                Route::put('/online-payment-status/update', [OrganizationController::class, 'ownerOnlinePaymentStatus'])->name('owner.organization.onlinePaymentStatus.update');
            });

        });

        Route::prefix('buildings')->middleware(['check.permission:Buildings'])->group(function () {

            Route::get('/', [BuildingController::class, 'ownerIndex'])->name('owner.buildings.index');

            Route::middleware('check.permission:Add Building')->group(function () {
                Route::get('/create', [BuildingController::class, 'ownerCreate'])->name('owner.buildings.create');
                Route::post('/', [BuildingController::class, 'ownerStore'])->name('owner.buildings.store');
            });

            Route::middleware('check.permission:Edit Building')->group(function () {
                Route::get('/{building}/edit', [BuildingController::class, 'ownerEdit'])->name('owner.buildings.edit');
                Route::put('/', [BuildingController::class, 'ownerUpdate'])->name('owner.buildings.update');
            });

            Route::middleware('check.permission:View Building Details')->group(function () {
                Route::get('/{building}/show', [BuildingController::class, 'ownerShow'])->name('owner.buildings.show');
            });

            Route::middleware('check.permission:Building Tree')->group(function () {
                Route::get('/tree', [BuildingController::class, 'tree'])->name('owner.buildings.tree');
            });

            Route::middleware('check.permission:Submit Building')->group(function () {
                Route::post('/submit', [BuildingController::class, 'submitBuilding'])->name('owner.buildings.submit');
            });

            Route::middleware('check.permission:Remind Admin')->group(function () {
                Route::post('/reminder', [BuildingController::class, 'approvalReminder'])->name('owner.buildings.reminder');
            });

        });

        Route::prefix('levels')->middleware(['check.permission:Levels'])->group(function () {

            Route::get('/', [BuildingLevelController::class, 'ownerIndex'])->name('owner.levels.index');

            Route::middleware('check.permission:Add Level')->group(function () {
                Route::get('/create', [BuildingLevelController::class, 'ownerCreate'])->name('owner.levels.create');
                Route::post('/', [BuildingLevelController::class, 'ownerStore'])->name('owner.levels.store');
            });

            Route::middleware('check.permission:Edit Level')->group(function () {
                Route::get('/{level}/edit', [BuildingLevelController::class, 'edit'])->name('owner.levels.edit');
                Route::put('/', [BuildingLevelController::class, 'ownerUpdate'])->name('owner.levels.update');
            });

        });

        Route::prefix('units')->middleware(['check.permission:Units'])->group(function () {

            Route::get('/', [BuildingUnitController::class, 'ownerIndex'])->name('owner.units.index');

            Route::middleware('check.permission:Add Unit')->group(function () {
                Route::get('/create', [BuildingUnitController::class, 'ownerCreate'])->name('owner.units.create');
                Route::post('/', [BuildingUnitController::class, 'ownerStore'])->name('owner.units.store');
            });

            Route::middleware('check.permission:Edit Unit')->group(function () {
                Route::get('/{unit}/edit', [BuildingUnitController::class, 'ownerEdit'])->name('owner.units.edit');
                Route::put('/', [BuildingUnitController::class, 'ownerUpdate'])->name('owner.units.update');
            });

            Route::middleware('check.permission:View Unit Details')->group(function () {
                Route::get('/{unit}/show', [BuildingUnitController::class, 'ownerShow'])->name('owner.units.show');
                Route::get('/{id}/details_with_contract', [BuildingUnitController::class, 'getUnitDetailsWithActiveContract'])->name('owner.units.details.contract');
            });

        });

        Route::prefix('property_users')->middleware(['check.permission:Property Users'])->group(function () {

            Route::get('/', [PropertyUsersController::class , 'index'])->name('owner.property.users.index');
            Route::get('/assign/unit', [AssignUnitController::class, 'index'])->name('owner.assignunits.index');

            Route::middleware('check.permission:Edit Property User')->group(function () {
                Route::get('/{user}/edit', [UsersController::class, 'ownerEdit'])->name('owner.property.users.edit');
                Route::put('/', [UsersController::class, 'ownerUpdate'])->name('owner.property.users.update');
            });

            Route::middleware('check.permission:View Details')->group(function () {
                Route::get('/{user}/show', [PropertyUsersController::class , 'show'])->name('owner.property.users.show');
            });

            Route::middleware('check.permission:Update Contract')->group(function () {
                Route::get('/{id}/contract', [PropertyUsersController::class, 'editContract'])->name('owner.property.users.contract.edit');
                Route::put('/contract', [PropertyUsersController::class, 'updateContract'])->name('owner.property.users.contract.update');
            });

            Route::middleware('check.permission:Update Renew Status')->group(function () {
                Route::put('/contract-status', [PropertyUsersController::class, 'updateRenewStatus'])->name('owner.property.users.contractStatus');
            });

            Route::middleware('check.permission:Record Rental Payment')->group(function () {
                Route::post('/mark-payment-received', [PropertyUsersController::class, 'markAsPaymentReceived'])->name('owner.property.users.rentalPaymentReceived');
            });

            Route::middleware('check.permission:Assign Unit')->group(function () {
                Route::post('/assign/unit', [AssignUnitController::class, 'create'])->name('owner.assignunits.store');
            });

        });

        Route::prefix('departments')->middleware(['check.permission:Departments'])->group(function () {

            Route::get('/', [DepartmentController::class , 'index'])->name('owner.departments.index');

            Route::middleware('check.permission:Add Department')->group(function () {
                Route::post('/', [DepartmentController::class , 'store'])->name('owner.departments.store');
            });

            Route::middleware('check.permission:Edit Department')->group(function () {
                Route::get('/{department}/edit', [DepartmentController::class , 'edit'])->name('owner.departments.edit');
                Route::put('/', [DepartmentController::class , 'update'])->name('owner.departments.update');
            });

            Route::middleware('check.permission:View Department Details')->group(function () {
                Route::get('/{department}/show', [DepartmentController::class, 'show'])->name('owner.departments.show');
            });

            Route::middleware('check.permission:Delete Department')->group(function () {
                Route::delete('/', [DepartmentController::class, 'destroy'])->name('owner.departments.destroy');
            });

        });

        Route::prefix('staff')->middleware(['check.permission:Staff'])->group(function () {

            Route::get('/', [hrController::class , 'staffIndex'])->name('owner.staff.index');

            Route::middleware('check.permission:Add Staff')->group(function () {
                Route::get('/create', [hrController::class , 'staffCreate'])->name('owner.staff.create');
                Route::post('/', [hrController::class , 'staffStore'])->name('owner.staff.store');
            });

            Route::middleware('check.permission:Edit Staff')->group(function () {
                Route::get('/{staff}/edit', [hrController::class , 'staffEdit'])->name('owner.staff.edit');
                Route::put('/', [hrController::class , 'staffUpdate'])->name('owner.staff.update');
                Route::put('/handle-Queries', [hrController::class , 'staffHandleQueries'])->name('owner.staff.handle.queries');
            });

            Route::middleware('check.permission:Delete Staff')->group(function () {
                Route::delete('/', [hrController::class , 'staffDestroy'])->name('owner.staff.destroy');
            });

            Route::middleware('check.permission:View Staff Details')->group(function () {
                Route::get('/{staff}/show', [hrController::class , 'staffShow'])->name('owner.staff.show');
                Route::get('/queries/{staff}/yearly-stats', [QueryController::class, 'getStaffYearlyStats'])->name('owner.staff.queries.yearly');
                Route::get('/queries/{staff}/monthly-stats', [QueryController::class, 'getStaffMonthlyStats'])->name('owner.staff.queries.monthly');
                Route::get('/query/details/{id}', [QueryController::class, 'getQueryDetails'])->name('owner.staff.query.details');
            });

            Route::middleware('check.permission:Promote Staff')->group(function () {
                Route::get('/promote/{id}', [hrController::class , 'promotionGet'])->name('owner.staff.promote.index');
                Route::post('/promote', [hrController::class , 'promotion'])->name('owner.staff.promote.store');
            });

        });

        Route::prefix('managers')->middleware(['check.permission:Managers'])->group(function () {

            Route::get('/', [hrController::class , 'managerIndex'])->name('owner.managers.index');

            Route::middleware('check.permission:Add Manager')->group(function () {
                Route::get('/create', [hrController::class , 'managerCreate'])->name('owner.managers.create');
                Route::post('/', [hrController::class , 'managerStore'])->name('owner.managers.store');
            });

            Route::middleware('check.permission:Edit Manager')->group(function () {
                Route::get('/{manager}/edit', [hrController::class , 'managerEdit'])->name('owner.managers.edit');
                Route::put('/', [hrController::class , 'managerUpdate'])->name('owner.managers.update');
            });

            Route::middleware('check.permission:Delete Manager')->group(function () {
                Route::delete('/', [hrController::class , 'managerDestroy'])->name('owner.managers.destroy');
            });

            Route::middleware('check.permission:Demote Manager')->group(function () {
                Route::get('/demotion/{id}', [hrController::class , 'demotionGet'])->name('owner.managers.demote.index');
                Route::post('/demotion', [hrController::class , 'demotion'])->name('owner.managers.demote.store');
            });

            Route::middleware('check.permission:View Manager Details')->group(function () {
                Route::get('/{manager}/show', [hrController::class , 'managerShow'])->name('owner.managers.show');
                Route::get('/{manager}/occupancy-stats', [BuildingController::class , 'getManagerBuildingsOccupancyStats'])->name('owner.managers.occupancy.stats');
                Route::get('/{manager}/monthly/financial/stats', [FinanceController::class , 'getManagerBuildingsMonthlyStats'])->name('owner.managers.monthlyFinancial.stats');
            });

        });

        Route::prefix('memberships')->middleware(['check.permission:Memberships'])->group(function () {

            Route::get('/', [MembershipController::class , 'index'])->name('owner.memberships.index');

            Route::middleware('check.permission:Add Membership')->group(function () {
                Route::get('/create', [MembershipController::class , 'create'])->name('owner.memberships.create');
                Route::post('/', [MembershipController::class , 'store'])->name('owner.memberships.store');
            });

            Route::middleware('check.permission:Edit Membership')->group(function () {
                Route::get('/{membership}/edit', [MembershipController::class , 'edit'])->name('owner.memberships.edit');
                Route::put('/', [MembershipController::class , 'update'])->name('owner.memberships.update');
            });

            Route::middleware('check.permission:View Membership Details')->group(function () {
                Route::get('/{membership}/show', [MembershipController::class , 'show'])->name('owner.memberships.show');
            });

            Route::middleware('check.permission:Assign Membership')->group(function () {
                Route::get('/{membership}/assign', [MembershipController::class , 'assignMembershipView'])->name('owner.memberships.assign.view');
                Route::post('/assign', [MembershipController::class , 'assignMembership'])->name('owner.memberships.assign');
            });

            Route::middleware('check.permission:Feature Membership')->group(function () {
                Route::put('/toggle-featured', [MembershipController::class, 'toggleFeatured'])->name('owner.memberships.toggle.featured');
            });

            Route::middleware('check.permission:Record Membership Payment')->group(function () {
                Route::post('/mark-payment-received', [MembershipController::class, 'markAsPaymentReceived'])->name('owner.memberships.planPaymentReceived');
            });

        });

        Route::prefix('finance')->middleware(['check.permission:Finance'])->group(function () {

            Route::get('/', [FinanceController::class , 'ownerIndex'])->name('owner.finance.index');
            Route::get('/trends', [FinanceController::class, 'ownerFinancialTrends'])->name('owner.finance.trends');
            Route::get('/chart', [FinanceController::class, 'ownerFinancialChartData'])->name('owner.finance.chart');

            Route::middleware('check.permission:View Transaction Details')->group(function () {
                Route::get('/{transaction}/show', [FinanceController::class, 'ownerShow'])->name('owner.finance.show');
            });

        });

        Route::prefix('reports')->middleware(['check.permission:Reports'])->group(function () {

            Route::get('/', [ReportsController::class, 'index'])->name('owner.reports.index');

            Route::prefix('buildings')->middleware(['check.permission:Building Report'])->group(function () {

                Route::get('/details', [BuildingController::class, 'getBuildingDetails'])->name('owner.reports.building.details');
                Route::get('/finance', [ReportsController::class, 'getFinance'])->name('owner.reports.buildings.finance');
                Route::get('/occupancy', [ReportsController::class, 'getBuildingOccupancy'])->name('owner.reports.buildings.occupancy');
                Route::get('/staff', [OwnerDashboardController::class, 'getStaffDistribution'])->name('owner.reports.buildings.staff');
                Route::get('/memberships', [ReportsController::class, 'getMembershipsStats'])->name('owner.reports.buildings.memberships');
                Route::get('/maintenance', [ReportsController::class, 'getMaintenanceRequests'])->name('owner.reports.buildings.maintenance');

            });

            Route::prefix('units')->middleware(['check.permission:Unit Report'])->group(function () {

                Route::get('/{id}/details', [BuildingUnitController::class, 'getUnitReportDetails'])->name('owner.reports.units.details');
                Route::get('/finance', [ReportsController::class, 'getFinance'])->name('owner.reports.units.finance');
                Route::get('/maintenance', [ReportsController::class, 'getUnitMaintenanceData'])->name('owner.reports.units.maintenance');
                Route::get('/contacts', [ReportsController::class, 'getPeriodContracts'])->name('owner.reports.units.contacts');

            });
        });

    });

});


require __DIR__.'/auth.php';
require __DIR__.'/api.php';
