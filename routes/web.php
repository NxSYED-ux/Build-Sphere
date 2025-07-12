<?php

use App\Http\Controllers\AppControllers\QueryController;
use App\Http\Controllers\GeneralControllers\CardController;
use App\Http\Controllers\GeneralControllers\ProfileController;
use App\Http\Controllers\WebControllers\AdminDashboardController;
use App\Http\Controllers\WebControllers\BuildingController;
use App\Http\Controllers\WebControllers\BuildingLevelController;
use App\Http\Controllers\WebControllers\BuildingReportController;
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
use App\Http\Controllers\WebControllers\RolePermissionController;
use App\Http\Controllers\WebControllers\RoleController;
use App\Http\Controllers\WebControllers\AssignUnitController;
use App\Http\Controllers\WebControllers\UsersController;
use Illuminate\Support\Facades\Route;

// Wrong Route
Route::fallback(function () {
    abort(404, 'Page Not Found');
});

Route::get('/back', function () {
    return redirect()->back();
})->name('back');

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

        Route::get('/stats', [AdminDashboardController::class, 'getMonthlyStats'])->name('admin.dashboard.stats');
        Route::get('/subscriptions', [AdminDashboardController::class, 'getSubscriptionPlans'])->name('admin.dashboard.subscription.plans');
        Route::get('/approvals', [AdminDashboardController::class, 'getApprovalRequests'])->name('admin.dashboard.approval.requests');
        Route::get('/revenue-growth', [AdminDashboardController::class, 'getRevenueGrowth'])->name('admin.dashboard.revenue.growth');
        Route::get('/plan-popularity', [AdminDashboardController::class, 'getPlanPopularity'])->name('admin.dashboard.plan.popularity');
        Route::get('/subscription-distribution', [AdminDashboardController::class, 'getSubscriptionDistribution'])->name('admin.dashboard.subscription.distribution');
        Route::get('/approval-timeline', [AdminDashboardController::class, 'getApprovalTimeline'])->name('admin.dashboard.approval.timeline');

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
        Route::put('/', [UsersController::class, 'adminUpdate'])->name('users.update');
        Route::put('/toggle-status', [UsersController::class, 'toggleStatus'])->name('user.toggleStatus');

    });

    Route::prefix('roles')->group(function () {

        Route::get('/', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/update', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

    });

    Route::prefix('/types')->group(function () {

        Route::get('/', [DropdownTypeController::class, 'index'])->name('types.index');
        Route::post('/', [DropdownTypeController::class, 'store'])->name('types.store');
        Route::get('/{type}/edit', [DropdownTypeController::class, 'edit'])->name('types.edit');
        Route::put('/{type}', [DropdownTypeController::class, 'update'])->name('types.update');

    });

    Route::prefix('values')->group(function () {

        Route::get('/', [DropdownValueController::class, 'index'])->name('values.index');
        Route::post('/', [DropdownValueController::class, 'store'])->name('values.store');
        Route::get('/{value}/edit', [DropdownValueController::class, 'edit'])->name('values.edit');
        Route::put('/{value}', [DropdownValueController::class, 'update'])->name('values.update');

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
        Route::get('/{level}/edit', [BuildingLevelController::class, 'edit'])->name('levels.edit');
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
        Route::get('/plan/{organization}/upgrade', [OrganizationController::class, 'adminUpgradePlanView'])->name('organizations.plan.upgrade.index');
        Route::put('/plan/upgrade', [OrganizationController::class, 'adminUpgradePlan'])->name('organizations.plan.upgrade.complete');

        Route::get('/organizations/{id}/buildings', [OrganizationController::class, 'getBuildingsAdmin'])->name('organizations.buildings');

    });

    Route::prefix('finance')->group(function () {

        Route::get('/', [FinanceController::class , 'adminIndex'])->name('finance.index');
        Route::get('/{transaction}/show', [FinanceController::class, 'adminShow'])->name('finance.show');
        Route::get('/{id}/latest', [FinanceController::class , 'latestPlatformOrganizationTransactions'])->name('finance.latest');

        Route::get('/trends', [FinanceController::class, 'adminFinancialTrends'])->name('finance.trends');
        Route::get('/chart', [FinanceController::class, 'adminFinancialChartData'])->name('finance.chart');

    });

    Route::prefix('/role-Permissions')->group(function () {

        Route::get('/', [RolePermissionController::class, 'showRolePermissions'])->name('role.permissions');
        Route::post('/toggle', [RolePermissionController::class, 'toggleRolePermission'])->name('toggle.role.permission');

    });

});

Route::prefix('owner')->middleware(['auth.jwt'])->group(function () {

    Route::prefix('dashboard')->group(function () {
        Route::get('/', [OwnerDashboardController::class, 'index'])->name('owner_manager_dashboard');

        Route::get('/stats', [OwnerDashboardController::class, 'getStats'])->name('owner_manager_dashboard.stats');
        Route::get('/finance-stats', [FinanceController::class, 'ownerFinancialTrends'])->name('owner_manager_dashboard.finance.stats');
        Route::get('/unit-occupancy', [OwnerDashboardController::class, 'getUnitOccupancy'])->name('owner_manager_dashboard.unit.occupancy');
        Route::get('/membership-plans', [OwnerDashboardController::class, 'getMembershipSubscriptionStats'])->name('owner_manager_dashboard.membership.subscription');
        Route::get('/unit-status', [OwnerDashboardController::class, 'getUnitStatus'])->name('owner_manager_dashboard.unit.status');
        Route::get('/staff-distribution', [OwnerDashboardController::class, 'getStaffDistribution'])->name('owner_manager_dashboard.staff.distribution');
        Route::get('/income-expense', [OwnerDashboardController::class, 'getIncomeExpense'])->name('owner_manager_dashboard.income.expense');
        Route::get('/membership-plan-usage', [OwnerDashboardController::class, 'getMembershipDistribution'])->name('owner_manager_dashboard.membership.subscription.distribution');

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
            Route::get('/tree', [BuildingController::class, 'tree'])->name('owner.buildings.tree');
            Route::get('/{building}/show', [BuildingController::class, 'ownerShow'])->name('owner.buildings.show');
            Route::get('/{building}/edit', [BuildingController::class, 'ownerEdit'])->name('owner.buildings.edit');
            Route::put('/', [BuildingController::class, 'ownerUpdate'])->name('owner.buildings.update');
            Route::post('/submit', [BuildingController::class, 'submitBuilding'])->name('owner.buildings.submit');
            Route::post('/reminder', [BuildingController::class, 'approvalReminder'])->name('owner.buildings.reminder');

            Route::get('/{building}/available-units', [BuildingUnitController::class, 'getAvailableBuildingUnits'])->name('owner.buildings.units.available');
            Route::get('/{building}/{type}/units', [BuildingUnitController::class, 'getBuildingUnitsByType'])->name('owner.buildings.units.byType');
            Route::get('/units', [BuildingUnitController::class, 'getAllUnits'])->name('owner.buildings.units.all');

        });

        Route::prefix('levels')->group(function () {

            Route::get('/', [BuildingLevelController::class, 'ownerIndex'])->name('owner.levels.index');
            Route::get('/create', [BuildingLevelController::class, 'ownerCreate'])->name('owner.levels.create');
            Route::post('/', [BuildingLevelController::class, 'ownerStore'])->name('owner.levels.store');
            Route::get('/{level}/edit', [BuildingLevelController::class, 'edit'])->name('owner.levels.edit');
            Route::put('/', [BuildingLevelController::class, 'ownerUpdate'])->name('owner.levels.update');

        });

        Route::prefix('units')->group(function () {

            Route::get('/', [BuildingUnitController::class, 'ownerIndex'])->name('owner.units.index');
            Route::get('/create', [BuildingUnitController::class, 'ownerCreate'])->name('owner.units.create');
            Route::post('/', [BuildingUnitController::class, 'ownerStore'])->name('owner.units.store');
            Route::get('/{unit}/show', [BuildingUnitController::class, 'ownerShow'])->name('owner.units.show');
            Route::get('/{unit}/edit', [BuildingUnitController::class, 'ownerEdit'])->name('owner.units.edit');
            Route::put('/', [BuildingUnitController::class, 'ownerUpdate'])->name('owner.units.update');
            Route::get('/{id}/details', [BuildingUnitController::class, 'unitDetails'])->name('owner.units.details');
            Route::get('/{id}/details_with_contract', [BuildingUnitController::class, 'getUnitDetailsWithActiveContract'])->name('owner.units.details.contract');

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

            Route::get('/', [FinanceController::class , 'ownerIndex'])->name('owner.finance.index');
            Route::get('/{transaction}/show', [FinanceController::class, 'ownerShow'])->name('owner.finance.show');
            Route::get('/latest', [FinanceController::class , 'latestOrganizationTransactions'])->name('owner.finance.latest');

            Route::get('/trends', [FinanceController::class, 'ownerFinancialTrends'])->name('owner.finance.trends');
            Route::get('/chart', [FinanceController::class, 'ownerFinancialChartData'])->name('owner.finance.chart');

        });


        Route::prefix('staff')->group(function () {

            Route::get('/', [hrController::class , 'staffIndex'])->name('owner.staff.index');
            Route::get('/create', [hrController::class , 'staffCreate'])->name('owner.staff.create');
            Route::post('/', [hrController::class , 'staffStore'])->name('owner.staff.store');
            Route::get('/{staff}/show', [hrController::class , 'staffShow'])->name('owner.staff.show');
            Route::get('/{staff}/edit', [hrController::class , 'staffEdit'])->name('owner.staff.edit');
            Route::put('/', [hrController::class , 'staffUpdate'])->name('owner.staff.update');

            Route::delete('/', [hrController::class , 'staffDestroy'])->name('owner.staff.destroy');
            Route::put('/handle-Queries', [hrController::class , 'staffHandleQueries'])->name('owner.staff.handle.queries');

            Route::get('/promote/{id}', [hrController::class , 'promotionGet'])->name('owner.staff.promote.index');
            Route::post('/promote', [hrController::class , 'promotion'])->name('owner.staff.promote.store');

            Route::get('/queries/{staff}/yearly-stats', [QueryController::class, 'getStaffYearlyStats'])->name('owner.staff.queries.yearly');
            Route::get('/queries/{staff}/monthly-stats', [QueryController::class, 'getStaffMonthlyStats'])->name('owner.staff.queries.monthly');
            Route::get('/query/details/{id}', [QueryController::class, 'getQueryDetails'])->name('owner.staff.query.details');

        });

        Route::prefix('managers')->group(function () {

            Route::get('/', [hrController::class , 'managerIndex'])->name('owner.managers.index');
            Route::get('/create', [hrController::class , 'managerCreate'])->name('owner.managers.create');
            Route::post('/', [hrController::class , 'managerStore'])->name('owner.managers.store');
            Route::get('/{manager}/edit', [hrController::class , 'managerEdit'])->name('owner.managers.edit');
            Route::put('/', [hrController::class , 'managerUpdate'])->name('owner.managers.update');
            Route::delete('/', [hrController::class , 'managerDestroy'])->name('owner.managers.destroy');

            Route::get('/demotion/{id}', [hrController::class , 'demotionGet'])->name('owner.managers.demote.index');
            Route::post('/demotion', [hrController::class , 'demotion'])->name('owner.managers.demote.store');

            Route::get('/{manager}/show', [hrController::class , 'managerShow'])->name('owner.managers.show');
            Route::get('/{manager}/occupancy-stats', [BuildingController::class , 'getManagerBuildingsOccupancyStats'])->name('owner.managers.occupancy.stats');
            Route::get('/{manager}/monthly/financial/stats', [FinanceController::class , 'getManagerBuildingsMonthlyStats'])->name('owner.managers.monthlyFinancial.stats');

        });

        Route::prefix('reports')->group(function () {

            Route::get('/', [ReportsController::class, 'index'])->name('owner.reports.index');

            Route::prefix('buildings')->group(function () {

                Route::get('/details', [BuildingController::class, 'getBuildingDetails'])->name('owner.reports.building.details');
                Route::get('/finance', [ReportsController::class, 'getFinance'])->name('owner.reports.buildings.finance');
                Route::get('/occupancy', [ReportsController::class, 'getBuildingOccupancy'])->name('owner.reports.buildings.occupancy');
                Route::get('/staff', [OwnerDashboardController::class, 'getStaffDistribution'])->name('owner.reports.buildings.staff');
                Route::get('/memberships', [ReportsController::class, 'getMembershipsStats'])->name('owner.reports.buildings.memberships');
                Route::get('/maintenance', [ReportsController::class, 'getMaintenanceRequests'])->name('owner.reports.buildings.maintenance');

            });

            Route::prefix('units')->group(function () {

                Route::get('/{id}/details', [BuildingUnitController::class, 'getUnitReportDetails'])->name('owner.reports.units.details');
                Route::get('/finance', [ReportsController::class, 'getFinance'])->name('owner.reports.units.finance');
                Route::get('/maintenance', [ReportsController::class, 'getUnitMaintenanceData'])->name('owner.reports.units.maintenance');
                Route::get('/contacts', [ReportsController::class, 'getPeriodContracts'])->name('owner.reports.units.contacts');

            });
        });

        Route::prefix('memberships')->group(function () {

            Route::get('/', [MembershipController::class , 'index'])->name('owner.memberships.index');
            Route::get('/create', [MembershipController::class , 'create'])->name('owner.memberships.create');
            Route::post('/', [MembershipController::class , 'store'])->name('owner.memberships.store');
            Route::get('/{membership}/edit', [MembershipController::class , 'edit'])->name('owner.memberships.edit');
            Route::put('/', [MembershipController::class , 'update'])->name('owner.memberships.update');
            Route::get('/{membership}/show', [MembershipController::class , 'show'])->name('owner.memberships.show');
            Route::get('/{membership}/assign', [MembershipController::class , 'assignMembershipView'])->name('owner.memberships.assign.view');
            Route::post('/assign', [MembershipController::class , 'assignMembership'])->name('owner.memberships.assign');
            Route::put('/toggle-featured', [MembershipController::class, 'toggleFeatured'])->name('owner.memberships.toggle.featured');
            Route::post('/mark-payment-received', [MembershipController::class, 'markAsPaymentReceived'])->name('owner.memberships.planPaymentReceived');

        });

        Route::prefix('property_users')->group(function () {

            Route::get('/', [PropertyUsersController::class , 'index'])->name('owner.property.users.index');
            Route::get('/{user}/show', [PropertyUsersController::class , 'show'])->name('owner.property.users.show');
            Route::get('/{user}/edit', [UsersController::class, 'ownerEdit'])->name('owner.property.users.edit');
            Route::put('/', [UsersController::class, 'ownerUpdate'])->name('owner.property.users.update');
            Route::put('/contract-status', [PropertyUsersController::class, 'updateRenewStatus'])->name('owner.property.users.contractStatus');
            Route::get('/{id}/contract', [PropertyUsersController::class, 'editContract'])->name('owner.property.users.contract.edit');
            Route::put('/contract', [PropertyUsersController::class, 'updateContract'])->name('owner.property.users.contract.update');
            Route::post('/mark-payment-received', [PropertyUsersController::class, 'markAsPaymentReceived'])->name('owner.property.users.rentalPaymentReceived');

        });

    });

});

require __DIR__.'/auth.php';
require __DIR__.'/api.php';
