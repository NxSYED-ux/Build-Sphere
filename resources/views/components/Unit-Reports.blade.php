@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #184E83;
            --primary-light: #1A6FC9;
            --danger: #ff4d6d;
            --warning: #ffbe0b;
            --success: #2ecc71;
            --light: #f8f9fa;
            --light-gray: #f5f7fa;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            --transition: all 0.3s ease;
        }

        #UnitReports {
            display: none;
        }

        /* ========================== */
        /* contract-details-container */
        /* ========================== */
        #UnitReports .contract-details-container {
            background: var(--sidenavbar-body-color);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 25px;
            margin-bottom: 20px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .contract-details-container .contract-type-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 16px;
            font-size: 14px;
            font-weight: 600;
            background: #e8f4fd;
            color: #1a73e8;
            margin-left: 10px;
        }

        .contract-details-container .contract-info-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .contract-details-container .info-item {
            display: flex;
            flex-direction: column;
            padding: 12px;
            border-radius: 6px;
            background: var(--body-background-color);
            min-height: 80px;
            border-bottom: 1px solid var(--sidenavbar-text-color);
        }

        .contract-details-container .info-label {
            font-weight: 600;
            font-size: 13px;
            color: var(--sidenavbar-text-color);
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .contract-details-container .info-value {
            font-size: 14px;
            color: var(--sidenavbar-text-color);
            word-break: break-word;
        }

        .contract-details-container .highlight {
            /*background: #edf7ed;*/
            /*border-left: 3px solid #4caf50;*/
        }

        .contract-details-container .highlight .info-value {
            /*color: #2e7d32;*/
            /*font-weight: 600;*/
        }

        /* Medium screens (tablets) */
        @media (max-width: 1024px) {
            .contract-details-container .contract-info-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* Small screens */
        @media (max-width: 768px) {
            .contract-details-container .contract-info-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Extra small screens */
        @media (max-width: 480px) {
            .contract-details-container .contract-info-grid {
                grid-template-columns: 1fr;
            }

            .contract-details-container .contract-details-container {
                padding: 15px;
            }
        }

        #UnitReports .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        #UnitReports .badge-primary {
            background-color: rgba(24, 78, 131, 0.1);
            color: var(--primary);
        }

        #UnitReports .badge-success {
            background-color: rgba(46, 204, 113, 0.1);
            color: var(--success);
        }

        #UnitReports .badge-warning {
            background-color: rgba(255, 190, 11, 0.1);
            color: var(--warning);
        }

        #UnitReports .badge-danger {
            background-color: rgba(255, 77, 109, 0.1);
            color: var(--danger);
        }

        /* Unit Status Timeline */
        #UnitReports .timeline-container {
            background: var(--sidenavbar-body-color);
            border-radius: 8px;
            box-shadow: var(--box-shadow);
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        #UnitReports .timeline {
            position: relative;
            padding-left: 30px;
            margin-top: 20px;
        }

        #UnitReports .timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: #e0e0e0;
        }

        #UnitReports .timeline-item {
            position: relative;
            padding-bottom: 20px;
        }

        #UnitReports .timeline-item:last-child {
            padding-bottom: 0;
        }

        #UnitReports .timeline-dot {
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
            color: #fff;
            font-size: 10px;
        }

        #UnitReports .timeline-content {
            padding: 10px 15px;
            background-color: var(--main-background-color2);
            border-radius: 6px;
        }

        #UnitReports .timeline-date {
            font-size: 12px;
            color: var(--sidenavbar-text-color);
            margin-bottom: 5px;
        }

        #UnitReports .timeline-title {
            font-weight: 600;
            margin-bottom: 5px;
        }

        #UnitReports .timeline-description {
            font-size: 13px;
            color: var(--sidenavbar-text-color);
        }


        /* Maintenance Requests Section - Specific Styles */
        /* Maintenance Section - Container Styles */
        #UnitReports .maintenance-section {
            margin-bottom: 25px;
        }

        #UnitReports .maintenance-section .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }

        #UnitReports .maintenance-section .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding-right: 15px;
            padding-left: 15px;
            display: flex;
            flex-direction: column;
            height: 500px;

        }

        /* Table Container Styles */
        #UnitReports .maintenance-section .data-table-container {
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: var(--sidenavbar-body-color);
            border-radius: 8px;
            padding: 10px;
            box-shadow: var(--box-shadow);
        }

        #UnitReports .maintenance-section .data-table-container h3 {
            padding: 15px 20px;
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            border-bottom: 1px solid #f0f0f0;
        }

        #UnitReports .maintenance-section .table-wrapper {
            flex: 1;
            overflow: auto;
            max-height: 100%;

            /* Hide scrollbar but keep scrolling */
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE/Edge */
        }

        #UnitReports .maintenance-section .table-wrapper::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }

        /* Table Styles */
        #UnitReports .maintenance-section .data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            min-width: 600px; /* Minimum width before scrolling kicks in */
        }

        #UnitReports .maintenance-section .data-table th,
        #UnitReports .maintenance-section .data-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
            word-wrap: break-word;
            vertical-align: top;
        }

        #UnitReports .maintenance-section .data-table th {
            position: sticky;
            top: 0;
            background-color: #f8f9fa;
            font-weight: 600;
            color: var(--sidenavbar-text-color);
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
        }

        #UnitReports .maintenance-section .data-table tbody tr:hover {
            background-color: rgba(24, 78, 131, 0.03);
        }

        /* Column Widths */
        #UnitReports .maintenance-section .data-table th:nth-child(1),
        #UnitReports .maintenance-section .data-table td:nth-child(1) {
            width: 15%; /* Department */
        }

        #UnitReports .maintenance-section .data-table th:nth-child(2),
        #UnitReports .maintenance-section .data-table td:nth-child(2) {
            width: 15%; /* User */
        }

        #UnitReports .maintenance-section .data-table th:nth-child(3),
        #UnitReports .maintenance-section .data-table td:nth-child(3) {
            width: 40%; /* Description */
        }

        #UnitReports .maintenance-section .data-table th:nth-child(4),
        #UnitReports .maintenance-section .data-table td:nth-child(4) {
            width: 15%; /* Date */
        }

        #UnitReports .maintenance-section .data-table th:nth-child(5),
        #UnitReports .maintenance-section .data-table td:nth-child(5) {
            width: 15%; /* Status */
        }

        /* Status Badges */
        #UnitReports .maintenance-section .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            text-transform: capitalize;
        }

        #UnitReports .maintenance-section .badge-primary {
            background-color: rgba(24, 78, 131, 0.1);
            color: var(--primary);
        }

        #UnitReports .maintenance-section .badge-success {
            background-color: rgba(46, 204, 113, 0.1);
            color: var(--success);
        }

        #UnitReports .maintenance-section .badge-warning {
            background-color: rgba(255, 190, 11, 0.1);
            color: var(--warning);
        }

        #UnitReports .maintenance-section .badge-danger {
            background-color: rgba(255, 77, 109, 0.1);
            color: var(--danger);
        }

        /* Right Side Chart Container */
        #UnitReports .maintenance-section .chart-container {
            height: 100%;
            display: flex;
            flex-direction: column;
            background: var(--sidenavbar-body-color);
            border-radius: 8px;
            box-shadow: var(--box-shadow);
            overflow: hidden;
        }

        #UnitReports .maintenance-section .chart-header {
            padding: 5px 20px;
            border-bottom: 1px solid #f0f0f0;
        }

        #UnitReports .maintenance-section .chart-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        #UnitReports .maintenance-section .chart-value {
            font-size: 13px;
            color: var(--sidenavbar-text-color);
            margin-top: 5px;
        }

        #UnitReports .maintenance-section .chart-canvas-container {
            flex: 1;
            min-height: 250px;
            padding: 15px;
        }

        #UnitReports .maintenance-section .status-grid {
            display: flex;
        }

        #UnitReports .maintenance-section .status-card {
            flex: 1;
            text-align: center;
            padding: 10px;
        }

        #UnitReports .maintenance-section .status-count {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        #UnitReports .maintenance-section .status-label {
            font-size: 13px;
            color: var(--sidenavbar-text-color);
            text-transform: uppercase;
        }

        /* Responsive Adjustments */
        @media (max-width: 1200px) {
            #UnitReports .maintenance-section .data-table th:nth-child(1),
            #UnitReports .maintenance-section .data-table td:nth-child(1),
            #UnitReports .maintenance-section .data-table th:nth-child(2),
            #UnitReports .maintenance-section .data-table td:nth-child(2) {
                width: 18%;
            }

            #UnitReports .maintenance-section .data-table th:nth-child(3),
            #UnitReports .maintenance-section .data-table td:nth-child(3) {
                width: 34%;
            }
        }

        @media (max-width: 992px) {
            #UnitReports .maintenance-section .col-md-6 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            #UnitReports .maintenance-section .data-table-container {
                margin-bottom: 20px;
            }

            #UnitReports .maintenance-section .table-wrapper {
                max-height: 400px;
            }
        }

        @media (max-width: 768px) {
            #UnitReports .maintenance-section .data-table {
                table-layout: auto;
                min-width: 100%;
            }

            #UnitReports .maintenance-section .data-table th,
            #UnitReports .maintenance-section .data-table td {
                padding: 8px 12px;
                font-size: 13px;
            }

            #UnitReports .maintenance-section .data-table th:nth-child(n),
            #UnitReports .maintenance-section .data-table td:nth-child(n) {
                width: auto;
            }

            #UnitReports .maintenance-section .badge {
                font-size: 11px;
                padding: 3px 6px;
            }
        }

        @media (max-width: 576px) {
            #UnitReports .maintenance-section .status-grid {
                flex-direction: column;
            }

            #UnitReports .maintenance-section .status-card {
                display: flex;
                justify-content: space-between;
                align-items: center;
                text-align: left;
                padding: 8px 15px;
            }

            #UnitReports .maintenance-section .status-count {
                margin-bottom: 0;
                font-size: 18px;
            }
        }

    </style>
@endpush

<div class="UnitReports" id="UnitReports">

    <!-- Unit Details -->
    <div class="unit-details-container building-Unit-details-container">
        <div class="unit-image-container">
            <img id="unitImage" src="" alt="Unit Image" class="unit-image">
        </div>
        <div class="unit-info">
            <h3 id="unitTitle">UNIT-<span id="unitName"></span> Details</h3>

            <!-- Dynamic Description -->
            <div class="unit-description" id="unitDescription">
                <!-- Will be populated via JS -->
            </div>

            <div class="unit-info-grid">
                <div class="info-item">
                    <span class="info-label">Building:</span>
                    <span class="info-value" id="unitBuilding">-</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Floor:</span>
                    <span class="info-value" id="unitFloor">-</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Type:</span>
                    <span class="info-value" id="unitType">-</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Area:</span>
                    <span class="info-value" id="unitSize">-</span>
                </div>

                <!-- Dynamic Status Section -->
                <div class="info-item status-item">
                    <span class="info-label">Status:</span>
                    <span class="info-value" id="unitStatusContainer">
                    <span class="badge" id="unitStatusBadge">-</span>
                    <span class="status-details" id="unitStatusDetails"></span>
                </span>
                </div>

                <div class="info-item">
                    <span class="info-label">Price:</span>
                    <span class="info-value" id="unitMarketValue">-</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="metrics-grid">
        <div class="metric-card bg-gradient-primary">
            <h4>Occupancy Rate</h4>
            <div class="value" id="unitOccupancyRate">92%</div>
        </div>
        <div class="metric-card bg-gradient-info">
            <h4>Net Operating Income</h4>
            <div class="value" id="unitNetOperatingIncome">$24,500</div>
        </div>
        <div class="metric-card bg-gradient-success">
            <h4>Average Rent</h4>
            <div class="value" id="unitAverageRent">$1,250</div>
        </div>
        <div class="metric-card bg-gradient-warning">
            <h4>Maintenance Requests</h4>
            <div class="value" id="unitMaintenanceRequests">4</div>
        </div>
    </div>

    <!-- Income vs Expense Pie Chart with Summary -->
    <div class="chart-summary-row">
        <div class="chart-container">
            <div class="chart-header">
                <h3>Income vs Expenses</h3>
                <div class="chart-value" id="unitProfitValue">PKR 0.00 Net Profit</div>
            </div>
            <div class="chart-canvas-container">
                <canvas id="unitIncomeExpenseChart"></canvas>
            </div>
        </div>
        <div class="summary-container">
            <h3 class="summary-title">Financial Summary</h3>
            <div class="summary-item">
                <span class="summary-label"><i class='bx bx-dollar-circle me-2'></i> Total Income</span>
                <span class="summary-value" id="unitSummaryIncome">PKR 0.00</span>
            </div>
            <div class="summary-item">
                <span class="summary-label"><i class='bx bx-money-withdraw me-2'></i> Total Expenses</span>
                <span class="summary-value" id="unitSummaryExpenses">PKR 0.00</span>
            </div>
            <div class="summary-item">
                <span class="summary-label"><i class='bx bx-trending-up me-2'></i> Net Profit</span>
                <span class="summary-value positive" id="unitSummaryProfit">PKR 0.00</span>
            </div>
            <div class="summary-item">
                <span class="summary-label"><i class='bx bx-line-chart me-2'></i> Profit Margin</span>
                <span class="summary-value positive" id="unitSummaryMargin">0%</span>
            </div>
            <div class="progress-container">
                <div class="unit-progress-item">
                    <div class="progress-header">
                        <span class="progress-label">Income Growth</span>
                        <span class="unit-progress-value positive">+0%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="unit-progress-fill" style="width: 0%; background-color: var(--primary);"></div>
                    </div>
                </div>
                <div class="unit-progress-item">
                    <div class="progress-header">
                        <span class="progress-label">Expense Growth</span>
                        <span class="unit-progress-value negative">+0%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="unit-progress-fill" style="width: 0%; background-color: var(--danger);"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Last Contract -->
    <div class="contract-details-container">
        <h3>Last Contract: <span class="contract-type-badge" id="contractTypeBadge">Rental</span></h3>

        <!-- Rental Contract View -->
        <div class="contract-section rental-contract">
            <div class="contract-info-grid">
                <div class="info-item">
                    <span class="info-label">Rented <span id="rentalUnitType"> Unit</span></span>
                    <span class="info-value" id="rentalUnitName">-</span>
                </div>
                <div class="info-item">
                    <span class="info-label">User Name</span>
                    <span class="info-value" id="rentalUnitUserName">-</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email</span>
                    <span class="info-value" id="rentalUnitUserEmail">-</span>
                </div>
                <div class="info-item">
                    <span class="info-label">CNIC</span>
                    <span class="info-value" id="rentalUnitUserCnic">-</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Phone Number</span>
                    <span class="info-value" id="rentalUnitUserPhoneNo">-</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Rent Start Date</span>
                    <span class="info-value" id="rentalUnitStartDate">-</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Rent End Date</span>
                    <span class="info-value" id="rentalUnitEndDate">-</span>
                </div>
                <div class="info-item highlight">
                    <span class="info-label">Contract Price</span>
                    <span class="info-value" id="rentalUnitContractPrice">-</span>
                </div>
            </div>
        </div>

        <!-- Sale Contract View (hidden by default) -->
        <div class="contract-section sale-contract" style="display:none;">
            <div class="contract-info-grid">
                <div class="info-item">
                    <span class="info-label">Purchased <span id="soldUnitType">Unit</span></span>
                    <span class="info-value" id="soldUnitName">-</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Buyer Name</span>
                    <span class="info-value" id="soldUnitUserName">-</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email</span>
                    <span class="info-value" id="soldUnitUserEmail">-</span>
                </div>
                <div class="info-item">
                    <span class="info-label">CNIC</span>
                    <span class="info-value" id="soldUnitUserCnic">-</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Phone Number</span>
                    <span class="info-value" id="soldUnitUserPhoneNo">-</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Purchase Date</span>
                    <span class="info-value" id="soldUnitContractDate">-</span>
                </div>
                <div class="info-item highlight">
                    <span class="info-label">Contract Price</span>
                    <span class="info-value" id="soldUnitContractPrice">-</span>
                </div>
            </div>
        </div>
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
                                <th>Department</th>
                                <th>User</th>
                                <th>Description</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <!-- Will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <div class="chart-header">
                        <h3>Maintenance History</h3>
                        <div class="chart-value" id="unitMaintenanceValue">Loading...</div>
                    </div>
                    <div class="chart-canvas-container">
                        <canvas id="unitMaintenanceChart"></canvas>
                    </div>
                    <div class="status-grid">
                        <div class="status-card">
                            <div class="status-count" style="color: var(--warning);">0</div>
                            <div class="status-label">Opened</div>
                        </div>
                        <div class="status-card">
                            <div class="status-count" style="color: var(--success);">0</div>
                            <div class="status-label">Closed</div>
                        </div>
                        <div class="status-card">
                            <div class="status-count" style="color: var(--danger);">0</div>
                            <div class="status-label">Rejected</div>
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
            <div class="table-responsive px-1">
                <table id="unitTransactionsTable" class="transactions-table ">
                    <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Status</th>
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

@push('scripts')
    <script>
        // Initialize charts
        let unitIncomeExpenseChart, unitIncomeSourcesChart, unitExpenseCategoriesChart, unitMaintenanceChart;



        function updateRentalContract(data) {
            document.querySelectorAll('.contract-section').forEach(section => {
                section.style.display = 'none';
            });
            const badge = document.getElementById('contractTypeBadge');
            document.querySelector('.rental-contract').style.display = 'block';
            badge.textContent = 'Rental';
            badge.style.background = '#e8f4fd';
            badge.style.color = '#1a73c0';

            const unit = data.Unit;
            const userUnit = data.Unit.user_units.length > 0 ? unit.user_units[0] : null;
            const user = userUnit ? userUnit.user : null;

            document.getElementById("rentalUnitType").textContent = data.Unit.unit_type || 'N/A';
            document.getElementById("rentalUnitName").textContent = data.Unit.unit_name || 'N/A';
            document.getElementById("rentalUnitUserName").textContent = user.name || 'N/A';
            document.getElementById("rentalUnitUserEmail").textContent = user.email;
            document.getElementById("rentalUnitUserCnic").textContent = user.cnic;
            document.getElementById("rentalUnitUserPhoneNo").textContent = user.phone_no;
            document.getElementById("rentalUnitStartDate").textContent = userUnit.subscription ? new Date(userUnit.subscription.created_at).toLocaleDateString() : 'N/A';
            document.getElementById("rentalUnitEndDate").textContent = userUnit.subscription ? new Date(userUnit.subscription.ends_at).toLocaleDateString() : 'N/A';
            document.getElementById("rentalUnitContractPrice").textContent = data.Unit.price;
        }

        function updateSaleContract(data) {
            document.querySelectorAll('.contract-section').forEach(section => {
                section.style.display = 'none';
            });
            const badge = document.getElementById('contractTypeBadge');
            document.querySelector('.sale-contract').style.display = 'block';
            badge.textContent = 'Sale';
            badge.style.background = '#f1e8fd';
            badge.style.color = '#7b1fa2';

            const unit = data.Unit;
            const userUnit = data.Unit.user_units.length > 0 ? unit.user_units[0] : null;
            const user = userUnit ? userUnit.user : null;

            document.getElementById("soldUnitType").textContent = data.Unit.unit_type || 'N/A';
            document.getElementById("soldUnitName").textContent = data.Unit.unit_name || 'N/A';
            document.getElementById("soldUnitUserName").textContent = user.name || 'N/A';
            document.getElementById("soldUnitUserEmail").textContent = user.email;
            document.getElementById("soldUnitUserCnic").textContent = user.cnic;
            document.getElementById("soldUnitUserPhoneNo").textContent = user.phone_no;
            document.getElementById("soldUnitContractDate").textContent = userUnit ? new Date(userUnit.created_at).toLocaleDateString() : 'N/A';
            document.getElementById("soldUnitContractPrice").textContent = data.Unit.price;
        }


        function fetchUnitDetails() {
            return fetch(`{{ route('owner.reports.units.details', ':id') }}`.replace(':id', currentUnit) , {
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
                    // Basic unit information
                    document.getElementById('unitName').textContent = data.Unit.unit_name || 'N/A';
                    document.getElementById('unitBuilding').textContent = data.Unit.building?.name || 'N/A';
                    document.getElementById('unitFloor').textContent = data.Unit.level?.level_name || 'N/A';
                    document.getElementById('unitType').textContent = data.Unit.unit_type || 'N/A';
                    document.getElementById('unitMarketValue').textContent = data.Unit.price || 'N/A';
                    document.getElementById('unitSize').textContent = data.Unit.area || 'N/A';
                    document.getElementById('unitImage').src = data.Unit.pictures.length > 0 ? '/' + data.Unit.pictures[0].file_path : 'default-image.jpg';
                    // Set unit description
                    const descriptionElement = document.getElementById('unitDescription');
                    if (data.Unit.description) {
                        descriptionElement.innerHTML = `<p>${data.Unit.description}</p>`;
                    } else {
                        descriptionElement.innerHTML = '';
                    }

                    // Handle status display
                    const statusBadge = document.getElementById('unitStatusBadge');
                    const statusDetails = document.getElementById('unitStatusDetails');
                    const status = data.Unit.availability_status || 'Available';

                    // Set status badge and details based on current status
                    switch(status.toLowerCase()) {
                        case 'rented':
                            statusBadge.className = 'badge badge-success';
                            statusBadge.textContent = 'Rented';
                            updateRentalContract(data)
                            break;

                        case 'sold':
                            statusBadge.className = 'badge badge-primary';
                            statusBadge.textContent = 'Sold';
                            updateSaleContract(data)
                            break;

                        default: // Available
                            statusBadge.className = 'badge badge-warning';
                            statusBadge.textContent = 'Available';
                            statusDetails.textContent = '';
                            break;
                    }

                });
        }

        // API 2: Fetch income/expense data
        function fetchUnitIncomeExpenseData() {
            return fetch(`{{ route('owner.reports.buildings.finance') }}?building=${currentBuilding}&unit=${currentUnit}&start=${currentStartDate}&end=${currentEndDate}`, {
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
                    document.getElementById('unitProfitValue').textContent = 'PKR ' + data.overview.net_profit.toLocaleString() + ' Net Profit';
                    document.getElementById('unitSummaryIncome').textContent = 'PKR ' + (data.overview.income || 0).toLocaleString();
                    document.getElementById('unitSummaryExpenses').textContent = 'PKR ' + (data.overview.expense || 0).toLocaleString();
                    document.getElementById('unitSummaryProfit').textContent = 'PKR ' + data.overview.net_profit.toLocaleString();
                    document.getElementById('unitSummaryMargin').textContent = data.overview.profit_margin.toFixed(1) + '%';

                    updateUnitGrowthProgress(data);

                    // Render income vs expense chart
                    renderUnitIncomeExpenseChart(data.overview.income || 0, data.overview.expense || 0);

                    if (data.recent_transactions) {
                        initUnitTransactionsTable(data.recent_transactions);
                    }
                });
        }

        // Income vs Expense Pie Chart
        function renderUnitIncomeExpenseChart(income, expenses) {
            const ctx = document.getElementById('unitIncomeExpenseChart');
            if (!ctx) return;

            if (unitIncomeExpenseChart) unitIncomeExpenseChart.destroy();

            unitIncomeExpenseChart = new Chart(ctx, {
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
                                    return `${context.label}: PKR ${context.raw.toLocaleString()}`;
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
                                size: 16
                            }
                        }
                    },
                    cutout: '65%'
                },
                plugins: [ChartDataLabels]
            });
        }

        // Initialize DataTable for transactions
        function initUnitTransactionsTable(data) {
            if ($.fn.DataTable.isDataTable('#unitTransactionsTable')) {
                $('#unitTransactionsTable').DataTable().destroy();
            }

            var table = $('#unitTransactionsTable').DataTable({
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
                        data: 'source',
                        render: function(data, type, row) {
                            return `<span>${data || 'N/A'}</span>`;
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
                            return `<span class="transaction-amount ${amountClass}">${sign} $${data}</span>`;
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

        // Maintenance Chart (with null checks for date elements)
        // Add this function to fetch maintenance data
        function fetchUnitMaintenanceData() {
            return fetch(`{{ route('owner.reports.units.maintenance') }}?building=${currentBuilding}&unit=${currentUnit}&start=${currentStartDate}&end=${currentEndDate}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data) {
                        throw new Error('No data returned from API');
                    }
                    renderUnitMaintenanceChart(data);
                    populateMaintenanceTable(data.requests || []);
                    updateStatusCounts(data.status_counts || { closed: 0, opened: 0, rejected: 0 });
                })
                .catch(error => {
                    console.error('Error loading maintenance data:', error);
                    // Render empty chart with zero values
                    renderUnitMaintenanceChart({
                        time_labels: [],
                        chart_data: []
                    });
                    populateMaintenanceTable([]);
                    updateStatusCounts({ closed: 0, opened: 0, rejected: 0 });
                });
        }

        // Update the renderMaintenanceChart function
        function renderUnitMaintenanceChart(data) {
            const ctx = document.getElementById('unitMaintenanceChart');
            if (!ctx) return;

            // Destroy existing chart if it exists
            if (unitMaintenanceChart) unitMaintenanceChart.destroy();

            // Check if data is valid
            if (!data || !data.time_labels || !data.chart_data) {
                console.error('Invalid maintenance chart data:', data);
                return;
            }

            unitMaintenanceChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.time_labels,
                    datasets: [
                        {
                            label: 'Maintenance Requests',
                            data: data.chart_data,
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

        // Update the populateMaintenanceTable function
        function populateMaintenanceTable(requests) {
            const tableBody = document.querySelector('.maintenance-section .data-table tbody');
            tableBody.innerHTML = '';

            requests.forEach(request => {
                const row = document.createElement('tr');

                // Determine badge class based on status
                let badgeClass = 'badge-primary';
                if (request.status === 'opened') badgeClass = 'badge-warning';
                if (request.status === 'rejected') badgeClass = 'badge-danger';
                if (request.status === 'closed') badgeClass = 'badge-success';

                // Format status text
                let statusText = request.status.charAt(0).toUpperCase() + request.status.slice(1);

                row.innerHTML = `
            <td>${request.department}</td>
            <td>${request.user}</td>
            <td>${request.description}</td>
            <td>${request.formatted_date}</td>
            <td><span class="badge ${badgeClass}">${statusText}</span></td>
        `;

                tableBody.appendChild(row);
            });
        }

        // Update the updateStatusCounts function
        function updateStatusCounts(counts) {
            document.querySelector('.status-card:nth-child(1) .status-count').textContent = counts.closed;
            document.querySelector('.status-card:nth-child(1) .status-label').textContent = 'Closed';

            document.querySelector('.status-card:nth-child(2) .status-count').textContent = counts.opened;
            document.querySelector('.status-card:nth-child(2) .status-label').textContent = 'Opened';

            document.querySelector('.status-card:nth-child(3) .status-count').textContent = counts.rejected;
            document.querySelector('.status-card:nth-child(3) .status-label').textContent = 'Rejected';

            document.getElementById('unitMaintenanceValue').textContent =
                `${counts.opened + counts.closed + counts.rejected} Requests (Last 12 months)`;
        }

        // Initialize unit charts when the component is loaded
        function initUnitCharts() {
            // Initialize empty charts
            renderUnitIncomeExpenseChart(0, 0);
            initUnitTransactionsTable([]);
            renderUnitMaintenanceChart({ time_labels: [], chart_data: [] });
            populateMaintenanceTable([]);
            updateStatusCounts({ closed: 0, opened: 0, rejected: 0 });
        }

        // Call this when the unit reports section is shown
        document.addEventListener('DOMContentLoaded', function() {
            // Check if we're on the unit reports page
            if (document.getElementById('UnitReports')) {
                initUnitCharts();
            }
        });

        function updateUnitGrowthProgress(data) {
            const incomeValueEl = document.querySelector('.unit-progress-item:nth-child(1) .unit-progress-value');
            const incomeFillEl = document.querySelector('.unit-progress-item:nth-child(1) .unit-progress-fill');
            const incomeGrowth = data.growth.income;

            incomeValueEl.textContent = (incomeGrowth > 0 ? '+' : '') + incomeGrowth.toFixed(1) + '%';
            incomeFillEl.style.width = Math.min(Math.abs(incomeGrowth), 100) + '%';

            if (incomeGrowth >= 0) {
                incomeValueEl.classList.add('positive');
                incomeValueEl.classList.remove('negative');
                incomeFillEl.style.backgroundColor = 'var(--primary)';
            } else {
                incomeValueEl.classList.add('negative');
                incomeValueEl.classList.remove('positive');
                incomeFillEl.style.backgroundColor = 'var(--danger)';
            }

            const expenseValueEl = document.querySelector('.unit-progress-item:nth-child(2) .unit-progress-value');
            const expenseFillEl = document.querySelector('.unit-progress-item:nth-child(2) .unit-progress-fill');
            const expenseGrowth = data.growth.expense;

            expenseValueEl.textContent = (expenseGrowth > 0 ? '+' : '') + expenseGrowth.toFixed(1) + '%';
            expenseFillEl.style.width = Math.min(Math.abs(expenseGrowth), 100) + '%';

            if (expenseGrowth <= 0) {
                expenseValueEl.classList.add('positive');
                expenseValueEl.classList.remove('negative');
                expenseFillEl.style.backgroundColor = 'var(--primary)';
            } else {
                expenseValueEl.classList.add('negative');
                expenseValueEl.classList.remove('positive');
                expenseFillEl.style.backgroundColor = 'var(--danger)';
            }
        }
    </script>
@endpush
