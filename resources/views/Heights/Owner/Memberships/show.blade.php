@extends('layouts.app')

@section('title', $membership->name . ' - Membership Details')

@push('styles')
    <style>
        /* Base Styles */
        #main {
            margin-top: 45px;
        }

        /* Card Styles */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 4px 12px rgba(0, 0, 0, 0.04);
            margin-bottom: 24px;
            background: var(--body-card-bg);
            overflow: hidden;
        }

        .card-header {
            background: transparent;
            /*border-bottom: 1px solid rgba(0, 0, 0, 0.06);*/
            padding: 20px 24px;
        }

        .card-body {
            padding: 24px;
        }

        /* Membership Header */
        .membership-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
            position: relative;
        }

        .membership-title-container {
            flex: 1;
            min-width: 0;
        }

        .membership-name {
            font-size: 26px;
            font-weight: 700;
            color: var(--sidenavbar-text-color);
            margin: 0 0 8px 0;
            line-height: 1.3;
        }

        .membership-meta {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .meta-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            line-height: 1;
        }

        .category-badge {
            background: rgba(var(--color-blue), 0.1);
            color: var(--color-blue);
        }

        .status-badge {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .status-draft {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .featured-badge {
            background: rgba(234, 179, 8, 0.1);
            color: #ca8a04;
        }

        .header-actions {
            display: flex;
            gap: 12px;
            margin-left: 16px;
        }

        /* Container for image and info grid */
        .image-info-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }

        /* Image Section */
        .membership-image-container {
            border-radius: 10px;
            overflow: hidden;
            max-height: 320px;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f8f9fa;
            position: relative;
            height: 320px;
        }

        .membership-image {
            width: 100%;
            height: auto;
            min-height: 320px;
            object-fit: cover;
            display: block;
            transition: transform 0.3s ease;
        }

        /* Add this class via JavaScript when conditions are met */
        .membership-image.auto-scroll {
            animation: scrollImage 5s linear infinite alternate;
        }

        @keyframes scrollImage {
            0% {
                transform: translateY(0);
            }
            100% {
                /* Will be overwritten by JavaScript */
                transform: translateY(calc(-100% + 400px));
            }
        }

        /* Disable animation on small screens */
        @media (max-width: 768px) {
            .membership-image.auto-scroll {
                animation: none;
            }
        }


        /* Key Info Grid */
        .key-info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(240px, 1fr));
            gap: 16px;
            align-content: start;
        }

        .info-card {
            background: var(--body-background-color);
            border-radius: 10px;
            padding: 18px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
            border: 1px solid #f0f0f0;
            min-width: 240px;
        }

        .info-label {
            font-size: 13px;
            color: var(--sidenavbar-text-color);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            font-weight: 500;
        }

        .info-value {
            font-size: 18px;
            font-weight: 600;
            margin-left: 30px;
            color: var(--sidenavbar-text-color);
            line-height: 1.4;
        }

        .info-icon {
            margin-right: 10px;
            color: var(--color-blue);
            font-size: 15px;
            width: 20px;
            text-align: center;
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .image-info-container {
                grid-template-columns: 1fr;
            }

            .key-info-grid {
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            }
        }
        /* Price Overlay Styles */
        .price-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.4) 60%, transparent 100%);
            padding: 30px 20px 20px;
            color: white;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        .price-content {
            max-width: 100%;
        }

        .price-display {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 8px;
        }

        .current-price {
            font-size: 28px;
            font-weight: 700;
            color: #fff;
            text-shadow: 0 1px 3px rgba(0,0,0,0.3);
        }

        .original-price {
            font-size: 18px;
            text-decoration: line-through;
            color: rgba(255,255,255,0.7);
            font-weight: 500;
        }

        .discount-badge {
            background: var(--color-blue);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .billing-info i {
            font-size: 16px;
        }

        /* Add this to your existing image container */
        .membership-image-container {
            position: relative; /* Required for absolute positioning of overlay */
        }

        /* Toggle Switch Styles */
        .image-info-container .featured-toggle-card {
            position: relative;
        }

        .image-info-container .featured-toggle-card .info-value {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .image-info-container .switch {
            position: relative;
            display: inline-block;
            width: 36px;
            height: 18px;
        }

        .image-info-container .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .image-info-container .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .image-info-container .slider:before {
            position: absolute;
            content: "";
            height: 14px;
            width: 14px;
            left: 2px;
            bottom: 2px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        .image-info-container input:checked + .slider {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
        }

        .image-info-container input:checked + .slider:before {
            transform: translateX(18px);
        }

        .image-info-container .featured-status {
            font-size: 16px;
            font-weight: 500;
            color: var(--sidenavbar-text-color);
        }

        /* Add glow effect when featured is active */
        .image-info-container input:checked ~ .featured-status {
            color: var(--color-blue);
            font-weight: 600;
        }

        /* Loading state */
        .image-info-container .switch .fa-spinner {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 10px;
        }
        /* Description */
        .image-info-container .description-content {
            line-height: 1.7;
            color: var(--sidenavbar-text-color);
            font-size: 15px;
        }

        /* Users Table */
        .users-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .users-table thead tr{
            background-color: var(--sidenavbar-body-color) !important;
        }

        .users-table th {
            text-align: left;
            padding: 14px 20px;
            font-size: 13px;
            color: var(--sidenavbar-text-color);
            font-weight: 600;
            /*letter-spacing: 0.3px;*/
            border-bottom: 1px solid #f0f0f0;
            background: #fafafa;
        }

        .users-table td {
            padding: 14px 20px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
            background: white;
        }

        .users-table tr:first-child td {
            border-top: 1px solid #f0f0f0;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 12px;
        }

        .user-name {
            display: flex;
            align-items: center;
            font-weight: 500;
        }

        .user-status {
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }

        .status-active {
            background: #ecfdf5;
            color: #10b981;
        }

        .status-inactive {
            background: #fef2f2;
            color: #ef4444;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 48px 20px;
            background: white;
        }

        .empty-icon {
            font-size: 52px;
            color: #e0e0e0;
            margin-bottom: 16px;
        }

        /* Action Buttons */
        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .btn-icon {
            margin-right: 8px;
            font-size: 14px;
        }

        /* Section Headings */
        .section-heading {
            font-size: 18px;
            font-weight: 600;
            color: var(--sidenavbar-text-color);
            margin: 32px 0 16px 0;
            display: flex;
            align-items: center;
        }

        .section-heading-icon {
            margin-right: 12px;
            color: var(--color-blue);
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .key-info-grid, .dates-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {

            .membership-header {
                flex-direction: column;
            }

            .header-actions {
                margin-left: 0;
                margin-top: 16px;
                width: 100%;
                justify-content: flex-end;
            }

            .key-info-grid, .dates-grid {
                grid-template-columns: 1fr;
            }

            .card-body {
                padding: 20px;
            }
        }

        @media (max-width: 576px) {
            .membership-name {
                font-size: 22px;
            }

            .membership-meta {
                gap: 8px;
            }

            .header-actions {
                flex-direction: column;
                gap: 8px;
            }

            .header-actions .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endpush

@section('content')
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('owner_manager_dashboard'), 'label' => 'Dashboard'],
            ['url' => route('owner.memberships.index'), 'label' => 'Memberships'],
            ['url' => '', 'label' => 'Details']
        ]"
    />
    <x-Owner.side-navbar :openSections="['Memberships']" />
    <x-error-success-model />

    <div id="main">
        <div class="container-fluid my-3 mx-2">
            <div class="row">
                <div class="col-12">
                    <!-- Main Membership Card -->
                    <div class="card shadow">
                        <div class="card-body">
                            <!-- Membership Header -->
                            <div class="membership-header">
                                <div class="membership-title-container">
                                    <h1 class="membership-name">{{ $membership->name }}</h1>
                                    <div class="membership-meta">
                                <span class="meta-badge category-badge">
                                    <i class="fas fa-tag me-1"></i>
                                    {{ $membership->category }}
                                </span>
                                        <span class="meta-badge {{ $membership->status === 'Published' ? 'status-badge' : 'status-draft' }}">
                                    {{ $membership->status }}
                                </span>
                                        @if($membership->mark_as_featured)
                                            <span class="meta-badge featured-badge">
                                        <i class="fas fa-star"></i> Featured
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="header-actions">
                                    <a href="{{ route('owner.memberships.edit', $membership->id) }}" class="btn btn-primary">
                                        <i class="fas fa-edit btn-icon"></i> Edit
                                    </a>
                                    <a href="{{ route('owner.memberships.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left btn-icon"></i> Back
                                    </a>
                                </div>
                            </div>

                            <div class="image-info-container">
                                <!-- Left Half - Image with Price Overlay -->
                                <div class="membership-image-container">
                                    <img src="{{ $membership->image ? asset($membership->image) : asset('img/placeholder-profile.png') }}"
                                         alt="{{ $membership->name }}"
                                         class="membership-image">

                                    <!-- Price Overlay -->
                                    <div class="price-overlay">
                                        <div class="price-content">
                                            <div class="price-display">
                                                <span class="current-price">{{ number_format($membership->price, 2) }} {{ $membership->currency }}</span>
                                                @if($membership->original_price > $membership->price)
                                                    <span class="original-price">{{ number_format($membership->original_price, 2) }} {{ $membership->currency }}</span>
                                                    <span class="discount-badge">
                                                        {{ round(100 - ($membership->price / $membership->original_price * 100)) }}% OFF
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Half - Key Information Grid -->
                                <div class="key-info-grid">
                                    <div class="info-card">
                                        <div class="info-label">
                                            <i class="fas fa-building info-icon"></i>
                                            Building
                                        </div>
                                        <div class="info-value">
                                            {{ $membership->building->name ?? 'N/A' }}
                                        </div>
                                    </div>

                                    <div class="info-card">
                                        <div class="info-label">
                                            <i class="fas fa-door-open info-icon"></i>
                                            Unit
                                        </div>
                                        <div class="info-value">
                                            {{ $membership->unit->unit_name ?? 'N/A' }}
                                        </div>
                                    </div>

                                    <div class="info-card">
                                        <div class="info-label">
                                            <i class="fas fa-qrcode info-icon"></i>
                                            Scans Per Day
                                        </div>
                                        <div class="info-value">
                                            {{ $membership->scans_per_day }}
                                        </div>
                                    </div>

                                    <div class="info-card">
                                        <div class="info-label">
                                            <i class="fas fa-clock info-icon"></i>
                                            Duration
                                        </div>
                                        <div class="info-value">
                                            {{ $membership->duration_months }} months
                                        </div>
                                    </div>

                                    <!-- Featured Toggle Card -->
                                    <div class="info-card featured-toggle-card">
                                        <div class="info-label">
                                            <i class="fas fa-star info-icon"></i>
                                            Featured
                                        </div>
                                        <div class="info-value">
                                            <label class="switch">
                                                <input type="checkbox" class="featured-toggle"
                                                       data-membership-id="{{ $membership->id }}"
                                                    {{ $membership->mark_as_featured ? 'checked' : '' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <span class="featured-status">{{ $membership->mark_as_featured ? 'Featured' : 'Not Featured' }}</span>
                                        </div>
                                    </div>

                                    <div class="info-card">
                                        <div class="info-label">
                                            <i class="fas fa-external-link-alt info-icon"></i>
                                            URL
                                        </div>
                                        <div class="info-value">
                                            @if($membership->url)
                                                <a href="{{ $membership->url }}" target="_blank" class="text-primary">
                                                    Visit Link
                                                </a>
                                            @else
                                                N/A
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description Card -->
                    <div class="card shadow">
                        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                            <h3 class="section-heading" style="margin: 0;">
                                <i class="fas fa-align-left section-heading-icon"></i>
                                Description
                            </h3>
                            <a href="{{ route('owner.memberships.assign.view', $membership->id) }}" class="btn btn-primary">
                                <i class="bx bxs-user-check btn-icon fs-5"></i> Assign Membership
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="description-content">
                                {{ $membership->description ?? 'No description provided.' }}
                            </div>
                        </div>
                    </div>

                    <!-- Subscribed Users Card -->
                    <div class="card shadow">
                        <div class="card-header border-bottom-0">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <h3 class="section-heading" style="margin: 0;">
                                    <i class="fas fa-users section-heading-icon"></i>
                                    Subscribed Users
                                </h3>
                                <span style="background: var(--color-blue); color: white !important; padding: 6px 12px; border-radius: 20px; font-size: 13px; font-weight: 500;">
                            {{ $membership->membershipUsers->count() }} users
                        </span>
                            </div>
                        </div>
                        <div class="card-body" style="padding: 0;">
                            @if($membership->membershipUsers->count() > 0)
                                <div style="overflow-x: auto;">
                                    <table class="users-table">
                                        <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Email</th>
                                            <th>Contact</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Mark Payment Received</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($membership->membershipUsers as $user)
                                            <tr>
                                                <td>
                                                    <div class="user-name">
                                                        <img src="{{ $user->user->picture ? asset($user->user->picture) : asset('img/default-avatar.png') }}"
                                                             class="user-avatar"
                                                             alt="{{ $user->user->name ?? 'N/A' }}">
                                                        {{ $user->user->name ?? 'N/A' }}
                                                    </div>
                                                </td>
                                                <td>{{ $user->user->email ?? 'N/A' }}</td>
                                                <td>{{ $user->user->phone_no ?? 'N/A' }}</td>
                                                <td>{{ $user->subscription->created_at ? \Carbon\Carbon::parse($user->subscription->created_at)->format('M d, Y') : 'N/A' }}</td>
                                                <td>{{ $user->subscription->ends_at ? \Carbon\Carbon::parse($user->subscription->ends_at)->format('M d, Y') : 'N/A' }}</td>
                                                <td>
                                                    <button class="btn btn-primary mark-payment-received"
                                                            data-user-membership-id="{{ $user->id }}">
                                                        Mark Payment
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <i class="fas fa-user-slash"></i>
                                    </div>
                                    <h4 style="font-weight: 600; margin-bottom: 8px; color: var(--sidenavbar-text-color);">No Subscribed Users</h4>
                                    <p style="color: #888; max-width: 400px; margin: 0 auto;">
                                        This membership plan doesn't have any subscribers yet.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
@endsection

@push('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add event listeners to all mark payment buttons
            document.querySelectorAll('.mark-payment-received').forEach(button => {
                button.addEventListener('click', function() {
                    const userMembershipId = this.getAttribute('data-user-membership-id');

                    // Show confirmation dialog
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You want to mark this payment as received?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, mark as received!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Prepare the request data
                            const requestData = {
                                user_membership_id: userMembershipId,
                                _token: '{{ csrf_token() }}' // CSRF token for Laravel
                            };

                            // Make the fetch request
                            fetch("{{ route('owner.memberships.planPaymentReceived') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify(requestData)
                            })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error('Network response was not ok');
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (data.message) {
                                        Swal.fire(
                                            'Success!',
                                            data.message,
                                            'success'
                                        );
                                        // Optional: reload the page or update the UI
                                        // window.location.reload();
                                    } else {
                                        throw new Error('Unexpected response format');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    Swal.fire(
                                        'Error!',
                                        error.message || 'Failed to mark payment as received.',
                                        'error'
                                    );
                                });
                        }
                    });
                });
            });
        });
    </script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Only run if not on mobile
        if (window.innerWidth > 768) {
            const imageContainers = document.querySelectorAll('.membership-image-container');

            imageContainers.forEach(container => {
                const img = container.querySelector('.membership-image');

                // Check if image is loaded
                if (img.complete) {
                    initScrollEffect(img, container);
                } else {
                    img.addEventListener('load', () => initScrollEffect(img, container));
                }
            });
        }
    });

    function initScrollEffect(img, container) {
        const containerHeight = container.clientHeight;
        const imgHeight = img.clientHeight;

        // Only apply effect if image is significantly taller than container
        if (imgHeight > containerHeight * 1.2) { // 20% taller threshold
            img.classList.add('auto-scroll');

            // Calculate the exact scroll distance needed
            const scrollDistance = imgHeight - containerHeight;
            img.style.setProperty('--scroll-distance', `-${scrollDistance}px`);

            // Pause animation on hover for better UX
            container.addEventListener('mouseenter', () => {
                img.style.animationPlayState = 'paused';
            });

            container.addEventListener('mouseleave', () => {
                img.style.animationPlayState = 'running';
            });

            // Handle window resize
            window.addEventListener('resize', () => {
                if (window.innerWidth <= 768) {
                    img.classList.remove('auto-scroll');
                } else if (imgHeight > container.clientHeight * 1.2) {
                    img.classList.add('auto-scroll');
                    const newScrollDistance = img.clientHeight - container.clientHeight;
                    img.style.setProperty('--scroll-distance', `-${newScrollDistance}px`);
                } else {
                    img.classList.remove('auto-scroll');
                }
            });
        }
    }
</script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.body.addEventListener('change', function(e) {
                if (e.target.classList.contains('featured-toggle')) {
                    const toggle = e.target;
                    const membershipId = toggle.dataset.membershipId;
                    const isChecked = toggle.checked;
                    const statusElement = toggle.closest('.info-value').querySelector('.featured-status');

                    // Store original state in case we need to revert
                    const originalState = statusElement.textContent;
                    const originalSliderHTML = toggle.nextElementSibling.innerHTML;

                    // Show loading state
                    toggle.nextElementSibling.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    toggle.disabled = true;
                    statusElement.textContent = 'Updating...';

                    fetch("{{ route('owner.memberships.toggle.featured') }}", {
                        method: 'PUT',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            membership_id: membershipId,
                            value: isChecked ? 1 : 0
                        })
                    })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => {
                                    throw new Error(
                                        err.message ||
                                        err.error ||
                                        'Request failed with status ' + response.status
                                    );
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                                return;
                            }

                            // Update status text
                            statusElement.textContent = isChecked ? 'Featured' : 'Regular';

                            // Show success message
                            const successMessage = data.success || 'Membership featured status updated';
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: successMessage,
                                timer: 2000,
                                background: 'var(--body-background-color)',
                                color: 'var(--sidenavbar-text-color)',
                                showConfirmButton: false
                            });
                        })
                        .catch(error => {
                            console.error('Error:', error);

                            // Revert UI to previous state
                            toggle.checked = !isChecked;
                            statusElement.textContent = originalState;

                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: error.message || 'Something went wrong. Please try again.',
                                timer: 2000,
                                background: 'var(--body-background-color)',
                                color: 'var(--sidenavbar-text-color)',
                                showConfirmButton: true
                            });
                        })
                        .finally(() => {
                            // Restore slider
                            toggle.nextElementSibling.innerHTML = originalSliderHTML;
                            toggle.disabled = false;
                        });
                }
            });
        });
    </script>
@endpush
