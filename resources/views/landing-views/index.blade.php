@extends('layouts.landing')

@section('title', 'Home')

@push('styles')

@endpush

@section('content')

    <x-Landing.navbar />

    <div class="bg-white">

        <!-- Hero Section -->
        <div class="relative isolate px-6 lg:py-20 py-32 lg:px-8">
            <!-- Background Video -->
            <div class="absolute inset-0 w-full h-full">
                <video autoplay loop muted playsinline class="absolute inset-0 w-full h-full object-cover -z-10"
                       poster="{{ asset('videos/bg-video-img.jpeg') }}">
                    <source src="{{ asset('videos/bg-video.mp4') }}" type="video/mp4">
                </video>

                <!-- Dark Overlay for Better Text Visibility -->
                <div class="absolute inset-0 bg-black bg-opacity-30"></div>
            </div>

            <div class="relative z-10 mx-auto max-w-2xl pt-10 lg:pt-20   lg:pb-15">
                <div class="text-center">
                    <h1 class="text-5xl font-bold tracking-tight text-white lg:text-6xl drop-shadow-lg">
                        Elevate Your Property Management Experience
                    </h1>
                    <p class="mt-8 text-lg font-medium text-gray-200 sm:text-xl drop-shadow-md">
                        A complete solution for building owners, tenants, and financial management.
                    </p>
                    <div class="flex justify-center items-center mt-8">
                        <a href="#" target="_blank" rel="nofollow"
                           class="px-6 py-3 text-white font-medium text-lg border border-white rounded-full
                          transition-all duration-300 hover:bg-white hover:text-black">
                            Get Started
                        </a>
                    </div>
                </div>
            </div>
            <!-- New Sections at the Bottom -->
            <div class="relative z-10 max-w-6xl mx-auto mt-10">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Section 1 -->
                    <div class="bg-white bg-opacity-10 p-6 rounded-lg text-center border border-white border-opacity-20">
                        <h3 class="text font-semibold text-white" style="font-size: 18px;">Unit Tracking</h3>
                        <p class="mt-3 text-gray-200">Efficiently manage all your property units effortlessly.</p>
                    </div>

                    <!-- Section 2 -->
                    <div class="bg-white bg-opacity-10 p-6 rounded-lg text-center border border-white border-opacity-20">
                        <h3 class="text font-semibold text-white" style="font-size: 18px;">Maintenance Requests</h3>
                        <p class="mt-3 text-gray-200">Handle maintenance issues quickly and efficiently.</p>
                    </div>

                    <!-- Section 3 -->
                    <div class="bg-white bg-opacity-10 p-6 rounded-lg text-center border border-white border-opacity-20">
                        <h3 class="text font-semibold text-white" style="font-size: 18px;">Automated Lease Management</h3>
                        <p class="mt-3 text-gray-200">Simplify lease processes with automated tracking features.</p>
                    </div>
                </div>
            </div>

        </div>



        <!-- Pricing Section -->
        <div class="relative isolate bg-white px-6 py-24 sm:py-32 lg:px-8">
            <div class="absolute inset-x-0 -top-3 -z-10 transform-gpu overflow-hidden px-36 blur-3xl" aria-hidden="true">
                <div class="mx-auto aspect-1155/678 w-[72.1875rem] bg-linear-to-tr from-[#ff80b5] to-[#9089fc] opacity-30" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
            </div>
            <div class="mx-auto max-w-4xl text-center">
                <h2 class="text-base/7 font-semibold text-indigo-600">Pricing</h2>
                <p class="mt-2 text-5xl font-semibold tracking-tight text-balance text-gray-900 sm:text-6xl">Choose the right plan for you</p>
            </div>
            <p class="mx-auto mt-6 max-w-2xl text-center text-lg font-medium text-pretty text-gray-600 sm:text-xl/8">Choose an affordable plan that’s packed with the best features for engaging your audience, creating customer loyalty, and driving sales.</p>

            <div class="mt-6 flex justify-center">
                <div class="relative inline-flex">
                    <label for="billing-cycle" class="sr-only">Billing Cycle</label>
                    <select id="billing-cycle" name="billing-cycle" class="block w-100 appearance-none rounded-md border border-gray-300 bg-white px-4 py-2 pr-10 text-base font-medium text-gray-700 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        @forelse($planCycles as $planCycle)
                            <option value="{{ $planCycle }}">{{ $planCycle }} Month</option>
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

            <div class="mx-auto mt-10 grid max-w-6xl grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 card-grid">

            </div>
{{--            <div class="mx-auto mt-10 grid max-w-6xl grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">--}}
{{--                <!-- Card 1 -->--}}
{{--                <div class="group rounded-3xl bg-white p-8 ring-1 ring-gray-900/10 transition-all duration-300 hover:bg-gray-900 hover:text-white">--}}
{{--                    <h3 id="tier-hobby" class="text-base/7 font-semibold text-indigo-600 group-hover:text-white plan_name">Basic</h3>--}}
{{--                    <p class="mt-4 flex items-baseline gap-x-2">--}}
{{--                        <span class="text-5xl font-semibold tracking-tight text-gray-900 group-hover:text-white plan_price">$29</span>  <!--currency PKR -->--}}
{{--                        <span class="text-base text-gray-500 group-hover:text-white billing_cycle">/month</span>--}}
{{--                    </p>--}}
{{--                    <p class="mt-6 text-base/7 text-gray-600 group-hover:text-white plan_description">The perfect plan if you&#039;re just getting started with our product.</p>--}}
{{--                    <ul role="list" class="mt-8 space-y-3 text-sm/6 text-gray-600 sm:mt-10">--}}
{{--                        <li class="flex gap-x-3 group-hover:text-white">--}}
{{--                            <svg class="h-6 w-5 flex-none text-indigo-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">--}}
{{--                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />--}}
{{--                            </svg>--}}
{{--                            2x Building Management  <!-- where 2 is service_quantity and Building Management is a service_name-->--}}
{{--                        </li>--}}
{{--                        <li class="flex gap-x-3 group-hover:text-white">--}}
{{--                            <svg class="h-6 w-5 flex-none text-indigo-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">--}}
{{--                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />--}}
{{--                            </svg>--}}
{{--                            2x Manager Accounts--}}
{{--                        </li>--}}
{{--                        <li class="flex gap-x-3 group-hover:text-white">--}}
{{--                            <svg class="h-6 w-5 flex-none text-indigo-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">--}}
{{--                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />--}}
{{--                            </svg>--}}
{{--                            20x Staff per Building--}}
{{--                        </li>--}}
{{--                        <li class="flex gap-x-3 group-hover:text-white">--}}
{{--                            <svg class="h-6 w-5 flex-none text-indigo-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">--}}
{{--                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />--}}
{{--                            </svg>--}}
{{--                            5x Levels Per Buildings--}}
{{--                        </li>--}}
{{--                    </ul>--}}
{{--                    <a href="#" aria-describedby="tier-hobby" class="mt-8 block rounded-md px-3.5 py-2.5 text-center text-sm font-semibold text-indigo-600 ring-1 ring-indigo-200 ring-inset hover:bg-indigo-500 hover:text-white hover:ring-indigo-300 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:mt-10">Get started today</a>--}}
{{--                </div>--}}

{{--                <!-- Card 2 (Hover Color Reference) -->--}}
{{--                <div class="group relative rounded-3xl bg-gray-900 p-8 ring-1 ring-gray-900/10 transition-all duration-300">--}}
{{--                    <h3 id="tier-hobby" class="text-base/7 font-semibold text-indigo-400">Enterprise</h3>--}}
{{--                    <p class="mt-4 flex items-baseline gap-x-2">--}}
{{--                        <span class="text-5xl font-semibold tracking-tight text-white">$99</span>--}}
{{--                        <span class="text-base text-gray-500">/month</span>--}}
{{--                    </p>--}}
{{--                    <p class="mt-6 text-base/7 text-gray-300 ">The perfect plan if you&#039;re just getting started with our product.</p>--}}
{{--                    <ul role="list" class="mt-8 space-y-3 text-sm/6 text-gray-300 sm:mt-10">--}}
{{--                        <li class="flex gap-x-3">--}}
{{--                            <svg class="h-6 w-5 flex-none text-indigo-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">--}}
{{--                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />--}}
{{--                            </svg>--}}
{{--                            25 products--}}
{{--                        </li>--}}
{{--                        <li class="flex gap-x-3">--}}
{{--                            <svg class="h-6 w-5 flex-none text-indigo-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">--}}
{{--                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />--}}
{{--                            </svg>--}}
{{--                            Up to 10,000 subscribers--}}
{{--                        </li>--}}
{{--                        <li class="flex gap-x-3">--}}
{{--                            <svg class="h-6 w-5 flex-none text-indigo-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">--}}
{{--                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />--}}
{{--                            </svg>--}}
{{--                            Advanced analytics--}}
{{--                        </li>--}}
{{--                        <li class="flex gap-x-3">--}}
{{--                            <svg class="h-6 w-5 flex-none text-indigo-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">--}}
{{--                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />--}}
{{--                            </svg>--}}
{{--                            24-hour support response time--}}
{{--                        </li>--}}
{{--                    </ul>--}}
{{--                    <a href="#" aria-describedby="tier-enterprise" class="mt-8 block rounded-md bg-indigo-500 px-3.5 py-2.5 text-center text-sm font-semibold text-white shadow-xs hover:bg-indigo-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500 sm:mt-10">Get started today</a>--}}
{{--                </div>--}}

{{--                <!-- Card 3 -->--}}
{{--                <div class="group rounded-3xl bg-white p-8 ring-1 ring-gray-900/10 transition-all duration-300 hover:bg-gray-900 hover:text-white">--}}
{{--                    <h3 id="tier-hobby" class="text-base/7 font-semibold text-indigo-600 group-hover:text-white">Basic</h3>--}}
{{--                    <p class="mt-4 flex items-baseline gap-x-2">--}}
{{--                        <span class="text-5xl font-semibold tracking-tight text-gray-900 group-hover:text-white">$29</span>--}}
{{--                        <span class="text-base text-gray-500 group-hover:text-white">/month</span>--}}
{{--                    </p>--}}
{{--                    <p class="mt-6 text-base/7 text-gray-600 group-hover:text-white">The perfect plan if you&#039;re just getting started with our product.</p>--}}
{{--                    <ul role="list" class="mt-8 space-y-3 text-sm/6 text-gray-600 sm:mt-10">--}}
{{--                        <li class="flex gap-x-3 group-hover:text-white">--}}
{{--                            <svg class="h-6 w-5 flex-none text-indigo-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">--}}
{{--                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />--}}
{{--                            </svg>--}}
{{--                            25 products--}}
{{--                        </li>--}}
{{--                        <li class="flex gap-x-3 group-hover:text-white">--}}
{{--                            <svg class="h-6 w-5 flex-none text-indigo-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">--}}
{{--                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />--}}
{{--                            </svg>--}}
{{--                            Up to 10,000 subscribers--}}
{{--                        </li>--}}
{{--                        <li class="flex gap-x-3 group-hover:text-white">--}}
{{--                            <svg class="h-6 w-5 flex-none text-indigo-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">--}}
{{--                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />--}}
{{--                            </svg>--}}
{{--                            Advanced analytics--}}
{{--                        </li>--}}
{{--                        <li class="flex gap-x-3 group-hover:text-white">--}}
{{--                            <svg class="h-6 w-5 flex-none text-indigo-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">--}}
{{--                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />--}}
{{--                            </svg>--}}
{{--                            24-hour support response time--}}
{{--                        </li>--}}
{{--                    </ul>--}}
{{--                    <a href="#" aria-describedby="tier-hobby" class="mt-8 block rounded-md px-3.5 py-2.5 text-center text-sm font-semibold text-indigo-600 ring-1 ring-indigo-200 ring-inset hover:bg-indigo-500 hover:text-white hover:ring-indigo-300 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:mt-10">Get started today</a>--}}
{{--                </div>--}}

{{--            </div>--}}

        </div>

    </div>

    <x-Landing.footer />
@endsection

@push('scripts')

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const planCycleSelect = document.getElementById("billing-cycle");
            const plansContainer = document.querySelector(".card-grid"); // Select the container where cards will be inserted

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
                    })
                    .catch(error => console.error("Error fetching plans:", error));
            }

            function renderPlans(plans) {
                // Clear existing cards
                plansContainer.innerHTML = '';

                // Create a card for each plan
                plans.forEach(plan => {
                    const card = document.createElement('div');
                    card.className = 'group rounded-3xl bg-white p-8 ring-1 ring-gray-900/10 transition-all duration-300 hover:bg-gray-900 hover:text-white';

                    // Plan name
                    const planName = document.createElement('h3');
                    planName.className = 'text-base/7 font-semibold text-indigo-600 group-hover:text-white plan_name';
                    planName.id = `tier-${plan.plan_name.toLowerCase()}`;
                    planName.textContent = plan.plan_name;

                    // Price section
                    const priceSection = document.createElement('p');
                    priceSection.className = 'mt-4 flex items-baseline gap-x-2';

                    const price = document.createElement('span');
                    price.className = 'text-5xl font-semibold tracking-tight text-gray-900 group-hover:text-white plan_price';
                    price.textContent = `${plan.currency === 'PKR' ? 'Rs.' : '$'}${plan.total_price}`;

                    const billingCycle = document.createElement('span');
                    billingCycle.className = 'text-base text-gray-500 group-hover:text-white billing_cycle';
                    billingCycle.textContent = '/month';

                    priceSection.appendChild(price);
                    priceSection.appendChild(billingCycle);

                    // Description
                    const description = document.createElement('p');
                    description.className = 'mt-6 text-base/7 text-gray-600 group-hover:text-white plan_description';
                    description.textContent = plan.plan_description;

                    // Services list
                    const servicesList = document.createElement('ul');
                    servicesList.className = 'mt-8 space-y-3 text-sm/6 text-gray-600 sm:mt-10';
                    servicesList.setAttribute('role', 'list');

                    plan.services.forEach(service => {
                        const serviceItem = document.createElement('li');
                        serviceItem.className = 'flex gap-x-3 group-hover:text-white';

                        const checkIcon = document.createElement('svg');
                        checkIcon.className = 'h-6 w-5 flex-none text-indigo-600';
                        checkIcon.setAttribute('viewBox', '0 0 20 20');
                        checkIcon.setAttribute('fill', 'currentColor');
                        checkIcon.setAttribute('aria-hidden', 'true');
                        checkIcon.setAttribute('data-slot', 'icon');
                        checkIcon.innerHTML = '<path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />';

                        const serviceText = document.createTextNode(`${service.service_quantity}x ${service.service_name}`);

                        serviceItem.appendChild(checkIcon);
                        serviceItem.appendChild(serviceText);
                        servicesList.appendChild(serviceItem);
                    });

                    // Button
                    const button = document.createElement('a');
                    button.href = '#';
                    button.className = 'mt-8 block rounded-md px-3.5 py-2.5 text-center text-sm font-semibold text-indigo-600 ring-1 ring-indigo-200 ring-inset hover:bg-indigo-500 hover:text-white hover:ring-indigo-300 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:mt-10';
                    button.setAttribute('aria-describedby', `tier-${plan.plan_name.toLowerCase()}`);
                    button.textContent = 'Get started today';

                    // Append all elements to the card
                    card.appendChild(planName);
                    card.appendChild(priceSection);
                    card.appendChild(description);
                    card.appendChild(servicesList);
                    card.appendChild(button);

                    // Add card to container
                    plansContainer.appendChild(card);
                });
            }

            planCycleSelect.addEventListener("change", function () {
                fetchPlans(this.value);
            });

            if (planCycleSelect.value) {
                fetchPlans(planCycleSelect.value);
            }
        });
    </script>

{{--    <script>--}}
{{--        document.addEventListener("DOMContentLoaded", function () {--}}
{{--            const planCycleSelect = document.getElementById("billing-cycle");--}}

{{--            function fetchPlans(cycleId) {--}}
{{--                if (!cycleId) return;--}}

{{--                fetch(`{{ route('plans', ':planCycle') }}`.replace(':planCycle', 1), {--}}
{{--                    method: "GET",--}}
{{--                    headers: {--}}
{{--                        "Accept": "application/json"--}}
{{--                    }--}}
{{--                })--}}
{{--                    .then(response => response.json())--}}
{{--                    .then(data => {--}}
{{--                        let plans = data.plans;--}}
{{--                        console.log("Fetched Plans:", plans);--}}
{{--                    })--}}
{{--                    .catch(error => console.error("Error fetching plans:", error));--}}
{{--            }--}}

{{--            planCycleSelect.addEventListener("change", function () {--}}
{{--                fetchPlans(this.value);--}}
{{--            });--}}

{{--            if (planCycleSelect.value) {--}}
{{--                fetchPlans(planCycleSelect.value);--}}
{{--            }--}}
{{--        });--}}
{{--    </script>--}}

@endpush
