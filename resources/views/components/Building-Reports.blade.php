@push('styles')
    <style>
        /* ================================================ */
        /* ************ Buildings Reports ***************** */
        /* ================================================ */

        #BuildingReports{
            display: none;
        }

        /* Unit Details Section */
        .building-details-container, .unit-details-container {
            display: flex;
            gap: 25px;
            margin-bottom: 30px;
        }

        .building-image-container, .unit-image-container {
            flex: 0 0 250px;
        }

        .building-image, .unit-image {
            width: 100%;
            height: 250px;
            border-radius: 12px;
            object-fit: cover;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .building-info, .unit-info {
            flex: 1;
        }

        .building-info h3 {
            font-size: 22px;
            color: var(--sidenavbar-text-color);
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f0f0f0;
        }
        .unit-info h3 {
             font-size: 22px;
             color: var(--sidenavbar-text-color);
             margin-bottom: 15px;
             padding-bottom: 10px;
             border-bottom: 1px solid #f0f0f0;
         }

        .building-info-grid, .unit-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
        }

        .info-label {
            font-weight: 600 !important;
            color: var(--sidenavbar-text-color)!important;
            min-width: 120px!important;
        }

        .info-value {
            color: var(--sidenavbar-text-color)!important;
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

        .progress-item, .unit-progress-item {
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

        .progress-value, .unit-progress-value {
            font-size: 13px;
            font-weight: 600;
        }

        .progress-bar {
            height: 6px;
            background-color: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill, .unit-progress-fill {
            height: 100%;
            border-radius: 3px;
        }

        /* ================ */
        /* Transactions Table */
        /* ================ */
        .transaction-section {
            background-color: var(--sidenavbar-body-color);
            margin-bottom: 2.5rem;
            padding: 10px !important;
        }

        .transactions-table-container, .maintenance-table-container {
            background: var(--body-background-color);
            border-radius: 12px;
            padding: 10px !important;
            margin: 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .transactions-table {
            width: 100% !important;
            min-width: 100%;
            margin: 0;
            border-collapse: collapse;
        }

        .transactions-table thead {
            background-color: var(--sidenavbar-body-color) !important;
        }

        .transactions-table th {
            padding: 0.75rem 1rem;
            text-align: left;
            color: var(--sidenavbar-text-color);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .transactions-table td {
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.875rem;
            padding: 0.75rem 1rem;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding: 0;
            margin: 0;
        }

        .transactions-table {
            width: 100%;
            min-width: 800px; /* Prevents squishing on small screens */
            border-collapse: collapse;
        }


        .transaction-id {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            background: var(--sidenavbar-body-color);
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .transaction-title {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            background: var(--sidenavbar-body-color);
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
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
        /* Responsive Styles */
        /* ================ */
        @media (max-width: 1200px) {
            .metrics-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .chart-summary-row {
                flex-direction: column;
            }
            .building-info-grid, .unit-info-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .metrics-grid {
                grid-template-columns: 1fr;
            }

            .status-grid {
                grid-template-columns: 1fr;
            }
            .building-details-container, .unit-details-container {
                flex-direction: column;
            }
            .building-image-container, .unit-image-container {
                flex: 1;
                max-width: 100%;
            }
        }

        @media (max-width: 600px) {
        }

        @media (max-width: 480px) {
        }
    </style>
@endpush


<div class="BuildingReports" id="BuildingReports">

    <!-- Unit Details -->
    <div class="building-details-container building-Unit-details-container">
        <div class="building-image-container">
            <img id="buildingImage" src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Building Image" class="building-image">
        </div>
        <div class="building-info">
            <h3 id="buildingName">Bahria Prime Details</h3>
            <div class="building-info-grid">
                <div class="info-item">
                    <span class="info-label">Building Type:</span>
                    <span class="info-value" id="buildingType">Residential</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status:</span>
                    <span class="info-value"><span class="badge badge-success" id="buildingStatus">Approved</span></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Year Built:</span>
                    <span class="info-value" id="constructionYear">2012</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Area:</span>
                    <span class="info-value" id="buildingArea">100000.00 sq.f</span>
                </div>
                <div class="info-item">
                    <span class="info-label">City:</span>
                    <span class="info-value" id="buildingCity">Islamabad</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="metrics-grid">
        <div class="metric-card bg-gradient-primary">
            <h4>Occupancy Rate</h4>
            <div class="value" id="occupancyRateMetric">0.00%</div>
        </div>
        <div class="metric-card bg-gradient-success">
            <h4>Memberships Renewal Rate</h4>
            <div class="value" id="membershipsMetric">0.00%</div>
        </div>
        <div class="metric-card bg-gradient-info">
            <h4>Hired Staff</h4>
            <div class="value" id="staffMetric">0</div>
        </div>
        <div class="metric-card bg-gradient-warning">
            <h4>Maintenance Requests</h4>
            <div class="value" id="requestsMetric">0</div>
        </div>
    </div>

    <!-- Income vs Expense Pie Chart with Summary -->
    <div class="chart-summary-row">
        <div class="chart-container">
            <div class="chart-header">
                <h3>Income vs Expenses</h3>
                <div class="chart-value" id="profitValue">PKR 0.00 Net Profit</div>
            </div>
            <div class="chart-canvas-container">
                <canvas id="incomeExpenseChart"></canvas>
            </div>
        </div>
        <div class="summary-container">
            <h3 class="summary-title">Financial Summary</h3>
            <div class="summary-item">
                <span class="summary-label"><i class='bx bx-dollar-circle me-2'></i> Total Income</span>
                <span class="summary-value" id="summaryIncome">PKR 0.00</span>
            </div>
            <div class="summary-item">
                <span class="summary-label"><i class='bx bx-money-withdraw me-2'></i> Total Expenses</span>
                <span class="summary-value" id="summaryExpenses">PKR 0.00</span>
            </div>
            <div class="summary-item">
                <span class="summary-label"><i class='bx bx-trending-up me-2'></i> Net Profit</span>
                <span class="summary-value positive" id="summaryProfit">PKR 0.00</span>
            </div>
            <div class="summary-item">
                <span class="summary-label"><i class='bx bx-line-chart me-2'></i> Profit Margin</span>
                <span class="summary-value positive" id="summaryMargin">0%</span>
            </div>
            <div class="progress-container">
                <div class="progress-item">
                    <div class="progress-header">
                        <span class="progress-label">Income Growth</span>
                        <span class="progress-value positive">+0%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 0%; background-color: var(--primary);"></div>
                    </div>
                </div>
                <div class="progress-item">
                    <div class="progress-header">
                        <span class="progress-label">Expense Growth</span>
                        <span class="progress-value negative">+0%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 0%; background-color: var(--danger);"></div>
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
                <div class="chart-value" id="incomeSourcesValue">0 Sources</div>
            </div>
            <div class="chart-canvas-container">
                <canvas id="incomeSourcesChart"></canvas>
            </div>
        </div>
        <div class="chart-container">
            <div class="chart-header">
                <h3>Expense Sources</h3>
                <div class="chart-value" id="expenseCategoriesValue">0 Sources</div>
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
                <span class="summary-label"><i class='bx bx-plus-circle me-2'></i> New Members</span>
                <span class="summary-value positive" id="summaryNewMembers">+0</span>
            </div>
            <div class="progress-container">
                <div class="progress-item" id="renewalRateItem">
                    <div class="progress-header">
                        <span class="progress-label">Renewal Rate</span>
                        <span class="progress-value positive" id="renewalRateValue">+0%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" id="renewalRateBar" style="width: 0%; background-color: var(--primary);"></div>
                    </div>
                </div>
                <div class="progress-item" id="churnRateItem">
                    <div class="progress-header">
                        <span class="progress-label">Churn Rate</span>
                        <span class="progress-value negative" id="churnRateValue">+0%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" id="churnRateBar" style="width: 0%; background-color: var(--danger);"></div>
                    </div>
                </div>
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
                <div class="status-count" style="color: #FFCD56;" id="pendingRequests">0</div>
                <div class="status-label">Opened</div>
            </div>
            <div class="status-card">
                <div class="status-count" style="color: #4BC0C0;" id="completedRequests">0</div>
                <div class="status-label">Completed</div>
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
            <div class="table-responsive px-1">
                <table id="transactionsTable" class="transactions-table ">
                    <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Title</th>
                        <th>Unit</th>
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
        // Chart instances
        let incomeExpenseChart, incomeSourcesChart, expenseCategoriesChart,
            occupancyChart, staffChart, membershipChart, maintenanceChart;

        // API 1: For Building Details
        function fetchBuildingDetails() {
            fetch( `{{ route('owner.reports.building.details') }}?building=${currentBuilding}` , {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(res => {
                    if (!res.ok) throw new Error('Network response was not ok');
                    return res.json();
                })
                .then(data => {
                    const img = document.getElementById('buildingImage');

                    if (img && data.image) {
                        img.src = '{{ asset('') }}' + data.image;
                        img.alt = data.name + ' Image';
                    } else {
                        img.src = 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80';
                        img.alt = 'Default Building Image';
                    }

                    document.getElementById('buildingName').textContent = (data.name || 'N/A') + ' Details';
                    document.getElementById('buildingType').textContent = data.building_type || 'N/A';
                    document.getElementById('constructionYear').textContent = data.construction_year || 'N/A';
                    document.getElementById('buildingArea').textContent = (parseFloat(data.area) || 0).toLocaleString() + ' sq.f';
                    document.getElementById('buildingCity').textContent = data.city || 'N/A';

                    const statusEl = document.getElementById('buildingStatus');
                    const status = data.status || 'N/A';
                    statusEl.textContent = status;
                    statusEl.className = 'badge ' + (
                        status === 'Approved' || status === 'For Re-Approval' ? 'badge-success' :
                            status === 'Under Review' || status === 'Under Processing' ? 'badge-warning' :
                                status === 'Rejected' ? 'badge-danger' : 'badge-secondary'
                    );
                })
        }

        // API 2: Fetch income/expense data (Income vs Expense, Financial Summary, Income Sources, Expense Categories, Recent Transactions)
        function fetchIncomeExpenseData() {
            return fetch(`{{ route('owner.reports.buildings.finance') }}?building=${currentBuilding}&start=${currentStartDate}&end=${currentEndDate}`, {
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
                    document.getElementById('profitValue').textContent = 'PKR ' + data.overview.net_profit.toLocaleString() + ' Net Profit';
                    document.getElementById('summaryIncome').textContent = 'PKR ' + (data.overview.income || 0).toLocaleString();
                    document.getElementById('summaryExpenses').textContent = 'PKR ' + (data.overview.expense || 0).toLocaleString();
                    document.getElementById('summaryProfit').textContent = 'PKR ' + data.overview.net_profit.toLocaleString();
                    document.getElementById('summaryMargin').textContent = data.overview.profit_margin.toFixed(1) + '%';

                    updateGrowthProgress(data);

                    renderIncomeExpenseChart(data.overview.income || 0, data.overview.expense || 0);

                    // Render Income sources chart if data exists
                    if (data.income_sources && data.income_sources.labels && data.income_sources.data) {
                        renderIncomeSourcesChart(data.income_sources);
                        document.getElementById('incomeSourcesValue').textContent = data.income_sources.labels.length + ' Sources';
                    }

                    // Render expense categories chart if data exists
                    if (data.expense_sources && data.expense_sources.labels && data.expense_sources.data) {
                        renderExpenseCategoriesChart(data.expense_sources);
                        document.getElementById('expenseCategoriesValue').textContent = data.expense_sources.labels.length + ' Sources';
                    }

                    // Fill the recent transactions if the data exists
                    if (data.recent_transactions) {
                        initTransactionsTable(data.recent_transactions);
                    }
                });
        }

        // API 3: Fetch occupancy data
        function fetchOccupancyData() {
            return fetch(`{{ route('owner.reports.buildings.occupancy') }}?building=${currentBuilding}&start=${currentStartDate}&end=${currentEndDate}`, {
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
                    document.getElementById('occupancyRateMetric').textContent = (data.occupancyRate || 0) + '%';
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
                    document.getElementById('staffMetric').textContent = data.total_staff || 0;
                    document.getElementById('totalStaff').textContent = (data.total_staff || 0) + ' Employees';

                    const staffSummaryContainer = document.getElementById('staffDistributionSummary');
                    staffSummaryContainer.innerHTML = '';

                    if (data.labels && data.data) {

                        const staff_by_department = {
                            labels: data.labels,
                            data: data.data
                        };

                        renderStaffChart(staff_by_department);

                        staff_by_department.labels.forEach((label, index) => {
                            const summaryItem = document.createElement('div');
                            summaryItem.className = 'summary-item';

                            const summaryLabel = document.createElement('span');
                            summaryLabel.className = 'summary-label';
                            summaryLabel.innerHTML = `${label}`;

                            const summaryValue = document.createElement('span');
                            summaryValue.className = 'summary-value';
                            summaryValue.textContent = staff_by_department.data[index] || 0;

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
                    document.getElementById('membershipsMetric').textContent = `${data.renewal_rate || 0}%`;
                    document.getElementById('activeMembers').textContent = (data.active_members || 0) + ' Active';
                    document.getElementById('summaryTotalMembers').textContent = data.total_users || 0;
                    document.getElementById('summaryActiveMembers').textContent = data.active_members || 0;
                    document.getElementById('summaryExpiredMembers').textContent = data.expired_members || 0;
                    document.getElementById('summaryNewMembers').textContent = '+' + (data.new_members || 0);

                    document.getElementById('renewalRateValue').textContent = `+${data.renewal_rate || 0}%`;
                    document.getElementById('renewalRateBar').style.width = `${data.renewal_rate || 0}%`;

                    document.getElementById('churnRateValue').textContent = `+${data.churn_rate || 0}%`;
                    document.getElementById('churnRateBar').style.width = `${data.churn_rate || 0}%`;

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
                    const opened = data.opened_requests || 0;
                    const completed = data.completed_requests || 0;
                    const rejected = data.rejected_requests || 0;


                    document.getElementById('requestsMetric').textContent = opened;
                    document.getElementById('totalRequests').textContent = opened + ' Requests';
                    document.getElementById('completedRequests').textContent = completed;
                    document.getElementById('pendingRequests').textContent = opened;
                    document.getElementById('rejectedRequests').textContent = rejected;

                    // Render maintenance chart if data exists
                    if (data.maintenance_trend && data.maintenance_trend.labels) {
                        renderMaintenanceChart(data.maintenance_trend);
                    }
                });
        }

        // Initialize DataTable for transactions
        function initTransactionsTable(data) {
            if ($.fn.DataTable.isDataTable('#transactionsTable')) {
                $('#transactionsTable').DataTable().destroy();
            }

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
                    { data: 'id', className: '' },
                    { data: 'title', className: '' },
                    {
                        data: 'unit',
                        render: function(data, type, row) {
                            return `<span class="transaction-unit">${data || 'N/A'}</span>`;
                        }
                    },
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
                                    return `PKR ${context.parsed.y.toLocaleString()}`;
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
                                    return 'PKR ' + value.toLocaleString();
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
                                    return `PKR ${context.parsed.y.toLocaleString()}`;
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
                                    return 'PKR ' + value.toLocaleString();
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
                                display: false,
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
                                size: 16
                            }
                        }
                    },
                    cutout: '65%'
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

        function updateGrowthBar(growthValue, barSelector, reverseColor = false) {
            const progressBar = document.querySelector(barSelector);
            if (!progressBar) return;

            const width = Math.min(Math.abs(growthValue ?? 0), 100);
            progressBar.style.width = `${width}%`;

            const isPositive = growthValue >= 0;
            progressBar.style.backgroundColor = reverseColor
                ? (isPositive ? '#e57373' : 'white')
                : (isPositive ? 'white' : '#e57373');
        }

        function updateGrowthProgress(data) {
            const incomeValueEl = document.querySelector('.progress-item:nth-child(1) .progress-value');
            const incomeFillEl = document.querySelector('.progress-item:nth-child(1) .progress-fill');
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

            const expenseValueEl = document.querySelector('.progress-item:nth-child(2) .progress-value');
            const expenseFillEl = document.querySelector('.progress-item:nth-child(2) .progress-fill');
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
