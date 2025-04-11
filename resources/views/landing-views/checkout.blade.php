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
    </style>

@endpush

@section('content')

    <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Complete Your Subscription</h1>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Left Side - Payment Details -->
        <div class="lg:w-1/2 bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-6">Payment Information</h2>

            <!-- Stripe Elements will be injected here -->
            <div id="card-element" class="border border-gray-200 rounded-lg p-4 mb-4"></div>
            <div id="card-errors" role="alert" class="text-red-500 text-sm mb-4"></div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-medium mb-2" for="email">
                    Email Address
                </label>
                <input type="email" id="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="hidden" id="selectedPlanId" value="">
                <input type="hidden" id="selectedPlanCycle" value="">
                <input type="hidden" id="selectedBillingCycleId" value="">
            </div>

            <div class="flex items-center mb-6">
                <input id="save-info" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="save-info" class="ml-2 block text-sm text-gray-700">
                    Save payment information for next time
                </label>
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

        <!-- Right Side - Plan Selection -->
        <div class="lg:w-1/2 bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-6">Choose Your Plan</h2>

            <!-- Billing Toggle -->
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
    </div>
</div>

@endsection

@push('scripts')

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        document.addEventListener('DOMContentLoaded', async function () {
            const stripe = Stripe("{{ config('services.stripe.public') }}");
            const elements = stripe.elements();
            const card = elements.create("card");
            card.mount("#card-element");

            const submitButton = document.getElementById("submit-button");
            const emailInput = document.getElementById("email");
            const selectedPlanId = document.getElementById('selectedPlanId');
            const selectedPlanCycle = document.getElementById('selectedPlanCycle');
            const selectedBillingCycleId = document.getElementById('selectedBillingCycleId');


            // Payment submission
            if (submitButton) {
                submitButton.addEventListener("click", async () => {
                    submitButton.disabled = true;

                    const { paymentMethod, error } = await stripe.createPaymentMethod({
                        type: 'card',
                        card: card,
                        billing_details: {
                            email: emailInput.value
                        }
                    });

                    if (error) {
                        document.getElementById("card-errors").textContent = error.message;
                        submitButton.disabled = false;
                        return;
                    }

                    const planId = selectedPlanId.value;
                    const planCycle = selectedPlanCycle.value;
                    const billingCycleId = selectedBillingCycleId.value;
                    const email = emailInput.value;

                    try {
                        const dataToSend = {
                            payment_method_id: paymentMethod.id,
                            email: email,
                            plan_id: planId,
                            plan_cycle: planCycle,
                            plan_cycle_id: billingCycleId
                        };

                        // Show alert with all data being sent
                        alert("About to send the following data:\n" + JSON.stringify(dataToSend, null, 2));

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

                        if (result.requires_action) {
                            const confirmResult = await stripe.confirmCardPayment(result.client_secret);

                            if (confirmResult.error) {
                                alert("Payment failed: " + confirmResult.error.message);
                            } else if (confirmResult.paymentIntent.status === "succeeded") {
                                alert("Payment successful!");
                                window.location.href = "{{ route('login') }}"  ;
                            }
                        } else if (result.success) {
                            alert("Payment successful!");
                            window.location.href = "{{ route('login') }}";
                        } else {
                            alert("Payment failed. Please try again.");
                        }

                    } catch (err) {
                        console.error("Error:", err);
                        alert("An error occurred. Please try again.");
                    }


                    submitButton.disabled = false;
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
                const planTotalPrice = plan.total_price;
                const formattedPrice = monthlyPrice.toFixed(2);
                const formattedTotalPrice = planTotalPrice.toFixed(2);
                const currencySymbol = plan.currency === 'PKR' ? 'Rs.' : '$';

                const buttonPrice = document.getElementById('button-price');
                if (buttonPrice) {
                    buttonPrice.textContent = `${currencySymbol}${formattedPrice}`;
                }

                const planNameElements = document.querySelectorAll('.selected_plan_name');
                const planCycleElements = document.querySelectorAll('.selected_plan_cycle');
                const subtotalPrice = document.getElementById('plan_subtotal_price');
                const totalPrice = document.getElementById('plan_total_price');

                const selectedPlanId = document.getElementById('selectedPlanId');
                const selectedPlanCycle = document.getElementById('selectedPlanCycle');
                const selectedBillingCycleId = document.getElementById('selectedBillingCycleId');

                planNameElements.forEach(el => el.textContent = plan.plan_name);
                planCycleElements.forEach(el => el.textContent = cycleId);

                selectedPlanId.value = plan.plan_id;
                selectedPlanCycle.value = cycleId;
                selectedBillingCycleId.value = plan.billing_cycle_id;

                if (subtotalPrice) subtotalPrice.textContent = `${currencySymbol}${formattedTotalPrice}`;
                if (totalPrice) totalPrice.textContent = `${currencySymbol}${formattedTotalPrice}`;

            }

            function fetchPlans(cycleId) {
                if (!cycleId) return;

                fetch(`{{ route('plans', ':planCycle') }}`.replace(':planCycle', cycleId), {
                    method: "GET",
                    headers: {
                        "Accept": "application/json"
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        let plans = data.plans;
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
                // Clear existing cards
                plansContainer.innerHTML = '';
                const cycleId = planCycleSelect.value;

                // Create a card for each plan
                plans.forEach(plan => {
                    const card = document.createElement('div');
                    card.className = 'plan-card bg-white border border-gray-200 rounded-lg p-4 cursor-pointer';
                    card.setAttribute('data-plan-id', plan.plan_name); // Use plan_name as data attribute

                    // Plan name
                    const planName = document.createElement('h3');
                    planName.className = 'font-bold text-lg text-center text-blue-600 plan_name';
                    planName.textContent = plan.plan_name;

                    // Price section
                    const priceSection = document.createElement('div');
                    priceSection.className = 'text-center mt-2';
                    priceSection.innerHTML = `
                <span class="text-2xl font-bold plan_price">${plan.currency === 'PKR' ? 'Rs.' : '$'}${plan.total_price / cycleId}</span>
                <span class="text-gray-500">/mo</span>
            `;

                    // Append plan name and price to the card
                    card.appendChild(planName);
                    card.appendChild(priceSection);

                    // Add event listener to the card for selection
                    card.addEventListener('click', function() {
                        selectedPlan = plan.plan_name; // Store the plan name
                        renderSelectedPlanDetails(plan); // Render details of the selected plan
                    });

                    // Add card to container
                    plansContainer.appendChild(card);
                });

                // If no plan is selected, select the first one by default
                if (!selectedPlan && plans.length > 0) {
                    selectedPlan = plans.find(plan => plan.plan_name === @json($selectedPackage)); // Match the selectedPackage from PHP
                    renderSelectedPlanDetails(plans[0]); // Render the details of the first plan
                } else {
                    // If a plan is already selected, render its details
                    const selectedPlanObj = plans.find(plan => plan.plan_name === selectedPlan);
                    if (selectedPlanObj) {
                        renderSelectedPlanDetails(selectedPlanObj);
                    }
                }
            }

            function renderSelectedPlanDetails(plan) {

                selectedPlanDetails.innerHTML = '';
                updatePriceDisplays(plan, planCycleSelect.value);

                const planTitle = document.createElement('h3');
                planTitle.className = 'font-bold text-lg mb-4';
                planTitle.textContent = `${plan.plan_name} Plan Includes:`;
                selectedPlanDetails.appendChild(planTitle);

                const servicesList = document.createElement('ul');
                servicesList.className = 'space-y-3';

                plan.services.forEach(service => {
                    const serviceItem = document.createElement('li');

                    serviceItem.className = 'flex gap-x-3 group-hover:text-white items-start';

                    serviceItem.innerHTML = `
                        <svg class="h-6 w-5 flex-none text-indigo-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8
                            10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1
                            1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1
                            1.05-.143Z" clip-rule="evenodd" />
                        </svg>
                        <span>${service.service_quantity}x ${service.service_name}</span>
                    `;
                    servicesList.appendChild(serviceItem);
                });

                selectedPlanDetails.appendChild(servicesList);

                if (plan.plan_description) {
                    const description = document.createElement('p');
                    description.className = 'mt-4 text-sm text-gray-600';
                    description.textContent = plan.plan_description;
                    selectedPlanDetails.appendChild(description);
                }

                const actionMessage = document.createElement('div');
                actionMessage.className = 'mt-6 pt-6 border-t border-gray-200';
                actionMessage.innerHTML = `
            <h4 class="font-medium text-gray-700 mb-2">Need more?</h4>
            <p class="text-sm text-gray-600">Upgrade to the next plan for more features.</p>
        `;
                selectedPlanDetails.appendChild(actionMessage);
            }

            planCycleSelect.addEventListener("change", function () {
                fetchPlans(this.value);
            });

            if (planCycleSelect.value) {
                fetchPlans(planCycleSelect.value);
            } else if (planCycleSelect.options.length > 0) {
                planCycleSelect.selectedIndex = 0; // Select the first option if none is selected
                fetchPlans(planCycleSelect.value);
            }

        });

    </script>

@endpush
