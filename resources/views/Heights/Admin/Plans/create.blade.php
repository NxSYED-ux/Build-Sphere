@extends('layouts.app')

@section('title', 'Create Plan')

@push('styles')
    <style>
        body {
        }
        #main {
            margin-top: 45px;
        }
        :root {
            --primary-color: #4e73df;
            --secondary-color: #1cc88a;
            --dark-color: #5a5c69;
            --light-color: #f8f9fc;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background-color: var(--light-color);
        }

        .form-section {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 2.5rem;
            margin-bottom: 2rem;
        }

        .section-title {
            position: relative;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
        }

        .section-title:after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background-color: var(--primary-color);
        }

        .feature-card {
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s;
        }

        .feature-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.1);
        }

        .feature-card.selected {
            border-color: var(--primary-color);
            background-color: rgba(78, 115, 223, 0.05);
        }

        .feature-icon {
            font-size: 1.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .btn-custom {
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary-custom {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary-custom:hover {
            background-color: #2e59d9;
            border-color: #2653d4;
        }

        .summary-card {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            position: sticky;
            top: 20px;
        }

        .summary-item {
            padding: 1rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .summary-item:last-child {
            border-bottom: none;
        }

        .price-display {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            font-weight: 600;
            border-bottom: 3px solid var(--primary-color);
        }

        .nav-tabs .nav-link {
            color: var(--dark-color);
            border: none;
            padding: 0.75rem 1.5rem;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
    </style>
@endpush

@section('content')
    <x-Admin.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('admin_dashboard'), 'label' => 'Dashboard'],
            ['url' =>  route('plans.index'), 'label' => 'Plans'],
            ['url' => '', 'label' => 'Create Plan']
        ]"
    />
    <x-Admin.side-navbar :openSections="['Plans']" />
    <x-error-success-model />

    <div id="main">

        <section class="content my-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="container py-1">
                            <div class="row">
                                <div class="col-lg-8">
                                    <!-- Plan Information Section -->
                                    <div class="form-section">
                                        <h3 class="section-title">Plan Information</h3>

                                        <div class="mb-3">
                                            <label for="planName" class="form-label">Plan Name</label>
                                            <input type="text" class="form-control" id="planName" placeholder="e.g., Enterprise Plan">
                                        </div>

                                        <div class="mb-3">
                                            <label for="planDescription" class="form-label">Description</label>
                                            <textarea class="form-control" id="planDescription" rows="3" placeholder="Brief description of what this plan offers"></textarea>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="planCurrency" class="form-label">Currency</label>
                                                <select class="form-select" id="planCurrency">
                                                    <option value="PKR" selected>PKR - Pakistani Rupee</option>
                                                    <option value="USD">USD - US Dollar</option>
                                                    <option value="EUR">EUR - Euro</option>
                                                    <option value="GBP">GBP - British Pound</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Status</label>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="planStatus" checked>
                                                    <label class="form-check-label" for="planStatus">Active</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Services Selection Section -->
                                    <div class="form-section">
                                        <h3 class="section-title">Select Services</h3>
                                        <p class="text-muted mb-4">Choose which services to include in your plan and set their quantities</p>

                                        <ul class="nav nav-tabs mb-4" id="servicesTab" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">All Services</button>
                                            </li>
                                        </ul>

                                        <div class="tab-content" id="servicesTabContent">
                                            <div class="tab-pane fade show active" id="all" role="tabpanel">
                                                <div class="row">
                                                    <!-- Building Management -->
                                                    <div class="col-md-6">
                                                        <div class="feature-card" onclick="toggleSelection(this)">
                                                            <div class="feature-icon">
                                                                <i class="fas fa-building"></i>
                                                            </div>
                                                            <h5>Building Management</h5>
                                                            <p class="text-muted small">Oversee and organize all registered buildings in one place.</p>
                                                            <div class="quantity-control mt-3" style="display: none;">
                                                                <label class="form-label">Quantity</label>
                                                                <input type="number" class="form-control" min="1" value="1" data-service-id="1">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Manager Accounts -->
                                                    <div class="col-md-6">
                                                        <div class="feature-card" onclick="toggleSelection(this)">
                                                            <div class="feature-icon">
                                                                <i class="fas fa-user-tie"></i>
                                                            </div>
                                                            <h5>Manager Accounts</h5>
                                                            <p class="text-muted small">Create and manage accounts for building managers.</p>
                                                            <div class="quantity-control mt-3" style="display: none;">
                                                                <label class="form-label">Quantity</label>
                                                                <input type="number" class="form-control" min="1" value="1" data-service-id="2">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Staff Members per Building -->
                                                    <div class="col-md-6">
                                                        <div class="feature-card" onclick="toggleSelection(this)">
                                                            <div class="feature-icon">
                                                                <i class="fas fa-users"></i>
                                                            </div>
                                                            <h5>Staff Members per Building</h5>
                                                            <p class="text-muted small">Assign staff members to individual buildings.</p>
                                                            <div class="quantity-control mt-3" style="display: none;">
                                                                <label class="form-label">Quantity</label>
                                                                <input type="number" class="form-control" min="1" value="1" data-service-id="3">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Levels per Building -->
                                                    <div class="col-md-6">
                                                        <div class="feature-card" onclick="toggleSelection(this)">
                                                            <div class="feature-icon">
                                                                <i class="fas fa-layer-group"></i>
                                                            </div>
                                                            <h5>Levels per Building</h5>
                                                            <p class="text-muted small">Define and manage the number of floors or levels.</p>
                                                            <div class="quantity-control mt-3" style="display: none;">
                                                                <label class="form-label">Quantity</label>
                                                                <input type="number" class="form-control" min="1" value="1" data-service-id="4">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Units per Building -->
                                                    <div class="col-md-6">
                                                        <div class="feature-card" onclick="toggleSelection(this)">
                                                            <div class="feature-icon">
                                                                <i class="fas fa-door-open"></i>
                                                            </div>
                                                            <h5>Units per Building</h5>
                                                            <p class="text-muted small">Add and track all residential or commercial units.</p>
                                                            <div class="quantity-control mt-3" style="display: none;">
                                                                <label class="form-label">Quantity</label>
                                                                <input type="number" class="form-control" min="1" value="1" data-service-id="5">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Featured Memberships -->
                                                    <div class="col-md-6">
                                                        <div class="feature-card" onclick="toggleSelection(this)">
                                                            <div class="feature-icon">
                                                                <i class="fas fa-crown"></i>
                                                            </div>
                                                            <h5>Featured Memberships</h5>
                                                            <p class="text-muted small">Highlight premium membership options for users.</p>
                                                            <div class="quantity-control mt-3" style="display: none;">
                                                                <label class="form-label">Quantity</label>
                                                                <input type="number" class="form-control" min="1" value="1" data-service-id="6">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <!-- Billing Cycle Section -->
                                    <div class="form-section">
                                        <h3 class="section-title">Billing Cycle</h3>
                                        <p class="text-muted mb-4">Select your preferred billing cycle</p>

                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-body text-center">
                                                        <h5 class="card-title">Monthly</h5>
                                                        <p class="card-text text-muted small">Pay month-to-month with no long-term commitment</p>
                                                        <div class="form-check d-flex justify-content-center">
                                                            <input class="form-check-input" type="radio" name="billingCycle" id="monthly" value="1" checked>
                                                            <label class="form-check-label ms-2" for="monthly">
                                                                Select
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-body text-center">
                                                        <h5 class="card-title">6 Months</h5>
                                                        <p class="card-text text-muted small">Save 10% with our semi-annual billing</p>
                                                        <div class="form-check d-flex justify-content-center">
                                                            <input class="form-check-input" type="radio" name="billingCycle" id="semiannual" value="2">
                                                            <label class="form-check-label ms-2" for="semiannual">
                                                                Select
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-body text-center">
                                                        <h5 class="card-title">Annual</h5>
                                                        <p class="card-text text-muted small">Save 15% with our annual billing</p>
                                                        <div class="form-check d-flex justify-content-center">
                                                            <input class="form-check-input" type="radio" name="billingCycle" id="annual" value="3">
                                                            <label class="form-check-label ms-2" for="annual">
                                                                Select
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Order Summary -->
                                <div class="col-lg-4">
                                    <div class="summary-card">
                                        <h3 class="section-title">Plan Summary</h3>

                                        <div class="mb-4">
                                            <h5 id="summaryPlanName">Custom Plan</h5>
                                            <p class="text-muted small" id="summaryPlanDescription">No description provided</p>
                                        </div>

                                        <div class="summary-item">
                                            <h6>Selected Services</h6>
                                            <div id="selectedServicesList">
                                                <p class="text-muted small">No services selected</p>
                                            </div>
                                        </div>

                                        <div class="summary-item">
                                            <h6>Billing Cycle</h6>
                                            <p class="mb-0" id="summaryBillingCycle">Monthly</p>
                                        </div>

                                        <div class="summary-item">
                                            <h6>Total Price</h6>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="price-display" id="totalPrice">PKR 0.00</span>
                                                <span class="text-muted small" id="billingNote">per month</span>
                                            </div>
                                        </div>

                                        <div class="d-grid mt-4">
                                            <button class="btn btn-primary-custom btn-custom" onclick="createPlan()">Create Plan</button>
                                        </div>

                                        <div class="alert alert-info mt-3 small">
                                            <i class="fas fa-info-circle me-2"></i> You can modify this plan later from your dashboard.
                                        </div>
                                    </div>
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

    <script>
        // Service prices for different billing cycles (service_id -> billing_cycle_id -> price)
        const servicePrices = {
            1: {1: 350.00, 2: 1890.00, 3: 3570.00},
            2: {1: 350.00, 2: 1890.00, 3: 3570.00},
            3: {1: 350.00, 2: 1890.00, 3: 3570.00},
            4: {1: 350.00, 2: 1890.00, 3: 3570.00},
            5: {1: 350.00, 2: 1890.00, 3: 3570.00},
            6: {1: 350.00, 2: 1890.00, 3: 3570.00}
        };

        // Toggle service selection
        function toggleSelection(card) {
            card.classList.toggle('selected');
            const quantityControl = card.querySelector('.quantity-control');

            if (card.classList.contains('selected')) {
                quantityControl.style.display = 'block';
            } else {
                quantityControl.style.display = 'none';
                quantityControl.querySelector('input').value = 1;
            }

            updateSummary();
        }

        // Update order summary
        function updateSummary() {
            // Update plan info
            const planName = document.getElementById('planName').value || 'Custom Plan';
            const planDescription = document.getElementById('planDescription').value || 'No description provided';

            document.getElementById('summaryPlanName').textContent = planName;
            document.getElementById('summaryPlanDescription').textContent = planDescription;

            // Update billing cycle
            const billingCycle = document.querySelector('input[name="billingCycle"]:checked');
            let cycleText = 'Monthly';
            let billingNote = 'per month';

            if (billingCycle) {
                if (billingCycle.value === '2') {
                    cycleText = '6 Months';
                    billingNote = 'every 6 months (save 10%)';
                } else if (billingCycle.value === '3') {
                    cycleText = 'Annual';
                    billingNote = 'per year (save 15%)';
                }
            }

            document.getElementById('summaryBillingCycle').textContent = cycleText;
            document.getElementById('billingNote').textContent = billingNote;

            // Update selected services
            const selectedServices = document.querySelectorAll('.feature-card.selected');
            const servicesList = document.getElementById('selectedServicesList');

            if (selectedServices.length === 0) {
                servicesList.innerHTML = '<p class="text-muted small">No services selected</p>';
                document.getElementById('totalPrice').textContent = 'PKR 0.00';
                return;
            }

            let html = '';
            let totalPrice = 0;
            const billingCycleId = billingCycle ? billingCycle.value : '1';

            selectedServices.forEach(service => {
                const serviceTitle = service.querySelector('h5').textContent;
                const quantityInput = service.querySelector('input[type="number"]');
                const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
                const serviceId = quantityInput ? quantityInput.getAttribute('data-service-id') : null;

                if (serviceId && servicePrices[serviceId] && servicePrices[serviceId][billingCycleId]) {
                    const servicePrice = servicePrices[serviceId][billingCycleId] * quantity;
                    totalPrice += servicePrice;

                    html += `
                        <div class="d-flex justify-content-between mb-2">
                            <span>${serviceTitle} (x${quantity})</span>
                            <span>PKR ${servicePrice.toFixed(2)}</span>
                        </div>
                    `;
                }
            });

            servicesList.innerHTML = html;

            // Update total price
            const currency = document.getElementById('planCurrency').value;
            document.getElementById('totalPrice').textContent = `${currency} ${totalPrice.toFixed(2)}`;
        }

        // Create plan function
        function createPlan() {
            const planName = document.getElementById('planName').value;
            const planDescription = document.getElementById('planDescription').value;
            const currency = document.getElementById('planCurrency').value;
            const status = document.getElementById('planStatus').checked ? 1 : 0;
            const billingCycle = document.querySelector('input[name="billingCycle"]:checked').value;

            if (!planName) {
                alert('Please enter a plan name');
                return;
            }

            // Collect selected services
            const selectedServices = [];
            document.querySelectorAll('.feature-card.selected').forEach(service => {
                const quantityInput = service.querySelector('input[type="number"]');
                if (quantityInput) {
                    selectedServices.push({
                        service_id: quantityInput.getAttribute('data-service-id'),
                        quantity: parseInt(quantityInput.value)
                    });
                }
            });

            if (selectedServices.length === 0) {
                alert('Please select at least one service');
                return;
            }

            // Here you would typically send this data to your backend
            console.log('Creating plan with:', {
                name: planName,
                description: planDescription,
                currency: currency,
                status: status,
                billing_cycle: billingCycle,
                services: selectedServices
            });

            alert('Plan created successfully!');
            // window.location.href = 'index.html'; // Redirect after creation
        }

        // Initialize event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Update summary when any input changes
            document.getElementById('planName').addEventListener('input', updateSummary);
            document.getElementById('planDescription').addEventListener('input', updateSummary);
            document.getElementById('planCurrency').addEventListener('change', updateSummary);

            // Update summary when billing cycle changes
            document.querySelectorAll('input[name="billingCycle"]').forEach(radio => {
                radio.addEventListener('change', updateSummary);
            });

            // Update summary when service quantities change
            document.querySelectorAll('.quantity-control input').forEach(input => {
                input.addEventListener('change', updateSummary);
                input.addEventListener('input', updateSummary);
            });

            // Initial summary update
            updateSummary();
        });
    </script>

@endpush
