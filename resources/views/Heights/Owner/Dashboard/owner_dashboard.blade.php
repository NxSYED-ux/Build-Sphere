@extends('layouts.app')

@section('title', 'Owner Dashboard')

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
            font-family: 'Inter', sans-serif;
        }

        .dashboard_Header {
            font-family: 'Poppins', sans-serif;
            font-size: 24px;
            font-weight: 600;
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
            margin-bottom: 20px;
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

        .bg-gradient-purple {
            background: linear-gradient(135deg, #6f42c1, #9b6bcc);
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

        .currentMonth,
        .currentYear,
        .currentBuilding,
        .currentUnit,
        .currentMembership,
        .currentStart,
        .currentEnd {
            font-size: 12px;
            color: var(--sidenavbar-text-color);
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
            font-size: 14px;
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
        }

        .chart-header h4 {
            font-size: 16px;
            font-weight: 600;
            margin: 0;
            color: var(--sidenavbar-text-color);
        }

        .legend-item i {
            margin-right: 5px;
            font-size: 14px;
        }

        .legend-item.rented i {
            color: #4BC0C0;
        }

        .legend-item.sold i {
            color: #FF6384;
        }

        .legend-item.available i {
            color: #FFCD56;
        }

        .legend-item.active i {
            color: #4BC0C0;
        }

        .legend-item.expired i {
            color: #FF6384;
        }

        .legend-item.managers i {
            color: #36A2EB;
        }

        .legend-item.staff i {
            color: #9966FF;
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

        .chart-btn .tooltip {
            visibility: hidden;
            width: 120px;
            background-color: #555;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 12px;
            font-weight: normal;
        }

        .chart-btn:hover .tooltip {
            visibility: visible;
            opacity: 1;
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
            /*padding: 20px;*/
        }

        .content-wrapper {
            padding: 15px;
        }
    </style>
@endpush

@section('content')
    <!-- Top Navigation -->
    <x-Owner.top-navbar :searchVisible="true"/>

    <!-- Side Navigation -->
    <x-Owner.side-navbar :openSections="['Dashboard']"/>
    <x-error-success-model />

    <div id="main" style="margin-top: 35px;">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="content-wrapper" style="min-height: 751px;">
                        <section class="content-header mt-3">
                            <h3 class="dashboard_Header">Owner Dashboard</h3>
                        </section>

                        <section class="content">
                            <!-- Stats Cards Row -->
                            <div class="row my-3">
                                <!-- Total Buildings Card -->
                                <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                                    <div class="stats-card bg-gradient-primary">
                                        <div class="icon-container">
                                            <i class="bx bx-buildings"></i>
                                        </div>
                                        <div class="stats-content">
                                            <h3 id="totalBuildings">0</h3>
                                            <p>Total Buildings</p>
                                        </div>
                                        <div class="progress-indicator">
                                            <div class="progress-bar" style="width: 45%"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Total Units Card -->
                                <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                                    <div class="stats-card bg-gradient-success">
                                        <div class="icon-container">
                                            <i class="bx bxs-home"></i>
                                        </div>
                                        <div class="stats-content">
                                            <h3 id="totalUnits">0</h3>
                                            <p>Total Units</p>
                                        </div>
                                        <div class="progress-indicator">
                                            <div class="progress-bar" style="width: 60%"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Total Staff Card -->
                                <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                                    <div class="stats-card bg-gradient-warning">
                                        <div class="icon-container">
                                            <i class="bx bxs-user-detail"></i>
                                        </div>
                                        <div class="stats-content">
                                            <h3 id="totalStaff">0</h3>
                                            <p>Total Staff</p>
                                        </div>
                                        <div class="progress-indicator">
                                            <div class="progress-bar" style="width: 75%"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Total Revenue Card -->
                                <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                                    <div class="stats-card bg-gradient-purple">
                                        <div class="icon-container">
                                            <i class="bx bx-money"></i>
                                        </div>
                                        <div class="stats-content">
                                            <h3 id="totalRevenue">PKR 0.00</h3>
                                            <p>Monthly Revenue</p>
                                        </div>
                                        <div id="revenue-progress" class="progress-indicator">
                                            <div class="progress-bar" style="width: 90%"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Total Expense Card -->
                                <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                                    <div class="stats-card bg-gradient-success">
                                        <div class="icon-container">
                                            <i class="bx bx-credit-card"></i>
                                        </div>
                                        <div class="stats-content">
                                            <h3 id="totalExpense">PKR 0.00</h3>
                                            <p>Monthly Expense</p>
                                        </div>
                                        <div id="expense-progress" class="progress-indicator">
                                            <div class="progress-bar" style="width: 90%"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Net Profit Card -->
                                <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                                    <div class="stats-card bg-gradient-info">
                                        <div class="icon-container">
                                            <i class="bx bx-line-chart"></i>
                                        </div>
                                        <div class="stats-content">
                                            <h3 id="netProfit">PKR 0.00</h3>
                                            <p>Monthly Net Profit</p>
                                        </div>
                                        <div id="profit-progress" class="progress-indicator">
                                            <div class="progress-bar" style="width: 90%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Cards Row -->
                            <div class="row my-3">
                                <!-- Unit Occupancy Summary -->
                                <div class="col-md-6 mb-4">
                                    <div class="advanced-data-card">
                                        <div class="card-header">
                                            <h3>Unit Occupancy</h3>
                                            <div class="card-actions">
                                                <span class="currentMonth" id="currentMonthChart1"></span>
                                                <span class="currentBuilding" id="currentBuildingChart1">All Buildings</span>
                                                <div class="chart-controls">
                                                    <button class="chart-btn reload-btn" data-chart="occupancy">
                                                        <i class="bx bx-refresh"></i>
                                                        <span class="tooltip">Reload Data</span>
                                                    </button>
                                                    <button class="chart-btn settings-btn" data-chart="occupancy">
                                                        <i class="bx bx-cog"></i>
                                                        <span class="tooltip">Chart Settings</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="flip-container" id="occupancyFlipContainer">
                                                <div class="flipper">
                                                    <div class="chart-container">
                                                        <div class="data-grid">
                                                            <div class="data-item" data-type="rented">
                                                                <div class="data-value" id="rentedUnits">0</div>
                                                                <div class="data-label">Rented</div>
                                                                <div class="data-trend"><span>-</span></div>
                                                            </div>
                                                            <div class="data-item" data-type="sold">
                                                                <div class="data-value" id="soldUnits">0</div>
                                                                <div class="data-label">Sold</div>
                                                                <div class="data-trend"><span>-</span></div>
                                                            </div>
                                                            <div class="data-item" data-type="available">
                                                                <div class="data-value" id="availableUnits">0</div>
                                                                <div class="data-label">Available</div>
                                                                <div class="data-trend"><span>-</span></div>
                                                            </div>
                                                        </div>
                                                        <div class="donut-chart-container">
                                                            <canvas id="unitOccupancyChart"></canvas>
                                                            <div id="unitOccupancyChartError"
                                                                 class="text-center text-danger"
                                                                 style="display: none; position: absolute; top: 45%; left: 50%; transform: translate(-50%, -50%); z-index: 2;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="filter-panel">
                                                        <h5>Unit Occupancy Filters</h5>
                                                        <div class="filter-group" data-chart="occupancy">
                                                            <label for="filterYear">Year</label>
                                                            <select id="filterYear" class="form-select">
                                                                @for ($y = now()->year; $y >= now()->year - 5; $y--)
                                                                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                                                @endfor
                                                            </select>
                                                        </div>

                                                        <div class="filter-group" data-chart="occupancy">
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

                                                        <div class="filter-group" data-chart="occupancy">
                                                            <label for="filterBuilding">Select Building</label>
                                                            <select id="filterBuilding" class="form-select" {{ empty($buildings) || $buildings->isEmpty() ? 'disabled' : '' }}>
                                                                <option value="">All Buildings</option>
                                                                @if (!empty($buildings) && $buildings->isNotEmpty())
                                                                    @foreach ($buildings as $building)
                                                                        <option value="{{ $building->id }}">{{ $building->name }}</option>
                                                                    @endforeach
                                                                @else
                                                                    <option disabled>No Building available</option>
                                                                @endif
                                                            </select>
                                                        </div>

                                                        <div class="filter-actions">
                                                            <button class="btn-filter btn-cancel" id="btn-cancel-chart1">Cancel</button>
                                                            <button class="btn-filter btn-apply">Apply Filters</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Membership Plans -->
                                <div class="col-md-6 mb-4">
                                    <div class="advanced-data-card">
                                        <div class="card-header">
                                            <h3>Membership Plans</h3>
                                            <div class="card-actions">
                                                <span class="currentDate"></span>
                                                <div class="chart-controls">
                                                    <button class="chart-btn reload-btn" data-chart="membership">
                                                        <i class="bx bx-refresh"></i>
                                                        <span class="tooltip">Reload Data</span>
                                                    </button>
                                                    <button class="chart-btn settings-btn" data-chart="membership">
                                                        <i class="bx bx-cog"></i>
                                                        <span class="tooltip">Chart Settings</span>
                                                    </button>
                                                </div>
                                                <button class="btn-details">Manage <i class="bx bx-chevron-right"></i></button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="flip-container" id="membershipFlipContainer">
                                                <div class="flipper">
                                                    <div class="chart-container">
                                                        <div class="data-grid">
                                                            <div class="data-item">
                                                                <div class="data-value" id="activeMemberships">0</div>
                                                                <div class="data-label">Active</div>
                                                                <div class="data-trend up">
                                                                    <i class="bx bx-up-arrow-alt"></i> 15%
                                                                </div>
                                                            </div>
                                                            <div class="data-item">
                                                                <div class="data-value" id="expiredMemberships">0</div>
                                                                <div class="data-label">Expired</div>
                                                                <div class="data-trend down">
                                                                    <i class="bx bx-down-arrow-alt"></i> 10%
                                                                </div>
                                                            </div>
                                                            <div class="data-item">
                                                                <div class="data-value" id="planUsage">0%</div>
                                                                <div class="data-label">Plan Usage</div>
                                                                <div class="data-trend up">
                                                                    <i class="bx bx-up-arrow-alt"></i> 5%
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mini-chart-container">
                                                            <canvas id="membershipTrendChart"></canvas>
                                                        </div>
                                                    </div>
                                                    <div class="filter-panel">
                                                        <h5>Membership Plans Filters</h5>
                                                        <div class="filter-group">
                                                            <label for="membershipDateRange">Date Range</label>
                                                            <select id="membershipDateRange" class="form-select">
                                                                <option value="7days">Last 7 Days</option>
                                                                <option value="30days" selected>Last 30 Days</option>
                                                                <option value="90days">Last 90 Days</option>
                                                                <option value="custom">Custom Range</option>
                                                            </select>
                                                        </div>
                                                        <div class="filter-group" id="membershipCustomRange" style="display: none;">
                                                            <label for="membershipStartDate">Start Date</label>
                                                            <input type="date" id="membershipStartDate">
                                                            <label for="membershipEndDate">End Date</label>
                                                            <input type="date" id="membershipEndDate">
                                                        </div>
                                                        <div class="filter-group">
                                                            <label for="membershipPlanType">Plan Type</label>
                                                            <select id="membershipPlanType" class="form-select">
                                                                <option value="all">All Plans</option>
                                                                <option value="basic">Basic</option>
                                                                <option value="premium">Premium</option>
                                                                <option value="enterprise">Enterprise</option>
                                                            </select>
                                                        </div>
                                                        <div class="filter-actions">
                                                            <button class="btn-filter btn-cancel">Cancel</button>
                                                            <button class="btn-filter btn-apply">Apply Filters</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Charts Row -->
                            <div class="row">
                                <!-- Unit Status Distribution -->
                                <div class="col-md-6 mb-4">
                                    <div class="chart-card">
                                        <div class="chart-header">
                                            <h4>Unit Status Distribution</h4>
                                            <div class="card-actions">
                                                <span class="currentYear" id="currentYearChart3">{{ now()->year }}</span>
                                                <span class="currentBuilding" id="currentBuildingChart3">All Buildings</span>
                                                <div class="chart-controls">
                                                    <button class="chart-btn reload-btn" data-chart="unitStatus">
                                                        <i class="bx bx-refresh"></i>
                                                        <span class="tooltip">Reload Data</span>
                                                    </button>
                                                    <button class="chart-btn settings-btn" data-chart="unitStatus">
                                                        <i class="bx bx-cog"></i>
                                                        <span class="tooltip">Chart Settings</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="flip-container" id="unitStatusFlipContainer">
                                                <div class="flipper">
                                                    <div class="chart-container">
                                                        <canvas id="unitStatusChart"></canvas>
                                                        <div id="unitStatusChartError"
                                                             class="text-center text-danger"
                                                             style="display: none; position: absolute; top: 45%; left: 50%; transform: translate(-50%, -50%); z-index: 2;">
                                                        </div>
                                                    </div>
                                                    <div class="filter-panel">
                                                        <h5>Unit Status Filters</h5>
                                                        <div class="filter-group" data-chart="unitStatus">
                                                            <label for="filterYear">Year</label>
                                                            <select id="filterYear" class="form-select">
                                                                @for ($y = now()->year; $y >= now()->year - 5; $y--)
                                                                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                                                @endfor
                                                            </select>
                                                        </div>

                                                        <div class="filter-group" data-chart="unitStatus">
                                                            <label for="filterBuilding">Select Building</label>
                                                            <select id="filterBuilding" class="form-select" {{ empty($buildings) || $buildings->isEmpty() ? 'disabled' : '' }}>
                                                                <option value="">All Buildings</option>
                                                                @if (!empty($buildings) && $buildings->isNotEmpty())
                                                                    @foreach ($buildings as $building)
                                                                        <option value="{{ $building->id }}">{{ $building->name }}</option>
                                                                    @endforeach
                                                                @else
                                                                    <option disabled>No Building available</option>
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

                                <!-- Staff Distribution -->
                                <div class="col-md-6 mb-4">
                                    <div class="chart-card">
                                        <div class="chart-header">
                                            <h4>Staff Distribution</h4>
                                            <div class="card-actions">
                                                <div class="chart-controls">
                                                    <button class="chart-btn reload-btn" data-chart="staff">
                                                        <i class="bx bx-refresh"></i>
                                                        <span class="tooltip">Reload Data</span>
                                                    </button>
                                                    <button class="chart-btn settings-btn" data-chart="staff">
                                                        <i class="bx bx-cog"></i>
                                                        <span class="tooltip">Chart Settings</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="flip-container" id="staffFlipContainer">
                                                <div class="flipper">
                                                    <div class="chart-container">
                                                        <canvas id="staffDistributionChart"></canvas>
                                                    </div>
                                                    <div class="filter-panel">
                                                        <h5>Staff Distribution Filters</h5>
                                                        <div class="filter-group">
                                                            <label for="staffDateRange">Date Range</label>
                                                            <select id="staffDateRange" class="form-select">
                                                                <option value="3months">Last 3 Months</option>
                                                                <option value="6months">Last 6 Months</option>
                                                                <option value="12months" selected>Last 12 Months</option>
                                                                <option value="custom">Custom Range</option>
                                                            </select>
                                                        </div>
                                                        <div class="filter-group" id="staffCustomRange" style="display: none;">
                                                            <label for="staffStartDate">Start Date</label>
                                                            <input type="date" id="staffStartDate">
                                                            <label for="staffEndDate">End Date</label>
                                                            <input type="date" id="staffEndDate">
                                                        </div>
                                                        <div class="filter-group">
                                                            <label for="staffBuilding">Building</label>
                                                            <select id="staffBuilding" class="form-select">
                                                                <option value="all">All Buildings</option>
                                                                <option value="building1">Building 1</option>
                                                                <option value="building2">Building 2</option>
                                                                <option value="building3">Building 3</option>
                                                            </select>
                                                        </div>
                                                        <div class="filter-actions">
                                                            <button class="btn-filter btn-cancel">Cancel</button>
                                                            <button class="btn-filter btn-apply">Apply Filters</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Second Charts Row -->
                            <div class="row">
                                <!-- Monthly Income vs Expenses -->
                                <div class="col-md-6 mb-4">
                                    <div class="chart-card">
                                        <div class="chart-header">
                                            <h4>Monthly Income vs Expenses</h4>
                                            <div class="card-actions">
                                                <div class="chart-controls">
                                                    <button class="chart-btn reload-btn" data-chart="incomeExpense">
                                                        <i class="bx bx-refresh"></i>
                                                        <span class="tooltip">Reload Data</span>
                                                    </button>
                                                    <button class="chart-btn settings-btn" data-chart="incomeExpense">
                                                        <i class="bx bx-cog"></i>
                                                        <span class="tooltip">Chart Settings</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="flip-container" id="incomeExpenseFlipContainer">
                                                <div class="flipper">
                                                    <div class="chart-container">
                                                        <canvas id="incomeExpenseChart"></canvas>
                                                    </div>
                                                    <div class="filter-panel">
                                                        <h5>Income vs Expenses Filters</h5>
                                                        <div class="filter-group">
                                                            <label for="incomeExpenseDateRange">Date Range</label>
                                                            <select id="incomeExpenseDateRange" class="form-select">
                                                                <option value="3months">Last 3 Months</option>
                                                                <option value="6months">Last 6 Months</option>
                                                                <option value="12months" selected>Last 12 Months</option>
                                                                <option value="custom">Custom Range</option>
                                                            </select>
                                                        </div>
                                                        <div class="filter-group" id="incomeExpenseCustomRange" style="display: none;">
                                                            <label for="incomeExpenseStartDate">Start Date</label>
                                                            <input type="date" id="incomeExpenseStartDate">
                                                            <label for="incomeExpenseEndDate">End Date</label>
                                                            <input type="date" id="incomeExpenseEndDate">
                                                        </div>
                                                        <div class="filter-group">
                                                            <label for="incomeExpenseBuilding">Building</label>
                                                            <select id="incomeExpenseBuilding" class="form-select">
                                                                <option value="all">All Buildings</option>
                                                                <option value="building1">Building 1</option>
                                                                <option value="building2">Building 2</option>
                                                                <option value="building3">Building 3</option>
                                                            </select>
                                                        </div>
                                                        <div class="filter-actions">
                                                            <button class="btn-filter btn-cancel">Cancel</button>
                                                            <button class="btn-filter btn-apply">Apply Filters</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Membership Plan Usage -->
                                <div class="col-md-6 mb-4">
                                    <div class="chart-card">
                                        <div class="chart-header">
                                            <h4>Membership Plan Usage</h4>
                                            <div class="card-actions">
                                                <div class="chart-controls">
                                                    <button class="chart-btn reload-btn" data-chart="membershipPlan">
                                                        <i class="bx bx-refresh"></i>
                                                        <span class="tooltip">Reload Data</span>
                                                    </button>
                                                    <button class="chart-btn settings-btn" data-chart="membershipPlan">
                                                        <i class="bx bx-cog"></i>
                                                        <span class="tooltip">Chart Settings</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="flip-container" id="membershipPlanFlipContainer">
                                                <div class="flipper">
                                                    <div class="chart-container">
                                                        <canvas id="membershipPlanChart"></canvas>
                                                    </div>
                                                    <div class="filter-panel">
                                                        <h5>Membership Plan Filters</h5>
                                                        <div class="filter-group">
                                                            <label for="membershipPlanDateRange">Date Range</label>
                                                            <select id="membershipPlanDateRange" class="form-select">
                                                                <option value="7days">Last 7 Days</option>
                                                                <option value="30days" selected>Last 30 Days</option>
                                                                <option value="90days">Last 90 Days</option>
                                                                <option value="custom">Custom Range</option>
                                                            </select>
                                                        </div>
                                                        <div class="filter-group" id="membershipPlanCustomRange" style="display: none;">
                                                            <label for="membershipPlanStartDate">Start Date</label>
                                                            <input type="date" id="membershipPlanStartDate">
                                                            <label for="membershipPlanEndDate">End Date</label>
                                                            <input type="date" id="membershipPlanEndDate">
                                                        </div>
                                                        <div class="filter-group">
                                                            <label for="membershipPlanStatus">Status</label>
                                                            <select id="membershipPlanStatus" class="form-select">
                                                                <option value="all">All Statuses</option>
                                                                <option value="active">Active</option>
                                                                <option value="expired">Expired</option>
                                                            </select>
                                                        </div>
                                                        <div class="filter-actions">
                                                            <button class="btn-filter btn-cancel">Cancel</button>
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
            let unitOccupancyChart, membershipTrendChart, unitStatusChart,
                staffDistributionChart, incomeExpenseChart, membershipPlanChart;

            //Current Month display
            const dateObj = new Date();
            const formattedDate = new Intl.DateTimeFormat('en-US', {
                year: 'numeric',
                month: 'long'
            }).format(dateObj);

            document.querySelectorAll('.currentMonth').forEach(el => {
                el.textContent = formattedDate;
            });

            // Initialize charts with empty data
            function initCharts() {
                // 1. Unit Occupancy Donut Chart
                const unitOccupancyCtx = document.getElementById('unitOccupancyChart').getContext('2d');
                unitOccupancyChart = new Chart(unitOccupancyCtx, {
                    type: 'doughnut',
                    data: { labels: [], datasets: [] },
                    options: { responsive: true, maintainAspectRatio: false }
                });

                // 2. Membership Trend Mini Chart
                const membershipTrendCtx = document.getElementById('membershipTrendChart').getContext('2d');
                membershipTrendChart = new Chart(membershipTrendCtx, {
                    type: 'line',
                    data: { labels: [], datasets: [] },
                    options: { responsive: true, maintainAspectRatio: false }
                });

                // 3. Unit Status Bar Chart
                const unitStatusCtx = document.getElementById('unitStatusChart').getContext('2d');
                unitStatusChart = new Chart(unitStatusCtx, {
                    type: 'bar',
                    data: { labels: [], datasets: [] },
                    options: { responsive: true, maintainAspectRatio: false }
                });

                // 4. Staff Distribution Pie Chart
                const staffDistributionCtx = document.getElementById('staffDistributionChart').getContext('2d');
                staffDistributionChart = new Chart(staffDistributionCtx, {
                    type: 'pie',
                    data: { labels: [], datasets: [] },
                    options: { responsive: true, maintainAspectRatio: false }
                });

                // 5. Income vs Expense Chart
                const incomeExpenseCtx = document.getElementById('incomeExpenseChart').getContext('2d');
                incomeExpenseChart = new Chart(incomeExpenseCtx, {
                    type: 'line',
                    data: { labels: [], datasets: [] },
                    options: { responsive: true, maintainAspectRatio: false }
                });

                // 6. Membership Plan Chart
                const membershipPlanCtx = document.getElementById('membershipPlanChart').getContext('2d');
                membershipPlanChart = new Chart(membershipPlanCtx, {
                    type: 'doughnut',
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
                fetchFinanceData();
                fetchUnitOccupancyData();
                fetchMembershipData();
                fetchUnitStatusData();
                fetchStaffDistributionData();
                fetchIncomeExpenseData();
                fetchMembershipPlanData();
            }

            // Fetch Stats Data (Total Buildings, Units, Staff)
            function fetchStatsData() {
                fetch(`{{ route('owner_manager_dashboard.stats') }}`, {
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
                        document.getElementById('totalBuildings').textContent = data.totalBuildings || '0';
                        document.getElementById('totalUnits').textContent = data.totalUnits || '0';
                        document.getElementById('totalStaff').textContent = data.totalStaff || '0';
                    })
                    .catch(error => console.error('Error fetching stats:', error));
            }

            // Fetch Finance Data (Monthly Revenue, Monthly Expense, Monthly Net Profit)
            function fetchFinanceData() {
                fetch(`{{ route('owner_manager_dashboard.finance.stats') }}`, {
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
                        document.getElementById('totalRevenue').textContent = `PKR ${(data.financialMetrics.total_revenue.value || 0).toLocaleString('en-PK', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }`;
                        document.getElementById('totalExpense').textContent = `PKR ${(data.financialMetrics.total_expenses.value || 0).toLocaleString('en-PK', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }`;
                        document.getElementById('netProfit').textContent = `PKR ${(data.financialMetrics.net_profit.value || 0).toLocaleString('en-PK', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }`;

                        updateGrowthBar(data.financialMetrics.total_revenue.change, '#revenue-progress .progress-bar');
                        updateGrowthBar(data.financialMetrics.total_expenses.change, '#expense-progress .progress-bar', true);
                        updateGrowthBar(data.financialMetrics.net_profit.change, '#profit-progress .progress-bar');

                        function updateGrowthBar(growthValue, barSelector, reverseColor = false) {
                            const progressBar = document.querySelector(barSelector);
                            if (!progressBar) return;

                            const width = Math.min(Math.abs(growthValue ?? 0), 100);
                            progressBar.style.width = `${width}%`;

                            const isPositive = growthValue >= 0;
                            progressBar.style.backgroundColor = reverseColor
                                ? (isPositive ? '#e57373' : 'white')
                                : (isPositive ? 'white' : '#e57373');
                        }
                    })
                    .catch(error => console.error('Error fetching stats:', error));
            }

            // 2. Fetch Unit Occupancy Data
            function fetchUnitOccupancyData() {
                const filterPanel = document.querySelector('.filter-group[data-chart="occupancy"]').closest('.filter-panel');
                const yearSelect = filterPanel.querySelector('select[id^="filterYear"]');
                const monthSelect = filterPanel.querySelector('select[id^="filterMonth"]');
                const buildingSelect = filterPanel.querySelector('select[id^="filterBuilding"]');


                const year = yearSelect ? yearSelect.value : null;
                const month = monthSelect ? monthSelect.value : null;
                const selectedBuilding = buildingSelect ? buildingSelect.value : null;
                const selectedMonth = `${year}-${month}`;

                params = {}
                params.month = selectedMonth;
                params.building = selectedBuilding;

                const queryString = new URLSearchParams(params).toString();
                fetch(`{{ route('owner_manager_dashboard.unit.occupancy') }}?${queryString}`, {
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
                        document.getElementById('rentedUnits').textContent = data.rented || '0';
                        document.getElementById('soldUnits').textContent = data.sold || '0';
                        document.getElementById('availableUnits').textContent = data.available || '0';

                        // Handle growth arrows
                        updateGrowthTrend('rented', data.growth.rented);
                        updateGrowthTrend('sold', data.growth.sold);
                        updateGrowthTrend('available', data.growth.available);

                        unitOccupancyChart.data.labels = ['Rented', 'Sold', 'Available'];
                        unitOccupancyChart.data.datasets = [{
                            data: [data.rented || 0, data.sold || 0, data.available || 0],
                            backgroundColor: ['#4BC0C0', '#FF6384', '#FFCD56'],
                            borderWidth: 0
                        }];

                        // Updating the Labels
                        let buildingName = buildingSelect.options[buildingSelect.selectedIndex].text || null;

                        const dateObj = new Date(`${selectedMonth}-01`);
                        const formattedDate = new Intl.DateTimeFormat('en-US', {
                            year: 'numeric',
                            month: 'long'
                        }).format(dateObj);

                        const [month, year] = formattedDate.split(' ');

                        document.getElementById('currentMonthChart1').textContent = `${month.toUpperCase()} ${year}`;
                        document.getElementById('currentBuildingChart1').textContent = buildingName;

                        // Update Chart
                        unitOccupancyChart.update();
                    })
                    .catch(error => {
                        showChartError(error, unitOccupancyChart, 'unitOccupancyChartError');
                    });
            }

            // 3. Fetch Membership Data
            function fetchMembershipData(params = {}) {
                const queryString = new URLSearchParams(params).toString();
                fetch(`{{ route('owner_manager_dashboard.membership.plans') }}?${queryString}`, {
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
                        document.getElementById('activeMemberships').textContent = data.active || '0';
                        document.getElementById('expiredMemberships').textContent = data.expired || '0';
                        document.getElementById('planUsage').textContent = `${data.usage || '0'}%`;

                        // Update membership trend chart
                        membershipTrendChart.data.labels = data.trend.labels || [];
                        membershipTrendChart.data.datasets = [{
                            label: 'New Memberships',
                            data: data.trend.values || [],
                            borderColor: '#184E83',
                            backgroundColor: 'rgba(24, 78, 131, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true
                        }];
                        membershipTrendChart.update();
                    })
                    .catch(error => console.error('Error fetching membership data:', error));
            }

            // 4. Fetch Unit Status Data
            function fetchUnitStatusData(params = {}) {
                const queryString = new URLSearchParams(params).toString();
                fetch(`{{ route('owner_manager_dashboard.unit.status') }}?${queryString}`, {
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
                        // Update unit status chart
                        unitStatusChart.data.labels = data.labels || [];
                        unitStatusChart.data.datasets = [
                            {
                                label: 'Rented',
                                data: data.rented || [],
                                backgroundColor: 'rgba(75, 192, 192, 0.7)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 0,
                                borderRadius: 4
                            },
                            {
                                label: 'Sold',
                                data: data.sold || [],
                                backgroundColor: 'rgba(255, 99, 132, 0.7)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 0,
                                borderRadius: 4
                            },
                            {
                                label: 'Available',
                                data: data.available || [],
                                backgroundColor: 'rgba(255, 205, 86, 0.7)',
                                borderColor: 'rgba(255, 205, 86, 1)',
                                borderWidth: 0,
                                borderRadius: 4
                            }
                        ];
                        unitStatusChart.update();
                    })
                    .catch(error => console.error('Error fetching unit status data:', error));
            }

            // 5. Fetch Staff Distribution Data
            function fetchStaffDistributionData(params = {}) {
                const queryString = new URLSearchParams(params).toString();
                fetch(`{{ route('owner_manager_dashboard.staff.distribution') }}?${queryString}`, {
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
                        // Update staff distribution chart
                        staffDistributionChart.data.labels = ['Managers', 'Other Staff'];
                        staffDistributionChart.data.datasets = [{
                            data: [data.managers || 0, data.staff || 0],
                            backgroundColor: ['#36A2EB', '#9966FF'],
                            borderWidth: 0
                        }];
                        staffDistributionChart.update();
                    })
                    .catch(error => console.error('Error fetching staff distribution data:', error));
            }

            // 6. Fetch Income vs Expense Data
            function fetchIncomeExpenseData(params = {}) {
                const queryString = new URLSearchParams(params).toString();
                fetch(`{{ route('owner_manager_dashboard.income.expense') }}?${queryString}`, {
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
                        // Update income vs expense chart
                        incomeExpenseChart.data.labels = data.labels || [];
                        incomeExpenseChart.data.datasets = [
                            {
                                label: 'Income',
                                data: data.income || [],
                                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 2,
                                tension: 0.3,
                                fill: true
                            },
                            {
                                label: 'Expenses',
                                data: data.expenses || [],
                                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 2,
                                tension: 0.3,
                                fill: true
                            }
                        ];
                        incomeExpenseChart.update();
                    })
                    .catch(error => console.error('Error fetching income vs expense data:', error));
            }

            // 7. Fetch Membership Plan Data
            function fetchMembershipPlanData(params = {}) {
                const queryString = new URLSearchParams(params).toString();
                fetch(`{{ route('owner_manager_dashboard.membership.plan.usage') }}?${queryString}`, {
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
                        // Update membership plan chart
                        membershipPlanChart.data.labels = data.labels || [];
                        membershipPlanChart.data.datasets = [{
                            data: data.values || [],
                            backgroundColor: ['#36A2EB', '#4BC0C0', '#9966FF', '#FFCE56'],
                            borderWidth: 0
                        }];
                        membershipPlanChart.update();
                    })
                    .catch(error => console.error('Error fetching membership plan data:', error));
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
                const isBadGrowth = ['available'].includes(type) ? isPositive : isNegative;

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
                        const tooltip = this.querySelector('.tooltip');
                        if (tooltip) {
                            tooltip.textContent = 'Data reloaded!';
                            setTimeout(() => {
                                tooltip.textContent = 'Reload Data';
                            }, 2000);
                        }
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

                            const tooltip = reloadBtn.querySelector('.tooltip');
                            if (tooltip) {
                                tooltip.textContent = 'Filters applied!';
                            }
                        }
                    }, 500);
                });
            });

            // Details buttons functionality
            document.querySelectorAll('.btn-details').forEach(button => {
                button.addEventListener('click', function() {
                    const cardTitle = this.closest('.card-header').querySelector('h3').textContent;
                    alert(`Viewing details for: ${cardTitle}`);
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
        });
    </script>
@endpush
