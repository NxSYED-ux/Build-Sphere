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

        #UnitReports .timeline-price {
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


        #unitMaintenanceTable th:nth-child(5),
        #unitMaintenanceTable td:nth-child(5) {
            min-width: 250px; /* Ensures the column doesn't get too narrow */
        }


        /* Responsive Adjustments */
        @media (max-width: 1200px) {
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

                <div class="info-item contract-user-info" style="display: none;">
                    <span class="info-label" id="contractUserNameLabel"></span>
                    <span class="info-value" id="contractUserName">-</span>
                </div>
                <div class="info-item contract-user-info" style="display: none;">
                    <span class="info-label" id="contractUserEmailLabel"></span>
                    <span class="info-value" id="contractUserEmail">-</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="metrics-grid">
        <div class="metric-card bg-gradient-primary">
            <h4>Total Income</h4>
            <div class="value" id="unitMatricTotalIncome">92%</div>
        </div>
        <div class="metric-card bg-gradient-info">
            <h4>Total Expense</h4>
            <div class="value" id="unitMatricTotalExpense">$24,500</div>
        </div>
        <div class="metric-card bg-gradient-success">
            <h4>Net Profit</h4>
            <div class="value" id="unitMatricNetProfit">$1,250</div>
        </div>
        <div class="metric-card bg-gradient-warning">
            <h4>Maintenance Requests</h4>
            <div class="value" id="unitMatricMaintenanceRequests">4</div>
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
        <h3>Last Contract of selected period: <span class="contract-type-badge" id="contractTypeBadge">Rental</span></h3>

        <!-- Rental Contract View -->
        <div class="contract-section rental-contract">
            <div class="contract-info-grid">
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

    <!-- Unit Status Timeline -->
    <div class="timeline-container">
        <h3>Unit Contract History</h3>
        <p>Review the historical record of rental and ownership contracts within the selected period, including tenant details and contract durations.</p>

        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-dot"><i class='bx bx-calendar'></i></div>
                <div class="timeline-content">
                    <div class="timeline-date">May 15 - May 31, 2023</div>
                    <div class="timeline-title">Rented to Ali</div>
                    <div class="timeline-price">
                        Price: PKR 23,000
                    </div>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot"><i class='bx bx-calendar'></i></div>
                <div class="timeline-content">
                    <div class="timeline-date">May 15 - May 31, 2023</div>
                    <div class="timeline-title">Rented to Ali</div>
                    <div class="timeline-price">
                        Price: PKR 23,000
                    </div>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot"><i class='bx bx-calendar'></i></div>
                <div class="timeline-content">
                    <div class="timeline-date">May 15 - May 31, 2023</div>
                    <div class="timeline-title">Rented to Ali</div>
                    <div class="timeline-price">
                        Price: PKR 23,000
                    </div>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot"><i class='bx bx-calendar'></i></div>
                <div class="timeline-content">
                    <div class="timeline-date">May 15 - May 31, 2023</div>
                    <div class="timeline-title">Rented to Ali</div>
                    <div class="timeline-price">
                        Price: PKR 23,000
                    </div>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot"><i class='bx bx-calendar'></i></div>
                <div class="timeline-content">
                    <div class="timeline-date">May 15 - May 31, 2023</div>
                    <div class="timeline-title">Rented to Ali</div>
                    <div class="timeline-price">
                        Price: PKR 23,000
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Maintenance Requests Chart (Full Width) -->
    <div class="full-width-chart">
        <div class="chart-header">
            <h3>Maintenance Requests</h3>
            <div class="chart-value" id="unitTotalRequests">0 Requests</div>
        </div>
        <div class="chart-canvas-container">
            <canvas id="unitMaintenanceChart"></canvas>
        </div>
        <div class="status-grid">
            <div class="status-card">
                <div class="status-count" style="color: #FFCD56;" id="unitPendingRequests">0</div>
                <div class="status-label">Opened</div>
            </div>
            <div class="status-card">
                <div class="status-count" style="color: #4BC0C0;" id="unitCompletedRequests">0</div>
                <div class="status-label">Completed</div>
            </div>
            <div class="status-card">
                <div class="status-count" style="color: #FF6384;" id="unitRejectedRequests">0</div>
                <div class="status-label">Rejected</div>
            </div>
        </div>
    </div>

    <!-- Unit Maintenace Request -->
    <div class="transaction-section p-3 pb-1 shadow rounded">
        <div class="section-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="section-title">Active Maintenance Requests</h2>
{{--                <p class="section-description">--}}
{{--                    Detailed view of all financial transactions including rental payments, service charges,--}}
{{--                    maintenance costs, and other income/expense items.--}}
{{--                </p>--}}
            </div>
            <div class="export-actions">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="maintenanceExportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class='bx bx-export me-1'></i>
                        Export
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="maintenanceExportDropdown">
                        <li><button class="dropdown-item" type="button" id="maintenanceCopyButton"><i class='bx bx-copy me-2'></i>Copy</button></li>
                        <li><button class="dropdown-item" type="button" id="maintenanceCsvButton"><i class='bx bx-file me-2'></i>CSV</button></li>
                        <li><button class="dropdown-item" type="button" id="maintenanceExcelButton"><i class='bx bx-spreadsheet me-2'></i>Excel</button></li>
                        <li><button class="dropdown-item" type="button" id="maintenancePdfButton"><i class='bx bxs-file-pdf me-2'></i>PDF</button></li>
                        <li><button class="dropdown-item" type="button" id="maintenancePrintButton"><i class='bx bx-printer me-2'></i>Print</button></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="maintenance-table-container">
            <div class="table-responsive px-1">
                <table id="unitMaintenanceTable" class="transactions-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Department</th>
                        <th>Staff</th>
                        <th>User</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- Data will be loaded by DataTables -->
                    </tbody>
                </table>
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
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="transactionsExportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class='bx bx-export me-1'></i>
                        Export
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="transactionsExportDropdown">
                        <li><button class="dropdown-item" type="button" id="transactionsCopyButton"><i class='bx bx-copy me-2'></i>Copy</button></li>
                        <li><button class="dropdown-item" type="button" id="transactionsCsvButton"><i class='bx bx-file me-2'></i>CSV</button></li>
                        <li><button class="dropdown-item" type="button" id="transactionsExcelButton"><i class='bx bx-spreadsheet me-2'></i>Excel</button></li>
                        <li><button class="dropdown-item" type="button" id="transactionsPdfButton"><i class='bx bxs-file-pdf me-2'></i>PDF</button></li>
                        <li><button class="dropdown-item" type="button" id="transactionsPrintButton"><i class='bx bx-printer me-2'></i>Print</button></li>
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
                        <th>Source</th>
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

@push('scripts')
    <script>
        // Initialize charts
        let unitIncomeExpenseChart, unitIncomeSourcesChart, unitExpenseCategoriesChart, unitMaintenanceChart;

        // API 1: Fetch unit details
        function fetchUnitDetails() {
            return fetch(`{{ route('owner.reports.units.details', ':id') }}`.replace(':id', currentUnit), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
                    descriptionElement.innerHTML = data.Unit.description ? `<p>${data.Unit.description}</p>` : '';

                    // Handle status display
                    const status = data.Unit.availability_status?.toLowerCase() || 'available';
                    const statusBadge = document.getElementById('unitStatusBadge');

                    switch(status) {
                        case 'rented':
                            statusBadge.className = 'badge badge-success';
                            statusBadge.textContent = 'Rented';
                            break;
                        case 'sold':
                            statusBadge.className = 'badge badge-primary';
                            statusBadge.textContent = 'Sold';
                            break;
                        default:
                            statusBadge.className = 'badge badge-warning';
                            statusBadge.textContent = 'Available';
                    }

                    // Update contract display (handles all cases)
                    updateContractDisplay(data);
                });
        }

        // API 2: Fetch income/expense data
        function fetchUnitIncomeExpenseData() {
            return fetch(`{{ route('owner.reports.units.finance') }}?building=${currentBuilding}&unit=${currentUnit}&start=${currentStartDate}&end=${currentEndDate}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
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


                    document.getElementById('unitMatricTotalIncome').textContent = 'PKR ' + (data.overview.income || 0).toLocaleString();
                    document.getElementById('unitMatricTotalExpense').textContent = 'PKR ' + (data.overview.expense || 0).toLocaleString();
                    document.getElementById('unitMatricNetProfit').textContent = 'PKR ' + data.overview.net_profit.toLocaleString();

                    updateUnitGrowthProgress(data);

                    // Render income vs expense chart
                    renderUnitIncomeExpenseChart(data.overview.income || 0, data.overview.expense || 0);

                    if (data.recent_transactions) {
                        initUnitTransactionsTable(data.recent_transactions);
                    }
                });
        }

        // API 3: Fetch contact History
        function fetchContractHistory() {
            return fetch(`{{ route('owner.reports.units.contacts') }}?building=${currentBuilding}&unit=${currentUnit}&start=${currentStartDate}&end=${currentEndDate}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {

                    // Update last contract details
                    updateLastContract(data.lastContract);

                    // Update contract timeline
                    updateContractTimeline(data.contracts);
                    console.log("API Response Data Contract History:", data);
                });
        }

        // API 4: Fetch maintenance data
        function fetchUnitMaintenanceData() {
            // First empty the table
            clearMaintenanceTable();

            return fetch(`{{ route('owner.reports.units.maintenance') }}?building=${currentBuilding}&unit=${currentUnit}&start=${currentStartDate}&end=${currentEndDate}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    console.log("Maintenance data: ", data);
                    const opened = data.opened_requests || 0;
                    const completed = data.completed_requests || 0;
                    const rejected = data.rejected_requests || 0;

                    document.getElementById('unitTotalRequests').textContent = opened + ' Requests';
                    document.getElementById('unitCompletedRequests').textContent = completed;
                    document.getElementById('unitPendingRequests').textContent = opened;
                    document.getElementById('unitRejectedRequests').textContent = rejected;
                    document.getElementById('unitMatricMaintenanceRequests').textContent = opened;

                    // Render maintenance chart if data exists
                    if (data.maintenance_trend && data.maintenance_trend.labels) {
                        renderUnitMaintenanceChart(data.maintenance_trend);
                    }

                    // Initialize or update table with new data
                    if (data.maintenanceData) {
                        initUnitMaintenanceTable(data.maintenanceData);
                    }
                })
                .catch(error => {
                    console.error('Error fetching maintenance data:', error);
                });
        }

        // Current Contract
        function updateContractDisplay(data) {
            const contractContainer = document.querySelector('.contract-details-container');
            const statusBadge = document.getElementById('unitStatusBadge');
            const userInfoItems = document.querySelectorAll('.contract-user-info');
            const contractBadge = document.getElementById('contractTypeBadge');

            // Hide everything by default
            contractContainer.style.display = 'none';
            document.querySelectorAll('.contract-section').forEach(section => {
                section.style.display = 'none';
            });
            userInfoItems.forEach(item => item.style.display = 'none');

            // If no data or unit isn't rented/sold, keep everything hidden
            if (!data || !data.Unit || !['rented', 'sold'].includes(data.Unit.availability_status?.toLowerCase())) {
                return;
            }

            // Get contract details
            const unit = data.Unit;
            const userUnit = unit.user_units?.[0];
            const user = userUnit?.user;
            const isRental = unit.availability_status.toLowerCase() === 'rented';

            // Show the appropriate contract section
            contractContainer.style.display = 'block';
            const activeContract = isRental ? document.querySelector('.rental-contract') : document.querySelector('.sale-contract');
            activeContract.style.display = 'block';

            // Update contract badge
            if (contractBadge) {
                contractBadge.textContent = isRental ? 'Rental' : 'Sale';
                contractBadge.style.background = isRental ? '#e8f4fd' : '#f1e8fd';
                contractBadge.style.color = isRental ? '#1a73c0' : '#7b1fa2';
            }

            // Update user info if available
            if (user) {
                userInfoItems.forEach(item => item.style.display = 'flex');
                document.getElementById('contractUserNameLabel').textContent = isRental ? 'Rental User:' : 'Buyer:';
                document.getElementById('contractUserEmailLabel').textContent = isRental ? 'Rental Email:' : 'Buyer Email:';
                document.getElementById('contractUserName').textContent = user.name || 'N/A';
                document.getElementById('contractUserEmail').textContent = user.email || 'N/A';
            }
        }

        // Last Contract of selected period
        function updateLastContract(data) {
            // Hide the entire contract container by default
            const contractContainer = document.querySelector('.contract-details-container');

            // If no data or invalid data structure, hide everything and return
            if (!data || !data.type || (!data.Unit && !data.user)) {
                if (contractContainer) {
                    contractContainer.style.display = 'none';
                }
                console.error("No valid contract data provided");
                return;
            }

            // Show the container since we have valid data
            if (contractContainer) {
                contractContainer.style.display = 'block';
            }

            // Rest of your existing code...
            // Hide all contract sections first
            document.querySelectorAll('.contract-section').forEach(section => {
                section.style.display = 'none';
            });

            const isRental = data.type === 'Rented';
            const user = data.user || null;
            const subscription = data.subscription || null;

            // Set up contract UI based on type
            const badge = document.getElementById('contractTypeBadge');
            const contractSection = isRental ? document.querySelector('.rental-contract') : document.querySelector('.sale-contract');

            if (contractSection) {
                contractSection.style.display = 'block';
            }

            if (badge) {
                if (isRental) {
                    badge.textContent = 'Rental';
                    badge.style.background = '#e8f4fd';
                    badge.style.color = '#1a73c0';
                } else {
                    badge.textContent = 'Sale';
                    badge.style.background = '#f1e8fd';
                    badge.style.color = '#7b1fa2';
                }
            }

            // Helper function to safely set text content
            function setTextContent(id, value) {
                const element = document.getElementById(id);
                if (element) {
                    element.textContent = value || 'N/A';
                }
            }

            // Update contract fields based on type
            if (isRental) {
                setTextContent("rentalUnitUserName", user?.name);
                setTextContent("rentalUnitUserEmail", user?.email);
                setTextContent("rentalUnitUserCnic", user?.cnic);
                setTextContent("rentalUnitUserPhoneNo", user?.phone_no);

                const startDate = subscription?.created_at ?
                    new Date(subscription.created_at).toLocaleDateString() : null;
                setTextContent("rentalUnitStartDate", startDate);

                const endDate = subscription?.ends_at ?
                    new Date(subscription.ends_at).toLocaleDateString() : null;
                setTextContent("rentalUnitEndDate", endDate);

                setTextContent("rentalUnitContractPrice", subscription.price_at_subscription);
            } else {
                setTextContent("soldUnitUserName", user?.name);
                setTextContent("soldUnitUserEmail", user?.email);
                setTextContent("soldUnitUserCnic", user?.cnic);
                setTextContent("soldUnitUserPhoneNo", user?.phone_no);

                const contractDate = data?.created_at ?
                    new Date(data.created_at).toLocaleDateString() : null;
                setTextContent("soldUnitContractDate", contractDate);

                setTextContent("soldUnitContractPrice", data.price);
            }
        }

        // Unit Contract History
        function updateContractTimeline(contracts) {
            const timelineContainer = document.querySelector('.timeline');
            timelineContainer.innerHTML = ''; // Clear existing items

            if (!contracts || contracts.length === 0) {
                const noContractsMsg = document.createElement('div');
                noContractsMsg.className = 'no-contracts';
                noContractsMsg.textContent = 'No contracts found for the selected period';
                timelineContainer.appendChild(noContractsMsg);
                return;
            }

            contracts.forEach(contract => {
                const timelineItem = document.createElement('div');
                timelineItem.className = 'timeline-item';

                timelineItem.innerHTML = `
                <div class="timeline-dot"><i class='bx bx-calendar'></i></div>
                <div class="timeline-content">
                    <div class="timeline-date">${contract.date}</div>
                    <div class="timeline-title">${contract.tittle}</div>
                    <div class="timeline-price">${contract.price}</div>
                </div>
            `;

                timelineContainer.appendChild(timelineItem);
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

        // Maintenance Chart
        function renderUnitMaintenanceChart(data) {
            const ctx = document.getElementById('unitMaintenanceChart');
            if (!ctx) return;

            if (maintenanceChart) maintenanceChart.destroy();

            maintenanceChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'Opened',
                            data: data.opened,
                            backgroundColor: '#FFCD56',
                            borderRadius: 4
                        },
                        {
                            label: 'Completed',
                            data: data.completed,
                            backgroundColor: '#4BC0C0',
                            borderRadius: 4
                        },
                        {
                            label: 'Rejected',
                            data: data.rejected,
                            backgroundColor: '#ff4d6d',
                            borderRadius: 4
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
                                    if (label) label += ': ';
                                    label += context.parsed.y;
                                    return label;
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
                            // Set this to true if you want the bars to stack
                            stacked: false
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        }

        // Maintenance Table
        function initUnitMaintenanceTable(data) {
            // Clear any existing table first
            if ($.fn.DataTable.isDataTable('#unitMaintenanceTable')) {
                $('#unitMaintenanceTable').DataTable().destroy();
            }

            // Initialize new DataTable
            var table = $('#unitMaintenanceTable').DataTable({
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
                    { data: 'id', className: '', width: '5%' },
                    { data: 'department', className: '', width: '10%' },
                    { data: 'staff', className: '', width: '10%' },
                    { data: 'user', className: '', width: '10%' },
                    { data: 'description', className: '', width: '40%' },
                    {
                        data: 'date', width: '10%',
                        render: function(data, type, row) {
                            const date = new Date(data);
                            return `<span class="transaction-date">${date.toLocaleDateString()}</span>`;
                        }
                    },
                    {
                        data: 'status', width: '15%',
                        render: function(data, type, row) {
                            // Determine badge class based on status
                            let badgeClass = 'badge-primary';
                            if (data === 'opened') badgeClass = 'badge-warning';
                            if (data === 'rejected') badgeClass = 'badge-danger';
                            if (data === 'closed') badgeClass = 'badge-success';

                            // Format status text
                            let statusText = data.charAt(0).toUpperCase() + data.slice(1);

                            return `<span class="badge ${badgeClass}">${statusText}</span>`;
                        }
                    },
                ],
                language: {
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    },
                    searchPlaceholder: "Search transactions..."
                },
                initComplete: function() {
                    // Set up export buttons after table initialization
                    document.getElementById("maintenanceCopyButton")?.addEventListener("click", function() {
                        table.button('.buttons-copy').trigger();
                    });
                    document.getElementById("maintenanceCsvButton")?.addEventListener("click", function() {
                        table.button('.buttons-csv').trigger();
                    });
                    document.getElementById("maintenanceExcelButton")?.addEventListener("click", function() {
                        table.button('.buttons-excel').trigger();
                    });
                    document.getElementById("maintenancePdfButton")?.addEventListener("click", function() {
                        table.button('.buttons-pdf').trigger();
                    });
                    document.getElementById("maintenancePrintButton")?.addEventListener("click", function() {
                        table.button('.buttons-print').trigger();
                    });
                }
            });
        }

        // Clear Maintenance Table
        function clearMaintenanceTable() {
            // Clear DataTable if it exists
            if ($.fn.DataTable.isDataTable('#unitMaintenanceTable')) {
                $('#unitMaintenanceTable').DataTable().destroy();
            }

            // Clear the table body
            $('#unitMaintenanceTable tbody').empty();

            // Reset summary fields
            document.getElementById('unitTotalRequests').textContent = '0 Requests';
            document.getElementById('unitCompletedRequests').textContent = '0';
            document.getElementById('unitPendingRequests').textContent = '0';
            document.getElementById('unitRejectedRequests').textContent = '0';
        }

        // Transactions Table
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
                    { data: 'id', className: '' },
                    { data: 'title', className: '' },
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

            document.getElementById("transactionsCopyButton")?.addEventListener("click", function() {
                table.button('.buttons-copy').trigger();
            });

            document.getElementById("transactionsCsvButton")?.addEventListener("click", function() {
                table.button('.buttons-csv').trigger();
            });

            document.getElementById("transactionsExcelButton")?.addEventListener("click", function() {
                table.button('.buttons-excel').trigger();
            });

            document.getElementById("transactionsPdfButton")?.addEventListener("click", function() {
                table.button('.buttons-pdf').trigger();
            });

            document.getElementById("transactionsPrintButton")?.addEventListener("click", function() {
                table.button('.buttons-print').trigger();
            });
        }

        // Initialize unit charts when the component is loaded
        function initUnitCharts() {
            renderUnitIncomeExpenseChart(0, 0);
            initUnitTransactionsTable([]);
            initUnitMaintenanceTable([]);
            renderUnitMaintenanceChart({ time_labels: [], chart_data: [] });
        }

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
