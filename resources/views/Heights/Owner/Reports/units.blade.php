@extends('layouts.app')

@section('title', 'Unit Performance Report')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #184E83;
            --primary-light: #1A6FC9;
            --primary-lighter: #E6F0FA;
            --danger: #E74C3C;
            --danger-light: #FDEDEC;
            --warning: #F39C12;
            --warning-light: #FEF5E7;
            --success: #27AE60;
            --success-light: #E8F8F0;
            --dark: #2C3E50;
            --dark-light: #34495E;
            --light: #F8F9FA;
            --gray: #95A5A6;
            --gray-light: #BDC3C7;
            --light-gray: #ECF0F1;
            --border-color: #E0E0E0;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--dark);
            background-color: #F5F7FA;
        }

        #main {
            margin-top: 60px;
        }

        .report-header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .report-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 5px;
        }

        .report-description {
            color: var(--gray);
            margin-bottom: 0;
            max-width: 800px;
            line-height: 1.6;
            font-size: 15px;
        }

        /* Unit Details Card */
        .unit-details-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            margin-bottom: 25px;
            border: 1px solid var(--border-color);
        }

        .unit-details-card h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .unit-details-card h3 i {
            margin-right: 10px;
            color: var(--primary);
        }

        .unit-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 15px;
        }

        .unit-detail-item {
            padding: 15px;
            background-color: var(--light);
            border-radius: 8px;
            border-left: 3px solid var(--primary);
        }

        .unit-detail-label {
            font-size: 13px;
            color: var(--gray);
            margin-bottom: 5px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .unit-detail-value {
            font-size: 16px;
            font-weight: 600;
            color: var(--dark);
        }

        /* Filters */
        .filters-container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            margin-bottom: 30px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            align-items: flex-end;
            border: 1px solid var(--border-color);
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            color: var(--gray);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: white;
            font-size: 14px;
            transition: all 0.3s;
            height: 42px;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(24, 78, 131, 0.1);
        }

        .custom-date-range {
            display: flex;
            gap: 15px;
        }

        /* Metrics Cards */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .metric-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            border: 1px solid var(--border-color);
            transition: transform 0.3s, box-shadow 0.3s;
            padding: 15px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            height: 100%;
        }

        .metric-card h4 {
            font-size: 14px;
            color: var(--gray);
            margin-bottom: 8px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .metric-card h4 i {
            margin-right: 8px;
            font-size: 16px;
        }

        .metric-card .value {
            font-size: 24px;
            font-weight: 700;
            color: var(--dark);
        }

        /* Status-specific card accents */
        .metric-card.completed {
            border-top: 3px solid var(--success);
        }

        .metric-card.pending {
            border-top: 3px solid var(--warning);
        }

        .metric-card.rejected {
            border-top: 3px solid var(--danger);
        }

        /* Chart Containers */
        .chart-summary-row {
            display: flex;
            gap: 25px;
            margin-bottom: 25px;
        }

        @media (max-width: 1200px) {
            .chart-summary-row {
                flex-direction: column;
            }
        }

        .chart-container {
            flex: 2;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            padding: 25px;
            border: 1px solid var(--border-color);
        }

        .summary-container {
            flex: 1;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            padding: 25px;
            display: flex;
            flex-direction: column;
            border: 1px solid var(--border-color);
        }

        .full-width-chart {
            width: 100%;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid var(--border-color);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .chart-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
        }

        .chart-header .chart-actions {
            display: flex;
            gap: 10px;
        }

        .chart-header .chart-value {
            font-size: 16px;
            font-weight: 600;
            color: var(--primary);
            background: var(--primary-lighter);
            padding: 5px 12px;
            border-radius: 20px;
        }

        .chart-canvas-container {
            position: relative;
            height: 320px;
            width: 100%;
        }

        /* Summary Styles */
        .summary-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .summary-item:last-child {
            border-bottom: none;
        }

        .summary-label {
            color: var(--gray);
            font-size: 14px;
            font-weight: 500;
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

        /* Table Styles */
        .data-table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid var(--border-color);
        }

        .data-table-container h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .data-table-container h3 i {
            margin-right: 10px;
            color: var(--primary);
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            text-align: left;
            padding: 12px 16px;
            background-color: var(--light);
            font-size: 13px;
            color: var(--gray);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--border-color);
        }

        .data-table td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-color);
            font-size: 14px;
            vertical-align: middle;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table tr:hover {
            background-color: var(--light);
        }

        /* Status Badges */
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
        }

        .status-badge i {
            margin-right: 5px;
            font-size: 10px;
        }

        .status-rented {
            background-color: var(--primary-lighter);
            color: var(--primary);
        }

        .status-sold {
            background-color: var(--success-light);
            color: var(--success);
        }

        .status-available {
            background-color: var(--light-gray);
            color: var(--gray);
        }

        .status-pending {
            background-color: var(--warning-light);
            color: var(--warning);
        }

        .status-completed {
            background-color: var(--success-light);
            color: var(--success);
        }

        /* Dual Column Layout */
        .dual-column {
            display: flex;
            gap: 25px;
            margin-bottom: 25px;
        }

        @media (max-width: 1200px) {
            .dual-column {
                flex-direction: column;
            }
        }

        .column {
            flex: 1;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            padding: 25px;
            border: 1px solid var(--border-color);
        }

        /* Export Buttons */
        .export-actions {
            display: flex;
            gap: 10px;
        }

        .export-btn {
            padding: 8px 16px;
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            color: var(--dark);
            cursor: pointer;
            display: flex;
            align-items: center;
            transition: all 0.3s;
        }

        .export-btn i {
            margin-right: 8px;
        }

        .export-btn:hover {
            background: var(--light);
            border-color: var(--primary);
            color: var(--primary);
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .unit-details-grid {
                grid-template-columns: 1fr 1fr;
            }

            .metrics-grid {
                grid-template-columns: 1fr 1fr;
            }

            .filters-container {
                flex-direction: column;
            }

            .custom-date-range {
                flex-direction: column;
            }
        }

        @media (max-width: 576px) {
            .unit-details-grid,
            .metrics-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Top Navbar -->
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
        ['url' => '#', 'label' => 'Reports'],
        ['url' => '', 'label' => 'Unit Performance']
    ]"/>

    <!-- Side Navbar -->
    <x-Owner.side-navbar :openSections="['Reports']" />
    <x-error-success-model />

    <div id="main">
        <section class="content my-4 mx-3">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="report-container">
                            <!-- Header -->
                            <div class="report-header">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                    <div>
                                        <h1>Unit Performance Report</h1>
                                        <p class="report-description">
                                            Comprehensive analysis of unit #A-205 financial performance, occupancy history, and maintenance records.
                                        </p>
                                    </div>
                                    <div class="export-actions">
                                        <button class="export-btn" id="exportPdf">
                                            <i class='bx bxs-file-pdf'></i> Export PDF
                                        </button>
                                        <button class="export-btn" id="exportImage">
                                            <i class='bx bxs-image-alt'></i> Export Image
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
                                        <option value="1" selected>Downtown Tower</option>
                                        <option value="2">Riverside Apartments</option>
                                    </select>
                                </div>

                                <div class="filter-group">
                                    <label for="unitSelect">Unit</label>
                                    <select id="unitSelect">
                                        <option value="all">All Units</option>
                                        <option value="A-101">A-101</option>
                                        <option value="A-205" selected>A-205</option>
                                        <option value="B-302">B-302</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Unit Details Card -->
                            <div class="unit-details-card">
                                <h3><i class='bx bxs-building-house'></i> Unit Details</h3>
                                <div class="unit-details-grid">
                                    <div class="unit-detail-item">
                                        <div class="unit-detail-label">Unit Number</div>
                                        <div class="unit-detail-value">A-205</div>
                                    </div>
                                    <div class="unit-detail-item">
                                        <div class="unit-detail-label">Building</div>
                                        <div class="unit-detail-value">Downtown Tower</div>
                                    </div>
                                    <div class="unit-detail-item">
                                        <div class="unit-detail-label">Unit Type</div>
                                        <div class="unit-detail-value">2 Bedroom Apartment</div>
                                    </div>
                                    <div class="unit-detail-item">
                                        <div class="unit-detail-label">Current Status</div>
                                        <div class="unit-detail-value">
                                            <span class="status-badge status-rented"><i class='bx bxs-check-circle'></i> Rented</span>
                                        </div>
                                    </div>
                                    <div class="unit-detail-item">
                                        <div class="unit-detail-label">Area (sq ft)</div>
                                        <div class="unit-detail-value">1,250</div>
                                    </div>
                                    <div class="unit-detail-item">
                                        <div class="unit-detail-label">Purchase Price</div>
                                        <div class="unit-detail-value">$350,000</div>
                                    </div>
                                    <div class="unit-detail-item">
                                        <div class="unit-detail-label">Current Value</div>
                                        <div class="unit-detail-value">$420,000</div>
                                    </div>
                                    <div class="unit-detail-item">
                                        <div class="unit-detail-label">Ownership Date</div>
                                        <div class="unit-detail-value">15 Jan 2018</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Key Metrics -->
                            <div class="metrics-grid">
                                <div class="metric-card">
                                    <h4><i class='bx bx-trending-up'></i> Total Income</h4>
                                    <div class="value">$12,450</div>
                                    <div class="trend up">
                                        <i class='bx bx-up-arrow-alt'></i> 12.5% from last period
                                    </div>
                                </div>
                                <div class="metric-card">
                                    <h4><i class='bx bx-trending-down'></i> Total Expenses</h4>
                                    <div class="value">$3,280</div>
                                    <div class="trend down">
                                        <i class='bx bx-down-arrow-alt'></i> 5.3% from last period
                                    </div>
                                </div>
                                <div class="metric-card">
                                    <h4><i class='bx bx-line-chart'></i> Net Profit</h4>
                                    <div class="value">$9,170</div>
                                    <div class="trend up">
                                        <i class='bx bx-up-arrow-alt'></i> 18.2% from last period
                                    </div>
                                </div>
                                <div class="metric-card">
                                    <h4><i class='bx bx-calendar-check'></i> Occupancy Rate</h4>
                                    <div class="value">92%</div>
                                    <div class="trend up">
                                        <i class='bx bx-up-arrow-alt'></i> 3.5% from last period
                                    </div>
                                </div>
                            </div>

                            <!-- Income vs Expense Chart with Summary -->
                            <div class="chart-summary-row">
                                <div class="chart-container">
                                    <div class="chart-header">
                                        <h3>Income vs Expenses Trend</h3>
                                        <div class="chart-value">6 Months</div>
                                    </div>
                                    <div class="chart-canvas-container">
                                        <canvas id="incomeExpenseChart"></canvas>
                                    </div>
                                </div>
                                <div class="summary-container">
                                    <h3 class="summary-title">Financial Summary</h3>
                                    <div class="summary-item">
                                        <span class="summary-label">Total Income</span>
                                        <span class="summary-value">$12,450</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">Total Expenses</span>
                                        <span class="summary-value">$3,280</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">Net Profit</span>
                                        <span class="summary-value positive">$9,170</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">Profit Margin</span>
                                        <span class="summary-value positive">73.6%</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">Avg Monthly Rent</span>
                                        <span class="summary-value">$1,850</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">Avg Maintenance Cost</span>
                                        <span class="summary-value negative">$320</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">ROI (Annual)</span>
                                        <span class="summary-value positive">6.3%</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Income Sources and Expense Sources Bar Charts -->
                            <div class="chart-summary-row">
                                <div class="chart-container">
                                    <div class="chart-header">
                                        <h3>Income Sources Breakdown</h3>
                                        <div class="chart-value">Last 6 Months</div>
                                    </div>
                                    <div class="chart-canvas-container">
                                        <canvas id="incomeSourcesChart"></canvas>
                                    </div>
                                </div>
                                <div class="chart-container">
                                    <div class="chart-header">
                                        <h3>Expense Categories Breakdown</h3>
                                        <div class="chart-value">Last 6 Months</div>
                                    </div>
                                    <div class="chart-canvas-container">
                                        <canvas id="expenseCategoriesChart"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- Maintenance Requests -->
                            <div class="dual-column">
                                <div class="column">
                                    <h3><i class='bx bx-wrench'></i> Active Maintenance Requests</h3>
                                    <div class="table-responsive">
                                        <table class="data-table">
                                            <thead>
                                            <tr>
                                                <th>Request ID</th>
                                                <th>Date</th>
                                                <th>Type</th>
                                                <th>Status</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>MR-2023-045</td>
                                                <td>15 Jun 2023</td>
                                                <td>Plumbing</td>
                                                <td><span class="status-badge status-pending"><i class='bx bx-time'></i> In Progress</span></td>
                                            </tr>
                                            <tr>
                                                <td>MR-2023-046</td>
                                                <td>18 Jun 2023</td>
                                                <td>Electrical</td>
                                                <td><span class="status-badge status-pending"><i class='bx bx-time'></i> Pending</span></td>
                                            </tr>
                                            <tr>
                                                <td>MR-2023-044</td>
                                                <td>10 Jun 2023</td>
                                                <td>HVAC</td>
                                                <td><span class="status-badge status-completed"><i class='bx bx-check-circle'></i> Completed</span></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="column">
                                    <div class="chart-header">
                                        <h3>Maintenance History</h3>
                                        <div class="chart-value">6 Months</div>
                                    </div>
                                    <div class="chart-canvas-container">
                                        <canvas id="maintenanceHistoryChart"></canvas>
                                    </div>

                                    <!-- Status Summary Cards - Now in a single row below the chart -->
                                    <div class="metrics-grid" style="margin-top: 20px; grid-template-columns: repeat(3, 1fr);">
                                        <div class="metric-card text-center">
                                            <h4>Completed</h4>
                                            <div class="value">24</div>
                                        </div>
                                        <div class="metric-card text-center">
                                            <h4>Pending</h4>
                                            <div class="value">8</div>
                                        </div>
                                        <div class="metric-card text-center">
                                            <h4>Rejected</h4>
                                            <div class="value">3</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Unit Status History -->
                            <div class="data-table-container">
                                <h3><i class='bx bx-history'></i> Unit Status History</h3>
                                <div class="table-responsive">
                                    <table class="data-table">
                                        <thead>
                                        <tr>
                                            <th>Period</th>
                                            <th>Status</th>
                                            <th>Tenant/Owner</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Duration</th>
                                            <th>Amount</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>Current</td>
                                            <td><span class="status-badge status-rented"><i class='bx bxs-check-circle'></i> Rented</span></td>
                                            <td>John Smith (T-1024)</td>
                                            <td>01 Jan 2023</td>
                                            <td>31 Dec 2023</td>
                                            <td>12 months</td>
                                            <td>$1,850/mo</td>
                                        </tr>
                                        <tr>
                                            <td>Previous</td>
                                            <td><span class="status-badge status-rented"><i class='bx bxs-check-circle'></i> Rented</span></td>
                                            <td>Sarah Johnson (T-0987)</td>
                                            <td>01 Jun 2022</td>
                                            <td>31 Dec 2022</td>
                                            <td>7 months</td>
                                            <td>$1,750/mo</td>
                                        </tr>
                                        <tr>
                                            <td>Previous</td>
                                            <td><span class="status-badge status-available"><i class='bx bx-calendar-x'></i> Available</span></td>
                                            <td>-</td>
                                            <td>01 May 2022</td>
                                            <td>31 May 2022</td>
                                            <td>1 month</td>
                                            <td>-</td>
                                        </tr>
                                        <tr>
                                            <td>Previous</td>
                                            <td><span class="status-badge status-rented"><i class='bx bxs-check-circle'></i> Rented</span></td>
                                            <td>Michael Brown (T-0765)</td>
                                            <td>01 Jan 2021</td>
                                            <td>30 Apr 2022</td>
                                            <td>16 months</td>
                                            <td>$1,650/mo</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Transactions -->
                            <div class="data-table-container">
                                <h3><i class='bx bx-credit-card'></i> Unit Transactions</h3>
                                <div class="table-responsive">
                                    <table class="data-table">
                                        <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Transaction ID</th>
                                            <th>Description</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>05 Jun 2023</td>
                                            <td>TXN-789456</td>
                                            <td>June Rent Payment</td>
                                            <td>Income</td>
                                            <td>$1,850.00</td>
                                            <td><span class="status-badge status-completed"><i class='bx bx-check-circle'></i> Completed</span></td>
                                        </tr>
                                        <tr>
                                            <td>15 May 2023</td>
                                            <td>TXN-789455</td>
                                            <td>HVAC Maintenance</td>
                                            <td>Expense</td>
                                            <td>-$450.00</td>
                                            <td><span class="status-badge status-completed"><i class='bx bx-check-circle'></i> Completed</span></td>
                                        </tr>
                                        <tr>
                                            <td>05 May 2023</td>
                                            <td>TXN-789454</td>
                                            <td>May Rent Payment</td>
                                            <td>Income</td>
                                            <td>$1,850.00</td>
                                            <td><span class="status-badge status-completed"><i class='bx bx-check-circle'></i> Completed</span></td>
                                        </tr>
                                        <tr>
                                            <td>20 Apr 2023</td>
                                            <td>TXN-789453</td>
                                            <td>Plumbing Repair</td>
                                            <td>Expense</td>
                                            <td>-$320.00</td>
                                            <td><span class="status-badge status-completed"><i class='bx bx-check-circle'></i> Completed</span></td>
                                        </tr>
                                        <tr>
                                            <td>05 Apr 2023</td>
                                            <td>TXN-789452</td>
                                            <td>April Rent Payment</td>
                                            <td>Income</td>
                                            <td>$1,850.00</td>
                                            <td><span class="status-badge status-completed"><i class='bx bx-check-circle'></i> Completed</span></td>
                                        </tr>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
        // Initialize jsPDF
        const { jsPDF } = window.jspdf;

        document.addEventListener('DOMContentLoaded', function() {
            // Income vs Expense Trend Chart (Line Chart)
            const incomeExpenseCtx = document.getElementById('incomeExpenseChart').getContext('2d');
            const incomeExpenseChart = new Chart(incomeExpenseCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [
                        {
                            label: 'Income',
                            data: [1850, 1850, 1850, 1850, 1850, 1850],
                            borderColor: '#184E83',
                            backgroundColor: 'rgba(24, 78, 131, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true,
                            pointBackgroundColor: '#184E83',
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Expenses',
                            data: [320, 450, 280, 320, 500, 450],
                            borderColor: '#E74C3C',
                            backgroundColor: 'rgba(231, 76, 60, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true,
                            pointBackgroundColor: '#E74C3C',
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
                                    return context.dataset.label + ': $' + context.parsed.y.toLocaleString();
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

            // Income Sources Bar Chart
            const incomeSourcesCtx = document.getElementById('incomeSourcesChart').getContext('2d');
            const incomeSourcesChart = new Chart(incomeSourcesCtx, {
                type: 'bar',
                data: {
                    labels: ['Rent', 'Parking', 'Amenities', 'Other'],
                    datasets: [{
                        label: 'Amount',
                        data: [11100, 900, 300, 150],
                        backgroundColor: [
                            'rgba(24, 78, 131, 0.8)',
                            'rgba(26, 111, 201, 0.8)',
                            'rgba(46, 204, 113, 0.8)',
                            'rgba(243, 156, 18, 0.8)'
                        ],
                        borderColor: [
                            'rgba(24, 78, 131, 1)',
                            'rgba(26, 111, 201, 1)',
                            'rgba(46, 204, 113, 1)',
                            'rgba(243, 156, 18, 1)'
                        ],
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((context.raw / total) * 100);
                                    return `${context.label}: $${context.raw.toLocaleString()} (${percentage}%)`;
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

            // Expense Categories Bar Chart
            const expenseCategoriesCtx = document.getElementById('expenseCategoriesChart').getContext('2d');
            const expenseCategoriesChart = new Chart(expenseCategoriesCtx, {
                type: 'bar',
                data: {
                    labels: ['Maintenance', 'Utilities', 'Repairs', 'Other'],
                    datasets: [{
                        label: 'Amount',
                        data: [1200, 900, 800, 380],
                        backgroundColor: [
                            'rgba(231, 76, 60, 0.8)',
                            'rgba(155, 89, 182, 0.8)',
                            'rgba(52, 152, 219, 0.8)',
                            'rgba(149, 165, 166, 0.8)'
                        ],
                        borderColor: [
                            'rgba(231, 76, 60, 1)',
                            'rgba(155, 89, 182, 1)',
                            'rgba(52, 152, 219, 1)',
                            'rgba(149, 165, 166, 1)'
                        ],
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((context.raw / total) * 100);
                                    return `${context.label}: $${context.raw.toLocaleString()} (${percentage}%)`;
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

            // Maintenance History Chart
            const maintenanceHistoryCtx = document.getElementById('maintenanceHistoryChart').getContext('2d');
            const maintenanceHistoryChart = new Chart(maintenanceHistoryCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [
                        {
                            label: 'Completed',
                            data: [3, 4, 2, 5, 6, 4],
                            borderColor: '#27AE60',
                            backgroundColor: 'rgba(39, 174, 96, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'Pending',
                            data: [2, 1, 3, 2, 1, 2],
                            borderColor: '#F39C12',
                            backgroundColor: 'rgba(243, 156, 18, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'Rejected',
                            data: [1, 0, 1, 0, 1, 0],
                            borderColor: '#E74C3C',
                            backgroundColor: 'rgba(231, 76, 60, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
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
                            },
                            ticks: {
                                stepSize: 1
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

            // Export functionality
            document.getElementById('exportPdf').addEventListener('click', exportToPdf);
            document.getElementById('exportImage').addEventListener('click', exportToImage);

            function exportToPdf() {
                const element = document.querySelector('.report-container');
                const options = {
                    scale: 2,
                    useCORS: true,
                    scrollY: 0,
                    backgroundColor: '#FFFFFF'
                };

                html2canvas(element, options).then(canvas => {
                    const pdf = new jsPDF('p', 'mm', 'a4');
                    const imgData = canvas.toDataURL('image/png');
                    const pdfWidth = pdf.internal.pageSize.getWidth();
                    const pdfHeight = (canvas.height * pdfWidth) / canvas.width;

                    pdf.addImage(imgData, 'PNG', 5, 5, pdfWidth - 10, pdfHeight - 10);
                    pdf.save(`Unit_Performance_Report_${new Date().toISOString().slice(0,10)}.pdf`);
                });
            }

            function exportToImage() {
                const element = document.querySelector('.report-container');
                const options = {
                    scale: 2,
                    useCORS: true,
                    scrollY: 0,
                    backgroundColor: '#FFFFFF'
                };

                html2canvas(element, options).then(canvas => {
                    const link = document.createElement('a');
                    link.download = `Unit_Performance_Report_${new Date().toISOString().slice(0,10)}.png`;
                    link.href = canvas.toDataURL('image/png');
                    link.click();
                });
            }

            // Date range filter toggle
            document.getElementById('dateRange').addEventListener('change', function() {
                const customDateContainer = document.getElementById('customDateRangeContainer');
                if (this.value === 'custom') {
                    customDateContainer.style.display = 'flex';
                    // Set default dates (last 30 days)
                    const endDate = new Date();
                    const startDate = new Date();
                    startDate.setDate(endDate.getDate() - 30);

                    document.getElementById('endDate').valueAsDate = endDate;
                    document.getElementById('startDate').valueAsDate = startDate;
                } else {
                    customDateContainer.style.display = 'none';
                }
            });
        });
    </script>
@endpush
