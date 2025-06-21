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

        .currentDate {
            font-size: 12px;
            color: var(--sidenavbar-text-color);
            margin-right: 15px;
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
            color: var(--primary);
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

        .chart-legend {
            display: flex;
            flex-wrap: wrap;
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
            color: var(--primary);
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

            .chart-legend {
                margin-top: 10px;
                flex-wrap: wrap;
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
                            <h3 class="dashboard_Header">Admin Dashboard</h3>
                        </section>

                        <section class="content">
                            <!-- Stats Cards Row -->
                            <div class="row my-3">
                                <!-- Total Organizations Card -->
                                <div class="col-md-3 col-sm-6 mb-4">
                                    <div class="stats-card bg-gradient-primary">
                                        <div class="icon-container">
                                            <i class="bx bxs-business"></i>
                                        </div>
                                        <div class="stats-content">
                                            <h3 id="totalOrganizations">0</h3>
                                            <p>Total Organizations</p>
                                        </div>
                                        <div class="progress-indicator">
                                            <div class="progress-bar" style="width: 75%"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Total Owners Card -->
                                <div class="col-md-3 col-sm-6 mb-4">
                                    <div class="stats-card bg-gradient-info">
                                        <div class="icon-container">
                                            <i class="bx bx-user"></i>
                                        </div>
                                        <div class="stats-content">
                                            <h3 id="totalOwners">0</h3>
                                            <p>Total Owners</p>
                                        </div>
                                        <div class="progress-indicator">
                                            <div class="progress-bar" style="width: 60%"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Buildings For Approval Card -->
                                <div class="col-md-3 col-sm-6 mb-4">
                                    <div class="stats-card bg-gradient-warning">
                                        <div class="icon-container">
                                            <i class="bx bx-buildings"></i>
                                        </div>
                                        <div class="stats-content">
                                            <h3 id="pendingApprovals">0</h3>
                                            <p>Pending Approvals</p>
                                        </div>
                                        <div class="progress-indicator">
                                            <div class="progress-bar" style="width: 45%"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Total Revenue Card -->
                                <div class="col-md-3 col-sm-6 mb-4">
                                    <div class="stats-card bg-gradient-success">
                                        <div class="icon-container">
                                            <i class="bx bx-dollar"></i>
                                        </div>
                                        <div class="stats-content">
                                            <h3 id="totalRevenue">$0</h3>
                                            <p>Total Revenue</p>
                                        </div>
                                        <div class="progress-indicator">
                                            <div class="progress-bar" style="width: 85%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Cards Row -->
                            <div class="row my-3">
                                <!-- Subscription Plans Summary -->
                                <div class="col-md-6 mb-4">
                                    <div class="advanced-data-card">
                                        <div class="card-header">
                                            <h3>Subscription Plans</h3>
                                            <div class="card-actions">
                                                <span class="currentDate"></span>
                                                <div class="chart-controls">
                                                    <button class="chart-btn reload-btn" data-chart="subscription">
                                                        <i class="bx bx-refresh"></i>
                                                        <span class="tooltip">Reload Data</span>
                                                    </button>
                                                    <button class="chart-btn settings-btn" data-chart="subscription">
                                                        <i class="bx bx-cog"></i>
                                                        <span class="tooltip">Chart Settings</span>
                                                    </button>
                                                </div>
                                                <button class="btn-details">View All <i class="bx bx-chevron-right"></i></button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="flip-container" id="subscriptionFlipContainer">
                                                <div class="flipper">
                                                    <div class="chart-container">
                                                        <div class="data-grid">
                                                            <div class="data-item">
                                                                <div class="data-value" id="activeSubscriptions">0</div>
                                                                <div class="data-label">Active</div>
                                                                <div class="data-trend up">
                                                                    <i class="bx bx-up-arrow-alt"></i> 12%
                                                                </div>
                                                            </div>
                                                            <div class="data-item">
                                                                <div class="data-value" id="expiredSubscriptions">0</div>
                                                                <div class="data-label">Expired</div>
                                                                <div class="data-trend down">
                                                                    <i class="bx bx-down-arrow-alt"></i> 5%
                                                                </div>
                                                            </div>
                                                            <div class="data-item">
                                                                <div class="data-value" id="trialSubscriptions">0</div>
                                                                <div class="data-label">Trial</div>
                                                                <div class="data-trend up">
                                                                    <i class="bx bx-up-arrow-alt"></i> 8%
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mini-chart-container pb-0 mb-0">
                                                            <canvas id="subscriptionTrendChart"></canvas>
                                                        </div>
                                                    </div>
                                                    <div class="filter-panel">
                                                        <h5>Subscription Plans Filters</h5>
                                                        <div class="filter-group">
                                                            <label for="subscriptionDateRange">Date Range</label>
                                                            <select id="subscriptionDateRange" class="form-select">
                                                                <option value="7days">Last 7 Days</option>
                                                                <option value="30days" selected>Last 30 Days</option>
                                                                <option value="90days">Last 90 Days</option>
                                                                <option value="custom">Custom Range</option>
                                                            </select>
                                                        </div>
                                                        <div class="filter-group" id="subscriptionCustomRange" style="display: none;">
                                                            <label for="subscriptionStartDate">Start Date</label>
                                                            <input type="date" id="subscriptionStartDate">
                                                            <label for="subscriptionEndDate">End Date</label>
                                                            <input type="date" id="subscriptionEndDate">
                                                        </div>
                                                        <div class="filter-group">
                                                            <label for="subscriptionPlanType">Plan Type</label>
                                                            <select id="subscriptionPlanType" class="form-select">
                                                                <option value="all">All Plans</option>
                                                                <option value="basic">Basic</option>
                                                                <option value="standard">Standard</option>
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

                                <!-- Approval Requests -->
                                <div class="col-md-6 mb-4">
                                    <div class="advanced-data-card">
                                        <div class="card-header">
                                            <h3>Approval Requests</h3>
                                            <div class="card-actions">
                                                <span class="currentDate"></span>
                                                <div class="chart-controls">
                                                    <button class="chart-btn reload-btn" data-chart="approval">
                                                        <i class="bx bx-refresh"></i>
                                                        <span class="tooltip">Reload Data</span>
                                                    </button>
                                                    <button class="chart-btn settings-btn" data-chart="approval">
                                                        <i class="bx bx-cog"></i>
                                                        <span class="tooltip">Chart Settings</span>
                                                    </button>
                                                </div>
                                                <button class="btn-details">Manage <i class="bx bx-chevron-right"></i></button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="flip-container" id="approvalFlipContainer">
                                                <div class="flipper">
                                                    <div class="chart-container">
                                                        <div class="data-grid">
                                                            <div class="data-item">
                                                                <div class="data-value" id="pendingRequests">0</div>
                                                                <div class="data-label">Pending</div>
                                                                <div class="data-trend up">
                                                                    <i class="bx bx-up-arrow-alt"></i> 15%
                                                                </div>
                                                            </div>
                                                            <div class="data-item">
                                                                <div class="data-value" id="approvedRequests">0</div>
                                                                <div class="data-label">Approved</div>
                                                                <div class="data-trend up">
                                                                    <i class="bx bx-up-arrow-alt"></i> 20%
                                                                </div>
                                                            </div>
                                                            <div class="data-item">
                                                                <div class="data-value" id="rejectedRequests">0</div>
                                                                <div class="data-label">Rejected</div>
                                                                <div class="data-trend down">
                                                                    <i class="bx bx-down-arrow-alt"></i> 5%
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="donut-chart-container">
                                                            <canvas id="approvalDonutChart"></canvas>
                                                        </div>
                                                    </div>
                                                    <div class="filter-panel">
                                                        <h5>Approval Requests Filters</h5>
                                                        <div class="filter-group">
                                                            <label for="approvalDateRange">Date Range</label>
                                                            <select id="approvalDateRange" class="form-select">
                                                                <option value="7days">Last 7 Days</option>
                                                                <option value="30days" selected>Last 30 Days</option>
                                                                <option value="90days">Last 90 Days</option>
                                                                <option value="custom">Custom Range</option>
                                                            </select>
                                                        </div>
                                                        <div class="filter-group" id="approvalCustomRange" style="display: none;">
                                                            <label for="approvalStartDate">Start Date</label>
                                                            <input type="date" id="approvalStartDate">
                                                            <label for="approvalEndDate">End Date</label>
                                                            <input type="date" id="approvalEndDate">
                                                        </div>
                                                        <div class="filter-group">
                                                            <label for="approvalStatus">Status</label>
                                                            <select id="approvalStatus" class="form-select">
                                                                <option value="all">All Statuses</option>
                                                                <option value="pending">Pending</option>
                                                                <option value="approved">Approved</option>
                                                                <option value="rejected">Rejected</option>
                                                            </select>
                                                        </div>
                                                        <div class="filter-group">
                                                            <label for="approvalType">Request Type</label>
                                                            <select id="approvalType" class="form-select">
                                                                <option value="all">All Types</option>
                                                                <option value="building">Building</option>
                                                                <option value="organization">Organization</option>
                                                                <option value="user">User</option>
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
                                <!-- Monthly Revenue Growth -->
                                <div class="col-md-6 mb-4">
                                    <div class="chart-card">
                                        <div class="chart-header">
                                            <h4>Monthly Revenue Growth</h4>
                                            <div class="card-actions">
                                                <div class="chart-controls">
                                                    <button class="chart-btn reload-btn" data-chart="revenue">
                                                        <i class="bx bx-refresh"></i>
                                                        <span class="tooltip">Reload Data</span>
                                                    </button>
                                                    <button class="chart-btn settings-btn" data-chart="revenue">
                                                        <i class="bx bx-cog"></i>
                                                        <span class="tooltip">Chart Settings</span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="chart-legend">
                                                <span class="legend-item"><i class="bx bx-line-chart"></i> Revenue</span>
                                                <span class="legend-item"><i class="bx bx-trending-up"></i> Growth Rate</span>
                                            </div>
                                        </div>
                                        <div class="flip-container" id="revenueFlipContainer">
                                            <div class="flipper">
                                                <div class="chart-container">
                                                    <canvas id="revenueLineChart"></canvas>
                                                </div>
                                                <div class="filter-panel">
                                                    <h5>Revenue Growth Filters</h5>
                                                    <div class="filter-group">
                                                        <label for="revenueDateRange">Date Range</label>
                                                        <select id="revenueDateRange" class="form-select">
                                                            <option value="3months">Last 3 Months</option>
                                                            <option value="6months">Last 6 Months</option>
                                                            <option value="12months" selected>Last 12 Months</option>
                                                            <option value="custom">Custom Range</option>
                                                        </select>
                                                    </div>
                                                    <div class="filter-group" id="revenueCustomRange" style="display: none;">
                                                        <label for="revenueStartDate">Start Date</label>
                                                        <input type="date" id="revenueStartDate">
                                                        <label for="revenueEndDate">End Date</label>
                                                        <input type="date" id="revenueEndDate">
                                                    </div>
                                                    <div class="filter-group">
                                                        <label for="revenueType">Revenue Type</label>
                                                        <select id="revenueType" class="form-select">
                                                            <option value="all">All Revenue</option>
                                                            <option value="subscription">Subscriptions</option>
                                                            <option value="service">Services</option>
                                                            <option value="other">Other</option>
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

                                <!-- Plan Popularity -->
                                <div class="col-md-6 mb-4">
                                    <div class="chart-card">
                                        <div class="chart-header">
                                            <h4>Plan Popularity</h4>
                                            <div class="card-actions">
                                                <div class="chart-controls">
                                                    <button class="chart-btn reload-btn" data-chart="plan">
                                                        <i class="bx bx-refresh"></i>
                                                        <span class="tooltip">Reload Data</span>
                                                    </button>
                                                    <button class="chart-btn settings-btn" data-chart="plan">
                                                        <i class="bx bx-cog"></i>
                                                        <span class="tooltip">Chart Settings</span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="chart-legend">
                                                <span class="legend-item"><i class="bx bx-pie-chart-alt"></i> By Subscriptions</span>
                                            </div>
                                        </div>
                                        <div class="flip-container" id="planFlipContainer">
                                            <div class="flipper">
                                                <div class="chart-container">
                                                    <canvas id="planPieChart"></canvas>
                                                </div>
                                                <div class="filter-panel">
                                                    <h5>Plan Popularity Filters</h5>
                                                    <div class="filter-group">
                                                        <label for="planDateRange">Date Range</label>
                                                        <select id="planDateRange" class="form-select">
                                                            <option value="7days">Last 7 Days</option>
                                                            <option value="30days" selected>Last 30 Days</option>
                                                            <option value="90days">Last 90 Days</option>
                                                            <option value="custom">Custom Range</option>
                                                        </select>
                                                    </div>
                                                    <div class="filter-group" id="planCustomRange" style="display: none;">
                                                        <label for="planStartDate">Start Date</label>
                                                        <input type="date" id="planStartDate">
                                                        <label for="planEndDate">End Date</label>
                                                        <input type="date" id="planEndDate">
                                                    </div>
                                                    <div class="filter-group">
                                                        <label for="planStatus">Subscription Status</label>
                                                        <select id="planStatus" class="form-select">
                                                            <option value="all">All Statuses</option>
                                                            <option value="active">Active</option>
                                                            <option value="expired">Expired</option>
                                                            <option value="trial">Trial</option>
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

                            <!-- Second Charts Row -->
                            <div class="row">
                                <!-- Subscription Plan Distribution -->
                                <div class="col-md-6 mb-4">
                                    <div class="chart-card">
                                        <div class="chart-header">
                                            <h4>Subscription Plan Distribution</h4>
                                            <div class="card-actions">
                                                <div class="chart-controls">
                                                    <button class="chart-btn reload-btn" data-chart="distribution">
                                                        <i class="bx bx-refresh"></i>
                                                        <span class="tooltip">Reload Data</span>
                                                    </button>
                                                    <button class="chart-btn settings-btn" data-chart="distribution">
                                                        <i class="bx bx-cog"></i>
                                                        <span class="tooltip">Chart Settings</span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="chart-legend">
                                                <span class="legend-item active"><i class="bx bx-square"></i> Active</span>
                                                <span class="legend-item expired"><i class="bx bx-square"></i> Expired</span>
                                                <span class="legend-item trial"><i class="bx bx-square"></i> Trial</span>
                                            </div>
                                        </div>
                                        <div class="flip-container" id="distributionFlipContainer">
                                            <div class="flipper">
                                                <div class="chart-container">
                                                    <canvas id="subscriptionBarChart"></canvas>
                                                </div>
                                                <div class="filter-panel">
                                                    <h5>Subscription Distribution Filters</h5>
                                                    <div class="filter-group">
                                                        <label for="distributionDateRange">Date Range</label>
                                                        <select id="distributionDateRange" class="form-select">
                                                            <option value="3months">Last 3 Months</option>
                                                            <option value="6months">Last 6 Months</option>
                                                            <option value="12months" selected>Last 12 Months</option>
                                                            <option value="custom">Custom Range</option>
                                                        </select>
                                                    </div>
                                                    <div class="filter-group" id="distributionCustomRange" style="display: none;">
                                                        <label for="distributionStartDate">Start Date</label>
                                                        <input type="date" id="distributionStartDate">
                                                        <label for="distributionEndDate">End Date</label>
                                                        <input type="date" id="distributionEndDate">
                                                    </div>
                                                    <div class="filter-group">
                                                        <label for="distributionPlanType">Plan Type</label>
                                                        <select id="distributionPlanType" class="form-select">
                                                            <option value="all">All Plans</option>
                                                            <option value="basic">Basic</option>
                                                            <option value="standard">Standard</option>
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

                                <!-- Approval Status Timeline -->
                                <div class="col-md-6 mb-4">
                                    <div class="chart-card">
                                        <div class="chart-header">
                                            <h4>Approval Status Timeline</h4>
                                            <div class="card-actions">
                                                <div class="chart-controls">
                                                    <button class="chart-btn reload-btn" data-chart="timeline">
                                                        <i class="bx bx-refresh"></i>
                                                        <span class="tooltip">Reload Data</span>
                                                    </button>
                                                    <button class="chart-btn settings-btn" data-chart="timeline">
                                                        <i class="bx bx-cog"></i>
                                                        <span class="tooltip">Chart Settings</span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="chart-legend">
                                                <span class="legend-item pending"><i class="bx bx-square"></i> Pending</span>
                                                <span class="legend-item approved"><i class="bx bx-square"></i> Approved</span>
                                                <span class="legend-item rejected"><i class="bx bx-square"></i> Rejected</span>
                                            </div>
                                        </div>
                                        <div class="flip-container" id="timelineFlipContainer">
                                            <div class="flipper">
                                                <div class="chart-container">
                                                    <canvas id="approvalTimelineChart"></canvas>
                                                </div>
                                                <div class="filter-panel">
                                                    <h5>Approval Timeline Filters</h5>
                                                    <div class="filter-group">
                                                        <label for="timelineDateRange">Date Range</label>
                                                        <select id="timelineDateRange" class="form-select">
                                                            <option value="3months">Last 3 Months</option>
                                                            <option value="6months">Last 6 Months</option>
                                                            <option value="12months" selected>Last 12 Months</option>
                                                            <option value="custom">Custom Range</option>
                                                        </select>
                                                    </div>
                                                    <div class="filter-group" id="timelineCustomRange" style="display: none;">
                                                        <label for="timelineStartDate">Start Date</label>
                                                        <input type="date" id="timelineStartDate">
                                                        <label for="timelineEndDate">End Date</label>
                                                        <input type="date" id="timelineEndDate">
                                                    </div>
                                                    <div class="filter-group">
                                                        <label for="timelineRequestType">Request Type</label>
                                                        <select id="timelineRequestType" class="form-select">
                                                            <option value="all">All Types</option>
                                                            <option value="building">Building</option>
                                                            <option value="organization">Organization</option>
                                                            <option value="user">User</option>
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

            // Current date display
            const monthNames = ["JANUARY", "FEBRUARY", "MARCH", "APRIL", "MAY", "JUNE", "JULY", "AUGUST", "SEPTEMBER", "OCTOBER", "NOVEMBER", "DECEMBER"];
            const currentDate = new Date();
            const currentMonth = currentDate.getMonth();
            const currentYear = currentDate.getFullYear();

            document.querySelectorAll('.currentDate').forEach(el => {
                el.textContent = `${monthNames[currentMonth]} ${currentYear}`;
            });

            // Initialize charts with empty data
            function initCharts() {
                // 1. Subscription Trend Mini Chart
                const subscriptionTrendCtx = document.getElementById('subscriptionTrendChart').getContext('2d');
                subscriptionTrendChart = new Chart(subscriptionTrendCtx, {
                    type: 'line',
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

            // 1. Fetch Stats Data (Total Organizations, Owners, Pending Approvals, Revenue)
            function fetchStatsData() {
                fetch(`{{ route('admin.dashboard.stats') }}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('totalOrganizations').textContent = data.totalOrganizations || '0';
                        document.getElementById('totalOwners').textContent = data.totalOwners || '0';
                        document.getElementById('pendingApprovals').textContent = data.pendingApprovals || '0';
                        document.getElementById('totalRevenue').textContent = `$${data.totalRevenue || '0'}`;
                    })
                    .catch(error => console.error('Error fetching stats:', error));
            }

            // 2. Fetch Subscription Data
            function fetchSubscriptionData(params = {}) {
                const queryString = new URLSearchParams(params).toString();
                fetch(`{{ route('admin.dashboard.subscription.plans') }}?${queryString}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('activeSubscriptions').textContent = data.active || '0';
                        document.getElementById('expiredSubscriptions').textContent = data.expired || '0';
                        document.getElementById('trialSubscriptions').textContent = data.trial || '0';

                        // Update subscription trend chart
                        subscriptionTrendChart.data.labels = data.trend.labels || [];
                        subscriptionTrendChart.data.datasets = [{
                            label: 'New Subscriptions',
                            data: data.trend.values || [],
                            borderColor: '#184E83',
                            backgroundColor: 'rgba(24, 78, 131, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true
                        }];
                        subscriptionTrendChart.update();
                    })
                    .catch(error => console.error('Error fetching subscription data:', error));
            }

            // 3. Fetch Approval Data
            function fetchApprovalData(params = {}) {
                const queryString = new URLSearchParams(params).toString();
                fetch(`{{ route('admin.dashboard.approval.requests') }}?${queryString}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('pendingRequests').textContent = data.pending || '0';
                        document.getElementById('approvedRequests').textContent = data.approved || '0';
                        document.getElementById('rejectedRequests').textContent = data.rejected || '0';

                        // Update approval donut chart
                        approvalDonutChart.data.labels = ['Pending', 'Approved', 'Rejected'];
                        approvalDonutChart.data.datasets = [{
                            data: [data.pending || 0, data.approved || 0, data.rejected || 0],
                            backgroundColor: ['#36A2EB', '#4BC0C0', '#FF6384'],
                            borderWidth: 0
                        }];
                        approvalDonutChart.update();
                    })
                    .catch(error => console.error('Error fetching approval data:', error));
            }

            // 4. Fetch Revenue Data
            function fetchRevenueData(params = {}) {
                const queryString = new URLSearchParams(params).toString();
                fetch(`{{ route('admin.dashboard.revenue.growth') }}?${queryString}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        // Update revenue line chart
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
                        revenueLineChart.update();
                    })
                    .catch(error => console.error('Error fetching revenue data:', error));
            }

            // 5. Fetch Plan Popularity Data
            function fetchPlanPopularityData(params = {}) {
                const queryString = new URLSearchParams(params).toString();
                fetch(`{{ route('admin.dashboard.plan.popularity') }}?${queryString}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        // Update plan pie chart
                        planPieChart.data.labels = data.labels || [];
                        planPieChart.data.datasets = [{
                            data: data.values || [],
                            backgroundColor: ['#36A2EB', '#4BC0C0', '#FFCE56', '#FF6384'],
                            borderWidth: 0
                        }];
                        planPieChart.update();
                    })
                    .catch(error => console.error('Error fetching plan popularity data:', error));
            }

            // 6. Fetch Subscription Distribution Data
            function fetchSubscriptionDistributionData(params = {}) {
                const queryString = new URLSearchParams(params).toString();
                fetch(`{{ route('admin.dashboard.subscription.distribution') }}?${queryString}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        // Update subscription bar chart
                        subscriptionBarChart.data.labels = data.labels || [];
                        subscriptionBarChart.data.datasets = [
                            {
                                label: 'Active',
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
                                label: 'Trial',
                                data: data.trial || [],
                                backgroundColor: 'rgba(255, 205, 86, 0.7)',
                                borderColor: 'rgba(255, 205, 86, 1)',
                                borderWidth: 0,
                                borderRadius: 4
                            }
                        ];
                        subscriptionBarChart.update();
                    })
                    .catch(error => console.error('Error fetching subscription distribution data:', error));
            }

            // 7. Fetch Approval Timeline Data
            function fetchApprovalTimelineData(params = {}) {
                const queryString = new URLSearchParams(params).toString();
                fetch(`{{ route('admin.dashboard.approval.timeline') }}?${queryString}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        // Update approval timeline chart
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
                        approvalTimelineChart.update();
                    })
                    .catch(error => console.error('Error fetching approval timeline data:', error));
            }

            // Helper function to get filter params from a panel
            function getFilterParams(panelId) {
                const panel = document.querySelector(`#${panelId} .filter-panel`);
                if (!panel) return {};

                const params = {};

                // Date range handling
                const dateRangeSelect = panel.querySelector('select[id$="DateRange"]');
                if (dateRangeSelect) {
                    const dateRangeValue = dateRangeSelect.value;
                    if (dateRangeValue === 'custom') {
                        const startDate = panel.querySelector('input[id$="StartDate"]').value;
                        const endDate = panel.querySelector('input[id$="EndDate"]').value;
                        if (startDate) params.start_date = startDate;
                        if (endDate) params.end_date = endDate;
                    } else {
                        params.range = dateRangeValue;
                    }
                }

                // Other filters
                panel.querySelectorAll('select:not([id$="DateRange"])').forEach(select => {
                    if (select.value && select.value !== 'all') {
                        const paramName = select.id.replace(/DateRange|StartDate|EndDate/g, '').toLowerCase();
                        params[paramName] = select.value;
                    }
                });

                return params;
            }

            // Chart controls functionality
            document.querySelectorAll('.settings-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const chartType = this.getAttribute('data-chart');
                    const container = document.getElementById(`${chartType}FlipContainer`);
                    container.classList.add('flipped');
                });
            });

            document.querySelectorAll('.reload-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const chartType = this.getAttribute('data-chart');
                    // Show loading indicator
                    const originalHTML = this.innerHTML;
                    this.innerHTML = '<i class="bx bx-loader bx-spin"></i>';

                    // Reload the appropriate data
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

            // Cancel button in filter panels
            document.querySelectorAll('.btn-cancel').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const container = this.closest('.flip-container');
                    container.classList.remove('flipped');
                });
            });

            // Apply button in filter panels
            document.querySelectorAll('.btn-apply').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const container = this.closest('.flip-container');
                    const chartType = container.id.replace('FlipContainer', '');
                    const params = getFilterParams(container.id);

                    // Show loading
                    const originalHTML = this.innerHTML;
                    this.innerHTML = 'Applying...';

                    // Apply filters to the appropriate chart
                    const filterFunctions = {
                        'subscription': fetchSubscriptionData,
                        'approval': fetchApprovalData,
                        'revenue': fetchRevenueData,
                        'plan': fetchPlanPopularityData,
                        'distribution': fetchSubscriptionDistributionData,
                        'timeline': fetchApprovalTimelineData
                    };

                    if (filterFunctions[chartType]) {
                        filterFunctions[chartType](params);
                    }

                    // Close panel and reset button
                    setTimeout(() => {
                        container.classList.remove('flipped');
                        this.innerHTML = originalHTML;

                        // Show success in reload button tooltip
                        const reloadBtn = document.querySelector(`.reload-btn[data-chart="${chartType}"]`);
                        if (reloadBtn) {
                            const tooltip = reloadBtn.querySelector('.tooltip');
                            if (tooltip) {
                                tooltip.textContent = 'Filters applied!';
                                setTimeout(() => {
                                    tooltip.textContent = 'Reload Data';
                                }, 2000);
                            }
                        }
                    }, 500);
                });
            });

            // Show/hide custom date range inputs
            document.querySelectorAll('select[id$="DateRange"]').forEach(select => {
                select.addEventListener('change', function() {
                    const containerId = this.id.replace('DateRange', 'CustomRange');
                    const customRangeContainer = document.getElementById(containerId);
                    if (this.value === 'custom') {
                        customRangeContainer.style.display = 'block';
                    } else {
                        customRangeContainer.style.display = 'none';
                    }
                });
            });

            // Details buttons functionality
            document.querySelectorAll('.btn-details').forEach(button => {
                button.addEventListener('click', function() {
                    const cardTitle = this.closest('.card-header').querySelector('h3').textContent;
                    alert(`Viewing details for: ${cardTitle}`);
                });
            });
        });
    </script>
@endpush
