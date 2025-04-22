@extends('layouts.app')

@section('title', $planDetails['plan_name'] . ' - Plan Details')

@section('styles')
    <style>
        .text-gradient-primary {
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .card {
            border-radius: 0.75rem;
            border: none;
        }

        .card-body{

        }


        .table th {
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
        }

        .table td {
            vertical-align: middle;
        }

        .list-group-item {
            border-left: none;
            border-right: none;
            transition: all 0.2s ease;
            background-color: var(--body-background-color) !important;
        }

        .list-group-item:hover {
            background-color: var(--body-background-color) !important;
        }

        .list-group-item:first-child {
            border-top: none;
        }

        .list-group-item:last-child {
            border-bottom: none;
        }

        .form-select-lg {
            padding: 0.75rem 1rem;
            font-size: 1rem;
        }

        .badge.bg-light {
            padding: 0.35em 0.65em;
            background-color: #f1f5f9;
            color: var(--main-text-color) !important;
        }

        .badge.text-white {
            padding: 0.35em 0.65em;
            font-weight: 500;
        }

        .btn-primary {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }

        .btn-primary:hover {
            background-color: #4338ca;
            border-color: #4338ca;
        }

        .btn-outline-secondary {
            border-color: #6c757d;
            color: #6c757d;
        }

        .btn-outline-secondary:hover {
            background-color: #6c757d;
            color: #fff;
        }

        .vr {
            border-left: 2px solid #dee2e6;
            height: 100%;
        }
    </style>
@endsection

@section('content')
    <x-Admin.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('admin_dashboard'), 'label' => 'Dashboard'],
            ['url' =>  route('plans.index'), 'label' => 'Plans'],
            ['url' => '', 'label' => 'Details']
        ]"
    />
    <x-Admin.side-navbar :openSections="['Plans']" />
    <x-error-success-model />

    <div id="main">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="container pt-5 pb-3 mt-2">

                        <div class="row mb-2">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h1 class="display-6 fw-bold text-gradient-primary mb-2">{{ $planDetails['plan_name'] }}</h1>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('plans.edit', $planDetails['plan_id']) }}" class="btn btn-primary px-4">
                                             Edit Plan
                                        </a>
                                        <a href="{{ route('plans.index') }}" class="btn btn-outline-secondary px-4">
                                             Back
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-4">
                                        <form method="GET" action="{{ route('plans.show', $planDetails['plan_id']) }}">
                                            <div class="row align-items-center">
                                                <div class="col-md-4 mb-3 mb-md-0">
                                                    <label class="form-label fw-semibold text-muted">Billing Cycle</label>
                                                    <select name="planCycle" class="form-select form-select-lg" onchange="this.form.submit()">
                                                        @foreach($billing_cycles as $cycle)
                                                            <option value="{{ $cycle->duration_months }}"
                                                                {{ $selected_cycle->duration_months == $cycle->duration_months ? 'selected' : '' }}>
                                                                {{ $cycle->duration_months }} Month{{ $cycle->duration_months > 1 ? 's' : '' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-md-end gap-3">
                                                        <div class="text-center text-md-end">
                                                            <span class="d-block text-muted small">Total Price</span>
                                                            <span class="display-5 fw-bold text-dark">
                                                            {{ number_format($planDetails['total_price'], 2) }} {{ $planDetails['currency'] }}
                                                        </span>
                                                        </div>
                                                        <div class="vr d-none d-md-block"></div>
                                                        <div class="text-center text-md-start">
                                                            <span class="d-block text-muted small">Billing Period</span>
                                                            <span class="h4 fw-bold text-primary">
                                                            {{ $selected_cycle->duration_months }} month{{ $selected_cycle->duration_months > 1 ? 's' : '' }}
                                                        </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-8">
                                <!-- Plan Description -->
                                <div class="card border-0 shadow-sm mb-4 plan-description-card">
                                    <div class="card-header border-bottom-0 pt-3 pb-0">
                                        <h5 class="fw-semibold mb-0 d-flex align-items-center" style="color: var(--main-text-color);">
                                            <i class="bx bx-file text-primary me-2"></i> Plan Description
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="p-3 rounded shadow-sm" style="background-color: var(--body-background-color) !important;">
                                            <p class="mb-0">{{ $planDetails['plan_description'] }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Included Services -->
                                <div class="card border-0 shadow-sm mb-4">
                                    <div class="card-header border-bottom-0 py-3">
                                        <h5 class="fw-semibold mb-0 d-flex align-items-center" style="color: var(--main-text-color);">
                                            <i class="bx bx-list-check text-primary me-2"></i> Included Services
                                        </h5>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="table-light">
                                                <tr>
                                                    <th width="5%" class="ps-4">#</th>
                                                    <th width="25%">Service</th>
                                                    <th width="45%">Description</th>
                                                    <th width="10%" class="text-center">Qty</th>
                                                    <th width="15%" class="text-end pe-4">Price</th>
                                                </tr>
                                                </thead>
                                                <tbody class="border">
                                                @foreach($planDetails['services'] as $index => $service)
                                                    <tr>
                                                        <td class="ps-4">{{ $index + 1 }}</td>
                                                        <td class="fw-semibold">{{ $service['service_name'] }}</td>
                                                        <td class="text-fade" >{{ $service['service_description'] }}</td>
                                                        <td class="text-center">{{ $service['service_quantity'] }}</td>
                                                        <td class="text-end pe-4 fw-semibold">
                                                            {{ number_format($service['price']['price'], 2) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column - Subscriptions -->
                            <div class="col-lg-4">
                                <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                                    <div class="card-header border-bottom-0 py-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="fw-semibold mb-0 d-flex align-items-center" style="color: var(--main-text-color);">
                                                <i class="bx bxs-crown text-primary me-2"></i> Subscriptions
                                            </h5>
                                            <span class="badge bg-primary rounded-pill" style="color: #fff !important;">{{ $subscriptions->count() }}</span>
                                        </div>
                                    </div>
                                    <div class="card-body border-top" >
                                        @if($subscriptions->count() > 0)
                                            <div class="list-group list-group-flush">
                                                @foreach($subscriptions as $subscription)
                                                    <div class="list-group-item border-0 px-2 py-3 mb-2 rounded shadow-sm" style="background-color: var(--body-background-color) !important;">
                                                        <div class="d-flex align-items-start">
                                                            <div class="position-relative me-3">
                                                                @if($subscription->organization && $subscription->organization->pictures->first())
                                                                    <img src="{{ asset(optional($subscription->organization->pictures->first())->file_path ?? 'img/organization_placeholder.png') }}" alt="Organization Logo"
                                                                         class="rounded-circle" width="50" height="50">
                                                                @else
                                                                    <div class="rounded-circle text-primary d-flex align-items-center justify-content-center"
                                                                         style="width: 48px; height: 48px;">
                                                                        <i class="bi bi-building fs-5"></i>
                                                                    </div>
                                                                @endif
                                                                <span class="position-absolute bottom-0 end-0 bg-{{ $subscription->subscription_status === 'active' ? 'success' : 'secondary' }} rounded-circle p-1 border border-2 border-white"></span>
                                                            </div>
                                                            <div class="flex-grow-1" style="color: var(--main-text-color) !important;">
                                                                <h6 class="fw-semibold mb-1">
                                                                    {{ $subscription->organization->name ?? 'Unknown Organization' }}
                                                                </h6>
                                                                <div class=" small" >
                                                                    <div class="d-flex align-items-center mb-1">
                                                                        <i class="bx bx-calendar me-2"></i>
                                                                        {{ $subscription->updated_at->format('M d, Y') }}
                                                                    </div>
                                                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                                                    <span class="d-flex align-items-center">
                                                                        <i class="bx bx-circle me-1"></i>
                                                                        <span class="me-1">{{ $subscription->billing_cycle }}</span>mo
                                                                    </span>
                                                                        <span class="d-flex align-items-center">
                                                                            <i class="bx bx-coin me-1"></i>
                                                                            {{ $subscription->price_at_subscription }} {{ $subscription->currency_at_subscription }}
                                                                        </span>
                                                                        <span class="badge bg-{{ $subscription->subscription_status === 'active' ? 'success' : 'secondary' }} text-white">
                                                                        {{ ucfirst($subscription->subscription_status) }}
                                                                    </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center pt-0 pb-2">
                                                <div class=" rounded-circle px-4 py-1 d-inline-block mb-3">
                                                    <i class="bx bxs-user-badge" style="font-size: 2rem;"></i>
                                                </div>
                                                <h6 class="fw-semibold">No subscriptions yet</h6>
                                                <p class="small mb-0">This plan hasn't been subscribed to by any organizations</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


