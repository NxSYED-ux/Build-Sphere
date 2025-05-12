@extends('layouts.app')

@section('title', 'Organizations')

@push('styles')
    <style>
        body {
        }
        #main {
            margin-top: 45px;
        }

        .nav-tabs .nav-link {
            background-color: var(--body-background-color) !important; /* Change to your desired color */
            color: var(--nav-tabs-inactive-text-color) !important;
            border-bottom: 1px solid var(--nav-tabs-inactive-border-color) !important; /* Corrected */
        }
        .nav-tabs .nav-link.active {
            background-color: var(--nav-tabs-active-bg-color) !important; /* Change to your desired color */
            color: var(--nav-tabs-active-text-color) !important;
            border-bottom: 2px solid #008CFF !important; /* Corrected */
        }

        /* DataTables Entries Dropdown */
        .dataTables_wrapper .dataTables_length select {
            background-color: var(--dataTable-paginate-menu-bg-color);
            color: var(--dataTable-paginate-menu-text-color);
            border: 1px solid var(--dataTable-paginate-menu-border-color);
        }
        .dataTables_wrapper .dataTables_length label {
            color: var(--dataTable-paginate-menu-label-color);
        }
        /* DataTables Search Box */
        .dataTables_wrapper .dataTables_filter input {
            background-color: var(--dataTable-search-input-bg-color);
            color: var(--dataTable-search-input-text-color);
            border: 1px solid var(--dataTable-search-input-border-color);
        }
        .dataTables_wrapper .dataTables_filter label {
            color: var(--dataTable-search-label-color);
        }
        .dataTables_filter input::placeholder {
            color: var(--dataTable-search-placeholder-color); /* Standard */
        }
        .dataTables_filter input::-webkit-input-placeholder {
            color: var(--dataTable-search-placeholder-color); /* WebKit browsers */
        }
        .dataTables_filter input::-moz-placeholder {
            color: var(--dataTable-search-placeholder-color); /* Mozilla Firefox 19+ */
        }
        .dataTables_filter input:-ms-input-placeholder {
            color: var(--dataTable-search-placeholder-color); /* Internet Explorer 10+ */
        }

        /* Card Styles */
        .detail-card {
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: none;
            transition: all 0.3s ease;
        }
        .detail-card:hover {
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        /* Form Styles */
        .form-section {
            background: var(--body-background-color);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .form-section h5 {
            color: var(--sidenavbar-text-color);
            margin-bottom: 1.5rem;
            font-weight: 600;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 0.75rem;
        }

        /* Plan Selection Styles */
        .plan-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        @media (max-width: 992px) {
            .plan-container {
                grid-template-columns: 1fr;
            }
        }

        .plan-card {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .plan-card:hover {
            border-color: #008CFF;
            background-color: var(--body-background-color);
            transform: translateY(-2px);
        }
        .plan-card.selected {
            border: 2px solid #008CFF;
            background-color: var(--sidenavbar-body-color);
        }
        .plan-card h4 {
            color: var(--sidenavbar-text-color);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .plan-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--sidenavbar-text-color);
        }
        .plan-cycle {
            color: var(--sidenavbar-text-color);
            font-size: 0.875rem;
        }
        .plan-features {
            margin-top: 1rem;
            flex-grow: 1;
        }
        .plan-feature {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
            color: #4b5563;
        }
        .plan-feature svg {
            color: #008CFF;
            margin-right: 0.5rem;
        }

        /* Image Upload Styles */
        .image-upload-container {
            border: 2px dashed #d1d5db;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            background: var(--sidenavbar-body-color);
        }
        .image-upload-container:hover {
            border-color: #008CFF;
        }
        .image-upload-container.drag-over {
            border-color: #008CFF;
            background-color: #f5f7ff;
        }
        .image-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 1rem;
        }
        .image-item {
            position: relative;
            width: 100px;
            height: 100px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .image-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .remove-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: rgba(239, 68, 68, 0.8);
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .remove-btn:hover {
            background-color: #ef4444;
        }

        .logo-badge {
            position: absolute;
            color: #ffff !important;
            top: 5px;
            left: 5px;
            font-size: 10px;
        }

        /* Selected Plan Details */
        .selected-plan-details {
            background-color: var(--body-card-bg);
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 1.5rem;
            border: 1px solid #e5e7eb;
        }
        .selected-plan-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }
        .selected-plan-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--sidenavbar-text-color);
        }
        .selected-plan-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--sidenavbar-text-color);
        }
        .selected-plan-features {
            color: var(--sidenavbar-text-color);
            margin-top: 1rem;
        }
        .selected-plan-feature {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
        }
        .selected-plan-feature svg {
            color: #008CFF;
            margin-right: 0.75rem;
            flex-shrink: 0;
        }

        /* Responsive Table */
        @media (max-width: 768px) {
            .responsive-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }

        /* Loading Overlay */
        #loadingOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: var(--body-background-color);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            display: none;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #e5e7eb;
            border-top-color: #008CFF;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .form-check-input:checked {
            background-color: var(--breadcrumb-text2-color); /* Bootstrap 'success' green */
            border-color: #198754;
        }

        #menu-icon {
            position: relative;
            z-index: 10; /* Adjust as needed */
        }

    </style>
@endpush

@section('content')

    <!--  -->
    <x-Admin.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('admin_dashboard'), 'label' => 'Dashboard'],
            ['url' => '', 'label' => 'Organizations']
        ]"
    />
    <!--  -->
    <x-Admin.side-navbar :openSections="['Organizations']" />
    <x-error-success-model />

    @php
        $activeTab = old('activeTab', $activeTab ?? 'Tab1');
    @endphp


        <!-- Loading Overlay -->
    <div id="loadingOverlay">
        <div class="spinner"></div>
    </div>

    <div id="main">

        <section class="content my-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box" style="overflow-x: auto;">
                            <div class="container mt-2">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link {{ $activeTab === 'Tab1' ? 'active' : '' }}" id="dropdwon-types-tab" data-bs-toggle="tab" href="#dropdwon-types" role="tab" aria-controls="dropdwon-types" aria-selected="{{ $activeTab === 'Tab1' ? 'true' : 'false' }}">Organization</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link {{ $activeTab === 'Tab2' ? 'active' : '' }}" id="dropdwon-values-tab" data-bs-toggle="tab" href="#dropdwon-values" role="tab" aria-controls="dropdwon-values" aria-selected="{{ $activeTab === 'Tab2' ? 'true' : 'false' }}">Add Organization</a>
                                    </li>
                                </ul>
                                <div class="tab-content mt-0" id="myTabContent">
                                    <!-- Organization Tab -->
                                    <div class="tab-pane fade {{ $activeTab === 'Tab1' ? 'show active' : '' }}" id="dropdwon-types" role="tabpanel" aria-labelledby="dropdwon-types-tab">
                                        <div class="card shadow px-3 pb-3 pt-0 mb-5 mt-0 bg-body rounded" style="border: none;">
                                            <div class="card-body py-0" style="position: relative; overflow-x: auto;">
                                                <div class="d-flex align-items-center position-absolute mt-0" style="top: 30px; left: 30px;">
                                                    <button class="btn btn-light" type="button" id="menu-icon" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <x-icon name="export" type="icon" class="" size="20px" />
                                                    </button>

                                                    <ul id="button-list" class="dropdown-menu dropdown-menu-end" style="position: absolute; top: 100%; left: 0;">
                                                        <li><button class="dropdown-item" type="button" id="copyButton">Copy</button></li>
                                                        <li><button class="dropdown-item" type="button" id="csvButton">CSV</button></li>
                                                        <li><button class="dropdown-item" type="button" id="excelButton">Excel</button></li>
                                                        <li><button class="dropdown-item" type="button" id="pdfButton">PDF</button></li>
                                                        <li><button class="dropdown-item" type="button" id="printButton">Print</button></li>
                                                    </ul>
                                                </div>
                                                <table id="organizationTable" class="table shadow-sm table-hover table-striped">
                                                    <thead class="shadow">
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Picture</th>
                                                            <th>Name</th>
                                                            <th>Owner</th>
                                                            <th>City</th>
                                                            <th>Online Payment</th>
                                                            <th>Status</th>
                                                            <th class="text-center" style="width: 70px;">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($organizations ?? [] as $organization)
                                                            <tr>
                                                                <td>{{ $organization->id }}</td>
                                                                <td>
                                                                    <img src="{{ $organization->logo ? asset($organization->logo) : asset('img/organization_placeholder.png') }}" alt="Organization Picture" class="rounded-circle" width="50" height="50">
                                                                </td>
                                                                <td>{{ $organization->name }}</td>
                                                                <td>{{ $organization->owner->name }}</td>
                                                                <td>{{ $organization->address->city ?? 'N/A' }}</td>
                                                                <td class="text-center" style="width: 150px !important;">
                                                                    <div class="form-check form-switch m-0 d-flex justify-content-center">
                                                                        <input type="hidden" name="is_online_payment_enabled" value="0">
                                                                        <input class="form-check-input" type="checkbox" role="switch" id="enable_online_payments"
                                                                               name="is_online_payment_enabled" value="{{ old('merchant_id', $organization->is_online_payment_enabled) }}"
                                                                               style="transform: scale(1.3);"
                                                                               {{ old('is_online_payment_enabled', $organization->is_online_payment_enabled ?? false) ? 'checked' : '' }}
                                                                               onchange="updateOnlinePaymentStatus(this, '{{ $organization->id }}', '{{ $organization->payment_gateway_merchant_id }}')">
                                                                    </div>
                                                                </td>
                                                                <td>{{ $organization->status }}</td>
                                                                <td class="text-center">
                                                                    <div class="d-flex justify-content-center align-items-center gap-3">
                                                                        <a href="{{ route('organizations.show', ['organization' => $organization->id]) }}" class="text-info" data-bs-toggle="tooltip" data-bs-placement="top" title="View">
                                                                            <x-icon name="view" type="icon" class="" size="20px" />
                                                                        </a>
                                                                        <a href="{{ route('organizations.edit', $organization->id) }}" class="text-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                                            <x-icon name="edit" type="icon" class="" size="20px" />
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="9" class="text-center">No organizations found.</td>
                                                                </tr>
                                                            @endforelse
                                                    </tbody>
                                                    @if ($organizations)
                                                        <div class="mt-3 custom-pagination-wrapper">
                                                            {{ $organizations->links('pagination::bootstrap-5') }}
                                                        </div>
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Add Organization Tab -->
                                    <div class="tab-pane fade {{ $activeTab === 'Tab2' ? 'show active' : '' }}" id="dropdwon-values" role="tabpanel" aria-labelledby="dropdwon-values-tab">
                                        <div class="card shadow py-2 px-1 mb-5 bg-body rounded" style="border: none;">
                                            <div class="card-body" style="overflow-x: auto;">
                                                <form action="{{ route('organizations.store') }}" method="POST" enctype="multipart/form-data" id="organizationForm">
                                                    @csrf

                                                    <input type="hidden" name="activeTab" value="Tab2">

                                                    <input type="hidden" name="plan_id" id="plan_id">
                                                    <input type="hidden" name="plan_cycle_id"  id="plan_cycle_id">
                                                    <input type="hidden" name="plan_cycle" id="plan_cycle">

                                                    <input type="hidden" id="selectedPlanId">
                                                    <input type="hidden" id="selectedPlanCycle">
                                                    <input type="hidden" id="selectedBillingCycleId">

                                                    <div class="row">
                                                        <!-- Left Column - Organization Details -->
                                                        <div class="col-lg-6 col-md-12">
                                                            <div class="form-section shadow-sm">
                                                                <h5><i class="fas fa-info-circle me-2"></i> Basic Information</h5>

                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="name" class="form-label">Organization Name</label>
                                                                        <span class="text-danger">*</span>
                                                                        <input type="text" name="name" id="name"
                                                                               class="form-control @error('name') is-invalid @enderror"
                                                                               value="{{ old('name') }}"
                                                                               maxlength="50"
                                                                               placeholder="Enter organization name"
                                                                               required>
                                                                        @error('name')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>

                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="owner_id" class="form-label">Owner</label>
                                                                        <span class="text-danger">*</span>
                                                                        <select class="form-select @error('owner_id') is-invalid @enderror"
                                                                                id="owner_id"
                                                                                name="owner_id"
                                                                                required>
                                                                            <option value="" disabled {{ old('owner_id') === null ? 'selected' : '' }}>Select Owner</option>
                                                                            @foreach($owners as $id => $name)
                                                                                <option value="{{ $id }}" {{ old('owner_id') == $id ? 'selected' : '' }}>
                                                                                    {{ $name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                        @error('owner_id')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="email" class="form-label">Email</label>
                                                                        <span class="text-danger">*</span>
                                                                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                                                                               value="{{ old('email') }}" maxlength="50" placeholder="i.e. org@gmail.com" required>
                                                                        @error('email')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>

                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="phone" class="form-label">Phone</label>
                                                                        <span class="text-danger">*</span>
                                                                        <input type="text" name="phone" id="phone_no" class="form-control @error('phone') is-invalid @enderror"
                                                                               value="{{ old('phone') }}" placeholder="0312-3456789" maxlength="14" required>
                                                                        @error('phone')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="stripe_merchant_id" class="form-label">
                                                                            Stripe Merchant ID <span class="text-danger" id="merchant-required-star" style="display: none;">*</span>
                                                                        </label>
                                                                        <div class="input-group">
                                                                            <span class="input-group-text" style="background-color: var(--sidenavbar-body-color);">
                                                                                <i class="fab fa-stripe text-primary"></i>
                                                                            </span>
                                                                            <input type="text"
                                                                                   name="merchant_id"
                                                                                   id="stripe_merchant_id"
                                                                                   class="form-control @error('merchant_id') is-invalid @enderror"
                                                                                   value="{{ old('merchant_id') }}"
                                                                                   placeholder="e.g. acct_1L9..."
                                                                                {{ old('is_online_payments_enabled') ? 'required' : '' }}>
                                                                        </div>
{{--                                                                        <small class="text-muted">Found in your Stripe Dashboard</small>--}}
                                                                        @error('merchant_id')
                                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>

                                                                    <div class="col-md-6 mb-3">
                                                                        <label class="form-label">
                                                                            Online Payments <span class="text-danger">*</span>
                                                                        </label>
                                                                        <div class="d-flex align-items-center mt-2">
                                                                            <label class="form-check-label me-3" for="enable_online_payments">
                                                                                Enable
                                                                            </label>
                                                                            <div class="form-check form-switch m-0">
                                                                                <input type="hidden" name="is_online_payment_enabled" value="0">
                                                                                <input class="form-check-input" type="checkbox" role="switch" id="enable_online_payments"
                                                                                       name="is_online_payment_enabled" value="1" style="transform: scale(1.3);"
                                                                                    {{ old('is_online_payment_enabled', $is_online_payment_enabled ?? false) ? 'checked' : '' }}>
                                                                            </div>
                                                                        </div>
                                                                        @error('is_online_payment_enabled')
                                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>


                                                                </div>
                                                            </div>

                                                            <div class="form-section shadow-sm">
                                                                <h5><i class="fas fa-map-marker-alt me-2"></i> Location Information</h5>

                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="country" class="form-label">Country</label>
                                                                        <span class="text-danger">*</span>
                                                                        <select class="form-select @error('country') is-invalid @enderror"
                                                                                id="country"
                                                                                name="country" required>
                                                                            <option value="" selected>Select Country</option>
                                                                        </select>
                                                                        @error('country')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>

                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="province" class="form-label">Province/State</label>
                                                                        <span class="text-danger">*</span>
                                                                        <select class="form-select @error('province') is-invalid @enderror"
                                                                                id="province"
                                                                                name="province" required>
                                                                            <option value="" selected>Select Province</option>
                                                                        </select>
                                                                        @error('province')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>

                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="city" class="form-label">City</label>
                                                                        <span class="text-danger">*</span>
                                                                        <select class="form-select @error('city') is-invalid @enderror"
                                                                                id="city"
                                                                                name="city" required>
                                                                            <option value="" selected>Select City</option>
                                                                        </select>
                                                                        @error('city')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>

                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="postal_code" class="form-label">Postal Code</label>
                                                                        <span class="text-danger">*</span>
                                                                        <input type="text" name="postal_code" id="postal_code"
                                                                               class="form-control @error('postal_code') is-invalid @enderror"
                                                                               value="{{ old('postal_code') }}"
                                                                               maxlength="20"
                                                                               placeholder="Enter postal code" required>
                                                                        @error('postal_code')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>

                                                                    <div class="col-md-12 mb-3">
                                                                        <label for="location" class="form-label">Street Address</label>
                                                                        <span class="text-danger">*</span>
                                                                        <input type="text" name="location" id="location"
                                                                               class="form-control @error('location') is-invalid @enderror"
                                                                               value="{{ old('location') }}"
                                                                               maxlength="100"
                                                                               placeholder="Enter street address" required>
                                                                        @error('location')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>


                                                                </div>
                                                            </div>

                                                            <div class="form-section shadow-sm">
                                                                <h5><i class="fas fa-image me-2"></i> Organization Pictures</h5>
                                                                <div class="alert alert-info mb-3">
                                                                    <strong><i class="fas fa-info-circle me-2"></i>Important:</strong>
                                                                    The first image you upload will be used as your organization's logo. Choose wisely!
                                                                    Maximum 5 images allowed.
                                                                </div>
                                                                <div class="image-upload-container" id="image-upload-container">
                                                                    <div class="d-flex flex-column align-items-center">
                                                                        <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
                                                                        <h6 class="mb-2">Drag & Drop Images Here</h6>
                                                                        <p class="mb-3">or</p>
                                                                        <label for="image-input" class="btn btn-primary">
                                                                            <i class="fas fa-folder-open me-2"></i> Browse Files
                                                                        </label>
                                                                        <input type="file" id="image-input" name="organization_pictures[]"
                                                                               accept="image/png, image/jpeg, image/jpg, image/gif"
                                                                               multiple hidden>
                                                                    </div>
                                                                    <div class="image-preview mt-3 text-center" id="image-preview">
                                                                    </div>
                                                                    @error('organization_pictures')
                                                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Right Column - Plan Selection -->
                                                        <div class="col-lg-6 col-md-12">
                                                            <div class="form-section shadow-sm">
                                                                <h5><i class="fas fa-cubes me-2"></i> Membership Plan</h5>

                                                                <div class="mb-4">
                                                                    <label for="billing-cycle" class="form-label mb-2">Billing Cycle</label>
                                                                    <div class="input-group">
                                                                        <select id="billing-cycle" name="billing-cycle" class="form-select">
                                                                            @forelse($planCycles as $planCycle)
                                                                                <option value="{{ $planCycle }}" {{ old('billing-cycle') == $planCycle ? 'selected' : '' }}>
                                                                                    {{ $planCycle }} Month
                                                                                </option>
                                                                            @empty
                                                                                <option value="">No Plans Available</option>
                                                                            @endforelse
                                                                        </select>
                                                                        <span class="input-group-text" style="background-color: var(--sidenavbar-body-color);">
                                                                            <i class="fas fa-calendar-alt"></i>
                                                                        </span>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-4">
                                                                    <label class="form-label mb-2">Available Plans</label>
                                                                    <div class="row g-3 plans-container" id="plans-container">
                                                                        <!-- Plans will be loaded here via JavaScript -->
                                                                        <div class="col-12 text-center py-4">
                                                                            <div class="spinner-border text-primary" role="status">
                                                                                <span class="visually-hidden">Loading...</span>
                                                                            </div>
                                                                            <p class="mt-2">Loading plans...</p>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="selected-plan-details shadow-sm" id="selected-plan-details">
                                                                    <div class="text-center py-4">
                                                                        <i class="fas fa-cube fa-3x text-muted mb-3"></i>
                                                                        <h5 class="text-muted">No Plan Selected</h5>
                                                                        <p class="text-muted">Please select a plan from above</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex justify-content-center justify-content-md-end mt-4">
                                                        <button type="submit" class="btn btn-primary px-4 py-2">
                                                            <i class="fas fa-save me-2"></i> Create Organization
                                                        </button>
                                                    </div>

                                                </form>
                                            </div>
                                        </div>
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

    <!-- Add DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>

    <!-- Add DataTables Buttons JS -->
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

    <!-- Data Tables Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var table = new DataTable("#organizationTable", {
                paging: false,
                info: false,
                dom: "Bfrtip",
                lengthChange: false,
                language: {
                    searchPlaceholder: "Search organization..."
                },
                buttons: [
                    {
                        extend: "csv",
                        text: "CSV",
                        className: "btn btn-secondary d-none"
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        className: "btn btn-secondary d-none"
                    },
                    {
                        extend: "pdf",
                        text: "PDF",
                        className: "btn btn-secondary d-none"
                    },
                    {
                        extend: "print",
                        text: "Print",
                        className: "btn btn-secondary d-none"
                    }
                ]
            });

            function triggerButton(buttonClass, logMessage) {
                console.log(logMessage);
                table.buttons(buttonClass).trigger();
            }

            document.getElementById("csvButton")?.addEventListener("click", function () {
                triggerButton(".buttons-csv", "CSV Button clicked");
            });

            document.getElementById("excelButton")?.addEventListener("click", function () {
                triggerButton(".buttons-excel", "Excel Button clicked");
            });

            document.getElementById("pdfButton")?.addEventListener("click", function () {
                triggerButton(".buttons-pdf", "PDF Button clicked");
            });

            document.getElementById("printButton")?.addEventListener("click", function () {
                triggerButton(".buttons-print", "Print Button clicked");
            });

            document.getElementById("colvisButton")?.addEventListener("click", function () {
                triggerButton(".buttons-colvis", "Column Visibility Button clicked");
            });
        });
    </script>

    <!-- Contact validation -->
    <script>
        document.getElementById('phone_no').addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,4})(\d{0,7})/);
            e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2];
        });
    </script>

    <!-- Tab Active -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const activeTab = "{{ $activeTab }}";
            if (activeTab === 'Tab2') {
                const tabTrigger = new bootstrap.Tab(document.querySelector('#tab_2'));
                tabTrigger.show();
            }
        });
    </script>

    <!-- Merchant id -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('enable_online_payments');
            const merchantInput = document.getElementById('stripe_merchant_id');
            const merchantStar = document.getElementById('merchant-required-star');

            function toggleMerchantRequirement() {
                if (toggle.checked) {
                    merchantInput.setAttribute('required', 'required');
                    merchantStar.style.display = 'inline';
                } else {
                    merchantInput.removeAttribute('required');
                    merchantStar.style.display = 'none';
                }
            }

            toggle.addEventListener('change', toggleMerchantRequirement);
            toggleMerchantRequirement(); // on page load
        });
    </script>

    <!-- Location Dropdowns -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const countrySelect = document.getElementById('country');
            const provinceSelect = document.getElementById('province');
            const citySelect = document.getElementById('city');

            const dropdownData = @json($dropdownData);

            // Populate Countries
            dropdownData.forEach(country => {
                const option = document.createElement('option');
                option.value = country.values[0]?.value_name || 'Unnamed Country';
                option.dataset.id = country.id;
                option.textContent = country.values[0]?.value_name || 'Unnamed Country';
                countrySelect.appendChild(option);
            });

            // Country Change Handler
            countrySelect.addEventListener('change', function () {
                provinceSelect.innerHTML = '<option value="" selected>Select Province</option>';
                citySelect.innerHTML = '<option value="" selected>Select City</option>';

                const selectedCountryId = this.options[this.selectedIndex]?.dataset.id;
                const selectedCountry = dropdownData.find(c => c.id == selectedCountryId);

                if (selectedCountry) {
                    selectedCountry.values.forEach(province => {
                        province.childs.forEach(childProvince => {
                            const option = document.createElement('option');
                            option.value = childProvince.value_name;
                            option.dataset.id = childProvince.id;
                            option.textContent = childProvince.value_name;
                            provinceSelect.appendChild(option);
                        });
                    });
                }
            });

            // Province Change Handler
            provinceSelect.addEventListener('change', function () {
                citySelect.innerHTML = '<option value="" selected>Select City</option>';

                const selectedCountryId = countrySelect.options[countrySelect.selectedIndex]?.dataset.id;
                const selectedCountry = dropdownData.find(c => c.id == selectedCountryId);

                if (selectedCountry) {
                    const selectedProvinceId = this.options[this.selectedIndex]?.dataset.id;
                    const selectedProvince = selectedCountry.values
                        .flatMap(province => province.childs)
                        .find(p => p.id == selectedProvinceId);

                    if (selectedProvince) {
                        selectedProvince.childs.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.value_name;
                            option.dataset.id = city.id;
                            option.textContent = city.value_name;
                            citySelect.appendChild(option);
                        });
                    }
                }
            });
        });
    </script>

    <!-- Plan Selection Logic -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const planCycleSelect = document.getElementById("billing-cycle");
            const plansContainer = document.getElementById("plans-container");
            const selectedPlanDetails = document.getElementById("selected-plan-details");
            const loadingOverlay = document.getElementById("loadingOverlay");

            let selectedPlan = null;
            let isInitialLoad = true; // Flag to track initial load

            function showLoading() {
                loadingOverlay.style.display = 'flex';
            }

            function hideLoading() {
                loadingOverlay.style.display = 'none';
            }

            function updatePriceDisplays(plan, cycleId) {
                if (!plan) return;

                // Update hidden form fields if needed
                const selectedPlanId = document.getElementById('selectedPlanId');
                const selectedPlanCycle = document.getElementById('selectedPlanCycle');
                const selectedBillingCycleId = document.getElementById('selectedBillingCycleId');

                const plan_id = document.getElementById('plan_id');
                const plan_cycle = document.getElementById('plan_cycle');
                const plan_cycle_id = document.getElementById('plan_cycle_id');

                selectedPlanId.value = plan.plan_id;
                selectedPlanCycle.value = cycleId;
                selectedBillingCycleId.value = plan.billing_cycle_id;

                plan_id.value = plan.plan_id;
                plan_cycle.value = cycleId;
                plan_cycle_id.value = plan.billing_cycle_id;
            }

            function fetchPlans(cycleId) {
                if (!cycleId) return;

                if (!isInitialLoad) { // Only show loading if not initial load
                    showLoading();
                }

                const url = `{{ route('plans.custom', ':planCycle') }}`.replace(':planCycle', cycleId);

                fetch(url, {
                    method: "GET",
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        const plans = data.plans || [];
                        renderPlans(plans);

                        if (selectedPlan) {
                            const selectedPlanObj = plans.find(p => p.plan_name === selectedPlan);
                            if (selectedPlanObj) {
                                updatePriceDisplays(selectedPlanObj, cycleId);
                            }
                        }
                    })
                    .catch(error => console.error("Error fetching plans:", error))
                    .finally(() => {
                        if (!isInitialLoad) { // Only hide loading if we showed it
                            hideLoading();
                        }
                        isInitialLoad = false; // Set flag to false after first load
                    });
            }

            function renderPlans(plans) {
                plansContainer.innerHTML = '';
                const cycleId = planCycleSelect.value;

                if (plans.length === 0) {
                    plansContainer.innerHTML = `
                <div class="col-12 text-center py-4">
                    <i class="fas fa-exclamation-circle fa-2x text-muted mb-3"></i>
                    <p class="">No plans available for this billing cycle</p>
                </div>
            `;
                    return;
                }

                plans.forEach(plan => {
                    const monthlyPrice = (plan.total_price / cycleId).toFixed(2);
                    const currency = plan.currency === 'PKR' ? 'Rs.' : '$';

                    const planCol = document.createElement('div');
                    planCol.className = 'col-md-6';

                    const planCard = document.createElement('div');
                    planCard.className = 'plan-card';
                    if (selectedPlan === plan.plan_name) {
                        planCard.classList.add('selected');
                    }
                    planCard.dataset.planId = plan.plan_name;

                    planCard.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h4 class="mb-0">${plan.plan_name}</h4>
                </div>
                <div class="plan-price">${currency}${monthlyPrice}<span class="plan-cycle">/month</span></div>

            `;

                    planCard.addEventListener('click', () => {
                        selectedPlan = plan.plan_name;
                        document.querySelectorAll('.plan-card').forEach(c => c.classList.remove('selected'));
                        planCard.classList.add('selected');
                        renderSelectedPlanDetails(plan);
                    });

                    planCol.appendChild(planCard);
                    plansContainer.appendChild(planCol);
                });

                // If no plan is selected, select the first one by default
                if (!selectedPlan && plans.length > 0) {
                    selectedPlan = plans[0].plan_name;
                    const firstCard = plansContainer.querySelector(`[data-plan-id="${selectedPlan}"]`);
                    if (firstCard) {
                        firstCard.classList.add('selected');
                        renderSelectedPlanDetails(plans[0]);
                    }
                }
            }

            function renderSelectedPlanDetails(plan) {
                const cycleId = planCycleSelect.value;
                const monthlyPrice = (plan.total_price / cycleId).toFixed(2);
                const totalPrice = plan.total_price.toFixed(2);
                const currency = plan.currency === 'PKR' ? 'Rs.' : '$';

                selectedPlanDetails.innerHTML = `
            <div class="selected-plan-header d-flex flex-column flex-sm-row align-items-sm-center justify-content-between">
                <div class="selected-plan-title">${plan.plan_name} Plan</div>
                <div class="selected-plan-price mt-2 mt-sm-0">${currency}${monthlyPrice}<span class="text-muted fs-6">/month</span></div>
            </div>

            <div class="selected-plan-features">
                <h6 class="fw-bold mb-3">Included Features:</h6>
                ${plan.services.map(service => `
                    <div class="selected-plan-feature">
                        <svg class="bi" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <div class="fw-medium">${service.service_quantity}x ${service.service_name}</div>
                            <small class="small">${service.service_description || 'No description available'}</small>
                        </div>
                    </div>
                `).join('')}
            </div>
            <div class="selected-plan-summary mt-4 pt-3 border-top">
                <div class="d-flex justify-content-between mb-2">
                    <span>Billing Cycle:</span>
                    <span class="fw-medium">${cycleId} Months</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Monthly Price:</span>
                    <span class="fw-medium">${currency}${monthlyPrice}</span>
                </div>
                <div class="d-flex justify-content-between fw-bold fs-5 mt-3 pt-2 border-top">
                    <span>Total:</span>
                    <span>${currency}${totalPrice}</span>
                </div>
            </div>
        `;

                updatePriceDisplays(plan, cycleId);
            }

            // Event listener for plan cycle change
            planCycleSelect.addEventListener("change", function () {
                fetchPlans(this.value);
            });

            // Initial fetch - won't show loading overlay
            if (planCycleSelect.value) {
                fetchPlans(planCycleSelect.value);
            } else if (planCycleSelect.options.length > 0) {
                planCycleSelect.selectedIndex = 0;
                fetchPlans(planCycleSelect.value);
            }

            // Form submission handler
            document.getElementById('organizationForm').addEventListener('submit', function(e) {
                if (!selectedPlan) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Plan Not Selected',
                        text: 'Please select a membership plan before submitting.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    </script>

    <!-- Images -->
    <script>
        // Image Upload Handling
        const imageInput = document.getElementById('image-input');
        const imagePreview = document.getElementById('image-preview');
        const uploadContainer = document.getElementById('image-upload-container');
        const MAX_IMAGES = 5;

        // This will store our actual selected files
        let selectedFiles = [];

        // Drag and Drop Events
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadContainer.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadContainer.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadContainer.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            uploadContainer.classList.add('drag-over');
        }

        function unhighlight() {
            uploadContainer.classList.remove('drag-over');
        }

        // Handle dropped files
        uploadContainer.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            handleFileSelection(files);
        }

        // Handle selected files
        imageInput.addEventListener('change', () => {
            handleFileSelection(imageInput.files);
        });

        function handleFileSelection(files) {
            if (!files || files.length === 0) return;

            // Check total images won't exceed limit
            const remainingSlots = MAX_IMAGES - selectedFiles.length;

            if (remainingSlots <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Maximum Images Reached',
                    text: `You've already uploaded the maximum of ${MAX_IMAGES} images. Remove some before adding more.`,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                return;
            }

            const filesArray = Array.from(files);
            const filesToAdd = filesArray.slice(0, remainingSlots);

            if (filesArray.length > remainingSlots) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Image Limit Exceeded',
                    text: `You can upload a maximum of ${MAX_IMAGES} images. Only ${remainingSlots} image(s) will be added.`,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            }

            // Add new files to our selectedFiles array
            selectedFiles = [...selectedFiles, ...filesToAdd];
            updateImagePreview();
            updateFileInput();
        }

        function updateImagePreview() {
            // Clear the preview
            imagePreview.innerHTML = '';

            if (selectedFiles.length === 0) {
                // imagePreview.innerHTML = '<p class="text-muted">No images selected</p>';
                return;
            }

            selectedFiles.forEach((file, index) => {
                if (!file.type.match('image.*')) return;

                const reader = new FileReader();
                reader.onload = (e) => {
                    const imageItem = document.createElement('div');
                    imageItem.classList.add('image-item');

                    if (index === 0) {
                        imageItem.classList.add('main-logo');
                        const badge = document.createElement('div');
                        badge.style.cssText = `
                        position: absolute;
                        top: 5px;
                        left: 5px;
                        z-index: 10;
                        width: 60px;
                        height: 20px;
                    `;
                        badge.innerHTML = `
                        <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                          <rect width="100%" height="100%" fill="#0d6efd" rx="3"/>
                          <text x="50%" y="50%" fill="white" font-size="10"
                                font-weight="bold" text-anchor="middle" dy=".3em">LOGO</text>
                        </svg>`;
                        imageItem.appendChild(badge);
                    }

                    const img = document.createElement('img');
                    img.src = e.target.result;

                    const removeBtn = document.createElement('button');
                    removeBtn.classList.add('remove-btn');
                    removeBtn.innerHTML = '×';
                    removeBtn.addEventListener('click', () => {
                        // Remove the file from our selectedFiles array
                        selectedFiles.splice(index, 1);
                        updateImagePreview();
                        updateFileInput();
                    });

                    imageItem.appendChild(img);
                    imageItem.appendChild(removeBtn);
                    imagePreview.appendChild(imageItem);
                };
                reader.readAsDataURL(file);
            });
        }

        function updateFileInput() {
            // Create a new DataTransfer object to hold our files
            const dataTransfer = new DataTransfer();

            // Add all selected files to it
            selectedFiles.forEach(file => {
                dataTransfer.items.add(file);
            });

            // Update the file input with our DataTransfer object
            imageInput.files = dataTransfer.files;
        }
    </script>

    <!-- Update payment status -->
    <script>
        function updateOnlinePaymentStatus(checkbox, organizationId, merchantId) {
            if (!merchantId) {
                checkbox.checked = !checkbox.checked;

                // Show SweetAlert error message
                Swal.fire({
                    icon: 'error',
                    title: 'Cannot Update',
                    text: 'Online payment cannot be enabled because the organization is not linked to any payment gateway.',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK',
                    background: 'var(--body-background-color)',
                    color: 'var(--sidenavbar-text-color)'
                });
                return;
            }

            const isEnabled = checkbox.checked ? 1 : 0;
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch("{{ route('organizations.onlinePaymentStatus.update') }}", {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    id: organizationId,
                    is_online_payment_enabled: isEnabled
                })
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        checkbox.checked = !checkbox.checked;
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.error,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        // Success - show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message || 'Online payment status updated successfully',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    checkbox.checked = !checkbox.checked;
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while updating the online payment status.',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                });
        }
    </script>

@endpush
