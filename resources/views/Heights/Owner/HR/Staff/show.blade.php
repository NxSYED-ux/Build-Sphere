@extends('layouts.app')

@section('title', 'Staff Details')

@push('styles')
    <style>
        :root {
            --primary-color: var(--color-blue);
            --primary-hover: var(--color-blue);
            --secondary-color: #f8f9fa;
            --border-color: #e2e8f0;
            --error-color: #e53e3e;
            --success-color: #38a169;
            --permission-header-bg: rgba(var(--color-blue), 0.05);
            --permission-card-bg: var(--body-background-color);
            --child-permission-indicator: var(--color-blue);
        }
        #main {
            margin-top: 45px;
        }

        .chart-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            background: var(--sidenavbar-body-color);
            overflow: hidden;
        }

        .chart-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .filter-section {
            background:  var(--sidenavbar-body-color);
            border-radius: 14px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .filter-section label {
            font-weight: 500;
            color: var(--sidenavbar-text-color) !important;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }
        .empty-state h4 {
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: var(--sidenavbar-text-color);
        }

        .empty-state p {
            max-width: 400px;
            margin: 0 auto 1.5rem;
        }

        .btn-primary {
            background-color: var(--color-blue);
            border-color: var(--color-blue);
            padding: 0.5rem 1.25rem;
            font-weight: 500;
            letter-spacing: 0.2px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--color-blue);
            border-color: var(--color-blue);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .form-select, .form-control {
            border-radius: 8px;
            padding: 0.5rem 1rem;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .form-select:focus, .form-control:focus {
            border-color: #a5b4fc;
            box-shadow: 0 0 0 3px rgba(199, 210, 254, 0.5);
        }

        .section-title {
            font-weight: 600;
            color: var(--sidenavbar-text-color) !important;
            margin-bottom: 1.5rem;
            font-size: 1.25rem;
            position: relative;
            padding-left: 1rem;
        }

        .section-title::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: #2196F3;
            border-radius: 4px;
        }

        @media (max-width: 768px) {
        }

        .chart-container {
            position: relative;
            width: 100%;
            min-height: 300px;
            margin-bottom: 2rem;
        }

        /* For smaller screens */
        @media (max-width: 768px) {
            .chart-container {
                min-height: 250px;
            }
        }

        /* Staff Details Container */
        .staff-detail-container {
            background: var(--body-card-bg);
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .staff-detail-hero {
            display: flex;
            flex-direction: row;
            padding: 2rem 2rem 0.5rem 2rem;
        }

        .staff-image-section {
            flex: 0 0 250px;
            padding-right: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .staff-avatar {
            width: 200px;
            height: 200px;
            border-radius: 10px;
            object-fit: cover;
            border: 4px solid #f8f9fa;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .staff-detail-section {
            flex: 1;
        }

        .staff-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .staff-name {
            font-size: 2rem;
            font-weight: 700;
            color: var(--sidenavbar-text-color);
            margin: 0;
        }

        .staff-header-actions {
            display: flex;
            gap: 0.75rem;
        }

        .staff-position {
            font-size: 1.1rem;
            color: var(--sidenavbar-text-color);
            margin-bottom: 1.5rem;
        }

        .staff-meta {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.95rem;
            padding: 0.1rem 0;
        }

        .promote-item {
            align-items: flex-start;
        }

        .meta-icon {
            color: var(--color-blue);
            width: 20px;
            text-align: center;
            font-size: 1rem;
            margin-top: 2px;
        }

        .query-handling-toggle {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            justify-content: space-between;
        }

        .toggle-label {
            font-weight: 500;
            color: var(--sidenavbar-text-color);
        }

        .enable-query-toggle-btn {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
            margin-right: 30px;
        }

        .enable-query-toggle-btn input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #e9ecef;
            transition: .4s;
            border-radius: 24px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background-color: var(--color-blue);
        }

        input:checked + .toggle-slider:before {
            transform: translateX(20px);
        }

        /* Button Styles */
        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
            border: none;
            white-space: nowrap;
        }

        .btn-edit {
            background-color: var(--color-blue);
            color: white;
        }

        .btn-edit:hover {
            background-color: var(--color-blue);
        }

        .btn-danger {
            background-color: #e74a3b;
            color: white;
        }

        .btn-danger:hover {
            background-color: #d52a1a;
        }

        /* Mobile Actions */
        .staff-mobile-actions {
            display: none;
            margin-top: 1.5rem;
        }

        .mobile-action-row {
            display: flex;
            gap: 0.75rem;
            width: 100%;
        }

        .mobile-action-row .btn {
            flex: 1;
            text-align: center;
        }

        @media (max-width: 768px) {
            .staff-detail-hero {
                flex-direction: column;
                padding: 1.5rem;
            }

            .staff-image-section {
                flex: 0 0 auto;
                padding-right: 0;
                margin-bottom: 0.2rem;
            }

            .staff-header-actions {
                display: none;
            }

            .staff-mobile-actions {
                display: block;
            }

            .staff-meta {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }

            .meta-item {
                padding: 0.2rem 0;
            }
        }

        /* Charts Styles */
        .charts-row {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-bottom: 0.7rem;
        }

        .chart-container {
            flex: 1;
            min-width: 300px;
            position: relative;
            min-height: 300px;
            background: var(--body-background-color);
            border-radius: 14px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .chart-title {
            font-weight: 600;
            color: var(--sidenavbar-text-color);
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .chart-wrapper {
            position: relative;
            height: 300px;
            width: 100%;
        }

        @media (max-width: 768px) {
            .charts-row {
                flex-direction: column;
            }

            .chart-container {
                min-width: 100%;
            }

            /* New styles for small screens */
            .d-flex.justify-content-between.align-items-center.mb-3 {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 1rem;
            }

            .performance-indicator {
                width: 100%;
                justify-content: space-between;
            }
        }

        /* Performance-indicator... */
        .performance-indicator {
            background: var(--body-card-bg);
            padding: 0.3rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: default;
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .performance-label {
            color: var(--sidenavbar-text-color);
            opacity: 0.9;
            font-weight: 500;
        }

        .performance-value {
            font-weight: 600;
            padding: 0.25rem 0.6rem;
            border-radius: 12px;
            min-width: 80px;
            text-align: center;
            font-size: 0.8rem;
        }

        .custom-performance-tooltip {
            position: absolute;
            display: none;
            z-index: 1000;
            background-color: var(--body-background-color);
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            width: 220px;
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            border: 1px solid #e0e0e0;
            transform: translateY(5px);
            opacity: 0;
            transition: opacity 0.2s ease, transform 0.2s ease;
            pointer-events: none;
        }

        .custom-performance-tooltip.show {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        .tooltip-content {
            padding: 0;
        }

        .tooltip-header {
            padding: 12px 16px;
            border-bottom: 1px solid #f0f0f0;
        }

        .tooltip-header h4 {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            color: var(--sidenavbar-text-color);
        }

        .tooltip-body {
            background-color: var(--body-background-color);
            color: var(--sidenavbar-text-color);
            padding: 16px;
            border-radius: 8px;
        }

        .tooltip-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            align-items: center;
        }

        .tooltip-label {
            font-size: 13px;
            color: var(--sidenavbar-text-color);
        }

        .tooltip-value {
            font-size: 13px;
            font-weight: 500;
            color: var(--sidenavbar-text-color);
        }

        .tooltip-rating {
            font-size: 12px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 12px;
        }

        .tooltip-divider {
            height: 1px;
            background-color: #f0f0f0;
            margin: 16px 0;
        }

        .tooltip-scale h5 {
            margin: 0 0 12px 0;
            font-size: 13px;
            font-weight: 600;
            color: var(--sidenavbar-text-color);
        }

        .scale-row {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            gap: 10px;
        }

        .scale-range {
            font-size: 12px;
            color: var(--sidenavbar-text-color);
            width: 50px;
        }

        .scale-label {
            font-size: 12px;
            color: var(--sidenavbar-text-color);
            flex-grow: 1;
        }

        .scale-marker {
            width: 12px;
            height: 12px;
            border-radius: 3px;
        }

        /* Performance rating classes */
        .performance-excellent { background-color: #10b981; color: white; }
        .performance-good { background-color: #3b82f6; color: white; }
        .performance-fair { background-color: #f59e0b; color: white; }
        .performance-poor { background-color: #ef4444; color: white; }
        .performance-critical { background-color: #7c3aed; color: white; }
        .performance-na { background-color: #9ca3af; color: white; }

        .performance-value {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
        }

        /* Query Cards */
        .query-card {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 10px;
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 0.2rem;
            overflow: hidden;
            background: white;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        .query-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }

        .query-card .card-header {
            padding: 0.85rem 1rem;
            background: var(--body-background-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .query-card .query-id {
            font-weight: 650;
            font-size: 0.88rem;
            color: var(--sidenavbar-text-color);
            display: flex;
            align-items: center;
            letter-spacing: -0.2px;
        }

        .query-card .query-id i {
            margin-right: 8px;
            font-size: 1rem;
        }

        .query-card .badge-status {
            font-size: 0.68rem;
            padding: 0.3rem 0.6rem;
            border-radius: 6px;
            font-weight: 650;
            letter-spacing: 0.3px;
        }

        .query-card .card-body {
            padding: 1rem;
            background-color: var(--body-background-color) !important;
        }

        .query-card .meta-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.6rem;
        }

        .query-card .meta-label {
            color: #64748b;
            font-size: 0.75rem;
            font-weight: 500;
            letter-spacing: 0.2px;
        }

        .query-card .meta-value {
            font-weight: 600;
            color: var(--sidenavbar-text-color);
            font-size: 0.82rem;
        }

        .query-card .expense-value {
            color: var(--color-blue);
            font-weight: 650;
            font-size: 0.85rem;
        }

        .query-card .description {
            font-size: 0.8rem;
            color: var(--sidenavbar-text-color);
            margin-top: 0.75rem;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .query-card .date-highlight {
            background: var(--sidenavbar-body-color);
            padding: 0.5rem;
            border-radius: 6px;
            margin-bottom: 0.75rem;
        }


        /* ******************** Query Detail Model ********************* */
        #queryDetailsModal .modal {
            z-index: 1050;
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: var(--sidenavbar-body-color);
            overflow-y: auto;
            padding: 1rem;
        }

        #queryDetailsModal .modal-content {
            background-color: var(--sidenavbar-body-color);
            margin: 2rem auto;
            border-radius: 0.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            max-width: 650px;
            width: 100%;
            overflow: hidden;
            animation: modalFadeIn 0.2s ease-out;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        #queryDetailsModal .modal-header {
            padding: 1rem 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        #queryDetailsModal .modal-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0;
            color: var(--sidenavbar-text-color);
        }

        /* Location and Date/Expense Rows */
        #queryDetailsModal .location-row,
        .date-expense-row {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        #queryDetailsModal .location-item,
        .date-expense-item {
            flex: 1;
            min-width: 120px;
            padding: 0.5rem 0;
        }

        #queryDetailsModal .location-item {
            min-width: 150px;
        }

        #queryDetailsModal .meta-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--sidenavbar-text-color);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.15rem;
        }

        #queryDetailsModal .meta-value {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--sidenavbar-text-color);
        }

        #queryDetailsModal .section-title {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--sidenavbar-text-color);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        #queryDetailsModal .description-content, .remarks-content {
            background-color: var(--body-background-color);
            padding: 0.75rem;
            border-radius: 0.375rem;
            line-height: 1.5;
            color: var(--sidenavbar-text-color);
            font-size: 0.9rem;
        }

        #queryDetailsModal .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 0.5rem;
        }

        #queryDetailsModal .gallery-item img {
            width: 100%;
            height: 80px;
            object-fit: cover;
            border-radius: 0.25rem;
            transition: transform 0.2s ease;
            cursor: pointer;
        }

        #queryDetailsModal .gallery-item img:hover {
            transform: scale(1.05);
        }

        @media (max-width: 768px) {
            #queryDetailsModal .location-row,
            .date-expense-row {
                gap: 0.75rem;
            }

            #queryDetailsModal .location-item,
            .date-expense-item {
                min-width: calc(50% - 0.75rem);
            }
        }

        @media (max-width: 576px) {
            #queryDetailsModal .modal-content {
                margin: 1rem auto;
            }

            #queryDetailsModal .location-item,
            .date-expense-item {
                min-width: calc(50% - 0.75rem);
            }

            #queryDetailsModal .modal-header {
                flex-direction: row;
                align-items: center;
            }
        }
    </style>
@endpush

@section('content')

    <!-- Top Navbar -->
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
        ['url' => route('owner_manager_dashboard'), 'label' => 'Dashboard'],
        ['url' => route('owner.staff.index'), 'label' => 'Staff'],
        ['url' => '', 'label' => 'Staff Show']
    ]" />

    <!-- Side Navbar -->
    <x-Owner.side-navbar :openSections="['Staff']" />

    <!-- Error/Success Modal -->
    <x-error-success-model />
    <x-promote-to-manager />

    <div id="main">
        <section class="content mt-1 mb-5 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                            <div class="container mt-2">

                                <div class="staff-detail-container">
                                    <div class="staff-detail-hero">
                                        <!-- Left Side - Staff Image -->
                                        <div class="staff-image-section">
                                            <img src="{{ $staffInfo->user->picture ? asset($staffInfo->user->picture) : asset('img/placeholder-profile.png') }}"
                                                 alt="{{ $staffInfo->user->name }}"
                                                 class="staff-avatar">
                                        </div>

                                        <!-- Right Side - Staff Details -->
                                        <div class="staff-detail-section">
                                            <div class="staff-header">
                                                <h1 class="staff-name">{{ $staffInfo->user->name }}</h1>
                                                <div class="staff-header-actions">
                                                    <a href="{{ route('owner.staff.edit', $staffInfo->id) }}" class="btn btn-edit" title="Edit Staff">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <button type="button"
                                                            class="btn btn-danger delete-member-btn"
                                                            data-member-id="{{ $staffInfo->id }}"
                                                            title="Delete Staff Member">
                                                        <i class="fas fa-trash-alt"></i> Delete
                                                    </button>
                                                </div>
                                            </div>

                                            <p class="staff-position">
                                                {{ $staffInfo->user->role->name ?? 'Staff Member' }} • {{ $staffInfo->department->name ?? 'No Department' }}
                                            </p>

                                            <div class="staff-meta">
                                                <div class="meta-item">
                                                    <i class="fas fa-envelope meta-icon"></i>
                                                    <a href="mailto:{{ $staffInfo->user->email }}" class="text-decoration-none">{{ $staffInfo->user->email }}</a>
                                                </div>
                                                <div class="meta-item">
                                                    <i class="fas fa-phone meta-icon"></i>
                                                    <span>{{ $staffInfo->user->phone_no ?? 'Not provided' }}</span>
                                                </div>
                                                <div class="meta-item">
                                                    <i class="fas fa-building meta-icon"></i>
                                                    <span>{{ $staffInfo->building ? $staffInfo->building->name : 'Building not assigned' }}</span>
                                                </div>
                                                <div class="meta-item">
                                                    <i class="fas fa-calendar-alt meta-icon"></i>
                                                    <span>Member since {{ $staffInfo->created_at->format('M Y') }}</span>
                                                </div>
                                                <div class="meta-item">
                                                    <i class="fas fa-award meta-icon"></i>
                                                    <div class="query-handling-toggle">
                                                        <span class="toggle-label">Handle Queries</span>
                                                        <label class="enable-query-toggle-btn">
                                                            <input type="checkbox" class="enable-query-btn" data-staff-id="{{ $staffInfo->id }}" {{ $staffInfo->accept_queries ? 'checked' : '' }}>
                                                            <span class="toggle-slider round"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="meta-item promote-item">
                                                    <i class="fas fa-user-shield meta-icon pt-2"></i>
                                                    <button class="btn btn-primary promote-btn w-100" data-staff-id="{{ $staffInfo->id }}">Promote to Manager</button>
                                                </div>
                                            </div>

                                            <!-- Mobile Actions -->
                                            <div class="staff-mobile-actions">
                                                <div class="mobile-action-row">
                                                    <a href="{{ route('owner.staff.edit', $staffInfo->id) }}" class="btn btn-edit" title="Edit Staff">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <button type="button"
                                                            class="btn btn-danger delete-member-btn"
                                                            data-member-id="{{ $staffInfo->id }}"
                                                            title="Delete Staff Member">
                                                        <i class="fas fa-trash-alt"></i> Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Charts Section -->
                                <div class="chart-card p-4 mt-3 mb-3">
                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
                                        <h5 class="section-title mb-3 mb-md-0">Queries Overview</h5>
                                        <div class="d-flex flex-wrap gap-2">
                                            <select class="form-select" id="yearSelect">
                                                @for($i = date('Y'); $i >= 2020; $i--)
                                                    <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                            <select class="form-select" id="monthSelect">
                                                <option value="">All Months</option>
                                                @foreach([
                                                    '01' => 'January', '02' => 'February', '03' => 'March',
                                                    '04' => 'April', '05' => 'May', '06' => 'June',
                                                    '07' => 'July', '08' => 'August', '09' => 'September',
                                                    '10' => 'October', '11' => 'November', '12' => 'December'
                                                ] as $num => $name)
                                                    <option value="{{ $num }}" {{ $num == date('m') ? 'selected' : '' }}>{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="charts-row">
                                        <!-- Pie Chart Container -->
                                        <div class="chart-container">
                                            <div class="chart-header d-flex justify-content-between align-items-center mb-3">
                                                <div class="chart-title">Query Status Distribution</div>
                                                <div id="performanceIndicator" class="performance-indicator">
                                                    <span class="performance-label">Performance:</span>
                                                    <span class="performance-value badge bg-secondary">Calculating...</span>
                                                </div>
                                            </div>
                                            <div class="chart-wrapper">
                                                <canvas id="pieChart"></canvas>
                                            </div>
                                        </div>

                                        <!-- Line Chart Container -->
                                        <div class="chart-container">
                                            <div class="chart-title">Monthly Query Trends</div>
                                            <div class="chart-wrapper">
                                                <canvas id="lineChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Filter Section -->
                                <form method="GET" action="{{ route('owner.staff.show', $staffInfo->id) }}">
                                    <div class="filter-section mb-3">
                                        <h5 class="section-title mb-4">Query Filters</h5>
                                        <div class="row g-3 align-items-end">
                                            <!-- Start Date -->
                                            <div class="col-md-3">
                                                <label class="form-label">Start Date</label>
                                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                                            </div>

                                            <!-- End Date -->
                                            <div class="col-md-3">
                                                <label class="form-label">End Date</label>
                                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                                            </div>

                                            <!-- Status -->
                                            <div class="col-md-3">
                                                <label class="form-label">Status</label>
                                                <select name="status" class="form-select">
                                                    <option value="">All Statuses</option>
                                                    <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                                    <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
                                                    <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                                    <option value="Closed Late" {{ request('status') == 'Closed Late' ? 'selected' : '' }}>Closed Late</option>
                                                </select>
                                            </div>

                                            <!-- Unit -->
                                            <div class="col-md-3">
                                                <label class="form-label">Unit</label>
                                                <select name="unit" class="form-select" {{ $units->isEmpty() ? 'disabled' : '' }}>
                                                    @if($units->isEmpty())
                                                        <option selected disabled>No units available</option>
                                                    @else
                                                        <option value="">All Units</option>
                                                        @foreach($units as $unit)
                                                            <option value="{{ $unit->id }}" {{ request('unit') == $unit->id ? 'selected' : '' }}>
                                                                {{ $unit->unit_name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>

                                            <!-- Min Expense -->
                                            <div class="col-md-3">
                                                <label class="form-label">Min Expense (PKR)</label>
                                                <input type="number" name="min_expense" class="form-control" placeholder="0" value="{{ request('min_expense') }}">
                                            </div>

                                            <!-- Max Expense -->
                                            <div class="col-md-3">
                                                <label class="form-label">Max Expense (PKR)</label>
                                                <input type="number" name="max_expense" class="form-control" placeholder="100000" value="{{ request('max_expense') }}">
                                            </div>

                                            <!-- Reset Button -->
                                            <div class="col-md-3">
                                                <a href="{{ route('owner.staff.show', $staffInfo->id) }}" class="btn btn-secondary w-100 d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-undo me-2"></i> Reset
                                                </a>
                                            </div>

                                            <!-- Apply Filter Button -->
                                            <div class="col-md-3">
                                                <button type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-filter me-2"></i> Apply Filters
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <div class="chart-card p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h5 class="section-title mb-0" style="font-size: 1.15rem; font-weight: 650;">Recent Queries</h5>
                                    </div>

                                    @if(count($queries) > 0)
                                        <div class="row">
                                            @foreach($queries as $query)
                                                @php
                                                    // Default status configuration
                                                    $statusConfig = [
                                                        'color' => 'secondary',
                                                        'icon' => 'question-circle'
                                                    ];

                                                    // Override defaults based on status
                                                    switch($query->status) {
                                                        case 'Open':
                                                            $statusConfig = ['color' => 'primary', 'icon' => 'circle-notch'];
                                                            break;
                                                        case 'Closed':
                                                            $statusConfig = ['color' => 'success', 'icon' => 'check-circle'];
                                                            break;
                                                        case 'In Progress':
                                                            $statusConfig = ['color' => 'primary', 'icon' => 'spinner fa-spin'];
                                                            break;
                                                        case 'Rejected':
                                                            $statusConfig = ['color' => 'danger', 'icon' => 'times-circle'];
                                                            break;
                                                        case 'Pending':
                                                            $statusConfig = ['color' => 'warning', 'icon' => 'clock'];
                                                            break;
                                                        case 'Closed Late':
                                                            $statusConfig = ['color' => 'success', 'icon' => 'calendar-check'];
                                                            break;
                                                    }

                                                    // Handle closure dates
                                                    $closedDate = $query->status == 'Closed' ? ($closure_date ?? $query->updated_at) : null;
                                                    $expectedDate = $query->status == 'In Progress' ? ($expected_closure_date ?? null) : null;
                                                @endphp

                                                <div class="col-lg-4 col-md-6 mb-4">
                                                    <div class="card query-card h-100" onclick="showQueryDetails({{ $query->id }}, `{{ addslashes($query->building->name ?? 'N/A') }}`, `{{ addslashes($query->department->name ?? 'N/A') }}`)" style="cursor: pointer;">
                                                        <div class="card-header border-bottom">
                                                            <div class="query-id">
                                                                <i class="fas fa-{{ $statusConfig['icon'] }} text-{{ $statusConfig['color'] }}"></i>
                                                                QR-{{ str_pad($query->id, 5, '0', STR_PAD_LEFT) }}
                                                            </div>
                                                            <span class="badge-status badge-{{ $statusConfig['color'] }}">
                                                                {{ $query->status }}
                                                            </span>
                                                        </div>

                                                        <div class="card-body">
                                                            <div class="meta-row">
                                                                <div>
                                                                    <span class="meta-label">Building</span>
                                                                    <span class="meta-value">{{ $query->building->name ?? 'N/A' }}</span>
                                                                </div>
                                                                <div>
                                                                    <span class="meta-label">Department</span>
                                                                    <span class="meta-value">{{ $query->department->name ?? 'N/A' }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="meta-row">
                                                                <div>
                                                                    <span class="meta-label">Unit</span>
                                                                    <span class="meta-value">{{ $query->unit->unit_name ?? 'N/A' }}</span>
                                                                </div>
                                                                <div>
                                                                    <span class="meta-label">Expense</span>
                                                                    <span class="meta-value expense-value">${{ number_format($query->expense, 2) }}</span>
                                                                </div>
                                                            </div>

                                                            <div class="description mb-3">
                                                                {{ $query->description ? Str::limit($query->description, 55) : 'N/A' }}
                                                            </div>

                                                            <div class="date-highlight">
                                                                <div class="meta-row">
                                                                    <div>
                                                                        <span class="meta-label">Opened</span>
                                                                        <span class="meta-value">{{ $query->created_at->format('M d, Y') }}</span>
                                                                    </div>
                                                                    <div>
                                                                        @if($query->status == 'Closed' && ($query->closure_date || $query->expected_closure_date))
                                                                            <span class="meta-label">Closed</span>
                                                                            <span class="meta-value">
                                                                                @if($query->closure_date)
                                                                                    {{ $query->closure_date->format('M d, Y') }}
                                                                                @else
                                                                                    {{ $query->expected_closure_date->format('M d, Y') }}
                                                                                @endif
                                                                            </span>
                                                                        @elseif($query->status == 'In Progress' && $query->expected_closure_date)
                                                                            <span class="meta-label">Expected</span>
                                                                            <span class="meta-value">{{ $query->expected_closure_date->format('M d, Y') }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        @if ($queries->hasPages())
                                            <div class="mt-3">
                                                {{ $queries->links('pagination::bootstrap-5') }}
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-center py-3">
                                            <i class="fas fa-exchange-alt text-muted" style="font-size: 1.75rem; opacity: 0.7;"></i>
                                            <p class="text-muted mb-0 mt-2" style="font-size: 0.9rem; font-weight: 500;">No queries found</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>



    <div id="queryDetailsModal" class="modal" style="display: none;">
        <div class="modal-content">
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script>
        // Handle Accept Queries
        document.addEventListener('DOMContentLoaded', function() {
            document.body.addEventListener('change', function(e) {
                if (e.target.classList.contains('enable-query-btn')) {
                    const button = e.target;
                    const staffId = button.dataset.staffId;
                    const isChecked = button.checked ? 1 : 0;

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                    const queryUrl = "{{ route('owner.staff.handle.queries') }}";
                    const originalHTML = button.nextElementSibling.innerHTML;
                    button.nextElementSibling.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    button.disabled = true;

                    fetch(queryUrl, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            id: staffId,
                            accept_query: isChecked
                        })
                    })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => { throw err; });
                            }
                            return response.json();
                        })
                        .then(data => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: data.success || 'Status updated successfully',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: error.error || 'Something went wrong. Please try again.',
                                timer: 2000,
                                showConfirmButton: true
                            });
                            // Revert the checkbox state
                            button.checked = !button.checked;
                        })
                        .finally(() => {
                            button.nextElementSibling.innerHTML = originalHTML;
                            button.disabled = false;
                        });
                }
            });
        });

        //Charts
        document.addEventListener('DOMContentLoaded', function() {
            const staffId = "{{ $staffInfo->id }}";
            const yearlyStatsRoute = "{{ route('owner.staff.queries.yearly', ['staff' => ':staffId']) }}".replace(':staffId', staffId);
            const monthlyStatsRoute = "{{ route('owner.staff.queries.monthly', ['staff' => ':staffId']) }}".replace(':staffId', staffId);

            let pieChart, lineChart;
            const yearSelect = document.getElementById('yearSelect');
            const monthSelect = document.getElementById('monthSelect');

            // Initialize Bootstrap tooltip
            const performanceTooltip = new bootstrap.Tooltip(document.getElementById('performanceIndicator'), {
                html: true,
                placement: 'left'
            });

            // Function to calculate performance
            function calculatePerformance(data) {
                console.log("Input data:", data);

                if (!data || !data.total_queries || data.total_queries === 0) {
                    return null;
                }

                // Map input status names to your expected format
                const statusMap = {
                    'Open': 'open',
                    'In Progress': 'in_progress',
                    'Closed': 'closed',
                    'Closed Late': 'closed_late',
                    'Rejected': 'rejected'
                };

                const weights = {
                    open: 0,
                    in_progress: 1,
                    closed: 2,
                    closed_late: 1,
                    rejected: -1
                };

                let actualScore = 0;
                Object.keys(weights).forEach(status => {
                    // Find the corresponding input data key
                    const inputKey = Object.keys(statusMap).find(key => statusMap[key] === status);
                    const count = inputKey ? parseInt(data[inputKey] || 0) : 0;
                    const weight = weights[status];
                    const score = count * weight;
                    actualScore += score;
                });

                const maxScore = data.total_queries * 2;

                const normalizedPercentage = Math.round((actualScore / maxScore) * 100);
                const finalScore = Math.max(0, Math.min(100, normalizedPercentage));
                return finalScore;
            }


            // Function to update performance indicator
            function updatePerformanceIndicator(percentage) {
                const indicator = document.getElementById('performanceIndicator');
                const valueElement = indicator.querySelector('.performance-value');

                // Create tooltip element if it doesn't exist
                let tooltip = document.querySelector('.custom-performance-tooltip');
                if (!tooltip) {
                    tooltip = document.createElement('div');
                    tooltip.className = 'custom-performance-tooltip';
                    document.body.appendChild(tooltip);

                    // Add event listeners for showing/hiding tooltip
                    let tooltipTimeout;
                    let isMouseInTooltip = false;

                    indicator.addEventListener('mouseenter', () => {
                        clearTimeout(tooltipTimeout);
                        positionTooltip(indicator, tooltip);
                        tooltip.style.display = 'block';
                        setTimeout(() => tooltip.classList.add('show'), 10);
                    });

                    indicator.addEventListener('mouseleave', () => {
                        // Only hide if mouse isn't in tooltip
                        if (!isMouseInTooltip) {
                            tooltipTimeout = setTimeout(() => {
                                tooltip.classList.remove('show');
                                setTimeout(() => tooltip.style.display = 'none', 200);
                            }, 100);
                        }
                    });

                    // Track when mouse enters tooltip
                    tooltip.addEventListener('mouseenter', () => {
                        isMouseInTooltip = true;
                        clearTimeout(tooltipTimeout);
                        tooltip.classList.add('show');
                        tooltip.style.display = 'block';
                    });

                    // Track when mouse leaves tooltip
                    tooltip.addEventListener('mouseleave', () => {
                        isMouseInTooltip = false;
                        tooltip.classList.remove('show');
                        setTimeout(() => tooltip.style.display = 'none', 200);
                    });
                }

                function positionTooltip(element, tooltip) {
                    const rect = element.getBoundingClientRect();
                    tooltip.style.left = `${rect.left + window.scrollX}px`;
                    tooltip.style.top = `${rect.bottom + window.scrollY + 5}px`;
                }

                if (percentage === null) {
                    valueElement.textContent = 'N/A';
                    valueElement.className = 'performance-value performance-na';
                    tooltip.innerHTML = `
            <div class="tooltip-content">
                <div class="tooltip-header">
                    <h4>Performance Data</h4>
                </div>
                <div class="tooltip-body">
                    <p>Not enough data to calculate performance</p>
                </div>
            </div>
        `;
                    return;
                }

                let rating, className;
                if (percentage >= 80) {
                    rating = 'Excellent';
                    className = 'performance-excellent';
                } else if (percentage >= 60) {
                    rating = 'Good';
                    className = 'performance-good';
                } else if (percentage >= 40) {
                    rating = 'Fair';
                    className = 'performance-fair';
                } else if (percentage >= 20) {
                    rating = 'Poor';
                    className = 'performance-poor';
                } else {
                    rating = 'Critical';
                    className = 'performance-critical';
                }

                valueElement.textContent = `${percentage}% (${rating})`;
                valueElement.className = `performance-value ${className}`;

                tooltip.innerHTML = `
        <div class="tooltip-content">
            <div class="tooltip-header">
                <h4>Performance Details</h4>
            </div>
            <div class="tooltip-body">
                <div class="tooltip-row">
                    <span class="tooltip-label">Score:</span>
                    <span class="tooltip-value">${percentage}%</span>
                </div>
                <div class="tooltip-row">
                    <span class="tooltip-label">Rating:</span>
                    <span class="tooltip-rating ${className}">${rating}</span>
                </div>

                <div class="tooltip-divider"></div>

                <div class="tooltip-scale">
                    <h5>Performance Scale</h5>
                    <div class="scale-row">
                        <span class="scale-range">80-100%</span>
                        <span class="scale-label">Excellent</span>
                        <span class="scale-marker performance-excellent"></span>
                    </div>
                    <div class="scale-row">
                        <span class="scale-range">60-79%</span>
                        <span class="scale-label">Good</span>
                        <span class="scale-marker performance-good"></span>
                    </div>
                    <div class="scale-row">
                        <span class="scale-range">40-59%</span>
                        <span class="scale-label">Fair</span>
                        <span class="scale-marker performance-fair"></span>
                    </div>
                    <div class="scale-row">
                        <span class="scale-range">20-39%</span>
                        <span class="scale-label">Poor</span>
                        <span class="scale-marker performance-poor"></span>
                    </div>
                    <div class="scale-row">
                        <span class="scale-range">0-19%</span>
                        <span class="scale-label">Critical</span>
                        <span class="scale-marker performance-critical"></span>
                    </div>
                </div>
            </div>
        </div>
    `;
            }

            // Initialize charts
            function initCharts() {
                const pieCtx = document.getElementById('pieChart').getContext('2d');
                const lineCtx = document.getElementById('lineChart').getContext('2d');

                pieChart = new Chart(pieCtx, {
                    type: 'pie',
                    data: {
                        labels: [],
                        datasets: [{
                            data: [],
                            backgroundColor: [
                                '#3B82F6', '#10B981', '#F59E0B', '#EF4444',
                                '#8B5CF6', '#EC4899', '#14B8A6', '#F97316'
                            ],
                            borderWidth: 2,
                            borderColor: '#fff',
                            hoverOffset: 15
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    font: {
                                        size: 12,
                                        family: "'Inter', sans-serif"
                                    },
                                    color: getComputedStyle(document.documentElement).getPropertyValue('--sidenavbar-text-color').trim(),
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = Math.round((value / total) * 100);
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                },
                                displayColors: true,
                                backgroundColor: '#1E293B',
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 12
                                },
                                padding: 12,
                                cornerRadius: 8
                            },
                            datalabels: {
                                formatter: (value, ctx) => {
                                    const total = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${value}\n(${percentage}%)`;
                                },
                                color: '#fff',
                                font: {
                                    weight: 'bold',
                                    size: 12,
                                    family: "'Inter', sans-serif"
                                },
                                textAlign: 'center',
                                anchor: 'center',
                                offset: 0,
                                clip: false,
                                display: function(context) {
                                    const dataset = context.dataset;
                                    const value = dataset.data[context.dataIndex];
                                    return value > 0;
                                }
                            }
                        },
                        cutout: '50%',
                        animation: {
                            animateScale: true,
                            animateRotate: true
                        },
                        layout: {
                            padding: {
                                top: 20,
                                bottom: 20,
                                left: 20,
                                right: 20
                            }
                        }
                    },
                    plugins: [ChartDataLabels]
                });

                lineChart = new Chart(lineCtx, {
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: []
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 12
                                    },
                                    maxRotation: 45,
                                    minRotation: 45,
                                    color: getComputedStyle(document.documentElement).getPropertyValue('--sidenavbar-text-color').trim()
                                },
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    font: {
                                        size: 12
                                    },
                                    color: getComputedStyle(document.documentElement).getPropertyValue('--sidenavbar-text-color').trim(),
                                    callback: function(value) {
                                        if (Number.isInteger(value)) {
                                            return value;
                                        }
                                    }
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true,
                                    font: {
                                        size: 12
                                    },
                                    boxWidth: 12,
                                    color: getComputedStyle(document.documentElement).getPropertyValue('--sidenavbar-text-color').trim(),
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `${context.dataset.label}: ${context.raw}`;
                                    },
                                    title: function(context) {
                                        return `Month: ${context[0].label}`;
                                    }
                                },
                                displayColors: true,
                                backgroundColor: '#1E293B',
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 12
                                },
                                padding: 12,
                                usePointStyle: true
                            }
                        },
                        elements: {
                            point: {
                                radius: 4,
                                hoverRadius: 6
                            },
                            line: {
                                tension: 0.3,
                                borderWidth: 2
                            }
                        }
                    }
                });
            }

            // Fetch data for pie chart only
            function fetchPieChartData() {
                const year = yearSelect.value;
                const month = monthSelect.value;

                fetch(`${yearlyStatsRoute}?year=${year}${month ? `&month=${month}` : ''}`)
                    .then(response => response.json())
                    .then(data => {
                        updatePieChart(data.data);
                    })
                    .catch(error => {
                        console.error('Error fetching yearly stats:', error);
                    });
            }

            // Fetch data for line chart only
            function fetchLineChartData() {
                const year = yearSelect.value;

                fetch(`${monthlyStatsRoute}?year=${year}`)
                    .then(response => response.json())
                    .then(data => {
                        updateLineChart(data.monthly);
                    })
                    .catch(error => {
                        console.error('Error fetching monthly stats:', error);
                    });
            }

            // Update pie chart with new data
            function updatePieChart(data) {
                if (!data || Object.keys(data).length === 0) {
                    pieChart.data.labels = ['No Data Available'];
                    pieChart.data.datasets[0].data = [1];
                    pieChart.data.datasets[0].backgroundColor = ['#E5E7EB'];
                    pieChart.update();
                    updatePerformanceIndicator(null);
                    return;
                }

                const labels = [];
                const values = [];

                Object.keys(data).forEach(key => {
                    if (key !== 'total_queries' && data[key] > 0) {
                        labels.push(key.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' '));
                        values.push(data[key]);
                    }
                });

                if (labels.length === 0) {
                    pieChart.data.labels = ['No Data Available'];
                    pieChart.data.datasets[0].data = [1];
                    pieChart.data.datasets[0].backgroundColor = ['#E5E7EB'];
                } else {
                    pieChart.data.labels = labels;
                    pieChart.data.datasets[0].data = values;
                    pieChart.data.datasets[0].backgroundColor = [
                        '#3B82F6', '#10B981', '#F59E0B', '#EF4444',
                        '#8B5CF6', '#EC4899', '#14B8A6', '#F97316'
                    ];
                }

                pieChart.update();

                // Calculate and update performance
                const performancePercentage = calculatePerformance(data);
                updatePerformanceIndicator(performancePercentage);
            }

            // Update line chart with new data
            function updateLineChart(data) {
                if (!data || Object.keys(data).length === 0) {
                    lineChart.data.labels = ['No Data'];
                    lineChart.data.datasets = [{
                        label: 'Queries',
                        data: [0],
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.3
                    }];
                    lineChart.update();
                    return;
                }

                const months = Object.keys(data);
                const statuses = new Set();

                months.forEach(month => {
                    Object.keys(data[month]).forEach(key => {
                        if (key !== 'total_queries') {
                            statuses.add(key);
                        }
                    });
                });

                const datasets = [];
                const colors = [
                    '#3B82F6', '#10B981', '#F59E0B', '#EF4444',
                    '#8B5CF6', '#EC4899', '#14B8A6', '#F97316'
                ];

                let colorIndex = 0;
                statuses.forEach(status => {
                    const statusData = months.map(month => data[month][status] || 0);

                    datasets.push({
                        label: status.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' '),
                        data: statusData,
                        borderColor: colors[colorIndex % colors.length],
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.3,
                        fill: false,
                        pointBackgroundColor: colors[colorIndex % colors.length],
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: colors[colorIndex % colors.length]
                    });

                    colorIndex++;
                });

                datasets.push({
                    label: 'Total Queries',
                    data: months.map(month => data[month].total_queries),
                    borderColor: '#000000',
                    backgroundColor: 'rgba(0, 0, 0, 0.1)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    tension: 0.3,
                    fill: false,
                    pointBackgroundColor: '#000',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#000'
                });

                lineChart.data.labels = months;
                lineChart.data.datasets = datasets;
                lineChart.update();
            }

            // Initialize charts and fetch initial data
            initCharts();
            fetchPieChartData();
            fetchLineChartData();

            // Add event listeners for filters
            yearSelect.addEventListener('change', function() {
                fetchPieChartData();
                fetchLineChartData();
            });

            monthSelect.addEventListener('change', function() {
                fetchPieChartData();
            });
        });
    </script>

     <script>
         // Function to handle card clicks and fetch query details
         function showQueryDetails(queryId, buildingName, departmentName) {
             const modal = document.getElementById('queryDetailsModal');
             const modalContent = modal.querySelector('.modal-content');

             modal.style.display = 'flex';
             document.body.classList.add('body-no-scroll');

             // Store building name in modal for later use
             modal.dataset.buildingName = buildingName;
             modal.dataset.departmentName = departmentName;

             // Fetch query details using named route
             fetch(`{{ route('owner.staff.query.details', ':id') }}`.replace(':id', queryId), {
                 headers: {
                     'Accept': 'application/json',
                     'X-Requested-With': 'XMLHttpRequest'
                 }
             })
                 .then(response => {
                     if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                     return response.json();
                 })
                 .then(data => {
                     if (data.error) throw new Error(data.error);
                     renderQueryDetails(data.query);
                 })
                 .catch(error => {
                     console.error('Error fetching query details:', error);
                     modalContent.innerHTML = `
                        <div class="modal-error">
                            <i class="fas fa-exclamation-circle text-danger fa-3x mb-3"></i>
                            <h5>Error Loading Query</h5>
                            <p>${error.message || 'Failed to load query details'}</p>
                            <button class="btn btn-sm btn-secondary mt-3" onclick="closeModal()">Close</button>
                        </div>
                    `;
                 });
         }

         // Helper function to render query details in modal
         function renderQueryDetails(query) {
             const modal = document.getElementById('queryDetailsModal');
             const modalContent = modal.querySelector('.modal-content');

             // Get building name from modal data or fallback to query data
             const buildingName = modal.dataset.buildingName || query.building?.name || 'N/A';
             const departmentName = modal.dataset.departmentName || query.department?.name || 'N/A';

             // Format dates
             const createdDate = new Date(query.created_at).toLocaleDateString('en-US', {
                 year: 'numeric', month: 'short', day: 'numeric'
             });

             const expectedDate = query.expected_closure_date
                 ? new Date(query.expected_closure_date).toLocaleDateString('en-US', {
                     year: 'numeric', month: 'short', day: 'numeric'
                 })
                 : 'Not specified';

             const closedDate = query.closure_date
                 ? new Date(query.closure_date).toLocaleDateString('en-US', {
                     year: 'numeric', month: 'short', day: 'numeric'
                 })
                 : null;

             // Status configuration
             const statusConfig = {
                 'Open': { color: 'primary', icon: 'check-circle' },
                 'Closed': { color: 'success', icon: 'check-circle' },
                 'In Progress': { color: 'primary', icon: 'spinner fa-spin' },
                 'Rejected': { color: 'danger', icon: 'times-circle' },
                 'Pending': { color: 'warning', icon: 'clock' },
                 'Closed Late': { color: 'success', icon: 'check' }
             }[query.status] || { color: 'secondary', icon: 'question-circle' };

             // Build pictures gallery if available
             const picturesHTML = query.pictures && query.pictures.length > 0
                 ? `
            <div class="query-gallery mt-3">
                <h6 class="section-title">Attachments</h6>
                <div class="gallery-grid">
                    ${query.pictures.map(pic => `
                        <div class="gallery-item">
                            <img src="${pic.file_path ? "{{ asset('/') }}" + pic.file_path : "{{ asset('assets/placeholder-profile.png') }}"}"
                                 alt="Query attachment"
                                 class="img-thumbnail"
                                 style="max-height: 150px; object-fit: contain;">
                        </div>
                    `).join('')}
                </div>
            </div>
        `
                 : '<p class="text-muted small">No attachments available</p>';

             // Set modal content
             modalContent.innerHTML = `
        <div class="modal-header border-bottom">
            <div class="d-flex align-items-center gap-2 flex-grow-1">
                <i class="fas fa-${statusConfig.icon} text-${statusConfig.color}"></i>
                <h5 class="modal-title mb-0">QR-${String(query.id).padStart(5, '0')}</h5>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-${statusConfig.color}">${query.status}</span>
<!--                <button type="button" class="btn-close" onclick="closeModal()"></button>-->
            </div>
        </div>
        <div class="modal-body">
            <div class="location-row mb-3">
                <div class="location-item">
                    <span class="meta-label">Building</span>
                    <span class="meta-value">${buildingName}</span>
                </div>
                <div class="location-item">
                    <span class="meta-label">Department</span>
                    <span class="meta-value">${departmentName}</span>
                </div>
                <div class="location-item">
                    <span class="meta-label">Unit</span>
                    <span class="meta-value">${query.unit?.unit_name || 'N/A'}</span>
                </div>
            </div>

            <div class="date-expense-row mb-3">
                <div class="date-expense-item">
                    <span class="meta-label">Opened</span>
                    <span class="meta-value">${createdDate}</span>
                </div>
                <div class="date-expense-item">
                    <span class="meta-label">${closedDate ? 'Closed' : 'Expected'}</span>
                    <span class="meta-value">${closedDate || expectedDate}</span>
                </div>
                <div class="date-expense-item">
                    <span class="meta-label">Expense</span>
                    <span class="meta-value">$${(parseFloat(query.expense) || 0).toFixed(2)}</span>
                </div>
            </div>

            <div class="query-description mb-3">
                <h6 class="section-title">Description</h6>
                <div class="description-content">${query.description || 'No description provided'}</div>
            </div>

            ${query.remarks ? `
            <div class="query-remarks mb-3">
                <h6 class="section-title">Remarks</h6>
                <div class="remarks-content">${query.remarks}</div>
            </div>
            ` : ''}

            ${picturesHTML}
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal()">Close</button>
        </div>
    `;
         }

         // Function to close modal
         function closeModal() {
             const modal = document.getElementById('queryDetailsModal');
             if (modal) {
                 modal.style.display = 'none';
                 document.body.classList.remove('body-no-scroll');
             }
         }

         // Close modal when clicking outside content
         document.getElementById('queryDetailsModal').addEventListener('click', function(e) {
             if (e.target === this) {
                 closeModal();
             }
         });
     </script>
@endpush
