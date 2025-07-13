@extends('layouts.app')

@section('title', 'Admin Dashboard')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        :root {
            --primary: #184E83;
            --primary-light: #1A6FC9;
            --secondary: #66CDAA;
            --accent: #FA8072;
            --light: #F8F9FA;
            --dark: #212529;
            --gray: #6C757D;
            --success: #28A745;
            --danger: #DC3545;
            --warning: #FFC107;
            --info: #17A2B8;
        }

        body {
        }

        .dashboard_Header {
            color: var(--sidenavbar-text-color);
            margin-bottom: 1.5rem;
            display: block;
        }

        /* Stats Cards */
        .stats-card {
            border-radius: 10px;
            padding: 20px;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #17A2B8, #5BC0DE);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, var(--secondary), #8FE3CF);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, var(--accent), #FFA07A);
        }

        .icon-container {
            font-size: 2.5rem;
            opacity: 0.2;
            position: absolute;
            right: 20px;
            top: 20px;
        }

        .stats-content h3 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stats-content p {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 15px;
        }

        .progress-indicator {
            background: rgba(255,255,255,0.2);
            height: 4px;
            border-radius: 2px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background: white;
            border-radius: 2px;
        }

        /* Advanced Data Cards */
        .advanced-data-card {
            background: var(--body-card-bg);
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            height: 100%;
            position: relative;
        }

        .card-header {
            padding: 18px 20px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .card-header h3 {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
            color: var(--sidenavbar-text-color);
        }

        .card-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-details {
            background: var(--primary);
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-details:hover {
            background: var(--primary-light);
            transform: translateX(3px);
        }

        .btn-details i {
            margin-left: 5px;
            font-size: 14px;
        }

        .card-body {
            padding: 20px;
        }

        .data-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .data-item {
            text-align: center;
            padding: 15px;
            background: var(--main-background-color2);
            border-radius: 8px;
        }

        .data-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--sidenavbar-text-color);
            margin-bottom: 5px;
        }

        .data-label {
            font-size: 13px;
            color: var(--sidenavbar-text-color);
            margin-bottom: 8px;
        }

        .data-trend {
            font-size: 11px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .data-trend i {
            margin-right: 3px;
        }

        .data-trend.up {
            color: var(--success);
        }

        .data-trend.down {
            color: var(--danger);
        }

        .mini-chart-container, .donut-chart-container {
            height: 100px;
            margin-top: 10px;
            position: relative;
        }

        /* Chart Cards */
        .chart-card {
            background: var(--body-card-bg);
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            height: 100%;
            position: relative;
        }

        .chart-header {
            padding: 15px 20px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .chart-header h4 {
            font-size: 16px;
            font-weight: 600;
            margin: 0;
            color: var(--sidenavbar-text-color);
        }

        .legend-item {
            font-size: 12px;
            color: var(--sidenavbar-text-color);
            margin-left: 15px;
            display: flex;
            align-items: center;
        }

        .legend-item i {
            margin-right: 5px;
            font-size: 14px;
        }

        .legend-item.active i {
            color: #4BC0C0;
        }

        .legend-item.expired i {
            color: #FF6384;
        }

        .legend-item.trial i {
            color: #FFCD56;
        }

        .legend-item.pending i {
            color: #36A2EB;
        }

        .legend-item.approved i {
            color: #4BC0C0;
        }

        .legend-item.rejected i {
            color: #FF6384;
        }

        .chart-container {
            padding: 15px;
            height: 300px;
            position: relative;
            width: 100%;
            transition: all 0.5s ease;
            backface-visibility: hidden;
        }

        .chart-container canvas {
            width: 100% !important;
            height: 100% !important;
        }

        /* Chart Controls */
        .chart-controls {
            display: flex;
            gap: 8px;
        }

        .chart-btn {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: white;
            border: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            color: var(--gray);
        }

        .chart-btn:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        /* Filter Panel */
        .filter-panel {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            padding: 20px;
            background: var(--body-card-bg);
            transform: rotateY(180deg);
            backface-visibility: hidden;
            transition: all 0.5s ease;
            overflow-y: auto;
        }

        .filter-panel h5 {
            margin-top: 0;
            margin-bottom: 15px;
            color: var(--sidenavbar-text-color);
        }

        .filter-group {
            margin-bottom: 15px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 13px;
            color: var(--sidenavbar-text-color);
        }

        .filter-group input, .filter-group select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 13px;
        }

        .filter-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-filter {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 13px;
            cursor: pointer;
        }

        .btn-apply {
            background: var(--primary);
            color: white;
            border: none;
        }

        .btn-cancel {
            background-color: #6c757d;
            color: #ffff;
            border: 1px solid #ddd;
        }

        /* Flip Container */
        .flip-container {
            perspective: 1000px;
            height: 100%;
        }

        .flipper {
            position: relative;
            width: 100%;
            height: 100%;
            transition: transform 0.5s;
            transform-style: preserve-3d;
        }

        .flip-container.flipped .flipper {
            transform: rotateY(180deg);
        }

        .currentMonth,
        .currentYear,
        .currentPlan,
        .currentOrganization,
        .currentStart,
        .currentEnd {
            font-size: 12px;
            color: var(--sidenavbar-text-color);
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .data-grid {
                grid-template-columns: 1fr;
            }

            .chart-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .card-actions {
                margin-top: 10px;
            }
        }

        /* Main content adjustments */
        #main {

        }

        .content-wrapper {
            padding: 15px;
        }

        .mini-chart-container canvas,
        .donut-chart-container canvas {
            width: 100% !important;
            height: 100% !important;
            display: block;
        }

        .flip-container {
            min-height: 300px;
        }

        @media (max-width: 768px) {
            .data-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .card-header h3 {
                font-size: 16px;
            }

            .card-actions {
                font-size: 12px;
            }

            .data-value {
                font-size: 20px;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Top Navigation -->
    <x-Admin.top-navbar :searchVisible="false"/>

    <!-- Side Navigation -->
    <x-Admin.side-navbar :openSections="['Dashboard']"/>
    <x-error-success-model />

    <div id="main" style="margin-top: 36px;">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="content-wrapper" style="min-height: 751px;">
                        <section class="content-header mt-3">
                            <h3 class="dashboard_Header">Dashboard</h3>
                        </section>

                        <section class="content">
                            <!-- Stats Cards Row -->
                            <div class="row">
                                <!-- Organizations Created Card -->
                                <div class="col-md-3 col-sm-6 mb-4">
                                    <a class="text-decoration-none" href="{{ route('organizations.index') }}">
                                        <div class="stats-card bg-gradient-primary">
                                            <div class="icon-container">
                                                <i class="bx bxs-business"></i>
                                            </div>
                                            <div class="stats-content">
                                                <h3 id="totalOrganizations">0 / 0</h3>
                                                <p>Organizations Created</p>
                                            </div>
                                            <div id="org-progress" class="progress-indicator">
                                                <div class="progress-bar" style="width: 75%"></div>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <!-- Active Users Card -->
                                <div class="col-md-3 col-sm-6 mb-4">
                                    <a class="text-decoration-none" href="{{ route('users.index') }}">
                                        <div class="stats-card bg-gradient-info">
                                            <div class="icon-container">
                                                <i class="bx bx-user"></i>
                                            </div>
                                            <div class="stats-content">
                                                <h3 id="totalUsers">0 / 0</h3>
                                                <p>Active Users</p>
                                            </div>
                                            <div id="user-progress" class="progress-indicator">
                                                <div class="progress-bar" style="width: 60%"></div>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <!-- Buildings For Approval Card -->
                                <div class="col-md-3 col-sm-6 mb-4">
                                    <a class="text-decoration-none" href="{{ route('buildings.index',['status'=>'Under Review']) }}">
                                        <div class="stats-card bg-gradient-warning">
                                            <div class="icon-container">
                                                <i class="bx bx-buildings"></i>
                                            </div>
                                            <div class="stats-content">
                                                <h3 id="pendingApprovals">0 / 0</h3>
                                                <p>Pending Approvals</p>
                                            </div>
                                            <div id="building-progress" class="progress-indicator">
                                                <div class="progress-bar" style="width: 45%"></div>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <!-- Monthly Revenue Card -->
                                <div class="col-md-3 col-sm-6 mb-4">
                                    <a class="text-decoration-none" href="{{ route('finance.index') }}">
                                        <div class="stats-card bg-gradient-success">
                                            <div class="icon-container">
                                                <i class="bx bx-dollar"></i>
                                            </div>
                                            <div class="stats-content">
                                                <h3 id="totalRevenue">PKR 0</h3>
                                                <p>Monthly Revenue</p>
                                            </div>
                                            <div id="revenue-progress" class="progress-indicator">
                                                <div class="progress-bar" style="width: 90%"></div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <!-- 1st Charts Row -->
                            <div class="row">
                                <!-- Subscription Plans Summary -->
                                <div class="col-xl-6 col-lg-12 mb-4">
                                    <div class="advanced-data-card h-100">
                                        <div class="card-header">
                                            <h3 class="mb-2 mb-md-0">Subscription Plans</h3>
                                            <div class="card-actions">
                                                <div class="d-flex flex-wrap align-items-center justify-content-end">
                                                    <span class="currentMonth me-2 mb-1 mb-md-0" id="currentMonthChart1"></span>
                                                    <span class="currentPlan me-2 mb-1 mb-md-0" id="currentPlanChart1">All Plans</span>
                                                    <div class="chart-controls d-flex mb-1 mb-md-0">
                                                        <button class="chart-btn reload-btn me-1" title="Reload Data" data-chart="subscription">
                                                            <i class="bx bx-refresh"></i>
                                                        </button>
                                                        <button class="chart-btn settings-btn" title="Chart Settings" data-chart="subscription">
                                                            <i class="bx bx-cog"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body p-2 p-md-3">
                                            <div class="flip-container" id="subscriptionFlipContainer" style="min-height: 250px;">
                                                <div class="flipper h-100">
                                                    <div class="chart-container h-100 position-relative">
                                                        <div class="data-grid mb-3">
                                                            <div class="data-item" data-type="active">
                                                                <div class="data-value" id="activeSubscriptions">0</div>
                                                                <div class="data-label">Active</div>
                                                                <div class="data-trend"><span>-</span></div>
                                                            </div>
                                                            <div class="data-item" data-type="expired">
                                                                <div class="data-value" id="expiredSubscriptions">0</div>
                                                                <div class="data-label">Expired</div>
                                                                <div class="data-trend"><span>-</span></div>
                                                            </div>
                                                            <div class="data-item" data-type="trial">
                                                                <div class="data-value" id="trialSubscriptions">0</div>
                                                                <div class="data-label">Active Trials</div>
                                                                <div class="data-trend"><span>-</span></div>
                                                            </div>
                                                        </div>

                                                        <div class="mini-chart-container" style="height: 180px;">
                                                            <canvas id="subscriptionTrendChart"></canvas>
                                                            <div id="subscriptionError"
                                                                 class="text-center text-danger w-100"
                                                                 style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="filter-panel p-3">
                                                        <h5>Subscription Plans Filters</h5>
                                                        <div class="filter-group" data-chart="subscription">
                                                            <label for="filterYear">Year</label>
                                                            <select id="filterYear" class="form-select">
                                                                @for ($y = now()->year; $y >= now()->year - 5; $y--)
                                                                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                                                @endfor
                                                            </select>
                                                        </div>

                                                        <div class="filter-group" data-chart="subscription">
                                                            <label for="filterMonth">Month</label>
                                                            <select id="filterMonth" class="form-select">
                                                                @foreach (range(1, 12) as $m)
                                                                    @php
                                                                        $val = str_pad($m, 2, '0', STR_PAD_LEFT);
                                                                    @endphp
                                                                    <option value="{{ $val }}" {{ $m == now()->month ? 'selected' : '' }}>
                                                                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="filter-group" data-chart="subscription">
                                                            <label for="filterPlan">Plan Type</label>
                                                            <select id="filterPlan" class="form-select" {{ empty($plans) || $plans->isEmpty() ? 'disabled' : '' }}>
                                                                <option value="">All Plans</option>
                                                                @if (!empty($plans) && $plans->isNotEmpty())
                                                                    @foreach ($plans as $plan)
                                                                        <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                                                                    @endforeach
                                                                @else
                                                                    <option disabled>No plans available</option>
                                                                @endif
                                                            </select>
                                                        </div>

                                                        <div class="filter-actions mt-3">
                                                            <button class="btn-filter btn-cancel me-2" id="btn-cancel-chart1">Cancel</button>
                                                            <button class="btn-filter btn-apply">Apply Filters</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Approval Requests -->
                                <div class="col-xl-6 col-lg-12 mb-4">
                                    <div class="advanced-data-card h-100">
                                        <div class="card-header">
                                            <h3 class="mb-2 mb-md-0">Approval Requests</h3>
                                            <div class="card-actions">
                                                <div class="d-flex flex-wrap align-items-center justify-content-end">
                                                    <span class="currentMonth me-2 mb-1 mb-md-0" id="currentMonthChart2"></span>
                                                    <span class="currentOrganization me-2 mb-1 mb-md-0" id="currentOrganizationChart2">All Organizations</span>
                                                    <div class="chart-controls d-flex me-2 mb-1 mb-md-0">
                                                        <button class="chart-btn reload-btn me-1" title="Reload Data" data-chart="approval">
                                                            <i class="bx bx-refresh"></i>
                                                        </button>
                                                        <button class="chart-btn settings-btn" title="Chart Settings" data-chart="approval">
                                                            <i class="bx bx-cog"></i>
                                                        </button>
                                                    </div>
                                                    <button class="btn-details" data-route="{{ route('buildings.index') }}" id="detailButtonChart2">Manage <i class="bx bx-chevron-right"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body p-2 p-md-3">
                                            <div class="flip-container" id="approvalFlipContainer" style="min-height: 250px;">
                                                <div class="flipper h-100">
                                                    <div class="chart-container h-100 position-relative">
                                                        <div class="data-grid mb-3">
                                                            <div class="data-item" data-type="pending">
                                                                <div class="data-value" id="pendingRequests">0</div>
                                                                <div class="data-label">Pending</div>
                                                                <div class="data-trend"><span>-</span></div>
                                                            </div>
                                                            <div class="data-item" data-type="approved">
                                                                <div class="data-value" id="approvedRequests">0</div>
                                                                <div class="data-label">Approved</div>
                                                                <div class="data-trend"><span>-</span></div>
                                                            </div>
                                                            <div class="data-item" data-type="rejected">
                                                                <div class="data-value" id="rejectedRequests">0</div>
                                                                <div class="data-label">Rejected</div>
                                                                <div class="data-trend"><span>-</span></div>
                                                            </div>
                                                        </div>
                                                        <div class="donut-chart-container" style="height: 180px;">
                                                            <canvas id="approvalDonutChart"></canvas>
                                                            <div id="approvalError"
                                                                 class="text-center text-danger w-100"
                                                                 style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="filter-panel p-3">
                                                        <h5>Approval Requests Filters</h5>
                                                        <div class="filter-group" data-chart="approval">
                                                            <label for="filterYear">Year</label>
                                                            <select id="filterYear" class="form-select">
                                                                @for ($y = now()->year; $y >= now()->year - 5; $y--)
                                                                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                                                @endfor
                                                            </select>
                                                        </div>

                                                        <div class="filter-group" data-chart="approval">
                                                            <label for="filterMonth">Month</label>
                                                            <select id="filterMonth" class="form-select">
                                                                @foreach (range(1, 12) as $m)
                                                                    @php
                                                                        $val = str_pad($m, 2, '0', STR_PAD_LEFT);
                                                                    @endphp
                                                                    <option value="{{ $val }}" {{ $m == now()->month ? 'selected' : '' }}>
                                                                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="filter-group" data-chart="approval">
                                                            <label for="filterPlan">Select Organization</label>
                                                            <select id="filterOrganizationChart2" class="form-select" {{ empty($organizations) || $organizations->isEmpty() ? 'disabled' : '' }}>
                                                                <option value="">All Organizations</option>
                                                                @if (!empty($organizations) && $organizations->isNotEmpty())
                                                                    @foreach ($organizations as $organization)
                                                                        <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                                                                    @endforeach
                                                                @else
                                                                    <option disabled>No organizations available</option>
                                                                @endif
                                                            </select>
                                                        </div>
                                                        <div class="filter-actions mt-3">
                                                            <button class="btn-filter btn-cancel me-2" id="btn-cancel-chart2">Cancel</button>
                                                            <button class="btn-filter btn-apply">Apply Filters</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 2nd Charts Row -->
                            <div class="row">
                                <!-- Monthly Revenue Growth -->
                                <div class="col-md-6 mb-4">
                                    <div class="chart-card">
                                        <div class="chart-header">
                                            <h4>Monthly Revenue Growth</h4>
                                            <div class="card-actions">
                                                <div class="chart-controls">
                                                    <span class="currentYear" id="currentYearChart3">{{ now()->year }}</span>
                                                    <span class="currentPlan" id="currentPlanChart3">All Plans</span>
                                                    <button class="chart-btn reload-btn" title="Reload Settings" data-chart="revenue">
                                                        <i class="bx bx-refresh"></i>
                                                    </button>
                                                    <button class="chart-btn settings-btn" title="Chart Settings" data-chart="revenue">
                                                        <i class="bx bx-cog"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="flip-container" id="revenueFlipContainer">
                                                <div class="flipper">
                                                    <div class="chart-container">
                                                        <canvas id="revenueLineChart"></canvas>
                                                        <div id="revenueError"
                                                             class="text-center text-danger"
                                                             style="display: none; position: absolute; top: 45%; left: 50%; transform: translate(-50%, -50%); z-index: 2;">
                                                        </div>
                                                    </div>
                                                    <div class="filter-panel">
                                                        <h5>Revenue Growth Filters</h5>
                                                        <div class="filter-group" data-chart="revenue">
                                                            <label for="filterYear">Year</label>
                                                            <select id="filterYear" class="form-select">
                                                                @for ($y = now()->year; $y >= now()->year - 5; $y--)
                                                                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                                                @endfor
                                                            </select>
                                                        </div>

                                                        <div class="filter-group" data-chart="revenue">
                                                            <label for="filterPlan">Plan Type</label>
                                                            <select id="filterPlan" class="form-select" {{ empty($plans) || $plans->isEmpty() ? 'disabled' : '' }}>
                                                                <option value="">All Plans</option>
                                                                @if (!empty($plans) && $plans->isNotEmpty())
                                                                    @foreach ($plans as $plan)
                                                                        <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                                                                    @endforeach
                                                                @else
                                                                    <option disabled>No plans available</option>
                                                                @endif
                                                            </select>
                                                        </div>
                                                        <div class="filter-actions">
                                                            <button class="btn-filter btn-cancel" id="btn-cancel-chart3">Cancel</button>
                                                            <button class="btn-filter btn-apply">Apply Filters</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Plan Popularity -->
                                <div class="col-md-6 mb-4">
                                    <div class="chart-card">
                                        <div class="chart-header">
                                            <h4>Plan Popularity</h4>
                                            <div class="card-actions">
                                                <span class="currentStart" id="currentStartChart4">{{ strtoupper(now()->subDays(30)->format('d F Y')) }}</span>
                                                <span class="separator"> - </span>
                                                <span class="currentEnd" id="currentEndChart4">{{ strtoupper(now()->format('d F Y')) }}</span>
                                                <span class="legend-item"><i class="bx bx-pie-chart-alt"></i> By Subscriptions</span>
                                                <div class="chart-controls">
                                                    <button class="chart-btn reload-btn" title="Reload Settings" data-chart="plan">
                                                        <i class="bx bx-refresh"></i>
                                                    </button>
                                                    <button class="chart-btn settings-btn" title="Chart Settings" data-chart="plan">
                                                        <i class="bx bx-cog"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="flip-container" id="planFlipContainer">
                                                <div class="flipper">
                                                    <div class="chart-container">
                                                        <canvas id="planPieChart"></canvas>
                                                        <div id="planError"
                                                             class="text-center text-danger"
                                                             style="display: none; position: absolute; top: 45%; left: 50%; transform: translate(-50%, -50%); z-index: 2;">
                                                        </div>
                                                    </div>
                                                    <div class="filter-panel">
                                                        <h5>Plan Popularity Filters</h5>
                                                        @php
                                                            $today = now()->toDateString();
                                                            $thirtyDaysAgo = now()->subDays(30)->toDateString();
                                                        @endphp

                                                        <div class="filter-group" data-chart="plan">
                                                            <label for="filterStart">Start Date</label>
                                                            <input type="date" id="filterStart" value="{{ $thirtyDaysAgo }}">
                                                        </div>

                                                        <div class="filter-group" data-chart="plan">
                                                            <label for="filterEnd">End Date</label>
                                                            <input type="date" id="filterEnd" value="{{ $today }}">
                                                        </div>

                                                        <div class="filter-actions">
                                                            <button class="btn-filter btn-cancel" id="btn-cancel-chart4">Cancel</button>
                                                            <button class="btn-filter btn-apply">Apply Filters</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 3rd Charts Row -->
                            <div class="row">
                                <!-- Subscription Plan Distribution -->
                                <div class="col-md-6 mb-4">
                                    <div class="chart-card">
                                        <div class="chart-header">
                                            <h4>Subscription Plan Distribution</h4>
                                            <div class="card-actions">
                                                <span class="currentYear" id="currentYearChart5">{{ now()->year }}</span>
                                                <span class="currentPlan" id="currentPlanChart5">All Plans</span>
                                                <div class="chart-controls">
                                                    <button class="chart-btn reload-btn" title="Reload Settings" data-chart="distribution">
                                                        <i class="bx bx-refresh"></i>
                                                    </button>
                                                    <button class="chart-btn settings-btn" title="Chart Settings" data-chart="distribution">
                                                        <i class="bx bx-cog"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="flip-container" id="distributionFlipContainer">
                                                <div class="flipper">
                                                    <div class="chart-container">
                                                        <canvas id="subscriptionBarChart"></canvas>
                                                        <div id="distributionError"
                                                             class="text-center text-danger"
                                                             style="display: none; position: absolute; top: 45%; left: 50%; transform: translate(-50%, -50%); z-index: 2;">
                                                        </div>
                                                    </div>
                                                    <div class="filter-panel">
                                                        <h5>Subscription Distribution Filters</h5>
                                                        <div class="filter-group" data-chart="distribution">
                                                            <label for="filterYear">Year</label>
                                                            <select id="filterYear" class="form-select">
                                                                @for ($y = now()->year; $y >= now()->year - 5; $y--)
                                                                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                                                @endfor
                                                            </select>
                                                        </div>

                                                        <div class="filter-group" data-chart="distribution">
                                                            <label for="filterPlan">Plan Type</label>
                                                            <select id="filterPlan" class="form-select" {{ empty($plans) || $plans->isEmpty() ? 'disabled' : '' }}>
                                                                <option value="">All Plans</option>
                                                                @if (!empty($plans) && $plans->isNotEmpty())
                                                                    @foreach ($plans as $plan)
                                                                        <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                                                                    @endforeach
                                                                @else
                                                                    <option disabled>No plans available</option>
                                                                @endif
                                                            </select>
                                                        </div>
                                                        <div class="filter-actions">
                                                            <button class="btn-filter btn-cancel" id="btn-cancel-chart5">Cancel</button>
                                                            <button class="btn-filter btn-apply">Apply Filters</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Approval Status Timeline -->
                                <div class="col-md-6 mb-4">
                                    <div class="chart-card">
                                        <div class="chart-header">
                                            <h4>Approval Status Timeline</h4>
                                            <div class="card-actions">
                                                <span class="currentYear" id="currentYearChart6">{{ now()->year }}</span>
                                                <span class="currentOrganization" id="currentOrganizationChart6">All Organizations</span>
                                                <div class="chart-controls">
                                                    <button class="chart-btn reload-btn" title="Reload Settings" data-chart="timeline">
                                                        <i class="bx bx-refresh"></i>
                                                    </button>
                                                    <button class="chart-btn settings-btn" title="Chart Settings" data-chart="timeline">
                                                        <i class="bx bx-cog"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="flip-container" id="timelineFlipContainer">
                                                <div class="flipper">
                                                    <div class="chart-container">
                                                        <canvas id="approvalTimelineChart"></canvas>
                                                        <div id="timelineError"
                                                             class="text-center text-danger"
                                                             style="display: none; position: absolute; top: 45%; left: 50%; transform: translate(-50%, -50%); z-index: 2;">
                                                        </div>
                                                    </div>
                                                    <div class="filter-panel">
                                                        <h5>Approval Timeline Filters</h5>
                                                        <div class="filter-group" data-chart="timeline">
                                                            <label for="filterYear">Year</label>
                                                            <select id="filterYear" class="form-select">
                                                                @for ($y = now()->year; $y >= now()->year - 5; $y--)
                                                                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                                                @endfor
                                                            </select>
                                                        </div>

                                                        <div class="filter-group" data-chart="timeline">
                                                            <label for="filterPlan">Select Organization</label>
                                                            <select id="filterOrganization" class="form-select" {{ empty($organizations) || $organizations->isEmpty() ? 'disabled' : '' }}>
                                                                <option value="">All Organizations</option>
                                                                @if (!empty($organizations) && $organizations->isNotEmpty())
                                                                    @foreach ($organizations as $organization)
                                                                        <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                                                                    @endforeach
                                                                @else
                                                                    <option disabled>No organizations available</option>
                                                                @endif
                                                            </select>
                                                        </div>
                                                        <div class="filter-actions">
                                                            <button class="btn-filter btn-cancel" id="btn-cancel-chart6">Cancel</button>
                                                            <button class="btn-filter btn-apply">Apply Filters</button>
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
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Chart.js plugins -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-trendline"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Initialize all charts
            let subscriptionTrendChart, approvalDonutChart, revenueLineChart, planPieChart, subscriptionBarChart, approvalTimelineChart;

            //Current Month display
            const dateObj = new Date();
            const formattedDate = new Intl.DateTimeFormat('en-US', {
                year: 'numeric',
                month: 'long'
            }).format(dateObj);

            const [month, year] = formattedDate.split(' ');
            const capitalMonthDate = `${month.toUpperCase()} ${year}`;

            // Update all elements with .currentMonth
            document.querySelectorAll('.currentMonth').forEach(el => {
                el.textContent = capitalMonthDate;
            });

            // Initialize charts with empty data
            function initCharts() {
                // 1. Subscription Trend Mini Chart
                const subscriptionTrendCtx = document.getElementById('subscriptionTrendChart').getContext('2d');
                subscriptionTrendChart = new Chart(subscriptionTrendCtx, {
                    type: 'doughnut',
                    data: { labels: [], datasets: [] },
                    options: { responsive: true, maintainAspectRatio: false }
                });

                // 2. Approval Donut Chart
                const approvalDonutCtx = document.getElementById('approvalDonutChart').getContext('2d');
                approvalDonutChart = new Chart(approvalDonutCtx, {
                    type: 'doughnut',
                    data: { labels: [], datasets: [] },
                    options: { responsive: true, maintainAspectRatio: false }
                });

                // 3. Monthly Revenue Growth Chart
                const revenueLineCtx = document.getElementById('revenueLineChart').getContext('2d');
                revenueLineChart = new Chart(revenueLineCtx, {
                    type: 'line',
                    data: { labels: [], datasets: [] },
                    options: { responsive: true, maintainAspectRatio: false }
                });

                // 4. Plan Popularity Pie Chart
                const planPieCtx = document.getElementById('planPieChart').getContext('2d');
                planPieChart = new Chart(planPieCtx, {
                    type: 'pie',
                    data: { labels: [], datasets: [] },
                    options: { responsive: true, maintainAspectRatio: false }
                });

                // 5. Subscription Plan Distribution Bar Chart
                const subscriptionBarCtx = document.getElementById('subscriptionBarChart').getContext('2d');
                subscriptionBarChart = new Chart(subscriptionBarCtx, {
                    type: 'bar',
                    data: { labels: [], datasets: [] },
                    options: { responsive: true, maintainAspectRatio: false }
                });

                // 6. Approval Status Timeline Chart
                const approvalTimelineCtx = document.getElementById('approvalTimelineChart').getContext('2d');
                approvalTimelineChart = new Chart(approvalTimelineCtx, {
                    type: 'line',
                    data: { labels: [], datasets: [] },
                    options: { responsive: true, maintainAspectRatio: false }
                });
            }

            // Initialize charts
            initCharts();

            // Load all data on page load
            loadAllData();

            // Function to load all data
            function loadAllData() {
                fetchStatsData();
                fetchSubscriptionData();
                fetchApprovalData();
                fetchRevenueData();
                fetchPlanPopularityData();
                fetchSubscriptionDistributionData();
                fetchApprovalTimelineData();
            }

            // Fetch Stats Data
            function fetchStatsData() {
                fetch(`{{ route('admin.dashboard.stats') }}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(async response => {
                        if (!response.ok) throw response;
                        return response.json();
                    })
                    .then(data => {
                        document.getElementById('totalOrganizations').textContent = `${data.counts.newOrganizationsThisMonth} / ${data.counts.totalOrganizations}`;
                        document.getElementById('totalUsers').textContent = `${data.counts.activeUsersThisMonth} / ${data.counts.totalUsers}`;
                        document.getElementById('pendingApprovals').textContent = `${data.counts.pendingBuildingsThisMonth} / ${data.counts.totalPendingBuildings}`;
                        document.getElementById('totalRevenue').textContent = `PKR ${(data.revenue.currentMonth || 0).toLocaleString()}`;

                        document.querySelector('#org-progress .progress-bar').style.width = `${data.progress.organization || 0}%`;
                        document.querySelector('#user-progress .progress-bar').style.width = `${data.progress.user || 0}%`;
                        document.querySelector('#building-progress .progress-bar').style.width = `${data.progress.building || 0}%`;

                        const revenueProgressBar = document.querySelector('#revenue-progress .progress-bar');
                        const revenueGrowth = data.revenue.growth ?? 0;
                        const revenueWidth = Math.min(Math.abs(revenueGrowth), 100);

                        revenueProgressBar.style.width = `${revenueWidth}%`;
                        revenueProgressBar.style.backgroundColor = revenueGrowth >= 0 ? 'white' : '#e57373';
                    })
                    .catch(error => console.error('Error fetching stats:', error));
            }

            // 1. Fetch Subscription Data
            function fetchSubscriptionData() {
                const filterPanel = document.querySelector('.filter-group[data-chart="subscription"]').closest('.filter-panel');
                const yearSelect = filterPanel.querySelector('select[id^="filterYear"]');
                const monthSelect = filterPanel.querySelector('select[id^="filterMonth"]');
                const planSelect = filterPanel.querySelector('select[id^="filterPlan"]');


                const year = yearSelect ? yearSelect.value : null;
                const month = monthSelect ? monthSelect.value : null;
                const selectedPlan = planSelect ? planSelect.value : null;
                const selectedMonth = `${year}-${month}`;

                params = {}
                params.month = selectedMonth;
                params.plan = selectedPlan;

                const queryString = new URLSearchParams(params).toString();
                fetch(`{{ route('admin.dashboard.subscription.plans') }}?${queryString}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(async response => {
                        if (!response.ok) throw response;
                        return response.json();
                    })
                    .then(data => {
                        document.getElementById('activeSubscriptions').textContent = data.active || '0';
                        document.getElementById('expiredSubscriptions').textContent = data.expired || '0';
                        document.getElementById('trialSubscriptions').textContent = data.trial || '0';

                        // Handle growth arrows
                        updateGrowthTrend('active', data.growth.active);
                        updateGrowthTrend('expired', data.growth.expired);
                        updateGrowthTrend('trial', data.growth.trial);

                        subscriptionTrendChart.data.labels = ['Active', 'Expired', 'Active Trials'];
                        subscriptionTrendChart.data.datasets = [{
                            data: [data.active || 0, data.expired || 0, data.trial || 0],
                            backgroundColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)', 'rgba(255, 205, 86, 1)'],
                            borderWidth: 0
                        }];

                        // Updating the Labels
                        let planName = planSelect.options[planSelect.selectedIndex].text || null;

                        const dateObj = new Date(`${selectedMonth}-01`);
                        const formattedDate = new Intl.DateTimeFormat('en-US', {
                            year: 'numeric',
                            month: 'long'
                        }).format(dateObj);

                        const [month, year] = formattedDate.split(' ');

                        document.getElementById('currentMonthChart1').textContent = `${month.toUpperCase()} ${year}`;
                        document.getElementById('currentPlanChart1').textContent = planName;

                        // Updating the chart
                        subscriptionTrendChart.update();
                    })
                    .catch(error => {
                        showChartError(error, subscriptionTrendChart, 'subscriptionError');
                    });
            }

            // 2. Fetch Approval Data
            function fetchApprovalData() {
                const filterPanel = document.querySelector('.filter-group[data-chart="approval"]').closest('.filter-panel');
                const yearSelect = filterPanel.querySelector('select[id^="filterYear"]');
                const monthSelect = filterPanel.querySelector('select[id^="filterMonth"]');
                const organizationSelect = filterPanel.querySelector('select[id^="filterOrganization"]');

                const year = yearSelect ? yearSelect.value : null;
                const month = monthSelect ? monthSelect.value : null;
                const selectedOrganization = organizationSelect ? organizationSelect.value : null;
                const selectedMonth = `${year}-${month}`;

                params = {}
                params.month = selectedMonth;
                params.organization = selectedOrganization;


                const queryString = new URLSearchParams(params).toString();
                fetch(`{{ route('admin.dashboard.approval.requests') }}?${queryString}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(async response => {
                        if (!response.ok) throw response;
                        return response.json();
                    })
                    .then(data => {
                        document.getElementById('pendingRequests').textContent = data.counts.pending || '0';
                        document.getElementById('approvedRequests').textContent = data.counts.approved || '0';
                        document.getElementById('rejectedRequests').textContent = data.counts.rejected || '0';

                        updateGrowthTrend('pending', data.growth.pending);
                        updateGrowthTrend('approved', data.growth.approved);
                        updateGrowthTrend('rejected', data.growth.rejected);

                        approvalDonutChart.data.labels = ['Pending', 'Approved', 'Rejected'];
                        approvalDonutChart.data.datasets = [{
                            data: [data.counts.pending || 0, data.counts.approved || 0, data.counts.rejected || 0],
                            backgroundColor: ['rgba(255, 205, 86, 1)', 'rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)'],
                            borderWidth: 0
                        }];

                        // Updating the Labels
                        let organizationName = organizationSelect.options[organizationSelect.selectedIndex].text || null;

                        const dateObj = new Date(`${selectedMonth}-01`);
                        const formattedDate = new Intl.DateTimeFormat('en-US', {
                            year: 'numeric',
                            month: 'long'
                        }).format(dateObj);

                        const [month, year] = formattedDate.split(' ');

                        document.getElementById('currentMonthChart2').textContent = `${month.toUpperCase()} ${year}`;
                        document.getElementById('currentOrganizationChart2').textContent = organizationName;

                        // Updating the Chart
                        approvalDonutChart.update();
                    })
                    .catch(error => {
                        showChartError(error, approvalDonutChart, 'approvalError');
                    });
            }

            // 3. Fetch Revenue Data
            function fetchRevenueData() {
                const filterPanel = document.querySelector('.filter-group[data-chart="revenue"]').closest('.filter-panel');
                const yearSelect = filterPanel.querySelector('select[id^="filterYear"]');
                const planSelect = filterPanel.querySelector('select[id^="filterPlan"]');


                const selectedYear = yearSelect ? yearSelect.value : null;
                const selectedPlan = planSelect ? planSelect.value : null;

                params = {}
                params.year = selectedYear;
                params.plan = selectedPlan;

                const queryString = new URLSearchParams(params).toString();
                fetch(`{{ route('admin.dashboard.revenue.growth') }}?${queryString}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(async response => {
                        if (!response.ok) throw response;
                        return response.json();
                    })
                    .then(data => {
                        revenueLineChart.data.labels = data.labels || [];
                        revenueLineChart.data.datasets = [
                            {
                                label: 'Revenue ($)',
                                data: data.revenue || [],
                                borderColor: '#184E83',
                                backgroundColor: 'rgba(24, 78, 131, 0.1)',
                                borderWidth: 2,
                                tension: 0.3,
                                yAxisID: 'y',
                                fill: true
                            },
                            {
                                label: 'Growth Rate (%)',
                                data: data.growthRate || [],
                                borderColor: '#FF6384',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                tension: 0.3,
                                yAxisID: 'y1'
                            }
                        ];

                        // Updating the Labels
                        let planName = planSelect.options[planSelect.selectedIndex].text || null;

                        document.getElementById('currentYearChart3').textContent = selectedYear;
                        document.getElementById('currentPlanChart3').textContent = planName;

                        // Updating the chart
                        revenueLineChart.update();
                    })
                    .catch(error => {
                        showChartError(error, revenueLineChart, 'revenueError');
                    });
            }

            // 4. Fetch Plan Popularity Data
            function fetchPlanPopularityData() {
                const filterPanel = document.querySelector('.filter-group[data-chart="plan"]').closest('.filter-panel');
                const startInput = filterPanel.querySelector('input[id^="filterStart"]');
                const endInput = filterPanel.querySelector('input[id^="filterEnd"]');

                const startDate = new Date(startInput.value);
                const endDate = new Date(endInput.value);

                if (startInput.value && endInput.value && startDate > endDate) {
                    alert("Start Date cannot be after End Date.");
                    return false;
                }

                params = {}
                params.start = startInput.value;
                params.end = endInput.value;

                const queryString = new URLSearchParams(params).toString();
                fetch(`{{ route('admin.dashboard.plan.popularity') }}?${queryString}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(async response => {
                        if (!response.ok) throw response;
                        return response.json();
                    })
                    .then(data => {
                        planPieChart.data.labels = data.labels || [];
                        planPieChart.data.datasets = [{
                            data: data.values || [],
                            backgroundColor: generateColors(data.labels.length),
                            borderWidth: 0
                        }];

                        // Update Label
                        const formatDate = (dateString) => {
                            const date = new Date(dateString);
                            return date.toLocaleDateString('en-GB', {
                                day: '2-digit',
                                month: 'long',
                                year: 'numeric'
                            }).toUpperCase();
                        };

                        document.getElementById('currentStartChart4').textContent = formatDate(startInput.value);
                        document.getElementById('currentEndChart4').textContent = formatDate(endInput.value);


                        // Update Chart
                        planPieChart.update();
                    })
                    .catch(error => {
                        showChartError(error, planPieChart, 'planError');
                    });
            }

            // 5. Fetch Subscription Distribution Data
            function fetchSubscriptionDistributionData() {
                const filterPanel = document.querySelector('.filter-group[data-chart="distribution"]').closest('.filter-panel');
                const yearSelect = filterPanel.querySelector('select[id^="filterYear"]');
                const planSelect = filterPanel.querySelector('select[id^="filterPlan"]');


                const selectedYear = yearSelect ? yearSelect.value : null;
                const selectedPlan = planSelect ? planSelect.value : null;

                params = {}
                params.year = selectedYear;
                params.plan = selectedPlan;

                const queryString = new URLSearchParams(params).toString();
                fetch(`{{ route('admin.dashboard.subscription.distribution') }}?${queryString}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(async response => {
                        if (!response.ok) throw response;
                        return response.json();
                    })
                    .then(data => {
                        subscriptionBarChart.data.labels = data.labels || [];
                        subscriptionBarChart.data.datasets = [
                            {
                                label: 'Activated',
                                data: data.active || [],
                                backgroundColor: 'rgba(75, 192, 192, 0.7)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 0,
                                borderRadius: 4
                            },
                            {
                                label: 'Expired',
                                data: data.expired || [],
                                backgroundColor: 'rgba(255, 99, 132, 0.7)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 0,
                                borderRadius: 4
                            },
                            {
                                label: 'Trials',
                                data: data.trial || [],
                                backgroundColor: 'rgba(255, 205, 86, 0.7)',
                                borderColor: 'rgba(255, 205, 86, 1)',
                                borderWidth: 0,
                                borderRadius: 4
                            }
                        ];

                        // Updating the Labels
                        let planName = planSelect.options[planSelect.selectedIndex].text || null;

                        document.getElementById('currentYearChart5').textContent = selectedYear;
                        document.getElementById('currentPlanChart5').textContent = planName;

                        // Update Plan
                        subscriptionBarChart.update();
                    })
                    .catch(error => {
                        showChartError(error, subscriptionBarChart, 'distributionError');
                    });
            }

            // 6. Fetch Approval Timeline Data
            function fetchApprovalTimelineData() {
                const filterPanel = document.querySelector('.filter-group[data-chart="timeline"]').closest('.filter-panel');
                const yearSelect = filterPanel.querySelector('select[id^="filterYear"]');
                const organizationSelect = filterPanel.querySelector('select[id^="filterOrganization"]');

                const selectedYear = yearSelect ? yearSelect.value : null;
                const selectedOrganization = organizationSelect ? organizationSelect.value : null;

                params = {}
                params.year = selectedYear;
                params.organization = selectedOrganization;

                const queryString = new URLSearchParams(params).toString();
                fetch(`{{ route('admin.dashboard.approval.timeline') }}?${queryString}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(async response => {
                        if (!response.ok) throw response;
                        return response.json();
                    })
                    .then(data => {
                        approvalTimelineChart.data.labels = data.labels || [];
                        approvalTimelineChart.data.datasets = [
                            {
                                label: 'Pending',
                                data: data.pending || [],
                                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 2,
                                tension: 0.3,
                                fill: true
                            },
                            {
                                label: 'Approved',
                                data: data.approved || [],
                                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 2,
                                tension: 0.3,
                                fill: true
                            },
                            {
                                label: 'Rejected',
                                data: data.rejected || [],
                                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 2,
                                tension: 0.3,
                                fill: true
                            }
                        ];

                        // Updating the Labels
                        let organizationName = organizationSelect.options[organizationSelect.selectedIndex].text || null;

                        document.getElementById('currentYearChart6').textContent = selectedYear;
                        document.getElementById('currentOrganizationChart6').textContent = organizationName;

                        // Update Chart
                        approvalTimelineChart.update();
                    })
                    .catch(error => {
                        showChartError(error, approvalTimelineChart, 'timelineError');
                    });
            }

            // Helper function to set the trends
            function updateGrowthTrend(type, value) {
                const container = document.querySelector(`.data-item[data-type="${type}"]`);
                if (!container) return;

                const trendDiv = container.querySelector('.data-trend');
                if (!trendDiv) return;

                // Reset all previous state
                trendDiv.classList.remove('text-success', 'text-danger', 'text-muted', 'up', 'down');

                if (value === 0 || value === null || typeof value !== 'number') {
                    trendDiv.innerHTML = `<span>-</span>`;
                    return;
                }

                const absValue = Math.abs(value).toFixed(1);
                const isPositive = value > 0;
                const isNegative = value < 0;
                const isBadGrowth = ['expired', 'canceled', 'rejected'].includes(type) ? isPositive : isNegative;

                const iconClass = isPositive ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt';
                const textClass = isBadGrowth ? 'text-danger' : 'text-success';
                const directionClass = isPositive ? 'up' : 'down';

                trendDiv.classList.add(textClass, directionClass);
                trendDiv.innerHTML = `<i class="bx ${iconClass}"></i> ${absValue}%`;
            }

            // Helper function to show the error in the chart
            function showChartError(error, chartInstance, errorElementId) {
                const errorDiv = document.getElementById(errorElementId);
                let message = 'Unexpected error occurred.';

                if (error instanceof Response) {
                    error.json().then(json => {
                        message = json.message || message;
                        displayError(message);
                    }).catch(() => displayError(message));
                } else if (error instanceof Error && error.message) {
                    message = error.message;
                    displayError(message);
                } else {
                    displayError(message);
                }

                function displayError(msg) {
                    if (errorDiv) {
                        errorDiv.textContent = msg;
                        errorDiv.style.display = 'block';
                    }

                    if (chartInstance) {
                        chartInstance.data.labels = [];
                        chartInstance.data.datasets = [];
                        chartInstance.update();
                    }
                }
            }

            // Helper function to create colors
            function generateColors(count, seed = Math.random() * 360) {
                const colors = [];
                const goldenAngle = 137.5;

                for (let i = 0; i < count; i++) {
                    const hue = (seed + i * goldenAngle) % 360;

                    const saturation = 70;
                    const lightness = 60;

                    const h = hue / 360;
                    const s = saturation / 100;
                    const l = lightness / 100;

                    const c = (1 - Math.abs(2 * l - 1)) * s;
                    const x = c * (1 - Math.abs((h * 6) % 2 - 1));
                    const m = l - c / 2;

                    let r, g, b;
                    if (h < 1/6) [r, g, b] = [c, x, 0];
                    else if (h < 2/6) [r, g, b] = [x, c, 0];
                    else if (h < 3/6) [r, g, b] = [0, c, x];
                    else if (h < 4/6) [r, g, b] = [0, x, c];
                    else if (h < 5/6) [r, g, b] = [x, 0, c];
                    else [r, g, b] = [c, 0, x];

                    r = Math.round((r + m) * 255);
                    g = Math.round((g + m) * 255);
                    b = Math.round((b + m) * 255);

                    colors.push(`rgb(${r}, ${g}, ${b})`);
                }

                return colors;
            }


            // Setting button in charts
            document.querySelectorAll('.settings-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const chartType = this.getAttribute('data-chart');
                    const container = document.getElementById(`${chartType}FlipContainer`);
                    container.classList.add('flipped');

                    // Disable the related reload button
                    const reloadBtn = document.querySelector(`.reload-btn[data-chart="${chartType}"]`);
                    if (reloadBtn) {
                        reloadBtn.disabled = true;
                        reloadBtn.classList.add('disabled');
                    }
                });
            });

            // Reload button in charts
            document.querySelectorAll('.reload-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const chartType = this.getAttribute('data-chart');
                    const originalHTML = this.innerHTML;
                    this.innerHTML = '<i class="bx bx-loader bx-spin"></i>';

                    const errorDiv = document.getElementById(chartType + 'Error');
                    if (errorDiv) {
                        errorDiv.textContent = '';
                        errorDiv.style.display = 'none';
                    }

                    const reloadFunctions = {
                        'subscription': fetchSubscriptionData,
                        'approval': fetchApprovalData,
                        'revenue': fetchRevenueData,
                        'plan': fetchPlanPopularityData,
                        'distribution': fetchSubscriptionDistributionData,
                        'timeline': fetchApprovalTimelineData
                    };

                    if (reloadFunctions[chartType]) {
                        reloadFunctions[chartType]();
                    } else {
                        loadAllData();
                    }

                    // Reset button after a short delay
                    setTimeout(() => {
                        this.innerHTML = originalHTML;
                    }, 1000);
                });
            });

            // Apply button in filter panels
            document.querySelectorAll('.btn-apply').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const container = this.closest('.flip-container');
                    const chartType = container.id.replace('FlipContainer', '');

                    const originalHTML = this.innerHTML;
                    this.innerHTML = 'Applying...';

                    const errorDiv = document.getElementById(chartType + 'Error');
                    if (errorDiv) {
                        errorDiv.textContent = '';
                        errorDiv.style.display = 'none';
                    }

                    const filterFunctions = {
                        'subscription': fetchSubscriptionData,
                        'approval': fetchApprovalData,
                        'revenue': fetchRevenueData,
                        'plan': fetchPlanPopularityData,
                        'distribution': fetchSubscriptionDistributionData,
                        'timeline': fetchApprovalTimelineData
                    };

                    if (filterFunctions[chartType]) {
                        filterFunctions[chartType]();
                    }

                    // Close panel and reset button
                    setTimeout(() => {
                        container.classList.remove('flipped');
                        this.innerHTML = originalHTML;

                        const reloadBtn = document.querySelector(`.reload-btn[data-chart="${chartType}"]`);
                        if (reloadBtn) {
                            reloadBtn.disabled = false;
                            reloadBtn.classList.remove('disabled');
                        }
                    }, 500);
                });
            });

            // Cancel button in filter panels
            document.querySelectorAll('.btn-cancel').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const container = this.closest('.flip-container');
                    container.classList.remove('flipped');

                    const chartType = container.id.replace('FlipContainer', '');
                    const reloadBtn = document.querySelector(`.reload-btn[data-chart="${chartType}"]`);
                    if (reloadBtn) {
                        reloadBtn.disabled = false;
                        reloadBtn.classList.remove('disabled');
                    }
                });
            });

            document.getElementById('detailButtonChart2').addEventListener('click', function(e) {
                e.preventDefault();

                const organizationSelect = document.getElementById('filterOrganizationChart2');
                const selectedOrganization = organizationSelect ? organizationSelect.value : null;

                const baseRoute = this.getAttribute('data-route');

                window.location.href = `${baseRoute}?organization_id=${encodeURIComponent(selectedOrganization)}&status=Under Review`;
            });

            const monthNames = [
                'january', 'february', 'march', 'april', 'may', 'june',
                'july', 'august', 'september', 'october', 'november', 'december'
            ];

            const formatToInputDate = (text) => {
                const [day, monthName, year] = text.split(' ');
                const monthIndex = monthNames.indexOf(monthName.toLowerCase()) + 1;
                const mm = String(monthIndex).padStart(2, '0');
                const dd = day.padStart(2, '0');
                return `${year}-${mm}-${dd}`;
            };

            const cancelButtonHandler = (chartNumber, config) => {
                document.querySelectorAll(`#btn-cancel-chart${chartNumber}`).forEach(button => {
                    button.addEventListener('click', function () {
                        const container = document.querySelector(`.filter-group[data-chart="${config.chartKey}"]`)?.closest('.filter-panel');
                        if (!container) return;

                        if (config.yearSpanId) {
                            const yearSelect = container.querySelector('select[id^="filterYear"]');
                            const yearText = document.getElementById(config.yearSpanId)?.textContent.trim();
                            if (yearSelect && yearText) yearSelect.value = yearText;
                        }

                        if (config.monthSpanId) {
                            const monthSelect = container.querySelector('select[id^="filterMonth"]');
                            const monthText = document.getElementById(config.monthSpanId)?.textContent.trim();
                            if (monthSelect && monthText) {
                                const [monthNameRaw, year] = monthText.split(' ');
                                const monthIndex = monthNames.indexOf(monthNameRaw.trim().toLowerCase()) + 1;
                                monthSelect.value = String(monthIndex).padStart(2, '0');

                                const yearSelect = container.querySelector('select[id^="filterYear"]');
                                if (yearSelect) yearSelect.value = year;
                            }
                        }

                        if (config.startSpanId && config.endSpanId) {
                            const startInput = container.querySelector('input[id^="filterStart"]');
                            const endInput = container.querySelector('input[id^="filterEnd"]');
                            const startText = document.getElementById(config.startSpanId)?.textContent.trim();
                            const endText = document.getElementById(config.endSpanId)?.textContent.trim();

                            if (startInput && startText) startInput.value = formatToInputDate(startText);
                            if (endInput && endText) endInput.value = formatToInputDate(endText);
                        }

                        if (config.selectPrefix && config.spanId) {
                            const spanText = document.getElementById(config.spanId)?.textContent.trim();
                            const select = container.querySelector(`select[id^="${config.selectPrefix}"]`);
                            if (select && spanText) {
                                for (let option of select.options) {
                                    if (option.text.trim().toLowerCase() === spanText.toLowerCase()) {
                                        select.value = option.value;
                                        break;
                                    }
                                }
                            }
                        }
                    });
                });
            };

            cancelButtonHandler(1, {
                chartKey: 'subscription',
                monthSpanId: 'currentMonthChart1',
                spanId: 'currentPlanChart1',
                selectPrefix: 'filterPlan'
            });

            cancelButtonHandler(2, {
                chartKey: 'approval',
                monthSpanId: 'currentMonthChart2',
                spanId: 'currentOrganizationChart2',
                selectPrefix: 'filterOrganization'
            });

            cancelButtonHandler(3, {
                chartKey: 'revenue',
                yearSpanId: 'currentYearChart3',
                spanId: 'currentPlanChart3',
                selectPrefix: 'filterPlan'
            });

            cancelButtonHandler(4, {
                chartKey: 'plan',
                startSpanId: 'currentStartChart4',
                endSpanId: 'currentEndChart4'
            });

            cancelButtonHandler(5, {
                chartKey: 'distribution',
                yearSpanId: 'currentYearChart5',
                spanId: 'currentPlanChart5',
                selectPrefix: 'filterPlan'
            });

            cancelButtonHandler(6, {
                chartKey: 'timeline',
                yearSpanId: 'currentYearChart6',
                spanId: 'currentOrganizationChart6',
                selectPrefix: 'filterOrganization'
            });

        });
    </script>

@endpush
