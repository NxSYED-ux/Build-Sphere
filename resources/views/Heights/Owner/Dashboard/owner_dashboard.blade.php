@extends('layouts.app')

@section('title', 'Owner Dashboard')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        :root {
            --primary: #184E83;
            --primary-light: #1A6FC9;
            --secondary: #66CDAA;
            --accent: #FA8072;
            --light: #F8F9FA;
            --dark: #212529;
            --gray: #6C757D;
            --success: #28A745;
            --danger: #DC3545;
            --warning: #FFC107;
            --info: #17A2B8;
        }

        body {
            font-family: 'Inter', sans-serif;
        }

        .dashboard_Header {
            font-family: 'Poppins', sans-serif;
            font-size: 24px;
            font-weight: 600;
            color: var(--sidenavbar-text-color);
            margin-bottom: 1.5rem;
            display: block;
        }

        /* Stats Cards */
        .stats-card {
            border-radius: 10px;
            padding: 20px;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
            margin-bottom: 20px;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #17A2B8, #5BC0DE);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, var(--secondary), #8FE3CF);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, var(--accent), #FFA07A);
        }

        .bg-gradient-purple {
            background: linear-gradient(135deg, #6f42c1, #9b6bcc);
        }

        .icon-container {
            font-size: 2.5rem;
            opacity: 0.2;
            position: absolute;
            right: 20px;
            top: 20px;
        }

        .stats-content h3 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stats-content p {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 15px;
        }

        .progress-indicator {
            background: rgba(255,255,255,0.2);
            height: 4px;
            border-radius: 2px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background: white;
            border-radius: 2px;
        }

        /* Advanced Data Cards */
        .advanced-data-card {
            background: var(--body-card-bg);
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            height: 100%;
        }

        .card-header {
            padding: 18px 20px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h3 {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
            color: var(--sidenavbar-text-color);
        }

        .card-actions {
            display: flex;
            align-items: center;
        }

        .currentDate {
            font-size: 12px;
            color: var(--gray);
            margin-right: 15px;
        }

        .btn-details {
            background: var(--primary);
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-details:hover {
            background: var(--primary-light);
            transform: translateX(3px);
        }

        .btn-details i {
            margin-left: 5px;
            font-size: 14px;
        }

        .card-body {
            padding: 20px;
        }

        .data-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .data-item {
            text-align: center;
            padding: 15px;
            background: rgba(24, 78, 131, 0.05);
            border-radius: 8px;
        }

        .data-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .data-label {
            font-size: 13px;
            color: var(--gray);
            margin-bottom: 8px;
        }

        .data-trend {
            font-size: 11px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .data-trend i {
            margin-right: 3px;
            font-size: 14px;
        }

        .data-trend.up {
            color: var(--success);
        }

        .data-trend.down {
            color: var(--danger);
        }

        .mini-chart-container, .donut-chart-container {
            height: 100px;
            margin-top: 10px;
            position: relative;
        }

        /* Chart Cards */
        .chart-card {
            background: var(--body-card-bg);
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            height: 100%;
        }

        .chart-header {
            padding: 15px 20px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chart-header h4 {
            font-size: 16px;
            font-weight: 600;
            margin: 0;
            color: var(--sidenavbar-text-color);
        }

        .chart-legend {
            display: flex;
            flex-wrap: wrap;
        }

        .legend-item {
            font-size: 12px;
            color: var(--gray);
            margin-left: 15px;
            display: flex;
            align-items: center;
        }

        .legend-item i {
            margin-right: 5px;
            font-size: 14px;
        }

        .legend-item.rented i {
            color: #4BC0C0;
        }

        .legend-item.sold i {
            color: #FF6384;
        }

        .legend-item.available i {
            color: #FFCD56;
        }

        .legend-item.active i {
            color: #4BC0C0;
        }

        .legend-item.expired i {
            color: #FF6384;
        }

        .legend-item.managers i {
            color: #36A2EB;
        }

        .legend-item.staff i {
            color: #9966FF;
        }

        .chart-container {
            padding: 15px;
            height: 300px;
            position: relative;
            width: 100%;
        }

        .chart-container canvas {
            width: 100% !important;
            height: 100% !important;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .data-grid {
                grid-template-columns: 1fr;
            }

            .chart-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .chart-legend {
                margin-top: 10px;
                flex-wrap: wrap;
            }
        }

        /* Main content adjustments */
        #main {
            /*padding: 20px;*/
        }

        .content-wrapper {
            padding: 15px;
        }
    </style>
@endpush

@section('content')
    <!-- Top Navigation -->
    <x-Owner.top-navbar :searchVisible="true"/>

    <!-- Side Navigation -->
    <x-Owner.side-navbar :openSections="['Dashboard']"/>
    <x-error-success-model />

    <div id="main" style="margin-top: 35px;">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="content-wrapper" style="min-height: 751px;">
                        <section class="content-header mt-3">
                            <h3 class="dashboard_Header">Owner Dashboard</h3>
                        </section>

                        <section class="content">
                            <!-- Stats Cards Row -->
                            <div class="row my-3">
                                <!-- Total Buildings Card -->
                                <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                                    <div class="stats-card bg-gradient-primary">
                                        <div class="icon-container">
                                            <i class="bx bx-buildings"></i>
                                        </div>
                                        <div class="stats-content">
                                            <h3 id="totalBuildings">0</h3>
                                            <p>Total Buildings</p>
                                        </div>
                                        <div class="progress-indicator">
                                            <div class="progress-bar" style="width: 75%"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Total Units Card -->
                                <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                                    <div class="stats-card bg-gradient-success">
                                        <div class="icon-container">
                                            <i class="bx bxs-home"></i>
                                        </div>
                                        <div class="stats-content">
                                            <h3 id="totalUnits">0</h3>
                                            <p>Total Units</p>
                                        </div>
                                        <div class="progress-indicator">
                                            <div class="progress-bar" style="width: 85%"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Total Staff Card -->
                                <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                                    <div class="stats-card bg-gradient-warning">
                                        <div class="icon-container">
                                            <i class="bx bxs-user-detail"></i>
                                        </div>
                                        <div class="stats-content">
                                            <h3 id="totalStaff">0</h3>
                                            <p>Total Staff</p>
                                        </div>
                                        <div class="progress-indicator">
                                            <div class="progress-bar" style="width: 45%"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Total Revenue Card -->
                                <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                                    <div class="stats-card bg-gradient-purple">
                                        <div class="icon-container">
                                            <i class="bx bx-money"></i>
                                        </div>
                                        <div class="stats-content">
                                            <h3 id="totalRevenue">0</h3>
                                            <p>Total Revenue</p>
                                        </div>
                                        <div class="progress-indicator">
                                            <div class="progress-bar" style="width: 65%"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Total Expense Card -->
                                <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                                    <div class="stats-card bg-gradient-success">
                                        <div class="icon-container">
                                            <i class="bx bx-credit-card"></i>
                                        </div>
                                        <div class="stats-content">
                                            <h3 id="totalExpense">0</h3>
                                            <p>Total Expense</p>
                                        </div>
                                        <div class="progress-indicator">
                                            <div class="progress-bar" style="width: 55%"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Net Profit Card -->
                                <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                                    <div class="stats-card bg-gradient-info">
                                        <div class="icon-container">
                                            <i class="bx bx-line-chart"></i>
                                        </div>
                                        <div class="stats-content">
                                            <h3 id="netProfit">0</h3>
                                            <p>Net Profit</p>
                                        </div>
                                        <div class="progress-indicator">
                                            <div class="progress-bar" style="width: 70%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Cards Row -->
                            <div class="row my-3">
                                <!-- Unit Occupancy Summary -->
                                <div class="col-md-6 mb-4">
                                    <div class="advanced-data-card">
                                        <div class="card-header">
                                            <h3>Unit Occupancy</h3>
                                            <div class="card-actions">
                                                <span class="currentDate"></span>
                                                <button class="btn-details">View All <i class="bx bx-chevron-right"></i></button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="data-grid">
                                                <div class="data-item">
                                                    <div class="data-value" id="rentedUnits">0</div>
                                                    <div class="data-label">Rented</div>
                                                    <div class="data-trend up">
                                                        <i class="bx bx-up-arrow-alt"></i> 12%
                                                    </div>
                                                </div>
                                                <div class="data-item">
                                                    <div class="data-value" id="soldUnits">0</div>
                                                    <div class="data-label">Sold</div>
                                                    <div class="data-trend down">
                                                        <i class="bx bx-down-arrow-alt"></i> 5%
                                                    </div>
                                                </div>
                                                <div class="data-item">
                                                    <div class="data-value" id="availableUnits">0</div>
                                                    <div class="data-label">Available</div>
                                                    <div class="data-trend up">
                                                        <i class="bx bx-up-arrow-alt"></i> 8%
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="donut-chart-container">
                                                <canvas id="unitOccupancyChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Membership Plans -->
                                <div class="col-md-6 mb-4">
                                    <div class="advanced-data-card">
                                        <div class="card-header">
                                            <h3>Membership Plans</h3>
                                            <div class="card-actions">
                                                <span class="currentDate"></span>
                                                <button class="btn-details">Manage <i class="bx bx-chevron-right"></i></button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="data-grid">
                                                <div class="data-item">
                                                    <div class="data-value" id="activeMemberships">0</div>
                                                    <div class="data-label">Active</div>
                                                    <div class="data-trend up">
                                                        <i class="bx bx-up-arrow-alt"></i> 15%
                                                    </div>
                                                </div>
                                                <div class="data-item">
                                                    <div class="data-value" id="expiredMemberships">0</div>
                                                    <div class="data-label">Expired</div>
                                                    <div class="data-trend down">
                                                        <i class="bx bx-down-arrow-alt"></i> 10%
                                                    </div>
                                                </div>
                                                <div class="data-item">
                                                    <div class="data-value" id="planUsage">0%</div>
                                                    <div class="data-label">Plan Usage</div>
                                                    <div class="data-trend up">
                                                        <i class="bx bx-up-arrow-alt"></i> 5%
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mini-chart-container">
                                                <canvas id="membershipTrendChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Charts Row -->
                            <div class="row">
                                <!-- Unit Status Distribution -->
                                <div class="col-md-6 mb-4">
                                    <div class="chart-card">
                                        <div class="chart-header">
                                            <h4>Unit Status Distribution</h4>
                                            <div class="chart-legend">
                                                <span class="legend-item rented"><i class="bx bx-square"></i> Rented</span>
                                                <span class="legend-item sold"><i class="bx bx-square"></i> Sold</span>
                                                <span class="legend-item available"><i class="bx bx-square"></i> Available</span>
                                            </div>
                                        </div>
                                        <div class="chart-container">
                                            <canvas id="unitStatusChart"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Staff Distribution -->
                                <div class="col-md-6 mb-4">
                                    <div class="chart-card">
                                        <div class="chart-header">
                                            <h4>Staff Distribution</h4>
                                            <div class="chart-legend">
                                                <span class="legend-item managers"><i class="bx bx-square"></i> Managers</span>
                                                <span class="legend-item staff"><i class="bx bx-square"></i> Other Staff</span>
                                            </div>
                                        </div>
                                        <div class="chart-container">
                                            <canvas id="staffDistributionChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Second Charts Row -->
                            <div class="row">
                                <!-- Monthly Income vs Expenses -->
                                <div class="col-md-6 mb-4">
                                    <div class="chart-card">
                                        <div class="chart-header">
                                            <h4>Monthly Income vs Expenses</h4>
                                            <div class="chart-legend">
                                                <span class="legend-item"><i class="bx bx-line-chart"></i> Income</span>
                                                <span class="legend-item"><i class="bx bx-trending-up"></i> Expenses</span>
                                            </div>
                                        </div>
                                        <div class="chart-container">
                                            <canvas id="incomeExpenseChart"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Membership Plan Usage -->
                                <div class="col-md-6 mb-4">
                                    <div class="chart-card">
                                        <div class="chart-header">
                                            <h4>Membership Plan Usage</h4>
                                            <div class="chart-legend">
                                                <span class="legend-item"><i class="bx bx-pie-chart-alt"></i> By Plan Type</span>
                                            </div>
                                        </div>
                                        <div class="chart-container">
                                            <canvas id="membershipPlanChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Chart.js plugins -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Sample data for owner dashboard
            const ownerData = {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                income: [8500, 12000, 9500, 11000, 13500, 16000, 14000, 15500, 14500, 17000, 18500, 20000],
                expenses: [5000, 7500, 6000, 7000, 9000, 11000, 9500, 10000, 9000, 11500, 12500, 13500],
                rentedUnits: [25, 30, 28, 35, 40, 45, 42, 40, 43, 46, 48, 50],
                soldUnits: [10, 8, 12, 10, 7, 5, 8, 10, 7, 5, 3, 2],
                availableUnits: [15, 12, 10, 5, 3, 0, 0, 0, 0, 0, 0, 0],
                activeMemberships: [35, 40, 38, 45, 50, 55, 52, 50, 53, 56, 58, 60],
                expiredMemberships: [5, 10, 12, 8, 5, 5, 8, 10, 7, 4, 2, 0],
                managers: 8,
                staff: 22,
                planUsage: {
                    basic: 45,
                    premium: 30,
                    enterprise: 15,
                    custom: 10
                }
            };

            // Current date display
            const monthNames = ["JANUARY", "FEBRUARY", "MARCH", "APRIL", "MAY", "JUNE", "JULY", "AUGUST", "SEPTEMBER", "OCTOBER", "NOVEMBER", "DECEMBER"];
            const currentDate = new Date();
            const currentMonth = currentDate.getMonth();
            const currentYear = currentDate.getFullYear();

            document.querySelectorAll('.currentDate').forEach(el => {
                el.textContent = `${monthNames[currentMonth]} ${currentYear}`;
            });

            // 1. Unit Occupancy Donut Chart
            const unitOccupancyCtx = document.getElementById('unitOccupancyChart').getContext('2d');
            new Chart(unitOccupancyCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Rented', 'Sold', 'Available'],
                    datasets: [{
                        data: [
                            ownerData.rentedUnits[currentMonth],
                            ownerData.soldUnits[currentMonth],
                            ownerData.availableUnits[currentMonth]
                        ],
                        backgroundColor: ['#4BC0C0', '#FF6384', '#FFCD56'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: false,
                        datalabels: {
                            display: false
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });

            // 2. Membership Trend Mini Chart
            const membershipTrendCtx = document.getElementById('membershipTrendChart').getContext('2d');
            new Chart(membershipTrendCtx, {
                type: 'line',
                data: {
                    labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                    datasets: [{
                        label: 'New Memberships',
                        data: [8, 12, 10, 15],
                        borderColor: '#184E83',
                        backgroundColor: 'rgba(24, 78, 131, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: false
                    },
                    scales: {
                        y: {
                            display: false,
                            min: 0,
                            max: 20
                        },
                        x: {
                            display: false
                        }
                    },
                    elements: {
                        point: {
                            radius: 0
                        }
                    }
                }
            });

            // 3. Unit Status Bar Chart
            const unitStatusCtx = document.getElementById('unitStatusChart').getContext('2d');
            new Chart(unitStatusCtx, {
                type: 'bar',
                data: {
                    labels: ownerData.labels,
                    datasets: [
                        {
                            label: 'Rented',
                            data: ownerData.rentedUnits,
                            backgroundColor: 'rgba(75, 192, 192, 0.7)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 0,
                            borderRadius: 4
                        },
                        {
                            label: 'Sold',
                            data: ownerData.soldUnits,
                            backgroundColor: 'rgba(255, 99, 132, 0.7)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 0,
                            borderRadius: 4
                        },
                        {
                            label: 'Available',
                            data: ownerData.availableUnits,
                            backgroundColor: 'rgba(255, 205, 86, 0.7)',
                            borderColor: 'rgba(255, 205, 86, 1)',
                            borderWidth: 0,
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false
                            },
                            ticks: {
                                color: '#6C757D'
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                color: '#6C757D'
                            }
                        }
                    },
                    plugins: {
                        legend: false,
                        tooltip: {
                            backgroundColor: '#212529',
                            titleFont: {
                                weight: 'normal'
                            },
                            bodyFont: {
                                weight: 'normal'
                            },
                            padding: 10,
                            usePointStyle: true,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.raw;
                                }
                            }
                        }
                    }
                }
            });

            // 4. Staff Distribution Pie Chart
            const staffDistributionCtx = document.getElementById('staffDistributionChart').getContext('2d');
            new Chart(staffDistributionCtx, {
                type: 'pie',
                data: {
                    labels: ['Managers', 'Other Staff'],
                    datasets: [{
                        data: [ownerData.managers, ownerData.staff],
                        backgroundColor: ['#36A2EB', '#9966FF'],
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
                                pointStyle: 'circle',
                                padding: 20
                            }
                        },
                        tooltip: {
                            backgroundColor: '#212529',
                            titleFont: {
                                weight: 'normal'
                            },
                            bodyFont: {
                                weight: 'normal'
                            },
                            padding: 10,
                            usePointStyle: true,
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((acc, data) => acc + data, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value} (${percentage}%)`;
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

            // 5. Income vs Expense Chart
            const incomeExpenseCtx = document.getElementById('incomeExpenseChart').getContext('2d');
            new Chart(incomeExpenseCtx, {
                type: 'line',
                data: {
                    labels: ownerData.labels,
                    datasets: [
                        {
                            label: 'Income',
                            data: ownerData.income,
                            backgroundColor: 'rgba(54, 162, 235, 0.1)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'Expenses',
                            data: ownerData.expenses,
                            backgroundColor: 'rgba(255, 99, 132, 0.1)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false
                            },
                            ticks: {
                                color: '#6C757D'
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                color: '#6C757D'
                            }
                        }
                    },
                    plugins: {
                        legend: false,
                        tooltip: {
                            backgroundColor: '#212529',
                            titleFont: {
                                weight: 'normal'
                            },
                            bodyFont: {
                                weight: 'normal'
                            },
                            padding: 10,
                            usePointStyle: true
                        }
                    },
                    elements: {
                        point: {
                            radius: 0,
                            hoverRadius: 6
                        }
                    }
                }
            });

            // 6. Membership Plan Chart
            const membershipPlanCtx = document.getElementById('membershipPlanChart').getContext('2d');
            new Chart(membershipPlanCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Basic', 'Premium', 'Enterprise', 'Custom'],
                    datasets: [{
                        data: Object.values(ownerData.planUsage),
                        backgroundColor: ['#36A2EB', '#4BC0C0', '#9966FF', '#FFCE56'],
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
                                pointStyle: 'circle',
                                padding: 20
                            }
                        },
                        tooltip: {
                            backgroundColor: '#212529',
                            titleFont: {
                                weight: 'normal'
                            },
                            bodyFont: {
                                weight: 'normal'
                            },
                            padding: 10,
                            usePointStyle: true,
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    return `${label}: ${value}%`;
                                }
                            }
                        }
                    }
                }
            });

            // Details buttons functionality
            const detailButtons = document.querySelectorAll('.btn-details');
            detailButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const cardTitle = this.closest('.card-header').querySelector('h3').textContent;
                    alert(`Viewing details for: ${cardTitle}`);
                });
            });

            // Initialize with sample data
            document.getElementById('totalBuildings').textContent = '5';
            document.getElementById('totalLevels').textContent = '24';
            document.getElementById('totalUnits').textContent = '120';
            document.getElementById('totalStaff').textContent = '30';
            document.getElementById('rentedUnits').textContent = ownerData.rentedUnits[currentMonth];
            document.getElementById('soldUnits').textContent = ownerData.soldUnits[currentMonth];
            document.getElementById('availableUnits').textContent = ownerData.availableUnits[currentMonth];
            document.getElementById('activeMemberships').textContent = ownerData.activeMemberships[currentMonth];
            document.getElementById('expiredMemberships').textContent = ownerData.expiredMemberships[currentMonth];
            document.getElementById('planUsage').textContent = '75%';
        });
    </script>
@endpush
