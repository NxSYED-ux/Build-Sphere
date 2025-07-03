@extends('layouts.app')

@section('title', 'Building Analytics Dashboard')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #3a86ff;
            --primary-light: #83b2ff;
            --primary-dark: #2667cc;
            --secondary: #8338ec;
            --danger: #ff006e;
            --warning: #ffbe0b;
            --success: #06d6a0;
            --dark: #2b2d42;
            --light: #f8f9fa;
            --gray: #8d99ae;
            --light-gray: #edf2f4;
            --table-header-bg: #f1f5f9;
            --table-row-hover: #f8fafc;
        }

        body {
        }

        #main {
            margin-top: 25px;
        }

        .dashboard-container {
            padding: 2rem;
            max-width: 1800px;
            margin: 0 auto;
        }

        .dashboard-header {
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dashboard-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark);
            margin: 0;
        }

        .dashboard-subtitle {
            color: var(--gray);
            font-size: 1rem;
            margin-top: 0.5rem;
        }

        .filters-container {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .filter-group {
            flex: 1;
            min-width: 220px;
        }

        .filter-label {
            display: block;
            margin-bottom: 0.75rem;
            font-size: 0.875rem;
            color: var(--dark);
            font-weight: 500;
        }

        .filter-select, .filter-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: white;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .filter-select:focus, .filter-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(58, 134, 255, 0.1);
            outline: none;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .metric-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 1.5rem;
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
            overflow: hidden;
        }

        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.08);
        }

        .metric-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--primary);
        }

        .metric-title {
            font-size: 0.875rem;
            color: var(--gray);
            margin-bottom: 0.75rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .metric-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .metric-trend {
            display: inline-flex;
            align-items: center;
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
        }

        .trend-up {
            background: rgba(6, 214, 160, 0.1);
            color: var(--success);
        }

        .trend-down {
            background: rgba(255, 0, 110, 0.1);
            color: var(--danger);
        }

        .trend-neutral {
            background: rgba(139, 149, 174, 0.1);
            color: var(--gray);
        }

        /* Section Styling */
        .dashboard-section {
            margin-bottom: 2.5rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
        }

        .section-description {
            color: var(--gray);
            font-size: 0.95rem;
            margin-top: 0.5rem;
            line-height: 1.5;
        }

        /* Financial Overview Section */
        .financial-container {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .financial-chart-container {
            flex: 2;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 1.5rem;
        }

        .financial-summary-container {
            flex: 1;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 1.5rem;
        }

        .summary-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 1rem;
        }

        .summary-text {
            color: var(--gray);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .summary-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .stat-item {
            padding: 1rem;
            border-radius: 8px;
            background: var(--light-gray);
            transition: transform 0.2s;
        }

        .stat-item:hover {
            transform: translateY(-2px);
        }

        .stat-label {
            font-size: 0.75rem;
            color: var(--gray);
            margin-bottom: 0.5rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.25rem;
        }

        .stat-detail {
            font-size: 0.75rem;
            color: var(--gray);
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        /* Chart Styling */
        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.25rem;
        }

        .chart-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
        }

        .chart-actions {
            display: flex;
            gap: 0.5rem;
        }

        .chart-btn {
            background: none;
            border: none;
            color: var(--gray);
            cursor: pointer;
            font-size: 1rem;
            transition: color 0.2s;
        }

        .chart-btn:hover {
            color: var(--primary);
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
            flex-grow: 1;
        }

        /* Two-column layout */
        .two-column-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .column-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 1.5rem;
            transition: transform 0.2s;
        }

        .column-card:hover {
            transform: translateY(-2px);
        }

        /* Full-width cards */
        .full-width-card {
            grid-column: 1 / -1;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        /* Detail Grid */
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            padding: 1rem;
            background: var(--light-gray);
            border-radius: 8px;
            transition: transform 0.2s;
        }

        .detail-item:hover {
            transform: translateY(-2px);
        }

        .detail-label {
            font-size: 0.75rem;
            color: var(--gray);
            margin-bottom: 0.25rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-data {
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
            gap: 0.25rem;
        }

        .badge-icon {
            font-size: 0.875rem;
        }

        .status-active {
            background: rgba(6, 214, 160, 0.1);
            color: var(--success);
        }

        .status-pending {
            background: rgba(255, 190, 11, 0.1);
            color: var(--warning);
        }

        .status-closed {
            background: rgba(255, 0, 110, 0.1);
            color: var(--danger);
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

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .metrics-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .financial-container {
                flex-direction: column;
            }

            .two-column-container {
                grid-template-columns: 1fr;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }

            .summary-stats {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .filters-container {
                flex-direction: column;
            }

            .metrics-grid {
                grid-template-columns: 1fr;
            }

            .transactions-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>
@endpush

@section('content')
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('owner_manager_dashboard'), 'label' => 'Dashboard'],
            ['url' => '', 'label' => 'Buildings Reports']
        ]"
    />

    <x-Owner.side-navbar :openSections="['Reports', 'BuildingsReports']" />
    <x-error-success-model />
    <div id="main">
        <section class="content">
            <div class="dashboard-container">
                <!-- Header -->
                <div class="dashboard-header">
                    <div>
                        <h1 class="dashboard-title">Building Analytics Dashboard</h1>
                        <p class="dashboard-subtitle">Comprehensive overview of your building performance and operations</p>
                    </div>
                    <div>
                        <button class="btn btn-primary">
                            <i class='bx bx-download'></i> Export Report
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filters-container">
                    <div class="filter-group">
                        <label class="filter-label" for="dateRange">Date Range</label>
                        <select class="filter-select" id="dateRange">
                            <option value="7days">Last 7 Days</option>
                            <option value="30days" selected>Last 30 Days</option>
                            <option value="90days">Last 90 Days</option>
                            <option value="month">This Month</option>
                            <option value="quarter">This Quarter</option>
                            <option value="year">This Year</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    <div class="filter-group" id="customDateRange" style="display: none;">
                        <label class="filter-label" for="startDate">Start Date</label>
                        <input type="date" class="filter-input" id="startDate">
                    </div>
                    <div class="filter-group" id="customDateRangeEnd" style="display: none;">
                        <label class="filter-label" for="endDate">End Date</label>
                        <input type="date" class="filter-input" id="endDate">
                    </div>
                    <div class="filter-group">
                        <label class="filter-label" for="buildingSelect">Building</label>
                        <select class="filter-select" id="buildingSelect">
                            <option value="all" selected>All Buildings</option>
                            <option value="1">Downtown Tower</option>
                            <option value="2">Riverside Apartments</option>
                            <option value="3">Hillside Complex</option>
                            <option value="4">Central Plaza</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label" for="transactionType">Transaction Type</label>
                        <select class="filter-select" id="transactionType">
                            <option value="all" selected>All Transactions</option>
                            <option value="income">Income</option>
                            <option value="expense">Expenses</option>
                        </select>
                    </div>
                </div>

                <!-- Key Metrics -->
                <div class="metrics-grid">
                    <div class="metric-card">
                        <div class="metric-title">
                            <i class='bx bx-home'></i> Total Units
                        </div>
                        <div class="metric-value">120</div>
                        <div class="metric-trend trend-up">
                            <i class='bx bx-up-arrow-alt'></i> 5.2% from last month
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-title">
                            <i class='bx bx-layer'></i> Total Levels
                        </div>
                        <div class="metric-value">15</div>
                        <div class="metric-trend trend-neutral">
                            <i class='bx bx-minus'></i> No change
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-title">
                            <i class='bx bx-money'></i> Total Income
                        </div>
                        <div class="metric-value">$24,580</div>
                        <div class="metric-trend trend-up">
                            <i class='bx bx-up-arrow-alt'></i> 12.4% from last month
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-title">
                            <i class='bx bx-credit-card'></i> Total Expenses
                        </div>
                        <div class="metric-value">$8,420</div>
                        <div class="metric-trend trend-down">
                            <i class='bx bx-down-arrow-alt'></i> 5.1% from last month
                        </div>
                    </div>
                </div>

                <!-- Financial Overview Section -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2 class="section-title">Financial Overview</h2>
                    </div>
                    <p class="section-description">
                        A comprehensive view of your building's financial performance, including income sources,
                        expense breakdowns, and net profitability metrics for the selected period.
                    </p>

                    <div class="financial-container">
                        <div class="financial-chart-container">
                            <div class="chart-header">
                                <h3 class="chart-title">Income vs Expenses</h3>
                                <div class="chart-actions">
                                    <button class="chart-btn" title="View details">
                                        <i class='bx bx-dots-horizontal-rounded'></i>
                                    </button>
                                </div>
                            </div>
                            <div class="chart-container">
                                <canvas id="incomeExpenseChart"></canvas>
                            </div>
                        </div>

                        <div class="financial-summary-container">
                            <h3 class="summary-title">Financial Summary</h3>
                            <p class="summary-text">
                                Your building has shown strong financial performance this period with a net profit
                                of $16,160, representing a 65.7% profit margin. Rental income continues to be
                                the primary revenue driver, accounting for 74.9% of total income.
                            </p>

                            <div class="summary-stats">
                                <div class="stat-item">
                                    <div class="stat-label">Net Profit</div>
                                    <div class="stat-value">$16,160</div>
                                    <div class="stat-detail">
                                        <i class='bx bx-up-arrow-alt trend-up'></i>
                                        <span class="trend-up">18.3% growth</span>
                                    </div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-label">Profit Margin</div>
                                    <div class="stat-value">65.7%</div>
                                    <div class="stat-detail">
                                        <i class='bx bx-up-arrow-alt trend-up'></i>
                                        <span class="trend-up">3.2% increase</span>
                                    </div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-label">Avg. Daily Income</div>
                                    <div class="stat-value">$819</div>
                                    <div class="stat-detail">
                                        <i class='bx bx-up-arrow-alt trend-up'></i>
                                        <span class="trend-up">$42 more</span>
                                    </div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-label">Avg. Daily Expense</div>
                                    <div class="stat-value">$281</div>
                                    <div class="stat-detail">
                                        <i class='bx bx-down-arrow-alt trend-down'></i>
                                        <span class="trend-down">$15 less</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Distribution -->
                    <div class="section-header">
                        <h3 class="section-title">Further Distribution of Income & Expenses</h3>
                    </div>

                    <div class="two-column-container">
                        <div class="column-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Income Trends</h3>
                                <div class="chart-actions">
                                    <button class="chart-btn" title="View details">
                                        <i class='bx bx-dots-horizontal-rounded'></i>
                                    </button>
                                </div>
                            </div>
                            <div class="chart-container">
                                <canvas id="incomeTrendsChart"></canvas>
                            </div>
                        </div>

                        <div class="column-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Expense Breakdown</h3>
                                <div class="chart-actions">
                                    <button class="chart-btn" title="View details">
                                        <i class='bx bx-dots-horizontal-rounded'></i>
                                    </button>
                                </div>
                            </div>
                            <div class="chart-container">
                                <canvas id="expenseBreakdownChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Unit Occupancy Section -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2 class="section-title">Unit Occupancy</h2>
                    </div>
                    <p class="section-description">
                        Track the occupancy rates and vacancy trends across your properties to optimize rental
                        strategies and identify potential revenue opportunities.
                    </p>

                    <div class="full-width-card">
                        <div class="chart-header">
                            <h3 class="chart-title">Occupancy Trends</h3>
                            <div class="chart-actions">
                                <button class="chart-btn" title="View details">
                                    <i class='bx bx-dots-horizontal-rounded'></i>
                                </button>
                            </div>
                        </div>
                        <div class="chart-container" style="height: 350px;">
                            <canvas id="unitOccupancyChart"></canvas>
                        </div>

                        <div class="detail-grid" style="margin-top: 1.5rem;">
                            <div class="detail-item">
                                <div class="detail-label">Total Units</div>
                                <div class="detail-data">120</div>
                                <div class="stat-detail">
                                    <i class='bx bx-up-arrow-alt trend-up'></i>
                                    <span>5 new this month</span>
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Occupied Units</div>
                                <div class="detail-data">94 (78.3%)</div>
                                <div class="stat-detail">
                                    <i class='bx bx-up-arrow-alt trend-up'></i>
                                    <span>4.2% increase</span>
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Vacant Units</div>
                                <div class="detail-data">26 (21.7%)</div>
                                <div class="stat-detail">
                                    <i class='bx bx-down-arrow-alt trend-down'></i>
                                    <span>3.8% decrease</span>
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Avg. Occupancy Rate</div>
                                <div class="detail-data">81.2%</div>
                                <div class="stat-detail">
                                    <i class='bx bx-up-arrow-alt trend-up'></i>
                                    <span>2.1% increase</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Human Resources Section -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2 class="section-title">Human Resources</h2>
                    </div>
                    <p class="section-description">
                        Overview of your staffing distribution, tenure, and turnover rates to help manage
                        your building's workforce effectively.
                    </p>

                    <div class="two-column-container">
                        <div class="column-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Staff Distribution</h3>
                                <div class="chart-actions">
                                    <button class="chart-btn" title="View details">
                                        <i class='bx bx-dots-horizontal-rounded'></i>
                                    </button>
                                </div>
                            </div>
                            <div class="chart-container">
                                <canvas id="staffDistributionChart"></canvas>
                            </div>
                        </div>

                        <div class="column-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Staff Analytics</h3>
                                <div class="chart-actions">
                                    <button class="chart-btn" title="View details">
                                        <i class='bx bx-dots-horizontal-rounded'></i>
                                    </button>
                                </div>
                            </div>
                            <div class="detail-grid">
                                <div class="detail-item">
                                    <div class="detail-label">Total Staff</div>
                                    <div class="detail-data">48</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">New Hires</div>
                                    <div class="detail-data">3 <span class="status-badge status-active">
                                    <i class='bx bx-up-arrow-alt badge-icon'></i> 6.7%
                                </span></div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Maintenance</div>
                                    <div class="detail-data">18 (37.5%)</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Security</div>
                                    <div class="detail-data">12 (25%)</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Cleaning</div>
                                    <div class="detail-data">10 (20.8%)</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Administration</div>
                                    <div class="detail-data">5 (10.4%)</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Avg. Tenure</div>
                                    <div class="detail-data">2.4 years</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Turnover Rate</div>
                                    <div class="detail-data">4.2% <span class="status-badge status-active">
                                    <i class='bx bx-down-arrow-alt badge-icon'></i> 1.1%
                                </span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Memberships Section -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2 class="section-title">Memberships</h2>
                    </div>
                    <p class="section-description">
                        Track membership growth, types, and revenue contribution to understand your
                        community engagement and recurring revenue streams.
                    </p>

                    <div class="two-column-container">
                        <div class="column-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Membership Growth</h3>
                                <div class="chart-actions">
                                    <button class="chart-btn" title="View details">
                                        <i class='bx bx-dots-horizontal-rounded'></i>
                                    </button>
                                </div>
                            </div>
                            <div class="chart-container">
                                <canvas id="membershipChart"></canvas>
                            </div>
                        </div>

                        <div class="column-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Membership Analytics</h3>
                                <div class="chart-actions">
                                    <button class="chart-btn" title="View details">
                                        <i class='bx bx-dots-horizontal-rounded'></i>
                                    </button>
                                </div>
                            </div>
                            <div class="detail-grid">
                                <div class="detail-item">
                                    <div class="detail-label">Total Members</div>
                                    <div class="detail-data">156</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Active Members</div>
                                    <div class="detail-data">132 (84.6%)</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">New Signups</div>
                                    <div class="detail-data">24 <span class="status-badge status-active">
                                    <i class='bx bx-up-arrow-alt badge-icon'></i> 15.4%
                                </span></div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Premium</div>
                                    <div class="detail-data">42 (26.9%)</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Standard</div>
                                    <div class="detail-data">78 (50%)</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Basic</div>
                                    <div class="detail-data">36 (23.1%)</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Avg. Revenue/Member</div>
                                    <div class="detail-data">$157.56</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Renewal Rate</div>
                                    <div class="detail-data">82.4% <span class="status-badge status-active">
                                    <i class='bx bx-up-arrow-alt badge-icon'></i> 3.2%
                                </span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Maintenance Requests Section -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2 class="section-title">Maintenance Requests</h2>
                    </div>
                    <p class="section-description">
                        Monitor maintenance request volume, status, and resolution times to ensure
                        efficient property management and tenant satisfaction.
                    </p>

                    <div class="full-width-card">
                        <div class="chart-header">
                            <h3 class="chart-title">Maintenance Request Trends</h3>
                            <div class="chart-actions">
                                <button class="chart-btn" title="View details">
                                    <i class='bx bx-dots-horizontal-rounded'></i>
                                </button>
                            </div>
                        </div>
                        <div class="chart-container" style="height: 350px;">
                            <canvas id="maintenanceChart"></canvas>
                        </div>

                        <div class="detail-grid" style="margin-top: 1.5rem;">
                            <div class="detail-item">
                                <div class="detail-label">Total Requests</div>
                                <div class="detail-data">142</div>
                                <div class="stat-detail">
                                    <i class='bx bx-up-arrow-alt trend-up'></i>
                                    <span>8.4% increase</span>
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Completed</div>
                                <div class="detail-data">87 <span class="status-badge status-active">
                                <i class='bx bx-check badge-icon'></i> 61.3%
                            </span></div>
                                <div class="stat-detail">Avg. resolution: 2.4 days</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">In Progress</div>
                                <div class="detail-data">38 <span class="status-badge status-pending">
                                <i class='bx bx-time-five badge-icon'></i> 26.8%
                            </span></div>
                                <div class="stat-detail">Avg. pending: 1.7 days</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Rejected</div>
                                <div class="detail-data">17 <span class="status-badge status-closed">
                                <i class='bx bx-x badge-icon'></i> 12%
                            </span></div>
                                <div class="stat-detail">Main reason: Invalid request</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transactions Section -->
                <div class="dashboard-section">
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
        </section>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle custom date range fields
            document.getElementById('dateRange').addEventListener('change', function() {
                const customDateDiv = document.getElementById('customDateRange');
                const customDateEndDiv = document.getElementById('customDateRangeEnd');
                if (this.value === 'custom') {
                    customDateDiv.style.display = 'block';
                    customDateEndDiv.style.display = 'block';
                } else {
                    customDateDiv.style.display = 'none';
                    customDateEndDiv.style.display = 'none';
                }
            });

            // Initialize all charts
            initCharts();

            // Filter functionality
            const filters = {
                dateRange: document.getElementById('dateRange'),
                buildingSelect: document.getElementById('buildingSelect'),
                transactionType: document.getElementById('transactionType')
            };

            // Add event listeners to all filters
            Object.values(filters).forEach(filter => {
                filter.addEventListener('change', function() {
                    applyFilters();
                });
            });

            function applyFilters() {
                const filterValues = {
                    dateRange: filters.dateRange.value,
                    building: filters.buildingSelect.value,
                    transactionType: filters.transactionType.value
                };

                console.log('Filters applied:', filterValues);
                // In real implementation, this would filter all charts and the transactions table
                // You would make an AJAX call to fetch filtered data and update the UI
            }

            function initCharts() {
                // Income vs Expense Pie Chart
                const incomeExpenseCtx = document.getElementById('incomeExpenseChart');
                new Chart(incomeExpenseCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Income', 'Expenses'],
                        datasets: [{
                            data: [24580, 8420],
                            backgroundColor: [
                                'rgba(6, 214, 160, 0.8)',
                                'rgba(255, 0, 110, 0.8)'
                            ],
                            borderWidth: 0,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.label + ': $' + context.raw.toLocaleString();
                                    }
                                }
                            },
                            datalabels: {
                                formatter: (value, ctx) => {
                                    const total = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    const percentage = (value * 100 / total).toFixed(1) + '%';
                                    return percentage;
                                },
                                color: '#fff',
                                font: {
                                    weight: 'bold'
                                }
                            }
                        }
                    },
                    plugins: [ChartDataLabels]
                });

                // Income Trends Line Chart
                const incomeTrendsCtx = document.getElementById('incomeTrendsChart');
                new Chart(incomeTrendsCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [
                            {
                                label: 'Rental Income',
                                data: [13500, 14200, 15000, 16200, 17500, 18420],
                                borderColor: 'rgba(6, 214, 160, 1)',
                                backgroundColor: 'rgba(6, 214, 160, 0.1)',
                                tension: 0.3,
                                fill: true,
                                pointRadius: 5,
                                pointBackgroundColor: 'rgba(6, 214, 160, 1)'
                            },
                            {
                                label: 'Service Charges',
                                data: [3200, 3500, 3700, 3900, 4000, 4120],
                                borderColor: 'rgba(58, 134, 255, 1)',
                                backgroundColor: 'rgba(58, 134, 255, 0.1)',
                                tension: 0.3,
                                fill: true,
                                pointRadius: 5,
                                pointBackgroundColor: 'rgba(58, 134, 255, 1)'
                            },
                            {
                                label: 'Other Income',
                                data: [2200, 2100, 2000, 1900, 2000, 2040],
                                borderColor: 'rgba(255, 190, 11, 1)',
                                backgroundColor: 'rgba(255, 190, 11, 0.1)',
                                tension: 0.3,
                                fill: true,
                                pointRadius: 5,
                                pointBackgroundColor: 'rgba(255, 190, 11, 1)'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '$' + value.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });

                // Expense Breakdown Bar Chart
                const expenseBreakdownCtx = document.getElementById('expenseBreakdownChart');
                new Chart(expenseBreakdownCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [
                            {
                                label: 'Maintenance',
                                data: [2800, 2900, 3000, 3100, 3200, 3250],
                                backgroundColor: 'rgba(24, 78, 131, 0.8)',
                                borderRadius: 4
                            },
                            {
                                label: 'Utilities',
                                data: [2100, 2200, 2150, 2100, 2150, 2180],
                                backgroundColor: 'rgba(6, 214, 160, 0.8)',
                                borderRadius: 4
                            },
                            {
                                label: 'Staff Salaries',
                                data: [2500, 2500, 2600, 2700, 2800, 2990],
                                backgroundColor: 'rgba(255, 190, 11, 0.8)',
                                borderRadius: 4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '$' + value.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });

                // Unit Occupancy Bar Chart
                const unitOccupancyCtx = document.getElementById('unitOccupancyChart');
                new Chart(unitOccupancyCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [
                            {
                                label: 'Occupied',
                                data: [82, 85, 88, 90, 92, 94],
                                backgroundColor: 'rgba(6, 214, 160, 0.8)',
                                borderRadius: 4
                            },
                            {
                                label: 'Vacant',
                                data: [38, 35, 32, 30, 28, 26],
                                backgroundColor: 'rgba(255, 0, 110, 0.8)',
                                borderRadius: 4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    afterLabel: function(context) {
                                        const total = context.dataset.data[context.dataIndex] +
                                            context.chart.data.datasets[1].data[context.dataIndex];
                                        const percentage = (context.raw * 100 / total).toFixed(1) + '%';
                                        return 'Occupancy: ' + percentage;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                stacked: true
                            },
                            y: {
                                stacked: true,
                                max: 120
                            }
                        }
                    }
                });

                // Staff Distribution Doughnut Chart
                const staffDistributionCtx = document.getElementById('staffDistributionChart');
                new Chart(staffDistributionCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Maintenance', 'Security', 'Cleaning', 'Admin', 'Other'],
                        datasets: [{
                            data: [18, 12, 10, 5, 3],
                            backgroundColor: [
                                'rgba(24, 78, 131, 0.8)',
                                'rgba(6, 214, 160, 0.8)',
                                'rgba(255, 190, 11, 0.8)',
                                'rgba(131, 56, 236, 0.8)',
                                'rgba(255, 0, 110, 0.8)'
                            ],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                position: 'right'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = (context.raw * 100 / total).toFixed(1) + '%';
                                        return context.label + ': ' + context.raw + ' (' + percentage + ')';
                                    }
                                }
                            },
                            datalabels: {
                                formatter: (value, ctx) => {
                                    const total = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    return (value * 100 / total).toFixed(1) + '%';
                                },
                                color: '#fff',
                                font: {
                                    weight: 'bold'
                                }
                            }
                        }
                    },
                    plugins: [ChartDataLabels]
                });

                // Membership Line Chart
                const membershipCtx = document.getElementById('membershipChart');
                new Chart(membershipCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [
                            {
                                label: 'Total Members',
                                data: [140, 142, 145, 148, 152, 156],
                                borderColor: 'rgba(24, 78, 131, 1)',
                                backgroundColor: 'rgba(24, 78, 131, 0.1)',
                                tension: 0.3,
                                fill: true,
                                pointRadius: 5,
                                pointBackgroundColor: 'rgba(24, 78, 131, 1)'
                            },
                            {
                                label: 'Active Members',
                                data: [120, 125, 130, 135, 140, 132],
                                borderColor: 'rgba(6, 214, 160, 1)',
                                backgroundColor: 'rgba(6, 214, 160, 0.1)',
                                tension: 0.3,
                                fill: true,
                                pointRadius: 5,
                                pointBackgroundColor: 'rgba(6, 214, 160, 1)'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        }
                    }
                });

                // Maintenance Requests Bar Chart
                const maintenanceCtx = document.getElementById('maintenanceChart');
                new Chart(maintenanceCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [
                            {
                                label: 'Completed',
                                data: [12, 15, 14, 16, 15, 15],
                                backgroundColor: 'rgba(6, 214, 160, 0.8)',
                                borderRadius: 4
                            },
                            {
                                label: 'In Progress',
                                data: [5, 6, 7, 6, 7, 7],
                                backgroundColor: 'rgba(255, 190, 11, 0.8)',
                                borderRadius: 4
                            },
                            {
                                label: 'Rejected',
                                data: [2, 3, 2, 3, 4, 3],
                                backgroundColor: 'rgba(255, 0, 110, 0.8)',
                                borderRadius: 4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    afterLabel: function(context) {
                                        const total = context.dataset.data[context.dataIndex] +
                                            context.chart.data.datasets[1].data[context.dataIndex] +
                                            context.chart.data.datasets[2].data[context.dataIndex];
                                        const percentage = (context.raw * 100 / total).toFixed(1) + '%';
                                        return 'Percentage: ' + percentage;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endpush
