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
        .currentSource,
        .currentMembership,
        .currentName,
        .currentStart,
        .currentEnd {
            font-size: 12px;
            color: var(--sidenavbar-text-color);
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
                            <h3 class="dashboard_Header">Dashboard</h3>
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
                                                            <div id="occupancyError"
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
                                            <h3>Membership Subscriptions</h3>
                                            <div class="card-actions">
                                                <span class="currentMonth" id="currentMonthChart2"></span>
                                                <span class="currentMembership" id="currentMembershipChart2">All Memberships</span>
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
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="flip-container" id="membershipFlipContainer">
                                                <div class="flipper">
                                                    <div class="chart-container">
                                                        <div class="data-grid">
                                                            <div class="data-item" data-type="active">
                                                                <div class="data-value" id="activeMemberships">0</div>
                                                                <div class="data-label">Active</div>
                                                                <div class="data-trend"><span>-</span></div>
                                                            </div>
                                                            <div class="data-item" data-type="expired">
                                                                <div class="data-value" id="expiredMemberships">0</div>
                                                                <div class="data-label">Expired</div>
                                                                <div class="data-trend"><span>-</span></div>
                                                            </div>
                                                            <div class="data-item" data-type="usage">
                                                                <div class="data-value" id="usage">0%</div>
                                                                <div class="data-label">Usage</div>
                                                            </div>
                                                        </div>
                                                        <div class="mini-chart-container">
                                                            <canvas id="membershipTrendChart"></canvas>
                                                            <div id="membershipError"
                                                                 class="text-center text-danger"
                                                                 style="display: none; position: absolute; top: 45%; left: 50%; transform: translate(-50%, -50%); z-index: 2;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="filter-panel">
                                                        <h5>Membership Subscriptions Filters</h5>
                                                        <div class="filter-group" data-chart="membership">
                                                            <label for="filterYear">Year</label>
                                                            <select id="filterYear" class="form-select">
                                                                @for ($y = now()->year; $y >= now()->year - 5; $y--)
                                                                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                                                @endfor
                                                            </select>
                                                        </div>

                                                        <div class="filter-group" data-chart="membership">
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

                                                        <div class="filter-group" data-chart="membership">
                                                            <label for="filterMembership">Select Membership</label>
                                                            <select id="filterMembership" class="form-select" {{ empty($memberships) || $memberships->isEmpty() ? 'disabled' : '' }}>
                                                                <option value="">All Memberships</option>
                                                                @if (!empty($memberships) && $memberships->isNotEmpty())
                                                                    @foreach ($memberships as $membership)
                                                                        <option value="{{ $membership->id }}">{{ $membership->name }}</option>
                                                                    @endforeach
                                                                @else
                                                                    <option disabled>No Memberships available</option>
                                                                @endif
                                                            </select>
                                                        </div>

                                                        <div class="filter-actions">
                                                            <button class="btn-filter btn-cancel" id="btn-cancel-chart2">Cancel</button>
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
                                                        <div id="unitStatusError"
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
                                                <span class="currentStart" id="currentStartChart4">{{ strtoupper(now()->subDays(30)->format('d F Y')) }}</span>
                                                <span class="separator"> - </span>
                                                <span class="currentEnd" id="currentEndChart4">{{ strtoupper(now()->format('d F Y')) }}</span>
                                                <span class="currentBuilding" id="currentBuildingChart4">All Buildings</span>
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
                                                        <div id="staffError"
                                                             class="text-center text-danger"
                                                             style="display: none; position: absolute; top: 45%; left: 50%; transform: translate(-50%, -50%); z-index: 2;">
                                                        </div>
                                                    </div>
                                                    <div class="filter-panel">
                                                        <h5>Staff Distribution Filters</h5>
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

                                                        <div class="filter-group" data-chart="staff">
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

                            <!-- Second Charts Row -->
                            <div class="row">
                                <!-- Monthly Income vs Expenses -->
                                <div class="col-md-6 mb-4">
                                    <div class="chart-card">
                                        <div class="chart-header">
                                            <h4>Monthly Income vs Expenses</h4>
                                            <div class="card-actions">
                                                <span class="currentYear" id="currentYearChart5">{{ now()->year }}</span>
                                                <span class="currentSource" id="currentSourceChart5">All Sources</span>
                                                <span class="currentName" id="currentNameChart5"></span>
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
                                                        <div id="incomeExpenseError"
                                                             class="text-center text-danger"
                                                             style="display: none; position: absolute; top: 45%; left: 50%; transform: translate(-50%, -50%); z-index: 2;">
                                                        </div>
                                                    </div>
                                                    <div class="filter-panel">
                                                        <h5>Income vs Expenses Filters</h5>
                                                        <div class="filter-group" data-chart="incomeExpense">
                                                            <label for="filterYear">Year</label>
                                                            <select id="filterYear" class="form-select">
                                                                @for ($y = now()->year; $y >= now()->year - 5; $y--)
                                                                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                                                @endfor
                                                            </select>
                                                        </div>

                                                        <div class="filter-group" data-chart="incomeExpense">
                                                            <label for="filterSource">Select Source</label>
                                                            <select id="filterSource" class="form-select">
                                                                <option value="">All Sources</option>
                                                                <option value="Rent">Rent</option>
                                                                <option value="Sale">Sale</option>
                                                                <option value="Membership">Membership</option>
                                                                <option value="Facility">Facility</option>
                                                                <option value="Request">Maintenance Request</option>
                                                                @if(auth()->user()->role_id == 2)
                                                                    <option value="Charges">Platform charges</option>
                                                                @endif
                                                            </select>
                                                        </div>

                                                        <div class="filter-group" data-chart="incomeExpense" style="display: none;">
                                                            <label for="filterMembership">Select Membership</label>
                                                            <select id="filterMembership" class="form-select" {{ empty($memberships) || $memberships->isEmpty() ? 'disabled' : '' }}>
                                                                <option value="">All Memberships</option>
                                                                @if (!empty($memberships) && $memberships->isNotEmpty())
                                                                    @foreach ($memberships as $membership)
                                                                        <option value="{{ $membership->id }}">{{ $membership->name }}</option>
                                                                    @endforeach
                                                                @else
                                                                    <option disabled>No Memberships available</option>
                                                                @endif
                                                            </select>
                                                        </div>

                                                        <div class="filter-group" data-chart="incomeExpense" style="display: none;">
                                                            <label for="filterUnit">Select Unit</label>
                                                            <select id="filterUnit" class="form-select" {{ empty($units) || $units->isEmpty() ? 'disabled' : '' }}>
                                                                <option value="">All Units</option>
                                                                @if (!empty($units) && $units->isNotEmpty())
                                                                    @foreach ($units as $unit)
                                                                        <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                                                    @endforeach
                                                                @else
                                                                    <option disabled>No Units available</option>
                                                                @endif
                                                            </select>
                                                        </div>

                                                        <div class="filter-group" data-chart="incomeExpense" style="display: none;">
                                                            <label for="filterFacility">Select Facility</label>
                                                            <select id="filterFacility" class="form-select" {{ empty($membershipsUnits) || $membershipsUnits->isEmpty() ? 'disabled' : '' }}>
                                                                <option value="">All Facilities</option>
                                                                @if (!empty($membershipsUnits) && $membershipsUnits->isNotEmpty())
                                                                    @foreach ($membershipsUnits as $membershipsUnit)
                                                                        <option value="{{ $membershipsUnit->id }}">{{ $membershipsUnit->unit_name }}</option>
                                                                    @endforeach
                                                                @else
                                                                    <option disabled>No Facilities Available</option>
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

                                <!-- Membership Plan Usage -->
                                <div class="col-md-6 mb-4">
                                    <div class="chart-card">
                                        <div class="chart-header">
                                            <h4>Membership Distribution</h4>
                                            <div class="card-actions">
                                                <span class="currentMonth" id="currentMonthChart6"></span>
                                                <span class="legend-item"><i class="bx bx-pie-chart-alt"></i> By Subscriptions</span>
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
                                                        <div id="membershipPlanError"
                                                             class="text-center text-danger"
                                                             style="display: none; position: absolute; top: 45%; left: 50%; transform: translate(-50%, -50%); z-index: 2;">
                                                        </div>
                                                    </div>
                                                    <div class="filter-panel">
                                                        <h5>Membership Plan Filters</h5>
                                                        <div class="filter-group" data-chart="membershipPlan">
                                                            <label for="filterYear">Year</label>
                                                            <select id="filterYear" class="form-select">
                                                                @for ($y = now()->year; $y >= now()->year - 5; $y--)
                                                                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                                                @endfor
                                                            </select>
                                                        </div>

                                                        <div class="filter-group" data-chart="membershipPlan">
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
            let unitOccupancyChart, membershipTrendChart, unitStatusChart,
                staffDistributionChart, incomeExpenseChart, membershipPlanChart;

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
                    type: 'doughnut',
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
                fetchMembershipSubscriptionsData();
                fetchUnitStatusData();
                fetchStaffDistributionData();
                fetchIncomeExpenseData();
                fetchMembershipDistributionData();
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

            // 1. Fetch Unit Occupancy Data
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
                        showChartError(error, unitOccupancyChart, 'occupancyError');
                    });
            }

            // 2. Fetch Membership Subscriptions
            function fetchMembershipSubscriptionsData() {
                const filterPanel = document.querySelector('.filter-group[data-chart="membership"]').closest('.filter-panel');
                const yearSelect = filterPanel.querySelector('select[id^="filterYear"]');
                const monthSelect = filterPanel.querySelector('select[id^="filterMonth"]');
                const membershipSelect = filterPanel.querySelector('select[id^="filterMembership"]');


                const year = yearSelect ? yearSelect.value : null;
                const month = monthSelect ? monthSelect.value : null;
                const selectedMembership = membershipSelect ? membershipSelect.value : null;
                const selectedMonth = `${year}-${month}`;

                params = {}
                params.month = selectedMonth;
                params.membership = selectedMembership;

                const queryString = new URLSearchParams(params).toString();
                fetch(`{{ route('owner_manager_dashboard.membership.subscription') }}?${queryString}`, {
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
                        document.getElementById('usage').textContent = `${data.usage || '0'}%`;

                        // Handle growth arrows
                        updateGrowthTrend('active', data.growth.active);
                        updateGrowthTrend('expired', data.growth.expired);

                        membershipTrendChart.data.labels = ['Active', 'Expired'];
                        membershipTrendChart.data.datasets = [{
                            data: [data.active || 0, data.expired || 0],
                            backgroundColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)'],
                            borderWidth: 0
                        }];

                        // Updating the Labels
                        let membershipName = membershipSelect.options[membershipSelect.selectedIndex].text || null;

                        const dateObj = new Date(`${selectedMonth}-01`);
                        const formattedDate = new Intl.DateTimeFormat('en-US', {
                            year: 'numeric',
                            month: 'long'
                        }).format(dateObj);

                        const [month, year] = formattedDate.split(' ');

                        document.getElementById('currentMonthChart2').textContent = `${month.toUpperCase()} ${year}`;
                        document.getElementById('currentMembershipChart2').textContent = membershipName;

                        // Update Chart
                        membershipTrendChart.update();
                    })
                    .catch(error => {
                        showChartError(error, membershipTrendChart, 'membershipError');
                    });
            }

            // 3. Fetch Unit Status Data
            function fetchUnitStatusData() {
                const filterPanel = document.querySelector('.filter-group[data-chart="unitStatus"]').closest('.filter-panel');
                const yearSelect = filterPanel.querySelector('select[id^="filterYear"]');
                const buildingSelect = filterPanel.querySelector('select[id^="filterBuilding"]');


                const selectedYear = yearSelect ? yearSelect.value : null;
                const selectedBuilding = buildingSelect ? buildingSelect.value : null;

                params = {}
                params.year = selectedYear;
                params.building = selectedBuilding;

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

                        // Updating the Labels
                        let buildingName = buildingSelect.options[buildingSelect.selectedIndex].text || null;

                        document.getElementById('currentYearChart3').textContent = selectedYear;
                        document.getElementById('currentBuildingChart3').textContent = buildingName;

                        // Update Chart
                        unitStatusChart.update();
                    })
                    .catch(error => {
                        showChartError(error, unitStatusChart, 'unitStatusError');
                    });
            }

            // 4. Fetch Staff Distribution Data
            function fetchStaffDistributionData() {
                const filterPanel = document.querySelector('.filter-group[data-chart="staff"]').closest('.filter-panel');
                const startInput = filterPanel.querySelector('input[id^="filterStart"]');
                const endInput = filterPanel.querySelector('input[id^="filterEnd"]');
                const buildingSelect = filterPanel.querySelector('select[id^="filterBuilding"]');

                const startDate = new Date(startInput.value);
                const endDate = new Date(endInput.value);
                const selectedBuilding = buildingSelect ? buildingSelect.value : null;

                if (startInput.value && endInput.value && startDate > endDate) {
                    alert("Start Date cannot be after End Date.");
                    return false;
                }

                params = {}
                params.start = startInput.value;
                params.end = endInput.value;
                params.building = selectedBuilding;

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
                        staffDistributionChart.data.labels = data.labels || [];
                        staffDistributionChart.data.datasets = [{
                            data: data.data || [],
                            backgroundColor: generateColors(staffDistributionChart.data.labels.length),
                            borderWidth: 0
                        }];

                        // Updating the Labels
                        let buildingName = buildingSelect.options[buildingSelect.selectedIndex].text || null;

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
                        document.getElementById('currentBuildingChart4').textContent = buildingName;

                        // Update Chart
                        staffDistributionChart.update();
                    })
                    .catch(error => {
                        showChartError(error, staffDistributionChart, 'staffError');
                    });
            }

            // 5. Fetch Income vs Expense Data
            function fetchIncomeExpenseData() {
                const filterPanel = document.querySelector('.filter-group[data-chart="incomeExpense"]').closest('.filter-panel');
                const yearSelect = filterPanel.querySelector('select[id^="filterYear"]');
                const sourceSelect = filterPanel.querySelector('select[id^="filterSource"]');
                const membershipSelect = filterPanel.querySelector('select[id^="filterMembership"]');
                const unitSelect = filterPanel.querySelector('select[id^="filterUnit"]');
                const facilitySelect = filterPanel.querySelector('select[id^="filterFacility"]');

                const selectedYear = yearSelect ? yearSelect.value : null;
                const selectedSource = sourceSelect ? sourceSelect.value : null;

                let selectedSourceId = null;
                let selectedSourceName = '';

                if (selectedSource === 'Membership') {
                    if (membershipSelect) {
                        selectedSourceId = membershipSelect.value || null;
                        selectedSourceName = membershipSelect.options[membershipSelect.selectedIndex]?.text || null;
                    }
                } else if (selectedSource === 'Rent' || selectedSource === 'Sale' || selectedSource === 'Request') {
                    if (unitSelect) {
                        selectedSourceId = unitSelect.value || null;
                        selectedSourceName = unitSelect.options[unitSelect.selectedIndex]?.text || null;
                    }
                } else if (selectedSource === 'Facility') {
                    if (facilitySelect) {
                        selectedSourceId = facilitySelect.value || null;
                        selectedSourceName = facilitySelect.options[facilitySelect.selectedIndex]?.text || null;
                    }
                }

                params = {}
                params.year = selectedYear;
                params.source = selectedSource;

                if (selectedSourceId !== null) {
                    params.source_id = selectedSourceId;
                }

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

                        // Updating the Labels
                        let source = sourceSelect.options[sourceSelect.selectedIndex].text || null;

                        document.getElementById('currentYearChart5').textContent = selectedYear;
                        document.getElementById('currentSourceChart5').textContent = source;
                        document.getElementById('currentNameChart5').textContent = selectedSourceName;

                        // Update Chart
                        incomeExpenseChart.update();
                    })
                    .catch(error => {
                        showChartError(error, incomeExpenseChart, 'incomeExpenseError');
                    });
            }

            // 6. Fetch Membership Distribution Data
            function fetchMembershipDistributionData() {
                const filterPanel = document.querySelector('.filter-group[data-chart="membershipPlan"]').closest('.filter-panel');
                const yearSelect = filterPanel.querySelector('select[id^="filterYear"]');
                const monthSelect = filterPanel.querySelector('select[id^="filterMonth"]');


                const year = yearSelect ? yearSelect.value : null;
                const month = monthSelect ? monthSelect.value : null;
                const selectedMonth = `${year}-${month}`;

                params = {}
                params.month = selectedMonth;

                const queryString = new URLSearchParams(params).toString();
                fetch(`{{ route('owner_manager_dashboard.membership.subscription.distribution') }}?${queryString}`, {
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
                        membershipPlanChart.data.labels = data.labels || [];
                        membershipPlanChart.data.datasets = [{
                            data: data.values || [],
                            backgroundColor: generateColors(membershipPlanChart.data.labels.length),
                            borderWidth: 0
                        }];

                        // Updating the Labels
                        const dateObj = new Date(`${selectedMonth}-01`);
                        const formattedDate = new Intl.DateTimeFormat('en-US', {
                            year: 'numeric',
                            month: 'long'
                        }).format(dateObj);

                        const [month, year] = formattedDate.split(' ');

                        document.getElementById('currentMonthChart6').textContent = `${month.toUpperCase()} ${year}`;

                        // Update Chart
                        membershipPlanChart.update();
                    })
                    .catch(error => {
                        showChartError(error, membershipPlanChart, 'membershipPlanError');
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
                const isBadGrowth = ['available', 'expired'].includes(type) ? isPositive : isNegative;

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
                        'occupancy': fetchUnitOccupancyData,
                        'membership': fetchMembershipSubscriptionsData,
                        'unitStatus': fetchUnitStatusData,
                        'staff': fetchStaffDistributionData,
                        'incomeExpense': fetchIncomeExpenseData,
                        'membershipPlan': fetchMembershipDistributionData
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
                        'occupancy': fetchUnitOccupancyData,
                        'membership': fetchMembershipSubscriptionsData,
                        'unitStatus': fetchUnitStatusData,
                        'staff': fetchStaffDistributionData,
                        'incomeExpense': fetchIncomeExpenseData,
                        'membershipPlan': fetchMembershipDistributionData
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

            // To Display the units or memberships on the change of source
            document.getElementById('filterSource').addEventListener('change', () => {
                const sourceSelect = document.getElementById('filterSource');

                const filterPanel = document.querySelector('.filter-group[data-chart="incomeExpense"]').closest('.filter-panel');
                const membershipGroup = filterPanel.querySelector('.filter-group select[id^="filterMembership"]').closest('.filter-group');
                const unitGroup = filterPanel.querySelector('.filter-group select[id^="filterUnit"]').closest('.filter-group');
                const facilityGroup = filterPanel.querySelector('select[id^="filterFacility"]').closest('.filter-group');

                const selectedValue = sourceSelect.value;

                membershipGroup.style.display = 'none';
                unitGroup.style.display = 'none';
                facilityGroup.style.display = 'none';

                if (selectedValue === 'Membership') {
                    membershipGroup.style.display = '';
                } else if (selectedValue === 'Rent' || selectedValue === 'Sale' || selectedValue === 'Request') {
                    unitGroup.style.display = '';
                } else if (selectedValue === 'Facility') {
                    facilityGroup.style.display = '';
                }
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
                chartKey: 'occupancy',
                monthSpanId: 'currentMonthChart1',
                spanId: 'currentBuildingChart1',
                selectPrefix: 'filterBuilding'
            });

            cancelButtonHandler(2, {
                chartKey: 'membership',
                monthSpanId: 'currentMonthChart2',
                spanId: 'currentMembershipChart2',
                selectPrefix: 'filterMembership'
            });

            cancelButtonHandler(3, {
                chartKey: 'unitStatus',
                yearSpanId: 'currentYearChart3',
                spanId: 'currentBuildingChart3',
                selectPrefix: 'filterBuilding'
            });

            cancelButtonHandler(4, {
                chartKey: 'staff',
                startSpanId: 'currentStartChart4',
                endSpanId: 'currentEndChart4',
                spanId: 'currentBuildingChart4',
                selectPrefix: 'filterBuilding'
            });

            cancelButtonHandler(5, {
                chartKey: 'incomeExpense',
                yearSpanId: 'currentYearChart5',
                spanId: 'currentSourceChart5',
                selectPrefix: 'filterSource'
            });

            cancelButtonHandler(6, {
                chartKey: 'membershipPlan',
                monthSpanId: 'currentMonthChart6'
            });

            document.getElementById('btn-cancel-chart5').addEventListener('click', () => {
                const filterPanel = document.querySelector('.filter-group[data-chart="incomeExpense"]')?.closest('.filter-panel');
                const membershipSelect = filterPanel.querySelector('select[id^="filterMembership"]');
                const unitSelect = filterPanel.querySelector('select[id^="filterUnit"]');
                const facilitySelect = filterPanel.querySelector('select[id^="filterFacility"]');

                const currentSource = document.getElementById('currentSourceChart5')?.textContent.trim();
                const currentName = document.getElementById('currentNameChart5')?.textContent.trim();

                if (currentSource === 'Membership') {
                    const option = [...membershipSelect.options].find(opt => opt.text.trim() === currentName);
                    if (option) membershipSelect.value = option.value;
                    membershipSelect.closest('.filter-group').style.display = '';
                    unitSelect.closest('.filter-group').style.display = 'none';
                    unitSelect.value = '';
                    facilitySelect.closest('.filter-group').style.display = 'none';
                    facilitySelect.value = '';

                } else if (currentSource === 'Rent' || currentSource === 'Sale' || currentSource === 'Maintenance Request') {
                    const option = [...unitSelect.options].find(opt => opt.text.trim() === currentName);
                    if (option) unitSelect.value = option.value;
                    unitSelect.closest('.filter-group').style.display = '';
                    membershipSelect.closest('.filter-group').style.display = 'none';
                    membershipSelect.value = '';
                    facilitySelect.closest('.filter-group').style.display = 'none';
                    facilitySelect.value = '';

                } else if (currentSource === 'Facility') {
                    const option = [...facilitySelect.options].find(opt => opt.text.trim() === currentName);
                    if (option) facilitySelect.value = option.value;
                    facilitySelect.closest('.filter-group').style.display = '';
                    membershipSelect.closest('.filter-group').style.display = 'none';
                    membershipSelect.value = '';
                    unitSelect.closest('.filter-group').style.display = 'none';
                    unitSelect.value = '';

                } else {
                    membershipSelect.closest('.filter-group').style.display = 'none';
                    membershipSelect.value = '';
                    unitSelect.closest('.filter-group').style.display = 'none';
                    unitSelect.value = '';
                    facilitySelect.closest('.filter-group').style.display = 'none';
                    facilitySelect.value = '';

                }

            });

        });
    </script>
@endpush
