@extends('layouts.app')

@section('title', 'Manager Details')

@push('styles')
    <style>
        :root {
            --primary-color: var(--color-blue);
            --primary-hover: var(--color-blue);
            --secondary-color: #f8f9fa;
            --border-color: #e2e8f0;
            --error-color: #e53e3e;
            --success-color: #38a169;
            --permission-header-bg: rgba(var(--color-blue), 0.05);
            --permission-card-bg: var(--body-background-color);
            --child-permission-indicator: var(--color-blue);
        }
        #main {
            margin-top: 45px;
        }

        .chart-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            background: var(--sidenavbar-body-color);
            overflow: hidden;
        }

        .chart-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
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
            background-color: var(--color-blue);
            border-color: var(--color-blue);
            padding: 0.5rem 1.25rem;
            font-weight: 500;
            letter-spacing: 0.2px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--color-blue);
            border-color: var(--color-blue);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
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
        }



        /* Staff Details Container */
        .staff-detail-container {
            position: relative;
            background: var(--body-card-bg);
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            overflow: visible;
        }

        .staff-detail-container .staff-detail-hero {
            display: flex;
            flex-direction: row;
            padding: 2rem 2rem 0.5rem 2rem;
        }

        .staff-detail-container .staff-image-section {
            flex: 0 0 250px;
            padding-right: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .staff-detail-container .staff-avatar {
            width: 200px;
            height: 200px;
            border-radius: 10px;
            object-fit: cover;
            border: 4px solid #f8f9fa;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .staff-detail-container .staff-detail-section {
            flex: 1;
        }

        .staff-detail-container .staff-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .staff-detail-container .staff-name {
            font-size: 2rem;
            font-weight: 700;
            color: var(--sidenavbar-text-color);
            margin: 0;
        }

        .staff-detail-container .staff-header-actions {
            display: flex;
            gap: 0.75rem;
        }

        .staff-detail-container .staff-position {
            font-size: 1.1rem;
            color: var(--sidenavbar-text-color);
            margin-bottom: 1.5rem;
        }

        .staff-detail-container .staff-meta {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1rem;
        }

        .staff-detail-container .meta-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.95rem;
            padding: 0.1rem 0;
        }

        .staff-detail-container .promote-item {
            align-items: flex-start;
        }

        .staff-detail-container .meta-icon {
            color: var(--color-blue);
            width: 20px;
            text-align: center;
            font-size: 1rem;
            margin-top: 2px;
        }

        /* Buildings Specific Styles */
        .staff-detail-container .meta-item-buildings {
            position: relative;
            align-items: flex-start;
        }

        .staff-detail-container .buildings-container {
            flex: 1;
            position: relative; /* Added for proper dropdown positioning */
        }

        /* Building Card Styles */
        .staff-detail-container .building-card {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 0.9rem;
            border: 1px solid #e9ecef;
            display: inline-block;
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Buildings Dropdown Styles */
        .staff-detail-container .buildings-dropdown {
            display: inline-block;
            position: relative; /* Changed to relative for proper dropdown positioning */
        }

        .staff-detail-container .buildings-toggle {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 0.9rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            min-width: 120px;
        }

        .staff-detail-container .buildings-toggle:hover {
            background: #e9ecef;
        }

        .staff-detail-container .buildings-count {
            font-weight: 500;
        }

        .staff-detail-container .dropdown-icon {
            font-size: 0.8rem;
            transition: transform 0.2s;
        }

        .staff-detail-container .buildings-dropdown.active .dropdown-icon {
            transform: rotate(180deg);
        }

        .staff-detail-container .buildings-dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            background: var(--body-background-color);
            border: 1px solid #e9ecef;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            min-width: 100%;
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            margin-top: 4px;
        }

        /* Corrected active state selector */
        .staff-detail-container .buildings-dropdown.active .buildings-dropdown-menu {
            display: block;
        }

        .staff-detail-container .building-item {
            padding: 8px 12px;
            border-bottom: 1px solid #f1f3f5;
            transition: background 0.2s;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .staff-detail-container .building-item:hover {
            background: #f8f9fa;
        }

        .staff-detail-container .building-item:last-child {
            border-bottom: none;
        }

        .staff-detail-container .no-buildings {
            color: var(--sidenavbar-text-color);
            font-style: italic;
            margin-top: 6px;
        }

        /* Button Styles */
        .staff-detail-container .btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
            border: none;
            white-space: nowrap;
        }

        .staff-detail-container .btn-edit {
            background-color: var(--color-blue);
            color: white;
        }

        .staff-detail-container .btn-edit:hover {
            background-color: var(--color-blue);
        }

        .staff-detail-container .btn-danger {
            background-color: #e74a3b;
            color: white;
        }

        .staff-detail-container .btn-danger:hover {
            background-color: #d52a1a;
        }

        /* Mobile Actions */
        .staff-detail-container .staff-mobile-actions {
            display: none;
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .staff-detail-container .staff-detail-hero {
                flex-direction: column;
                padding: 1.5rem;
            }

            .staff-detail-container .staff-image-section {
                padding-right: 0;
                margin-bottom: 1.5rem;
                flex: 0 0 auto;
            }

            .staff-detail-container .staff-avatar {
                width: 150px;
                height: 150px;
            }

            .staff-detail-container .staff-meta {
                grid-template-columns: 1fr;
            }

            .staff-detail-container .buildings-dropdown-menu {
                left: 0;
                right: auto;
                min-width: 100%;
            }

            .staff-detail-container .staff-header-actions {
                display: none;
            }

            .staff-detail-container .staff-mobile-actions {
                display: block;
                margin-top: 1.5rem;
            }

            .staff-detail-container .mobile-action-row {
                display: flex;
                gap: 0.75rem;
            }

            .staff-detail-container .building-card {
                width: 100%;
                color: #5F5F5F !important;
            }
        }

        @media (max-width: 576px) {
            .staff-detail-container .staff-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .staff-detail-container .staff-name {
                font-size: 1.5rem;
            }

            .staff-detail-container .mobile-action-row {
                flex-direction: column;
            }

            .staff-detail-container .mobile-action-row .btn {
                width: 100%;
            }

            .staff-detail-container .buildings-toggle {
                min-width: 100%;
            }
        }

        /* Charts Styles */
        .charts-row {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-bottom: 0.7rem;
        }

        .chart-container {
            flex: 1;
            min-width: 300px;
            position: relative;
            min-height: 300px;
            background: var(--body-background-color);
            border-radius: 14px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .chart-title {
            font-weight: 600;
            color: var(--sidenavbar-text-color);
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .chart-wrapper {
            position: relative;
            height: 300px;
            width: 100%;
        }

        @media (max-width: 768px) {
            .charts-row {
                flex-direction: column;
            }

            .chart-container {
                min-width: 100%;
            }

        }
    </style>
@endpush

@section('content')

    <!-- Top Navbar -->
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
        ['url' => route('owner_manager_dashboard'), 'label' => 'Dashboard'],
        ['url' => route('owner.managers.index'), 'label' => 'Managers'],
        ['url' => '', 'label' => 'Manager Show']
    ]" />

    <!-- Side Navbar -->
    <x-Owner.side-navbar :openSections="['Managers']" />

    <!-- Error/Success Modal -->
    <x-error-success-model />
    <x-demote-to-staff />

    <div id="main">
        <section class="content mt-1 mb-5 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                            <div class="container mt-2">

                                <div class="staff-detail-container">
                                    <div class="staff-detail-hero">
                                        <!-- Left Side - Staff Image -->
                                        <div class="staff-image-section">
                                            <img src="{{ $staffInfo->user->picture ? asset($staffInfo->user->picture) : asset('img/placeholder-profile.png') }}"
                                                 alt="{{ $staffInfo->user->name }}"
                                                 class="staff-avatar">
                                        </div>

                                        <!-- Right Side - Staff Details -->
                                        <div class="staff-detail-section">
                                            <div class="staff-header">
                                                <h1 class="staff-name">{{ $staffInfo->user->name }}</h1>
                                                <div class="staff-header-actions">
                                                    <a href="{{ route('owner.staff.edit', $staffInfo->id) }}" class="btn btn-edit" title="Edit Staff">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <button type="button"
                                                            class="btn btn-danger delete-member-btn"
                                                            data-member-id="{{ $staffInfo->id }}"
                                                            title="Delete Staff Member">
                                                        <i class="fas fa-trash-alt"></i> Delete
                                                    </button>
                                                </div>
                                            </div>

                                            <p class="staff-position">
                                                {{ $staffInfo->user->role->name ?? 'Manager' }}
                                            </p>

                                            <div class="staff-meta">
                                                <div class="meta-item">
                                                    <i class="fas fa-envelope meta-icon"></i>
                                                    <a href="mailto:{{ $staffInfo->user->email }}" class="text-decoration-none">{{ $staffInfo->user->email }}</a>
                                                </div>
                                                <div class="meta-item">
                                                    <i class="fas fa-phone meta-icon"></i>
                                                    <span>{{ $staffInfo->user->phone_no ?? 'Not provided' }}</span>
                                                </div>
                                                <div class="meta-item">
                                                    <i class="fas fa-address-card meta-icon"></i>
                                                    <span>{{ $staffInfo->user->cnic ?? 'Not provided' }}</span>
                                                </div>
                                                <div class="meta-item">
                                                    <i class="fas fa-calendar-alt meta-icon"></i>
                                                    <span>Member since {{ $staffInfo->created_at->format('M Y') }}</span>
                                                </div>
                                                <div class="meta-item meta-item-buildings">
                                                    <i class="fas fa-building meta-icon pt-2"></i>
                                                    <div class="buildings-container w-100">
                                                        @if($managerBuildings->count() > 0)
                                                            @if($managerBuildings->count() === 1)
                                                                <div class="building-card" style="color: #5F5F5F !important; font-weight: 600;">
                                                                    {{ $managerBuildings->first()->building->name }}
                                                                </div>
                                                            @else
                                                                <div class="buildings-dropdown">
                                                                    <button class="buildings-toggle">
                                                                        <span class="buildings-count">{{ $managerBuildings->count() }} Buildings</span>
                                                                        <i class="fas fa-chevron-down dropdown-icon"></i>
                                                                    </button>
                                                                    <div class="buildings-dropdown-menu">
                                                                        @foreach($managerBuildings as $managerBuilding)
                                                                            <div class="building-item">
                                                                                {{ $managerBuilding->building->name }}
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @else
                                                            <div class="no-buildings">No buildings assigned</div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="meta-item promote-item">
                                                    <i class="fas fa-user-shield meta-icon pt-2"></i>
                                                    <button class="btn btn-primary demote-btn w-100" data-staff-id="{{ $staffInfo->id }}">Demote To Staff</button>
                                                </div>
                                            </div>

                                            <!-- Mobile Actions -->
                                            <div class="staff-mobile-actions">
                                                <div class="mobile-action-row">
                                                    <a href="{{ route('owner.staff.edit', $staffInfo->id) }}" class="btn btn-edit" title="Edit Staff">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <button type="button"
                                                            class="btn btn-danger delete-member-btn"
                                                            data-member-id="{{ $staffInfo->id }}"
                                                            title="Delete Staff Member">
                                                        <i class="fas fa-trash-alt"></i> Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Charts Section -->
                                <div class="chart-card p-4 mt-3 mb-3">
                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
                                        <h5 class="section-title mb-3 mb-md-0">Buildings Overview</h5>
                                        <div class="d-flex flex-wrap gap-2">
                                            <select class="form-select" id="yearSelect">
                                                @for($i = date('Y'); $i >= 2020; $i--)
                                                    <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                            <select class="form-select" id="buildingSelect">
                                                <option value="">All Buildings</option>
                                                @foreach($managerBuildings as $managerBuilding)
                                                    <option value="{{ $managerBuilding->building->id }}" >{{ $managerBuilding->building->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="charts-row">
                                        <!-- Pie Chart Container -->
                                        <div class="chart-container">
                                            <div class="chart-title">Unit Occupancy</div>
                                            <div class="chart-wrapper">
                                                <canvas id="occupancyChart"></canvas>
                                            </div>
                                        </div>

                                        <!-- Line Chart Container -->
                                        <div class="chart-container">
                                            <div class="chart-title">Financial Overview</div>
                                            <div class="chart-wrapper">
                                                <canvas id="financialChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Transactions Section -->
                                <div class="chart-card p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h5 class="section-title mb-0">Recent Transactions</h5>
                                    </div>

                                    @if(count($transactions) > 0)
                                        <div class="row">
                                            @foreach($transactions as $item)
                                                <x-transaction-card :transaction="$item" route-name="owner.finance.show"/>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="empty-state text-center">
                                            <i class="fas fa-exchange-alt empty-state-icon"></i>
                                            <h4>No Transactions Found</h4>
                                            <p>You don't have any transactions yet.</p>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.buildings-toggle').forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    this.closest('.buildings-dropdown').classList.toggle('active');
                });
            });

            // Close when clicking elsewhere
            document.addEventListener('click', function() {
                document.querySelectorAll('.buildings-dropdown').forEach(dropdown => {
                    dropdown.classList.remove('active');
                });
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script>
        // Common functions
        function showLoading(chartId) {
            const chartContainer = document.getElementById(chartId).parentElement;
            if (!document.getElementById(`${chartId}Loading`)) {
                const loadingDiv = document.createElement('div');
                loadingDiv.id = `${chartId}Loading`;
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

        function hideLoading(chartId) {
            const loadingElement = document.getElementById(`${chartId}Loading`);
            if (loadingElement) loadingElement.remove();
        }

        // Financial Chart Logic
        function updateFinancialChart(buildingId) {
            showLoading('financialChart');

            if (window.financialChartInstance) {
                window.financialChartInstance.destroy();
            }

            const managerId = @json($staffInfo->id);
            const year = document.getElementById('yearSelect').value;
            const ctx = document.getElementById('financialChart').getContext('2d');

            fetch(`{{ route('owner.managers.monthlyFinancial.stats', $staffInfo->id) }}?buildingId=${buildingId}&year=${year}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(chartData => {
                    const gridColor = 'rgba(0, 0, 0, 0.05)';
                    const tooltipBackground = 'rgba(0, 0, 0, 0.8)';
                    const fontFamily = "'Inter', sans-serif";

                    window.financialChartInstance = new Chart(ctx, {
                        type: 'line',
                        data: chartData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    labels: {
                                        font: {
                                            family: fontFamily,
                                            size: 12
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
                                        size: 12,
                                        weight: 'bold'
                                    },
                                    bodyFont: {
                                        family: fontFamily,
                                        size: 12
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
                                            size: 12
                                        },
                                        callback: function(value) {
                                            return 'PKR ' + value.toLocaleString();
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
                                            size: 12
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
                })
                .catch(error => {
                    console.error('Error loading financial chart data:', error);
                })
                .finally(() => {
                    hideLoading('financialChart');
                });
        }

        // Occupancy Chart Logic
        function updateOccupancyChart(buildingId) {
            showLoading('occupancyChart');

            if (window.occupancyChartInstance) {
                window.occupancyChartInstance.destroy();
            }

            const managerId = @json($staffInfo->id);
            const ctx = document.getElementById('occupancyChart').getContext('2d');

            fetch(`{{ route('owner.managers.occupancy.stats', $staffInfo->id) }}?buildingId=${buildingId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    const chartData = {
                        labels: ['Available', 'Rented', 'Sold'],
                        datasets: [{
                            data: [data.availableUnits, data.rentedUnits, data.soldUnits],
                            backgroundColor: [
                                'rgba(75, 192, 192, 0.8)',
                                'rgba(54, 162, 235, 0.8)',
                                'rgba(255, 99, 132, 0.8)'
                            ],
                            borderColor: [
                                'rgba(75, 192, 192, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 99, 132, 1)'
                            ],
                            borderWidth: 1
                        }]
                    };

                    window.occupancyChartInstance = new Chart(ctx, {
                        type: 'doughnut',
                        data: chartData,
                        plugins: [{
                            id: 'centerTotal',
                            beforeDraw: (chart) => {
                                if (chart.config.options.centerTotal) {
                                    const { ctx, chartArea: { width, height } } = chart;
                                    ctx.restore();

                                    // Calculate total
                                    const total = chart.data.datasets[0].data.reduce((a, b) => a + b, 0);

                                    // Styling for center text
                                    ctx.font = 'bold 30px "Inter", sans-serif';
                                    ctx.fillStyle = getComputedStyle(document.documentElement).getPropertyValue('--sidenavbar-text-color').trim(),
                                    ctx.textAlign = 'center';
                                    ctx.textBaseline = 'middle';

                                    // Display total in center
                                    ctx.fillText(total, width / 2, height / 2);
                                    ctx.save();
                                }
                            }
                        }, ChartDataLabels],
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '70%',
                            centerTotal: true, // Custom flag to enable center total
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { font: { family: "'Inter', sans-serif", size: 12 }, padding: 20 }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0,0,0,0.9)',
                                    bodyFont: { family: "'Inter', sans-serif", size: 12, weight: 'bold' },
                                    callbacks: {
                                        label: (ctx) => {
                                            const label = ctx.label || '';
                                            const value = ctx.raw || 0;
                                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = Math.round((value / total) * 100);
                                            return `${label}: ${value} (${percentage}%)`;
                                        }
                                    }
                                },
                                datalabels: {
                                    display: (ctx) => {
                                        const total = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                        const percentage = Math.round((ctx.dataset.data[ctx.dataIndex] / total) * 100);
                                        return percentage >= 5; // Hide labels for segments <5%
                                    },
                                    formatter: (value, ctx) => {
                                        const total = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                        return `${Math.round((value / total) * 100)}%`; // Only show % inside segments
                                    },
                                    color: '#fff',
                                    font: { family: "'Inter', sans-serif", size: 12, weight: 'bold' },
                                    textShadowBlur: 6,
                                    textShadowColor: 'rgba(0,0,0,0.8)'
                                }
                            },
                            animation: {
                                animateScale: true,
                                animateRotate: true,
                                duration: 1000
                            },
                            hover: {
                                mode: 'nearest',
                                intersect: false,
                                animation: {
                                    duration: 300,
                                    easing: 'easeOutQuad'
                                },
                                onHover: (e, elements) => {
                                    if (elements.length) {
                                        const chart = e.chart;
                                        const index = elements[0].index;

                                        // Hover animation: Scale segment slightly
                                        chart.setActiveElements([{ datasetIndex: 0, index }]);
                                        chart.update();
                                    }
                                }
                            },
                            elements: {
                                arc: {
                                    hoverOffset: 10, // Pop-out effect on hover
                                    borderWidth: 0,
                                    borderRadius: 5 // Rounded edges for segments
                                }
                            }
                        }
                    });
                })
                .catch(error => {
                    console.error('Error loading occupancy chart data:', error);
                })
                .finally(() => {
                    hideLoading('occupancyChart');
                });
        }

        // Initialize both charts when building selection changes
        function updateAllCharts() {
            const buildingId = document.getElementById('buildingSelect').value;
            updateFinancialChart(buildingId);
            updateOccupancyChart(buildingId);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize both charts
            updateAllCharts();

            // Update charts when building selection changes
            document.getElementById('buildingSelect').addEventListener('change', updateAllCharts);

            // Update financial chart only when year selection changes
            document.getElementById('yearSelect').addEventListener('change', function() {
                const buildingId = document.getElementById('buildingSelect').value;
                updateFinancialChart(buildingId);
            });
        });

        window.addEventListener('beforeunload', function() {
            if (window.financialChartInstance) {
                window.financialChartInstance.destroy();
            }
            if (window.occupancyChartInstance) {
                window.occupancyChartInstance.destroy();
            }
        });
    </script>
@endpush
