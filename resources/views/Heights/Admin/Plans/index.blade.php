@extends('layouts.app')

@section('title', 'Plans')

@section('styles')
    <style>
        .text-gradient-primary {
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .shadow-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .shadow-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
        }

        .transition-all {
            transition: all 0.3s ease;
        }

        .plan-description {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            min-height: 4.5em;
            color: var(--sidenavbar-text-color) !important;
            opacity: 0.5 !important;
        }
    </style>
@endsection

@section('content')

    <x-Admin.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('admin_dashboard'), 'label' => 'Dashboard'],
            ['url' =>  '', 'label' => 'Plans']
        ]"
    />
    <x-Admin.side-navbar :openSections="['Plans']" />
    <x-error-success-model />

    <div id="main">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="container pt-5 pb-3 mt-2">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="text-center text-md-start">
                                <h2 class="h4 fw-bold mb-0 text-gradient-primary">Manage Subscription Plans</h2>
                            </div>
                            <a href="{{ route('plans.create') }}" class="btn btn-primary rounded-pill px-4 py-2">
                                <i class=" me-2"></i> Add Plan
                            </a>
                        </div>

                        <div class="row justify-content-center mb-2">
                            <div class="col-lg-6">
                                <form method="GET" action="{{ route('plans.index') }}">
                                    <div class="card shadow-sm border-0">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <label for="planCycle" class="form-label mb-0 fw-semibold text-muted">Billing Cycle:</label>
                                                <div class="w-50">
                                                    <select name="planCycle" id="planCycle" class="form-select shadow-none border-0 bg-light" onchange="this.form.submit()">
                                                        @foreach($billing_cycles as $cycle)
                                                            <option value="{{ $cycle->duration_months }}"
                                                                {{ isset($selected_cycle) && $selected_cycle->duration_months == $cycle->duration_months ? 'selected' : '' }}>
                                                                {{ $cycle->duration_months }} Month{{ $cycle->duration_months > 1 ? 's' : '' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        @if($plans->isEmpty())
                            <div class="alert alert-warning text-center shadow-sm rounded-3">
                                <i class="bi bi-exclamation-circle me-2"></i> No plans available for the selected billing cycle.
                            </div>
                        @else
                            <div class="row row-cols-1 row-cols-md-3 g-4 mt-2">
                                @foreach($plans as $plan)
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                        <div class="card h-100 border-0 shadow shadow-hover overflow-hidden transition-all">
                                            <div class="card-body p-4 flex-grow-1 d-flex flex-column justify-content-between">
                                                <div class="mb-2">
                                                    <h3 class="h4 fw-bold text-primary plan-name">{{ $plan['plan_name'] }}</h3>
                                                    <p class="mb-0 plan-description" style="color: var(--sidenavbar-text-color);">{{ $plan['plan_description'] }}</p>
                                                </div>

                                                <div class="mt-auto">
                                                    <div class="d-flex align-items-end">
                                                        <span class="display-5 fw-bold text-dark total-price">{{ number_format($plan['total_price'], 0) }}</span>
                                                        <span class="text-muted ms-2 mb-2 currency">{{ $plan['currency'] }}</span>
                                                    </div>
                                                    <small class=" "  style="color: var(--sidenavbar-text-color);">per {{ $selected_cycle->duration_months }} month{{ $selected_cycle->duration_months > 1 ? 's' : '' }}</small>
                                                </div>

                                                <hr class="my-3" style="border-color: var(--topnavbar-border-color) !important;">

                                                <ul class="list-unstyled mb-4">
                                                    @foreach($plan['services'] as $service)
                                                        <li class="mb-3">
                                                            <div class="d-flex align-items-start">
                                                                <i class="bi bi-check-circle-fill text-success mt-1 me-2"></i>
                                                                <div>
                                                                    <span class="fw-semibold">
                                                                        {{ $service['service_quantity'] }}x {{ $service['service_name'] }}
                                                                    </span>
                                                                    @if($service['service_description'])
                                                                        <div class="service-description"  style="color: var(--sidenavbar-text-color);">{{ $service['service_description'] }}</div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>

                                            <div class="card-footer bg-transparent border-0 p-4 pt-0">
                                                <div class="d-grid gap-2">
                                                    <a href="{{ route('plans.show', ['id' => $plan['id'], 'planCycle' => $selected_cycle->duration_months]) }}"
                                                       class="btn btn-outline-primary rounded-pill py-2 view-details-btn">
                                                        <i class="fas fa-eye-slash me-2"></i> View Details
                                                    </a>

                                                    <a href="{{ route('plans.edit', $plan['id']) }}"
                                                       class="btn btn-sm btn-light rounded-pill py-2">
                                                        <i class="fas fa-edit me-1"></i> Edit Plan
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

