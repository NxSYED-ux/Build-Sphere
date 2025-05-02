@extends('layouts.app')

@section('title', 'Finance')

@push('styles')
    <style>
        #main {
            margin-top: 45px;
        }

        .finance-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
        }

        .finance-header h3 {
            font-weight: 700;
            color: var(--sidenavbar-text-color);
            font-size: 1.75rem;
            margin-bottom: 0.25rem;
        }

        .finance-header p {
            color: var(--sidenavbar-text-color);
            font-size: 0.95rem;
        }

        .finance-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            background: var(--sidenavbar-body-color);
            overflow: hidden;
        }

        .finance-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .summary-card {
            background: var(--body-card-bg);
            border-radius: 14px;
            padding: 1.75rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .summary-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        .summary-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(to bottom, #3b82f6, var(--color-blue));
        }

        .summary-card h5 {
            font-size: 0.875rem;
            color: var(--sidenavbar-text-color);
            margin-bottom: 0.75rem;
            font-weight: 500;
            letter-spacing: 0.2px;
        }

        .summary-card .amount {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--sidenavbar-text-color);
            margin-bottom: 0.5rem;
        }

        .summary-card .trend {
            display: flex;
            align-items: center;
            font-size: 0.8rem;
            color: #64748b;
        }

        .positive {
            color: #10b981 !important;
        }

        .negative {
            color: #ef4444 !important;
        }

        .filter-section {
            background:  var(--sidenavbar-body-color);
            border-radius: 14px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .filter-section label {
            font-weight: 500;
            color: var(--sidenavbar-text-color) !important;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        .custom-pagination-wrapper {
            justify-content: center;
        }

        .empty-state {
            text-align: center;
            padding: 4rem;
            color: var(--sidenavbar-text-color) !important;
            background:  var(--sidenavbar-body-color) !important;
            border-radius: 14px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            color: #e2e8f0;
        }

        .empty-state h4 {
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: var(--sidenavbar-text-color);
        }

        .empty-state p {
            max-width: 400px;
            margin: 0 auto 1.5rem;
        }

        .btn-primary {
            background-color: #2196F3;
            border-color: #2196F3;
            padding: 0.5rem 1.25rem;
            font-weight: 500;
            letter-spacing: 0.2px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #2196F3;
            border-color: #2196F3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .btn-outline-primary {
            color: #4f46e5;
            border-color: #4f46e5;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background-color: #4f46e5;
            border-color: #4f46e5;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
        }

        .form-select, .form-control {
            border-radius: 8px;
            padding: 0.5rem 1rem;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .form-select:focus, .form-control:focus {
            border-color: #a5b4fc;
            box-shadow: 0 0 0 3px rgba(199, 210, 254, 0.5);
        }

        .section-title {
            font-weight: 600;
            color: var(--sidenavbar-text-color) !important;
            margin-bottom: 1.5rem;
            font-size: 1.25rem;
            position: relative;
            padding-left: 1rem;
        }

        .section-title::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: #2196F3;
            border-radius: 4px;
        }

        @media (max-width: 768px) {
            .finance-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .summary-cards {
                grid-template-columns: 1fr 1fr;
            }
        }

        .chart-container {
            position: relative;
            width: 100%;
            min-height: 300px;
            margin-bottom: 2rem;
        }

        /* Ensure the canvas fills its container */
        #financialChart {
            width: 100% !important;
            height: 100% !important;
            min-height: 300px;
            max-height: 300px;
        }

        /* For smaller screens */
        @media (max-width: 768px) {
            .chart-container {
                min-height: 250px;
            }
            #financialChart {
                width: 100% !important;
                height: 100% !important;
                min-height: 350px;
                max-height: 350px;
            }
        }

        .days-select {
            width: 100%;
            margin-top: 0.5rem;
        }

        /* Medium screens and up: Fixed width (25%) */
        @media (min-width: 768px) {
            .days-select {
                width: 25%;
                margin-top: 0;
            }
        }
    </style>
@endpush

@section('content')

    <!-- Top Navbar -->
    <x-Admin.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('admin_dashboard'), 'label' => 'Dashboard'],
            ['url' => '', 'label' => 'Finance']
        ]"
    />

    <!-- Side Navbar -->
    <x-Admin.side-navbar :openSections="['Finance']" />
    <x-error-success-model />


    <div id="main">
        <section class="content mt-1 mb-5 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                            <div class="container mt-2">
                                <!-- Finance Header -->
                                <div class="finance-header">
                                    <div>
                                        <h3>Financial Dashboard</h3>
                                    </div>
                                    <form id="trendsFilterForm" method="GET" class="d-flex gap-2">
                                        <select class="form-select form-select-sm w-auto me-2" style="min-width: 100px;" name="year" id="yearSelect">
                                            @foreach(range(date('Y'), date('Y') - 5) as $y)
                                                <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                                            @endforeach
                                        </select>
                                        <select class="form-select form-select-sm w-auto me-2" style="min-width: 100px;" name="month" id="monthSelect">
                                            @foreach(range(1, 12) as $m)
                                                <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>
                                                    {{ date('M', mktime(0, 0, 0, $m, 1)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <span class="d-inline-block">Update Trends</span>
                                            <i class="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- Fixed Summary Cards -->
                                <div id="financialMetricsContainer" class="row g-2 mb-2">
                                    <div class="col-md-4">
                                        <div class="summary-card">
                                            <h5>Total Revenue</h5>
                                            <div class="amount" id="totalRevenue">PKR 0.00</div>
                                            <div class="trend" id="totalRevenueTrend">
                                                <i class="fas fa-arrow-up me-1 positive"></i>
                                                <span class="positive">0.00% from last month</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="summary-card">
                                            <h5>Total Expenses</h5>
                                            <div class="amount" id="totalExpenses">PKR 0.00</div>
                                            <div class="trend" id="totalExpensesTrend">
                                                <i class="fas fa-arrow-down me-1 positive"></i>
                                                <span class="positive">0.00% from last month</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="summary-card">
                                            <h5>Net Profit</h5>
                                            <div class="amount positive" id="netProfit">PKR 0.00</div>
                                            <div class="trend" id="netProfitTrend">
                                                <i class="fas fa-arrow-up me-1 positive"></i>
                                                <span class="positive">0.00% from last month</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Chart Section -->
                                <div class="finance-card p-4 mt-3 mb-3">
                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
                                        <h5 class="section-title mb-0 mb-md-0">Financial Overview</h5>
                                        <select class="form-select days-select"   id="daysSelect">
                                            <option value="30">Last 30 Days</option>
                                            <option value="90">Last 90 Days</option>
                                            <option value="custom" id="thisYearOption">This Year (Jan 1 - Today)</option>
                                        </select>
                                    </div>
                                    <div class="chart-container">
                                        <div class="card-body" style="position: relative; height: 100%; width: 100%;">
                                            <canvas id="financialChart"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Filter Section -->
                                <form method="GET" action="{{ route('finance.index') }}">
                                    <div class="filter-section mb-3">
                                        <h5 class="section-title mb-4">Transaction Filters</h5>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label">Date Range</label>
                                                <select name="date_range" class="form-select">
                                                    <option value="7" {{ request('date_range') == 7 ? 'selected' : '' }}>Last 7 days</option>
                                                    <option value="30" {{ request('date_range', 30) == 30 ? 'selected' : '' }}>Last 30 days</option>
                                                    <option value="90" {{ request('date_range') == 90 ? 'selected' : '' }}>Last 3 months</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Transaction Type</label>
                                                <select name="type" class="form-select">
                                                    <option value="">All Transactions</option>
                                                    <option value="Debit" {{ request('type') == 'Debit' ? 'selected' : '' }}>Debit</option>
                                                    <option value="Credit" {{ request('type') == 'Credit' ? 'selected' : '' }}>Credit</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Status</label>
                                                <select name="status" class="form-select">
                                                    <option value="">All Statuses</option>
                                                    <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                                                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="Failed" {{ request('status') == 'Failed' ? 'selected' : '' }}>Failed</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Min Amount (PKR)</label>
                                                <input type="number" name="min_price" class="form-control" placeholder="0" value="{{ request('min_price') }}" >
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Max Amount (PKR)</label>
                                                <input type="number" name="max_price" class="form-control" placeholder="100000" value="{{ request('max_price') }}">
                                            </div>

                                            <div class="col-md-4 d-flex align-items-end">
                                                <div class="d-flex w-100 justify-content-between gap-2">
                                                    <a href="{{ route('finance.index') }}" class="btn btn-secondary flex-grow-1 d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-undo me-2"></i> Reset
                                                    </a>
                                                    <button class="btn btn-primary flex-grow-1 d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-filter me-2"></i> Apply Filters
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <!-- Transactions Section -->
                                <div class="finance-card p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h5 class="section-title mb-0">Recent Transactions</h5>
                                        {{--                                        <button class="btn btn-sm btn-outline-primary">--}}
                                        {{--                                            <i class="fas fa-plus me-1"></i> Add Transaction--}}
                                        {{--                                        </button>--}}
                                    </div>

                                    @if(count($history) > 0)
                                        <div class="row">
                                            @foreach($history as $item)
                                                <x-transaction-card :transaction="$item" route-name="finance.show"/>
                                            @endforeach
                                        </div>

                                        @if ($transactions)
                                            <div class="mt-4 custom-pagination-wrapper">
                                                {{ $transactions->links('pagination::bootstrap-5') }}
                                            </div>
                                        @endif
                                    @else
                                        <div class="empty-state">
                                            <i class="fas fa-exchange-alt empty-state-icon"></i>
                                            <h4>No Transactions Found</h4>
                                            <p>You don't have any transactions yet.</p>
                                            {{--                                            <button class="btn btn-primary">--}}
                                            {{--                                                <i class="fas fa-plus me-1"></i> Create Transaction--}}
                                            {{--                                            </button>--}}
                                        </div>
                                    @endif
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
    <script>
        const ctx = document.getElementById('financialChart').getContext('2d');
        const chartContainer = document.getElementById('financialChart').parentElement;

        function showLoading() {
            if (!document.getElementById('chartLoading')) {
                const loadingDiv = document.createElement('div');
                loadingDiv.id = 'chartLoading';
                loadingDiv.innerHTML = `
                <div style="
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    text-align: center;
                ">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading data...</p>
                </div>
            `;
                chartContainer.style.position = 'relative';
                chartContainer.appendChild(loadingDiv);
            }
        }

        function hideLoading() {
            const loadingElement = document.getElementById('chartLoading');
            if (loadingElement) loadingElement.remove();
        }

        window.addEventListener('beforeunload', function() {
            if (window.chartResizeObserver) {
                window.chartResizeObserver.disconnect();
            }
        });

        function updateChart(days) {
            showLoading();

            // Destroy previous chart instance if exists
            if (window.chartInstance) {
                window.chartInstance.destroy();
            }

            fetch(`{{ route('finance.chart') }}?days=${days}`)
                .then(response => response.json())
                .then(chartData => {
                    const gridColor = 'rgba(0, 0, 0, 0.05)';
                    const tooltipBackground = 'rgba(0, 0, 0, 0.8)';
                    const fontFamily = "'Inter', sans-serif";

                    // Get the parent container dimensions
                    const container = document.getElementById('financialChart').parentElement;
                    const containerWidth = container.clientWidth;

                    // Adjust font sizes based on container width
                    const baseFontSize = containerWidth > 768 ? 12 : 10;
                    const titleFontSize = containerWidth > 768 ? 16 : 12;

                    window.chartInstance = new Chart(ctx, {
                        type: 'line',
                        data: chartData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false, // Changed to false for better control
                            plugins: {
                                legend: {
                                    labels: {
                                        font: {
                                            family: fontFamily,
                                            size: baseFontSize
                                        },
                                        padding: 20,
                                        usePointStyle: true,
                                        pointStyle: 'circle'
                                    }
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                    backgroundColor: tooltipBackground,
                                    titleFont: {
                                        family: fontFamily,
                                        size: baseFontSize,
                                        weight: 'bold'
                                    },
                                    bodyFont: {
                                        family: fontFamily,
                                        size: baseFontSize
                                    },
                                    padding: 10,
                                    cornerRadius: 6,
                                    callbacks: {
                                        label: function(context) {
                                            return context.dataset.label + ': PKR ' + context.raw.toLocaleString();
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: gridColor,
                                        drawBorder: false
                                    },
                                    ticks: {
                                        font: {
                                            family: fontFamily,
                                            size: baseFontSize
                                        },
                                        callback: function(value) {
                                            return 'PKR' + value.toLocaleString();
                                        }
                                    }
                                },
                                x: {
                                    grid: {
                                        color: gridColor,
                                        drawBorder: false
                                    },
                                    ticks: {
                                        font: {
                                            family: fontFamily,
                                            size: baseFontSize
                                        }
                                    }
                                }
                            },
                            elements: {
                                line: {
                                    tension: 0.3,
                                    borderWidth: 2,
                                    fill: true
                                },
                                point: {
                                    radius: 0,
                                    hoverRadius: 0,
                                    backgroundColor: 'white',
                                    borderWidth: 2
                                }
                            },
                            animation: {
                                duration: 1000,
                                easing: 'easeOutQuart'
                            }
                        }
                    });

                    // Add resize observer to handle window resizing
                    if (!window.chartResizeObserver) {
                        window.chartResizeObserver = new ResizeObserver(() => {
                            if (window.chartInstance) {
                                window.chartInstance.resize();
                            }
                        });
                        window.chartResizeObserver.observe(container);
                    }
                })
                .catch(error => {
                    console.error('Error loading chart data:', error);
                })
                .finally(() => {
                    hideLoading();
                });
        }

        function fetchFinancialMetrics(year, month) {
            fetch(`{{ route('finance.trends') }}?year=${year}&month=${month}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    // Update the select dropdowns if needed
                    if(data.selectedYear && data.selectedMonth) {
                        document.getElementById('yearSelect').value = data.selectedYear;
                        document.getElementById('monthSelect').value = data.selectedMonth;
                        selectedYear = data.selectedYear;
                        selectedMonth = data.selectedMonth;
                    }

                    // Update the metric values directly
                    updateMetricValue('totalRevenue', data.financialMetrics.total_revenue);
                    updateMetricValue('totalExpenses', data.financialMetrics.total_expenses);
                    updateMetricValue('netProfit', data.financialMetrics.net_profit);
                })
                .catch(error => {
                    console.error('Error fetching financial trends:', error);
                });
        }

        function updateMetricValue(metricId, metricData) {
            // Update amount
            const amountElement = document.getElementById(metricId);
            amountElement.textContent = `PKR ${metricData.value.toLocaleString('en-PK', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;

            // Update trend
            const trendElement = document.getElementById(`${metricId}Trend`);
            const isPositive = metricData.trend === 'up' || (metricId === 'totalExpenses' && metricData.trend === 'down');
            const trendClass = isPositive ? 'positive' : 'negative';
            const trendIcon = isPositive ? 'fa-arrow-up' : 'fa-arrow-down';

            trendElement.innerHTML = `
            <i class="fas ${trendIcon} me-1 ${trendClass}"></i>
            <span class="${trendClass}">${Math.abs(metricData.change).toFixed(2)}% from last month</span>
        `;

            // Special handling for Net Profit amount color
            if (metricId === 'netProfit') {
                amountElement.className = 'amount';
                amountElement.classList.add(metricData.value >= 0 ? 'positive' : 'negative');
            }
        }


        document.addEventListener('DOMContentLoaded', async function () {
            const daysSelect = document.getElementById('daysSelect');
            const yearSelect = document.getElementById('yearSelect');
            const monthSelect = document.getElementById('monthSelect');
            const trendsForm = document.getElementById('trendsFilterForm');
            const thisYearOption = document.getElementById('thisYearOption');

            const currentDate = new Date();
            let selectedMonth = currentDate.getMonth() + 1;
            let selectedYear = currentDate.getFullYear();

            yearSelect.value = selectedYear;
            monthSelect.value = selectedMonth;

            const defaultDays = daysSelect.value;

            fetchFinancialMetrics(selectedYear, selectedMonth);
            updateChart(defaultDays);

            daysSelect.addEventListener('change', function () {
                updateChart(this.value);
            });

            trendsForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                selectedYear = yearSelect.value;
                selectedMonth = monthSelect.value;
                await fetchFinancialMetrics(selectedYear, selectedMonth);
            });

            const today = new Date();
            const firstDayOfYear = new Date(today.getFullYear(), 0, 1);
            const diffTime = today - firstDayOfYear;
            thisYearOption.value = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        });
    </script>
@endpush
