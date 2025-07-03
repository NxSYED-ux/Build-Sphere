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
                                <h1>Building Management Dashboard</h1>
                                <p class="report-description">
                                    Comprehensive overview of financial performance, occupancy rates, and operational metrics.
                                </p>
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
                                    <div class="value" id="totalUnits">248</div>
                                    <div class="trend up">
                                        <i class='bx bx-up-arrow-alt'></i> <span id="unitsChange">5%</span> from last period
                                    </div>
                                </div>
                                <div class="metric-card">
                                    <h4>Total Levels</h4>
                                    <div class="value" id="totalLevels">24</div>
                                    <div class="trend up">
                                        <i class='bx bx-up-arrow-alt'></i> <span id="levelsChange">0%</span> from last period
                                    </div>
                                </div>
                                <div class="metric-card">
                                    <h4>Total Income</h4>
                                    <div class="value" id="totalIncome">$24,580</div>
                                    <div class="trend up">
                                        <i class='bx bx-up-arrow-alt'></i> <span id="incomeChange">12%</span> from last period
                                    </div>
                                </div>
                                <div class="metric-card">
                                    <h4>Total Expenses</h4>
                                    <div class="value" id="totalExpenses">$8,420</div>
                                    <div class="trend down">
                                        <i class='bx bx-down-arrow-alt'></i> <span id="expensesChange">5%</span> from last period
                                    </div>
                                </div>
                            </div>

                            <!-- Income vs Expense Pie Chart with Summary -->
                            <div class="chart-summary-row">
                                <div class="chart-container">
                                    <div class="chart-header">
                                        <h3>Income vs Expenses</h3>
                                        <div class="chart-value" id="profitValue">$16,160 Net Profit</div>
                                    </div>
                                    <div class="chart-canvas-container">
                                        <canvas id="incomeExpenseChart"></canvas>
                                    </div>
                                </div>
                                <div class="summary-container">
                                    <h3 class="summary-title">Financial Summary</h3>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-dollar-circle'></i> Total Income</span>
                                        <span class="summary-value" id="summaryIncome">$24,580</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-money-withdraw'></i> Total Expenses</span>
                                        <span class="summary-value" id="summaryExpenses">$8,420</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-trending-up'></i> Net Profit</span>
                                        <span class="summary-value positive" id="summaryProfit">$16,160</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-line-chart'></i> Profit Margin</span>
                                        <span class="summary-value positive" id="summaryMargin">65.7%</span>
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

                            <!-- Income and Expense Distribution -->
                            <div class="chart-summary-row">
                                <div class="chart-container">
                                    <div class="chart-header">
                                        <h3>Income Sources</h3>
                                        <div class="chart-value" id="incomeSourcesValue">4 Sources</div>
                                    </div>
                                    <div class="chart-canvas-container">
                                        <canvas id="incomeSourcesChart"></canvas>
                                    </div>
                                </div>
                                <div class="chart-container">
                                    <div class="chart-header">
                                        <h3>Expense Categories</h3>
                                        <div class="chart-value" id="expenseCategoriesValue">5 Categories</div>
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
                                    <div class="chart-value" id="occupancyRate">92% Occupancy Rate</div>
                                </div>
                                <div class="chart-canvas-container">
                                    <canvas id="occupancyChart"></canvas>
                                </div>
                                <div class="status-grid">
                                    <div class="status-card">
                                        <div class="status-count" style="color: var(--primary);">200</div>
                                        <div class="status-label">Rented Units</div>
                                    </div>
                                    <div class="status-card">
                                        <div class="status-count" style="color: var(--success);">28</div>
                                        <div class="status-label">Sold Units</div>
                                    </div>
                                    <div class="status-card">
                                        <div class="status-count" style="color: var(--gray);">20</div>
                                        <div class="status-label">Available Units</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Human Resources Donut Chart with Summary -->
                            <div class="chart-summary-row">
                                <div class="chart-container">
                                    <div class="chart-header">
                                        <h3>Staff by Department</h3>
                                        <div class="chart-value" id="totalStaff">48 Employees</div>
                                    </div>
                                    <div class="chart-canvas-container">
                                        <canvas id="staffChart"></canvas>
                                    </div>
                                </div>
                                <div class="summary-container">
                                    <h3 class="summary-title">Staff Distribution</h3>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-wrench'></i> Maintenance</span>
                                        <span class="summary-value" id="summaryMaintenance">18</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-shield-alt-2'></i> Security</span>
                                        <span class="summary-value" id="summarySecurity">12</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-broom'></i> Cleaning</span>
                                        <span class="summary-value" id="summaryCleaning">10</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-briefcase'></i> Administration</span>
                                        <span class="summary-value" id="summaryAdmin">5</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-group'></i> Other</span>
                                        <span class="summary-value" id="summaryOther">3</span>
                                    </div>
                                    <div class="progress-container">
                                        <div class="progress-item">
                                            <div class="progress-header">
                                                <span class="progress-label">Staff Satisfaction</span>
                                                <span class="progress-value positive">84%</span>
                                            </div>
                                            <div class="progress-bar">
                                                <div class="progress-fill" style="width: 84%; background-color: var(--success);"></div>
                                            </div>
                                        </div>
                                        <div class="progress-item">
                                            <div class="progress-header">
                                                <span class="progress-label">Turnover Rate</span>
                                                <span class="progress-value negative">12%</span>
                                            </div>
                                            <div class="progress-bar">
                                                <div class="progress-fill" style="width: 12%; background-color: var(--danger);"></div>
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
                                        <div class="chart-value" id="activeMembers">142 Active</div>
                                    </div>
                                    <div class="chart-canvas-container">
                                        <canvas id="membershipChart"></canvas>
                                    </div>
                                </div>
                                <div class="summary-container">
                                    <h3 class="summary-title">Membership Analytics</h3>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-user'></i> Total Members</span>
                                        <span class="summary-value" id="summaryTotalMembers">185</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-check-circle'></i> Active Members</span>
                                        <span class="summary-value" id="summaryActiveMembers">142</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-time'></i> Expired Members</span>
                                        <span class="summary-value" id="summaryExpiredMembers">43</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-plus-circle'></i> New This Period</span>
                                        <span class="summary-value positive" id="summaryNewMembers">+24</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label"><i class='bx bx-refresh'></i> Renewal Rate</span>
                                        <span class="summary-value positive" id="summaryRenewalRate">78%</span>
                                    </div>
                                    <div class="progress-container">
                                        <div class="progress-item">
                                            <div class="progress-header">
                                                <span class="progress-label">Membership Growth</span>
                                                <span class="progress-value positive">+15%</span>
                                            </div>
                                            <div class="progress-bar">
                                                <div class="progress-fill" style="width: 65%; background-color: var(--primary);"></div>
                                            </div>
                                        </div>
                                        <div class="progress-item">
                                            <div class="progress-header">
                                                <span class="progress-label">Engagement Rate</span>
                                                <span class="progress-value positive">72%</span>
                                            </div>
                                            <div class="progress-bar">
                                                <div class="progress-fill" style="width: 72%; background-color: var(--success);"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Maintenance Requests Chart (Full Width) -->
                            <div class="full-width-chart">
                                <div class="chart-header">
                                    <h3>Maintenance Requests</h3>
                                    <div class="chart-value" id="totalRequests">142 Requests</div>
                                </div>
                                <div class="chart-canvas-container">
                                    <canvas id="maintenanceChart"></canvas>
                                </div>
                                <div class="status-grid">
                                    <div class="status-card">
                                        <div class="status-count" style="color: var(--success);">87</div>
                                        <div class="status-label">Completed</div>
                                    </div>
                                    <div class="status-card">
                                        <div class="status-count" style="color: var(--warning);">38</div>
                                        <div class="status-label">In Progress</div>
                                    </div>
                                    <div class="status-card">
                                        <div class="status-count" style="color: var(--danger);">17</div>
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
                                    <table class="transactions-table">
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
                                        <tr>
                                            <td class="transaction-id">TX-789456</td>
                                            <td class="transaction-title">June Rent Payment</td>
                                            <td><span class="transaction-unit">Apt 302</span></td>
                                            <td><span class="transaction-type type-income">Income</span></td>
                                            <td>
                                        <span class="transaction-status status-active">
                                            <i class='bx bx-check-circle'></i> Completed
                                        </span>
                                            </td>
                                            <td class="transaction-amount amount-income">+ $1,200.00</td>
                                            <td class="transaction-date">Jun 5, 2023</td>
                                            <td>
                                                <div class="transaction-actions">
                                                    <button class="transaction-action-btn" title="View details">
                                                        <i class='bx bx-show'></i>
                                                    </button>
                                                    <button class="transaction-action-btn" title="Download receipt">
                                                        <i class='bx bx-download'></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="transaction-id">TX-789455</td>
                                            <td class="transaction-title">HVAC Maintenance</td>
                                            <td><span class="transaction-unit">Common Area</span></td>
                                            <td><span class="transaction-type type-expense">Expense</span></td>
                                            <td>
                                        <span class="transaction-status status-pending">
                                            <i class='bx bx-time'></i> Pending
                                        </span>
                                            </td>
                                            <td class="transaction-amount amount-expense">- $450.00</td>
                                            <td class="transaction-date">Jun 4, 2023</td>
                                            <td>
                                                <div class="transaction-actions">
                                                    <button class="transaction-action-btn" title="View details">
                                                        <i class='bx bx-show'></i>
                                                    </button>
                                                    <button class="transaction-action-btn" title="Download invoice">
                                                        <i class='bx bx-download'></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="transaction-id">TX-789454</td>
                                            <td class="transaction-title">Parking Fee</td>
                                            <td><span class="transaction-unit">P-042</span></td>
                                            <td><span class="transaction-type type-income">Income</span></td>
                                            <td>
                                        <span class="transaction-status status-active">
                                            <i class='bx bx-check-circle'></i> Completed
                                        </span>
                                            </td>
                                            <td class="transaction-amount amount-income">+ $75.00</td>
                                            <td class="transaction-date">Jun 3, 2023</td>
                                            <td>
                                                <div class="transaction-actions">
                                                    <button class="transaction-action-btn" title="View details">
                                                        <i class='bx bx-show'></i>
                                                    </button>
                                                    <button class="transaction-action-btn" title="Download receipt">
                                                        <i class='bx bx-download'></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="transaction-id">TX-789453</td>
                                            <td class="transaction-title">Cleaning Supplies</td>
                                            <td><span class="transaction-unit">Common Area</span></td>
                                            <td><span class="transaction-type type-expense">Expense</span></td>
                                            <td>
                                        <span class="transaction-status status-active">
                                            <i class='bx bx-check-circle'></i> Completed
                                        </span>
                                            </td>
                                            <td class="transaction-amount amount-expense">- $120.50</td>
                                            <td class="transaction-date">Jun 2, 2023</td>
                                            <td>
                                                <div class="transaction-actions">
                                                    <button class="transaction-action-btn" title="View details">
                                                        <i class='bx bx-show'></i>
                                                    </button>
                                                    <button class="transaction-action-btn" title="Download invoice">
                                                        <i class='bx bx-download'></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="transaction-id">TX-789452</td>
                                            <td class="transaction-title">May Rent Payment</td>
                                            <td><span class="transaction-unit">Apt 105</span></td>
                                            <td><span class="transaction-type type-income">Income</span></td>
                                            <td>
                                        <span class="transaction-status status-active">
                                            <i class='bx bx-check-circle'></i> Completed
                                        </span>
                                            </td>
                                            <td class="transaction-amount amount-income">+ $950.00</td>
                                            <td class="transaction-date">Jun 1, 2023</td>
                                            <td>
                                                <div class="transaction-actions">
                                                    <button class="transaction-action-btn" title="View details">
                                                        <i class='bx bx-show'></i>
                                                    </button>
                                                    <button class="transaction-action-btn" title="Download receipt">
                                                        <i class='bx bx-download'></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="transaction-id">TX-789451</td>
                                            <td class="transaction-title">Security Service</td>
                                            <td><span class="transaction-unit">Building</span></td>
                                            <td><span class="transaction-type type-expense">Expense</span></td>
                                            <td>
                                        <span class="transaction-status status-closed">
                                            <i class='bx bx-x-circle'></i> Rejected
                                        </span>
                                            </td>
                                            <td class="transaction-amount amount-expense">- $1,200.00</td>
                                            <td class="transaction-date">May 30, 2023</td>
                                            <td>
                                                <div class="transaction-actions">
                                                    <button class="transaction-action-btn" title="View details">
                                                        <i class='bx bx-show'></i>
                                                    </button>
                                                    <button class="transaction-action-btn" title="Download invoice">
                                                        <i class='bx bx-download'></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="transaction-id">TX-789450</td>
                                            <td class="transaction-title">Gym Membership</td>
                                            <td><span class="transaction-unit">Resident</span></td>
                                            <td><span class="transaction-type type-income">Income</span></td>
                                            <td>
                                        <span class="transaction-status status-active">
                                            <i class='bx bx-check-circle'></i> Completed
                                        </span>
                                            </td>
                                            <td class="transaction-amount amount-income">+ $50.00</td>
                                            <td class="transaction-date">May 28, 2023</td>
                                            <td>
                                                <div class="transaction-actions">
                                                    <button class="transaction-action-btn" title="View details">
                                                        <i class='bx bx-show'></i>
                                                    </button>
                                                    <button class="transaction-action-btn" title="Download receipt">
                                                        <i class='bx bx-download'></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="pagination-container">
                                    <div class="pagination-info">
                                        Showing 1 to 7 of 42 transactions
                                    </div>
                                    <div class="pagination-controls">
                                        <button class="pagination-btn" disabled>
                                            <i class='bx bx-chevron-left'></i> Previous
                                        </button>
                                        <button class="pagination-btn active">1</button>
                                        <button class="pagination-btn">2</button>
                                        <button class="pagination-btn">3</button>
                                        <button class="pagination-btn">4</button>
                                        <button class="pagination-btn">5</button>
                                        <button class="pagination-btn">
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sample data for different buildings and time periods
            const buildingData = {
                'all': {
                    units: 248,
                    levels: 24,
                    income: 24580,
                    expenses: 8420,
                    incomeSources: {
                        labels: ['Rent', 'Parking', 'Amenities', 'Other'],
                        data: [18000, 4200, 1500, 880],
                        colors: ['#184E83', '#1A6FC9', '#2ecc71', '#ffbe0b']
                    },
                    expenseCategories: {
                        labels: ['Maintenance', 'Utilities', 'Staff', 'Insurance', 'Other'],
                        data: [3200, 2800, 1500, 500, 420],
                        colors: ['#ff4d6d', '#ff758f', '#ff8fa3', '#ffb3c1', '#ffccd5']
                    },
                    occupancy: {
                        labels: ['Available', 'Rented', 'Sold'],
                        data: [20, 200, 28],
                        colors: ['#e0e0e0', '#184E83', '#2ecc71']
                    },
                    staff: {
                        labels: ['Maintenance', 'Security', 'Cleaning', 'Admin', 'Other'],
                        data: [18, 12, 10, 5, 3],
                        colors: ['#184E83', '#1A6FC9', '#2ecc71', '#ffbe0b', '#ff4d6d']
                    },
                    memberships: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        active: [120, 125, 130, 135, 140, 142],
                        expired: [40, 38, 42, 41, 40, 43],
                        colors: ['#184E83', '#ff4d6d']
                    },
                    maintenance: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        completed: [10, 12, 15, 18, 16, 16],
                        pending: [8, 6, 7, 5, 6, 6],
                        rejected: [2, 3, 1, 4, 3, 4],
                        colors: ['#2ecc71', '#ffbe0b', '#ff4d6d']
                    }
                },
                '1': { // Downtown Tower
                    units: 80,
                    levels: 8,
                    income: 8500,
                    expenses: 3200,
                    incomeSources: {
                        labels: ['Rent', 'Parking', 'Amenities', 'Other'],
                        data: [6000, 1500, 700, 300],
                        colors: ['#184E83', '#1A6FC9', '#2ecc71', '#ffbe0b']
                    },
                    expenseCategories: {
                        labels: ['Maintenance', 'Utilities', 'Staff', 'Insurance', 'Other'],
                        data: [1200, 1000, 600, 200, 200],
                        colors: ['#ff4d6d', '#ff758f', '#ff8fa3', '#ffb3c1', '#ffccd5']
                    },
                    occupancy: {
                        labels: ['Available', 'Rented', 'Sold'],
                        data: [6, 70, 4],
                        colors: ['#e0e0e0', '#184E83', '#2ecc71']
                    },
                    staff: {
                        labels: ['Maintenance', 'Security', 'Cleaning', 'Admin', 'Other'],
                        data: [6, 4, 5, 3, 2],
                        colors: ['#184E83', '#1A6FC9', '#2ecc71', '#ffbe0b', '#ff4d6d']
                    },
                    memberships: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        active: [40, 42, 44, 46, 48, 50],
                        expired: [15, 14, 16, 15, 14, 15],
                        colors: ['#184E83', '#ff4d6d']
                    },
                    maintenance: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        completed: [4, 5, 6, 7, 6, 6],
                        pending: [3, 2, 2, 1, 2, 2],
                        rejected: [1, 1, 0, 1, 1, 1],
                        colors: ['#2ecc71', '#ffbe0b', '#ff4d6d']
                    }
                },
                // Similar data structures for other buildings (2, 3, 4)
            };

            // Chart instances
            let incomeExpenseChart, incomeSourcesChart, expenseCategoriesChart,
                occupancyChart, staffChart, membershipChart, maintenanceChart;

            // Current filters
            let currentBuilding = 'all';
            let currentRange = '30days';

            // Initialize the dashboard
            function initDashboard() {
                renderAllCharts();
                setupEventListeners();
                updateMetrics();
            }

            // Set up event listeners
            function setupEventListeners() {
                // Building selection change
                document.getElementById('buildingSelect').addEventListener('change', function() {
                    currentBuilding = this.value;
                    renderAllCharts();
                    updateMetrics();
                });

                // Date range change
                document.getElementById('dateRange').addEventListener('change', function() {
                    currentRange = this.value;

                    // Show/hide custom date inputs
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
                        renderAllCharts();
                        updateMetrics();
                    }
                });

                // Custom date changes
                document.getElementById('startDate').addEventListener('change', function() {
                    const startDate = new Date(this.value);
                    const endDate = new Date(document.getElementById('endDate').value);
                    if (startDate && endDate) {
                        renderAllCharts();
                        updateMetrics();
                    }
                });

                document.getElementById('endDate').addEventListener('change', function() {
                    const startDate = new Date(document.getElementById('startDate').value);
                    const endDate = new Date(this.value);
                    if (startDate && endDate) {
                        renderAllCharts();
                        updateMetrics();
                    }
                });
            }

            // Render all charts
            function renderAllCharts() {
                const data = buildingData[currentBuilding] || buildingData['all'];

                renderIncomeExpenseChart(data);
                renderIncomeSourcesChart(data.incomeSources);
                renderExpenseCategoriesChart(data.expenseCategories);
                renderOccupancyChart(data.occupancy);
                renderStaffChart(data.staff);
                renderMembershipChart(data.memberships);
                renderMaintenanceChart(data.maintenance);
            }

            // Update all metrics
            function updateMetrics() {
                const data = buildingData[currentBuilding] || buildingData['all'];

                // Update metric cards
                document.getElementById('totalUnits').textContent = data.units;
                document.getElementById('totalLevels').textContent = data.levels;
                document.getElementById('totalIncome').textContent = '$' + data.income.toLocaleString();
                document.getElementById('totalExpenses').textContent = '$' + data.expenses.toLocaleString();

                // Update financial summary
                const profit = data.income - data.expenses;
                const margin = (profit / data.income) * 100;

                document.getElementById('profitValue').textContent = '$' + profit.toLocaleString() + ' Net Profit';
                document.getElementById('summaryIncome').textContent = '$' + data.income.toLocaleString();
                document.getElementById('summaryExpenses').textContent = '$' + data.expenses.toLocaleString();
                document.getElementById('summaryProfit').textContent = '$' + profit.toLocaleString();
                document.getElementById('summaryMargin').textContent = margin.toFixed(1) + '%';

                // Update occupancy rate
                const occupied = data.occupancy.data[1] + data.occupancy.data[2];
                const total = data.units;
                const occupancyRate = (occupied / total) * 100;
                document.getElementById('occupancyRate').textContent = occupancyRate.toFixed(0) + '% Occupancy Rate';

                // Update staff summary
                document.getElementById('totalStaff').textContent = data.staff.data.reduce((a, b) => a + b, 0) + ' Employees';
                document.getElementById('summaryMaintenance').textContent = data.staff.data[0];
                document.getElementById('summarySecurity').textContent = data.staff.data[1];
                document.getElementById('summaryCleaning').textContent = data.staff.data[2];
                document.getElementById('summaryAdmin').textContent = data.staff.data[3];
                document.getElementById('summaryOther').textContent = data.staff.data[4];

                // Update membership summary
                const active = data.memberships.active[data.memberships.active.length - 1];
                const expired = data.memberships.expired[data.memberships.expired.length - 1];
                const totalMembers = active + expired;
                const newMembers = Math.round(active * 0.2); // Simulate 20% new
                const renewalRate = Math.round((active - newMembers) / (active) * 100);

                document.getElementById('activeMembers').textContent = active + ' Active';
                document.getElementById('summaryTotalMembers').textContent = totalMembers;
                document.getElementById('summaryActiveMembers').textContent = active;
                document.getElementById('summaryExpiredMembers').textContent = expired;
                document.getElementById('summaryNewMembers').textContent = '+' + newMembers;
                document.getElementById('summaryRenewalRate').textContent = renewalRate + '%';

                // Update maintenance requests
                const completed = data.maintenance.completed.reduce((a, b) => a + b, 0);
                const pending = data.maintenance.pending.reduce((a, b) => a + b, 0);
                const rejected = data.maintenance.rejected.reduce((a, b) => a + b, 0);
                const totalRequests = completed + pending + rejected;

                document.getElementById('totalRequests').textContent = totalRequests + ' Requests';
                document.querySelectorAll('.status-count')[0].textContent = completed;
                document.querySelectorAll('.status-count')[1].textContent = pending;
                document.querySelectorAll('.status-count')[2].textContent = rejected;
            }

            // Income vs Expense Pie Chart
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

            // Occupancy Chart with Time Period
            function renderOccupancyChart(data) {
                const ctx = document.getElementById('occupancyChart');
                if (!ctx) return;

                if (occupancyChart) occupancyChart.destroy();

                // Determine time period labels based on selected range
                let timeLabels = [];
                if (currentRange === '7days') {
                    timeLabels = ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'];
                } else if (currentRange === '30days') {
                    timeLabels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
                } else if (currentRange === '90days') {
                    timeLabels = ['Month 1', 'Month 2', 'Month 3'];
                } else if (currentRange === 'custom') {
                    // For custom range, show month names if more than 1 month, otherwise weeks/days
                    const startDate = document.getElementById('startDate').valueAsDate;
                    const endDate = document.getElementById('endDate').valueAsDate;

                    if (startDate && endDate) {
                        const diffTime = Math.abs(endDate - startDate);
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                        if (diffDays > 60) {
                            // More than 2 months - show monthly
                            const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                            const startMonth = startDate.getMonth();
                            const endMonth = endDate.getMonth();
                            const yearDiff = endDate.getFullYear() - startDate.getFullYear();
                            const totalMonths = (yearDiff * 12) + (endMonth - startMonth) + 1;

                            for (let i = 0; i < totalMonths; i++) {
                                const monthIndex = (startMonth + i) % 12;
                                const year = startDate.getFullYear() + Math.floor((startMonth + i) / 12);
                                timeLabels.push(`${monthNames[monthIndex]} ${year}`);
                            }
                        } else if (diffDays > 14) {
                            // 2 weeks to 2 months - show weekly
                            const weeks = Math.ceil(diffDays / 7);
                            for (let i = 0; i < weeks; i++) {
                                timeLabels.push(`Week ${i+1}`);
                            }
                        } else {
                            // Less than 2 weeks - show daily
                            for (let i = 0; i < diffDays; i++) {
                                const date = new Date(startDate);
                                date.setDate(date.getDate() + i);
                                timeLabels.push(date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
                            }
                        }
                    }
                }

                // Generate sample data based on time period
                const availableData = timeLabels.map(() => Math.floor(Math.random() * 10) + 5);
                const rentedData = timeLabels.map(() => Math.floor(Math.random() * 50) + 150);
                const soldData = timeLabels.map(() => Math.floor(Math.random() * 10) + 5);

                occupancyChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: timeLabels,
                        datasets: [
                            {
                                label: 'Available',
                                data: availableData,
                                backgroundColor: '#e0e0e0',
                                borderRadius: 4,
                                borderWidth: 0
                            },
                            {
                                label: 'Rented',
                                data: rentedData,
                                backgroundColor: '#184E83',
                                borderRadius: 4,
                                borderWidth: 0
                            },
                            {
                                label: 'Sold',
                                data: soldData,
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

                // Update status cards with current period data
                const currentAvailable = availableData[availableData.length - 1];
                const currentRented = rentedData[rentedData.length - 1];
                const currentSold = soldData[soldData.length - 1];
                const totalUnits = currentAvailable + currentRented + currentSold;
                const occupancyRate = Math.round(((currentRented + currentSold) / totalUnits) * 100);

                document.getElementById('occupancyRate').textContent = `${occupancyRate}% Occupancy Rate`;
                document.querySelectorAll('.status-count')[0].textContent = currentRented;
                document.querySelectorAll('.status-count')[1].textContent = currentSold;
                document.querySelectorAll('.status-count')[2].textContent = currentAvailable;
            }

            function getTimePeriodLabel() {
                switch(currentRange) {
                    case '7days': return 'Days';
                    case '30days': return 'Weeks';
                    case '90days': return 'Months';
                    case 'custom':
                        const startDate = document.getElementById('startDate').valueAsDate;
                        const endDate = document.getElementById('endDate').valueAsDate;
                        if (startDate && endDate) {
                            const diffTime = Math.abs(endDate - startDate);
                            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                            if (diffDays > 60) return 'Months';
                            if (diffDays > 14) return 'Weeks';
                            return 'Days';
                        }
                        return 'Time Period';
                    default: return 'Time Period';
                }
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

            // Initialize the dashboard
            initDashboard();
        });
    </script>
@endpush
