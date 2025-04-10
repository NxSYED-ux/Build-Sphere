<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Screen</title>
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://cdn.tailwindcss.com"></script>
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
</head>
<body class="bg-gray-50 min-h-screen">
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Complete Your Subscription</h1>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Left Side - Transaction Details -->
        <div class="lg:w-1/2 bg-white rounded-xl shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-6">Payment Information</h2>

            <!-- Stripe Elements will be injected here -->
            <div id="card-element" class="border border-gray-200 rounded-lg p-4 mb-4"></div>
            <div id="card-errors" role="alert" class="text-red-500 text-sm mb-4"></div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-medium mb-2" for="email">
                    Email Address
                </label>
                <input type="email" id="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex items-center mb-6">
                <input id="save-info" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="save-info" class="ml-2 block text-sm text-gray-700">
                    Save payment information for next time
                </label>
            </div>

            <button id="submit-button" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-md transition duration-200">
                Pay $9.99/month
            </button>

            <div class="mt-6 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-medium text-gray-700 mb-3">Order Summary</h3>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-600">Plan:</span>
                    <span class="font-medium">Basic (Monthly)</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-600">Subtotal:</span>
                    <span class="font-medium">$9.99</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-600">Tax:</span>
                    <span class="font-medium">$0.99</span>
                </div>
                <div class="flex justify-between text-lg font-bold mt-4 pt-4 border-t border-gray-200">
                    <span>Total:</span>
                    <span>$10.98</span>
                </div>
            </div>
        </div>

        <!-- Right Side - Plan Selection -->
        <div class="lg:w-1/2 bg-white rounded-xl shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-6">Choose Your Plan</h2>

            <!-- Billing Toggle -->
            <div class="flex justify-center mb-8">
                <div class="inline-flex bg-gray-100 rounded-lg p-1">
                    <button class="px-4 py-2 rounded-md font-medium" id="monthly-btn">Monthly</button>
                    <button class="px-4 py-2 rounded-md font-medium text-gray-500 hover:text-gray-700" id="yearly-btn">Yearly (Save 20%)</button>
                </div>
            </div>

            <!-- Horizontal Plan Cards -->
            <div class="plans-container overflow-x-auto flex gap-4 pb-4 mb-6 -mx-2 px-2 scrollbar-hide">
                <div class="plan-card bg-white border border-gray-200 rounded-lg p-4 cursor-pointer selected-plan">
                    <h3 class="font-bold text-lg text-center text-blue-600">Basic</h3>
                    <div class="text-center mt-2">
                        <span class="text-2xl font-bold">$9.99</span>
                        <span class="text-gray-500">/mo</span>
                    </div>
                </div>

                <div class="plan-card bg-white border border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-300">
                    <h3 class="font-bold text-lg text-center">Standard</h3>
                    <div class="text-center mt-2">
                        <span class="text-2xl font-bold">$19.99</span>
                        <span class="text-gray-500">/mo</span>
                    </div>
                </div>

                <div class="plan-card bg-white border border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-300">
                    <h3 class="font-bold text-lg text-center">Premium</h3>
                    <div class="text-center mt-2">
                        <span class="text-2xl font-bold">$29.99</span>
                        <span class="text-gray-500">/mo</span>
                    </div>
                </div>

                <div class="plan-card bg-white border border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-300">
                    <h3 class="font-bold text-lg text-center">Business</h3>
                    <div class="text-center mt-2">
                        <span class="text-2xl font-bold">$49.99</span>
                        <span class="text-gray-500">/mo</span>
                    </div>
                </div>
            </div>

            <!-- Selected Plan Details -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="font-bold text-lg mb-4">Basic Plan Includes:</h3>
                <ul class="space-y-3">
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>10 projects</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>5 team members</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>20GB storage</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Basic analytics</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Email support</span>
                    </li>
                </ul>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="font-medium text-gray-700 mb-2">Need more?</h4>
                    <p class="text-sm text-gray-600">Upgrade to Standard for unlimited projects and priority support.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // This would be replaced with actual Stripe.js implementation
    document.addEventListener('DOMContentLoaded', function() {
        // Simulate Stripe Elements
        const stripe = Stripe('pk_test_your_publishable_key');
        const elements = stripe.elements();
        const cardElement = elements.create('card');
        cardElement.mount('#card-element');

        // Plan selection
        const planCards = document.querySelectorAll('.plan-card');
        planCards.forEach(card => {
            card.addEventListener('click', function() {
                planCards.forEach(c => c.classList.remove('selected-plan', 'border-blue-500'));
                this.classList.add('selected-plan', 'border-blue-500');

                // Update payment button and order summary
                const planName = this.querySelector('h3').textContent;
                const planPrice = this.querySelector('span.text-2xl').textContent;
                document.querySelector('#submit-button').textContent = `Pay ${planPrice}/month`;

                // Update order summary
                document.querySelector('.order-summary-plan').textContent = `${planName} (Monthly)`;
                document.querySelector('.order-summary-price').textContent = `${planPrice}`;
            });
        });

        // Billing toggle
        const monthlyBtn = document.getElementById('monthly-btn');
        const yearlyBtn = document.getElementById('yearly-btn');

        monthlyBtn.addEventListener('click', function() {
            monthlyBtn.classList.add('bg-blue-600', 'text-white');
            monthlyBtn.classList.remove('text-gray-500');
            yearlyBtn.classList.remove('bg-blue-600', 'text-white');
            yearlyBtn.classList.add('text-gray-500');
        });

        yearlyBtn.addEventListener('click', function() {
            yearlyBtn.classList.add('bg-blue-600', 'text-white');
            yearlyBtn.classList.remove('text-gray-500');
            monthlyBtn.classList.remove('bg-blue-600', 'text-white');
            monthlyBtn.classList.add('text-gray-500');
        });
    });
</script>
</body>
</html>
