@extends('layouts.landing')

@section('title', 'Checkout Screen')

@push('styles')

    <style>
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
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #ffff;
            display: none;
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

    <div id="loadingOverlay" >
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
        <div class="lg:w-1/2 bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-6">Payment Information</h2>

            <!-- Stripe Elements will be injected here -->
            <div id="card-element" class="border border-gray-200 rounded-lg p-4 mb-4"></div>
            <div id="card-errors" role="alert" class="text-red-500 text-sm mb-4"></div>

            <div class="mb-6">

                <div class="flex flex-row gap-4">
                    <!-- Email Organization Name -->
                    <div class="w-1/2">
                        <label class="block text-gray-700 text-sm font-medium mb-2" for="organization_name">
                            Organization Name
                        </label>
                        <input type="text" id="organization_name" value="{{ $organization_name }}" readonly tabindex="-1" class="w-full px-3 py-2 border border-gray-300 bg-gray-100 cursor-not-allowed rounded-md">
                    </div>

                    <!-- Email Address -->
                    <div class="w-1/2">
                        <label class="block text-gray-700 text-sm font-medium mb-2" for="email">
                            Email Address
                        </label>
                        <input type="email" id="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter Email">
                    </div>
                </div>

                <input type="hidden" id="owner_id" value="{{ $owner_id }}">
                <input type="hidden" id="organization_id" value="{{ $organization_id }}">
                {{--                <input type="hidden" id="organization_name" value="{{ $organization_name }}">--}}

                <input type="hidden" id="selectedPlanId" value="{{ $selectedPackage }}">
                <input type="hidden" id="selectedPlanCycle" value="{{ $planCycles }}">
                <input type="hidden" id="selectedBillingCycleId" value="{{ $selectedCycle }}">
            </div>

            <button id="submit-button" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-md transition duration-200">
                Pay <span id="button-price">Rs.0.00</span>/month
            </button>

            <div class="mt-6 pt-6 border-t border-gray-200">
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

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        document.addEventListener('DOMContentLoaded', async function () {
            const stripe = Stripe("{{ config('services.stripe.public') }}");
            const elements = stripe.elements({ disableLink: true });
            const card = elements.create("card");
            card.mount("#card-element");

            // Card input error display
            card.on('change', function (event) {
                const errorDiv = document.getElementById('card-errors');
                errorDiv.textContent = event.error ? event.error.message : '';
            });

            const submitButton = document.getElementById("submit-button");
            const emailInput = document.getElementById("email");
            const ownerId = document.getElementById('owner_id');
            const organizationId = document.getElementById('organization_id');
            const selectedPlanId = document.getElementById('selectedPlanId');
            const selectedPlanCycle = document.getElementById('selectedPlanCycle');
            const selectedBillingCycleId = document.getElementById('selectedBillingCycleId');

            if (submitButton) {
                submitButton.addEventListener("click", async () => {
                    submitButton.disabled = true;
                    showLoading(true);

                    try {
                        const { paymentMethod, error } = await stripe.createPaymentMethod({
                            type: 'card',
                            card: card,
                            billing_details: emailInput.value ? { email: emailInput.value } : {}
                        });

                        if (error) {
                            document.getElementById("card-errors").textContent = error.message;
                            submitButton.disabled = false;
                            showLoading(false);
                            return;
                        }

                        const dataToSend = {
                            payment_method_id: paymentMethod.id,
                            plan_id: selectedPlanId.value,
                            plan_cycle: selectedPlanCycle.value,
                            plan_cycle_id: selectedBillingCycleId.value,
                            owner_id: ownerId.value,
                            organization_id: organizationId.value
                        };

                        const response = await fetch("{{ route('checkout.processing') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(dataToSend)
                        });

                        const result = await response.json();

                        if (response.ok) {
                            if (result.requires_action) {
                                const confirmResult = await stripe.confirmCardPayment(result.client_secret);

                                if (confirmResult.error) {
                                    showResponseMessage("Payment failed: " + confirmResult.error.message, 'error');
                                } else if (confirmResult.paymentIntent.status === "succeeded") {

                                    const dataForComplete = {
                                        plan_id: selectedPlanId.value,
                                        plan_cycle: selectedPlanCycle.value,
                                        plan_cycle_id: selectedBillingCycleId.value,
                                        owner_id: ownerId.value,
                                        organization_id: organizationId.value,
                                        payment_intend_id: confirmResult.paymentIntent.id
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

                                    const result2 = await completeResponse.json();

                                    if (completeResponse.ok) {
                                        if (result2.success) {
                                            showResponseMessage(result2.message || "Payment successful!", 'success');
                                            redirectToLogin();
                                        } else {
                                            showResponseMessage(result2.message || result2.error || "Payment failed. Please try again.", 'error');
                                        }
                                    }
                                    showResponseMessage("Payment successful!", 'success');
                                    redirectToLogin();
                                }
                            } else if (result.success) {
                                showResponseMessage(result.message || "Payment successful!", 'success');
                                redirectToLogin();
                            } else {
                                showResponseMessage(result.message || "Payment failed. Please try again.", 'error');
                            }
                        } else {
                            showResponseMessage(result.message || "Server error occurred.", 'error');
                        }

                    } catch (err) {
                        console.error("Error:", err);
                        showResponseMessage("An error occurred. Please try again.", 'error');
                    }

                    showLoading(false);
                    submitButton.disabled = false;
                });
            }

            function showLoading(show = true) {
                const overlay = document.getElementById('loadingOverlay');
                if (overlay) {
                    overlay.style.display = show ? 'block' : 'none';
                }
            }

            function redirectToLogin() {
                setTimeout(() => {
                    window.location.href = "{{ route('login') }}";
                }, 1500);
            }


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
        document.addEventListener("DOMContentLoaded", function () {
            const planCycleSelect = document.getElementById("billing-cycle");
            const plansContainer = document.querySelector(".card-grid");
            const selectedPlanDetails = document.querySelector(".selected-plan-details");

            let selectedPlan = @json($selectedPackage) ?? null;

            function updatePriceDisplays(plan, cycleId) {
                if (!plan) return;

                const monthlyPrice = plan.total_price / cycleId;
                const formattedMonthly = monthlyPrice.toFixed(2);
                const formattedTotal = plan.total_price.toFixed(2);
                const currency = plan.currency === 'PKR' ? 'Rs.' : '$';

                const buttonPrice = document.getElementById('button-price');
                if (buttonPrice) {
                    buttonPrice.textContent = `${currency}${formattedMonthly}`;
                }

                const planNameEls = document.querySelectorAll('.selected_plan_name');
                const planCycleEls = document.querySelectorAll('.selected_plan_cycle');
                const subtotalPrice = document.getElementById('plan_subtotal_price');
                const totalPrice = document.getElementById('plan_total_price');

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
                        console.log("Fetched Plans:", plans);
                        renderPlans(plans);

                        if (selectedPlan) {
                            const selectedPlanObj = plans.find(p => p.plan_name === selectedPlan);
                            if (selectedPlanObj) {
                                updatePriceDisplays(selectedPlanObj, cycleId);
                            }
                        }
                    })
                    .catch(error => console.error("Error fetching plans:", error));
            }

            function renderPlans(plans) {
                plansContainer.innerHTML = '';
                const cycleId = planCycleSelect.value;

                plans.forEach(plan => {
                    const card = document.createElement('div');
                    card.className = 'plan-card bg-white border border-gray-200 rounded-lg p-4 cursor-pointer';
                    card.dataset.planId = plan.plan_name;

                    // Plan title
                    const planTitle = document.createElement('h3');
                    planTitle.className = 'font-bold text-lg text-center text-blue-600 plan_name';
                    planTitle.textContent = plan.plan_name;

                    // Pricing
                    const priceSection = document.createElement('div');
                    priceSection.className = 'text-center mt-2';
                    priceSection.innerHTML = `
                    <span class="text-2xl font-bold plan_price">${plan.currency === 'PKR' ? 'Rs.' : '$'}${(plan.total_price / cycleId).toFixed(2)}</span>
                    <span class="text-gray-500">/mo</span>
                `;

                    card.appendChild(planTitle);
                    card.appendChild(priceSection);

                    card.addEventListener('click', () => {
                        selectedPlan = plan.plan_name;

                        document.querySelectorAll('.plan-card').forEach(c => c.classList.remove('selected'));
                        card.classList.add('selected');

                        renderSelectedPlanDetails(plan);
                    });

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

            function renderSelectedPlanDetails(plan) {
                selectedPlanDetails.innerHTML = '';
                updatePriceDisplays(plan, planCycleSelect.value);

                const title = document.createElement('h3');
                title.className = 'font-bold text-lg mb-4';
                title.textContent = `${plan.plan_name} Plan Includes:`;
                selectedPlanDetails.appendChild(title);

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

                if (plan.plan_description) {
                    const desc = document.createElement('p');
                    desc.className = 'mt-4 text-sm text-gray-600';
                    desc.textContent = plan.plan_description;
                    selectedPlanDetails.appendChild(desc);
                }

                const upgradeNote = document.createElement('div');
                upgradeNote.className = 'mt-6 pt-6 border-t border-gray-200';
                upgradeNote.innerHTML = `
                <h4 class="font-medium text-gray-700 mb-2">Need more?</h4>
                <p class="text-sm text-gray-600">Upgrade to the next plan for more features.</p>
            `;
                selectedPlanDetails.appendChild(upgradeNote);
            }

            // Event listener for plan cycle change
            planCycleSelect.addEventListener("change", function () {
                fetchPlans(this.value);
            });

            // Initial fetch based on pre-selected or default cycle
            if (planCycleSelect.value) {
                fetchPlans(planCycleSelect.value);
            } else if (planCycleSelect.options.length > 0) {
                planCycleSelect.selectedIndex = 0;
                fetchPlans(planCycleSelect.value);
            }
        });
    </script>



@endpush
