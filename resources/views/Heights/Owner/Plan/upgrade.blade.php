@extends('layouts.app')

@section('title', 'Checkout')

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
            background: linear-gradient(135deg, #4e54c8, #8f94fb);
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
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('owner_manager_dashboard'), 'label' => 'Dashboard'],
            ['url' => '', 'label' => 'Checkout']
        ]"
    />

    <!-- Side Navbar -->
    <x-Owner.side-navbar :openSections="['Dashboard']" />
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
                    <div class="col-lg-6 col-md-12">
                        <div class="form-section shadow">
                            <h5><i class="fas fa-cubes me-2"></i> Membership Plan</h5>

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
                                    <h5 class="text-muted">No Plan Selected</h5>
                                    <p class="text-muted">Please select a plan from above</p>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Right Column - Organization Details -->
                    <div class="col-lg-6 col-md-12">
                        <div class="form-section shadow">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i> Payment Methods
                                </h4>
                                <button type="button" class="btn btn-sm btn-primary add-payment-method-btn" id="add-payment-method-btn" data-bs-toggle="modal" data-bs-target="#paymentMethodModal">
                                    <i class="fas fa-plus me-1"></i> Add Method
                                </button>
                            </div>

                            <div class="row" id="cards-container">
                                <!-- Loading state -->
                                <div class="col-12 text-center py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section shadow">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="mb-0">
                                    <i class="fas fa-credit-card me-2"></i> Complete Payment
                                </h4>

                                <input type="hidden" id="selectedCardId">
                                <input type="hidden" name="plan_id" id="plan_id">
                                <input type="hidden" name="plan_cycle_id"  id="plan_cycle_id">
                                <input type="hidden" name="plan_cycle" id="plan_cycle">

                                <input type="hidden" id="selectedPlanId">
                                <input type="hidden" id="selectedPlanCycle">
                                <input type="hidden" id="selectedBillingCycleId">

                                <!-- Beautiful Checkout Button -->
                                <button type="submit" class="btn btn-primary btn-checkout" onclick="handleCompletePayment()">
                                    <i class="fas fa-lock me-2"></i> Checkout
                                </button>
                            </div>
                        </div>


                    </div>


                </div>

            </div>
        </section>
    </div>

    <div class="modal fade" id="paymentMethodModal" tabindex="-1" aria-labelledby="paymentMethodLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered bg-transparent">
            <div class="modal-content p-0 m-0 border-0 rounded-3  bg-transparent">
                <div class="modal-body p-0 m-0">
                    <x-stripe-card-form
                        :stripeKey="config('services.stripe.key')"
                        formAction="#"
                        buttonText="Save Card"
                        title="Add Payment Method"
                    />
                </div>
            </div>
        </div>
    </div>


@endsection

@push('scripts')

    <script>
        /**
         * Payment Cards Management Script
         * Handles loading, displaying, and managing payment cards
         */
        document.addEventListener('DOMContentLoaded', async function() {
            // DOM Elements
            const dom = {
                cardsContainer: document.getElementById('cards-container')
            };

            // Event delegation for card actions
            document.addEventListener('click', handleCardActions);

            // Initialize
            window.submitaddedCard = submitaddedCard;
            loadCards();

            /**
             * Submits a new card to the server
             */
            async function submitaddedCard() {
                const methodId = document.getElementById('payment_method_id').value;
                const response = await fetch('{{ route('owner.cards.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ payment_method_id: methodId })
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.error || result.message || 'Failed to add payment method');
                }

                const modal = bootstrap.Modal.getInstance(document.getElementById('paymentMethodModal'));
                if (modal) modal.hide();

                loadCards();
            }

            /**
             * Loads cards from server
             */
            async function loadCards() {
                // Show loading state
                dom.cardsContainer.innerHTML = `
                <div class="col-12 text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;

                try {
                    const response = await fetch('{{ route('owner.cards.index') }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to load cards');
                    }

                    const data = await response.json();
                    renderCards(data.cards);
                } catch (error) {
                    console.error('Error loading cards:', error);
                    showCardsError();
                }
            }

            /**
             * Shows error state when cards fail to load
             */
            function showCardsError() {
                dom.cardsContainer.innerHTML = `
                <div class="col-12 text-center py-4 text-danger">
                    Failed to load payment methods. Please try again.
                </div>
            `;
            }

            /**
             * Renders cards in the UI
             */
            function renderCards(cards) {
                dom.cardsContainer.innerHTML = '';

                if (!cards || cards.length === 0) {
                    dom.cardsContainer.innerHTML = `
                    <div class="col-12 text-center py-4 text-muted">
                        No payment methods found.
                    </div>
                `;
                    return;
                }

                const rowDiv = document.createElement('div');
                rowDiv.className = 'row';
                dom.cardsContainer.appendChild(rowDiv);

                // Sort cards with primary first
                [...cards].sort((a, b) => b.is_default - a.is_default)
                    .forEach(card => {
                        rowDiv.insertAdjacentHTML('beforeend', renderPaymentCard(card));
                    });
            }

            /**
             * Generates HTML for a payment card
             */
            function renderPaymentCard(card) {
                const cardStyles = {
                    'visa': {
                        background: 'linear-gradient(135deg, #1a1f71 0%, #0065a3 100%)',
                        icon: 'bxl-visa',
                        accent: 'linear-gradient(to right, #f9a61a, #f8d568)',
                        textColor: '#1a1f71'
                    },
                    'mastercard': {
                        background: 'linear-gradient(135deg, #EB001B 0%, #F79E1B 100%)',
                        icon: 'bxl-mastercard',
                        accent: 'rgba(255,255,255,0.9)',
                        textColor: '#EB001B'
                    },
                    'amex': {
                        background: 'linear-gradient(135deg, #016FD0 0%, #00A3E0 100%)',
                        icon: 'fa-cc-amex',
                        accent: 'rgba(255,255,255,0.9)',
                        textColor: '#016FD0'
                    },
                    'discover': {
                        background: 'linear-gradient(135deg, #FF6000 0%, #FFA000 100%)',
                        icon: 'fa-cc-discover',
                        accent: 'rgba(255,255,255,0.9)',
                        textColor: '#FF6000'
                    },
                    'default': {
                        background: 'linear-gradient(135deg, #4a5568 0%, #0E131DFF 100%)',
                        icon: 'bx-credit-card',
                        accent: 'rgba(255,255,255,0.9)',
                        textColor: '#4a5568'
                    }
                };

                const normalizedCardType = card.brand.toLowerCase();
                const style = cardStyles[normalizedCardType] || cardStyles['default'];
                const isFontAwesome = style.icon.includes('fa-');
                const expMonth = String(card.exp_month).padStart(2, '0');
                const expYear = String(card.exp_year).slice(-2);
                const expiry = `${expMonth}/${expYear}`;
                const dropdownId = `cardMenu${card.id.replace(/\D/g, '')}`;

                // Set the initial value if this is the default card
                if (card.is_default) {
                    document.getElementById('selectedCardId').value = card.id;
                }

                return `
<div class="col-md-6 col-md-6 col-12 mb-3">
    <div class="payment-card w-100 p-3"
        style="background: ${style.background};
               border-radius: 10px;
               color: white;
               box-shadow: 0 4px 8px rgba(0,0,0,0.1);
               position: relative;
               overflow: hidden;
               border: ${card.is_default ? '2px solid ' + style.accent : '2px solid transparent'};">
        <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>

        <div class="d-flex justify-content-between align-items-center mb-1">
            <div class="form-check">
                <input class="form-check-input card-radio"
                       type="radio"
                       name="selectedCard"
                       id="card-${card.id}"
                       value="${card.id}"
                       ${card.is_default ? 'checked' : ''}
                       style="cursor: pointer;"
                       onchange="document.getElementById('selectedCardId').value = this.value;">
            </div>

            ${isFontAwesome
                    ? `<i class="fab ${style.icon}" style="font-size: 40px; color: white;"></i>`
                    : `<i class='bx ${style.icon}' style="font-size: 40px; color: white;"></i>`
                }

            ${card.is_default
                    ? `<span class="badge" style="background: ${style.accent}; color: ${style.textColor}; font-weight: bold;">Primary</span>`
                    : `<div class="dropdown">
                    <button class="btn btn-sm p-0" style="background: transparent; color: white; border: none;" type="button" id="${dropdownId}" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" style="background-color: var(--body-background-color);" aria-labelledby="${dropdownId}">
                        <li><a class="dropdown-item set-primary-btn" href="#" data-card-id="${card.id}"
                            style="background-color: var(--body-background-color); color: var(--sidenavbar-text-color);" onmouseover="this.style.backgroundColor='var(--body-background-color)'" onmouseout="this.style.backgroundColor='var(--body-background-color)'">
                            Set as primary</a>
                        </li>
                    </ul>
                </div>`
                }
        </div>

        <div class="mb-1" style="position: relative; z-index: 2;">
            <h5 class="mb-0 text-white" style="letter-spacing: 1px;">•••• •••• •••• ${card.last4}</h5>
        </div>
    </div>
</div>
`;
            }

            document.querySelectorAll('.card-radio').forEach(radio => {
                radio.addEventListener('change', function() {
                    document.getElementById('selectedCardId').value = this.value;
                });
            });

            /**
             * Handles card action events
             */
            async function handleCardActions(e) {
                // Set primary button handler
                if (e.target.classList.contains('set-primary-btn') || e.target.closest('.set-primary-btn')) {
                    e.preventDefault();
                    await handleSetPrimary(e);
                }

                // Delete button handler
                if (e.target.classList.contains('delete-card-btn') || e.target.closest('.delete-card-btn')) {
                    e.preventDefault();
                    await handleDeleteCard(e);
                }
            }

            /**
             * Handles setting a card as primary
             */
            async function handleSetPrimary(e) {
                const cardId = e.target.dataset.cardId || e.target.closest('[data-card-id]').dataset.cardId;

                try {
                    const result = await Swal.fire({
                        title: 'Set as Primary?',
                        text: 'Do you want to set this card as your primary payment method?',
                        icon: 'question',
                        showCancelButton: true,
                        background: 'var(--body-background-color)',
                        color: 'var(--sidenavbar-text-color)',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, set as primary'
                    });

                    if (result.isConfirmed) {
                        await updatePrimaryCard(cardId);
                        await Swal.fire({
                            title: 'Success!',
                            text: 'Primary card updated successfully',
                            icon: 'success',
                            confirmButtonText: 'OK',
                            background: 'var(--body-background-color)',
                            color: 'var(--sidenavbar-text-color)'
                        });
                        await loadCards();
                    }
                } catch (error) {
                    await showErrorAlert(error);
                }
            }

            /**
             * Updates primary card on server
             */
            async function updatePrimaryCard(cardId) {
                const response = await fetch('{{ route('owner.cards.update.default') }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ payment_method_id: cardId })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to set card as primary');
                }
            }

            /**
             * Handles card deletion
             */
            async function handleDeleteCard(e) {
                const cardId = e.target.dataset.cardId || e.target.closest('[data-card-id]').dataset.cardId;

                try {
                    const result = await Swal.fire({
                        title: 'Delete Card?',
                        text: 'Are you sure you want to delete this payment method?',
                        icon: 'warning',
                        showCancelButton: true,
                        background: 'var(--body-background-color)',
                        color: 'var(--sidenavbar-text-color)',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!',
                        dangerMode: true
                    });

                    if (result.isConfirmed) {
                        await deleteCard(cardId);
                        await Swal.fire({
                            title: 'Deleted!',
                            text: 'Your payment method has been deleted.',
                            icon: 'success',
                            confirmButtonText: 'OK',
                            background: 'var(--body-background-color)',
                            color: 'var(--sidenavbar-text-color)'
                        });
                        await loadCards();
                    }
                } catch (error) {
                    await showErrorAlert(error);
                }
            }

            /**
             * Deletes card from server
             */
            async function deleteCard(cardId) {
                const response = await fetch('{{ route('owner.cards.delete') }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ payment_method_id: cardId })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || 'Failed to delete card');
                }
            }

            /**
             * Shows error alert
             */
            async function showErrorAlert(error) {
                await Swal.fire({
                    title: 'Error!',
                    text: error.message,
                    icon: 'error',
                    confirmButtonText: 'OK',
                    background: 'var(--body-background-color)',
                    color: 'var(--sidenavbar-text-color)'
                });
                console.error('Error:', error);
            }
        });
    </script>

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

                const url = `{{ route('plans.organization', ':planCycle') }}`.replace(':planCycle', cycleId);

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
            <div class="col-12 text-center py-4">
                <i class="fas fa-exclamation-circle fa-2x text-muted mb-3"></i>
                <p class="">No plans available for this billing cycle</p>
            </div>
        `;
            }

            function noPlanSelectedHTML() {
                return `
            <div class="text-center py-4">
                <i class="fas fa-cube fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Plan Selected</h5>
                <p class="text-muted">Please select a plan from above</p>
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

    <script>
        /**
         * Payment Processing Script
         * Handles the checkout process including card payment confirmation
         */
        document.addEventListener('DOMContentLoaded', async function () {

            console.log("Complete Payment button clicked");
            // DOM Elements
            const selectedPlanId = document.getElementById('selectedPlanId');
            const selectedPlanCycle = document.getElementById('selectedPlanCycle');
            const selectedBillingCycleId = document.getElementById('selectedBillingCycleId');

            // Initialize function on window for global access
            window.handleCompletePayment = handleCompletePayment;

            /**
             * Handles the card submission and payment processing
             */
            async function handleCompletePayment() {
                const methodId = document.getElementById('selectedCardId').value;
                const dataToSend = {
                    payment_method_id: methodId,
                    plan_id: selectedPlanId.value,
                    plan_cycle: selectedPlanCycle.value,
                    plan_cycle_id: selectedBillingCycleId.value
                };

                showLoading(true);

                try {
                    // Initial payment processing request
                    const response = await fetch('{{ route('owner.plan.upgrade.processing') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(dataToSend)
                    });

                    const result = await response.json();

                    if (!response.ok) {
                        const errorMsg = result.message || result.error || "Server error occurred.";
                        showResponseMessage(errorMsg, 'error');
                    }

                    // Handle 3D Secure authentication if required
                    if (result.requires_action) {
                        await handle3DSecureAuthentication(result);
                    }
                    // Handle direct success case
                    else if (result.success) {
                        handlePaymentSuccess(result);
                    }
                    // Handle other error cases
                    else {
                        handlePaymentError(result);
                    }
                } catch (err) {
                    console.error("Error:", err);
                    if (!err.message.includes('Payment successful')) {
                        showResponseMessage("An error occurred. Please try again.", 'error');
                    }
                    throw err;
                } finally {
                    showLoading(false);
                }
            }

            /**
             * Handles 3D Secure authentication flow
             */
            async function handle3DSecureAuthentication(result) {
                const confirmResult = await stripe.confirmCardPayment(result.client_secret);

                if (confirmResult.error) {
                    showResponseMessage(confirmResult.error.message, 'error');
                }

                if (confirmResult.paymentIntent.status === "succeeded") {
                    await completePaymentAfter3DS(confirmResult);
                }
            }

            /**
             * Completes payment after successful 3D Secure authentication
             */
            async function completePaymentAfter3DS(confirmResult) {
                const dataForComplete = {
                    plan_id: selectedPlanId.value,
                    plan_cycle: selectedPlanCycle.value,
                    plan_cycle_id: selectedBillingCycleId.value,
                    payment_intent_id: confirmResult.paymentIntent.id
                };

                const completeResponse = await fetch("{{ route('owner.plan.upgrade.processing.complete') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(dataForComplete)
                });

                const result = await completeResponse.json();

                if (!completeResponse.ok) {
                    const errorMsg = result.message || result.error || "Server error occurred.";
                    showResponseMessage(errorMsg, 'error');
                }

                if (!result.success) {
                    const errorMsg = result.message || result.error || "Payment failed. Please try again.";
                    showResponseMessage(errorMsg, 'error');
                }

                handlePaymentSuccess(result);
            }

            /**
             * Handles successful payment case
             */
            function handlePaymentSuccess(result) {
                showResponseMessage(result.message || "Payment successful!", 'success');
                redirectToLogin();
                showStatus('Payment successful!', 'success');
            }

            /**
             * Handles payment error case
             */
            function handlePaymentError(result) {
                const errorMsg = result.message || result.error || "Payment failed. Please try again.";
                showResponseMessage(errorMsg, 'error');
            }

            /**
             * Shows/hides loading overlay
             */
            function showLoading(show = true) {
                const overlay = document.getElementById('loadingOverlay');
                if (overlay) {
                    overlay.style.display = show ? 'flex' : 'none';
                }
            }

            /**
             * Redirects to login page after successful payment
             */
            function redirectToLogin() {
                setTimeout(() => {
                    window.location.href = "{{ route('owner.organization.profile') }}";
                }, 1500);
            }

            /**
             * Shows response message using SweetAlert
             */
            function showResponseMessage(message, type = 'success') {
                const isSuccess = type === 'success';
                Swal.fire({
                    title: isSuccess ? "Success!" : "Error!",
                    text: message,
                    icon: type,
                    confirmButtonText: 'OK',
                    timer: 3000,
                    timerProgressBar: true,
                    background: getComputedStyle(document.documentElement).getPropertyValue('--swal-bg-color').trim(),
                    color: getComputedStyle(document.documentElement).getPropertyValue('--swal-text-color').trim(),
                    iconColor: getComputedStyle(document.documentElement).getPropertyValue(`--swal-icon-${type}-color`).trim(),
                    customClass: {
                        popup: 'theme-swal-popup',
                        confirmButton: 'theme-swal-button'
                    }
                });
            }
        });
    </script>


@endpush
