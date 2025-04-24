@extends('layouts.app')

@section('title', 'Organization Profile')

@push('styles')
    <style>
        :root {
            --secondary-color: #1cc88a;
        }

        body {
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        .profile-card {
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: none;
            overflow: hidden;
            transition: transform 0.3s ease;
            background-color: var(--body-card-bg);
        }

        .profile-card h3,p{
            color: var(--sidenavbar-text-color) !important;
        }

        .profile-card:hover {
            transform: translateY(-5px);
        }

        .profile-card a{
            color: var(--color-blue) !important;
        }

        .org-logo {
            width: 100%;
            margin: auto;
            height: 150px;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            position: relative;
            padding-bottom: 10px;
            margin-bottom: 20px;
            color: var(--sidenavbar-text-color);
        }

        .section-title:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 3px;
            background: var(--color-blue);
        }

        .detail-item {
            margin-bottom: 15px;
        }

        .detail-label {
            font-weight: 600;
            font-size: 14px;
            color: var(--sidenavbar-text-color);
            margin-bottom: 5px;
        }

        .detail-value {
            color: var(--sidenavbar-text-color);
        }

        .plan-card {
            border-left: 4px solid var(--color-blue);
        }

        .plan-card.featured {
            border-left: 4px solid var(--secondary-color);
        }

        .nav-pills .nav-link.active {
            background-color: var(--color-blue);
        }

        .tab-content {
            background-color: var(--body-background-color);
            border-radius: 0 0 10px 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        /* Gauge Meter Styles */
        #chartdiv {
            width: 100%;
            height: 200px;
        }
        .amcharts-export-menu {
            display: none !important;
        }

        /* Add this to your stylesheet */
        .StripeElement {
            box-sizing: border-box;
            height: 40px;
            padding: 10px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            background-color: white;
        }

        .StripeElement--focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .StripeElement--invalid {
            border-color: #dc3545;
        }

        .StripeElement--webkit-autofill {
            background-color: #fefde5 !important;
        }

    </style>
@endpush

@section('content')

    <!-- Top Navbar -->
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => url('owner_manager_dashboard'), 'label' => 'Dashboard'],
            ['url' => '', 'label' => 'Organization Profile']
        ]"
    />

    <!-- Side Navbar -->
    <x-Owner.side-navbar :openSections="['Dashboard']" />
    <x-error-success-model />


    <div id="main">
        <div class="container py-5 mt-3">
            <div class="row">
                <!-- Left Column - Organization Details -->
                <div class="col-lg-4 mb-4">
                    <div class="profile-card p-4 text-center shadow border-0">
                        <div class="d-flex justify-content-center mb-3">
                            <div class="org-logo-container position-relative">
                                <img src="{{ asset('img/organization_placeholder.png') }}" alt="Bahria Town Logo" class="org-logo  border border-3">
                            </div>
                        </div>

                        <h3 class="mb-1 fw-bold">Bahria Town</h3>
                        <p class="small mb-3">Heights Management System</p>

                        <div class="d-flex justify-content-center gap-2 mb-4 flex-wrap">
                            <span class="badge bg-success bg-opacity-10 text-success py-2 px-3 rounded-pill">
                                <i class="fas fa-check-circle me-1"></i> Active
                            </span>
                                            <span class="badge bg-info bg-opacity-10 text-info py-2 px-3 rounded-pill">
                                <i class="fas fa-shield-alt me-1"></i> Verified
                            </span>
                        </div>

                        <div class="text-start mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="section-title mb-0 fw-semibold">Organization Details</h5>
                                <a href="" class="rounded-pill py-2 fw-medium">
                                    <i class="fas fa-edit me-2 text-warning fs-5"></i>
                                </a>
                            </div>

                            <div class="detail-list">

                                <div class="row">
                                <div class="col-6 detail-item py-1">
                                    <div class="detail-label">Membership ID</div>
                                    <div class="detail-value fw-medium">ORG-789456</div>
                                </div>

                                <div class="col-6 detail-item pb-1">
                                    <div class="detail-label">Registration Date</div>
                                    <div class="detail-value fw-medium">15 Jan 2023</div>
                                </div>
                                </div>

                                <div class="detail-item py-1">
                                    <div class="detail-label">Contact Email</div>
                                    <div class="detail-value fw-medium d-flex align-items-center">
                                        <i class="fas fa-envelope me-2 text-primary"></i>
                                        <a href="" class="text-decoration-none">contact@techsolutions.com</a>
                                    </div>
                                </div>

                                <div class="detail-item py-1">
                                    <div class="detail-label">Phone Number</div>
                                    <div class="detail-value fw-medium d-flex align-items-center">
                                        <i class="fas fa-phone me-2 text-primary"></i>
                                        <a href="" class="text-decoration-none">+1 (555) 123-4567</a>
                                    </div>
                                </div>

                                <div class="detail-item py-1 pb-0">
                                    <div class="detail-label">Address</div>
                                    <div class="detail-value fw-medium d-flex">
                                        <i class="fas fa-map-marker-alt me-2 mt-1 text-primary"></i>
                                        <div>
                                            123 Tech Park, Silicon Valley<br>
                                            San Francisco, CA 94107<br>
                                            United States
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-start mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="section-title mb-0 fw-semibold">Owner Details</h5>
                                <a href="" class="rounded-pill py-2 fw-medium">
                                    <i class="fas fa-edit me-2 text-warning fs-5"></i>
                                </a>
                            </div>

                            <div class="d-flex align-items-center mb-3 p-2 rounded-3 bg-light">
                                <img src="{{ asset('img/placeholder-profile.png') }}" alt="John Smith" class="rounded-circle me-3" width="50" height="50">
                                <div>
                                    <h6 class="mb-0 fw-medium text-dark">John Smith</h6>
                                    <small class="d-flex align-items-center text-dark">
                                        <i class="fas fa-briefcase me-1 small text-dark"></i> Owner & CEO
                                    </small>
                                </div>
                            </div>

                            <div class="detail-list">
                                <div class="detail-item py-1">
                                    <div class="detail-label small">Contact Email</div>
                                    <div class="detail-value fw-medium d-flex align-items-center">
                                        <i class="fas fa-envelope me-2 text-primary"></i>
                                        <a href="" class="text-decoration-none">john.smith@techsolutions.com</a>
                                    </div>
                                </div>

                                <div class="detail-item py-1">
                                    <div class="detail-label small">Mobile</div>
                                    <div class="detail-value fw-medium d-flex align-items-center">
                                        <i class="fas fa-mobile-alt me-2 text-primary"></i>
                                        <a href="" class="text-decoration-none">+1 (555) 987-6543</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Plan & Payment Details -->
                <div class="col-lg-8">
                    <div class="profile-card p-4 mb-4 shadow">
                        <h4 class="section-title">Current Plan</h4>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="plan-card p-4 h-100">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h5 class="mb-1">Premium Plan</h5>
                                            <span class="badge bg-success">Active</span>
                                        </div>
                                        <div class="text-primary fw-bold">$99<small>/mo</small></div>
                                    </div>

                                    <table class="table table-borderless" style="background-color: var(--sidenavbar-body-color);">
                                        <thead>
                                        <tr class="border-bottom">
                                            <th class="text-start">Service</th>
                                            <th class="text-end">Qty</th>
                                            <th class="text-end">Used</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>Team Members</td>
                                            <td class="text-end">50</td>
                                            <td class="text-end">30</td>
                                        </tr>
                                        <tr>
                                            <td>Storage</td>
                                            <td class="text-end">500GB</td>
                                            <td class="text-end">250GB</td>
                                        </tr>
                                        <tr>
                                            <td>Premium Support</td>
                                            <td class="text-end">10</td>
                                            <td class="text-end">6</td>
                                        </tr>
                                        <tr>
                                            <td>API Access</td>
                                            <td class="text-end">25</td>
                                            <td class="text-end">15</td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <div class="mt-3 pt-3 border-top">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Billing Cycle:</span>
                                            <span class="fw-medium">Annual</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Next Billing:</span>
                                            <span class="fw-medium">15 Jan 2024</span>
                                        </div>
                                        <div class="d-flex justify-content-between fw-bold">
                                            <span>Total:</span>
                                            <span>$1,188.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="plan-card featured p-4 h-100 d-flex flex-column justify-content-between">
                                    <h5 class=" fw-semibold">Plan Used</h5>

                                    <div id="chartdiv" class="flex-grow-1 my-1"></div>

                                    <div class="mt-auto pt-2">
                                        <button class="btn btn-success w-100 mb-3 py-2 rounded-1">
                                            <i class="fas fa-arrow-up me-2"></i> Upgrade Plan
                                        </button>
                                        <button class="btn btn-outline-danger w-100 py-2 rounded-1">
                                            <i class="fas fa-times me-2"></i> Cancel Subscription
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <div class="profile-card p-4 mb-4 shadow">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="section-title mb-0">Payment Methods</h4>
                            <button class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i> Add Payment Method
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

{{--                        <div class="row">--}}
{{--                            <!-- Visa Card -->--}}
{{--                            <x-payment-card--}}
{{--                                    cardNumber="4242 4242 4242 4242"--}}
{{--                                    expiry="12/25"--}}
{{--                                    cvv="123"--}}
{{--                                    cardType="visa"--}}
{{--                                    isPrimary="true"--}}
{{--                            />--}}

{{--                            <!-- Mastercard -->--}}
{{--                            <x-payment-card--}}
{{--                                    cardNumber="5555 5555 5555 4444"--}}
{{--                                    expiry="06/24"--}}
{{--                                    cvv="456"--}}
{{--                                    cardType="mastercard"--}}
{{--                            />--}}
{{--                        </div>--}}
                    </div>

                    <!-- Billing History -->
                    <div class="profile-card p-4 shadow">
                        <ul class="nav nav-pills mb-4" id="billing-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="invoices-tab" data-bs-toggle="pill" data-bs-target="#invoices" type="button" role="tab">Invoices</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="transactions-tab" data-bs-toggle="pill" data-bs-target="#transactions" type="button" role="tab">Transactions</button>
                            </li>
                        </ul>

                        <div class="tab-content" id="billing-tabContent">
                            <div class="tab-pane fade show active" id="invoices" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                        <tr>
                                            <th>Invoice #</th>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>INV-2023-789</td>
                                            <td>15 Dec 2023</td>
                                            <td>$99.00</td>
                                            <td><span class="badge bg-success">Paid</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>INV-2023-788</td>
                                            <td>15 Nov 2023</td>
                                            <td>$99.00</td>
                                            <td><span class="badge bg-success">Paid</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>INV-2023-787</td>
                                            <td>15 Oct 2023</td>
                                            <td>$99.00</td>
                                            <td><span class="badge bg-success">Paid</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="transactions" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                        <tr>
                                            <th>Transaction ID</th>
                                            <th>Date</th>
                                            <th>Description</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>TXN-789456</td>
                                            <td>15 Dec 2023</td>
                                            <td>Monthly Subscription</td>
                                            <td>$99.00</td>
                                            <td><span class="badge bg-success">Completed</span></td>
                                        </tr>
                                        <tr>
                                            <td>TXN-789455</td>
                                            <td>15 Nov 2023</td>
                                            <td>Monthly Subscription</td>
                                            <td>$99.00</td>
                                            <td><span class="badge bg-success">Completed</span></td>
                                        </tr>
                                        <tr>
                                            <td>TXN-789454</td>
                                            <td>15 Oct 2023</td>
                                            <td>Monthly Subscription</td>
                                            <td>$99.00</td>
                                            <td><span class="badge bg-success">Completed</span></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this right after the Payment Methods card div -->
    <div class="modal fade" id="paymentMethodModal" tabindex="-1" aria-labelledby="paymentMethodModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentMethodModalLabel">Add Payment Method</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="payment-form">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email (optional)</label>
                            <input type="email" class="form-control" id="email" placeholder="email@example.com">
                        </div>

                        <!-- Stripe Elements will be injected here -->
                        <div id="card-element" class="border border-gray-200 rounded-lg p-4 mb-4"></div>
                        <div id="card-errors" role="alert" class="text-red-500 text-sm mb-4"></div>

                        <button type="submit" class="btn btn-primary w-100" id="submit-button">
                            <span id="button-text">Add Payment Method</span>
                            <span id="button-spinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Sample function for payment method actions
            document.querySelectorAll('.payment-card .btn-outline-danger').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (confirm('Are you sure you want to remove this payment method?')) {
                        this.closest('.col-md-6').remove();
                    }
                });
            });
        });
    </script>

    <!-- Resources -->
    <script src="https://cdn.amcharts.com/lib/4/core.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>

    <!-- Chart code -->
    <script>
        // Chart references
        let chart;
        let hand;
        let label;

        // Helper: Get CSS variable value
        function getCssVar(name) {
            return getComputedStyle(document.documentElement)
                .getPropertyValue(name)
                .trim();
        }

        // Update gauge value (0-100)
        function updateGaugeValue(finalValue) {
            finalValue = Math.max(0, Math.min(100, finalValue));
            var to100 = new am4core.Animation(hand, {
                property: "value",
                to: 100
            }, 1000, am4core.ease.cubicOut);

            to100.events.on("animationended", function() {
                new am4core.Animation(hand, {
                    property: "value",
                    to: finalValue
                }, 1000, am4core.ease.cubicOut).start();
            });
            to100.start();
        }

        // Initialize chart with CSS colors
        am4core.ready(function() {
            am4core.useTheme(am4themes_animated);

            // Create chart
            chart = am4core.create("chartdiv", am4charts.GaugeChart);
            chart.innerRadius = am4core.percent(82);
            chart.logo.disabled = true;

            // Normal axis
            var axis = chart.xAxes.push(new am4charts.ValueAxis());
            axis.min = 0;
            axis.max = 100;
            axis.strictMinMax = true;
            axis.fontSize = 12;
            axis.renderer.radius = am4core.percent(82);
            axis.renderer.inside = true;
            axis.renderer.line.strokeOpacity = 1;
            axis.renderer.ticks.template.disabled = false;
            axis.renderer.ticks.template.strokeOpacity = 1;
            axis.renderer.ticks.template.length = 9;
            axis.renderer.grid.template.disabled = true;
            axis.renderer.labels.template.radius = 30;
            axis.renderer.labels.template.fill = am4core.color(getCssVar('--gauge-axis-label-color'));
            axis.renderer.ticks.template.stroke = am4core.color(getCssVar('--gauge-axis-tick-color'));

            // Axis for ranges
            var colorSet = new am4core.ColorSet();
            colorSet.list = [
                am4core.color(getCssVar('--gauge-range-0')),
                am4core.color("#0000FF"), // Default fallback
                am4core.color(getCssVar('--gauge-range-1'))
            ];

            var axis2 = chart.xAxes.push(new am4charts.ValueAxis());
            axis2.min = 0;
            axis2.max = 100;
            axis2.strictMinMax = true;
            axis2.renderer.labels.template.disabled = true;
            axis2.renderer.ticks.template.disabled = true;
            axis2.renderer.grid.template.disabled = true;

            var range0 = axis2.axisRanges.create();
            range0.value = 0;
            range0.endValue = 0;
            range0.axisFill.fillOpacity = 1;
            range0.axisFill.fill = colorSet.getIndex(0);

            var range1 = axis2.axisRanges.create();
            range1.value = 0;
            range1.endValue = 100;
            range1.axisFill.fillOpacity = 1;
            range1.axisFill.fill = colorSet.getIndex(2);

            // Label
            label = chart.radarContainer.createChild(am4core.Label);
            label.isMeasured = false;
            label.fontSize = 18;
            label.x = am4core.percent(50);
            label.y = am4core.percent(100);
            label.horizontalCenter = "middle";
            label.verticalCenter = "bottom";
            label.text = "0%";
            label.fill = am4core.color(getCssVar('--gauge-label-color'));

            // Hand
            hand = chart.hands.push(new am4charts.ClockHand());
            hand.axis = axis2;
            hand.innerRadius = am4core.percent(30);
            hand.startWidth = 10;
            hand.pin.disabled = true;
            hand.value = 0;
            hand.fill = am4core.color(getCssVar('--gauge-hand-fill'));
            hand.stroke = am4core.color(getCssVar('--gauge-hand-stroke'));

            hand.events.on("propertychanged", function(ev) {
                range0.endValue = ev.target.value;
                range1.value = ev.target.value;
                label.text = axis2.positionToValue(hand.currentPosition).toFixed(1) + "%";
                axis2.invalidate();
            });

            // Initial animation
            setTimeout(() => updateGaugeValue(65), 500);

            // Expose to global scope
            window.updateGaugeValue = updateGaugeValue;
        });
    </script>

    <!-- Example of how to use with backend data -->
    <script>
        // This is just for demonstration - in real use, you would call this
        // when you receive data from your backend API
        /*
        document.addEventListener('DOMContentLoaded', function() {
          fetch('/api/get-gauge-value')
            .then(response => response.json())
            .then(data => {
              updateGaugeValue(data.value);
            });
        });
        */

        setInterval(function() {
            var value = Math.round(Math.random() * 100);
            updateGaugeValue(value);
        }, 2000);
    </script>

    <!-- Cards -->
    <script>
        document.addEventListener('DOMContentLoaded', async function () {
            // Configuration and Initialization
            const config = {
                stripe: {
                    publicKey: "{{ config('services.stripe.public') }}",
                    elementsOptions: { disableLink: true }
                },
                routes: {
                    store: "{{ route('owner.cards.store') }}",
                    index: "{{ route('owner.cards.index') }}",
                    updateDefault: "{{ route('owner.cards.update.default') }}",
                    delete: "{{ route('owner.cards.delete') }}"
                },
                csrfToken: "{{ csrf_token() }}"
            };

            // Initialize Stripe
            const stripe = Stripe(config.stripe.publicKey);
            const elements = stripe.elements(config.stripe.elementsOptions);
            let card;

            // DOM Elements
            const dom = {
                paymentMethodModal: new bootstrap.Modal(document.getElementById('paymentMethodModal')),
                form: document.getElementById('payment-form'),
                emailInput: document.getElementById('email'),
                submitButton: document.getElementById('submit-button'),
                buttonText: document.getElementById('button-text'),
                buttonSpinner: document.getElementById('button-spinner'),
                cardErrors: document.getElementById('card-errors'),
                cardsContainer: document.getElementById('cards-container')
            };

            // Event Listeners
            function setupEventListeners() {
                // Add Payment Method Button
                document.querySelector('.btn-primary .fa-plus').closest('button')
                    .addEventListener('click', initializeCardElement);

                // Form Submission
                dom.form.addEventListener('submit', handleFormSubmit);

                // Card Element Events
                if (card) {
                    card.on('change', handleCardChange);
                }

                // Delegated event listeners for dynamic elements
                document.addEventListener('click', handleCardActions);
            }

            // Card Element Management
            function initializeCardElement() {
                if (card) {
                    card.unmount();
                }

                card = elements.create("card");
                card.mount("#card-element");
                dom.cardErrors.textContent = '';
                dom.paymentMethodModal.show();
            }

            function handleCardChange(event) {
                dom.cardErrors.textContent = event.error ? event.error.message : '';
            }

            // Form Handling
            async function handleFormSubmit(e) {
                e.preventDefault();
                setFormLoadingState(true);

                try {
                    const { paymentMethod, error } = await stripe.createPaymentMethod({
                        type: 'card',
                        card: card,
                        billing_details: dom.emailInput.value ? { email: dom.emailInput.value } : {}
                    });

                    if (error) {
                        showCardError(error.message);
                        return;
                    }

                    await savePaymentMethod(paymentMethod.id);
                    dom.paymentMethodModal.hide();
                    await loadCards();
                } catch (err) {
                    console.error('Error:', err);
                    showCardError('An unexpected error occurred');
                } finally {
                    setFormLoadingState(false);
                }
            }

            function setFormLoadingState(isLoading) {
                dom.submitButton.disabled = isLoading;
                dom.buttonText.textContent = isLoading ? 'Processing...' : 'Add Payment Method';
                dom.buttonSpinner.classList.toggle('d-none', !isLoading);
            }

            function showCardError(message) {
                dom.cardErrors.textContent = message;
            }

            // API Interactions
            async function savePaymentMethod(paymentMethodId) {
                const response = await fetch(config.routes.store, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken
                    },
                    body: JSON.stringify({ payment_method_id: paymentMethodId })
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || 'Failed to add payment method');
                }
            }

            async function loadCards() {
                try {
                    const response = await fetch(config.routes.index, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to load payment methods');
                    }

                    const data = await response.json();
                    renderCards(data.cards);
                } catch (error) {
                    console.error('Error loading cards:', error);
                    showCardsError();
                }
            }

            function showCardsError() {
                dom.cardsContainer.innerHTML = `
                <div class="col-12 text-center py-4 text-danger">
                    Failed to load payment methods. Please try again.
                </div>
            `;
            }

            // Card Rendering
            function renderCards(cards) {
                dom.cardsContainer.innerHTML = '';

                if (!cards || cards.length === 0) {
                    dom.cardsContainer.innerHTML = `
                    <div class="col-12 text-center py-4 text-muted">
                        No payment methods found. Add one to get started.
                    </div>
                `;
                    return;
                }

                const rowDiv = document.createElement('div');
                rowDiv.className = 'row';
                dom.cardsContainer.appendChild(rowDiv);

                // Sort cards to put primary card first
                [...cards].sort((a, b) => b.is_default - a.is_default)
                    .forEach(card => {
                        rowDiv.insertAdjacentHTML('beforeend', renderPaymentCard(card));
                    });
            }

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
                    'diners': {
                        background: 'linear-gradient(135deg, #0079BE 0%, #00A3E0 100%)',
                        icon: 'fa-cc-diners-club',
                        accent: 'rgba(255,255,255,0.9)',
                        textColor: '#0079BE'
                    },
                    'unionpay': {
                        background: 'linear-gradient(135deg, #04517A 0%, #0588C9 100%)',
                        icon: 'bx-credit-card',
                        accent: 'rgba(255,255,255,0.9)',
                        textColor: '#04517A'
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

                return `
                <div class="col-md-6 mb-3">
                    <div class="payment-card p-3" style="background: ${style.background}; border-radius: 10px; color: white; box-shadow: 0 4px 8px rgba(0,0,0,0.1); position: relative; overflow: hidden;">
                        <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>

                        <div class="d-flex justify-content-between align-items-center mb-1">
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
                                        <li><a class="dropdown-item set-primary-btn d-flex align-items-center" href="#" data-card-id="${card.id}">Set as primary</a></li>
                                        <li><a class="dropdown-item delete-card-btn d-flex align-items-center" href="#" data-card-id="${card.id}">Delete card</a></li>
                                    </ul>
                                </div>`
                }
                        </div>

                        <div class="mb-1" style="position: relative; z-index: 2;">
                            <span class="text-white-50" style="font-size: 0.8rem;">Card Number</span>
                            <h5 class="mb-0" style="letter-spacing: 1px;">•••• •••• •••• ${card.last4}</h5>
                        </div>

                        <div class="row" style="position: relative; z-index: 2;">
                            <div class="col-6">
                                <span class="text-white-50" style="font-size: 0.8rem;">Expires</span>
                                <h6 class="mb-0">${expiry}</h6>
                            </div>
                            <div class="col-6">
                                <span class="text-white-50" style="font-size: 0.8rem;">CVV</span>
                                <h6 class="mb-0">•••</h6>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-1" style="position: relative; z-index: 2;">
                            <button class="btn btn-sm delete-card-btn" style="background: rgba(255,255,255,0.2); color: white; border: none;" data-card-id="${card.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            }

            // Card Actions
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

            async function handleSetPrimary(e) {
                const cardId = e.target.dataset.cardId || e.target.closest('[data-card-id]').dataset.cardId;

                try {
                    const result = await Swal.fire({
                        title: 'Set as Primary?',
                        text: 'Do you want to set this card as your primary payment method?',
                        icon: 'question',
                        showCancelButton: true,
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
                            confirmButtonText: 'OK'
                        });
                        await loadCards();
                    }
                } catch (error) {
                    await showErrorAlert(error);
                }
            }

            async function updatePrimaryCard(cardId) {
                const response = await fetch(config.routes.updateDefault, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ payment_method_id: cardId })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to set card as primary');
                }
            }

            async function handleDeleteCard(e) {
                const cardId = e.target.dataset.cardId || e.target.closest('[data-card-id]').dataset.cardId;

                try {
                    const result = await Swal.fire({
                        title: 'Delete Card?',
                        text: 'Are you sure you want to delete this payment method?',
                        icon: 'warning',
                        showCancelButton: true,
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
                            confirmButtonText: 'OK'
                        });
                        await loadCards();
                    }
                } catch (error) {
                    await showErrorAlert(error);
                }
            }

            async function deleteCard(cardId) {
                const response = await fetch(config.routes.delete, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ payment_method_id: cardId })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || 'Failed to delete card');
                }
            }

            async function showErrorAlert(error) {
                await Swal.fire({
                    title: 'Error!',
                    text: error.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                console.error('Error:', error);
            }

            // Initialize the application
            setupEventListeners();
            loadCards();
        });
    </script>
@endpush
