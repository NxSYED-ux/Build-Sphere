@push('styles')
    <style>
        /* ================================================ */
        /* ************ Buildings Reports ***************** */
        /* ================================================ */

        #BuildingReports{
            display: none;
        }

        /* Unit Details Section */
        #BuildingReports .building-details-container {
            display: flex;
            gap: 25px;
            margin-bottom: 30px;
        }

        #BuildingReports .building-image-container {
            flex: 0 0 250px;
        }

        #BuildingReports .building-image {
            width: 100%;
            height: auto;
            border-radius: 12px;
            object-fit: cover;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        #BuildingReports .building-info {
            flex: 1;
        }

        #BuildingReports .building-info h3 {
            font-size: 22px;
            color: var(--sidenavbar-text-color);
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f0f0f0;
        }

        #BuildingReports .building-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        #BuildingReports .info-item {
            display: flex;
            align-items: flex-start;
        }

        #BuildingReports .info-label {
            font-weight: 600;
            color: var(--sidenavbar-text-color);
            min-width: 120px;
        }

        #BuildingReports .info-value {
            color: var(--sidenavbar-text-color);
        }

        /* ================ */
        /* Charts & Metrics */
        /* ================ */
        #BuildingReports .chart-summary-row {
            display: flex;
            gap: 25px;
            margin-bottom: 25px;
        }

        #BuildingReports .chart-container {
            flex: 2;
            background: var(--sidenavbar-body-color);
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            padding: 20px;
            transition: transform 0.3s;
        }

        #BuildingReports .chart-container:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        #BuildingReports .full-width-chart {
            width: 100%;
            background: var(--sidenavbar-body-color);
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            padding: 20px;
            margin-bottom: 25px;
            transition: transform 0.3s;
        }

        #BuildingReports .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        #BuildingReports .chart-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--sidenavbar-text-color);
        }

        #BuildingReports .chart-header .chart-value {
            font-size: 16px;
            font-weight: 600;
            color: var(--primary);
        }

        #BuildingReports .chart-canvas-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        /* ================ */
        /* Metrics Grid */
        /* ================ */
        #BuildingReports .metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        #BuildingReports .metric-card {
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            padding: 20px;
            text-align: center;
            transition: transform 0.3s;
        }

        #BuildingReports .metric-card h4 {
            font-size: 14px;
            color: #ffff;
            margin-bottom: 10px;
            font-weight: 500;
        }

        #BuildingReports .metric-card .value {
            font-size: 24px;
            font-weight: 600;
            color: #ffff;
        }

        #BuildingReports .metric-card .trend {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 8px;
            font-size: 13px;
        }

        #BuildingReports .trend.up {
            color: var(--success);
        }

        #BuildingReports .trend.down {
            color: var(--danger);
        }

        /* ================ */
        /* Summary Section */
        /* ================ */
        #BuildingReports .summary-container {
            flex: 1;
            background: var(--sidenavbar-body-color);
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        #BuildingReports .summary-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--sidenavbar-text-color);
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f0f0f0;
        }

        #BuildingReports .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        #BuildingReports .summary-item:last-child {
            border-bottom: none;
        }

        #BuildingReports .summary-label {
            color: var(--sidenavbar-text-color);
            font-size: 14px;
            display: flex;
            align-items: center;
        }

        #BuildingReports .summary-value {
            font-weight: 600;
            color: var(--sidenavbar-text-color);
        }

        #BuildingReports .summary-value.positive {
            color: var(--success);
        }

        #BuildingReports .summary-value.negative {
            color: var(--danger);
        }

        /* ================ */
        /* Status Grid */
        /* ================ */
        #BuildingReports .status-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 15px;
        }

        #BuildingReports .status-card {
            background: var(--body-background-color);
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            transition: transform 0.3s;
        }

        #BuildingReports .status-count {
            font-weight: 600;
            font-size: 20px;
            margin-bottom: 5px;
        }

        #BuildingReports .status-label {
            font-size: 12px;
            color: var(--sidenavbar-text-color);
        }

        /* ================ */
        /* Progress Bars */
        /* ================ */
        #BuildingReports .progress-container {
            margin-top: auto;
            padding-top: 15px;
        }

        #BuildingReports .progress-item {
            margin-bottom: 15px;
        }

        #BuildingReports .progress-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        #BuildingReports .progress-label {
            font-size: 13px;
            color: var(--gray);
        }

        #BuildingReports .progress-value {
            font-size: 13px;
            font-weight: 600;
        }

        #BuildingReports .progress-bar {
            height: 6px;
            background-color: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
        }

        #BuildingReports .progress-fill {
            height: 100%;
            border-radius: 3px;
        }

        /* ================ */
        /* Transactions Table */
        /* ================ */
        #BuildingReports .transaction-section {
            background-color: var(--sidenavbar-body-color);
            margin-bottom: 2.5rem;
        }

        #BuildingReports .transactions-table-container {
            background: var(--body-background-color);
            border-radius: 12px;
            padding: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        #BuildingReports .transactions-table {
            width: 100%;
            border-collapse: collapse;
        }

        #BuildingReports .transactions-table thead {
            background-color: var(--sidenavbar-body-color) !important;
        }

        #BuildingReports .transactions-table th {
            padding: 1rem;
            text-align: left;
            color: var(--sidenavbar-text-color);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        #BuildingReports .transactions-table td {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.875rem;
        }

        #BuildingReports .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        #BuildingReports .transactions-table {
            width: 100%;
            min-width: 800px; /* Prevents squishing on small screens */
            border-collapse: collapse;
        }


        #BuildingReports .transaction-id {
            font-family: 'Courier New', monospace;
            color: var(--primary);
            font-weight: 500;
        }

        #BuildingReports .transaction-title {
            font-weight: 500;
            color: var(--dark);
        }

        #BuildingReports .transaction-unit {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            background: var(--sidenavbar-body-color);
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        #BuildingReports .transaction-type {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        #BuildingReports .type-income {
            background: rgba(6, 214, 160, 0.1);
            color: var(--success);
        }

        #BuildingReports .type-expense {
            background: rgba(255, 0, 110, 0.1);
            color: var(--danger);
        }

        #BuildingReports .transaction-amount {
            font-weight: 600;
        }

        #BuildingReports .amount-income {
            color: var(--success);
        }

        #BuildingReports .amount-expense {
            color: var(--danger);
        }

        /* ================ */
        /* Pagination */
        /* ================ */
        #BuildingReports .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
        }

        #BuildingReports .pagination-info {
            font-size: 0.875rem;
            color: var(--gray);
        }

        #BuildingReports .pagination-controls {
            display: flex;
            gap: 0.5rem;
        }

        #BuildingReports .pagination-btn {
            padding: 0.5rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background: white;
            color: var(--dark);
            cursor: pointer;
            transition: all 0.2s;
        }

        #BuildingReports .pagination-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }


        /* ================ */
        /* Responsive Styles */
        /* ================ */
        @media (max-width: 1200px) {
            #BuildingReports .metrics-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            #BuildingReports .chart-summary-row {
                flex-direction: column;
            }
            #BuildingReports .building-info-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            #BuildingReports .metrics-grid {
                grid-template-columns: 1fr;
            }

            #BuildingReports .status-grid {
                grid-template-columns: 1fr;
            }
            #BuildingReports .building-details-container {
                flex-direction: column;
            }
            #BuildingReports .building-image-container {
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
    <div class="building-details-container">
        <div class="building-image-container">
            <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Building Image" class="building-image">
        </div>
        <div class="building-info">
            <h3>Bahria Prime Details</h3>
            <div class="building-info-grid">
                <div class="info-item">
                    <span class="info-label">Building Type:</span>
                    <span class="info-value">Residential</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status:</span>
                    <span class="info-value"><span class="badge badge-success">Approved</span></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Year Built:</span>
                    <span class="info-value">2012</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Area:</span>
                    <span class="info-value">100000.00 sq.f</span>
                </div>
                <div class="info-item">
                    <span class="info-label">City:</span>
                    <span class="info-value">Islamabad</span>
                </div>
            </div>
        </div>
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
    <p class="section-description">
        The key metrics section provides a quick snapshot of your property's performance.
        You can see the total number of units and levels across all buildings, along with
        financial metrics showing income and expenses.
    </p>

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
                <span class="summary-label"><i class='bx bx-dollar-circle'></i> Total Income</span>
                <span class="summary-value" id="summaryIncome">$0</span>
            </div>
            <div class="summary-item">
                <span class="summary-label"><i class='bx bx-money-withdraw'></i> Total Expenses</span>
                <span class="summary-value" id="summaryExpenses">$0</span>
            </div>
            <div class="summary-item">
                <span class="summary-label"><i class='bx bx-trending-up'></i> Net Profit</span>
                <span class="summary-value positive" id="summaryProfit">$0</span>
            </div>
            <div class="summary-item">
                <span class="summary-label"><i class='bx bx-line-chart'></i> Profit Margin</span>
                <span class="summary-value positive" id="summaryMargin">0%</span>
            </div>
        </div>
    </div>
    <p class="section-description">
        The income vs expenses visualization helps you understand your property's financial health at a glance.
        The doughnut chart shows the proportion of income to expenses, while the financial summary provides
        detailed numbers.
    </p>

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

            // Chart instances
            let incomeExpenseChart, incomeSourcesChart, expenseCategoriesChart,
                occupancyChart, staffChart, membershipChart, maintenanceChart;



            // API 1: Fetch metrics data (Total Units, Total Levels, Total Income, Total Expenses)
            function fetchMetricsData() {
                return fetch(`{{ route('owner.reports.buildings.metrics') }}?building_id=${currentBuilding}&start_date=${currentStartDate}&end_date=${currentEndDate}`, {
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
                return fetch(`{{ route('owner.reports.buildings.income-expense') }}?building_id=${currentBuilding}&start_date=${currentStartDate}&end_date=${currentEndDate}`, {
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
                return fetch(`{{ route('owner.reports.buildings.occupancy') }}?building_id=${currentBuilding}&start_date=${currentStartDate}&end_date=${currentEndDate}`, {
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
                        // Update occupancy rate
                        const occupied = (data.rented_units || 0) + (data.sold_units || 0);
                        const total = data.total_units || 1; // Avoid division by zero
                        const occupancyRate = (occupied / total) * 100;

                        document.getElementById('occupancyRate').textContent = occupancyRate.toFixed(0) + '% Occupancy Rate';
                        document.getElementById('rentedUnits').textContent = data.rented_units || 0;
                        document.getElementById('soldUnits').textContent = data.sold_units || 0;
                        document.getElementById('availableUnits').textContent = data.available_units || 0;

                        // Render occupancy chart if data exists
                        if (data.occupancy_trend && data.occupancy_trend.labels) {
                            renderOccupancyChart(data.occupancy_trend);
                        }
                    });
            }

            // API 4: Fetch staff data
            function fetchStaffData() {
                return fetch(`{{ route('owner.reports.buildings.staff') }}?building_id=${currentBuilding}&start_date=${currentStartDate}&end_date=${currentEndDate}`, {
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
                                summaryLabel.innerHTML = `<i class='bx bx-group'></i> ${label}`;

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
                return fetch(`{{ route('owner.reports.buildings.memberships') }}?building_id=${currentBuilding}&start_date=${currentStartDate}&end_date=${currentEndDate}`, {
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
                return fetch(`{{ route('owner.reports.buildings.maintenance') }}?building_id=${currentBuilding}&start_date=${currentStartDate}&end_date=${currentEndDate}`, {
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
                            data: 'status',
                            render: function(data, type, row) {
                                let icon = '';
                                let text = data;

                                switch(data.toLowerCase()) {
                                    case 'completed':
                                        icon = '<i class="bx bx-check-circle"></i>';
                                        break;
                                    case 'pending':
                                        icon = '<i class="bx bx-time"></i>';
                                        break;
                                    case 'rejected':
                                        icon = '<i class="bx bx-x-circle"></i>';
                                        break;
                                    default:
                                        icon = '<i class="bx bx-question-mark"></i>';
                                }

                                return `<span class="transaction-status">${icon} ${text}</span>`;
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
                                backgroundColor: '#e0e0e0',
                                borderRadius: 4,
                                borderWidth: 0
                            },
                            {
                                label: 'Rented',
                                data: data.rented || [],
                                backgroundColor: '#184E83',
                                borderRadius: 4,
                                borderWidth: 0
                            },
                            {
                                label: 'Sold',
                                data: data.sold || [],
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

            function getTimePeriodLabel() {
                const startDate = new Date(currentStartDate);
                const endDate = new Date(currentEndDate);
                const diffTime = Math.abs(endDate - startDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                if (diffDays > 60) return 'Months';
                if (diffDays > 14) return 'Weeks';
                return 'Days';
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


    </script>
@endpush
