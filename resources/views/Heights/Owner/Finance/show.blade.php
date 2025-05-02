@extends('layouts.app')

@section('title', 'Transaction Details')

@push('styles')
    <style>
        :root {
            --primary-color: #4361ee;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-bg: #f8f9fa;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --border-radius: 12px;
        }

        #main {
            margin-top: 60px;
        }

        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            margin-bottom: 24px;
            overflow: hidden;
        }

        .card-header {
            background-color: var(--body-card-bg);
            padding: 20px 25px;
        }

        .card-header h4, .card-header h5 {
            margin: 0;
            font-weight: 600;
            color: var(--sidenavbar-text-color);
        }

        .card-body {
            padding: 25px;
        }

        .detail-section {
            margin-bottom: 5px;
        }

        .detail-section h5 {
            font-weight: 600;
            color: var(--sidenavbar-text-color);
            margin-bottom: 15px;
            font-size: 1.1rem;
            position: relative;
            padding-left: 15px;
        }

        .detail-section h5:before {
            content: '';
            position: absolute;
            left: 0;
            top: 5px;
            height: 18px;
            width: 4px;
            background: var(--primary-color);
            border-radius: 2px;
        }

        .detail-item {
            display: flex;
            margin-bottom: 12px;
            align-items: flex-start;
        }

        .detail-label {
            font-weight: 500;
            color: var(--sidenavbar-text-color);
            min-width: 160px;
        }

        .detail-value {
            font-weight: 500;
            color: var(--sidenavbar-text-color);
            flex: 1;
        }

        .badge {
            padding: 6px 10px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.75rem;
        }

        .badge-success {
            background-color: var(--success-color);
        }

        .badge-warning {
            background-color: var(--warning-color);
        }

        .badge-danger {
            background-color: var(--danger-color);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            box-shadow: 0 2px 10px rgba(67, 97, 238, 0.3);
        }

        .btn-primary:hover {
            background-color: #3a56d4;
            transform: translateY(-1px);
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .unit-image {
            width: 80px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 10px;
            margin-bottom: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .transaction-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .transaction-id {
            font-size: 0.9rem;
            color: var(--sidenavbar-text-color);
        }

        .transaction-amount {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--sidenavbar-text-color);
        }

        .divider {
            height: 1px;
            background-color: rgba(0, 0, 0, 0.05);
            margin: 25px 0;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--sidenavbar-text-color);
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 10px;
            color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .detail-item {
                flex-direction: column;
            }

            .detail-label {
                margin-bottom: 5px;
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
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <div class="transaction-header">
                                <div>
                                    <h4>Transaction Details</h4>
                                    <div class="transaction-id">ID: {{ $transaction['transaction_id'] }}</div>
                                </div>
                                <span class="badge bg-{{ $transaction['status'] === 'completed' ? 'success' : ($transaction['status'] === 'failed' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($transaction['status']) }}
                                </span>
                            </div>
                        </div>

                        <div class="card-body">

                            <div class="transaction-amount">
                                {{ $transaction['price'] }}
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="detail-section">
                                        <h5>Transaction Information</h5>
                                        <div class="detail-item">
                                            <div class="detail-label">Title:</div>
                                            <div class="detail-value">{{ $transaction['title'] }}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Type:</div>
                                            <div class="detail-value">{{ ucfirst($transaction['type']) }}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Date:</div>
                                            <div class="detail-value">{{ $transaction['created_at'] }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="detail-section">
                                        <h5>Payment Details</h5>
                                        <div class="detail-item">
                                            <div class="detail-label">Payment Method:</div>
                                            <div class="detail-value">{{ ucfirst($transaction['payment_method']) }}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Subscription:</div>
                                            <div class="detail-value">{{ $transaction['is_subscription'] ? 'Yes' : 'No' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($transaction['is_subscription'])
                                <div class="divider"></div>
                                <div class="detail-section">
                                    <h5><i class="fas fa-sync-alt"></i> Subscription Details</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="detail-item">
                                                <div class="detail-label">Start Date:</div>
                                                <div class="detail-value">{{ $transaction['subscription_details']['start_date'] ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-item">
                                                <div class="detail-label">End Date:</div>
                                                <div class="detail-value">{{ $transaction['subscription_details']['end_date'] ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-item">
                                                <div class="detail-label">Billing Cycle:</div>
                                                <div class="detail-value">{{ ucfirst($transaction['subscription_details']['billing_cycle']) ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($source)
                                <div class="divider"></div>
                                <div class="detail-section">
                                    @if($source_name === 'unit contract')
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="detail-section">
                                                    <h5><i class="fas fa-user"></i> User Details</h5>
                                                    <div class="detail-item">
                                                        <div class="detail-label">Name:</div>
                                                        <div class="detail-value">{{ $source->user->name ?? 'N/A' }}</div>
                                                    </div>
                                                    <div class="detail-item">
                                                        <div class="detail-label">Email:</div>
                                                        <div class="detail-value">{{ $source->user->email ?? 'N/A' }}</div>
                                                    </div>
                                                    <div class="detail-item">
                                                        <div class="detail-label">Picture:</div>
                                                        <div class="detail-value">
                                                            <img src="{{ $source->user->picture ? asset($source->user->picture) : asset('img/placeholder-profile.png') }}"
                                                                 alt="User Picture" class="user-avatar">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @if($source->type === 'Sold')
                                                <div class="col-md-6">
                                                    <div class="detail-section">
                                                        <h5><i class="fas fa-home"></i> Unit Details</h5>
                                                        <div class="detail-item">
                                                            <div class="detail-label">Unit Name:</div>
                                                            <div class="detail-value">{{ $source->unit->unit_name ?? 'N/A' }}</div>
                                                        </div>
                                                        <div class="detail-item">
                                                            <div class="detail-label">Unit Type:</div>
                                                            <div class="detail-value">{{ $source->unit->unit_type ?? 'N/A' }}</div>
                                                        </div>
                                                        <div class="detail-item">
                                                            <div class="detail-label">Price:</div>
                                                            <div class="detail-value">{{ $source->price ?? 'N/A' }}</div>
                                                        </div>
                                                        <div class="detail-item">
                                                            <div class="detail-label">Pictures:</div>
                                                            <div class="detail-value">
                                                                @forelse($source->unit->pictures ?? [] as $picture)
                                                                    <img src="{{ asset($picture->file_path) }}" class="unit-image" alt="Unit Picture">
                                                                @empty
                                                                    <img src="{{ asset('img/placeholder-img.jfif') }}" class="unit-image" alt="Unit Picture">
                                                                @endforelse
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    @if($source_name === 'subscription')
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="detail-section">
                                                    <h5><i class="fas fa-calendar-alt"></i> Subscription Info</h5>
                                                    <div class="row">
                                                        <div class="detail-item col-md-6">
                                                            <div class="detail-label">Source:</div>
                                                            <div class="detail-value">{{ $source->source_name ?? 'N/A' }}</div>
                                                        </div>
                                                        <div class="detail-item col-md-6">
                                                            <div class="detail-label">Billing Cycle:</div>
                                                            <div class="detail-value">{{ $source->billing_cycle ? $source->billing_cycle . ' Months' : 'N/A' }}</div>
                                                        </div>
                                                        <div class="detail-item col-md-6">
                                                            <div class="detail-label">Price:</div>
                                                            <div class="detail-value">{{ $source->price_at_subscription . ' ' . $source->currency_at_subscription }}</div>
                                                        </div>

                                                        <div class="detail-item col-md-6">
                                                            <div class="detail-label">Trial End:</div>
                                                            <div class="detail-value">{{ $source->ends_at }}</div>
                                                        </div>
                                                        <div class="detail-item col-md-6">
                                                            <div class="detail-label">Status:</div>
                                                            <div class="detail-value">{{ $source->subscription_status }}</div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if($nested_source_name === 'unit contract')
                                            <div class="divider"></div>
{{--                                            <h6 class="section-title">Related Unit Contract</h6>--}}
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="detail-section">
                                                        <h5><i class="fas fa-user"></i> User Details</h5>
                                                        <div class="detail-item">
                                                            <div class="detail-label">Name:</div>
                                                            <div class="detail-value">{{ $nested_source->user->name ?? 'N/A' }}</div>
                                                        </div>
                                                        <div class="detail-item">
                                                            <div class="detail-label">Email:</div>
                                                            <div class="detail-value">{{ $nested_source->user->email ?? 'N/A' }}</div>
                                                        </div>
                                                        <div class="detail-item">
                                                            <div class="detail-label">Picture:</div>
                                                            <div class="detail-value">
                                                                <img src="{{ $nested_source->user->picture ? asset($source->user->picture) : asset('img/placeholder-profile.png') }}"
                                                                     alt="User Picture" class="user-avatar">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="detail-section">
                                                        <h5><i class="fas fa-home"></i> Unit Details</h5>
                                                        <div class="detail-item">
                                                            <div class="detail-label">Unit Name:</div>
                                                            <div class="detail-value">{{ $nested_source->unit->unit_name ?? 'N/A' }}</div>
                                                        </div>
                                                        <div class="detail-item">
                                                            <div class="detail-label">Unit Type:</div>
                                                            <div class="detail-value">{{ $nested_source->unit->unit_type ?? 'N/A' }}</div>
                                                        </div>
                                                        <div class="detail-item">
                                                            <div class="detail-label">Price:</div>
                                                            <div class="detail-value">{{ $nested_source->price ?? 'N/A' }}</div>
                                                        </div>
                                                        <div class="detail-item">
                                                            <div class="detail-label">Pictures:</div>
                                                            <div class="detail-value">
                                                                @forelse($nested_source->unit->pictures ?? [] as $picture)
                                                                    <img src="{{ asset($picture->file_path) }}" class="unit-image" alt="Unit Picture">
                                                                @empty
                                                                    <img src="{{ asset('img/placeholder-img.jfif') }}" class="unit-image" alt="Unit Picture">
                                                                @endforelse
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if($nested_source_name === 'plan')
                                            <div class="divider"></div>
                                            <h6 class="section-title">Related Plan</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="detail-section">
                                                        <div class="detail-item">
                                                            <div class="detail-label">Plan Name:</div>
                                                            <div class="detail-value">{{ $nested_source->name ?? 'N/A' }}</div>
                                                        </div>
                                                        <div class="detail-item">
                                                            <div class="detail-label">Currency:</div>
                                                            <div class="detail-value">{{ $nested_source->currency ?? 'N/A' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="detail-section">
                                                        <div class="detail-item">
                                                            <div class="detail-label">Status:</div>
                                                            <div class="detail-value">{{ $nested_source->status ?? 'N/A' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @endif

                            <div class="divider"></div>
                            <div class="text-center mt-4">
                                <a href="{{ route('owner.finance.index') }}" class="btn btn-primary">
                                    <i class="fas fa-arrow-left mr-2"></i> Back to Transactions
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
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Inter font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
@endpush
