@extends('layouts.landing')

@section('title', 'Checkout Screen')

@push('styles')

    <style>

        :root {
            --payment-card-primary: #008CFF;
            --payment-card-primary-hover: #008CFF;
            --payment-card-error: #dc2626;
            --payment-card-text: #1e293b;
            --payment-card-text-light: #64748b;
            --payment-card-border: #e2e8f0;
            --payment-card-border-hover: #cbd5e1;
            --payment-card-background: #ffff;
            --payment-card-input-background2: #ffff;
            --payment-card-radius: 10px;
            --payment-card-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
            --payment-card-transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #payment-card {
            width: 100% !important;
            max-width: 100% !important;
            object-fit: cover;
            animation: fadeIn 0.3s ease-out;
        }

        #payment-card .payment-card-form {
            width: 100%;
            padding: 32px;
            border-radius: var(--payment-card-radius) var(--payment-card-radius) 0 0 !important;
            box-shadow: var(--payment-card-shadow);
            border: 1px solid var(--payment-card-border);
            background-color: var(--payment-card-background);
        }

        .plan-card {
            min-width: 180px;
            scroll-snap-align: start;
        }
        .plans-container {
            scroll-snap-type: x mandatory;
        }
        .selected-plan {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);
        }



        #loadingOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: none;
            justify-content: center;
            align-items: center;
            background-color: var(--body-background-color);
            z-index: 9999; /* Ensures the loading animation is on top of other content */
        }

        .plan-card.selected {
            background-color: #1f2937; /* Tailwind's blue-600 */
            color: white;
            border-color: #1f2937;
        }
        .plan-card:hover {
            background-color: #1f2937; /* Tailwind gray-900 */
            color: white;
        }
    </style>

@endpush

@section('content')

    <div class="loading-animation" id="loadingOverlay">
        <img src="{{ asset('img/Loading1.gif') }}" alt="Loading..." class="loading-gif">
    </div>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Complete Your Subscription</h1>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Left Side - Plan Selection -->
            <div class="lg:w-1/2 bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-6">Choose Your Plan</h2>


                <div class="my-6 flex justify-center">
                    <div class="relative inline-flex">
                        <select id="billing-cycle" name="billing-cycle" class="block w-100 appearance-none rounded-md border border-gray-300 bg-white px-4 py-2 pr-10 text-base font-medium text-gray-700 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            @forelse($planCycles as $planCycle)
                                <option value="{{ $planCycle }}" {{ old('billing-cycle', $selectedCycle) == $planCycle ? 'selected' : '' }}>{{ $planCycle }} Month</option>
                            @empty
                                <option value="">No Plans Cycle</option>
                            @endforelse

                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.65a.75.75 0 01-1.08 0l-4.25-4.65a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Horizontal Plan Cards -->
                <div class="plans-container overflow-x-auto flex gap-4 pb-4 mb-6 -mx-2 px-2 scrollbar-hide card-grid"></div>

                <!-- Selected Plan Details -->
                <div class="bg-gray-50 rounded-lg p-6 selected-plan-details">
                    <!-- Dynamic content will be injected here based on the selected plan -->
                </div>

            </div>

            <!-- Right Side - Payment Details -->
            <div class="lg:w-1/2 bg-white rounded-xl shadow o h-fullverflow-hidden p-0">

                <!-- Card -->
                    <x-stripe-card-form
                        class="w-full h-full"
                        :stripeKey="config('services.stripe.key')"
                        formAction="#"
                        buttonText="Pay Now"
                        title="Payment Information"
                    />

                <input type="hidden" id="owner_id" value="{{ $owner_id }}">
                <input type="hidden" id="organization_id" value="{{ $organization_id }}">
                <input type="hidden" id="selectedPlanId" value="{{ $selectedPackage }}">
                <input type="hidden" id="selectedPlanCycle" value="{{ $planCycles }}">
                <input type="hidden" id="selectedBillingCycleId" value="{{ $selectedCycle }}">

                <div class="mt-6 px-4 pb-4">
                    <h3 class="text-lg font-medium text-gray-700 mb-3">Order Summary</h3>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Plan:</span>
                        <span class="font-medium"><span class="selected_plan_name"> Basic</span> (<span class="selected_plan_cycle">12</span> Month)</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-medium" id="plan_subtotal_price">$0.00</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Tax:</span>
                        <span class="font-medium">Rs.0.00</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold mt-4 pt-4 border-t border-gray-200">
                        <span>Total:</span>
                        <span class="" id="plan_total_price">Rs.0.00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        /**
         * Payment Processing Script
         * Handles the checkout process including card payment confirmation
         */
        document.addEventListener('DOMContentLoaded', async function () {
            // DOM Elements
            const ownerId = document.getElementById('owner_id');
            const organizationId = document.getElementById('organization_id');
            const selectedPlanId = document.getElementById('selectedPlanId');
            const selectedPlanCycle = document.getElementById('selectedPlanCycle');
            const selectedBillingCycleId = document.getElementById('selectedBillingCycleId');

            // Initialize function on window for global access
            window.submitaddedCard = submitaddedCard;

            /**
             * Handles the card submission and payment processing
             */
            async function submitaddedCard() {
                const methodId = document.getElementById('payment_method_id').value;
                const dataToSend = {
                    payment_method_id: methodId,
                    plan_id: selectedPlanId.value,
                    plan_cycle: selectedPlanCycle.value,
                    plan_cycle_id: selectedBillingCycleId.value,
                    owner_id: ownerId.value,
                    organization_id: organizationId.value
                };

                showLoading(true);

                try {
                    // Initial payment processing request
                    const response = await fetch('{{ route('checkout.processing') }}', {
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
                        throw new Error(errorMsg);
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
                    throw new Error(confirmResult.error.message);
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
                    owner_id: ownerId.value,
                    organization_id: organizationId.value,
                    payment_intend_id: confirmResult.payment_intend_id
                };

                const completeResponse = await fetch("{{ route('checkout.processing.complete') }}", {
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
                    throw new Error(errorMsg);
                }

                if (!result.success) {
                    const errorMsg = result.message || result.error || "Payment failed. Please try again.";
                    showResponseMessage(errorMsg, 'error');
                    throw new Error(errorMsg);
                }

                handlePaymentSuccess(result);
            }

            /**
             * Handles successful payment case
             */
            function handlePaymentSuccess(result) {
                showResponseMessage(result.message || "Payment successful!", 'success');
                redirectToLogin();
                throw new Error(result.message || 'Payment successful!');
            }

            /**
             * Handles payment error case
             */
            function handlePaymentError(result) {
                const errorMsg = result.message || result.error || "Payment failed. Please try again.";
                showResponseMessage(errorMsg, 'error');
                throw new Error(errorMsg);
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
                    window.location.href = "{{ route('login') }}";
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

    <script>
        /**
         * Plan Selection Script
         * Handles plan selection, billing cycle changes, and price calculations
         */
        document.addEventListener("DOMContentLoaded", function () {
            // DOM Elements
            const planCycleSelect = document.getElementById("billing-cycle");
            const plansContainer = document.querySelector(".card-grid");
            const selectedPlanDetails = document.querySelector(".selected-plan-details");

            // Variables
            let selectedPlan = @json($selectedPackage) ?? null;

            /**
             * Updates price displays based on selected plan and billing cycle
             */
            function updatePriceDisplays(plan, cycleId) {
                if (!plan) return;

                const monthlyPrice = plan.total_price / cycleId;
                const formattedMonthly = monthlyPrice.toFixed(2);
                const formattedTotal = plan.total_price.toFixed(2);
                const currency = plan.currency === 'PKR' ? 'Rs.' : '$';

                // Update button price
                const buttonPrice = document.getElementById('button-price');
                if (buttonPrice) {
                    buttonPrice.textContent = `${currency}${formattedMonthly}`;
                }

                // Update plan info elements
                const planNameEls = document.querySelectorAll('.selected_plan_name');
                const planCycleEls = document.querySelectorAll('.selected_plan_cycle');
                const subtotalPrice = document.getElementById('plan_subtotal_price');
                const totalPrice = document.getElementById('plan_total_price');

                // Update hidden inputs
                const selectedPlanId = document.getElementById('selectedPlanId');
                const selectedPlanCycle = document.getElementById('selectedPlanCycle');
                const selectedBillingCycleId = document.getElementById('selectedBillingCycleId');

                planNameEls.forEach(el => el.textContent = plan.plan_name);
                planCycleEls.forEach(el => el.textContent = cycleId);

                selectedPlanId.value = plan.plan_id;
                selectedPlanCycle.value = cycleId;
                selectedBillingCycleId.value = plan.billing_cycle_id;

                if (subtotalPrice) subtotalPrice.textContent = `${currency}${formattedTotal}`;
                if (totalPrice) totalPrice.textContent = `${currency}${formattedTotal}`;
            }

            /**
             * Fetches plans from server based on billing cycle
             */
            function fetchPlans(cycleId) {
                if (!cycleId) return;

                const url = `{{ route('plans', ':planCycle') }}`.replace(':planCycle', cycleId);

                fetch(url, {
                    method: "GET",
                    headers: {
                        "Accept": "application/json"
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        const plans = data.plans || [];
                        renderPlans(plans);

                        // Update selected plan if exists
                        if (selectedPlan) {
                            const selectedPlanObj = plans.find(p => p.plan_name === selectedPlan);
                            if (selectedPlanObj) {
                                updatePriceDisplays(selectedPlanObj, cycleId);
                            }
                        }
                    })
                    .catch(error => console.error("Error fetching plans:", error));
            }

            /**
             * Renders plan cards in the UI
             */
            function renderPlans(plans) {
                plansContainer.innerHTML = '';
                const cycleId = planCycleSelect.value;

                plans.forEach(plan => {
                    const card = createPlanCard(plan, cycleId);
                    plansContainer.appendChild(card);
                });

                // Highlight selected or first plan
                if (!selectedPlan && plans.length > 0) {
                    selectedPlan = plans[0].plan_name;
                    renderSelectedPlanDetails(plans[0]);

                    const firstCard = plansContainer.querySelector(`[data-plan-id="${selectedPlan}"]`);
                    if (firstCard) firstCard.classList.add('selected');
                } else {
                    const selectedPlanObj = plans.find(plan => plan.plan_name === selectedPlan);
                    if (selectedPlanObj) {
                        renderSelectedPlanDetails(selectedPlanObj);
                        const selectedCard = plansContainer.querySelector(`[data-plan-id="${selectedPlanObj.plan_name}"]`);
                        if (selectedCard) selectedCard.classList.add('selected');
                    }
                }
            }

            /**
             * Creates a plan card element
             */
            function createPlanCard(plan, cycleId) {
                const card = document.createElement('div');
                card.className = 'plan-card bg-white border border-gray-200 rounded-lg p-4 cursor-pointer';
                card.dataset.planId = plan.plan_name;

                // Plan title
                const planTitle = document.createElement('h3');
                planTitle.className = 'font-bold text-lg text-center text-blue-600 plan_name';
                planTitle.textContent = plan.plan_name;

                // Pricing section
                const priceSection = document.createElement('div');
                priceSection.className = 'text-center mt-2';
                priceSection.innerHTML = `
                <span class="text-2xl font-bold plan_price">${plan.currency === 'PKR' ? 'Rs.' : '$'}${(plan.total_price / cycleId).toFixed(2)}</span>
                <span class="text-gray-500">/mo</span>
            `;

                card.appendChild(planTitle);
                card.appendChild(priceSection);

                // Add click event
                card.addEventListener('click', () => {
                    selectedPlan = plan.plan_name;
                    document.querySelectorAll('.plan-card').forEach(c => c.classList.remove('selected'));
                    card.classList.add('selected');
                    renderSelectedPlanDetails(plan);
                });

                return card;
            }

            /**
             * Renders the selected plan details section
             */
            function renderSelectedPlanDetails(plan) {
                selectedPlanDetails.innerHTML = '';
                updatePriceDisplays(plan, planCycleSelect.value);

                // Plan title
                const title = document.createElement('h3');
                title.className = 'font-bold text-lg mb-4';
                title.textContent = `${plan.plan_name} Plan Includes:`;
                selectedPlanDetails.appendChild(title);

                // Services list
                const servicesList = document.createElement('ul');
                servicesList.className = 'space-y-3';

                plan.services.forEach(service => {
                    const item = document.createElement('li');
                    item.className = 'flex gap-x-3 group-hover:text-white items-start';
                    item.innerHTML = `
                    <svg class="h-6 w-5 flex-none text-indigo-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8
                            10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1
                            1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1
                            1.05-.143Z" clip-rule="evenodd" />
                    </svg>
                    <span>${service.service_quantity}x ${service.service_name}</span>
                `;
                    servicesList.appendChild(item);
                });

                selectedPlanDetails.appendChild(servicesList);

                // Plan description if exists
                if (plan.plan_description) {
                    const desc = document.createElement('p');
                    desc.className = 'mt-4 text-sm text-gray-600';
                    desc.textContent = plan.plan_description;
                    selectedPlanDetails.appendChild(desc);
                }

                // Upgrade note
                const upgradeNote = document.createElement('div');
                upgradeNote.className = 'mt-6 pt-6 border-t border-gray-200';
                upgradeNote.innerHTML = `
                <h4 class="font-medium text-gray-700 mb-2">Need more?</h4>
                <p class="text-sm text-gray-600">Upgrade to the next plan for more features.</p>
            `;
                selectedPlanDetails.appendChild(upgradeNote);
            }

            // Event listeners
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
