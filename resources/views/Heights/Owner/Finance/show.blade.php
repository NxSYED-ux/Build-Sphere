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
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: none;
            margin-bottom: 24px;
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 24px;
            border-radius: 12px 12px 0 0 !important;
        }
        .card-header h4, .card-header h5 {
            margin: 0;
            font-weight: 600;
            color: #1e293b;
        }
        .card-body {
            padding: 24px;
        }
        .list-group-item {
            border: none;
            border-bottom: 1px solid #f1f5f9;
            padding: 12px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .list-group-item:last-child {
            border-bottom: none;
        }
        .list-group-item strong {
            color: #64748b;
            font-weight: 500;
            min-width: 160px;
        }
        .badge {
            padding: 6px 10px;
            font-weight: 500;
            font-size: 0.8rem;
            border-radius: 8px;
        }
        .bg-success {
            background-color: #10b981 !important;
        }
        .bg-warning {
            background-color: #f59e0b !important;
        }
        .bg-danger {
            background-color: #ef4444 !important;
        }
        .section-title {
            color: #334155;
            font-weight: 600;
            margin-bottom: 16px;
            font-size: 1.1rem;
            position: relative;
            padding-left: 12px;
        }
        .section-title:before {
            content: '';
            position: absolute;
            left: 0;
            top: 4px;
            height: 16px;
            width: 4px;
            background-color: #3b82f6;
            border-radius: 4px;
        }
        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e2e8f0;
        }
        .info-card {
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 16px;
        }
        .info-card-title {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 8px;
            font-weight: 500;
        }
        .info-card-value {
            font-size: 1rem;
            color: #1e293b;
            font-weight: 600;
        }
        .back-btn {
            background-color: #3b82f6;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
        }
        .back-btn:hover {
            background-color: #2563eb;
            transform: translateY(-1px);
        }
        .divider {
            height: 1px;
            background-color: #e2e8f0;
            margin: 24px 0;
        }
        @media (max-width: 768px) {
            .card-body {
                padding: 16px;
            }
            .list-group-item {
                flex-direction: column;
                align-items: flex-start;
            }
            .list-group-item strong {
                margin-bottom: 4px;
            }
        }
    </style>
@endpush

<!-- Top Navbar -->
<x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('owner_manager_dashboard'), 'label' => 'Dashboard'],
            ['url' => route('owner.finance.index'), 'label' => 'Finance'],
            ['url' => '', 'label' => 'Details']
        ]"
/>

<!-- Side Navbar -->
<x-Owner.side-navbar :openSections="['Finance']" />
<x-error-success-model />

@section('content')
    <div id="main">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>Transaction Details</h4>
                            <span class="badge bg-{{ $transaction['status'] === 'completed' ? 'success' : ($transaction['status'] === 'failed' ? 'danger' : 'warning') }}">
                                {{ ucfirst($transaction['status']) }}
                            </span>
                        </div>

                        <div class="card-body">
                            @if (session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h5 class="section-title">Basic Information</h5>
                                    <div class="info-card">
                                        <div class="info-card-title">Transaction ID</div>
                                        <div class="info-card-value">{{ $transaction['transaction_id'] }}</div>
                                    </div>
                                    <div class="info-card">
                                        <div class="info-card-title">Title</div>
                                        <div class="info-card-value">{{ $transaction['title'] }}</div>
                                    </div>
                                    <div class="info-card">
                                        <div class="info-card-title">Type</div>
                                        <div class="info-card-value text-capitalize">{{ $transaction['type'] }}</div>
                                    </div>
                                    <div class="info-card">
                                        <div class="info-card-title">Amount</div>
                                        <div class="info-card-value">{{ $transaction['price'] }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h5 class="section-title">Additional Details</h5>
                                    <div class="info-card">
                                        <div class="info-card-title">Date</div>
                                        <div class="info-card-value">{{ $transaction['created_at'] }}</div>
                                    </div>
                                    <div class="info-card">
                                        <div class="info-card-title">Payment Method</div>
                                        <div class="info-card-value text-capitalize">{{ $transaction['payment_method'] }}</div>
                                    </div>
                                    <div class="info-card">
                                        <div class="info-card-title">Subscription</div>
                                        <div class="info-card-value">{{ $transaction['is_subscription'] ? 'Yes' : 'No' }}</div>
                                    </div>
                                </div>
                            </div>

                            @if($transaction['is_subscription'])
                                <div class="divider"></div>
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Subscription Details</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="info-card">
                                                    <div class="info-card-title">Start Date</div>
                                                    <div class="info-card-value">{{ $transaction['subscription_details']['start_date'] ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="info-card">
                                                    <div class="info-card-title">End Date</div>
                                                    <div class="info-card-value">{{ $transaction['subscription_details']['end_date'] ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="info-card">
                                                    <div class="info-card-title">Billing Cycle</div>
                                                    <div class="info-card-value text-capitalize">{{ $transaction['subscription_details']['billing_cycle'] ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($source)
                                <div class="divider"></div>
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Source Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="text-uppercase text-muted mb-3">Source: {{ $source_name }}</h6>

                                        @if($source_name === 'unit contract')
                                            <div class="row mb-4">
                                                <div class="col-md-12">
                                                    <h5 class="section-title">User Details</h5>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ $source->user->picture ? asset($source->user->picture) : asset('img/placeholder-profile.png') }}"
                                                             alt="User Picture" class="user-avatar me-3">
                                                        <div>
                                                            <div class="info-card-title">User</div>
                                                            <div class="info-card-value">{{ $source->user->name ?? 'N/A' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="info-card">
                                                        <div class="info-card-title">Email</div>
                                                        <div class="info-card-value">{{ $source->user->email ?? 'N/A' }}</div>
                                                    </div>
                                                </div>
                                            </div>

                                            @if($source->type === 'Sold')
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h5 class="section-title">Unit Details</h5>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="info-card">
                                                            <div class="info-card-title">Unit Name</div>
                                                            <div class="info-card-value">{{ $source->unit->unit_name ?? 'N/A' }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="info-card">
                                                            <div class="info-card-title">Unit Type</div>
                                                            <div class="info-card-value">{{ $source->unit->unit_type ?? 'N/A' }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="info-card">
                                                            <div class="info-card-title">Contract Price</div>
                                                            <div class="info-card-value">{{ $source->price ?? 'N/A' }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="info-card">
                                                            <div class="info-card-title">Purchase Date</div>
                                                            <div class="info-card-value">{{ $source->purchase_date ?? 'N/A' }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="info-card">
                                                            <div class="info-card-title">Unit Area</div>
                                                            <div class="info-card-value">{{ $source->unit->area ?? 'N/A' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif

                                        @if($source_name === 'subscription')
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="info-card">
                                                        <div class="info-card-title">Source Name</div>
                                                        <div class="info-card-value">{{ $source->source_name ?? 'N/A' }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="info-card">
                                                        <div class="info-card-title">Billing Cycle</div>
                                                        <div class="info-card-value">{{ $source->billing_cycle ? $source->billing_cycle . ' Months' : 'N/A' }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="info-card">
                                                        <div class="info-card-title">Contract Price</div>
                                                        <div class="info-card-value">{{ $source->price_at_subscription . ' ' . $source->currency_at_subscription }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="info-card">
                                                        <div class="info-card-title">Trial End Date</div>
                                                        <div class="info-card-value">{{ $source->ends_at }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="info-card">
                                                        <div class="info-card-title">Subscription Status</div>
                                                        <div class="info-card-value">{{ $source->subscription_status }}</div>
                                                    </div>
                                                </div>
                                            </div>

                                            @if($nested_source_name === 'unit contract')
                                                <div class="divider"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h5 class="section-title">User Details</h5>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="d-flex align-items-center">
                                                            <img src="{{ $nested_source->user->picture ? asset($source->user->picture) : asset('img/placeholder-profile.png') }}"
                                                                 alt="User Picture" class="user-avatar me-3">
                                                            <div>
                                                                <div class="info-card-title">User</div>
                                                                <div class="info-card-value">{{ $nested_source->user->name ?? 'N/A' }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="info-card">
                                                            <div class="info-card-title">Email</div>
                                                            <div class="info-card-value">{{ $nested_source->user->email ?? 'N/A' }}</div>
                                                        </div>
                                                    </div>
                                                </div>

                                                @if($source->type === 'Rent')
                                                    <div class="row mt-4">
                                                        <div class="col-md-12">
                                                            <h5 class="section-title">Unit Details</h5>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="info-card">
                                                                <div class="info-card-title">Unit Name</div>
                                                                <div class="info-card-value">{{ $nested_source->unit->unit_name ?? 'N/A' }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="info-card">
                                                                <div class="info-card-title">Unit Type</div>
                                                                <div class="info-card-value">{{ $nested_source->unit->unit_type ?? 'N/A' }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="info-card">
                                                                <div class="info-card-title">Contract Price</div>
                                                                <div class="info-card-value">{{ $nested_source->price ?? 'N/A' }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif

                                            @if($nested_source_name === 'plan')
                                                <div class="divider"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h5 class="section-title">Plan Details</h5>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="info-card">
                                                            <div class="info-card-title">Plan Name</div>
                                                            <div class="info-card-value">{{ $nested_source->name ?? 'N/A' }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="info-card">
                                                            <div class="info-card-title">Plan Currency</div>
                                                            <div class="info-card-value">{{ $nested_source->currency ?? 'N/A' }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="info-card">
                                                            <div class="info-card-title">Contract Status</div>
                                                            <div class="info-card-value">{{ $nested_source->status ?? 'N/A' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div class="d-flex justify-content-end mt-4">
                                <a href="{{ route('owner.finance.index') }}" class="btn back-btn">
                                    <i class="fas fa-arrow-left me-2"></i> Back to Transactions
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Add any necessary scripts here -->
@endpush
