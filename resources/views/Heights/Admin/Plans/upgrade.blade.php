@extends('layouts.app')

@section('title', 'Upgrade Plan')

@push('styles')

    <style>

        :root {
            --secondary-color: #1cc88a;
            --payment-card-primary: var(--color-blue);
            --payment-card-primary-hover: var(--color-blue);
            --payment-card-error: #dc2626;
            --payment-card-text: var(--sidenavbar-text-color);
            --payment-card-text-light: var(--sidenavbar-text-color);
            --payment-card-border: #e2e8f0;
            --payment-card-border-hover: #cbd5e1;
            --payment-card-background: var(--body-card-bg);
            --payment-card-input-background2: var(--body-background-color);
            --payment-card-radius: 10px;
            --payment-card-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
            --payment-card-transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
        }
        #main {
            margin-top: 45px;
        }

        /* Card Styles */
        .detail-card {
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: none;
            transition: all 0.3s ease;
        }
        .detail-card:hover {
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        /* Form Styles */
        .form-section {
            background: var(--body-card-bg);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .form-section h5 {
            color: var(--sidenavbar-text-color);
            margin-bottom: 1.5rem;
            font-weight: 600;
            /*border-bottom: 1px solid #e5e7eb;*/
            padding-bottom: 0.75rem;
        }

        /* Plan Selection Styles */
        .plan-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        @media (max-width: 992px) {
            .plan-container {
                grid-template-columns: 1fr;
            }
        }

        .plan-card {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .plan-card:hover {
            border-color: #008CFF;
            background-color: var(--body-background-color);
            transform: translateY(-2px);
        }
        .plan-card.selected {
            border: 2px solid #008CFF;
            background-color: var(--sidenavbar-body-color);
        }
        .plan-card h4 {
            color: var(--sidenavbar-text-color);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .plan-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--sidenavbar-text-color);
        }
        .plan-cycle {
            color: var(--sidenavbar-text-color);
            font-size: 0.875rem;
        }
        .plan-features {
            margin-top: 1rem;
            flex-grow: 1;
        }
        .plan-feature {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
            color: #4b5563;
        }
        .plan-feature svg {
            color: #008CFF;
            margin-right: 0.5rem;
        }

        /* Selected Plan Details */
        .selected-plan-details {
            background-color: var(--body-card-bg);
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 1.5rem;
            border: 1px solid #e5e7eb;
        }
        .selected-plan-details i,h5,p{
            color: var(--sidenavbar-text-color) !important;
        }
        .selected-plan-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }
        .selected-plan-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--sidenavbar-text-color);
        }
        .selected-plan-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--sidenavbar-text-color);
        }
        .selected-plan-features {
            color: var(--sidenavbar-text-color);
            margin-top: 1rem;
        }
        .selected-plan-feature {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
        }
        .selected-plan-feature svg {
            color: #008CFF;
            margin-right: 0.75rem;
            flex-shrink: 0;
        }

        /* Responsive Table */
        @media (max-width: 768px) {
            .responsive-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }

        /* Loading Overlay */
        #loadingOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: var(--body-background-color);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            display: none;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #e5e7eb;
            border-top-color: #008CFF;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Force remove all spacing around the row */
        #cards-container > .row {
            margin-right: 0 !important;
            margin-left: 0 !important;
            padding-right: 0 !important;
            padding-left: 0 !important;
        }

        /* Remove column padding if needed */
        #cards-container > .row > [class*="col-"] {
            padding-right: 7px !important;
            padding-left: 7px !important;
        }

        .btn-checkout {
            background: linear-gradient(135deg, var(--color-blue), #4e54c8);
            border: none;
            border-radius: 30px;
            padding: 10px 25px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }

        .btn-checkout:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            background: linear-gradient(135deg, var(--color-blue), #4e54c8);
        }

        .btn-checkout:active {
            transform: translateY(0);
        }
    </style>
@endpush

@section('content')

    <!-- Top Navbar -->
    <x-Admin.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('admin_dashboard'), 'label' => 'Dashboard'],
            ['url' => '', 'label' => 'Upgrade Plan']
        ]"
    />

    <!-- Side Navbar -->
    <x-Admin.side-navbar :openSections="['Dashboard']" />
    <x-error-success-model />

    <!-- Loading Overlay -->
    <div id="loadingOverlay">
        <div class="spinner"></div>
    </div>

    <div id="main">

        <section class="content my-3 mx-2">
            <div class="container-fluid">

                <div class="row">
                    <!-- Left Column - Plan Selection -->
                    <div class="col-lg-12 col-md-12">
                        <div class="form-section shadow">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5><i class="fas fa-cubes me-2"></i> Membership Plan</h5>

                                <div>
                                    <form action="{{ route('organizations.plan.upgrade.complete') }}" method="POST" enctype="multipart/form-data">
                                    @method('PUT')

                                        <input type="hidden" name="organization_id" value="{{ $organization_id }}">
                                        <input type="hidden" name="plan_id" id="plan_id">
                                        <input type="hidden" name="plan_cycle_id"  id="plan_cycle_id">
                                        <input type="hidden" name="plan_cycle" id="plan_cycle">

                                        <input type="hidden" id="selectedPlanId">
                                        <input type="hidden" id="selectedPlanCycle">
                                        <input type="hidden" id="selectedBillingCycleId">
                                        <button type="submit" class="btn btn-primary btn-checkout">
                                            <i class="fas fa-lock me-2"></i> Upgrade
                                        </button>
                                    </form>
                                </div>


                            </div>

                            <div class="mb-4">
                                <label for="billing-cycle" class="form-label mb-2">Billing Cycle</label>
                                <div class="input-group">
                                    <select id="billing-cycle" name="billing-cycle" class="form-select">
                                        @forelse($planCycles as $planCycle)
                                            <option value="{{ $planCycle }}" {{ old('billing-cycle', $activeCycle) == $planCycle ? 'selected' : '' }}>
                                                {{ $planCycle }} Month
                                            </option>
                                        @empty
                                            <option value="">No Plans Available</option>
                                        @endforelse
                                    </select>
                                    <span class="input-group-text" style="background-color: var(--sidenavbar-body-color);">
                                                <i class="fas fa-calendar-alt"></i>
                                            </span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label mb-2">Available Plans</label>
                                <div class="row g-3 plans-container" id="plans-container">
                                    <!-- Plans will be loaded here via JavaScript -->
                                    <div class="col-12 text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2">Loading plans...</p>
                                    </div>
                                </div>
                            </div>

                            <div class="selected-plan-details shadow-sm" id="selected-plan-details">
                                <div class="text-center py-4">
                                    <i class="fas fa-cube fa-3x text-muted mb-3"></i>
                                    <h5 class="">No Plan Selected</h5>
                                    <p class="">Please select a plan from above</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>

@endsection

@push('scripts')

    <!-- Plan Selection Logic -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const planCycleSelect = document.getElementById("billing-cycle");
            const plansContainer = document.getElementById("plans-container");
            const selectedPlanDetails = document.getElementById("selected-plan-details");
            const loadingOverlay = document.getElementById("loadingOverlay");

            let selectedPlanId = @json($activePlanId) ?? null;
            let currentPlans = [];
            let isInitialLoad = true;

            function showLoading() {
                if (loadingOverlay) loadingOverlay.style.display = 'flex';
            }

            function hideLoading() {
                if (loadingOverlay) loadingOverlay.style.display = 'none';
            }

            function updatePriceDisplays(plan, cycleId) {
                if (!plan) return;

                // Update hidden form fields
                const elements = {
                    'selectedPlanId': plan.plan_id,
                    'selectedPlanCycle': cycleId,
                    'selectedBillingCycleId': plan.billing_cycle_id,
                    'plan_id': plan.plan_id,
                    'plan_cycle': cycleId,
                    'plan_cycle_id': plan.billing_cycle_id
                };

                Object.entries(elements).forEach(([id, value]) => {
                    const el = document.getElementById(id);
                    if (el) el.value = value;
                });
            }

            function fetchPlans(cycleId) {
                if (!cycleId) return;

                if (!isInitialLoad) showLoading();

                const url = `{{ route('plans.custom', ':planCycle') }}`.replace(':planCycle', cycleId);

                fetch(url, {
                    method: "GET",
                    headers: {
                        "Accept": "application/json"
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        currentPlans = data.plans || [];
                        renderPlans(currentPlans);

                        // Try to maintain the selected plan across billing cycle changes
                        if (selectedPlanId) {
                            const selectedPlan = currentPlans.find(p =>
                                p.plan_id == selectedPlanId || p.plan_name === selectedPlanId
                            );

                            if (selectedPlan) {
                                // Found the same plan in the new billing cycle
                                renderSelectedPlanDetails(selectedPlan);
                            } else if (currentPlans.length > 0) {
                                // Select the first plan if previous selection isn't available
                                selectedPlanId = currentPlans[0].plan_id;
                                renderSelectedPlanDetails(currentPlans[0]);
                            } else {
                                // No plans available
                                selectedPlanDetails.innerHTML = noPlanSelectedHTML();
                            }
                        } else if (currentPlans.length > 0 && isInitialLoad) {
                            // Initial load with no selected plan - select first one
                            selectedPlanId = currentPlans[0].plan_id;
                            renderSelectedPlanDetails(currentPlans[0]);
                        }
                    })
                    .catch(error => console.error("Error fetching plans:", error))
                    .finally(() => {
                        if (!isInitialLoad) hideLoading();
                        isInitialLoad = false;
                    });
            }

            function renderPlans(plans) {
                if (!plansContainer) return;

                plansContainer.innerHTML = '';
                const cycleId = planCycleSelect.value;

                if (plans.length === 0) {
                    plansContainer.innerHTML = noPlansAvailableHTML();
                    selectedPlanDetails.innerHTML = noPlanSelectedHTML();
                    selectedPlanId = null;
                    return;
                }

                plans.forEach(plan => {
                    const monthlyPrice = (plan.total_price / cycleId).toFixed(2);
                    const currency = plan.currency === 'PKR' ? 'Rs.' : '$';

                    const planCol = document.createElement('div');
                    planCol.className = 'col-md-6';

                    const planCard = document.createElement('div');
                    planCard.className = 'plan-card';

                    // Check if this plan is the selected one
                    if (selectedPlanId && (plan.plan_id == selectedPlanId || plan.plan_name === selectedPlanId)) {
                        planCard.classList.add('selected');
                    }

                    planCard.dataset.planId = plan.plan_id;

                    planCard.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h4 class="mb-0">${plan.plan_name}</h4>
                </div>
                <div class="plan-price">${currency}${monthlyPrice}<span class="plan-cycle">/month</span></div>
            `;

                    planCard.addEventListener('click', () => {
                        selectedPlanId = plan.plan_id;
                        document.querySelectorAll('.plan-card').forEach(c => c.classList.remove('selected'));
                        planCard.classList.add('selected');
                        renderSelectedPlanDetails(plan);
                    });

                    planCol.appendChild(planCard);
                    plansContainer.appendChild(planCol);
                });
            }

            function renderSelectedPlanDetails(plan) {
                if (!selectedPlanDetails || !plan) return;

                const cycleId = planCycleSelect.value;
                const monthlyPrice = (plan.total_price / cycleId).toFixed(2);
                const totalPrice = plan.total_price.toFixed(2);
                const currency = plan.currency === 'PKR' ? 'Rs.' : '$';

                selectedPlanDetails.innerHTML = `
            <div class="selected-plan-header d-flex flex-column flex-sm-row align-items-sm-center justify-content-between">
                <div class="selected-plan-title">${plan.plan_name} Plan</div>
                <div class="selected-plan-price mt-2 mt-sm-0">${currency}${monthlyPrice}<span class="text-muted fs-6">/month</span></div>
            </div>

            <div class="selected-plan-features">
                <h6 class="fw-bold mb-3">Included Features:</h6>
                ${(plan.services || []).map(service => `
                    <div class="selected-plan-feature">
                        <svg class="bi" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <div class="fw-medium">${service.service_quantity}x ${service.service_name}</div>
                            <small class="small">${service.service_description || 'No description available'}</small>
                        </div>
                    </div>
                `).join('')}
            </div>
            <div class="selected-plan-summary mt-4 pt-3 border-top">
                <div class="d-flex justify-content-between mb-2">
                    <span>Billing Cycle:</span>
                    <span class="fw-medium">${cycleId} Months</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Monthly Price:</span>
                    <span class="fw-medium">${currency}${monthlyPrice}</span>
                </div>
                <div class="d-flex justify-content-between fw-bold fs-5 mt-3 pt-2 border-top">
                    <span>Total:</span>
                    <span>${currency}${totalPrice}</span>
                </div>
            </div>
        `;

                updatePriceDisplays(plan, cycleId);
            }

            function noPlansAvailableHTML() {
                return `
            <div class="col-12 text-center py-4" >
                <i class="fas fa-exclamation-circle fa-2x mb-3" style="color: var(--sidenavbar-text-color) !important;"></i>
                <p class="" style="color: var(--sidenavbar-text-color) !important;">No plans available for this billing cycle</p>
            </div>
        `;
            }

            function noPlanSelectedHTML() {
                return `
            <div class="text-center py-4">
                <i class="fas fa-cube fa-3x mb-3" style="color: var(--sidenavbar-text-color) !important;"></i>
                <h5 class="" style="color: var(--sidenavbar-text-color) !important;">No Plan Selected</h5>
                <p class="" style="color: var(--sidenavbar-text-color) !important;">Please select a plan from above</p>
            </div>
        `;
            }

            // Event listener for plan cycle change
            planCycleSelect.addEventListener("change", function () {
                fetchPlans(this.value);
            });

            // Initial fetch
            if (planCycleSelect.value) {
                fetchPlans(planCycleSelect.value);
            } else if (planCycleSelect.options.length > 0) {
                planCycleSelect.selectedIndex = 0;
                fetchPlans(planCycleSelect.value);
            }

        });
    </script>


@endpush
