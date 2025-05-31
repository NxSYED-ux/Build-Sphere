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
            color: var(--main-text-color);
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

        /* Image Section */
        /* Updated Image Section */
        .membership-image-container {
            margin-bottom: 24px;
            border-radius: 10px;
            overflow: hidden;
            max-height: 400px;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f8f9fa;
            position: relative;
            /* Add these for the scrolling effect */
            height: 400px;
        }

        .membership-image {
            width: 100%;
            height: auto;
            object-fit: cover;
            display: block;
            /* Default state - no animation */
            transition: transform 0.3s ease;
        }

        /* Add this class via JavaScript when image is taller than container */
        .membership-image.auto-scroll {
            animation: scrollImage 20s linear infinite alternate;
        }

        @keyframes scrollImage {
            0% {
                transform: translateY(0);
            }
            100% {
                /* This will be calculated dynamically in JS based on image height */
                transform: translateY(calc(-100% + 600px));
            }
        }


        /* Key Info Grid */
        .key-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .info-card {
            background: var(--body-background-color);
            border-radius: 10px;
            padding: 18px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
            border: 1px solid #f0f0f0;
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

        /* Pricing Section */
        .pricing-card {
            background: linear-gradient(135deg, var(--body-background-color) 0%, var(--sidenavbar-body-color) 100%);
            border: 1px solid #e0e8ff;
            position: relative;
            overflow: hidden;
        }

        .pricing-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 80px;
            height: 80px;
            background: rgba(var(--color-blue), 0.05);
            border-radius: 50%;
            transform: translate(40px, -40px);
        }

        .price-display {
            display: flex;
            align-items: flex-end;
            margin-bottom: 6px;
        }

        .current-price {
            font-size: 30px;
            font-weight: 700;
            color: var(--color-blue);
            line-height: 1;
        }

        .original-price {
            font-size: 16px;
            text-decoration: line-through;
            color: var(--sidenavbar-text-color);
            margin-left: 10px;
            margin-bottom: 4px;
        }

        .discount-badge {
            background: #10b981;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
            line-height: 1;
        }

        /* Subscription Dates */
        .dates-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 16px;
        }

        .date-card {
            background: var(--body-background-color);
            border-radius: 10px;
            padding: 18px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        }

        /* Description */
        .description-content {
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

        .users-table th {
            text-align: left;
            padding: 14px 20px;
            font-size: 13px;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
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

        .btn-edit {
            background: var(--color-blue);
            color: white;
        }

        .btn-edit:hover {
            background: rgba(var(--color-blue), 0.9);
        }

        .btn-back {
            background: white;
            color: #444;
            border-color: #e0e0e0;
        }

        .btn-back:hover {
            background: #f5f5f5;
        }

        /* Section Headings */
        .section-heading {
            font-size: 18px;
            font-weight: 600;
            color: var(--main-text-color);
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
        <div class="container my-3 mx-2">
            <!-- Main Membership Card -->
            <div class="card shadow">
                <div class="card-body">
                    <!-- Membership Header -->
                    <div class="membership-header">
                        <div class="membership-title-container">
                            <h1 class="membership-name">{{ $membership->name }}</h1>
                            <div class="membership-meta">
                                <span class="meta-badge category-badge">
                                    <i class="fas fa-tag"></i>
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

                    <!-- Membership Image -->
                    <div class="membership-image-container">
                        <img src="{{ $membership->image ? asset($membership->image) : asset('img/placeholder-profile.png') }}"
                             alt="{{ $membership->name }}"
                             class="membership-image">
                    </div>

                    <!-- Key Information Grid -->
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
                    </div>

                    <!-- Pricing Section -->
                    <h3 class="section-heading">
                        <i class="fas fa-tags section-heading-icon"></i>
                        Pricing Information
                    </h3>
                    <div class="info-card pricing-card">
                        <div class="price-display">
                            <span class="current-price">{{ number_format($membership->price, 2) }} {{ $membership->currency }}</span>
                            @if($membership->original_price > $membership->price)
                                <span class="original-price">{{ number_format($membership->original_price, 2) }} {{ $membership->currency }}</span>
                                <span class="discount-badge">
                                    {{ round(100 - ($membership->price / $membership->original_price * 100)) }}% OFF
                                </span>
                            @endif
                        </div>
                        <div class="info-label" style="margin-top: 12px;">
                            <i class="fas fa-info-circle info-icon"></i>
                            Billed every {{ $membership->duration_months }} month(s)
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description Card -->
            <div class="card shadow">
                <div class="card-header border-bottom">
                    <h3 class="section-heading" style="margin: 0;">
                        <i class="fas fa-align-left section-heading-icon"></i>
                        Description
                    </h3>
                </div>
                <div class="card-body">
                    <div class="description-content">
                        {{ $membership->description ?? 'No description provided.' }}
                    </div>
                </div>
            </div>

            <!-- Subscribed Users Card -->
            <div class="card shadow">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h3 class="section-heading" style="margin: 0;">
                            <i class="fas fa-users section-heading-icon"></i>
                            Subscribed Users
                        </h3>
                        <span style="background: var(--color-blue); color: white; padding: 6px 12px; border-radius: 20px; font-size: 13px; font-weight: 500;">
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
                                    <th style="width: 30%;">User</th>
                                    <th style="width: 30%;">Email</th>
                                    <th style="width: 20%;">Contact</th>
                                    <th style="width: 20%;">Status</th>
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
                                        <td>
                                                <span class="user-status status-{{ $user->status === 1 ? 'active' : 'inactive' }}">
                                                    {{ $user->status === 1 ? 'Active' : 'Inactive' }}
                                                </span>
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
                            <h4 style="font-weight: 600; margin-bottom: 8px; color: var(--main-text-color);">No Subscribed Users</h4>
                            <p style="color: #888; max-width: 400px; margin: 0 auto;">
                                This membership plan doesn't have any subscribers yet.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
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
    });

    function initScrollEffect(img, container) {
        const containerHeight = container.clientHeight;
        const imgHeight = img.clientHeight;

        // Only apply effect if image is taller than container
        if (imgHeight > containerHeight) {
            img.classList.add('auto-scroll');

            // Calculate the exact scroll distance needed
            const scrollDistance = imgHeight - containerHeight;
            img.style.setProperty('--scroll-distance', `-${scrollDistance}px`);

            // Pause animation on hover
            container.addEventListener('mouseenter', () => {
                img.style.animationPlayState = 'paused';
            });

            container.addEventListener('mouseleave', () => {
                img.style.animationPlayState = 'running';
            });
        }
    }
</script>
