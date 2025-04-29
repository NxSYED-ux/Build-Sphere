@extends('layouts.app')

@section('title', 'Organization Profile')

@push('styles')
    <style>
        :root {
            --secondary-color: #1cc88a;
            --payment-card-primary: var(--color-blue);
            --payment-card-primary-hover: var(--color-blue);
            --payment-card-error: #dc2626;
            --payment-card-text: var(--sidenavbar-text-color);
            --payment-card-text-light: var(--sidenavbar-text-color);
            --payment-card-border: #e2e8f0;
            --payment-card-border-hover: #cbd5e1;
            --payment-card-background: var(--body-card-bg);
            --payment-card-input-background2: var(--body-background-color);
            --payment-card-radius: 10px;
            --payment-card-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
            --payment-card-transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
        }

        #main{
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

        #cards-container{
            padding-right: 0 !important;
        }

        #cards-container .col-md-6{
            margin-right: 0 !important;
        }

        #cards-container > .row {
            padding-right: 0 !important;
        }

        /* Force remove all spacing around the row */
        #cards-container > .row {
            margin-right: 0 !important;
            margin-left: 0 !important;
            padding-right: 0 !important;
            padding-left: 0 !important;
        }

        /* Remove column padding if needed */
        #cards-container > .row > [class*="col-"] {
            padding-right: 7px !important;
            padding-left: 7px !important;
        }

    </style>
@endpush

@section('content')

    <!-- Top Navbar -->
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('owner_manager_dashboard'), 'label' => 'Dashboard'],
            ['url' => '', 'label' => 'Organization Profile']
        ]"
    />

    <!-- Side Navbar -->
    <x-Owner.side-navbar :openSections="['']" />
    <x-error-success-model />

    <div id="main">
        <div class="container py-5 mt-3">
            <div class="row">
                <!-- Left Column - Organization Details -->
                <div class="col-lg-4 mb-4">
                    <div class="profile-card p-4 text-center shadow border-0">

                        <div class="position-relative"> <!-- This wrapper is needed for absolute positioning -->
                            <div id="unitCarousel{{ $organization->id }}" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    @forelse($organization->pictures ?? [] as $key => $picture)
                                        <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                            <img src="{{ asset($picture->file_path) }}" class="d-block" alt="Unit Picture" style="border-radius: 5px; width:100%; height:200px;">
                                        </div>
                                    @empty
                                        <img src="{{ asset('img/placeholder-img.jfif') }}" class="d-block" alt="Unit Picture" style="border-radius: 5px; width:90%; height:200px;">
                                    @endforelse
                                </div>
                            </div>

                            <!-- Circular logo image positioned at bottom left with camera icon -->
                            <div class="position-absolute" style="bottom: 10px; left: 10px;">
                                <div class="position-relative" style="width: 80px; height: 80px;">
                                    <!-- Logo Image -->
                                    <img src="{{ asset($organization->logo ?? 'img/default-logo.png') }}"
                                         alt="Organization Logo" id="organizantion-logo"
                                         class="rounded-circle border border-3 border-white w-100 h-100"
                                         style="object-fit: cover;">

                                    <!-- Camera Icon Overlay -->
                                    <label for="image-upload" class="position-absolute bottom-0 end-0 bg-primary rounded-circle d-flex justify-content-center align-items-center"
                                         style="width: 24px; height: 24px; cursor: pointer;"
                                         data-bs-toggle="modal" data-bs-target="#changeLogoModal">
                                        <i class="fas fa-camera text-white" style="font-size: 12px;"></i>
                                        <input type="file" id="image-upload" class="d-none" accept="image/*">
                                    </label>
                                </div>
                            </div>
                        </div>

                        <h3 class="mb-1 fw-bold mt-2">{{ $organization->name }}</h3>
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
                                <a href="{{ route('owner.organization.edit') }}" class="rounded-pill py-2 fw-medium">
                                    <i class="fas fa-edit me-2 text-warning fs-5"></i>
                                </a>
                            </div>

                            <div class="detail-list">

                                <div class="row">
                                    <div class="col-md-6 col-7 detail-item py-1">
                                        <div class="detail-label">Membership ID</div>
                                        <div class="detail-value fw-medium">{{ $organization->payment_gateway_merchant_id }}</div>
                                    </div>

                                    <div class="col-md-6 col-5 detail-item pb-1">
                                        <div class="detail-label">Online Payment</div>
                                            <div class="d-flex align-items-center">
                                                <label class="form-check-label me-3" for="enable_online_payments">
                                                    Enable
                                                </label>
                                                <div class="form-check form-switch m-0 mx-2 mt-1">
                                                    <input type="hidden" name="is_online_payment_enabled" value="0">
                                                    <input class="form-check-input" type="checkbox" role="switch" id="enable_online_payments"
                                                       name="is_online_payment_enabled" value="{{ old('merchant_id', $organization->is_online_payment_enabled) }}"
                                                       style="transform: scale(1.3);"
                                                       {{ old('is_online_payment_enabled', $organization->is_online_payment_enabled ?? false) ? 'checked' : '' }}
                                                       onchange="updateOnlinePaymentStatus(this, '{{ $organization->id }}', '{{ $organization->payment_gateway_merchant_id }}')">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6 col-lg-12 detail-item pb-1">
                                        <div class="detail-label">Registration Date</div>
                                        <div class="detail-value fw-medium">{{ isset($organization->created_at) ? \Carbon\Carbon::parse($organization->created_at)->format('M d, Y') : 'N/A' }}</div>
                                    </div>

                                    <div class="col-12 col-md-6 col-lg-12 detail-item py-1">
                                        <div class="detail-label">Contact Email</div>
                                        <div class="detail-value fw-medium d-flex align-items-center">
                                            <i class="fas fa-envelope me-2 text-primary"></i>
                                            <a href="" class="text-decoration-none">{{ $organization->email }}</a>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6 col-lg-12 detail-item py-1">
                                        <div class="detail-label">Phone Number</div>
                                        <div class="detail-value fw-medium d-flex align-items-center">
                                            <i class="fas fa-phone me-2 text-primary"></i>
                                            <a href="" class="text-decoration-none">{{ $organization->phone }}</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="detail-item py-1 pb-0">
                                    <div class="detail-label">Address</div>
                                    <div class="detail-value fw-medium d-flex">
                                        <i class="fas fa-map-marker-alt me-2 mt-1 text-primary"></i>
                                        <div>
                                            @if ($organization->address)
                                                {{ $organization->address->location ?? '' }},
                                                {{ $organization->address->city ?? '' }},
                                                {{ $organization->address->province ?? '' }},
                                                {{ $organization->address->country ?? '' }}
                                            @else
                                                N/A
                                            @endif
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-start mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="section-title mb-0 fw-semibold">Owner Details</h5>
                                <a href="{{ route('owner.profile') }}" class="rounded-pill py-2 fw-medium">
                                    <i class="fas fa-edit me-2 text-warning fs-5"></i>
                                </a>
                            </div>

                            <div class="d-flex align-items-center mb-3 p-2 rounded-3 bg-light">
                                <img src="{{ $organization->owner->picture ? asset($organization->owner->picture) : asset('uploads/users/images/Placeholder.jpg') }}" alt="John Smith" class="rounded-circle me-3" width="50" height="50">
                                <div>
                                    <h6 class="mb-0 fw-medium text-dark">{{ $organization->owner->name }}</h6>
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
                                        <a href="" class="text-decoration-none">{{ $organization->owner->email }}</a>
                                    </div>
                                </div>

                                <div class="detail-item py-1">
                                    <div class="detail-label small">Mobile</div>
                                    <div class="detail-value fw-medium d-flex align-items-center">
                                        <i class="fas fa-mobile-alt me-2 text-primary"></i>
                                        <a href="" class="text-decoration-none">{{ $organization->owner->phone_no }}</a>
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
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="mb-1">{{ $subscription['name'] ?? 'N/A' }}</h5>
                                            <span class="badge bg-success bg-opacity-10 text-success py-1 px-2 mx-3 rounded-pill">
                                                <i class="fas fa-check-circle me-1"></i> {{ $subscription['status'] }}
                                            </span>
                                        </div>
                                        <div class="text-primary fw-bold" style="font-size: 12px;">
                                            {{ isset($subscription['price'], $subscription['currency'], $subscription['billing_cycle']) && $subscription['billing_cycle'] > 0
                                                ? number_format($subscription['price'] / $subscription['billing_cycle'], 2) . ' ' . $subscription['currency']
                                                : '0.00 PKR' }}
                                            <small>/ Month</small>
                                        </div>

                                    </div>

                                    <table class="table table-borderless" style="background-color: var(--sidenavbar-body-color);">
                                        <thead>
                                        <tr class="border-bottom">
                                            <th class="text-center">Icon</th>
                                            <th class="text-start">Service</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-center">Used</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($subscription['services'] ?? [] as $service)
                                            <tr>
                                                <td class="text-center"><i class="{{ $service['icon'] ?? '' }}" style="font-size: 20px;"></i></td>
                                                <td class="text-start">{{ $service['title'] ?? '' }}</td>
                                                <td class="text-center">{{ $service['quantity'] ?? 0 }}</td>
                                                <td class="text-center">{{ $service['used'] ?? 0 }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">No Any Service</td>
                                            </tr>
                                        @endforelse

                                        </tbody>
                                    </table>

                                    <div class="mt-3 pt-3 border-top">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Billing Cycle:</span>
                                            <span class="fw-medium">{{ $subscription['billing_cycle'] ?? 'N/A' }}<small> Months</small></span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Next Billing:</span>
                                            <span class="fw-medium">
                                                {{ isset($subscription['ends_at']) ? \Carbon\Carbon::parse($subscription['ends_at'])->format('M d, Y') : 'N/A' }}
                                            </span>

                                        </div>
                                        <div class="d-flex justify-content-between fw-bold">
                                            <span>Total:</span>
                                            <span>  {{ isset($subscription['price'], $subscription['currency'])
                                                    ? $subscription['price'] . ' ' . $subscription['currency']
                                                    : 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="plan-card featured p-4 h-100 d-flex flex-column justify-content-between">
                                    <h5 class=" fw-semibold">Plan Used</h5>

                                    <div id="chartdiv" class="flex-grow-1 my-1"></div>

                                    <div class="mt-auto pt-2">
                                        <a href="{{ route('owner.plan.upgrade.index') }}" class="btn btn-success w-100 mb-3 py-2 rounded-1" style="color: #fff !important;">
                                            <i class="fas fa-arrow-up me-2 text-white"></i> Upgrade Plan
                                        </a>
                                        @if($subscription['status'] === 'Active')
                                            <form id="cancelSubscriptionForm" action="{{ route('owner.organization.planSubscription.cancel') }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="id" value="{{ $organization->id }}">
                                                <button type="button" onclick="confirmCancellation()" class="btn btn-danger w-100 py-2 rounded-1">
                                                    <i class="fas fa-ban me-2"></i> Cancel Subscription
                                                </button>
                                            </form>
                                        @elseif($subscription['status'] === 'Cancelled')
                                            <form id="resumeSubscriptionForm" action="{{ route('owner.organization.planSubscription.resume') }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="id" value="{{ $organization->id }}">
                                                <button type="button" onclick="confirmResume()" class="btn btn-primary w-100 py-2 rounded-1">
                                                    <i class="fas fa-play me-2"></i> Resume Subscription
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <div class="profile-card p-4 mb-4 shadow">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="section-title mb-0">Payment Methods</h4>
                            <button type="button" class="btn btn-sm btn-primary add-payment-method-btn" id="add-payment-method-btn"  data-bs-toggle="modal" data-bs-target="#paymentMethodModal">
                                <i class="fas fa-plus me-1"></i> Add Method
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

                    </div>


                </div>

                <div class="col-12">
                    <!-- Billing History -->
                    <div class="profile-card p-4 shadow">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="section-title mb-0">Transactions</h4>
                            <a href="{{ route('owner.finance.index') }}" class="btn btn-sm btn-primary text-white text-decoration-none" style="color: #fff !important;">
                                All Transactions
                            </a>
                        </div>

                        <div class="row g-4" id="transactions-container">
                            <!-- Loading state will appear here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="paymentMethodModal" tabindex="-1" aria-labelledby="paymentMethodLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered bg-transparent">
            <div class="modal-content p-0 m-0 border-0 rounded-3  bg-transparent">
                <div class="modal-body p-0 m-0">
                    <x-stripe-card-form
                        :stripeKey="config('services.stripe.key')"
                        formAction="#"
                        buttonText="Save Card"
                        title="Add Payment Method"
                    />
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')



    <script>
        async function loadTransactions() {
            const container = $('#transactions-container');

            // Show loading state
            container.html(`
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading transactions...</p>
            </div>
        `);

            try {
                const response = await $.ajax({
                    url: '{{ route("owner.finance.latest") }}',
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                container.empty();

                if (response.history && response.history.length > 0) {
                    response.history.forEach(function(transaction) {
                        const amountSign = transaction.type === 'Credit' ? '+' : '-';
                        const isFailed = transaction.status.toLowerCase() === 'failed';


                        const cardHtml = `
                        <div class="col-md-6 col-xl-4">
                            <a href="${'{{ route('owner.finance.show', ':id') }}'.replace(':id', transaction.id)}" class="text-white text-decoration-none" style="color: #fff !important;">
                                <div class="card border-0 shadow hover-shadow-lg transition-all h-100" style="background-color: var(--body-background-color) !important;">
                                    <div class="card-body p-4 d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start mb-3 flex-grow-1">
                                            <div class="d-flex align-items-center">
                                                <div class="${getBgClass(transaction)} p-3 rounded-circle me-3">
                                                    <i class="${getIconClass(transaction)} ${getTextColor(transaction)} fs-4"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">${transaction.title}</h6>
                                                    <small class="small">${transaction.created_at}</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-end mt-auto">
                                            <div>
                                                <p class=" small mb-1">Amount</p>
                                                <h4 class="mb-0 ${getTextColor(transaction)}">
                                                    ${isFailed ? '<span class="text-decoration-line-through">' : ''}
                                                    ${amountSign}${transaction.price}
                                                    ${isFailed ? '</span>' : ''}
                                                </h4>
                                            </div>
                                            <span class="badge ${getStatusBadge(transaction)} px-3 py-2">
                                                <i class="${getStatusIcon(transaction)} me-1"></i>
                                                ${transaction.status.charAt(0).toUpperCase() + transaction.status.slice(1)}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    `;
                        container.append(cardHtml);
                    });
                } else {
                    container.html(`
                    <div class="col-12 text-center py-4">
                        <i class="fas fa-wallet fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No transactions found</h5>
                    </div>
                `);
                }
            } catch (error) {
                console.error('Error loading transactions:', error);
                container.html(`
                <div class="col-12">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Failed to load transactions. Please try again later.
                    </div>
                </div>
            `);
            }
        }

        // Helper functions
        function getIconClass(transaction) {
            const status = transaction.status.toLowerCase();
            const type = transaction.type.toLowerCase();

            if (status === 'failed') return 'fas fa-ban';
            if (type === 'credit') return 'fas fa-download';
            return 'fas fa-upload';
        }

        function getStatusIcon(transaction) {
            const status = transaction.status.toLowerCase();

            if (status === 'completed') return 'fas fa-check-circle';
            if (status === 'pending') return 'fas fa-clock';
            if (status === 'failed') return 'fas fa-exclamation-circle';
            return 'fas fa-info-circle';
        }

        function getBgClass(transaction) {
            const status = transaction.status.toLowerCase();
            const type = transaction.type.toLowerCase();

            if (status === 'failed') return 'bg-danger bg-opacity-10';
            if (type === 'credit') return 'bg-success bg-opacity-10';
            if (status === 'pending') return 'bg-warning bg-opacity-10';
            return 'bg-primary bg-opacity-10';
        }

        function getTextColor(transaction) {
            const status = transaction.status.toLowerCase();
            const type = transaction.type.toLowerCase();

            if (status === 'failed') return 'text-danger';
            if (type === 'credit') return 'text-success';
            if (status === 'pending') return 'text-warning';
            return 'text-primary';
        }

        function getStatusBadge(transaction) {
            const status = transaction.status.toLowerCase();

            if (status === 'completed') return 'bg-success bg-opacity-10 text-success';
            if (status === 'pending') return 'bg-warning bg-opacity-10 text-warning';
            if (status === 'failed') return 'bg-danger bg-opacity-10 text-danger';
            return 'bg-secondary bg-opacity-10 text-secondary';
        }

        // Call on page load
        $(document).ready(function() {
            loadTransactions();
        });
    </script>

    <script>
        /**
         * Tooltip Initialization Script
         * Initializes Bootstrap tooltips and handles basic payment method actions
         */
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Payment method removal confirmation
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

    <script>
        /**
         * Gauge Chart Script
         * Creates and manages an animated gauge chart using amCharts
         */
            // Chart references
        let chart;
        let hand;
        let label;

        /**
         * Gets CSS variable value
         */
        function getCssVar(name) {
            return getComputedStyle(document.documentElement)
                .getPropertyValue(name)
                .trim();
        }

        /**
         * Updates gauge value with animation
         */
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

        // Initialize chart when amCharts is ready
        am4core.ready(function() {
            am4core.useTheme(am4themes_animated);

            // Create chart instance
            chart = am4core.create("chartdiv", am4charts.GaugeChart);
            chart.innerRadius = am4core.percent(82);
            chart.logo.disabled = true;

            // Configure main axis
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

            // Configure color ranges axis
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

            // Create ranges
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

            // Create center label
            label = chart.radarContainer.createChild(am4core.Label);
            label.isMeasured = false;
            label.fontSize = 18;
            label.x = am4core.percent(50);
            label.y = am4core.percent(100);
            label.horizontalCenter = "middle";
            label.verticalCenter = "bottom";
            label.text = "0%";
            label.fill = am4core.color(getCssVar('--gauge-label-color'));

            // Create gauge hand/pointer
            hand = chart.hands.push(new am4charts.ClockHand());
            hand.axis = axis2;
            hand.innerRadius = am4core.percent(30);
            hand.startWidth = 10;
            hand.pin.disabled = true;
            hand.value = 0;
            hand.fill = am4core.color(getCssVar('--gauge-hand-fill'));
            hand.stroke = am4core.color(getCssVar('--gauge-hand-stroke'));

            // Update ranges and label when hand moves
            hand.events.on("propertychanged", function(ev) {
                range0.endValue = ev.target.value;
                range1.value = ev.target.value;
                label.text = axis2.positionToValue(hand.currentPosition).toFixed(1) + "%";
                axis2.invalidate();
            });

            // Initial animation
            const gaugeValue = @json($usage);
            setTimeout(() => updateGaugeValue(gaugeValue), 500);

            // Expose to global scope
            window.updateGaugeValue = updateGaugeValue;
        });
    </script>

    <script>
        /**
         * Gauge Demo Script
         * Demonstrates how to update gauge with backend data
         * (This is just a demo - in production you would use real API calls)
         */
        document.addEventListener('DOMContentLoaded', function() {
            // Example API call (commented out for reference)
            /*
            fetch('/api/get-gauge-value')
                .then(response => response.json())
                .then(data => {
                    updateGaugeValue(data.value);
                });
            */

            // Demo: Random updates every 2 seconds
            // setInterval(function() {
            //     var value = Math.round(Math.random() * 100);
            //     updateGaugeValue(value);
            // }, 2000);
        });
    </script>

    <script>
        /**
         * Payment Cards Management Script
         * Handles loading, displaying, and managing payment cards
         */
        document.addEventListener('DOMContentLoaded', async function() {
            // DOM Elements
            const dom = {
                cardsContainer: document.getElementById('cards-container')
            };

            // Event delegation for card actions
            document.addEventListener('click', handleCardActions);

            // Initialize
            window.submitaddedCard = submitaddedCard;
            loadCards();

            /**
             * Submits a new card to the server
             */
            async function submitaddedCard() {
                const methodId = document.getElementById('payment_method_id').value;
                const response = await fetch('{{ route('owner.cards.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ payment_method_id: methodId })
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.error || result.message || 'Failed to add payment method');
                }

                const modal = bootstrap.Modal.getInstance(document.getElementById('paymentMethodModal'));
                if (modal) modal.hide();

                loadCards();
            }

            /**
             * Loads cards from server
             */
            async function loadCards() {
                // Show loading state
                dom.cardsContainer.innerHTML = `
                <div class="col-12 text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;

                try {
                    const response = await fetch('{{ route('owner.cards.index') }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to load cards');
                    }

                    const data = await response.json();
                    renderCards(data.cards);
                } catch (error) {
                    console.error('Error loading cards:', error);
                    showCardsError();
                }
            }

            /**
             * Shows error state when cards fail to load
             */
            function showCardsError() {
                dom.cardsContainer.innerHTML = `
                <div class="col-12 text-center py-4 text-danger">
                    Failed to load payment methods. Please try again.
                </div>
            `;
            }

            /**
             * Renders cards in the UI
             */
            function renderCards(cards) {
                dom.cardsContainer.innerHTML = '';

                if (!cards || cards.length === 0) {
                    dom.cardsContainer.innerHTML = `
                    <div class="col-12 text-center py-4 text-muted">
                        No payment methods found.
                    </div>
                `;
                    return;
                }

                const rowDiv = document.createElement('div');
                rowDiv.className = 'row';
                dom.cardsContainer.appendChild(rowDiv);

                // Sort cards with primary first
                [...cards].sort((a, b) => b.is_default - a.is_default)
                    .forEach(card => {
                        rowDiv.insertAdjacentHTML('beforeend', renderPaymentCard(card));
                    });
            }

            /**
             * Generates HTML for a payment card
             */
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
                    <div class="payment-card w-100 p-3" style="background: ${style.background}; border-radius: 10px; color: white; box-shadow: 0 4px 8px rgba(0,0,0,0.1); position: relative; overflow: hidden;">
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
                                            <li><a class="dropdown-item set-primary-btn" href="#" data-card-id="${card.id}"
                                                style="background-color: var(--body-background-color); color: var(--sidenavbar-text-color) !important;" onmouseover="this.style.backgroundColor='var(--body-background-color)'" onmouseout="this.style.backgroundColor='var(--body-background-color)'">
                                                Set as primary</a>
                                            </li>
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
                        <div class="d-flex justify-content-between align-items-center mt-1" style="position: relative; z-index: 2;">
                            <span class="text-white fw-medium">${card.cardholder_name ?? ''}</span>

                            <div class="d-flex gap-2">
                                <button class="btn btn-sm delete-card-btn"  data-card-id="${card.id}" style="background: rgba(255,255,255,0.2); color: white; border: none;">
                                    <i class="fas fa-trash text-white"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            }

            /**
             * Handles card action events
             */
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

            /**
             * Handles setting a card as primary
             */
            async function handleSetPrimary(e) {
                const cardId = e.target.dataset.cardId || e.target.closest('[data-card-id]').dataset.cardId;

                try {
                    const result = await Swal.fire({
                        title: 'Set as Primary?',
                        text: 'Do you want to set this card as your primary payment method?',
                        icon: 'question',
                        showCancelButton: true,
                        background: 'var(--body-background-color)',
                        color: 'var(--sidenavbar-text-color)',
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
                            confirmButtonText: 'OK',
                            background: 'var(--body-background-color)',
                            color: 'var(--sidenavbar-text-color)'
                        });
                        await loadCards();
                    }
                } catch (error) {
                    await showErrorAlert(error);
                }
            }

            /**
             * Updates primary card on server
             */
            async function updatePrimaryCard(cardId) {
                const response = await fetch('{{ route('owner.cards.update.default') }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ payment_method_id: cardId })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to set card as primary');
                }
            }

            /**
             * Handles card deletion
             */
            async function handleDeleteCard(e) {
                const cardId = e.target.dataset.cardId || e.target.closest('[data-card-id]').dataset.cardId;

                try {
                    const result = await Swal.fire({
                        title: 'Delete Card?',
                        text: 'Are you sure you want to delete this payment method?',
                        icon: 'warning',
                        showCancelButton: true,
                        background: 'var(--body-background-color)',
                        color: 'var(--sidenavbar-text-color)',
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
                            confirmButtonText: 'OK',
                            background: 'var(--body-background-color)',
                            color: 'var(--sidenavbar-text-color)'
                        });
                        await loadCards();
                    }
                } catch (error) {
                    await showErrorAlert(error);
                }
            }

            /**
             * Deletes card from server
             */
            async function deleteCard(cardId) {
                const response = await fetch('{{ route('owner.cards.delete') }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ payment_method_id: cardId })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || 'Failed to delete card');
                }
            }

            /**
             * Shows error alert
             */
            async function showErrorAlert(error) {
                await Swal.fire({
                    title: 'Error!',
                    text: error.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                console.error('Error:', error);
            }
        });
    </script>

    <!-- Update Profile -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const imageUpload = document.getElementById("image-upload");

            if (imageUpload) {
                imageUpload.addEventListener("change", function (event) {
                    let file = event.target.files[0];

                    if (file) {
                        let formData = new FormData();
                        formData.append("logo", file);
                        formData.append("_method", "PUT"); // Required for Laravel PUT request

                        fetch("{{ route('owner.organization.logo.update') }}", {
                            method: "POST",
                            body: formData,
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "Accept": "application/json"
                            }
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    let reader = new FileReader();
                                    reader.onload = function (e) {
                                        document.getElementById("organizantion-logo").src = e.target.result;
                                        document.querySelectorAll(".remove_picture_button").forEach(button => {
                                            button.style.display = "block";
                                        });
                                    };
                                    reader.readAsDataURL(file);

                                    // Show SweetAlert toast on success
                                    const Toast = Swal.mixin({
                                        toast: true,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 3000,
                                        timerProgressBar: true,
                                        didOpen: (toast) => {
                                            toast.addEventListener('mouseenter', Swal.stopTimer)
                                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                                        }
                                    });

                                    Toast.fire({
                                        icon: 'success',
                                        title: 'Logo updated successfully!'
                                    });
                                } else if (data.error) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: data.error
                                    });
                                }
                            })
                            .catch(error => {
                                console.error("Error:", error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Error updating profile picture. Please try again.'
                                });
                            })
                            .finally(() => {
                                imageUpload.disabled = false; // Re-enable input after upload
                            });

                        imageUpload.disabled = true; // Disable input during upload
                    }
                });
            }
        });
    </script>

    <!-- Update payment status -->
    <script>
        function updateOnlinePaymentStatus(checkbox, organizationId, merchantId) {
            if (!merchantId) {
                checkbox.checked = !checkbox.checked;

                // Show SweetAlert error message
                Swal.fire({
                    icon: 'error',
                    title: 'Cannot Update',
                    text: 'Online payment cannot be enabled because the organization is not linked to any payment gateway.',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                return;
            }

            const isEnabled = checkbox.checked ? 1 : 0;
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch("{{ route('owner.organization.onlinePaymentStatus.update') }}", {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    id: organizationId,
                    is_online_payment_enabled: isEnabled
                })
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        checkbox.checked = !checkbox.checked;
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.error,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message || 'Online payment status updated successfully',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    checkbox.checked = !checkbox.checked;
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while updating the online payment status.',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                });
        }
    </script>

    <!-- Plans Cancel and Resume -->
    <script>
        function confirmCancellation() {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will cancel your subscription at the end of the current billing period!",
                icon: 'warning',
                showCancelButton: true,
                background: 'var(--body-background-color)',
                color: 'var(--sidenavbar-text-color)',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, cancel it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('cancelSubscriptionForm').submit();
                }
            });
        }

        function confirmResume() {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will resume your subscription immediately!",
                icon: 'question',
                background: 'var(--body-background-color)',
                color: 'var(--sidenavbar-text-color)',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, resume it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('resumeSubscriptionForm').submit();
                }
            });
        }
    </script>
@endpush
