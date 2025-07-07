@extends('layouts.app')

@section('title', 'Unit Management Report')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
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
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: var(--dark);
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

        /* Enhanced Filters Section */
        .filters-container {
            background: white;
            padding: 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 30px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .filters-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .filters-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
        }

        .filters-header p {
            color: var(--gray);
            margin: 0;
            font-size: 14px;
        }

        .filters-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 15px;
            align-items: flex-end;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: var(--dark);
            font-weight: 500;
        }

        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
            border-radius: var(--border-radius);
            background: white;
            font-size: 14px;
            transition: var(--transition);
            height: 42px;
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
            width: 100%;
        }

        .custom-date-range .filter-group {
            flex: 1;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }

        .generate-btn {
            padding: 12px 25px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            height: 42px;
            box-shadow: 0 2px 4px rgba(24, 78, 131, 0.2);
        }

        .generate-btn:hover {
            background-color: var(--primary-light);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(24, 78, 131, 0.3);
        }

        /* Report Content Styles (hidden by default) */
        .report-content {
            display: none;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 25px;
            margin-top: 20px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* Unit Details Section */
        .unit-details-container {
            display: flex;
            gap: 25px;
            margin-bottom: 30px;
        }

        .unit-image-container {
            flex: 0 0 250px;
        }

        .unit-image {
            width: 100%;
            height: auto;
            border-radius: var(--border-radius);
            object-fit: cover;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .unit-info {
            flex: 1;
        }

        .unit-info h3 {
            font-size: 22px;
            color: var(--dark);
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f0f0f0;
        }

        .unit-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
        }

        .info-label {
            font-weight: 600;
            color: var(--dark);
            min-width: 120px;
        }

        .info-value {
            color: var(--gray);
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
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 20px;
            transition: var(--transition);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .chart-container:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .summary-container {
            flex: 1;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 20px;
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .full-width-chart {
            width: 100%;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 20px;
            margin-bottom: 25px;
            transition: var(--transition);
            border: 1px solid rgba(0, 0, 0, 0.05);
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
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 20px;
            text-align: center;
            transition: var(--transition);
            border: 1px solid rgba(0, 0, 0, 0.05);
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
            border-radius: var(--border-radius);
            text-align: center;
            transition: var(--transition);
            border: 1px solid rgba(0, 0, 0, 0.05);
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

        /* Table Styles */
        .data-table-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 20px;
            margin-bottom: 25px;
            overflow-x: auto;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            text-align: left;
            padding: 12px 15px;
            background-color: #f8f9fa;
            color: var(--dark);
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .data-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table tr:hover td {
            background-color: rgba(24, 78, 131, 0.03);
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-primary {
            background-color: rgba(24, 78, 131, 0.1);
            color: var(--primary);
        }

        .badge-success {
            background-color: rgba(46, 204, 113, 0.1);
            color: var(--success);
        }

        .badge-warning {
            background-color: rgba(255, 190, 11, 0.1);
            color: var(--warning);
        }

        .badge-danger {
            background-color: rgba(255, 77, 109, 0.1);
            color: var(--danger);
        }

        /* Unit Status Timeline */
        .timeline-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .timeline {
            position: relative;
            padding-left: 30px;
            margin-top: 20px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: #e0e0e0;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 20px;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-dot {
            position: absolute;
            left: -30px;
            top: 0;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 10px;
        }

        .timeline-content {
            padding: 10px 15px;
            background-color: #f8f9fa;
            border-radius: 6px;
        }

        .timeline-date {
            font-size: 12px;
            color: var(--gray);
            margin-bottom: 5px;
        }

        .timeline-title {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .timeline-description {
            font-size: 13px;
            color: var(--gray);
        }

        /* Export styles */
        .export-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .export-btn {
            padding: 8px 15px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: var(--transition);
        }

        .export-btn:hover {
            background-color: var(--primary-light);
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

        /* Responsive */
        @media (max-width: 1200px) {
            .metrics-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .chart-summary-row {
                flex-direction: column;
            }

            .unit-info-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .filters-container {
                flex-direction: column;
            }

            .filters-row {
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

            .unit-details-container {
                flex-direction: column;
            }

            .unit-image-container {
                flex: 1;
                max-width: 100%;
            }

            .filter-group {
                min-width: 100%;
            }

            .action-buttons {
                width: 100%;
            }

            .generate-btn {
                width: 100%;
                justify-content: center;
            }
        }

        /* Maintenance Requests Section - Specific Styles */
        .maintenance-section .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }

        .maintenance-section .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding-right: 15px;
            padding-left: 15px;
            display: flex;
            flex-direction: column;
        }

        .maintenance-section .data-table-container {
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden; /* Ensures the container doesn't grow */
        }

        .maintenance-section .table-wrapper {
            flex: 1;
            overflow-y: auto;
            max-height: 100%;
            border: 1px solid #f0f0f0;
            border-radius: var(--border-radius);
        }

        .maintenance-section .chart-container {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .maintenance-section .chart-canvas-container {
            flex: 1;
            min-height: 250px;
        }

        /* Responsive adjustments only for this section */
        @media (max-width: 768px) {
            .maintenance-section .col-md-6 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .maintenance-section .table-wrapper {
                max-height: 300px;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Top Navbar -->
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
        ['url' => '#', 'label' => 'Dashboard'],
        ['url' => '', 'label' => 'Unit Reports']
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
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <h1>Unit Management Report</h1>
                                        <p class="report-description">
                                            Select filters and generate a comprehensive report for any unit
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Enhanced Filters Section -->
                            <div class="filters-container">
                                <div class="filters-header">
                                    <div>
                                        <h3>Report Filters</h3>
                                        <p>Select your criteria and generate the report</p>
                                    </div>
                                </div>

                                <div class="filters-row">
                                    <div class="filter-group">
                                        <label for="dateRange">Date Range</label>
                                        <select id="dateRange">
                                            <option value="7days">Last 7 Days</option>
                                            <option value="30days" selected>Last 30 Days</option>
                                            <option value="90days">Last 90 Days</option>
                                            <option value="custom">Custom Date Range</option>
                                        </select>
                                    </div>

                                    <div class="filter-group">
                                        <label for="buildingSelect">Building</label>
                                        <select id="buildingSelect">
                                            <option value="">Select Building</option>
                                            <option value="1">Downtown Tower</option>
                                            <option value="2">Riverside Apartments</option>
                                            <option value="3">Hillside Complex</option>
                                        </select>
                                    </div>

                                    <div class="filter-group">
                                        <label for="unitSelect">Unit</label>
                                        <select id="unitSelect" disabled>
                                            <option value="">Select Unit</option>
                                        </select>
                                    </div>

                                    <div class="action-buttons">
                                        <button class="generate-btn" id="generateReport">
                                            <i class='bx bx-printer'></i> Generate Report
                                        </button>
                                    </div>
                                </div>

                                <div id="customDateRangeContainer" class="filters-row" style="display: none;">
                                    <div class="filter-group">
                                        <label for="startDate">Start Date</label>
                                        <input type="date" id="startDate">
                                    </div>
                                    <div class="filter-group">
                                        <label for="endDate">End Date</label>
                                        <input type="date" id="endDate">
                                    </div>
                                </div>
                            </div>

                            <!-- Report Content (hidden by default) -->
                            <div class="report-content" id="reportContent">
                                <!-- Export Actions -->
                                <div class="export-actions">
                                    <button class="export-btn" id="exportPdf">
                                        <i class='bx bx-download'></i> Export PDF
                                    </button>
                                    <button class="export-btn" id="exportImage">
                                        <i class='bx bx-image-alt'></i> Export Image
                                    </button>
                                </div>

                                <!-- Unit Details -->
                                <div class="unit-details-container">
                                    <div class="unit-image-container">
                                        <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Unit Image" class="unit-image">
                                    </div>
                                    <div class="unit-info">
                                        <h3>UNIT-105 Details</h3>
                                        <div class="unit-info-grid">
                                            <div class="info-item">
                                                <span class="info-label">Unit ID:</span>
                                                <span class="info-value">UNIT-2023-105</span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">Building:</span>
                                                <span class="info-value">Downtown Tower (Level 10)</span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">Type:</span>
                                                <span class="info-value">2 Bedroom Apartment</span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">Size:</span>
                                                <span class="info-value">850 sq.ft</span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">Current Status:</span>
                                                <span class="info-value"><span class="badge badge-success">Rented</span></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">Current Tenant:</span>
                                                <span class="info-value">John Smith (since May 2022)</span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">Purchase Price:</span>
                                                <span class="info-value">$350,000</span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">Market Value:</span>
                                                <span class="info-value">$420,000</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Key Metrics -->
                                <div class="metrics-grid">
                                    <div class="metric-card">
                                        <h4>Total Income</h4>
                                        <div class="value" id="totalIncome">$2,400</div>
                                        <div class="trend up">
                                            <i class='bx bx-up-arrow-alt'></i> <span id="incomeChange">12%</span> from last period
                                        </div>
                                    </div>
                                    <div class="metric-card">
                                        <h4>Total Expenses</h4>
                                        <div class="value" id="totalExpenses">$850</div>
                                        <div class="trend down">
                                            <i class='bx bx-down-arrow-alt'></i> <span id="expensesChange">5%</span> from last period
                                        </div>
                                    </div>
                                    <div class="metric-card">
                                        <h4>Net Profit</h4>
                                        <div class="value" id="netProfit">$1,550</div>
                                        <div class="trend up">
                                            <i class='bx bx-up-arrow-alt'></i> <span id="profitChange">18%</span> from last period
                                        </div>
                                    </div>
                                    <div class="metric-card">
                                        <h4>Occupancy Rate</h4>
                                        <div class="value" id="occupancyRate">92%</div>
                                        <div class="trend up">
                                            <i class='bx bx-up-arrow-alt'></i> <span id="occupancyChange">3%</span> from last period
                                        </div>
                                    </div>
                                </div>

                                <!-- Income vs Expense Chart with Summary -->
                                <div class="chart-summary-row">
                                    <div class="chart-container">
                                        <div class="chart-header">
                                            <h3>Income vs Expenses</h3>
                                            <div class="chart-value" id="profitValue">$1,550 Net Profit</div>
                                        </div>
                                        <div class="chart-canvas-container">
                                            <canvas id="incomeExpenseChart"></canvas>
                                        </div>
                                    </div>
                                    <div class="summary-container">
                                        <h3 class="summary-title">Financial Summary</h3>
                                        <div class="summary-item">
                                            <span class="summary-label"><i class='bx bx-dollar-circle'></i> Total Income</span>
                                            <span class="summary-value" id="summaryIncome">$2,400</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label"><i class='bx bx-money-withdraw'></i> Total Expenses</span>
                                            <span class="summary-value" id="summaryExpenses">$850</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label"><i class='bx bx-trending-up'></i> Net Profit</span>
                                            <span class="summary-value positive" id="summaryProfit">$1,550</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label"><i class='bx bx-line-chart'></i> Profit Margin</span>
                                            <span class="summary-value positive" id="summaryMargin">64.6%</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="summary-label"><i class='bx bx-calendar'></i> Days Rented</span>
                                            <span class="summary-value" id="summaryDaysRented">30/30</span>
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
                                                    <span class="progress-label">Expense Reduction</span>
                                                    <span class="progress-value positive">-5%</span>
                                                </div>
                                                <div class="progress-bar">
                                                    <div class="progress-fill" style="width: 25%; background-color: var(--danger);"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Income and Expense Sources -->
                                <div class="chart-summary-row">
                                    <div class="chart-container">
                                        <div class="chart-header">
                                            <h3>Income Sources</h3>
                                            <div class="chart-value" id="incomeSourcesValue">3 Sources</div>
                                        </div>
                                        <div class="chart-canvas-container">
                                            <canvas id="incomeSourcesChart"></canvas>
                                        </div>
                                    </div>
                                    <div class="chart-container">
                                        <div class="chart-header">
                                            <h3>Expense Categories</h3>
                                            <div class="chart-value" id="expenseCategoriesValue">4 Categories</div>
                                        </div>
                                        <div class="chart-canvas-container">
                                            <canvas id="expenseCategoriesChart"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Current Rental Contract -->
                                <div class="data-table-container">
                                    <h3>Current Rental Contract Details</h3>
                                    <table class="data-table">
                                        <thead>
                                        <tr>
                                            <th>Tenant Name</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Monthly Rent</th>
                                            <th>Deposit</th>
                                            <th>Payment Status</th>
                                            <th>Lease Type</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>John Smith</td>
                                            <td>June 1, 2023</td>
                                            <td>May 31, 2024</td>
                                            <td>$1,200</td>
                                            <td>$1,200</td>
                                            <td><span class="badge badge-success">Current</span></td>
                                            <td>Fixed Term</td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <h4 style="margin-top: 20px;">Tenant Details</h4>
                                    <table class="data-table">
                                        <tbody>
                                        <tr>
                                            <td style="width: 150px; font-weight: 600;">Contact</td>
                                            <td>john.smith@example.com | (555) 123-4567</td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: 600;">Emergency Contact</td>
                                            <td>Jane Smith (Spouse) - (555) 987-6543</td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: 600;">Occupation</td>
                                            <td>Software Engineer at Tech Corp</td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: 600;">Notes</td>
                                            <td>Prefers communication via email. Has one pet (cat).</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Maintenance Requests -->
                                <div class="maintenance-section">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="data-table-container scrollable-container">
                                                <h3>Active Maintenance Requests</h3>
                                                <div class="table-wrapper">
                                                    <table class="data-table">
                                                        <thead>
                                                        <tr>
                                                            <th>Request ID</th>
                                                            <th>Type</th>
                                                            <th>Reported</th>
                                                            <th>Status</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td>MR-2023-105-1</td>
                                                            <td>Plumbing - Leaky faucet</td>
                                                            <td>June 15, 2023</td>
                                                            <td><span class="badge badge-warning">In Progress</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td>MR-2023-105-2</td>
                                                            <td>Electrical - Outlet not working</td>
                                                            <td>June 20, 2023</td>
                                                            <td><span class="badge badge-danger">Pending</span></td>
                                                        </tr>
                                                        <!-- Additional rows to demonstrate scrolling -->
                                                        <tr>
                                                            <td>MR-2023-105-3</td>
                                                            <td>HVAC - Thermostat not working</td>
                                                            <td>June 22, 2023</td>
                                                            <td><span class="badge badge-primary">Completed</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td>MR-2023-105-4</td>
                                                            <td>Appliance - Refrigerator issue</td>
                                                            <td>June 25, 2023</td>
                                                            <td><span class="badge badge-warning">In Progress</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td>MR-2023-105-5</td>
                                                            <td>Structural - Window repair</td>
                                                            <td>June 28, 2023</td>
                                                            <td><span class="badge badge-danger">Pending</span></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="chart-container">
                                                <div class="chart-header">
                                                    <h3>Maintenance History</h3>
                                                    <div class="chart-value" id="maintenanceValue">12 Requests (Last 12 months)</div>
                                                </div>
                                                <div class="chart-canvas-container">
                                                    <canvas id="maintenanceChart"></canvas>
                                                </div>
                                                <div class="status-grid">
                                                    <div class="status-card">
                                                        <div class="status-count" style="color: var(--success);">8</div>
                                                        <div class="status-label">Completed</div>
                                                    </div>
                                                    <div class="status-card">
                                                        <div class="status-count" style="color: var(--warning);">2</div>
                                                        <div class="status-label">In Progress</div>
                                                    </div>
                                                    <div class="status-card">
                                                        <div class="status-count" style="color: var(--danger);">2</div>
                                                        <div class="status-label">Pending</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Unit Status Timeline -->
                                <div class="timeline-container">
                                    <h3>Unit Status History</h3>
                                    <p>Timeline of unit status changes and occupancy history</p>

                                    <div class="timeline">
                                        <div class="timeline-item">
                                            <div class="timeline-dot"><i class='bx bx-check'></i></div>
                                            <div class="timeline-content">
                                                <div class="timeline-date">June 1, 2023 - Present</div>
                                                <div class="timeline-title">Rented to John Smith</div>
                                                <div class="timeline-description">
                                                    Monthly rent: $1,200 | Lease term: 12 months | Deposit: $1,200
                                                </div>
                                            </div>
                                        </div>
                                        <div class="timeline-item">
                                            <div class="timeline-dot"><i class='bx bx-calendar'></i></div>
                                            <div class="timeline-content">
                                                <div class="timeline-date">May 15 - May 31, 2023</div>
                                                <div class="timeline-title">Available for Rent</div>
                                                <div class="timeline-description">
                                                    Listed at $1,250/month | 5 showings | 2 applications
                                                </div>
                                            </div>
                                        </div>
                                        <div class="timeline-item">
                                            <div class="timeline-dot"><i class='bx bx-user'></i></div>
                                            <div class="timeline-content">
                                                <div class="timeline-date">January 1 - May 14, 2023</div>
                                                <div class="timeline-title">Rented to Sarah Johnson</div>
                                                <div class="timeline-description">
                                                    Monthly rent: $1,150 | Early termination due to relocation
                                                </div>
                                            </div>
                                        </div>
                                        <div class="timeline-item">
                                            <div class="timeline-dot"><i class='bx bx-home'></i></div>
                                            <div class="timeline-content">
                                                <div class="timeline-date">March 2022 - December 2022</div>
                                                <div class="timeline-title">Owned by Property Management</div>
                                                <div class="timeline-description">
                                                    Used for corporate housing and short-term rentals
                                                </div>
                                            </div>
                                        </div>
                                        <div class="timeline-item">
                                            <div class="timeline-dot"><i class='bx bx-dollar'></i></div>
                                            <div class="timeline-content">
                                                <div class="timeline-date">February 15, 2022</div>
                                                <div class="timeline-title">Purchased by Property</div>
                                                <div class="timeline-description">
                                                    Purchase price: $350,000 | Closing costs: $10,500
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Unit Transactions -->
                                <div class="data-table-container">
                                    <h3>Unit Transactions</h3>
                                    <p>Financial transactions related to this unit including rent payments, expenses, and other charges</p>

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
                                            <td>June 1, 2023</td>
                                            <td>TXN-2023-105-001</td>
                                            <td>June Rent Payment</td>
                                            <td><span class="badge badge-success">Income</span></td>
                                            <td style="color: var(--success); font-weight: 600;">+ $1,200.00</td>
                                            <td><span class="badge badge-success">Completed</span></td>
                                        </tr>
                                        <tr>
                                            <td>June 15, 2023</td>
                                            <td>TXN-2023-105-002</td>
                                            <td>Plumbing Repair - Leaky faucet</td>
                                            <td><span class="badge badge-danger">Expense</span></td>
                                            <td style="color: var(--danger); font-weight: 600;">- $85.00</td>
                                            <td><span class="badge badge-warning">Pending</span></td>
                                        </tr>
                                        <tr>
                                            <td>June 1, 2023</td>
                                            <td>TXN-2023-105-003</td>
                                            <td>Parking Spot Rental</td>
                                            <td><span class="badge badge-success">Income</span></td>
                                            <td style="color: var(--success); font-weight: 600;">+ $75.00</td>
                                            <td><span class="badge badge-success">Completed</span></td>
                                        </tr>
                                        <tr>
                                            <td>May 1, 2023</td>
                                            <td>TXN-2023-105-004</td>
                                            <td>May Rent Payment</td>
                                            <td><span class="badge badge-success">Income</span></td>
                                            <td style="color: var(--success); font-weight: 600;">+ $1,200.00</td>
                                            <td><span class="badge badge-success">Completed</span></td>
                                        </tr>
                                        <tr>
                                            <td>April 15, 2023</td>
                                            <td>TXN-2023-105-005</td>
                                            <td>HVAC Maintenance</td>
                                            <td><span class="badge badge-danger">Expense</span></td>
                                            <td style="color: var(--danger); font-weight: 600;">- $120.00</td>
                                            <td><span class="badge badge-success">Completed</span></td>
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
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
        // Initialize jsPDF
        const { jsPDF } = window.jspdf;

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize charts
            let incomeExpenseChart, incomeSourcesChart, expenseCategoriesChart, maintenanceChart;

            // Building and unit data (simulated)
            const buildingData = {
                '1': {
                    name: 'Downtown Tower',
                    units: [
                        { id: '101', name: 'UNIT-101', type: '1 Bedroom' },
                        { id: '105', name: 'UNIT-105', type: '2 Bedroom' },
                        { id: '202', name: 'UNIT-202', type: 'Studio' }
                    ]
                },
                '2': {
                    name: 'Riverside Apartments',
                    units: [
                        { id: '301', name: 'UNIT-301', type: '3 Bedroom' },
                        { id: '302', name: 'UNIT-302', type: '2 Bedroom' }
                    ]
                },
                '3': {
                    name: 'Hillside Complex',
                    units: [
                        { id: '401', name: 'UNIT-401', type: '1 Bedroom' },
                        { id: '405', name: 'UNIT-405', type: '2 Bedroom' },
                        { id: '410', name: 'UNIT-410', type: 'Penthouse' }
                    ]
                }
            };

            // Date range selector
            document.getElementById('dateRange').addEventListener('change', function() {
                const value = this.value;
                const customDateContainer = document.getElementById('customDateRangeContainer');

                if (value === 'custom') {
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

            // Building selector - populate units
            document.getElementById('buildingSelect').addEventListener('change', function() {
                const buildingId = this.value;
                const unitSelect = document.getElementById('unitSelect');

                unitSelect.innerHTML = '<option value="">Select Unit</option>';
                unitSelect.disabled = !buildingId;

                if (buildingId) {
                    buildingData[buildingId].units.forEach(unit => {
                        const option = document.createElement('option');
                        option.value = unit.id;
                        option.textContent = `${unit.name} (${unit.type})`;
                        unitSelect.appendChild(option);
                    });
                }
            });

            // Generate report button
            document.getElementById('generateReport').addEventListener('click', function() {
                const buildingId = document.getElementById('buildingSelect').value;
                const unitId = document.getElementById('unitSelect').value;

                if (!buildingId || !unitId) {
                    alert('Please select both a building and a unit');
                    return;
                }

                // Show loading
                const btn = this;
                btn.disabled = true;
                btn.innerHTML = '<i class="bx bx-loader bx-spin"></i> Generating...';

                // Simulate API call delay
                setTimeout(() => {
                    // Render all charts
                    renderIncomeExpenseChart();
                    renderIncomeSourcesChart();
                    renderExpenseCategoriesChart();
                    renderMaintenanceChart();

                    // Show report content
                    document.getElementById('reportContent').style.display = 'block';

                    // Reset button
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bx bx-printer"></i> Generate Report';

                    // Scroll to report
                    document.getElementById('reportContent').scrollIntoView({ behavior: 'smooth' });
                }, 1000);
            });

            // Export functionality
            document.getElementById('exportPdf').addEventListener('click', exportToPdf);
            document.getElementById('exportImage').addEventListener('click', exportToImage);

            function renderIncomeExpenseChart() {
                const ctx = document.getElementById('incomeExpenseChart');
                if (!ctx) return;

                if (incomeExpenseChart) incomeExpenseChart.destroy();

                incomeExpenseChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Income', 'Expenses'],
                        datasets: [{
                            data: [2400, 850],
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

            function renderIncomeSourcesChart() {
                const ctx = document.getElementById('incomeSourcesChart');
                if (!ctx) return;

                if (incomeSourcesChart) incomeSourcesChart.destroy();

                incomeSourcesChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Rent', 'Parking', 'Amenities'],
                        datasets: [{
                            label: 'Amount',
                            data: [1200, 75, 1125],
                            backgroundColor: ['#184E83', '#1A6FC9', '#2ecc71'],
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

            function renderExpenseCategoriesChart() {
                const ctx = document.getElementById('expenseCategoriesChart');
                if (!ctx) return;

                if (expenseCategoriesChart) expenseCategoriesChart.destroy();

                expenseCategoriesChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Maintenance', 'Utilities', 'Insurance', 'Other'],
                        datasets: [{
                            label: 'Amount',
                            data: [450, 250, 100, 50],
                            backgroundColor: ['#ff4d6d', '#ff758f', '#ff8fa3', '#ffb3c1'],
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

            function renderMaintenanceChart() {
                const ctx = document.getElementById('maintenanceChart');
                if (!ctx) return;

                if (maintenanceChart) maintenanceChart.destroy();

                // Determine time period labels based on selected range
                let timeLabels = [];
                const range = document.getElementById('dateRange').value;

                if (range === '7days') {
                    timeLabels = ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'];
                } else if (range === '30days') {
                    timeLabels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
                } else if (range === '90days') {
                    timeLabels = ['Month 1', 'Month 2', 'Month 3'];
                } else if (range === 'custom') {
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

                maintenanceChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: timeLabels,
                        datasets: [
                            {
                                label: 'Maintenance Requests',
                                data: timeLabels.map(() => Math.floor(Math.random() * 5) + 1),
                                borderColor: '#184E83',
                                backgroundColor: 'rgba(24, 78, 131, 0.1)',
                                borderWidth: 2,
                                tension: 0.3,
                                fill: true,
                                pointBackgroundColor: '#184E83',
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
                                },
                                title: {
                                    display: true,
                                    text: 'Number of Requests'
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

            function exportToPdf() {
                showLoading();
                document.body.classList.add('exporting');

                const element = document.getElementById('reportContent');
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
                        pdf.save(`Unit_Report_${getFormattedDate()}.pdf`);

                        hideLoading();
                        document.body.classList.remove('exporting');
                    });
                }, 500);
            }

            function exportToImage() {
                showLoading();
                document.body.classList.add('exporting');

                const element = document.getElementById('reportContent');
                const options = {
                    scale: 2,
                    useCORS: true,
                    scrollY: 0,
                    backgroundColor: '#FFFFFF'
                };

                setTimeout(() => {
                    html2canvas(element, options).then(canvas => {
                        const link = document.createElement('a');
                        link.download = `Unit_Report_${getFormattedDate()}.png`;
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
