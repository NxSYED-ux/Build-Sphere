@extends('layouts.app')

@section('title', 'Create Plan')

@push('styles')
    <style>
        body {
        }
        #main {
            font-family: 'Nunito', sans-serif;
            margin-top: 45px;
        }
        :root {
            --primary-color: #008CFF;
            --secondary-color: #1cc88a;
            --dark-color: #5a5c69;
            --light-color: #f8f9fc;
        }

        .form-section {
            background-color: var(--body-card-bg);
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 1.6rem;
            margin-bottom: 1.6rem;
        }

        .section-title {
            position: relative;
            margin-bottom: 1.2rem;
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

        .service-card {
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 0 1rem 1rem 1rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s;
            background-color: var(--body-background-color);
        }

        .service-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.1);
        }

        .service-card .card-header {
            cursor: pointer;
            padding: 0;
            background: none;
            border: none;
        }

        .service-icon {
            font-size: 1.5rem;
            color: var(--primary-color);
            top: 3px;
            right: 7px;
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
            background-color: var(--body-card-bg);
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

        .price-input-group {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px dashed #eee;
        }

        .billing-cycle-card {
            margin-bottom: 10px;
            border: 1px solid #eee;
            border-radius: 8px;
            padding-top: 10px;
            height: 50px;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .billing-cycle-card.selected {
            border-color: var(--primary-color);
            background-color: rgba(78, 115, 223, 0.05);
        }

        .billing-cycle-card .form-check {
            pointer-events: none;
        }

        .price-table {
            width: 100%;
            border-collapse: collapse;
        }

        .price-table th, .price-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .price-table th {
            font-weight: 600;
            color: var(--dark-color);
        }

        .per-month-price {
            font-size: 0.85rem;
            color: var(--secondary-color);
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
                        <form id="planForm" action="{{ route('plans.store') }}" method="POST">
                            @csrf
                            <div class="container py-1">
                                <div class="row">
                                    <div class="col-lg-8">
                                        <div class="form-section">
                                            <h3 class="section-title">Plan Information</h3>

                                            <div class="mb-3">
                                                    <label for="plan_name" class="form-label">Plan Name *</label>
                                                    <input type="text" name="plan_name" class="form-control  @error('plan_name') is-invalid @enderror" id="plan_name"
                                                           placeholder="e.g., Enterprise Plan" value="{{ old('plan_name') }}" required>
                                                    @error('plan_name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="plan_description" class="form-label">Description</label>
                                                <textarea class="form-control  @error('plan_description') is-invalid @enderror" name="plan_description" id="plan_description"
                                                          rows="3" maxlength="250" placeholder="Brief description of what this plan offers">{{ old('plan_description') }}</textarea>
                                                @error('plan_description')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="currency" class="form-label">Currency *</label>
                                                    <select class="form-select" name="currency" id="currency" required>
                                                        @foreach($currencies as $currency)
                                                            <option value="{{ $currency }}" {{ old('currency') == $currency ? 'selected' : '' }}>{{ $currency }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('currency')
                                                    <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="status" class="form-label">Status *</label>
                                                    <select class="form-select" name="status" id="status" required>
                                                        @foreach($status as $status)
                                                            <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('status')
                                                    <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-section">
                                            <h3 class="section-title">Billing Cycles</h3>
                                            <p class="mb-4" style="color: var(--sidenavbar-text-color);">Select which billing cycles will be available for this plan</p>

                                            <div class="row">
                                                @foreach($priceCycles as $cycle)
                                                    <div class="col-md-4 mb-2">
                                                        <div class="billing-cycle-card {{ in_array($cycle['id'], old('billing_cycles', [])) ? 'selected' : '' }}"
                                                             onclick="toggleBillingCycleSelection(this, {{ $cycle['id'] }})">
                                                            <div class="form-check">
                                                                <input class="form-check-input @error('billing_cycles') is-invalid @enderror"
                                                                       type="checkbox"
                                                                       name="billing_cycles[]"
                                                                       id="cycle_{{ $cycle['id'] }}"
                                                                       value="{{ $cycle['id'] }}"
                                                                    {{ in_array($cycle['id'], old('billing_cycles', [])) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="cycle_{{ $cycle['id'] }}">
                                                                    <h5>{{ $cycle['name'] }}</h5>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach

                                                @error('billing_cycles')
                                                <div class="col-12">
                                                    <span class="text-danger">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                </div>
                                                @enderror
                                            </div>

                                        </div>

                                        <div class="form-section">
                                            <h3 class="section-title">Plan Services</h3>
                                            <p class="mb-4" style="color: var(--sidenavbar-text-color);">Set quantities and prices for each service</p>

                                            <div class="row">
                                                @foreach($services as $service)
                                                    @php
                                                        $serviceId = $service->id;
                                                    @endphp
                                                    <div class="col-md-6">
                                                        <div class="service-card position-relative">
                                                            <div class="card-header" onclick="toggleServiceSelection(this, {{ $serviceId }})">
                                                                <h5 class="mt-4">{{ $service->title }}</h5>
                                                                <p class="small" style="color: var(--sidenavbar-text-color);">{{ $service->description }}</p>
                                                            </div>
                                                            <div class="service-icon position-absolute m-2">
                                                                <i class="fas fa-{{ $service->icon ?? 'cog' }}"></i>
                                                            </div>

                                                            <div class="service-details" style="display: none;" data-service-id="{{ $serviceId }}">
                                                                <div class="mb-3">
                                                                    <label for="quantity_{{ $serviceId }}" class="form-label">Quantity *</label>
                                                                    <input type="number"
                                                                           name="services[{{ $serviceId }}][quantity]"
                                                                           class="form-control quantity-input @error("services.$serviceId.quantity") is-invalid @enderror"
                                                                           id="quantity_{{ $serviceId }}"
                                                                           min="0"
                                                                           value="{{ old("services.$serviceId.quantity", 0) }}"
                                                                           required>
                                                                    @error("services.$serviceId.quantity")
                                                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                                    @enderror
                                                                </div>

                                                                <div class="price-inputs">
                                                                    <h6>Prices per Billing Cycle</h6>
                                                                    @foreach($priceCycles as $cycle)
                                                                        @php
                                                                            $cycleId = $cycle['id'];
                                                                            $priceOldValue = old("services.$serviceId.prices.$cycleId", 0);
                                                                        @endphp
                                                                        <div class="price-input-group" data-cycle-id="{{ $cycleId }}" style="display: none;">
                                                                            <label for="price_{{ $serviceId }}_{{ $cycleId }}" class="form-label">{{ $cycle['name'] }} Price</label>
                                                                            <input type="number"
                                                                                   name="services[{{ $serviceId }}][prices][{{ $cycleId }}]"
                                                                                   class="form-control price-input @error("services.$serviceId.prices.$cycleId") is-invalid @enderror"
                                                                                   id="price_{{ $serviceId }}_{{ $cycleId }}"
                                                                                   min="0"
                                                                                   step="0.01"
                                                                                   value="{{ $priceOldValue }}"
                                                                                   placeholder="0.00">
                                                                            @error("services.$serviceId.prices.$cycleId")
                                                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                                            @enderror
                                                                            <small class="" style="color: var(--sidenavbar-text-color);">
                                                                                Per month:
                                                                                <span class="per-month-price" id="per_month_{{ $serviceId }}_{{ $cycleId }}">
                                                                                    {{ number_format($priceOldValue, 2) }}
                                                                                </span>
                                                                            </small>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="summary-card">
                                            <h3 class="section-title">Plan Summary</h3>

                                            <div class="mb-4">
                                                <h5 id="summaryPlanName">New Plan</h5>
                                                <p class="small" style="color: var(--sidenavbar-text-color);" id="summaryPlanDescription">No description provided</p>
                                            </div>

                                            <div class="summary-item">
                                                <h6>Currency</h6>
                                                <p class="mb-0" id="summaryCurrency">USD</p>
                                            </div>

                                            <div class="summary-item">
                                                <h6>Services & Pricing</h6>
                                                <div id="selectedServicesList">
                                                    <p class="small" style="color: var(--sidenavbar-text-color);">No services configured</p>
                                                </div>
                                            </div>

                                            <div class="summary-item">
                                                <h6>Total Amount per Billing Cycle</h6>
                                                <div id="cycleTotals">
                                                    <p class="small" style="color: var(--sidenavbar-text-color);">Select billing cycles and set prices to see totals</p>
                                                </div>
                                            </div>

                                            <div class="d-grid mt-1">
                                                <button type="submit" class="btn btn-primary-custom btn-custom">Create Plan</button>
                                            </div>

                                            <div class="alert alert-info mt-3 small">
                                                <i class="fas fa-info-circle me-2"></i> You can modify this plan later from your dashboard.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        function updatePerMonthPrice(priceInput, cycleId) {
            const serviceId = priceInput.closest('.service-details').getAttribute('data-service-id');
            const cycleMonths = getCycleMonths(cycleId);
            const price = parseFloat(priceInput.value) || 0;
            const perMonth = price / cycleMonths;

            const perMonthElement = document.getElementById(`per_month_${serviceId}_${cycleId}`);
            if (perMonthElement) {
                perMonthElement.textContent = perMonth.toFixed(2);
            }
        }

        function getCycleMonths(cycleId) {
            const cycles = {
                1: 1,
                2: 6,
                3: 12
            };
            return cycles[cycleId] || 1;
        }

        function formatCurrency(amount) {
            const currency = document.getElementById('currency').value;
            return `${currency} ${amount.toFixed(2)}`;
        }

        function toggleServiceSelection(element, serviceId) {
            const card = element.closest('.service-card');
            const details = card.querySelector('.service-details');
            const isSelected = details.style.display === 'block';

            if (isSelected) {
                details.style.display = 'none';
            } else {
                details.style.display = 'block';
                card.querySelector('.quantity-input').value = 1;
            }

            updateSummary();
        }

        function toggleBillingCycleSelection(element, cycleId) {
            const checkbox = element.querySelector('input[type="checkbox"]');
            checkbox.checked = !checkbox.checked;

            if (checkbox.checked) {
                element.classList.add('selected');
                document.querySelectorAll(`.price-input-group[data-cycle-id="${cycleId}"]`).forEach(el => {
                    el.style.display = 'block';
                    const priceInput = el.querySelector('.price-input');
                    if (priceInput) {
                        priceInput.disabled = false;
                        priceInput.required = true;
                    }
                });
            } else {
                element.classList.remove('selected');
                document.querySelectorAll(`.price-input-group[data-cycle-id="${cycleId}"]`).forEach(el => {
                    el.style.display = 'none';
                    const priceInput = el.querySelector('.price-input');
                    if (priceInput) {
                        priceInput.value = '';
                        priceInput.disabled = true;
                        priceInput.required = false;
                    }
                });
            }

            updateSummary();
        }

        function updateSummary() {
            const planName = document.getElementById('plan_name').value || 'New Plan';
            const planDescription = document.getElementById('plan_description').value || 'No description provided';
            const currency = document.getElementById('currency').value;

            document.getElementById('summaryPlanName').textContent = planName;
            document.getElementById('summaryPlanDescription').textContent = planDescription;
            document.getElementById('summaryCurrency').textContent = currency;

            const selectedCycles = Array.from(document.querySelectorAll('input[name="billing_cycles[]"]:checked'));

            const servicesList = document.getElementById('selectedServicesList');
            const allServices = Array.from(document.querySelectorAll('.service-card'));

            if (allServices.length === 0) {
                servicesList.innerHTML = '<p class="text-muted small">No services available</p>';
                return;
            }

            let html = '<div class="table-responsive"><table class="price-table">';
            html += '<thead><tr><th>Service</th><th>Qty</th></tr></thead><tbody>';

            const cycleTotals = {};

            allServices.forEach(serviceCard => {
                const serviceId = serviceCard.querySelector('.service-details')?.getAttribute('data-service-id');
                if (!serviceId) return;

                const serviceName = serviceCard.querySelector('h5').textContent;
                const quantityInput = serviceCard.querySelector('.quantity-input');
                const quantity = quantityInput ? quantityInput.value : '0';

                html += `<tr><td>${serviceName}</td><td>${quantity}</td></tr>`;

                selectedCycles.forEach(cycle => {
                    const cycleId = cycle.value;
                    const priceInput = serviceCard.querySelector(`input[name="services[${serviceId}][prices][${cycleId}]"]`);
                    const priceValue = priceInput ? parseFloat(priceInput.value) || 0 : 0;

                    if (!cycleTotals[cycleId]) {
                        cycleTotals[cycleId] = 0;
                    }
                    cycleTotals[cycleId] += priceValue;
                });
            });

            html += '</tbody></table></div>';
            servicesList.innerHTML = html;

            const cycleTotalsElement = document.getElementById('cycleTotals');
            if (selectedCycles.length === 0) {
                cycleTotalsElement.innerHTML = '<p class="small" style="color: var(--sidenavbar-text-color);">Select billing cycles and set prices to see totals</p>';
            } else {
                let totalsHtml = '';
                selectedCycles.forEach(cycle => {
                    const cycleId = cycle.value;
                    const cycleName = cycle.closest('.billing-cycle-card').querySelector('h5').textContent;
                    const total = cycleTotals[cycleId] || 0;

                    totalsHtml += `
                <div class="mb-3 border rounded p-2">
                    <div class="fw-bold">${cycleName}</div>
                    <div class="d-flex justify-content-between">
                        <div>Total:</div>
                        <div>${formatCurrency(total)}</div>
                    </div>
                </div>
            `;
                });
                cycleTotalsElement.innerHTML = totalsHtml;
            }
        }


        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all price inputs as disabled
            document.querySelectorAll('.price-input').forEach(input => {
                input.disabled = true;
            });

            document.getElementById('plan_name').addEventListener('input', updateSummary);
            document.getElementById('plan_description').addEventListener('input', updateSummary);
            document.getElementById('currency').addEventListener('change', updateSummary);

            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('quantity-input')) {
                    updateSummary();
                } else if (e.target.classList.contains('price-input')) {
                    const cycleId = e.target.closest('.price-input-group').getAttribute('data-cycle-id');
                    updatePerMonthPrice(e.target, cycleId);
                    updateSummary();
                }
            });

            document.getElementById('planForm').addEventListener('submit', function(e) {
                const selectedCycles = document.querySelectorAll('input[name="billing_cycles[]"]:checked').length;
                if (selectedCycles === 0) {
                    e.preventDefault();
                    alert('Please select at least one billing cycle');
                    return;
                }

                const selectedCycleIds = Array.from(document.querySelectorAll('input[name="billing_cycles[]"]:checked')).map(c => c.value);
                const serviceCards = document.querySelectorAll('.service-card');

                for (const card of serviceCards) {
                    const details = card.querySelector('.service-details[style="display: block;"]');
                    if (details) {
                        const serviceId = details.getAttribute('data-service-id');
                        for (const cycleId of selectedCycleIds) {
                            const priceInput = card.querySelector(`input[name="services[${serviceId}][prices][${cycleId}]"]`);
                            if (!priceInput || !priceInput.value) {
                                e.preventDefault();
                                alert(`Please set prices for all selected billing cycles for service: ${card.querySelector('h5').textContent}`);
                                return;
                            }
                        }
                    }
                }

                // Disable all unselected price inputs before submission
                document.querySelectorAll('.price-input-group').forEach(group => {
                    const cycleId = group.getAttribute('data-cycle-id');
                    const isSelected = document.querySelector(`input[name="billing_cycles[]"][value="${cycleId}"]:checked`);
                    if (!isSelected) {
                        const priceInput = group.querySelector('.price-input');
                        if (priceInput) {
                            priceInput.disabled = true;
                        }
                    }
                });
            });

            updateSummary();
        });
    </script>
@endpush
