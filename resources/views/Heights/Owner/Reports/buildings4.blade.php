@extends('layouts.app')

@section('title', 'Building Management Dashboard')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #184E83;
            --primary-light: #1A6FC9;
            --danger: #ff4d6d;
            --warning: #ffbe0b;
            --success: #2ecc71;
            --dark: #2b2d42;
            --light: #f8f9fa;
            --gray: #6c757d;
            --light-gray: #f5f7fa;
        }

        body {
        }

        #main {
            margin-top: 45px;
        }

        .report-header {
            margin-bottom: 30px;
        }

        .report-header h1 {
            font-size: 28px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 10px;
        }

        .report-description {
            color: var(--gray);
            margin-bottom: 20px;
            max-width: 800px;
            line-height: 1.6;
        }

        .filters-container {
            background: white;
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
            flex: 1;
            min-width: 180px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: var(--gray);
            font-weight: 500;
        }

        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background: white;
            font-size: 14px;
            transition: all 0.3s;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            border-color: var(--primary-light);
            outline: none;
            box-shadow: 0 0 0 3px rgba(24, 78, 131, 0.1);
        }

        .custom-date-range {
            display: flex;
            gap: 15px;
        }

        .custom-date-range .filter-group {
            flex: 1;
        }

        /* Chart and Summary Layout */
        .chart-summary-row {
            display: flex;
            gap: 25px;
            margin-bottom: 25px;
        }

        .chart-container {
            flex: 2;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            padding: 20px;
            transition: transform 0.3s;
        }

        .chart-container:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .summary-container {
            flex: 1;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .full-width-chart {
            width: 100%;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            padding: 20px;
            margin-bottom: 25px;
            transition: transform 0.3s;
        }

        .full-width-chart:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
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
            color: var(--dark);
        }

        .chart-header .chart-value {
            font-size: 16px;
            font-weight: 600;
            color: var(--primary);
        }

        .chart-canvas-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        /* Metrics Grid */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .metric-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            padding: 20px;
            text-align: center;
            transition: transform 0.3s;
        }

        .metric-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .metric-card h4 {
            font-size: 14px;
            color: var(--gray);
            margin-bottom: 10px;
            font-weight: 500;
        }

        .metric-card .value {
            font-size: 24px;
            font-weight: 600;
            color: var(--dark);
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

        /* Summary Items */
        .summary-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--dark);
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
            color: var(--gray);
            font-size: 14px;
            display: flex;
            align-items: center;
        }

        .summary-label i {
            margin-right: 8px;
            font-size: 16px;
        }

        .summary-value {
            font-weight: 600;
            color: var(--dark);
        }

        .summary-value.positive {
            color: var(--success);
        }

        .summary-value.negative {
            color: var(--danger);
        }

        /* Status Grid */
        .status-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 15px;
        }

        .status-card {
            background: rgba(24, 78, 131, 0.05);
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            transition: transform 0.3s;
        }

        .status-card:hover {
            transform: translateY(-3px);
        }

        .status-count {
            font-weight: 600;
            font-size: 20px;
            margin-bottom: 5px;
        }

        .status-label {
            font-size: 12px;
            color: var(--gray);
        }

        /* Progress Bars */
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
            color: var(--gray);
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

        /* Responsive */
        @media (max-width: 1200px) {
            .metrics-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .chart-summary-row {
                flex-direction: column;
            }
        }

        @media (max-width: 768px) {
            .filters-container {
                flex-direction: column;
            }

            .metrics-grid {
                grid-template-columns: 1fr;
            }

            .custom-date-range {
                flex-direction: column;
            }

            .status-grid {
                grid-template-columns: 1fr;
            }
        }

        /* New styles for transactions table */
        .transaction-section {
            margin-bottom: 2.5rem;
        }
        /* Transactions Table */
        .transactions-table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .transactions-table {
            width: 100%;
            border-collapse: collapse;
        }

        .transactions-table thead {
            background-color: var(--table-header-bg);
        }

        .transactions-table th {
            padding: 1rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .transactions-table td {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.875rem;
        }

        .transactions-table tbody tr:last-child td {
            border-bottom: none;
        }

        .transactions-table tbody tr:hover {
            background-color: var(--table-row-hover);
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
            background: var(--light-gray);
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

        .transaction-status {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
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

        .transaction-date {
            color: var(--gray);
            font-size: 0.75rem;
        }

        .transaction-actions {
            display: flex;
            gap: 0.5rem;
        }

        .transaction-action-btn {
            background: none;
            border: none;
            color: var(--gray);
            cursor: pointer;
            font-size: 1rem;
            transition: color 0.2s;
        }

        .transaction-action-btn:hover {
            color: var(--primary);
        }

        /* Pagination */
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

        .pagination-btn:hover {
            background: var(--light-gray);
        }

        .pagination-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Hide navbars during export */
        body.exporting .top-navbar,
        body.exporting .side-navbar,
        body.exporting .breadcrumb {
            display: none !important;
        }

        /* Ensure report container takes full width during export */
        body.exporting .report-container {
            width: 100% !important;
            margin: 0 !important;
            padding: 20px !important;
            box-shadow: none !important;
        }

        /* Loading spinner styles */
        .export-spinner {
            animation: spin 1s linear infinite;
            display: inline-block;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
@endpush

@section('content')
    <!-- Top Navbar -->
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
        ['url' => '#', 'label' => 'Dashboard'],
        ['url' => '', 'label' => 'Building Reports']
    ]"/>

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
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <h1>Building Management Dashboard</h1>
                                        <p class="report-description">
                                            Comprehensive overview of financial performance, occupancy rates, and operational metrics.
                                        </p>
                                    </div>
                                    <div class="export-actions">
                                        <button class="export-btn" id="exportPdf">
                                            <i class='bx bx-download'></i> Export PDF
                                        </button>
                                        <button class="export-btn" id="exportImage">
                                            <i class='bx bx-image-alt'></i> Export Image
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Filters -->
                            <div class="filters-container">
                                <div class="filter-group">
                                    <label for="dateRange">Date Range</label>
                                    <select id="dateRange">
                                        <option value="7days">Last 7 Days</option>
                                        <option value="30days" selected>Last 30 Days</option>
                                        <option value="90days">Last 90 Days</option>
                                        <option value="custom">Custom Date Range</option>
                                    </select>
                                </div>

                                <div id="customDateRangeContainer" class="custom-date-range" style="display: none;">
                                    <div class="filter-group">
                                        <label for="startDate">Start Date</label>
                                        <input type="date" id="startDate">
                                    </div>
                                    <div class="filter-group">
                                        <label for="endDate">End Date</label>
                                        <input type="date" id="endDate">
                                    </div>
                                </div>

                                <div class="filter-group">
                                    <label for="buildingSelect">Building</label>
                                    <select id="buildingSelect">
                                        <option value="all">All Buildings</option>
                                        <option value="1">Downtown Tower</option>
                                        <option value="2">Riverside Apartments</option>
                                        <option value="3">Hillside Complex</option>
                                        <option value="4">Central Plaza</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Key Metrics -->
                            <div class="metrics-grid">
                                <div class="metric-card">
                                    <h4>Total Units</h4>
                                    <div class="value" id="totalUnits">Loading...</div>
                                    <div class="trend up">
                                        <i class='bx bx-up-arrow-alt'></i> <span id="unitsChange">0%</span> from last period
                                    </div>
                                </div>
                                <div class="metric-card">
                                    <h4>Total Levels</h4>
                                    <div class="value" id="totalLevels">Loading...</div>
                                    <div class="trend up">
                                        <i class='bx bx-up-arrow-alt'></i> <span id="levelsChange">0%</span> from last period
                                    </div>
                                </div>
                                <div class="metric-card">
                                    <h4>Total Income</h4>
                                    <div class="value" id="totalIncome">Loading...</div>
                                    <div class="trend up">
                                        <i class='bx bx-up-arrow-alt'></i> <span id="incomeChange">0%</span> from last period
                                    </div>
                                </div>
                                <div class="metric-card">
                                    <h4>Total Expenses</h4>
                                    <div class="value" id="totalExpenses">Loading...</div>
                                    <div class="trend down">
                                        <i class='bx bx-down-arrow-alt'></i> <span id="expensesChange">0%</span> from last period
                                    </div>
                                </div>
                            </div>

                            <!-- Income vs Expense Pie Chart with Summary -->
                            <div class="chart-summary-row">
                                <div class="chart-container">
                                    <div class="chart-header">
                                        <h3>Income vs Expenses</h3>
                                        <div class="chart-value" id="profitValue">Loading...</div>
                                    </div>
                                    <div class="chart-canvas-container">
                                        <canvas id="incomeExpenseChart"></canvas>
                                    </div>
                                </div>
                                <div class="summary-container">
                                    <h3 class="summary-title">Financial Summary</h3>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-dollar-circle'></i> Total Income</span>
                                        <span class="summary-value" id="summaryIncome">Loading...</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-money-withdraw'></i> Total Expenses</span>
                                        <span class="summary-value" id="summaryExpenses">Loading...</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-trending-up'></i> Net Profit</span>
                                        <span class="summary-value positive" id="summaryProfit">Loading...</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-line-chart'></i> Profit Margin</span>
                                        <span class="summary-value positive" id="summaryMargin">Loading...</span>
                                    </div>
                                    <div class="progress-container">
                                        <div class="progress-item">
                                            <div class="progress-header">
                                                <span class="progress-label">Income Growth</span>
                                                <span class="progress-value positive" id="incomeGrowth">0%</span>
                                            </div>
                                            <div class="progress-bar">
                                                <div class="progress-fill" id="incomeGrowthBar" style="width: 0%; background-color: var(--primary);"></div>
                                            </div>
                                        </div>
                                        <div class="progress-item">
                                            <div class="progress-header">
                                                <span class="progress-label">Expense Growth</span>
                                                <span class="progress-value negative" id="expenseGrowth">0%</span>
                                            </div>
                                            <div class="progress-bar">
                                                <div class="progress-fill" id="expenseGrowthBar" style="width: 0%; background-color: var(--danger);"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Income and Expense Distribution -->
                            <div class="chart-summary-row">
                                <div class="chart-container">
                                    <div class="chart-header">
                                        <h3>Income Sources</h3>
                                        <div class="chart-value" id="incomeSourcesValue">Loading...</div>
                                    </div>
                                    <div class="chart-canvas-container">
                                        <canvas id="incomeSourcesChart"></canvas>
                                    </div>
                                </div>
                                <div class="chart-container">
                                    <div class="chart-header">
                                        <h3>Expense Categories</h3>
                                        <div class="chart-value" id="expenseCategoriesValue">Loading...</div>
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
                                    <div class="chart-value" id="occupancyRate">Loading...</div>
                                </div>
                                <div class="chart-canvas-container">
                                    <canvas id="occupancyChart"></canvas>
                                </div>
                                <div class="status-grid">
                                    <div class="status-card">
                                        <div class="status-count" style="color: var(--primary);" id="rentedUnits">0</div>
                                        <div class="status-label">Rented Units</div>
                                    </div>
                                    <div class="status-card">
                                        <div class="status-count" style="color: var(--success);" id="soldUnits">0</div>
                                        <div class="status-label">Sold Units</div>
                                    </div>
                                    <div class="status-card">
                                        <div class="status-count" style="color: var(--gray);" id="availableUnits">0</div>
                                        <div class="status-label">Available Units</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Human Resources Donut Chart with Summary -->
                            <div class="chart-summary-row">
                                <div class="chart-container">
                                    <div class="chart-header">
                                        <h3>Staff by Department</h3>
                                        <div class="chart-value" id="totalStaff">Loading...</div>
                                    </div>
                                    <div class="chart-canvas-container">
                                        <canvas id="staffChart"></canvas>
                                    </div>
                                </div>
                                <div class="summary-container">
                                    <h3 class="summary-title">Staff Distribution</h3>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-wrench'></i> Maintenance</span>
                                        <span class="summary-value" id="summaryMaintenance">0</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-shield-alt-2'></i> Security</span>
                                        <span class="summary-value" id="summarySecurity">0</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-broom'></i> Cleaning</span>
                                        <span class="summary-value" id="summaryCleaning">0</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-briefcase'></i> Administration</span>
                                        <span class="summary-value" id="summaryAdmin">0</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-group'></i> Other</span>
                                        <span class="summary-value" id="summaryOther">0</span>
                                    </div>
                                    <div class="progress-container">
                                        <div class="progress-item">
                                            <div class="progress-header">
                                                <span class="progress-label">Staff Satisfaction</span>
                                                <span class="progress-value positive" id="staffSatisfaction">0%</span>
                                            </div>
                                            <div class="progress-bar">
                                                <div class="progress-fill" id="staffSatisfactionBar" style="width: 0%; background-color: var(--success);"></div>
                                            </div>
                                        </div>
                                        <div class="progress-item">
                                            <div class="progress-header">
                                                <span class="progress-label">Turnover Rate</span>
                                                <span class="progress-value negative" id="turnoverRate">0%</span>
                                            </div>
                                            <div class="progress-bar">
                                                <div class="progress-fill" id="turnoverRateBar" style="width: 0%; background-color: var(--danger);"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Memberships Chart with Summary -->
                            <div class="chart-summary-row">
                                <div class="chart-container">
                                    <div class="chart-header">
                                        <h3>Memberships</h3>
                                        <div class="chart-value" id="activeMembers">Loading...</div>
                                    </div>
                                    <div class="chart-canvas-container">
                                        <canvas id="membershipChart"></canvas>
                                    </div>
                                </div>
                                <div class="summary-container">
                                    <h3 class="summary-title">Membership Analytics</h3>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-user'></i> Total Members</span>
                                        <span class="summary-value" id="summaryTotalMembers">0</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-check-circle'></i> Active Members</span>
                                        <span class="summary-value" id="summaryActiveMembers">0</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-time'></i> Expired Members</span>
                                        <span class="summary-value" id="summaryExpiredMembers">0</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-plus-circle'></i> New This Period</span>
                                        <span class="summary-value positive" id="summaryNewMembers">+0</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-refresh'></i> Renewal Rate</span>
                                        <span class="summary-value positive" id="summaryRenewalRate">0%</span>
                                    </div>
                                    <div class="progress-container">
                                        <div class="progress-item">
                                            <div class="progress-header">
                                                <span class="progress-label">Membership Growth</span>
                                                <span class="progress-value positive" id="membershipGrowth">0%</span>
                                            </div>
                                            <div class="progress-bar">
                                                <div class="progress-fill" id="membershipGrowthBar" style="width: 0%; background-color: var(--primary);"></div>
                                            </div>
                                        </div>
                                        <div class="progress-item">
                                            <div class="progress-header">
                                                <span class="progress-label">Engagement Rate</span>
                                                <span class="progress-value positive" id="engagementRate">0%</span>
                                            </div>
                                            <div class="progress-bar">
                                                <div class="progress-fill" id="engagementRateBar" style="width: 0%; background-color: var(--success);"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Maintenance Requests Chart (Full Width) -->
                            <div class="full-width-chart">
                                <div class="chart-header">
                                    <h3>Maintenance Requests</h3>
                                    <div class="chart-value" id="totalRequests">Loading...</div>
                                </div>
                                <div class="chart-canvas-container">
                                    <canvas id="maintenanceChart"></canvas>
                                </div>
                                <div class="status-grid">
                                    <div class="status-card">
                                        <div class="status-count" style="color: var(--success);" id="completedRequests">0</div>
                                        <div class="status-label">Completed</div>
                                    </div>
                                    <div class="status-card">
                                        <div class="status-count" style="color: var(--warning);" id="pendingRequests">0</div>
                                        <div class="status-label">In Progress</div>
                                    </div>
                                    <div class="status-card">
                                        <div class="status-count" style="color: var(--danger);" id="rejectedRequests">0</div>
                                        <div class="status-label">Rejected</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Transactions Section -->
                            <div class="transaction-section">
                                <div class="section-header">
                                    <h2 class="section-title">Recent Transactions</h2>
                                </div>
                                <p class="section-description">
                                    Detailed view of all financial transactions including rental payments, service charges,
                                    maintenance costs, and other income/expense items.
                                </p>

                                <div class="transactions-table-container">
                                    <table class="transactions-table" id="transactionsTable">
                                        <thead>
                                        <tr>
                                            <th>Transaction ID</th>
                                            <th>Title</th>
                                            <th>Unit</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <!-- Will be populated by JavaScript -->
                                        </tbody>
                                    </table>
                                </div>

                                <div class="pagination-container">
                                    <div class="pagination-info" id="paginationInfo">
                                        Loading transactions...
                                    </div>
                                    <div class="pagination-controls" id="paginationControls">
                                        <button class="pagination-btn" disabled id="prevPageBtn">
                                            <i class='bx bx-chevron-left'></i> Previous
                                        </button>
                                        <button class="pagination-btn active">1</button>
                                        <button class="pagination-btn" id="nextPageBtn">
                                            Next <i class='bx bx-chevron-right'></i>
                                        </button>
                                    </div>
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

    <script>
        // Initialize jsPDF
        const { jsPDF } = window.jspdf;

        document.addEventListener('DOMContentLoaded', function() {
            // Chart instances
            let incomeExpenseChart, incomeSourcesChart, expenseCategoriesChart,
                occupancyChart, staffChart, membershipChart, maintenanceChart;

            // Current filters and pagination
            let currentBuilding = 'all';
            let currentRange = '30days';
            let currentPage = 1;
            const itemsPerPage = 7;

            // Initialize the dashboard
            function initDashboard() {
                fetchMetrics();
                fetchIncomeExpense();
                fetchIncomeSources();
                fetchExpenseCategories();
                fetchOccupancy();
                fetchStaff();
                fetchMemberships();
                fetchMaintenance();
                fetchTransactions();
                setupEventListeners();
            }

            // Set up event listeners
            function setupEventListeners() {
                // Building selection change
                document.getElementById('buildingSelect').addEventListener('change', function() {
                    currentBuilding = this.value;
                    refreshAllData();
                });

                // Date range change
                document.getElementById('dateRange').addEventListener('change', function() {
                    currentRange = this.value;

                    if (currentRange === 'custom') {
                        document.getElementById('customDateRangeContainer').style.display = 'flex';
                        // Set default dates (last 30 days)
                        const endDate = new Date();
                        const startDate = new Date();
                        startDate.setDate(endDate.getDate() - 30);

                        document.getElementById('endDate').valueAsDate = endDate;
                        document.getElementById('startDate').valueAsDate = startDate;
                    } else {
                        document.getElementById('customDateRangeContainer').style.display = 'none';
                        refreshAllData();
                    }
                });

                // Custom date changes
                document.getElementById('startDate').addEventListener('change', function() {
                    refreshAllData();
                });

                document.getElementById('endDate').addEventListener('change', function() {
                    refreshAllData();
                });

                // Pagination controls
                document.getElementById('prevPageBtn').addEventListener('click', function() {
                    if (currentPage > 1) {
                        currentPage--;
                        fetchTransactions();
                    }
                });

                document.getElementById('nextPageBtn').addEventListener('click', function() {
                    currentPage++;
                    fetchTransactions();
                });

                // Export functionality
                document.getElementById('exportPdf').addEventListener('click', exportToPdf);
                document.getElementById('exportImage').addEventListener('click', exportToImage);
            }

            // Refresh all data when filters change
            function refreshAllData() {
                fetchMetrics();
                fetchIncomeExpense();
                fetchIncomeSources();
                fetchExpenseCategories();
                fetchOccupancy();
                fetchStaff();
                fetchMemberships();
                fetchMaintenance();
                fetchTransactions();
            }

            // API Fetch Functions
            function fetchMetrics() {
                const params = getFilterParams();
                fetch(route('owner.reports.buildings.metrics', params))
                    .then(response => response.json())
                    .then(data => {
                        updateMetrics(data);
                    })
                    .catch(error => {
                        console.error('Error fetching metrics:', error);
                    });
            }

            function fetchIncomeExpense() {
                const params = getFilterParams();
                fetch(route('owner.reports.buildings.income-expense', params))
                    .then(response => response.json())
                    .then(data => {
                        renderIncomeExpenseChart(data);
                        updateFinancialSummary(data);
                    })
                    .catch(error => {
                        console.error('Error fetching income/expense data:', error);
                    });
            }

            function fetchIncomeSources() {
                const params = getFilterParams();
                fetch(route('owner.reports.buildings.income-sources', params))
                    .then(response => response.json())
                    .then(data => {
                        renderIncomeSourcesChart(data);
                    })
                    .catch(error => {
                        console.error('Error fetching income sources:', error);
                    });
            }

            function fetchExpenseCategories() {
                const params = getFilterParams();
                fetch(route('owner.reports.buildings.expense-categories', params))
                    .then(response => response.json())
                    .then(data => {
                        renderExpenseCategoriesChart(data);
                    })
                    .catch(error => {
                        console.error('Error fetching expense categories:', error);
                    });
            }

            function fetchOccupancy() {
                const params = getFilterParams();
                fetch(route('owner.reports.buildings.occupancy', params))
                    .then(response => response.json())
                    .then(data => {
                        renderOccupancyChart(data);
                        updateOccupancySummary(data);
                    })
                    .catch(error => {
                        console.error('Error fetching occupancy data:', error);
                    });
            }

            function fetchStaff() {
                const params = getFilterParams();
                fetch(route('owner.reports.buildings.staff', params))
                    .then(response => response.json())
                    .then(data => {
                        renderStaffChart(data);
                        updateStaffSummary(data);
                    })
                    .catch(error => {
                        console.error('Error fetching staff data:', error);
                    });
            }

            function fetchMemberships() {
                const params = getFilterParams();
                fetch(route('owner.reports.buildings.memberships', params))
                    .then(response => response.json())
                    .then(data => {
                        renderMembershipChart(data);
                        updateMembershipSummary(data);
                    })
                    .catch(error => {
                        console.error('Error fetching membership data:', error);
                    });
            }

            function fetchMaintenance() {
                const params = getFilterParams();
                fetch(route('owner.reports.buildings.maintenance', params))
                    .then(response => response.json())
                    .then(data => {
                        renderMaintenanceChart(data);
                        updateMaintenanceSummary(data);
                    })
                    .catch(error => {
                        console.error('Error fetching maintenance data:', error);
                    });
            }

            function fetchTransactions() {
                const params = {
                    ...getFilterParams(),
                    page: currentPage,
                    per_page: itemsPerPage
                };

                fetch(route('owner.reports.buildings.transactions', params))
                    .then(response => response.json())
                    .then(data => {
                        renderTransactionsTable(data);
                        updatePaginationControls(data);
                    })
                    .catch(error => {
                        console.error('Error fetching transactions:', error);
                    });
            }

            // Helper function to get filter parameters
            function getFilterParams() {
                const params = {
                    building_id: currentBuilding,
                    range: currentRange
                };

                if (currentRange === 'custom') {
                    const startDate = document.getElementById('startDate').value;
                    const endDate = document.getElementById('endDate').value;

                    if (startDate && endDate) {
                        params.start_date = startDate;
                        params.end_date = endDate;
                    }
                }

                return params;
            }

            // Update UI Functions
            function updateMetrics(data) {
                document.getElementById('totalUnits').textContent = data.total_units;
                document.getElementById('totalLevels').textContent = data.total_levels;
                document.getElementById('totalIncome').textContent = '$' + data.total_income.toLocaleString();
                document.getElementById('totalExpenses').textContent = '$' + data.total_expenses.toLocaleString();

                // Update trend indicators
                document.getElementById('unitsChange').textContent = data.units_change + '%';
                document.getElementById('levelsChange').textContent = data.levels_change + '%';
                document.getElementById('incomeChange').textContent = data.income_change + '%';
                document.getElementById('expensesChange').textContent = data.expenses_change + '%';

                // Update trend icons
                document.querySelectorAll('.metric-card .trend')[0].className = `trend ${data.units_change >= 0 ? 'up' : 'down'}`;
                document.querySelectorAll('.metric-card .trend')[1].className = `trend ${data.levels_change >= 0 ? 'up' : 'down'}`;
                document.querySelectorAll('.metric-card .trend')[2].className = `trend ${data.income_change >= 0 ? 'up' : 'down'}`;
                document.querySelectorAll('.metric-card .trend')[3].className = `trend ${data.expenses_change >= 0 ? 'up' : 'down'}`;
            }

            function updateFinancialSummary(data) {
                const profit = data.income - data.expenses;
                const margin = (profit / data.income) * 100;

                document.getElementById('profitValue').textContent = '$' + profit.toLocaleString() + ' Net Profit';
                document.getElementById('summaryIncome').textContent = '$' + data.income.toLocaleString();
                document.getElementById('summaryExpenses').textContent = '$' + data.expenses.toLocaleString();
                document.getElementById('summaryProfit').textContent = '$' + profit.toLocaleString();
                document.getElementById('summaryMargin').textContent = margin.toFixed(1) + '%';

                // Update progress bars
                document.getElementById('incomeGrowth').textContent = data.income_growth >= 0 ? '+' + data.income_growth + '%' : data.income_growth + '%';
                document.getElementById('expenseGrowth').textContent = data.expense_growth >= 0 ? '+' + data.expense_growth + '%' : data.expense_growth + '%';

                document.getElementById('incomeGrowthBar').style.width = Math.abs(data.income_growth) + '%';
                document.getElementById('expenseGrowthBar').style.width = Math.abs(data.expense_growth) + '%';
            }

            function updateOccupancySummary(data) {
                document.getElementById('occupancyRate').textContent = data.occupancy_rate + '% Occupancy Rate';
                document.getElementById('rentedUnits').textContent = data.rented_units;
                document.getElementById('soldUnits').textContent = data.sold_units;
                document.getElementById('availableUnits').textContent = data.available_units;
            }

            function updateStaffSummary(data) {
                document.getElementById('totalStaff').textContent = data.total_staff + ' Employees';
                document.getElementById('summaryMaintenance').textContent = data.maintenance;
                document.getElementById('summarySecurity').textContent = data.security;
                document.getElementById('summaryCleaning').textContent = data.cleaning;
                document.getElementById('summaryAdmin').textContent = data.admin;
                document.getElementById('summaryOther').textContent = data.other;

                // Update progress bars
                document.getElementById('staffSatisfaction').textContent = data.satisfaction + '%';
                document.getElementById('turnoverRate').textContent = data.turnover + '%';

                document.getElementById('staffSatisfactionBar').style.width = data.satisfaction + '%';
                document.getElementById('turnoverRateBar').style.width = data.turnover + '%';
            }

            function updateMembershipSummary(data) {
                document.getElementById('activeMembers').textContent = data.active + ' Active';
                document.getElementById('summaryTotalMembers').textContent = data.total;
                document.getElementById('summaryActiveMembers').textContent = data.active;
                document.getElementById('summaryExpiredMembers').textContent = data.expired;
                document.getElementById('summaryNewMembers').textContent = '+' + data.new_members;
                document.getElementById('summaryRenewalRate').textContent = data.renewal_rate + '%';

                // Update progress bars
                document.getElementById('membershipGrowth').textContent = data.growth + '%';
                document.getElementById('engagementRate').textContent = data.engagement + '%';

                document.getElementById('membershipGrowthBar').style.width = data.growth + '%';
                document.getElementById('engagementRateBar').style.width = data.engagement + '%';
            }

            function updateMaintenanceSummary(data) {
                document.getElementById('totalRequests').textContent = data.total + ' Requests';
                document.getElementById('completedRequests').textContent = data.completed;
                document.getElementById('pendingRequests').textContent = data.pending;
                document.getElementById('rejectedRequests').textContent = data.rejected;
            }

            function renderTransactionsTable(data) {
                const tbody = document.querySelector('#transactionsTable tbody');
                tbody.innerHTML = '';

                data.data.forEach(transaction => {
                    const row = document.createElement('tr');

                    row.innerHTML = `
                        <td class="transaction-id">${transaction.id}</td>
                        <td class="transaction-title">${transaction.title}</td>
                        <td><span class="transaction-unit">${transaction.unit}</span></td>
                        <td><span class="transaction-type type-${transaction.type.toLowerCase()}">${transaction.type}</span></td>
                        <td>
                            <span class="transaction-status status-${transaction.status.toLowerCase()}">
                                <i class='bx ${getStatusIcon(transaction.status)}'></i> ${transaction.status}
                            </span>
                        </td>
                        <td class="transaction-amount amount-${transaction.type.toLowerCase()}">
                            ${transaction.type === 'Income' ? '+' : '-'} $${transaction.amount.toFixed(2)}
                        </td>
                        <td class="transaction-date">${formatDate(transaction.date)}</td>
                        <td>
                            <div class="transaction-actions">
                                <button class="transaction-action-btn" title="View details">
                                    <i class='bx bx-show'></i>
                                </button>
                                <button class="transaction-action-btn" title="Download ${transaction.type === 'Income' ? 'receipt' : 'invoice'}">
                                    <i class='bx bx-download'></i>
                                </button>
                            </div>
                        </td>
                    `;

                    tbody.appendChild(row);
                });
            }

            function updatePaginationControls(data) {
                const paginationInfo = document.getElementById('paginationInfo');
                const prevBtn = document.getElementById('prevPageBtn');
                const nextBtn = document.getElementById('paginationControls');

                // Update pagination info
                const start = (data.current_page - 1) * data.per_page + 1;
                const end = Math.min(data.current_page * data.per_page, data.total);
                paginationInfo.textContent = `Showing ${start} to ${end} of ${data.total} transactions`;

                // Update pagination buttons
                prevBtn.disabled = data.current_page === 1;

                // Clear existing page buttons
                nextBtn.innerHTML = '';

                // Add previous button
                const prevButton = document.createElement('button');
                prevButton.className = 'pagination-btn';
                prevButton.id = 'prevPageBtn';
                prevButton.disabled = data.current_page === 1;
                prevButton.innerHTML = '<i class="bx bx-chevron-left"></i> Previous';
                prevButton.addEventListener('click', () => {
                    if (currentPage > 1) {
                        currentPage--;
                        fetchTransactions();
                    }
                });
                nextBtn.appendChild(prevButton);

                // Add page numbers
                const startPage = Math.max(1, data.current_page - 2);
                const endPage = Math.min(data.last_page, data.current_page + 2);

                for (let i = startPage; i <= endPage; i++) {
                    const pageBtn = document.createElement('button');
                    pageBtn.className = `pagination-btn ${i === data.current_page ? 'active' : ''}`;
                    pageBtn.textContent = i;
                    pageBtn.addEventListener('click', () => {
                        currentPage = i;
                        fetchTransactions();
                    });
                    nextBtn.appendChild(pageBtn);
                }

                // Add next button
                const nextButton = document.createElement('button');
                nextButton.className = 'pagination-btn';
                nextButton.id = 'nextPageBtn';
                nextButton.disabled = data.current_page === data.last_page;
                nextButton.innerHTML = 'Next <i class="bx bx-chevron-right"></i>';
                nextButton.addEventListener('click', () => {
                    if (currentPage < data.last_page) {
                        currentPage++;
                        fetchTransactions();
                    }
                });
                nextBtn.appendChild(nextButton);
            }

            // Helper functions
            function getStatusIcon(status) {
                switch(status.toLowerCase()) {
                    case 'completed': return 'bx-check-circle';
                    case 'pending': return 'bx-time';
                    case 'rejected': return 'bx-x-circle';
                    default: return 'bx-circle';
                }
            }

            function formatDate(dateString) {
                const options = { year: 'numeric', month: 'short', day: 'numeric' };
                return new Date(dateString).toLocaleDateString('en-US', options);
            }

            // Chart Rendering Functions (same as before, but using API data)
            function renderIncomeExpenseChart(data) {
                const ctx = document.getElementById('incomeExpenseChart');
                if (!ctx) return;

                if (incomeExpenseChart) incomeExpenseChart.destroy();

                incomeExpenseChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Income', 'Expenses'],
                        datasets: [{
                            data: [data.income, data.expenses],
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
                            backgroundColor: data.colors,
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

                document.getElementById('incomeSourcesValue').textContent = data.labels.length + ' Sources';
            }

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
                            backgroundColor: data.colors,
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

                document.getElementById('expenseCategoriesValue').textContent = data.labels.length + ' Categories';
            }

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
                                data: data.available,
                                backgroundColor: '#e0e0e0',
                                borderRadius: 4,
                                borderWidth: 0
                            },
                            {
                                label: 'Rented',
                                data: data.rented,
                                backgroundColor: '#184E83',
                                borderRadius: 4,
                                borderWidth: 0
                            },
                            {
                                label: 'Sold',
                                data: data.sold,
                                backgroundColor: '#2ecc71',
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
                                    text: getTimePeriodLabel()
                                }
                            }
                        }
                    }
                });
            }

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
                            backgroundColor: data.colors,
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
                                data: data.active,
                                borderColor: data.colors[0],
                                backgroundColor: data.colors[0] + '20',
                                borderWidth: 2,
                                tension: 0.3,
                                fill: true,
                                pointBackgroundColor: data.colors[0],
                                pointRadius: 4,
                                pointHoverRadius: 6
                            },
                            {
                                label: 'Expired Members',
                                data: data.expired,
                                borderColor: data.colors[1],
                                backgroundColor: data.colors[1] + '20',
                                borderWidth: 2,
                                tension: 0.3,
                                fill: true,
                                pointBackgroundColor: data.colors[1],
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
                                backgroundColor: data.colors[0],
                                borderRadius: 4,
                                borderWidth: 0
                            },
                            {
                                label: 'Pending',
                                data: data.pending,
                                backgroundColor: data.colors[1],
                                borderRadius: 4,
                                borderWidth: 0
                            },
                            {
                                label: 'Rejected',
                                data: data.rejected,
                                backgroundColor: data.colors[2],
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
                                },
                                stacked: true
                            }
                        }
                    }
                });
            }

            // Export functions (same as before)
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

                setTimeout(() => {
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

            // Initialize the dashboard
            initDashboard();
        });
    </script>
@endpush
