<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Screen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        .plan-scroll {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
        }
        /* Custom scrollbar */
        .plan-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .plan-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .plan-scroll::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }
        .plan-scroll::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>
<body class="bg-gray-50">
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Checkout</h1>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Left side - Payment Details -->
        <div class="lg:w-1/2 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-6">Payment Details</h2>

            <!-- Stripe Elements will be inserted here -->
            <div id="card-element" class="border border-gray-300 rounded-lg p-3 mb-4"></div>
            <div id="card-errors" role="alert" class="text-red-500 text-sm mb-4"></div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-medium mb-2" for="email">
                    Email Address
                </label>
                <input type="email" id="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-medium mb-2" for="name">
                    Full Name
                </label>
                <input type="text" id="name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button id="submit-payment" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-md transition duration-200">
                Pay Now
            </button>

            <div class="mt-4 flex items-center">
                <img src="https://via.placeholder.com/40" alt="Secure Payment" class="h-8 mr-2">
                <span class="text-gray-500 text-sm">Payments are secure and encrypted</span>
            </div>
        </div>

        <!-- Right side - Plans -->
        <div class="lg:w-1/2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-6">Select Your Plan</h2>

                <!-- Plans Scrollable Container -->
                <div class="plan-scroll space-y-4">
                    <!-- Basic Plan (Selected) -->
                    <div class="group rounded-3xl bg-white p-8 ring-2 ring-blue-500 transition-all duration-300 hover:bg-gray-900 hover:text-white bg-blue-50">
                        <h3 id="tier-hobby" class="text-base/7 font-semibold text-indigo-600 group-hover:text-white">Basic</h3>
                        <p class="mt-4 flex items-baseline gap-x-2">
                            <span class="text-5xl font-semibold tracking-tight text-gray-900 group-hover:text-white">$29</span>
                            <span class="text-base text-gray-500 group-hover:text-white">/month</span>
                        </p>
                        <p class="mt-6 text-base/7 text-gray-600 group-hover:text-white">The perfect plan if you're just getting started with our product.</p>
                        <ul role="list" class="mt-8 space-y-3 text-sm/6 text-gray-600 sm:mt-10">
                            <li class="flex gap-x-3 group-hover:text-white">
                                <svg class="h-6 w-5 flex-none text-indigo-600 group-hover:text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                </svg>
                                25 products
                            </li>
                            <li class="flex gap-x-3 group-hover:text-white">
                                <svg class="h-6 w-5 flex-none text-indigo-600 group-hover:text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                </svg>
                                Up to 10,000 subscribers
                            </li>
                            <li class="flex gap-x-3 group-hover:text-white">
                                <svg class="h-6 w-5 flex-none text-indigo-600 group-hover:text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                </svg>
                                Advanced analytics
                            </li>
                            <li class="flex gap-x-3 group-hover:text-white">
                                <svg class="h-6 w-5 flex-none text-indigo-600 group-hover:text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                </svg>
                                24-hour support response time
                            </li>
                        </ul>
                        <div class="mt-8 block rounded-md px-3.5 py-2.5 text-center text-sm font-semibold text-white bg-blue-600">
                            Currently Selected
                        </div>
                    </div>

                    <!-- Pro Plan -->
                    <div class="group rounded-3xl bg-white p-8 ring-1 ring-gray-900/10 transition-all duration-300 hover:bg-gray-900 hover:text-white">
                        <h3 id="tier-pro" class="text-base/7 font-semibold text-indigo-600 group-hover:text-white">Pro</h3>
                        <p class="mt-4 flex items-baseline gap-x-2">
                            <span class="text-5xl font-semibold tracking-tight text-gray-900 group-hover:text-white">$59</span>
                            <span class="text-base text-gray-500 group-hover:text-white">/month</span>
                        </p>
                        <p class="mt-6 text-base/7 text-gray-600 group-hover:text-white">For growing businesses that need more advanced features.</p>
                        <ul role="list" class="mt-8 space-y-3 text-sm/6 text-gray-600 sm:mt-10">
                            <li class="flex gap-x-3 group-hover:text-white">
                                <svg class="h-6 w-5 flex-none text-indigo-600 group-hover:text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                </svg>
                                100 products
                            </li>
                            <li class="flex gap-x-3 group-hover:text-white">
                                <svg class="h-6 w-5 flex-none text-indigo-600 group-hover:text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                </svg>
                                Up to 50,000 subscribers
                            </li>
                            <li class="flex gap-x-3 group-hover:text-white">
                                <svg class="h-6 w-5 flex-none text-indigo-600 group-hover:text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                </svg>
                                Advanced analytics
                            </li>
                            <li class="flex gap-x-3 group-hover:text-white">
                                <svg class="h-6 w-5 flex-none text-indigo-600 group-hover:text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                </svg>
                                12-hour support response time
                            </li>
                        </ul>
                        <a href="#" aria-describedby="tier-pro" class="mt-8 block rounded-md px-3.5 py-2.5 text-center text-sm font-semibold text-indigo-600 ring-1 ring-indigo-200 ring-inset hover:bg-indigo-500 hover:text-white hover:ring-indigo-300 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:mt-10">Select Plan</a>
                    </div>

                    <!-- Enterprise Plan -->
                    <div class="group rounded-3xl bg-white p-8 ring-1 ring-gray-900/10 transition-all duration-300 hover:bg-gray-900 hover:text-white">
                        <h3 id="tier-enterprise" class="text-base/7 font-semibold text-indigo-600 group-hover:text-white">Enterprise</h3>
                        <p class="mt-4 flex items-baseline gap-x-2">
                            <span class="text-5xl font-semibold tracking-tight text-gray-900 group-hover:text-white">$99</span>
                            <span class="text-base text-gray-500 group-hover:text-white">/month</span>
                        </p>
                        <p class="mt-6 text-base/7 text-gray-600 group-hover:text-white">For large organizations with custom needs.</p>
                        <ul role="list" class="mt-8 space-y-3 text-sm/6 text-gray-600 sm:mt-10">
                            <li class="flex gap-x-3 group-hover:text-white">
                                <svg class="h-6 w-5 flex-none text-indigo-600 group-hover:text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                </svg>
                                Unlimited products
                            </li>
                            <li class="flex gap-x-3 group-hover:text-white">
                                <svg class="h-6 w-5 flex-none text-indigo-600 group-hover:text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                </svg>
                                Unlimited subscribers
                            </li>
                            <li class="flex gap-x-3 group-hover:text-white">
                                <svg class="h-6 w-5 flex-none text-indigo-600 group-hover:text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                </svg>
                                Premium analytics
                            </li>
                            <li class="flex gap-x-3 group-hover:text-white">
                                <svg class="h-6 w-5 flex-none text-indigo-600 group-hover:text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                </svg>
                                1-hour support response time
                            </li>
                        </ul>
                        <a href="#" aria-describedby="tier-enterprise" class="mt-8 block rounded-md px-3.5 py-2.5 text-center text-sm font-semibold text-indigo-600 ring-1 ring-indigo-200 ring-inset hover:bg-indigo-500 hover:text-white hover:ring-indigo-300 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:mt-10">Select Plan</a>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="mt-8 border-t border-gray-200 pt-6">
                    <h3 class="font-semibold text-gray-700 mb-4">Order Summary</h3>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Basic Plan</span>
                        <span class="font-medium">$29.00</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Tax</span>
                        <span class="font-medium">$2.32</span>
                    </div>
                    <div class="flex justify-between mt-4 pt-4 border-t border-gray-200">
                        <span class="font-semibold text-gray-800">Total</span>
                        <span class="font-bold text-lg text-gray-800">$31.32</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize Stripe
    const stripe = Stripe('pk_test_your_publishable_key_here');
    const elements = stripe.elements();

    // Create card element
    const cardElement = elements.create('card', {
        style: {
            base: {
                fontSize: '16px',
                color: '#424770',
                '::placeholder': {
                    color: '#aab7c4',
                },
            },
            invalid: {
                color: '#9e2146',
            },
        },
    });

    cardElement.mount('#card-element');

    // Handle form submission
    const form = document.getElementById('payment-form');
    const cardErrors = document.getElementById('card-errors');

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const {error, paymentMethod} = await stripe.createPaymentMethod({
            type: 'card',
            card: cardElement,
            billing_details: {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
            },
        });

        if (error) {
            cardErrors.textContent = error.message;
        } else {
            // Handle successful payment method creation
            console.log('PaymentMethod:', paymentMethod);
            // Here you would typically send the paymentMethod.id to your server
        }
    });

    // Plan selection functionality
    document.querySelectorAll('[aria-describedby^="tier-"]').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const planId = this.getAttribute('aria-describedby').replace('tier-', '');

            // Update selected plan styling
            document.querySelectorAll('.group.rounded-3xl').forEach(card => {
                card.classList.remove('ring-2', 'ring-blue-500', 'bg-blue-50');
                card.querySelector('a').classList.remove('bg-blue-600', 'text-white');
                card.querySelector('a').classList.add('text-indigo-600', 'ring-indigo-200');
                card.querySelector('a').textContent = 'Select Plan';
            });

            const selectedCard = this.closest('.group.rounded-3xl');
            selectedCard.classList.add('ring-2', 'ring-blue-500', 'bg-blue-50');
            const selectButton = selectedCard.querySelector('a');
            selectButton.classList.remove('text-indigo-600', 'ring-indigo-200');
            selectButton.classList.add('bg-blue-600', 'text-white');
            selectButton.textContent = 'Currently Selected';

            // Update order summary
            const planName = selectedCard.querySelector('h3').textContent;
            const planPrice = selectedCard.querySelector('span.text-5xl').textContent;
            document.querySelector('.order-summary .text-gray-600:first-child').textContent = `${planName} Plan`;
            document.querySelector('.order-summary .font-medium:first-child').textContent = `$${parseInt(planPrice.replace('$', ''))}.00`;

            // Recalculate total (you might want to adjust tax calculation based on plan)
            const subtotal = parseInt(planPrice.replace('$', ''));
            const tax = Math.round(subtotal * 0.08 * 100) / 100;
            const total = subtotal + tax;

            document.querySelector('.order-summary .text-gray-600:nth-child(2)').textContent = `$${tax.toFixed(2)}`;
            document.querySelector('.font-bold.text-lg').textContent = `$${total.toFixed(2)}`;
        });
    });
</script>
</body>
</html>
