@extends('layouts.app')

@section('title', 'Finance Dashboard')

@push('styles')
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Inter', sans-serif;
        }
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

        .summary-card:nth-child(2)::before {
            background: linear-gradient(to bottom, #ef4444, #f97316);
        }

        .summary-card:nth-child(3)::before {
            background: linear-gradient(to bottom, #10b981, #14b8a6);
        }

        .summary-card:nth-child(4)::before {
            background: linear-gradient(to bottom, #f59e0b, #ec4899);
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
            color: #1e293b;
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
            background: white;
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
            background-color: #4f46e5;
            border-color: #4f46e5;
            padding: 0.5rem 1.25rem;
            font-weight: 500;
            letter-spacing: 0.2px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #4338ca;
            border-color: #4338ca;
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

        .chart-container {
            height: 300px;
            margin-bottom: 2rem;
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
            background: #4f46e5;
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
    </style>
@endpush

@section('content')

    <!-- Top Navbar -->
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('owner_manager_dashboard'), 'label' => 'Dashboard'],
            ['url' => '', 'label' => 'Finance']
        ]"
    />

    <!-- Side Navbar -->
    <x-Owner.side-navbar :openSections="['Finance']" />
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
                                        <p>Track your transactions and financial performance</p>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-primary d-flex align-items-center">
                                            <i class="fas fa-file-export me-2"></i> Export Report
                                        </button>
                                        <button class="btn btn-outline-primary d-flex align-items-center">
                                            <i class="fas fa-sliders-h me-2"></i> Settings
                                        </button>
                                    </div>
                                </div>

                                <!-- Summary Cards -->
                                <div class="row g-2 mb-2">
                                    <div class="col-md-4">
                                        <div class="summary-card">
                                            <h5>Total Revenue</h5>
                                            <div class="amount positive">PKR 0</div>
                                            <div class="trend">
                                                <i class="fas fa-arrow-up me-1 positive"></i>
                                                <span class="positive">0% from last month</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="summary-card">
                                            <h5>Total Expenses</h5>
                                            <div class="amount negative">PKR 0</div>
                                            <div class="trend">
                                                <i class="fas fa-arrow-down me-1 negative"></i>
                                                <span class="negative">0% from last month</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="summary-card">
                                            <h5>Net Profit</h5>
                                            <div class="amount positive">PKR 0</div>
                                            <div class="trend">
                                                <i class="fas fa-arrow-up me-1 positive"></i>
                                                <span class="positive">0% from last month</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Chart Section -->
                                <div class="finance-card p-4 mt-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="section-title mb-0">Financial Overview</h5>
                                        <select class="form-select w-25">
                                            <option>Last 30 Days</option>
                                            <option>Last 90 Days</option>
                                            <option>This Year</option>
                                        </select>
                                    </div>
                                    <div class="chart-container">
                                        <!-- Chart would be rendered here -->
                                        <div class="d-flex align-items-center justify-content-center h-100">
                                            <div class="text-center">
                                                <i class="fas fa-chart-line empty-state-icon"></i>
                                                <p class="">Revenue and expense chart will appear here</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Filter Section -->
                                <div class="filter-section mb-3">
                                    <h5 class="section-title mb-4">Transaction Filters</h5>
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label">Date Range</label>
                                            <select class="form-select">
                                                <option>Last 7 days</option>
                                                <option selected>Last 30 days</option>
                                                <option>Last 3 months</option>
                                                <option>Custom range</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Transaction Type</label>
                                            <select class="form-select">
                                                <option>All Transactions</option>
                                                <option>Income</option>
                                                <option>Expenses</option>
                                                <option>Transfers</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Status</label>
                                            <select class="form-select">
                                                <option>All Statuses</option>
                                                <option>Completed</option>
                                                <option>Pending</option>
                                                <option>Failed</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Amount Range</label>
                                            <select class="form-select">
                                                <option>Any Amount</option>
                                                <option>Under PKR 10,000</option>
                                                <option>PKR 10,000 - PKR 50,000</option>
                                                <option>Over PKR 50,000</option>
                                            </select>
                                        </div>
                                        <div class="col-md-12 d-flex justify-content-end gap-2 mt-4">
                                            <button class="btn btn-outline-secondary">
                                                <i class="fas fa-undo me-1"></i> Reset
                                            </button>
                                            <button class="btn btn-primary">
                                                <i class="fas fa-filter me-1"></i> Apply Filters
                                            </button>
                                        </div>
                                    </div>
                                </div>

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
                                                <x-transaction-card :transaction="$item" route-name="owner.finance.show"/>
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
                                            <p>You don't have any transactions yet. Add your first transaction to get started.</p>
                                            <button class="btn btn-primary">
                                                <i class="fas fa-plus me-1"></i> Create Transaction
                                            </button>
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
            const summaryCards = document.querySelectorAll('.summary-card');
            summaryCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = '';
                });
            });

            // You could add chart initialization code here
            // For example using Chart.js:
            // const ctx = document.getElementById('financeChart').getContext('2d');
            // new Chart(ctx, { ... });
        });
    </script>
@endpush
