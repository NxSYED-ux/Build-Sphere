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
    <style>
        /* Previous styles remain the same, add these new styles */
        .unit-category {
            margin-bottom: 30px;
            background: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .unit-category h4 {
            font-size: 16px;
            color: var(--primary);
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #eee;
        }

        .category-summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .category-count {
            font-weight: 600;
            color: var(--dark);
        }

        .category-value {
            font-weight: 600;
            color: var(--primary);
        }
    </style>
@endpush

@section('content')
    <!-- Top Navbar -->
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
        ['url' => '#', 'label' => 'Dashboard'],
        ['url' => '', 'label' => 'Unit Reports']
    ]"/>

    <!-- Side Navbar -->
    <x-Owner.side-navbar :openSections="['Reports']"/>
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
                                            Filter and view units by their current status
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Enhanced Filters Section -->
                            <div class="filters-container">
                                <div class="filters-header">
                                    <div>
                                        <h3>Unit Filters</h3>
                                        <p>Select criteria to filter units</p>
                                    </div>
                                </div>

                                <div class="filters-row">
                                    <div class="filter-group">
                                        <label for="unitStatusType">Unit Status Type</label>
                                        <select id="unitStatusType">
                                            <option value="all">All Units</option>
                                            <option value="current">Currently Present</option>
                                            <option value="rented_then_sold">First Rented Last Period & Now Sold</option>
                                            <option value="only_sold">Only Sold Now</option>
                                        </select>
                                    </div>

                                    <div class="filter-group">
                                        <label for="buildingSelect">Building</label>
                                        <select id="buildingSelect">
                                            <option value="">All Buildings</option>
                                            <option value="1">Downtown Tower</option>
                                            <option value="2">Riverside Apartments</option>
                                            <option value="3">Hillside Complex</option>
                                        </select>
                                    </div>

                                    <div class="action-buttons">
                                        <button class="generate-btn" id="generateReport">
                                            <i class='bx bx-filter-alt'></i> Filter Units
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Report Content -->
                            <div class="report-content" id="reportContent">
                                <!-- Unit Categories Section -->
                                <div class="data-table-container">
                                    <!-- Currently Present Units -->
                                    <div class="unit-category" id="currentUnitsSection">
                                        <div class="category-summary">
                                            <h4>Currently Present Units</h4>
                                            <div>
                                                <span class="category-count">3 Units</span> |
                                                <span class="category-value">Total Value: $1,250,000</span>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="data-table">
                                                <thead>
                                                <tr>
                                                    <th>Unit ID</th>
                                                    <th>Building</th>
                                                    <th>Type</th>
                                                    <th>Status</th>
                                                    <th>Current Tenant</th>
                                                    <th>Monthly Rent</th>
                                                    <th>Market Value</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>UNIT-105</td>
                                                    <td>Downtown Tower</td>
                                                    <td>2 Bedroom</td>
                                                    <td><span class="badge badge-success">Rented</span></td>
                                                    <td>John Smith</td>
                                                    <td>$1,200</td>
                                                    <td>$420,000</td>
                                                </tr>
                                                <tr>
                                                    <td>UNIT-202</td>
                                                    <td>Downtown Tower</td>
                                                    <td>Studio</td>
                                                    <td><span class="badge badge-warning">Vacant</span></td>
                                                    <td>-</td>
                                                    <td>$950</td>
                                                    <td>$350,000</td>
                                                </tr>
                                                <tr>
                                                    <td>UNIT-301</td>
                                                    <td>Riverside Apartments</td>
                                                    <td>3 Bedroom</td>
                                                    <td><span class="badge badge-success">Rented</span></td>
                                                    <td>Sarah Johnson</td>
                                                    <td>$1,800</td>
                                                    <td>$480,000</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- First Rented Last Period & Now Sold -->
                                    <div class="unit-category" id="rentedThenSoldSection">
                                        <div class="category-summary">
                                            <h4>First Rented Last Period & Now Sold</h4>
                                            <div>
                                                <span class="category-count">2 Units</span> |
                                                <span class="category-value">Total Sale Value: $780,000</span>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="data-table">
                                                <thead>
                                                <tr>
                                                    <th>Unit ID</th>
                                                    <th>Building</th>
                                                    <th>Type</th>
                                                    <th>First Rented Date</th>
                                                    <th>Tenant</th>
                                                    <th>Sold Date</th>
                                                    <th>Sale Price</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>UNIT-101</td>
                                                    <td>Downtown Tower</td>
                                                    <td>1 Bedroom</td>
                                                    <td>May 15, 2023</td>
                                                    <td>Michael Brown</td>
                                                    <td>June 20, 2023</td>
                                                    <td>$380,000</td>
                                                </tr>
                                                <tr>
                                                    <td>UNIT-302</td>
                                                    <td>Riverside Apartments</td>
                                                    <td>2 Bedroom</td>
                                                    <td>April 1, 2023</td>
                                                    <td>Emily Davis</td>
                                                    <td>June 10, 2023</td>
                                                    <td>$400,000</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Only Sold Now -->
                                    <div class="unit-category" id="onlySoldSection">
                                        <div class="category-summary">
                                            <h4>Only Sold Now</h4>
                                            <div>
                                                <span class="category-count">1 Unit</span> |
                                                <span class="category-value">Total Sale Value: $520,000</span>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="data-table">
                                                <thead>
                                                <tr>
                                                    <th>Unit ID</th>
                                                    <th>Building</th>
                                                    <th>Type</th>
                                                    <th>Purchase Date</th>
                                                    <th>Sold Date</th>
                                                    <th>Holding Period</th>
                                                    <th>Sale Price</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>UNIT-410</td>
                                                    <td>Hillside Complex</td>
                                                    <td>Penthouse</td>
                                                    <td>March 1, 2023</td>
                                                    <td>June 10, 2023</td>
                                                    <td>3 months</td>
                                                    <td>$520,000</td>
                                                </tr>
                                                </tbody>
                                            </table>
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
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize charts
            let incomeExpenseChart, incomeSourcesChart, expenseCategoriesChart, maintenanceChart;

            // For demo purposes - you'll replace this with your actual unit selection logic
            const demoUnitId = '105';

            // CSRF token for all requests
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Load all data when page loads
            loadAllData(demoUnitId);

            function loadAllData(unitId) {
                fetchUnitDetails(unitId);
                fetchUnitTrends(unitId);
                fetchIncomeExpenseData(unitId);
                fetchIncomeSources(unitId);
                fetchExpenseSources(unitId);
                fetchCurrentContract(unitId);
                fetchMaintenanceData(unitId);
                fetchStatusHistory(unitId);
                fetchTransactionHistory(unitId);
            }

            function fetchUnitDetails(unitId) {
                const url = `{{ route('owner.reports.units.details', ':unitId') }}`.replace(':unitId', 1);

                fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) throw response;
                        return response.json();
                    })
                    .then(data => {
                        // Update unit details in the UI
                        document.getElementById('unitImage').src = data.image_url;
                        document.getElementById('unitTitle').textContent = `${data.name} Details`;
                        document.getElementById('unitId').textContent = data.id;
                        document.getElementById('unitBuilding').textContent = data.building;
                        document.getElementById('unitType').textContent = data.type;
                        document.getElementById('unitSize').textContent = data.size;

                        // Set status with appropriate badge
                        if (data.status === 'rented') {
                            document.getElementById('unitStatus').innerHTML = '<span class="badge badge-success">Rented</span>';
                            document.getElementById('tenantLabel').textContent = 'Current Tenant:';
                            document.getElementById('valueLabel').textContent = 'Market Value:';
                        } else {
                            document.getElementById('unitStatus').innerHTML = '<span class="badge badge-primary">Sold</span>';
                            document.getElementById('tenantLabel').textContent = 'Last Tenant:';
                            document.getElementById('valueLabel').textContent = 'Sale Price:';
                        }

                        document.getElementById('unitTenant').textContent = data.status === 'rented' ?
                            `${data.tenant} (since ${data.tenant_since})` : data.tenant;
                        document.getElementById('unitPurchasePrice').textContent = data.purchase_price;
                        document.getElementById('unitValue').textContent = data.status === 'rented' ?
                            data.market_value : data.sale_price;
                    })
                    .catch(error => {
                        console.error('Error fetching unit details:', error);
                        alert('Failed to load unit details');
                    });
            }

            function fetchUnitTrends(unitId) {
                fetch(`{{ route('owner.reports.units.trends', ':unitId') }}`.replace(':unitId', 1), {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) throw response;
                        return response.json();
                    })
                    .then(data => {
                        // Update metrics
                        document.getElementById('totalIncome').textContent = data.total_income;
                        document.getElementById('totalExpenses').textContent = data.total_expenses;
                        document.getElementById('netProfit').textContent = data.net_profit;
                        document.getElementById('occupancyRate').textContent = data.occupancy_rate;

                        // Update changes
                        document.getElementById('incomeChange').textContent = data.income_change;
                        document.getElementById('expensesChange').textContent = data.expenses_change;
                        document.getElementById('profitChange').textContent = data.profit_change;
                        document.getElementById('occupancyChange').textContent = data.occupancy_change;

                        // Update progress bars
                        const incomeGrowth = parseInt(data.income_change);
                        const expenseReduction = parseInt(data.expenses_change);

                        document.getElementById('incomeGrowth').textContent =
                            incomeGrowth > 0 ? `+${incomeGrowth}%` : `${incomeGrowth}%`;
                        document.getElementById('expenseReduction').textContent =
                            expenseReduction > 0 ? `+${expenseReduction}%` : `${expenseReduction}%`;

                        document.getElementById('incomeGrowthBar').style.width = `${Math.min(Math.abs(incomeGrowth), 100)}%`;
                        document.getElementById('expenseReductionBar').style.width = `${Math.min(Math.abs(expenseReduction), 100)}%`;

                        // Update financial summary
                        document.getElementById('summaryIncome').textContent = data.total_income;
                        document.getElementById('summaryExpenses').textContent = data.total_expenses;
                        document.getElementById('summaryProfit').textContent = data.net_profit;

                        // Calculate profit margin
                        const income = parseFloat(data.total_income.replace(/[^0-9.]/g, ''));
                        const profit = parseFloat(data.net_profit.replace(/[^0-9.]/g, ''));
                        const margin = income > 0 ? (profit / income * 100).toFixed(1) + '%' : 'N/A';
                        document.getElementById('summaryMargin').textContent = margin;
                    })
                    .catch(error => {
                        console.error('Error fetching unit trends:', error);
                        alert('Failed to load unit trends');
                    });
            }

            function fetchIncomeExpenseData(unitId) {
                fetch(`{{ route('owner.reports.units.income-expense', ':unitId') }}`.replace(':unitId', 1), {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) throw response;
                        return response.json();
                    })
                    .then(data => {
                        renderIncomeExpenseChart(data);
                    })
                    .catch(error => {
                        console.error('Error fetching income/expense data:', error);
                        alert('Failed to load income/expense data');
                    });
            }

            function fetchIncomeSources(unitId) {
                fetch(`{{ route('owner.reports.units.income-sources', ':unitId') }}`.replace(':unitId', 1), {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) throw response;
                        return response.json();
                    })
                    .then(data => {
                        renderIncomeSourcesChart(data);
                        document.getElementById('incomeSourcesValue').textContent = `${data.labels.length} Sources`;
                    })
                    .catch(error => {
                        console.error('Error fetching income sources:', error);
                        alert('Failed to load income sources');
                    });
            }

            function fetchExpenseSources(unitId) {
                fetch(`{{ route('owner.reports.units.expense-sources', ':unitId') }}`.replace(':unitId', 1), {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) throw response;
                        return response.json();
                    })
                    .then(data => {
                        renderExpenseCategoriesChart(data);
                        document.getElementById('expenseCategoriesValue').textContent = `${data.labels.length} Categories`;
                    })
                    .catch(error => {
                        console.error('Error fetching expense sources:', error);
                        alert('Failed to load expense sources');
                    });
            }

            function fetchCurrentContract(unitId) {
                fetch(`{{ route('owner.reports.units.current-contract', ':unitId') }}`.replace(':unitId', 1), {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) throw response;
                        return response.json();
                    })
                    .then(data => {
                        document.getElementById('contractTenantName').textContent = data.tenant_name;
                        document.getElementById('contractStartDate').textContent = data.start_date;
                        document.getElementById('contractEndDate').textContent = data.end_date;
                        document.getElementById('contractMonthlyRent').textContent = data.monthly_rent;
                        document.getElementById('contractDeposit').textContent = data.deposit;

                        // Set payment status badge
                        let paymentStatusBadge;
                        switch(data.payment_status.toLowerCase()) {
                            case 'current':
                                paymentStatusBadge = '<span class="badge badge-success">Current</span>';
                                break;
                            case 'overdue':
                                paymentStatusBadge = '<span class="badge badge-danger">Overdue</span>';
                                break;
                            case 'partial':
                                paymentStatusBadge = '<span class="badge badge-warning">Partial</span>';
                                break;
                            default:
                                paymentStatusBadge = `<span class="badge">${data.payment_status}</span>`;
                        }
                        document.getElementById('contractPaymentStatus').innerHTML = paymentStatusBadge;

                        document.getElementById('contractLeaseType').textContent = data.lease_type;

                        // Tenant details
                        document.getElementById('tenantContact').textContent = data.tenant_details.contact;
                        document.getElementById('tenantEmergencyContact').textContent = data.tenant_details.emergency_contact;
                        document.getElementById('tenantOccupation').textContent = data.tenant_details.occupation;
                        document.getElementById('tenantNotes').textContent = data.tenant_details.notes;
                    })
                    .catch(error => {
                        console.error('Error fetching current contract:', error);
                        alert('Failed to load current contract');
                    });
            }

            function fetchMaintenanceData(unitId) {
                fetch(`{{ route('owner.reports.units.maintenance', ':unitId') }}`.replace(':unitId', 1), {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) throw response;
                        return response.json();
                    })
                    .then(data => {
                        // Update maintenance requests table
                        const tbody = document.getElementById('maintenanceRequestsBody');
                        tbody.innerHTML = '';

                        data.active_requests.forEach(request => {
                            const row = document.createElement('tr');

                            let statusBadge;
                            switch(request.status) {
                                case 'in_progress':
                                    statusBadge = '<span class="badge badge-warning">In Progress</span>';
                                    break;
                                case 'pending':
                                    statusBadge = '<span class="badge badge-danger">Pending</span>';
                                    break;
                                case 'completed':
                                    statusBadge = '<span class="badge badge-primary">Completed</span>';
                                    break;
                                default:
                                    statusBadge = `<span class="badge">${request.status}</span>`;
                            }

                            row.innerHTML = `
                    <td>${request.id}</td>
                    <td>${request.type}</td>
                    <td>${request.reported}</td>
                    <td>${statusBadge}</td>
                `;
                            tbody.appendChild(row);
                        });

                        // Maintenance history summary
                        document.getElementById('completedMaintenance').textContent = data.history.completed;
                        document.getElementById('inProgressMaintenance').textContent = data.history.in_progress;
                        document.getElementById('pendingMaintenance').textContent = data.history.pending;

                        // Render maintenance chart
                        renderMaintenanceChart(data.chart_data);
                    })
                    .catch(error => {
                        console.error('Error fetching maintenance data:', error);
                        alert('Failed to load maintenance data');
                    });
            }

            function fetchStatusHistory(unitId) {
                fetch(`{{ route('owner.reports.units.status-history', ':unitId') }}`.replace(':unitId', 1), {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) throw response;
                        return response.json();
                    })
                    .then(data => {
                        const timelineContainer = document.getElementById('unitTimeline');
                        timelineContainer.innerHTML = '';

                        const icons = ['bx-check', 'bx-calendar', 'bx-user', 'bx-home', 'bx-dollar'];

                        data.forEach((item, index) => {
                            const icon = icons[index] || 'bx-calendar';

                            const timelineItem = document.createElement('div');
                            timelineItem.className = 'timeline-item';
                            timelineItem.innerHTML = `
                    <div class="timeline-dot"><i class='bx ${icon}'></i></div>
                    <div class="timeline-content">
                        <div class="timeline-date">${item.date}</div>
                        <div class="timeline-title">${item.title}</div>
                        <div class="timeline-description">${item.description}</div>
                    </div>
                `;
                            timelineContainer.appendChild(timelineItem);
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching status history:', error);
                        alert('Failed to load status history');
                    });
            }

            function fetchTransactionHistory(unitId) {
                fetch(`{{ route('owner.reports.units.transactions', ':unitId') }}`.replace(':unitId', 1), {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) throw response;
                        return response.json();
                    })
                    .then(data => {
                        const tbody = document.getElementById('transactionsBody');
                        tbody.innerHTML = '';

                        data.forEach(transaction => {
                            const row = document.createElement('tr');

                            let typeBadge;
                            if (transaction.type === 'income') {
                                typeBadge = '<span class="badge badge-success">Income</span>';
                            } else {
                                typeBadge = '<span class="badge badge-danger">Expense</span>';
                            }

                            let amountColor = transaction.type === 'income' ? 'color: var(--success);' : 'color: var(--danger);';

                            let statusBadge;
                            switch(transaction.status) {
                                case 'completed':
                                    statusBadge = '<span class="badge badge-success">Completed</span>';
                                    break;
                                case 'pending':
                                    statusBadge = '<span class="badge badge-warning">Pending</span>';
                                    break;
                                case 'failed':
                                    statusBadge = '<span class="badge badge-danger">Failed</span>';
                                    break;
                                default:
                                    statusBadge = `<span class="badge">${transaction.status}</span>`;
                            }

                            row.innerHTML = `
                    <td>${transaction.date}</td>
                    <td>${transaction.id}</td>
                    <td>${transaction.description}</td>
                    <td>${typeBadge}</td>
                    <td style="${amountColor} font-weight: 600;">${transaction.amount}</td>
                    <td>${statusBadge}</td>
                `;
                            tbody.appendChild(row);
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching transaction history:', error);
                        alert('Failed to load transaction history');
                    });
            }

            // Chart rendering functions
            function renderIncomeExpenseChart(chartData) {
                const ctx = document.getElementById('incomeExpenseChart');
                if (!ctx) return;

                if (incomeExpenseChart) incomeExpenseChart.destroy();

                incomeExpenseChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                            data: chartData.data,
                            backgroundColor: chartData.colors,
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

            function renderIncomeSourcesChart(chartData) {
                const ctx = document.getElementById('incomeSourcesChart');
                if (!ctx) return;

                if (incomeSourcesChart) incomeSourcesChart.destroy();

                incomeSourcesChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                            label: 'Amount',
                            data: chartData.data,
                            backgroundColor: chartData.colors,
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

            function renderExpenseCategoriesChart(chartData) {
                const ctx = document.getElementById('expenseCategoriesChart');
                if (!ctx) return;

                if (expenseCategoriesChart) expenseCategoriesChart.destroy();

                expenseCategoriesChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                            label: 'Amount',
                            data: chartData.data,
                            backgroundColor: chartData.colors,
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

            function renderMaintenanceChart(chartData) {
                const ctx = document.getElementById('maintenanceChart');
                if (!ctx) return;

                if (maintenanceChart) maintenanceChart.destroy();

                maintenanceChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.labels,
                        datasets: [
                            {
                                label: 'Maintenance Requests',
                                data: chartData.data,
                                borderColor: chartData.color,
                                backgroundColor: 'rgba(24, 78, 131, 0.1)',
                                borderWidth: 2,
                                tension: 0.3,
                                fill: true,
                                pointBackgroundColor: chartData.color,
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

            // Export functionality
            document.getElementById('exportPdf').addEventListener('click', exportToPdf);
            document.getElementById('exportImage').addEventListener('click', exportToImage);

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

                setTimeout(() => {
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
