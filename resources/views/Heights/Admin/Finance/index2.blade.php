@extends('layouts.app')

@section('title', 'Finance')

@push('styles')
    <style>
        body {
            background-color: #f8fafc;
        }
        #main {
            margin-top: 45px;
        }

        .finance-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .finance-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            background: white;
        }

        .finance-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        .transaction-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .summary-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .summary-card h5 {
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 0.5rem;
        }

        .summary-card .amount {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1e293b;
        }

        .positive {
            color: #10b981 !important;
        }

        .negative {
            color: #ef4444 !important;
        }

        .filter-section {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .custom-pagination-wrapper .pagination {
            justify-content: center;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #64748b;
        }

        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #cbd5e1;
        }

        @media (max-width: 768px) {
            .transaction-grid {
                grid-template-columns: 1fr;
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
        <section class="content mt-1 mb-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                            <div class="container mt-2">
                                <!-- Finance Header -->
                                <div class="finance-header">
                                    <div>
                                        <h3 class="mb-0">Financial Overview</h3>
                                        <p class="text-muted mb-0">Track your transactions and financial activity</p>
                                    </div>
                                    <div>
                                        <button class="btn btn-primary">
                                            <i class="fas fa-download me-1"></i> Export
                                        </button>
                                    </div>
                                </div>

                                <!-- Summary Cards -->
                                <div class="summary-cards">
                                    <div class="summary-card">
                                        <h5>Total Revenue</h5>
                                        <div class="amount positive">$12,345</div>
                                    </div>
                                    <div class="summary-card">
                                        <h5>Total Expenses</h5>
                                        <div class="amount negative">$3,210</div>
                                    </div>
                                    <div class="summary-card">
                                        <h5>Net Profit</h5>
                                        <div class="amount positive">$9,135</div>
                                    </div>
                                    <div class="summary-card">
                                        <h5>Pending Transactions</h5>
                                        <div class="amount">12</div>
                                    </div>
                                </div>

                                <!-- Filter Section -->
                                <div class="filter-section">
                                    <div class="row">
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
                                        <div class="col-md-3 d-flex align-items-end">
                                            <button class="btn btn-outline-primary w-100">
                                                <i class="fas fa-filter me-1"></i> Apply Filters
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Transactions -->
                                <div class="card finance-card">
                                    <div class="card-body">
                                        @if(count($history) > 0)
                                            <div class="transaction-grid">
                                                @foreach($history as $item)
                                                    <x-transaction-card :transaction="$item" />
                                                @endforeach
                                            </div>

                                            @if ($transactions)
                                                <div class="mt-4 custom-pagination-wrapper">
                                                    {{ $transactions->links('pagination::bootstrap-5') }}
                                                </div>
                                            @endif
                                        @else
                                            <div class="empty-state">
                                                <div class="empty-state-icon">
                                                    <i class="fas fa-wallet"></i>
                                                </div>
                                                <h4>No transactions found</h4>
                                                <p>Your transaction history will appear here once you have activity.</p>
                                                <button class="btn btn-primary mt-2">
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
            </div>
        </section>
    </div>

@endsection

@push('scripts')
    <script>
        // Add any interactive functionality here
        document.addEventListener('DOMContentLoaded', function() {
            // You could add filtering functionality, date pickers, etc.
            console.log('Finance dashboard loaded');
        });
    </script>
@endpush
