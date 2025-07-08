@extends('layouts.app')

@section('title', 'Building Management Dashboard')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #184E83;
            --primary-light: #1A6FC9;
            --danger: #ff4d6d;
            --warning: #ffbe0b;
            --success: #2ecc71;
            --secondary: #66CDAA;
            --dark: #2b2d42;
            --light: #f8f9fa;
            --gray: #6c757d;
            --light-gray: #f5f7fa;
            --accent: #FA8072;
        }

        body {
            font-family: 'Inter', sans-serif;
        }

        #main {
            margin-top: 45px;
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

        .report-header {
            margin-bottom: 15px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 15px;
            flex-wrap: wrap;
            min-width: 0;
        }

        .report-header h1 {
            font-size: 28px;
            font-weight: 600;
            color: var(--sidenavbar-text-color);
            margin-bottom: 10px;
        }

        .report-description {
            color: var(--sidenavbar-text-color);
            margin-bottom: 20px;
            max-width: 800px;
            line-height: 1.6;
        }

        .export-actions {
            display: flex;
            gap: 12px;
            flex-wrap: nowrap;
            align-items: center;
            flex-shrink: 0;
        }

        .export-btn {
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
            flex-shrink: 0;
        }

        /* ================ */
        /* Filters Section */
        /* ================ */
        .filters-container {
            background: var(--sidenavbar-body-color);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
        }

        .filter-group {
            flex: 1 1 180px;
            min-width: 0;
        }

        .filter-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: var(--sidenavbar-text-color);
            font-weight: 500;
        }

        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            border: 1px solid #ddd;
            background-color: white;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            border-color: var(--primary-light);
            outline: none;
            box-shadow: 0 0 0 3px rgba(24, 78, 131, 0.1);
        }

        .filter-button {
            flex: 0 1 auto;
            margin-bottom: 0;
            align-self: flex-end;
        }

        /* ================ */
        /* Charts & Metrics */
        /* ================ */
        .chart-summary-row {
            display: flex;
            gap: 25px;
            margin-bottom: 25px;
        }

        .chart-container {
            flex: 2;
            background: var(--sidenavbar-body-color);
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            padding: 20px;
            transition: transform 0.3s;
        }

        .chart-container:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .full-width-chart {
            width: 100%;
            background: var(--sidenavbar-body-color);
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            padding: 20px;
            margin-bottom: 25px;
            transition: transform 0.3s;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .chart-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--sidenavbar-text-color);
        }

        .chart-header .chart-value {
            font-size: 16px;
            font-weight: 600;
            color: var(--sidenavbar-text-color);
        }

        .chart-canvas-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        /* ================ */
        /* Metrics Grid */
        /* ================ */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .metric-card {
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            padding: 20px;
            text-align: center;
            transition: transform 0.3s;
        }

        .metric-card h4 {
            font-size: 14px;
            color: #ffff;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .metric-card .value {
            font-size: 24px;
            font-weight: 600;
            color: #ffff;
        }

        .metric-card .trend {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 8px;
            font-size: 13px;
        }

        .trend.up {
            color: var(--success);
        }

        .trend.down {
            color: var(--danger);
        }

        /* ================ */
        /* Summary Section */
        /* ================ */
        .summary-container {
            flex: 1;
            background: var(--sidenavbar-body-color);
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .summary-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--sidenavbar-text-color);
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f0f0f0;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .summary-item:last-child {
            border-bottom: none;
        }

        .summary-label {
            color: var(--sidenavbar-text-color);
            font-size: 14px;
            display: flex;
            align-items: center;
        }

        .summary-value {
            font-weight: 600;
            color: var(--sidenavbar-text-color);
        }

        .summary-value.positive {
            color: var(--success);
        }

        .summary-value.negative {
            color: var(--danger);
        }

        /* ================ */
        /* Status Grid */
        /* ================ */
        .status-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 15px;
        }

        .status-card {
            background: var(--body-background-color);
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            transition: transform 0.3s;
        }

        .status-count {
            font-weight: 600;
            font-size: 20px;
            margin-bottom: 5px;
        }

        .status-label {
            font-size: 12px;
            color: var(--sidenavbar-text-color);
        }

        /* ================ */
        /* Progress Bars */
        /* ================ */
        .progress-container {
            margin-top: auto;
            padding-top: 15px;
        }

        .progress-item {
            margin-bottom: 15px;
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .progress-label {
            font-size: 13px;
            color: var(--sidenavbar-text-color);
        }

        .progress-value {
            font-size: 13px;
            font-weight: 600;
        }

        .progress-bar {
            height: 6px;
            background-color: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 3px;
        }

        /* ================ */
        /* Transactions Table */
        /* ================ */
        .transaction-section {
            background-color: var(--sidenavbar-body-color);
            margin-bottom: 2.5rem;
        }

        .transactions-table-container {
            background: var(--body-background-color);
            border-radius: 12px;
            padding: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .transactions-table {
            width: 100%;
            border-collapse: collapse;
        }

        .transactions-table thead {
            background-color: var(--sidenavbar-body-color) !important;
        }

        .transactions-table th {
            padding: 1rem;
            text-align: left;
            color: var(--sidenavbar-text-color);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .transactions-table td {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.875rem;
        }

        .transaction-id {
            font-family: 'Courier New', monospace;
            color: var(--primary);
            font-weight: 500;
        }

        .transaction-title {
            font-weight: 500;
            color: var(--dark);
        }

        .transaction-unit {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            background: var(--sidenavbar-body-color);
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .transaction-type {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .type-income {
            background: rgba(6, 214, 160, 0.1);
            color: var(--success);
        }

        .type-expense {
            background: rgba(255, 0, 110, 0.1);
            color: var(--danger);
        }

        .transaction-amount {
            font-weight: 600;
        }

        .amount-income {
            color: var(--success);
        }

        .amount-expense {
            color: var(--danger);
        }

        /* ================ */
        /* Pagination */
        /* ================ */
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
        }

        .pagination-info {
            font-size: 0.875rem;
            color: var(--gray);
        }

        .pagination-controls {
            display: flex;
            gap: 0.5rem;
        }

        .pagination-btn {
            padding: 0.5rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background: white;
            color: var(--dark);
            cursor: pointer;
            transition: all 0.2s;
        }

        .pagination-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        /* ================ */
        /* Export Styles */
        /* ================ */
        body.exporting .top-navbar,
        body.exporting .side-navbar,
        body.exporting .breadcrumb {
            display: none !important;
        }

        body.exporting .report-container {
            width: 100% !important;
            margin: 0 !important;
            padding: 20px !important;
            box-shadow: none !important;
        }

        /* ================ */
        /* Loading Styles */
        /* ================ */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            flex-direction: column;
        }

        .loading-spinner {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #184E83;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }

        /* ================ */
        /* Animations */
        /* ================ */
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* ================ */
        /* Responsive Styles */
        /* ================ */
        @media (max-width: 1200px) {
            .metrics-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .chart-summary-row {
                flex-direction: column;
            }
        }

        @media (max-width: 768px) {
            .metrics-grid {
                grid-template-columns: 1fr;
            }

            .status-grid {
                grid-template-columns: 1fr;
            }

            .filters-container {
                gap: 12px;
                padding: 15px;
            }

            .filter-group {
                flex: 1 1 100%;
            }
        }

        @media (max-width: 600px) {
            .header-content {
                flex-direction: column;
                align-items: flex-start;
            }

            .export-actions {
                flex-wrap: wrap;
            }

            .export-btn {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .report-header h1 {
                font-size: 22px;
            }

            .report-description {
                font-size: 14px;
            }

            .filters-container {
                gap: 10px;
                padding: 12px;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Top Navbar -->
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
        ['url' => '#', 'label' => 'Dashboard'],
        ['url' => '', 'label' => 'Building Reports']
    ]"
    />

    <!-- Side Navbar -->
    <x-Owner.side-navbar :openSections="['Reports']" />
    <x-error-success-model />

    <div id="main">
        <section class="content my-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="report-container">
                            <!-- Header -->
                            <div class="report-header">
                                <div class="header-content">
                                    <div>
                                        <h1>Building Reports</h1>
                                        <p class="report-description">
                                            Comprehensive overview of financial performance, occupancy rates, and operational metrics.
                                        </p>
                                    </div>
                                    <div class="export-actions">
                                        <button class="export-btn btn btn-outline-secondary" id="exportPdf">
                                            <i class='bx bx-download'></i> Export PDF
                                        </button>
                                        <button class="export-btn btn btn-primary" id="exportImage">
                                            <i class='bx bx-image-alt'></i> Export Image
                                        </button>
                                    </div>
                                </div>
                            </div>


                            <!-- Filters -->
                            <div class="filters-container">
                                <div class="filter-group">
                                    <label for="startDate">Start Date</label>
                                    <input type="date" id="startDate" class="filter-input">
                                </div>
                                <div class="filter-group">
                                    <label for="endDate">End Date</label>
                                    <input type="date" id="endDate" class="filter-input">
                                </div>
                                <div class="filter-group">
                                    <label for="buildingSelect">Building</label>
                                    <select id="buildingSelect" class="filter-input form-select">
                                        <option value="1">Building 1</option>
                                    </select>
                                </div>
                                <div class="filter-group filter-button">
                                    <button id="applyFilters" class="btn btn-primary">
                                        Apply Filters
                                    </button>
                                </div>
                            </div>

                            <!-- Loading Overlay -->
                            <div class="loading-overlay" style="display: none;">
                                <div class="loading-spinner"></div>
                                <div class="loading-text">Loading dashboard data...</div>
                            </div>

                            <!-- Key Metrics -->
                            <div class="metrics-grid">
                                <div class="metric-card bg-gradient-primary">
                                    <h4>Total Units</h4>
                                    <div class="value" id="totalUnits">0</div>
                                </div>
                                <div class="metric-card bg-gradient-info">
                                    <h4>Total Levels</h4>
                                    <div class="value" id="totalLevels">0</div>
                                </div>
                                <div class="metric-card bg-gradient-success">
                                    <h4>Total Income</h4>
                                    <div class="value" id="totalIncome">$0</div>
                                </div>
                                <div class="metric-card bg-gradient-warning">
                                    <h4>Total Expenses</h4>
                                    <div class="value" id="totalExpenses">$0</div>
                                </div>
                            </div>
                            {{--                            <p class="section-description">--}}
                            {{--                                The key metrics section provides a quick snapshot of your property's performance.--}}
                            {{--                                You can see the total number of units and levels across all buildings, along with--}}
                            {{--                                financial metrics showing income and expenses.--}}
                            {{--                            </p>--}}

                            <!-- Income vs Expense Pie Chart with Summary -->
                            <div class="chart-summary-row">
                                <div class="chart-container">
                                    <div class="chart-header">
                                        <h3>Income vs Expenses</h3>
                                        <div class="chart-value" id="profitValue">$0 Net Profit</div>
                                    </div>
                                    <div class="chart-canvas-container">
                                        <canvas id="incomeExpenseChart"></canvas>
                                    </div>
                                </div>
                                <div class="summary-container">
                                    <h3 class="summary-title">Financial Summary</h3>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-dollar-circle me-2'></i> Total Income</span>
                                        <span class="summary-value" id="summaryIncome">$0</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-money-withdraw me-2'></i> Total Expenses</span>
                                        <span class="summary-value" id="summaryExpenses">$0</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-trending-up me-2'></i> Net Profit</span>
                                        <span class="summary-value positive" id="summaryProfit">$0</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-line-chart me-2'></i> Profit Margin</span>
                                        <span class="summary-value positive" id="summaryMargin">0%</span>
                                    </div>

                                    <div class="progress-container">
                                        <div class="progress-item">
                                            <div class="progress-header">
                                                <span class="progress-label">Income Growth</span>
                                                <span class="progress-value positive">+12%</span>
                                            </div>
                                            <div class="progress-bar">
                                                <div class="progress-fill" style="width: 62%; background-color: var(--primary);"></div>
                                            </div>
                                        </div>
                                        <div class="progress-item">
                                            <div class="progress-header">
                                                <span class="progress-label">Expense Growth</span>
                                                <span class="progress-value negative">+5%</span>
                                            </div>
                                            <div class="progress-bar">
                                                <div class="progress-fill" style="width: 25%; background-color: var(--danger);"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{--                            <p class="section-description">--}}
                            {{--                                The income vs expenses visualization helps you understand your property's financial health at a glance.--}}
                            {{--                                The doughnut chart shows the proportion of income to expenses, while the financial summary provides--}}
                            {{--                                detailed numbers.--}}
                            {{--                            </p>--}}

                            <!-- Income and Expense Distribution -->
                            <div class="chart-summary-row">
                                <div class="chart-container">
                                    <div class="chart-header">
                                        <h3>Income Sources</h3>
                                        <div class="chart-value" id="incomeSourcesValue">0 Sources</div>
                                    </div>
                                    <div class="chart-canvas-container">
                                        <canvas id="incomeSourcesChart"></canvas>
                                    </div>
                                </div>
                                <div class="chart-container">
                                    <div class="chart-header">
                                        <h3>Expense Categories</h3>
                                        <div class="chart-value" id="expenseCategoriesValue">0 Categories</div>
                                    </div>
                                    <div class="chart-canvas-container">
                                        <canvas id="expenseCategoriesChart"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- Unit Occupancy Chart (Full Width) -->
                            <div class="full-width-chart">
                                <div class="chart-header">
                                    <h3>Unit Occupancy</h3>
                                    <div class="chart-value" id="occupancyRate">0% Occupancy Rate</div>
                                </div>
                                <div class="chart-canvas-container">
                                    <canvas id="occupancyChart"></canvas>
                                </div>
                                <div class="status-grid">
                                    <div class="status-card">
                                        <div class="status-count" style="color: #4BC0C0;" id="rentedUnits">0</div>
                                        <div class="status-label">Rented Units</div>
                                    </div>
                                    <div class="status-card">
                                        <div class="status-count" style="color: #FF6384;" id="soldUnits">0</div>
                                        <div class="status-label">Sold Units</div>
                                    </div>
                                    <div class="status-card">
                                        <div class="status-count" style="color: #FFCD56;" id="availableUnits">0</div>
                                        <div class="status-label">Available Units</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Human Resources Donut Chart with Summary -->
                            <div class="chart-summary-row">
                                <div class="chart-container">
                                    <div class="chart-header">
                                        <h3>Staff by Department</h3>
                                        <div class="chart-value" id="totalStaff">0 Employees</div>
                                    </div>
                                    <div class="chart-canvas-container">
                                        <canvas id="staffChart"></canvas>
                                    </div>
                                </div>
                                <div class="summary-container">
                                    <h3 class="summary-title">Staff Distribution</h3>
                                    <div id="staffDistributionSummary">
                                        <!-- Will be populated by API -->
                                    </div>
                                </div>
                            </div>

                            <!-- Memberships Chart with Summary -->
                            <div class="chart-summary-row">
                                <div class="chart-container">
                                    <div class="chart-header">
                                        <h3>Memberships</h3>
                                        <div class="chart-value" id="activeMembers">0 Active</div>
                                    </div>
                                    <div class="chart-canvas-container">
                                        <canvas id="membershipChart"></canvas>
                                    </div>
                                </div>
                                <div class="summary-container">
                                    <h3 class="summary-title">Membership Analytics</h3>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-user me-2'></i> Total Members</span>
                                        <span class="summary-value" id="summaryTotalMembers">0</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-check-circle me-2'></i> Active Members</span>
                                        <span class="summary-value" id="summaryActiveMembers">0</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-time me-2'></i> Expired Members</span>
                                        <span class="summary-value" id="summaryExpiredMembers">0</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-plus-circle me-2'></i> New This Period</span>
                                        <span class="summary-value positive" id="summaryNewMembers">+0</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-refresh me-2'></i> Renewal Rate</span>
                                        <span class="summary-value positive" id="summaryRenewalRate">0%</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Maintenance Requests Chart (Full Width) -->
                            <div class="full-width-chart">
                                <div class="chart-header">
                                    <h3>Maintenance Requests</h3>
                                    <div class="chart-value" id="totalRequests">0 Requests</div>
                                </div>
                                <div class="chart-canvas-container">
                                    <canvas id="maintenanceChart"></canvas>
                                </div>
                                <div class="status-grid">
                                    <div class="status-card">
                                        <div class="status-count" style="color: #4BC0C0;" id="completedRequests">0</div>
                                        <div class="status-label">Completed</div>
                                    </div>
                                    <div class="status-card">
                                        <div class="status-count" style="color: #FFCD56;" id="pendingRequests">0</div>
                                        <div class="status-label">In Progress</div>
                                    </div>
                                    <div class="status-card">
                                        <div class="status-count" style="color: #FF6384;" id="rejectedRequests">0</div>
                                        <div class="status-label">Rejected</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Transactions Section -->
                            <div class="transaction-section p-3 pb-1 shadow rounded">
                                <div class="section-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <h2 class="section-title">Recent Transactions</h2>
                                        <p class="section-description">
                                            Detailed view of all financial transactions including rental payments, service charges,
                                            maintenance costs, and other income/expense items.
                                        </p>
                                    </div>
                                    <div class="export-actions">
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class='bx bx-export me-1'></i>
                                                Export
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                                                <li><button class="dropdown-item" type="button" id="copyButton"><i class='bx bx-copy me-2'></i>Copy</button></li>
                                                <li><button class="dropdown-item" type="button" id="csvButton"><i class='bx bx-file me-2'></i>CSV</button></li>
                                                <li><button class="dropdown-item" type="button" id="excelButton"><i class='bx bx-spreadsheet me-2'></i>Excel</button></li>
                                                <li><button class="dropdown-item" type="button" id="pdfButton"><i class='bx bxs-file-pdf me-2'></i>PDF</button></li>
                                                <li><button class="dropdown-item" type="button" id="printButton"><i class='bx bx-printer me-2'></i>Print</button></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="transactions-table-container">
                                    <table id="transactionsTable" class="transactions-table">
                                        <thead>
                                        <tr>
                                            <th>Transaction ID</th>
                                            <th>Title</th>
                                            <th>Unit</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <!-- Data will be loaded by DataTables -->
                                        </tbody>
                                    </table>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <!-- Add DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>

    <!-- Add DataTables Buttons JS -->
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

    <script>

        document.addEventListener('DOMContentLoaded', function() {
            // Chart instances
            let incomeExpenseChart, incomeSourcesChart, expenseCategoriesChart,
                occupancyChart, staffChart, membershipChart, maintenanceChart;

            // CSRF Token for AJAX requests
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Current filters
            let currentBuilding = 'all';
            let currentStartDate = '';
            let currentEndDate = '';

            // Set default dates (last 30 days)
            const endDate = new Date();
            const startDate = new Date();
            startDate.setDate(endDate.getDate() - 30);

            document.getElementById('startDate').valueAsDate = startDate;
            document.getElementById('endDate').valueAsDate = endDate;

            // Format dates for API
            currentStartDate = formatDate(startDate);
            currentEndDate = formatDate(endDate);

            // Initialize the dashboard
            function initDashboard() {
                setupEventListeners();
                loadAllData();
            }

            // Format date as YYYY-MM-DD
            function formatDate(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            // Show loading overlay
            function showLoading() {
                document.querySelector('.loading-overlay').style.display = 'flex';
            }

            // Hide loading overlay
            function hideLoading() {
                document.querySelector('.loading-overlay').style.display = 'none';
            }

            // Set up event listeners
            function setupEventListeners() {
                // Apply filters button
                document.getElementById('applyFilters').addEventListener('click', function() {
                    const startDateInput = document.getElementById('startDate').valueAsDate;
                    const endDateInput = document.getElementById('endDate').valueAsDate;

                    if (!startDateInput || !endDateInput) {
                        alert('Please select both start and end dates');
                        return;
                    }

                    currentStartDate = formatDate(startDateInput);
                    currentEndDate = formatDate(endDateInput);
                    currentBuilding = document.getElementById('buildingSelect').value;

                    loadAllData();
                });
            }

            // Load all data from APIs
            function loadAllData() {
                showLoading();

                // Load metrics data (API 1)
                fetchMetricsData().then(() => {
                    // Load income/expense data (API 2)
                    return fetchIncomeExpenseData();
                }).then(() => {
                    // Load occupancy data (API 3)
                    return fetchOccupancyData();
                }).then(() => {
                    // Load staff data (API 4)
                    return fetchStaffData();
                }).then(() => {
                    // Load memberships data (API 5)
                    return fetchMembershipsData();
                }).then(() => {
                    // Load maintenance data (API 6)
                    return fetchMaintenanceData();
                }).catch(error => {
                    console.error('Error loading data:', error);
                }).finally(() => {
                    hideLoading();
                });
            }

            // API 1: Fetch metrics data (Total Units, Total Levels, Total Income, Total Expenses)
            function fetchMetricsData() {
                return fetch(`{{ route('owner.reports.buildings.metrics') }}?building=${currentBuilding}&start=${currentStartDate}&end=${currentEndDate}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        // Update metric cards
                        document.getElementById('totalUnits').textContent = data.total_units || 0;
                        document.getElementById('totalLevels').textContent = data.total_levels || 0;
                        document.getElementById('totalIncome').textContent = '$' + (data.total_income || 0).toLocaleString();
                        document.getElementById('totalExpenses').textContent = '$' + (data.total_expenses || 0).toLocaleString();
                    });
            }

            // API 2: Fetch income/expense data (Income vs Expense, Financial Summary, Income Sources, Expense Categories, Recent Transactions)
            function fetchIncomeExpenseData() {
                return fetch(`{{ route('owner.reports.buildings.finance') }}?building=${currentBuilding}&start=${currentStartDate}&end=${currentEndDate}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        // Update financial summary
                        const profit = (data.total_income || 0) - (data.total_expenses || 0);
                        const margin = (data.total_income > 0) ? (profit / data.total_income) * 100 : 0;

                        document.getElementById('profitValue').textContent = '$' + profit.toLocaleString() + ' Net Profit';
                        document.getElementById('summaryIncome').textContent = '$' + (data.total_income || 0).toLocaleString();
                        document.getElementById('summaryExpenses').textContent = '$' + (data.total_expenses || 0).toLocaleString();
                        document.getElementById('summaryProfit').textContent = '$' + profit.toLocaleString();
                        document.getElementById('summaryMargin').textContent = margin.toFixed(1) + '%';

                        // Render income vs expense chart
                        renderIncomeExpenseChart(data.total_income || 0, data.total_expenses || 0);

                        // Render income sources chart if data exists
                        if (data.income_sources && data.income_sources.labels && data.income_sources.data) {
                            renderIncomeSourcesChart(data.income_sources);
                            document.getElementById('incomeSourcesValue').textContent = data.income_sources.labels.length + ' Sources';
                        }

                        // Render expense categories chart if data exists
                        if (data.expense_categories && data.expense_categories.labels && data.expense_categories.data) {
                            renderExpenseCategoriesChart(data.expense_categories);
                            document.getElementById('expenseCategoriesValue').textContent = data.expense_categories.labels.length + ' Categories';
                        }

                        console.log('Received data:', data); // Add this line
                        if (data.recent_transactions) {
                            console.log('Transactions data:', data.recent_transactions); // And this line
                            initTransactionsTable(data.recent_transactions);
                        }
                    });
            }

            // API 3: Fetch occupancy data
            function fetchOccupancyData() {
                return fetch(`{{ route('owner.reports.buildings.occupancy') }}?building=${currentBuilding}&start=${currentStartDate}&end=${currentEndDate}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        document.getElementById('occupancyRate').textContent = data.occupancyRate + '% Occupancy Rate';
                        document.getElementById('rentedUnits').textContent = data.totals.rented || 0;
                        document.getElementById('soldUnits').textContent = data.totals.sold || 0;
                        document.getElementById('availableUnits').textContent = data.totals.available || 0;

                        if (data.occupancy_trend && data.occupancy_trend.labels) {
                            renderOccupancyChart(data.occupancy_trend);
                        }
                    });
            }

            // API 4: Fetch staff data
            function fetchStaffData() {
                return fetch(`{{ route('owner.reports.buildings.staff') }}?building=${currentBuilding}&start=${currentStartDate}&end=${currentEndDate}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        // Update staff summary
                        document.getElementById('totalStaff').textContent = (data.total_staff || 0) + ' Employees';

                        // Update staff distribution summary
                        const staffSummaryContainer = document.getElementById('staffDistributionSummary');
                        staffSummaryContainer.innerHTML = '';

                        if (data.staff_by_department && data.staff_by_department.labels && data.staff_by_department.data) {
                            // Render staff chart
                            renderStaffChart(data.staff_by_department);

                            // Create summary items for each department
                            data.staff_by_department.labels.forEach((label, index) => {
                                const summaryItem = document.createElement('div');
                                summaryItem.className = 'summary-item';

                                const summaryLabel = document.createElement('span');
                                summaryLabel.className = 'summary-label';
                                summaryLabel.innerHTML = `${label}`;

                                const summaryValue = document.createElement('span');
                                summaryValue.className = 'summary-value';
                                summaryValue.textContent = data.staff_by_department.data[index] || 0;

                                summaryItem.appendChild(summaryLabel);
                                summaryItem.appendChild(summaryValue);
                                staffSummaryContainer.appendChild(summaryItem);
                            });
                        }
                    });
            }

            // API 5: Fetch memberships data
            function fetchMembershipsData() {
                return fetch(`{{ route('owner.reports.buildings.memberships') }}?building=${currentBuilding}&start=${currentStartDate}&end=${currentEndDate}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        // Update membership summary
                        const active = data.active_members || 0;
                        const expired = data.expired_members || 0;
                        const totalMembers = active + expired;
                        const newMembers = data.new_members || 0;
                        const renewalRate = (active > 0) ? Math.round(((active - newMembers) / active) * 100) : 0;

                        document.getElementById('activeMembers').textContent = active + ' Active';
                        document.getElementById('summaryTotalMembers').textContent = totalMembers;
                        document.getElementById('summaryActiveMembers').textContent = active;
                        document.getElementById('summaryExpiredMembers').textContent = expired;
                        document.getElementById('summaryNewMembers').textContent = '+' + newMembers;
                        document.getElementById('summaryRenewalRate').textContent = renewalRate + '%';

                        // Render membership chart if data exists
                        if (data.membership_trend && data.membership_trend.labels) {
                            renderMembershipChart(data.membership_trend);
                        }
                    });
            }

            // API 6: Fetch maintenance data
            function fetchMaintenanceData() {
                return fetch(`{{ route('owner.reports.buildings.maintenance') }}?building=${currentBuilding}&start=${currentStartDate}&end=${currentEndDate}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        // Update maintenance summary
                        const completed = data.completed_requests || 0;
                        const pending = data.pending_requests || 0;
                        const rejected = data.rejected_requests || 0;
                        const totalRequests = completed + pending + rejected;

                        document.getElementById('totalRequests').textContent = totalRequests + ' Requests';
                        document.getElementById('completedRequests').textContent = completed;
                        document.getElementById('pendingRequests').textContent = pending;
                        document.getElementById('rejectedRequests').textContent = rejected;

                        // Render maintenance chart if data exists
                        if (data.maintenance_trend && data.maintenance_trend.labels) {
                            renderMaintenanceChart(data.maintenance_trend);
                        }
                    });
            }

            // Initialize DataTable for transactions
            function initTransactionsTable(data) {
                // Destroy existing DataTable if it exists
                if ($.fn.DataTable.isDataTable('#transactionsTable')) {
                    $('#transactionsTable').DataTable().destroy();
                }

                // Initialize DataTable with pagination and export buttons
                var table = $('#transactionsTable').DataTable({
                    data: data,
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                    buttons: [
                        {
                            extend: 'copy',
                            text: 'Copy',
                            className: 'd-none',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'csv',
                            text: 'CSV',
                            className: 'd-none',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'excel',
                            text: 'Excel',
                            className: 'd-none',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'pdf',
                            text: 'PDF',
                            className: 'd-none',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'print',
                            text: 'Print',
                            className: 'd-none',
                            exportOptions: {
                                columns: ':visible'
                            }
                        }
                    ],
                    columns: [
                        { data: 'id', className: 'transaction-id' },
                        { data: 'title', className: 'transaction-title' },
                        {
                            data: 'unit',
                            render: function(data, type, row) {
                                return `<span class="transaction-unit">${data || 'N/A'}</span>`;
                            }
                        },
                        {
                            data: 'type',
                            render: function(data, type, row) {
                                const typeClass = data.toLowerCase() === 'income' ? 'type-income' : 'type-expense';
                                return `<span class="transaction-type ${typeClass}">${data}</span>`;
                            }
                        },
                        {
                            data: 'amount',
                            render: function(data, type, row) {
                                const amountClass = row.type.toLowerCase() === 'income' ? 'amount-income' : 'amount-expense';
                                const sign = row.type === 'Income' ? '+' : '-';
                                return `<span class="transaction-amount ${amountClass}">${sign} $${data.toFixed(2)}</span>`;
                            }
                        },
                        {
                            data: 'date',
                            render: function(data, type, row) {
                                const date = new Date(data);
                                return `<span class="transaction-date">${date.toLocaleDateString()}</span>`;
                            }
                        }
                    ],
                    language: {
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous"
                        },
                        searchPlaceholder: "Search transactions..."
                    }
                });

                // Connect dropdown buttons to DataTable export functions
                document.getElementById("copyButton")?.addEventListener("click", function() {
                    table.button('.buttons-copy').trigger();
                });

                document.getElementById("csvButton")?.addEventListener("click", function() {
                    table.button('.buttons-csv').trigger();
                });

                document.getElementById("excelButton")?.addEventListener("click", function() {
                    table.button('.buttons-excel').trigger();
                });

                document.getElementById("pdfButton")?.addEventListener("click", function() {
                    table.button('.buttons-pdf').trigger();
                });

                document.getElementById("printButton")?.addEventListener("click", function() {
                    table.button('.buttons-print').trigger();
                });
            }

            // Chart rendering functions

            // Income vs Expense Pie Chart
            function renderIncomeExpenseChart(income, expenses) {
                const ctx = document.getElementById('incomeExpenseChart');
                if (!ctx) return;

                if (incomeExpenseChart) incomeExpenseChart.destroy();

                incomeExpenseChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Income', 'Expenses'],
                        datasets: [{
                            data: [income, expenses],
                            backgroundColor: ['#184E83', '#ff4d6d'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `${context.label}: $${context.raw.toLocaleString()}`;
                                    }
                                }
                            },
                            datalabels: {
                                formatter: (value, ctx) => {
                                    const total = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    return Math.round((value / total) * 100) + '%';
                                },
                                color: '#fff',
                                font: {
                                    weight: 'bold',
                                    size: 12
                                }
                            }
                        },
                        cutout: '70%'
                    },
                    plugins: [ChartDataLabels]
                });
            }

            // Income Sources Chart
            function renderIncomeSourcesChart(data) {
                const ctx = document.getElementById('incomeSourcesChart');
                if (!ctx) return;

                if (incomeSourcesChart) incomeSourcesChart.destroy();

                incomeSourcesChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Amount',
                            data: data.data,
                            backgroundColor: data.colors || ['#184E83', '#1A6FC9', '#2ecc71', '#ffbe0b'],
                            borderRadius: 4,
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `$${context.parsed.y.toLocaleString()}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    drawBorder: false
                                },
                                ticks: {
                                    callback: function(value) {
                                        return '$' + value.toLocaleString();
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // Expense Categories Chart
            function renderExpenseCategoriesChart(data) {
                const ctx = document.getElementById('expenseCategoriesChart');
                if (!ctx) return;

                if (expenseCategoriesChart) expenseCategoriesChart.destroy();

                expenseCategoriesChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Amount',
                            data: data.data,
                            backgroundColor: data.colors || ['#ff4d6d', '#ff758f', '#ff8fa3', '#ffb3c1', '#ffccd5'],
                            borderRadius: 4,
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `$${context.parsed.y.toLocaleString()}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    drawBorder: false
                                },
                                ticks: {
                                    callback: function(value) {
                                        return '$' + value.toLocaleString();
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // Occupancy Chart
            function renderOccupancyChart(data) {
                const ctx = document.getElementById('occupancyChart');
                if (!ctx) return;

                if (occupancyChart) occupancyChart.destroy();

                occupancyChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Available',
                                data: data.available || [],
                                backgroundColor: '#FFCD56',
                                borderRadius: 4,
                                borderWidth: 0
                            },
                            {
                                label: 'Rented',
                                data: data.rented || [],
                                backgroundColor: '#4BC0C0',
                                borderRadius: 4,
                                borderWidth: 0
                            },
                            {
                                label: 'Sold',
                                data: data.sold || [],
                                backgroundColor: '#FF6384',
                                borderRadius: 4,
                                borderWidth: 0
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `${context.dataset.label}: ${context.parsed.y} units`;
                                    },
                                    footer: function(tooltipItems) {
                                        const total = tooltipItems.reduce((a, b) => a + b.parsed.y, 0);
                                        return `Total: ${total} units`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    drawBorder: false
                                },
                                title: {
                                    display: true,
                                    text: 'Number of Units'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                title: {
                                    display: true,
                                    text: 'Days'
                                }
                            }
                        }
                    }
                });
            }

            // Staff Chart
            function renderStaffChart(data) {
                const ctx = document.getElementById('staffChart');
                if (!ctx) return;

                if (staffChart) staffChart.destroy();

                staffChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.data,
                            backgroundColor: data.colors || ['#184E83', '#1A6FC9', '#2ecc71', '#ffbe0b', '#ff4d6d'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = Math.round((context.raw / total) * 100);
                                        return `${context.label}: ${context.raw} (${percentage}%)`;
                                    }
                                }
                            },
                            datalabels: {
                                formatter: (value, ctx) => {
                                    const total = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    return Math.round((value / total) * 100) + '%';
                                },
                                color: '#fff',
                                font: {
                                    weight: 'bold',
                                    size: 12
                                }
                            }
                        },
                        cutout: '70%'
                    },
                    plugins: [ChartDataLabels]
                });
            }

            // Membership Chart
            function renderMembershipChart(data) {
                const ctx = document.getElementById('membershipChart');
                if (!ctx) return;

                if (membershipChart) membershipChart.destroy();

                membershipChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Active Members',
                                data: data.active || [],
                                borderColor: '#184E83',
                                backgroundColor: '#184E8320',
                                borderWidth: 2,
                                tension: 0.3,
                                fill: true,
                                pointBackgroundColor: '#184E83',
                                pointRadius: 4,
                                pointHoverRadius: 6
                            },
                            {
                                label: 'Expired Members',
                                data: data.expired || [],
                                borderColor: '#ff4d6d',
                                backgroundColor: '#ff4d6d20',
                                borderWidth: 2,
                                tension: 0.3,
                                fill: true,
                                pointBackgroundColor: '#ff4d6d',
                                pointRadius: 4,
                                pointHoverRadius: 6
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `${context.dataset.label}: ${context.parsed.y}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    drawBorder: false
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // Maintenance Chart
            function renderMaintenanceChart(data) {
                const ctx = document.getElementById('maintenanceChart');
                if (!ctx) return;

                if (maintenanceChart) maintenanceChart.destroy();

                maintenanceChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Completed',
                                data: data.completed,
                                type: 'line',
                                borderColor: '#2ecc71',
                                backgroundColor: 'transparent',
                                borderWidth: 3,
                                tension: 0.3,
                                pointBackgroundColor: '#2ecc71',
                                pointRadius: 5,
                                pointHoverRadius: 7,
                                order: 1 // Show on top
                            },
                            {
                                label: 'Pending',
                                data: data.pending,
                                backgroundColor: '#ffbe0b',
                                borderRadius: 4,
                                borderWidth: 0,
                                order: 2
                            },
                            {
                                label: 'Rejected',
                                data: data.rejected,
                                type: 'line', // Add this to make it a line
                                borderColor: '#ff4d6d', // Line color
                                backgroundColor: 'transparent',
                                borderWidth: 3,
                                tension: 0.3,
                                pointBackgroundColor: '#ff4d6d',
                                pointRadius: 5,
                                pointHoverRadius: 7,
                                order: 3
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += context.parsed.y;
                                        return label;
                                    },
                                    footer: function(tooltipItems) {
                                        // Only show total for the bar datasets (pending + rejected)
                                        const barItems = tooltipItems.filter(item => item.datasetIndex > 0);
                                        if (barItems.length > 1) {
                                            const total = barItems.reduce((a, b) => a + b.parsed.y, 0);
                                            return `Total Open Requests: ${total}`;
                                        }
                                        return null;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    drawBorder: false
                                },
                                title: {
                                    display: true,
                                    text: 'Number of Requests'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                stacked: true // Only stacks the bar datasets
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        }
                    }
                });
            }

            // Initialize the dashboard
            initDashboard();
        });
    </script>

    <script>
        // Initialize jsPDF
        const { jsPDF } = window.jspdf;

        document.addEventListener('DOMContentLoaded', function() {
            // Your existing chart code here...

            // Export functionality
            document.getElementById('exportPdf').addEventListener('click', exportToPdf);
            document.getElementById('exportImage').addEventListener('click', exportToImage);

            function exportToPdf() {
                showLoading();
                document.body.classList.add('exporting');

                const element = document.querySelector('.report-container');
                const options = {
                    scale: 2,
                    useCORS: true,
                    scrollY: 0,
                    backgroundColor: '#FFFFFF',
                    onclone: function(clonedDoc) {
                        clonedDoc.body.classList.add('exporting');
                    }
                };

                setTimeout(() => {  // Small delay to ensure rendering
                    html2canvas(element, options).then(canvas => {
                        const pdf = new jsPDF('p', 'mm', 'a4');
                        const imgData = canvas.toDataURL('image/png');
                        const pdfWidth = pdf.internal.pageSize.getWidth();
                        const pdfHeight = (canvas.height * pdfWidth) / canvas.width;

                        pdf.addImage(imgData, 'PNG', 5, 5, pdfWidth - 10, pdfHeight - 10);
                        pdf.save(`Building_Report_${getFormattedDate()}.pdf`);

                        hideLoading();
                        document.body.classList.remove('exporting');
                    });
                }, 500);
            }

            function exportToImage() {
                showLoading();
                document.body.classList.add('exporting');

                const element = document.querySelector('.report-container');
                const options = {
                    scale: 2,
                    useCORS: true,
                    scrollY: 0,
                    backgroundColor: '#FFFFFF'
                };

                setTimeout(() => {
                    html2canvas(element, options).then(canvas => {
                        const link = document.createElement('a');
                        link.download = `Building_Report_${getFormattedDate()}.png`;
                        link.href = canvas.toDataURL('image/png');
                        link.click();

                        hideLoading();
                        document.body.classList.remove('exporting');
                    });
                }, 500);
            }

            // Helper functions
            function showLoading() {
                const loading = document.createElement('div');
                loading.id = 'export-loading';
                loading.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0,0,0,0.7);
                    z-index: 9999;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    color: white;
                    font-size: 1.5rem;
                `;
                loading.innerHTML = `
                    <div style="text-align: center;">
                        <div class="export-spinner" style="font-size: 3rem;">↻</div>
                        <p>Generating report...</p>
                    </div>
                `;
                document.body.appendChild(loading);
            }

            function hideLoading() {
                const loading = document.getElementById('export-loading');
                if (loading) loading.remove();
            }

            function getFormattedDate() {
                const d = new Date();
                return [
                    d.getFullYear(),
                    String(d.getMonth() + 1).padStart(2, '0'),
                    String(d.getDate()).padStart(2, '0')
                ].join('-') + '_' + [
                    String(d.getHours()).padStart(2, '0'),
                    String(d.getMinutes()).padStart(2, '0')
                ].join('-');
            }
        });
    </script>
@endpush
