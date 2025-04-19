@extends('layouts.app')

@section('title', 'Plans')

@section('content')
    <div class="container py-5">
        {{-- Header Section --}}
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div class="text-center text-md-start">
                <h1 class="display-5 fw-bold text-gradient-primary">Choose Your Perfect Plan</h1>
                <p class="lead text-muted">Select the billing cycle that works best for you</p>
            </div>
            <a href="{{ route('plans.create') }}" class="btn btn-primary rounded-pill px-4 py-2">
                <i class="bi bi-plus-lg me-2"></i> Add Plan
            </a>
        </div>

        {{-- Billing Cycle Selector --}}
        <div class="row justify-content-center mb-5">
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

        {{-- Plans List --}}
        @if($plans->isEmpty())
            <div class="alert alert-warning text-center shadow-sm rounded-3">
                <i class="bi bi-exclamation-circle me-2"></i> No plans available for the selected billing cycle.
            </div>
        @else
            <div class="row row-cols-1 row-cols-md-3 g-4">
                @foreach($plans as $plan)
                    <div class="col">
                        <div class="card h-100 border-0 shadow-hover overflow-hidden transition-all">
                            <div class="card-body p-4">
                                <div class="mb-4">
                                    <h3 class="h4 fw-bold text-primary">{{ $plan['plan_name'] }}</h3>
                                    <p class="text-muted mb-0">{{ $plan['plan_description'] }}</p>
                                </div>

                                <div class="mb-4">
                                    <div class="d-flex align-items-end">
                                        <span class="display-5 fw-bold text-dark">{{ number_format($plan['total_price'], 0) }}</span>
                                        <span class="text-muted ms-2 mb-2">{{ $plan['currency'] }}</span>
                                    </div>
                                    <small class="text-muted">per {{ $selected_cycle->duration_months }} month{{ $selected_cycle->duration_months > 1 ? 's' : '' }}</small>
                                </div>

                                <hr class="my-4">

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
                                                        <div class="text-muted small">{{ $service['service_description'] }}</div>
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
                                       class="btn btn-outline-primary rounded-pill py-2">
                                        <i class="bi bi-eye-fill me-2"></i> View Details
                                    </a>

                                    <a href="{{ route('plans.edit', $plan['id']) }}"
                                       class="btn btn-sm btn-light rounded-pill py-2">
                                        <i class="bi bi-pencil-fill me-1"></i> Edit Plan
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection

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
    </style>
@endsection
