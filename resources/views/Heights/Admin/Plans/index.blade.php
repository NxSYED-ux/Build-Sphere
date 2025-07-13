@extends('layouts.app')

@section('title', 'Plans')

@section('styles')
    <style>

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

        .delete-btn-wrapper .btn {
            opacity: 0.5;
            transform: translate(5px, -5px);
            transition: all 0.2s ease;
        }
        .delete-btn-wrapper:hover .btn,
        .card:hover .delete-btn-wrapper .btn {
            opacity: 1;
            transform: translate(0, 0);
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
        <div class="container-fluid my-2">
            <div class="row">
                <div class="col-md-12">
                    <div class="container pt-5 pb-3 mt-2">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="text-center text-md-start">
                                <h4 class="mb-0">Manage Subscription Plans</h4>
                            </div>
                            <a href="{{ route('plans.create') }}" class="btn btn-primary hidden AdminAddPlans">
                                <i class="fas fa-plus me-2"></i> Add Plan
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
                                                        @foreach($billing_cycles ?? [] as $cycle)
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
                                @foreach($plans ?? [] as $plan)
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                        <div class="card h-100 border-0 shadow shadow-hover overflow-hidden transition-all">

                                            <div class="position-absolute end-0 top-0 p-3 delete-btn-wrapper">
                                                <button type="button"
                                                        class="btn btn-sm btn-icon-only btn-danger rounded-circle shadow-sm transition-all delete-plan-btn hidden AdminDeletePlan"
                                                        data-plan-id="{{ $plan['id'] }}"
                                                        data-plan-name="{{ $plan['plan_name'] }}"
                                                        title="Delete Plan">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>

                                            <div class="card-body p-4 flex-grow-1 d-flex flex-column justify-content-between">
                                                <div class="mb-2">
                                                    <h3 class="h4 fw-bold text-primary plan-name">{{ $plan['plan_name'] }}</h3>
                                                    <p class="mb-0 plan-description" style="color: var(--sidenavbar-text-color);">{{ $plan['plan_description'] }}</p>
                                                </div>

                                                <div class="mt-auto text-center">
                                                    <div class="d-flex flex-column align-items-center">
                                                        <div class="d-flex align-items-end justify-content-center">
                                                            <span class="display-5 fw-bold text-dark total-price">{{ number_format($plan['total_price'], 0) }}</span>
                                                            <span class="text-muted ms-2 mb-2 currency">{{ $plan['currency'] }}</span>
                                                        </div>
                                                        <small style="color: var(--sidenavbar-text-color);">
                                                            per {{ $selected_cycle->duration_months }} month{{ $selected_cycle->duration_months > 1 ? 's' : '' }}
                                                        </small>
                                                    </div>
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
                                                       class="btn btn-outline-primary rounded-pill py-2 view-details-btn hidden AdminViewPlansDetails">
                                                        <i class="fas fa-eye-slash me-2"></i> View Details
                                                    </a>

                                                    <a href="{{ route('plans.edit', $plan['id']) }}"
                                                       class="btn btn-sm btn-light rounded-pill py-2 hidden AdminEditPlans">
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const routeTemplate = "{{ route('plans.destroy', ['id' => '__ID__']) }}";

            document.querySelectorAll('.delete-plan-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const planId = this.getAttribute('data-plan-id');
                    const planName = this.getAttribute('data-plan-name');

                    const bgColor = getComputedStyle(document.documentElement).getPropertyValue('--swal-bg-color').trim();
                    const textColor = getComputedStyle(document.documentElement).getPropertyValue('--swal-text-color').trim();
                    const iconColor = getComputedStyle(document.documentElement).getPropertyValue('--swal-icon-warning-color').trim();

                    Swal.fire({
                        title: `Delete ${planName}?`,
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel',
                        backdrop: `
                        rgba(0,0,0,0.7)
                        url("/images/trash-icon.png")
                        center top
                        no-repeat
                    `,
                        background: bgColor,
                        color: textColor,
                        iconColor: iconColor,
                        customClass: {
                            popup: 'theme-swal-popup',
                            confirmButton: 'theme-swal-button',
                            cancelButton: 'theme-swal-cancel-button'
                        },
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const actionUrl = routeTemplate.replace('__ID__', planId);

                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = actionUrl;

                            const csrfToken = document.createElement('input');
                            csrfToken.type = 'hidden';
                            csrfToken.name = '_token';
                            csrfToken.value = "{{ csrf_token() }}";

                            const methodInput = document.createElement('input');
                            methodInput.type = 'hidden';
                            methodInput.name = '_method';
                            methodInput.value = 'DELETE';

                            form.appendChild(csrfToken);
                            form.appendChild(methodInput);
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>


@endpush
