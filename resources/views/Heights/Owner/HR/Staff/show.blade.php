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

        /* staff detail*/

        /* end*/

        .finance-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
        }

        .finance-header h3 {
            font-weight: 700;
            color: var(--sidenavbar-text-color);
            font-size: 1.75rem;
            margin-bottom: 0.25rem;
        }

        .finance-header p {
            color: var(--sidenavbar-text-color);
            font-size: 0.95rem;
        }

        .finance-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            background: var(--sidenavbar-body-color);
            overflow: hidden;
        }

        .finance-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .summary-card {
            background: var(--body-card-bg);
            border-radius: 14px;
            padding: 1.75rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .summary-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        .summary-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(to bottom, #3b82f6, var(--color-blue));
        }

        .summary-card h5 {
            font-size: 0.875rem;
            color: var(--sidenavbar-text-color);
            margin-bottom: 0.75rem;
            font-weight: 500;
            letter-spacing: 0.2px;
        }

        .summary-card .amount {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--sidenavbar-text-color);
            margin-bottom: 0.5rem;
        }

        .summary-card .trend {
            display: flex;
            align-items: center;
            font-size: 0.8rem;
            color: #64748b;
        }

        .positive {
            color: #10b981 !important;
        }

        .negative {
            color: #ef4444 !important;
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

        .custom-pagination-wrapper {
            justify-content: center;
        }

        .empty-state {
            text-align: center;
            padding: 4rem;
            color: var(--sidenavbar-text-color) !important;
            background:  var(--sidenavbar-body-color) !important;
            border-radius: 14px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            color: #e2e8f0;
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

        .btn-outline-primary {
            color: var(--color-blue);
            border-color: var(--color-blue);
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background-color: var(--color-blue);
            border-color: var(--color-blue);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
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
            .finance-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .summary-cards {
                grid-template-columns: 1fr 1fr;
            }
        }

        .chart-container {
            position: relative;
            width: 100%;
            min-height: 300px;
            margin-bottom: 2rem;
        }

        /* Ensure the canvas fills its container */
        #financialChart {
            width: 100% !important;
            height: 100% !important;
            min-height: 300px;
            max-height: 300px;
        }

        /* For smaller screens */
        @media (max-width: 768px) {
            .chart-container {
                min-height: 250px;
            }
            #financialChart {
                width: 100% !important;
                height: 100% !important;
                min-height: 350px;
                max-height: 350px;
            }
        }

        .days-select {
            width: 100%;
            margin-top: 0.5rem;
        }

        /* Medium screens and up: Fixed width (25%) */
        @media (min-width: 768px) {
            .days-select {
                width: 25%;
                margin-top: 0;
            }
        }
    </style>
    <style>
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

        .promote-item .btn-promote {
            flex: 1;
            text-align: left;
            padding: 8px 12px;
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

        .btn-promote {
            background-color: #1cc88a;
            color: white;
        }

        .btn-promote:hover {
            background-color: #17a673;
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

            .promote-item .btn-promote {
                width: 100%;
            }
        }
    </style>

    <style>
        /* Add these new styles for the charts layout */
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
        }

        /* Your existing styles... */
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
                                                {{ $staffInfo->position ?? 'Staff Member' }} • {{ $staffInfo->department->name ?? 'No Department' }}
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
                                                            <input type="checkbox" {{ $staffInfo->accept_queries ? 'checked' : '' }}>
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
                                <div class="finance-card p-4 mt-3 mb-3">
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
                                            <div class="chart-title">Query Status Distribution</div>
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

                                <style>
                                    .query-card {
                                        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
                                        border-radius: 10px;
                                        border: none;
                                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                                        margin-bottom: 1.25rem;
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
                                        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
                                        background: white;
                                        display: flex;
                                        justify-content: space-between;
                                        align-items: center;
                                    }

                                    .query-card .query-id {
                                        font-weight: 650;
                                        font-size: 0.88rem;
                                        color: #1a1a1a;
                                        display: flex;
                                        align-items: center;
                                        letter-spacing: -0.2px;
                                    }

                                    .query-card .query-id i {
                                        margin-right: 8px;
                                        font-size: 1rem;
                                    }

                                    .badge-status {
                                        font-size: 0.68rem;
                                        padding: 0.3rem 0.6rem;
                                        border-radius: 6px;
                                        font-weight: 650;
                                        letter-spacing: 0.3px;
                                    }

                                    .query-card .card-body {
                                        padding: 1rem;
                                    }

                                    .meta-row {
                                        display: flex;
                                        justify-content: space-between;
                                        margin-bottom: 0.6rem;
                                    }

                                    .meta-label {
                                        color: #64748b;
                                        font-size: 0.75rem;
                                        font-weight: 500;
                                        letter-spacing: 0.2px;
                                    }

                                    .meta-value {
                                        font-weight: 600;
                                        color: #1e293b;
                                        font-size: 0.82rem;
                                    }

                                    .expense-value {
                                        color: #2563eb;
                                        font-weight: 650;
                                        font-size: 0.85rem;
                                    }

                                    .description {
                                        font-size: 0.8rem;
                                        color: #334155;
                                        margin-top: 0.75rem;
                                        line-height: 1.5;
                                        display: -webkit-box;
                                        -webkit-line-clamp: 2;
                                        -webkit-box-orient: vertical;
                                        overflow: hidden;
                                    }

                                    .card-footer {
                                        padding: 0.75rem 1rem;
                                        background: #f8fafc;
                                        border-top: 1px solid rgba(0, 0, 0, 0.05);
                                    }

                                    .btn-start {
                                        font-size: 0.72rem;
                                        padding: 0.35rem 0.7rem;
                                        border-radius: 6px;
                                        font-weight: 600;
                                        letter-spacing: 0.2px;
                                    }

                                    .date-highlight {
                                        background: #f1f5f9;
                                        padding: 0.5rem;
                                        border-radius: 6px;
                                        margin-bottom: 0.75rem;
                                    }
                                </style>

                                <div class="finance-card p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h5 class="section-title mb-0" style="font-size: 1.15rem; font-weight: 650; color: #1e293b;">Recent Queries</h5>
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
                                                    }

                                                    // Handle closure dates
                                                    $closedDate = $query->status == 'Closed' ? ($closure_date ?? $query->updated_at) : null;
                                                    $expectedDate = $query->status == 'In Progress' ? ($expected_closure_date ?? null) : null;
                                                @endphp

                                                <div class="col-lg-4 col-md-6 mb-4">
                                                    <div class="card query-card h-100" onclick="showQueryDetails({{ $query->id }})" style="cursor: pointer;">
                                                        <div class="card-header">
                                                            <div class="query-id">
                                                                <i class="fas fa-{{ $statusConfig['icon'] }} text-{{ $statusConfig['color'] }}"></i>
                                                                QR-{{ str_pad($query->id, 5, '0', STR_PAD_LEFT) }}
                                                            </div>
                                                            <span class="badge-status badge-{{ $statusConfig['color'] }}">
                                {{ $query->status }}
                            </span>
                                                        </div>

                                                        <div class="card-body">
                                                            <div class="date-highlight">
                                                                <div class="meta-row">
                                                                    <div>
                                                                        <span class="meta-label">Opened</span>
                                                                        <span class="meta-value">{{ $query->created_at->format('M d, Y') }}</span>
                                                                    </div>
                                                                    <div>
                                                                        @if($query->status == 'Closed' && $query->closure_date)
                                                                            <span class="meta-label">Closed</span>
                                                                            <span class="meta-value">{{ $query->closure_date->format('M d, Y') }}</span>
                                                                        @elseif($query->status == 'In Progress' && $query->expected_closure_date)
                                                                            <span class="meta-label">Expected</span>
                                                                            <span class="meta-value">{{ $query->expected_closure_date->format('M d, Y') }}</span>
                                                                        @endif
                                                                    </div>
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

                                                            <div class="description">
                                                                {{ Str::limit($query->description, 90) }}
                                                            </div>
                                                        </div>

                                                        @if($query->status == 'Pending')
                                                            <div class="card-footer text-center">
                                                                <form action="{{ route('queries.update-status', $query->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <input type="hidden" name="status" value="In Progress">
                                                                    <button type="submit" class="btn btn-primary btn-sm btn-start">
                                                                        <i class="fas fa-play-circle mr-1"></i> Start Progress
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @endif
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



@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const staffId = "{{ $staffInfo->id }}";
            const yearlyStatsRoute = "{{ route('owner.staff.queries.yearly', ['staff' => ':staffId']) }}".replace(':staffId', staffId);
            const monthlyStatsRoute = "{{ route('owner.staff.queries.monthly', ['staff' => ':staffId']) }}".replace(':staffId', staffId);

            let pieChart, lineChart;
            const yearSelect = document.getElementById('yearSelect');
            const monthSelect = document.getElementById('monthSelect');

            // Initialize charts
            function initCharts() {
                const pieCtx = document.getElementById('pieChart').getContext('2d');
                const lineCtx = document.getElementById('lineChart').getContext('2d');

                // Enhanced Pie Chart with labels
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
                                    color: '#374151'
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

                // Enhanced Line Chart
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
                                    minRotation: 45
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    font: {
                                        size: 12
                                    },
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
                                    boxWidth: 12
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
                    return;
                }

                const labels = [];
                const values = [];

                // Exclude total_queries from the pie chart
                Object.keys(data).forEach(key => {
                    if (key !== 'total_queries' && data[key] > 0) {
                        labels.push(key.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' '));
                        values.push(data[key]);
                    }
                });

                // If no data, show a placeholder
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

                // Extract months and statuses
                const months = Object.keys(data);
                const statuses = new Set();

                months.forEach(month => {
                    Object.keys(data[month]).forEach(key => {
                        if (key !== 'total_queries') {
                            statuses.add(key);
                        }
                    });
                });

                // Prepare datasets for each status
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

                // Also add total queries dataset
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
                // When year changes, update both charts
                fetchPieChartData();
                fetchLineChartData();
            });

            monthSelect.addEventListener('change', function() {
                // When month changes, update only pie chart
                fetchPieChartData();
            });
        });
    </script>
@endpush
