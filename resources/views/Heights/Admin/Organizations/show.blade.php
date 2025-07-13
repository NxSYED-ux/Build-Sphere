@extends('layouts.app')

@section('title', 'Organization')

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

    <!--  -->
    <x-Admin.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('admin_dashboard'), 'label' => 'Dashboard'],
            ['url' => route('organizations.index'), 'label' => 'Organizations'],
            ['url' => '', 'label' => 'Details']
        ]"
    />
    <!--  -->
    <x-Admin.side-navbar :openSections="['Organizations']" />
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

                        <h3 class="mb-1 fw-bold my-2">{{ $organization->name }}</h3>

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
                                <a href="{{ route('organizations.edit', $organization->id) }}" class="rounded-pill py-2 fw-medium hidden AdminEditOrganizations">
                                    <i class="fas fa-edit me-2 text-warning fs-5"></i>
                                </a>
                            </div>

                            <div class="detail-list">

                                <div class="row">
                                    <div class="col-md-6 detail-item py-1">
                                        <div class="detail-label">Membership ID</div>
                                        <div class="detail-value fw-medium">{{ $organization->payment_gateway_merchant_id ?? 'N/A' }}</div>
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
                                            <a href="" class="text-decoration-none">{{ $organization->email ?? 'N/A' }}</a>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6 col-lg-12 detail-item py-1">
                                        <div class="detail-label">Phone Number</div>
                                        <div class="detail-value fw-medium d-flex align-items-center">
                                            <i class="fas fa-phone me-2 text-primary"></i>
                                            <a href="" class="text-decoration-none">{{ $organization->phone ?? 'N/A' }}</a>
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
                                <a href="{{ route('users.edit', $organization->owner->id) }}" class="rounded-pill py-2 fw-medium hidden AdminEditUser">
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

                        @if(!empty($subscription) && isset($subscription['name']))
                            <div class="row">
                                {{-- Subscription Plan Details --}}
                                <div class="col-md-6 mb-4">
                                    <div class="plan-card p-4 h-100">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h5 class="mb-1">{{ $subscription['name'] }}</h5>
                                                <span class="badge bg-success bg-opacity-10 text-success py-1 px-2 mx-3 rounded-pill">
                                                    <i class="fas fa-check-circle me-1"></i> {{ $subscription['status'] ?? 'N/A' }}
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
                                                <span>
                                                    {{ isset($subscription['price'], $subscription['currency'])
                                                        ? $subscription['price'] . ' ' . $subscription['currency']
                                                        : 'N/A' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Plan Actions --}}
                                <div class="col-md-6 mb-4">
                                    <div class="plan-card featured p-4 h-100">
                                        <h5 class="fw-semibold">Plan Usage</h5>

                                        <div style="position: relative; width: 370px; height: 200px; margin: auto;">
                                            <div id="chartdiv" style="width: 85%; height: 100%;"></div>

                                            <div id="gaugeLabel"
                                                 style="
                                                     position: absolute;
                                                     top: 65%;
                                                     left: 42%;
                                                     transform: translate(-50%, -40%); /* adjust vertical offset as needed */
                                                     font-size: 25px;
                                                     font-weight: bold;
                                                     color: var(--sidenavbar-text-color);">
                                                {{ $usage }}%
                                            </div>
                                        </div>

                                        <div class="mt-auto pt-2">
                                            <form id="organizationPlanUpgradeForm" action="{{ route('organizations.plan.upgrade.index', $organization->id ) }}" method="GET" class="d-inline hidden AdminUpgradeOrganizationsPlan">
                                                @csrf
                                                <button type="submit" class="btn btn-success w-100 py-2 mb-3 rounded-1">
                                                    <i class="fas fa-arrow-up me-2"></i> Upgrade Plan
                                                </button>
                                            </form>

                                            <form id="planPaymentReceivedForm" action="{{ route('organizations.planPaymentReceived') }}" method="POST" class="d-inline hidden AdminRecordPlanPaymentOrganizationsPlan">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $organization->id }}">
                                                <button type="button" onclick="confirmPlanPaymentReceived()" class="btn btn-primary w-100 py-2 mb-3 rounded-1">
                                                    <i class="fas fa-money me-2"></i> Marked Payment Received
                                                </button>
                                            </form>

                                            @if($subscription['status'] === 'Active')
                                                <form id="cancelSubscriptionForm" action="{{ route('organizations.planSubscription.cancel') }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="id" value="{{ $organization->id }}">
                                                    <button type="button" onclick="confirmCancellation()" class="btn btn-danger w-100 py-2 rounded-1">
                                                        <i class="fas fa-ban me-2"></i> Cancel Subscription
                                                    </button>
                                                </form>
                                            @elseif($subscription['status'] === 'Cancelled')
                                                <form id="resumeSubscriptionForm" action="{{ route('organizations.planSubscription.resume') }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="id" value="{{ $organization->id }}">
                                                    <button type="button" onclick="confirmResume()" class="btn btn-outline-success w-100 py-2 rounded-1">
                                                        <i class="fas fa-play me-2"></i> Resume Subscription
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning text-center fw-bold">
                                No plan subscribed yet.
                            </div>
                        @endif

                    </div>

                    <!-- Billing History -->
                    <div class="profile-card p-4 shadow">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="section-title mb-0">Transactions</h4>
                            <a href="{{ route('finance.index',['organization_id'=>$organization->id]) }}" class="btn btn-sm btn-primary text-white text-decoration-none hidden AdminFinance" style="color: #fff !important;">
                                All Transactions
                            </a>
                        </div>

                        <div class="row g-3" id="transactions-container">
                            <!-- Loading state will appear here -->
                        </div>
                    </div>
                </div>

                <div class="col-12">

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
                    url: '{{ route("finance.latest", $organization->id) }}',
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                container.empty();

                if (response.history && response.history.length > 0) {
                    response.history.forEach(function(transaction) {
                        const amountSign = transaction.type === 'Credit' ? '+' : '-';
                        const isFailed = transaction.status.toLowerCase() === 'failed';


                        const cardHtml = `
                        <div class="col-md-6 col-xl-6">
                            <a href="${'{{ route('finance.show', ':id') }}'.replace(':id', transaction.id)}" class="text-white text-decoration-none" style="color: #fff !important;">
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
        document.addEventListener('DOMContentLoaded', function() {
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
        am4core.ready(function () {
            am4core.useTheme(am4themes_animated);

            var chart = am4core.create("chartdiv", am4charts.GaugeChart);
            chart.innerRadius = -20;
            chart.startAngle = -180;
            chart.endAngle = 0;
            chart.logo.disabled = true;

            // Remove any default container strokes
            chart.radarContainer.strokeWidth = 0;

            // Axis
            var axis = chart.xAxes.push(new am4charts.ValueAxis());
            axis.min = 0;
            axis.max = 100;
            axis.strictMinMax = true;
            axis.renderer.grid.template.disabled = true;
            axis.renderer.labels.template.disabled = true;
            axis.renderer.ticks.template.disabled = true;
            axis.renderer.line.disabled = true;  // This removes the axis line completely
            axis.renderer.minGridDistance = 0;   // Prevents any residual rendering

            // Background range
            var rangeBg = axis.axisRanges.create();
            rangeBg.value = 0;
            rangeBg.endValue = 100;
            rangeBg.axisFill.fill = am4core.color("#E5E7EB");
            rangeBg.axisFill.fillOpacity = 1;
            rangeBg.axisFill.innerRadius = -20;
            rangeBg.axisFill.cornerRadius = 25;
            rangeBg.axisFill.strokeWidth = 0;  // Explicitly remove stroke

            // Usage range
            const usage = @json($usage);
            var rangeFill = axis.axisRanges.create();
            rangeFill.value = 0;
            rangeFill.endValue = 0; // Start from 0

            var gradient = new am4core.LinearGradient();
            gradient.addColor(am4core.color("#3B82F6"));
            gradient.addColor(am4core.color("#60A5FA"));
            rangeFill.axisFill.fill = gradient;
            rangeFill.axisFill.fillOpacity = 1;
            rangeFill.axisFill.innerRadius = -20;
            rangeFill.axisFill.cornerRadius = 25;
            rangeFill.axisFill.strokeWidth = 0;  // Explicitly remove stroke

            // First animation: 0 to 100%
            var fillAnimation1 = rangeFill.animate(
                { property: "endValue", to: 100 },
                1000,
                am4core.ease.cubicOut
            );

            // When first animation completes, animate back to usage
            fillAnimation1.events.on("animationended", function () {
                // Animate both the range and bullet together
                var fillAnimation2 = rangeFill.animate(
                    { property: "endValue", to: usage },
                    800,
                    am4core.ease.cubicOut
                );

                // Synchronize animations
                fillAnimation2.events.on("animationprogress", function(ev) {
                    rangeFill.endValue = 100 - ev.progress * (100 - usage);
                });
            });
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
                        // Show loading indicator
                        const loadingToast = Swal.fire({
                            title: 'Uploading...',
                            html: 'Please wait while we upload your logo',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        let formData = new FormData();
                        const orgId = @json($organization->id);
                        formData.append("logo", file);
                        formData.append("id", orgId);
                        formData.append("_method", "PUT");

                        fetch("{{ route('organizations.logo.update') }}", {
                            method: "POST",
                            body: formData,
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
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

                                    // Close loading indicator and show success toast
                                    loadingToast.close();

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
                                    loadingToast.close();
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Upload Failed',
                                        text: data.error
                                    });
                                }
                            })
                            .catch(error => {
                                console.error("Error:", error);
                                loadingToast.close();
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Upload Error',
                                    text: 'Error updating logo. Please try again.'
                                });
                            })
                            .finally(() => {
                                imageUpload.disabled = false;
                                imageUpload.value = ''; // Reset the file input
                            });

                        imageUpload.disabled = true;
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

            fetch("{{ route('organizations.onlinePaymentStatus.update') }}", {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
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
                        // Success - show success message
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
                text: "This will cancel subscription at the end of the current billing period!",
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

        function confirmPlanPaymentReceived() {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will marked the plan payment received and extend the deadline!",
                icon: 'warning',
                showCancelButton: true,
                background: 'var(--body-background-color)',
                color: 'var(--sidenavbar-text-color)',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, received!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('planPaymentReceivedForm').submit();
                }
            });
        }

        function confirmResume() {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will resume subscription immediately!",
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
