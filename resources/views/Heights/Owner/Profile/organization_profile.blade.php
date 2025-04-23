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
            width: 120px;
            height: 120px;
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
                    <div class="profile-card p-4 text-center shadow-sm rounded-3 border-0">
                        <div class="d-flex justify-content-center mb-3">
                            <div class="org-logo-container position-relative">
                                <img src="{{ asset('img/organization_placeholder.png') }}" alt="Bahria Town Logo" class="org-logo rounded-circle border border-3 border-primary" width="100" height="100">
                                <span class="verified-badge position-absolute bottom-0 end-0 bg-success text-white rounded-circle p-1">
                                    <i class="fas fa-check"></i>
                                </span>
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
                            <h5 class="section-title mb-3 fw-semibold">Primary Contact</h5>

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
                                <div class="detail-item py-2">
                                    <div class="detail-label small">Contact Email</div>
                                    <div class="detail-value fw-medium d-flex align-items-center">
                                        <i class="fas fa-envelope me-2 text-primary"></i>
                                        <a href="" class="text-decoration-none">john.smith@techsolutions.com</a>
                                    </div>
                                </div>

                                <div class="detail-item py-2">
                                    <div class="detail-label small">Mobile</div>
                                    <div class="detail-value fw-medium d-flex align-items-center">
                                        <i class="fas fa-mobile-alt me-2 text-primary"></i>
                                        <a href="" class="text-decoration-none">+1 (555) 987-6543</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button class="btn btn-primary rounded-pill py-2 fw-medium">
                                <i class="fas fa-edit me-2"></i> Edit Profile
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Plan & Payment Details -->
                <div class="col-lg-8">
                    <div class="profile-card p-4 mb-4">
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

                                    <table class="table table-borderless">
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
                    <div class="profile-card p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="section-title mb-0">Payment Methods</h4>
                            <button class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i> Add Payment Method
                            </button>
                        </div>

                        <div class="row">
                            <!-- Visa Card -->
                            <x-payment-card
                                    cardNumber="4242 4242 4242 4242"
                                    expiry="12/25"
                                    cvv="123"
                                    cardType="visa"
                                    isPrimary="true"
                            />

                            <!-- Mastercard -->
                            <x-payment-card
                                    cardNumber="5555 5555 5555 4444"
                                    expiry="06/24"
                                    cvv="456"
                                    cardType="mastercard"
                            />
                        </div>
                    </div>

                    <!-- Billing History -->
                    <div class="profile-card p-4">
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
@endsection

@push('scripts')
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
@endpush
