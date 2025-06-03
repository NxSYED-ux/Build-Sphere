@extends('layouts.app')

@section('title', 'Property User Details')

@push('styles')
    <style>
        body {
        }
        #main {
            margin-top: 45px;
        }

        /* User Profile Section */
        .user-profile-card {
            background: var(--sidenavbar-body-color);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            position: relative;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .user-profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .user-profile-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--sidenavbar-text-color);
            margin: 0;
        }

        .user-profile-actions {
            display: flex;
            gap: 12px;
        }

        .user-profile-content {
            display: flex;
            gap: 30px;
            align-items: center; /* This centers both avatar and details vertically */
        }

        .user-avatar-wrapper {
            position: relative;
            width: 140px;
            height: 140px;
            border-radius: 50%;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .user-avatar {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .user-avatar-hover {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            cursor: pointer;
        }

        .user-avatar-hover i {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }

        .user-avatar-hover span {
            font-size: 0.8rem;
        }

        .user-avatar-wrapper:hover .user-avatar-hover {
            opacity: 1;
        }

        .user-details-grid {
            display: flex;
            gap: 40px;
            flex-grow: 1;
            align-items: center; /* This centers the grid content vertically */
        }

        .user-details-column {
            flex: 1;
            min-width: 0;
        }

        .user-detail-row {
            display: flex;
            margin-bottom: 15px;
            align-items: center;
        }

        .user-detail-label {
            font-weight: 600;
            color: var(--sidenavbar-text-color);
            min-width: 100px;
            display: flex;
            align-items: center;
            opacity: 0.8;
        }

        .user-detail-value {
            color: var(--sidenavbar-text-color);
            font-weight: 500;
            word-break: break-word;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .user-profile-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .user-profile-actions {
                width: 100%;
                justify-content: flex-end;
            }

            .user-profile-content {
                flex-direction: column;
                align-items: center;
            }

            .user-details-grid {
                flex-direction: column;
                gap: 20px;
                width: 100%;
                align-items: flex-start;
            }

            .user-detail-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }

            .user-detail-label {
                min-width: auto;
            }
        }

        /* ================ FILTERS SECTION ================ */
        .filter-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 25px;
            background: var(--sidenavbar-body-color);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            align-items: flex-end;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            min-width: 220px;
        }

        .filter-group label {
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--sidenavbar-text-color);
            font-size: 0.9rem;
        }

        .filter-select, .search-input {
            width: 100%;
            padding: 10px 15px;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
            background-color: white;
            transition: all 0.3s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .search-input {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23999' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: 15px center;
            background-size: 16px 16px;
            padding-left: 40px;
        }

        .filter-buttons {
            display: flex;
            gap: 12px;
            margin-left: auto;
            align-self: center;
            margin-top: 30px;
        }

        .filter-buttons .btn {
            padding: 10px 15px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 120px;
        }

        th, td {
            white-space: nowrap;
        }

        .dataTables_wrapper {
            width: 100%;
            overflow-x: auto;
        }

        /* ================ Unit CARDS ================ */

        .unit-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        /* Card Styles */
        .unit-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: none;
            margin-bottom: 0;
            display: flex;
            flex-direction: column;
            height: 100%;
            background: var(--body-background-color);
        }

        .unit-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        /* Card Image */
        .unit-card .card-img-container {
            height: 180px;
            overflow: hidden;
            position: relative;
            background-color: #f8f9fa;
        }

        .unit-card .card-img-top {
            object-fit: cover;
            width: 100%;
            height: 100%;
            transition: transform 0.5s ease;
        }

        .unit-card:hover .card-img-top {
            transform: scale(1.05);
        }

        .unit-type-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .unit-status-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            /*background: rgba(0, 0, 0, 0.7);*/
            /*color: white;*/
            /*padding: 4px 10px;*/
            /*border-radius: 20px;*/
            /*font-size: 12px;*/
            /*font-weight: 600;*/
        }

        .unit-price-tag {
            position: absolute;
            bottom: 5px;
            left: 5px;
            background: rgba(0, 0, 0, 0.4);
            color: white;
            padding: 5px 15px;
            font-size: 14px;
            font-weight: 600;
        }
        .unit-availability-tag {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: rgba(0, 0, 0, 0.4);
            color: white;
            padding: 5px 15px;
            font-size: 14px;
            font-weight: 600;
        }

        /* Card Body */
        .unit-card .card-body {
            padding: 1.25rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            background-color: var(--body-background-color);
        }

        .unit-card .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--sidenavbar-text-color);
            word-break: break-word;
        }

        .unit-card .card-text {
            color: var(--sidenavbar-text-color);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            word-break: break-word;
        }

        /* Status Badges */
        .unit-card .badge-status {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
            border-radius: 50px;
            font-weight: 600;
            white-space: nowrap;
        }

        .unit-card .badge-under-review {
            background-color: #fff3cd;
            color: #856404 !important;
        }

        .unit-card .badge-active {
            background-color: #d4edda;
            color: #155724 !important;
        }

        .unit-card .badge-inactive {
            background-color: #f8d7da;
            color: #721c24 !important;
        }

        /* Action Buttons */
        .unit-card .action-buttons {
            display: flex;
            margin-top: auto;
            flex-wrap: wrap;
            gap: 2px;
        }

        .unit-card .action-btn {
            flex: 1;
            margin: 4px;
            padding: 8px 0;
            border-radius: 5px;
            font-size: 0.85rem;
        }

        /* Special Button Styles */
        .btn-add {
            padding: 10px 10px !important;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 100px;
            font-size: 0.95rem !important;
            text-decoration: none;
        }

        .rented-status-btn {
            padding: 5px 5px !important;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 100px;
            font-size: 14px !important;
            text-decoration: none;
        }

        .btn-view {
            background-color: rgba(52, 152, 219, 0.1);
            color: #3498db;
            border: 1px solid rgba(52, 152, 219, 0.2);
        }

        .btn-view:hover {
            background-color: rgba(52, 152, 219, 0.2);
            color: #2980b9;
        }

        .btn-edit {
            background-color: rgba(46, 204, 113, 0.1);
            color: var(--sage-green);
            border: 1px solid rgba(46, 204, 113, 0.2);
        }

        .btn-edit:hover {
            background-color: rgba(46, 204, 113, 0.2);
            color: #27ae60;
        }

        .btn-danger {
            background-color: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
            border: 1px solid rgba(231, 76, 60, 0.2);
        }

        .btn-danger:hover {
            background-color: rgba(231, 76, 60, 0.2);
            color: #c0392b;
        }

        .btn-discontinue {
            background-color: #f39c12;
            color: #ffff;
            border: 1px solid #f39c12;
        }

        .btn-discontinue:hover {
            background-color: #f39c12;
            color: #d35400;
        }

        .btn-continue {
            background-color: rgba(46, 204, 113, 0.2);
            color: #ffff;
            border: 1px solid rgba(46, 204, 113, 0.2);
        }

        .btn-continue:hover {
            background-color: rgba(46, 204, 113, 0.2);
            color: #27ae60;
        }


        /* Levels Button */
        .unit-card .levels-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            color: #7f8c8d;
            transition: all 0.2s ease;
            z-index: 1;
        }

        .unit-card .levels-btn:hover {
            background: white;
            color: #3498db;
            transform: scale(1.1);
        }

        /* ================ EMPTY STATE ================ */
        .empty-state {
            text-align: center;
            padding: 40px;
            background-color: #f8f9fa;
            border-radius: 12px;
            grid-column: 1 / -1;
        }

        .empty-state-icon {
            font-size: 3rem;
            color: #bdc3c7;
            margin-bottom: 15px;
        }

        /* ================ VIEW TOGGLE ================ */
        .grid-view-toggle {
            display: flex;
            align-items: center;
            margin-right: 15px;
        }

        .grid-view-toggle .btn {
            padding: 0.375rem 0.75rem;
        }

        /* ================ TABLE VIEW ================ */
        #tableView {
            display: none;
            margin-top: 0!important;
            padding-top: 0 !important;
        }

        /* ================ TABS STYLES ================ */
        .nav-tabs {
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            /*margin-bottom: 25px;*/
            display: flex;
            gap: 5px;
        }

        .nav-tabs .nav-link {
            border: none;
            color: var(--sidenavbar-text-color);
            font-weight: 600;
            padding: 12px 24px;
            margin-right: 0;
            border-radius: 8px 8px 0 0;
            transition: all 0.3s ease;
            position: relative;
            background: rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-bottom: none;
            display: flex;
            align-items: center;
            gap: 8px;
            opacity: 0.8;
        }

        .nav-tabs .nav-link:hover {
            color: var(--color-blue);
            background: rgba(0, 0, 0, 0.05);
            opacity: 1;
            transform: translateY(-2px);
        }

        .nav-tabs .nav-link.active {
            color: var(--color-blue);
            background: var(--sidenavbar-body-color);
            border-color: rgba(0, 0, 0, 0.05);
            border-bottom-color: var(--sidenavbar-body-color);
            opacity: 1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            position: relative;
            z-index: 1;
        }

        .nav-tabs .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--color-blue);
            border-radius: 2px 2px 0 0;
        }

        .nav-tabs .nav-link .badge {
            font-size: 0.7rem;
            padding: 4px 6px;
            background: rgba(0, 0, 0, 0.1);
            color: inherit;
            font-weight: 700;
        }

        .nav-tabs .nav-link.active .badge {
            background: var(--color-blue);
            color: white;
        }

        .tab-content {
            position: relative;
            background-color: var(--sidenavbar-body-color);
            padding: 5px 20px 20px 20px;
            border-radius: 0 0 8px 8px;
            z-index: 0;
        }

        .tab-pane {
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* For dark mode compatibility */
        @media (prefers-color-scheme: dark) {
            .nav-tabs .nav-link {
                background: rgba(255, 255, 255, 0.05);
                border-color: rgba(255, 255, 255, 0.1);
            }

            .nav-tabs .nav-link:hover {
                background: rgba(255, 255, 255, 0.1);
            }

            .nav-tabs .nav-link.active {
                background: var(--sidenavbar-body-color);
                border-color: rgba(255, 255, 255, 0.1);
                border-bottom-color: var(--sidenavbar-body-color);
            }
        }

        /* ================ RESPONSIVE ADJUSTMENTS ================ */
        @media (max-width: 1399.98px) {
            .buildings-grid {
                grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            }
        }

        @media (max-width: 1199.98px) {
            .buildings-grid {
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            }

            .unit-card .card-img-container {
                height: 170px;
            }
        }

        @media (max-width: 991.98px) {
            .buildings-grid {
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            }

            .filter-group {
                min-width: 180px;
            }
        }

        @media (max-width: 767.98px) {
            .buildings-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }

            .unit-card .card-img-container {
                height: 160px;
            }

            .filter-container {
                flex-direction: column;
            }

            .filter-buttons {
                margin-left: 0;
                width: 100%;
            }

            .filter-buttons .btn {
                flex: 1;
            }

            .user-profile-content {
                flex-direction: column;
                align-items: center;
            }

            .user-details {
                width: 100%;
            }
        }

        @media (max-width: 575.98px) {
            .buildings-grid {
                grid-template-columns: 1fr;
            }

            .unit-card .card-img-container {
                height: 200px;
            }

            .filter-group {
                min-width: 100%;
            }

            .unit-card .action-btn {
                flex: 1 1 100%;
            }
        }

        @media (max-width: 400px) {
            .unit-card .card-text {
                font-size: 0.85rem;
            }

            .unit-card .card-title {
                font-size: 1rem;
            }

            .unit-card .badge-status {
                font-size: 0.65rem;
            }

            .btn-add {
                min-width: 90px;
                font-size: 0.85rem !important;
                padding: 8px 5px !important;
            }
            .unit-card .action-btn {
                flex: 1 1 calc(50% - 4px);
            }
        }
    </style>
@endpush

@section('content')

    <!-- Top Navbar -->
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('owner_manager_dashboard'), 'label' => 'Dashboard'],
            ['url' => route('owner.property.users.index'), 'label' => 'Property Users'],
            ['url' => '', 'label' => $user->name ?? 'User Details']
        ]"
    />

    <!-- Side Navbar -->
    <x-Owner.side-navbar :openSections="['PropertyUsers']" />
    <x-error-success-model />

    <div id="main">

        <section class="content mt-3 mb-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">

                        <!-- User Profile Section -->
                        <div class="user-profile-card">
                            <div class="user-profile-header">
                                <h4 class="user-profile-title">{{ $user->name ?? 'N/A' }}</h4>
                                <div class="user-profile-actions">
                                    <a href="{{ route('owner.property.users.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i> Back
                                    </a>
                                    <a href="" class="btn btn-primary">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>
                                </div>
                            </div>

                            <div class="user-profile-content">
                                <div class="user-avatar-wrapper">
                                    <img src="{{ $user->picture ? asset($user->picture) : asset('img/default-user.png') }}"
                                         class="user-avatar"
                                         alt="User Avatar">
                                    <div class="user-avatar-hover">
                                        <i class="fas fa-camera"></i>
                                        <span>Change Photo</span>
                                    </div>
                                </div>

                                <div class="user-details-grid">
                                    <div class="user-details-column">
                                        <div class="user-detail-row">
                                            <span class="user-detail-label">
                                                <i class="fas fa-envelope me-2"></i>Email:
                                            </span>
                                            <span class="user-detail-value">{{ $user->email ?? 'N/A' }}</span>
                                        </div>

                                        <div class="user-detail-row">
                                            <span class="user-detail-label">
                                                <i class="fas fa-phone me-2"></i>Phone:
                                            </span>
                                            <span class="user-detail-value">{{ $user->phone_no ?? 'N/A' }}</span>
                                        </div>

                                        <div class="user-detail-row">
                                            <span class="user-detail-label">
                                                <i class="fas fa-id-card me-2"></i>CNIC:
                                            </span>
                                            <span class="user-detail-value">{{ $user->cnic ?? 'N/A' }}</span>
                                        </div>
                                    </div>

                                    <div class="user-details-column">
                                        <div class="user-detail-row">
                                            <span class="user-detail-label">
                                                <i class="fas fa-city me-2"></i>City:
                                            </span>
                                            <span class="user-detail-value">{{ $user->address->city ?? 'N/A' }}</span>
                                        </div>

                                        <div class="user-detail-row">
                                            <span class="user-detail-label">
                                                <i class="fas fa-venus-mars me-2"></i>Gender:
                                            </span>
                                            <span class="user-detail-value">{{ ucfirst($user->gender ?? 'N/A') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h3 class="mb-1">User Units</h3>
                        </div>

                        <!-- Filter Form -->
                        <form method="GET" id="filterForm" class="filter-container">
                            <div class="filter-group">
                                <label for="BuildingId">Building</label>
                                <select name="building_id" id="BuildingId" class="form-select filter-select">
                                    <option value="">All Buildings</option>
                                    @foreach($buildings as $building)
                                        <option value="{{ $building->id }}" {{ request('building_id') == $building->id ? 'selected' : '' }}>
                                            {{ $building->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="filter-group">
                                <label for="unit_id">Unit</label>
                                <select name="unit_id" id="unit_id" class="form-select filter-select">
                                    <option value="">All Units</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->unit_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="filter-buttons">
                                <button type="button" class="btn btn-secondary flex-grow-1 d-flex align-items-center justify-content-center" onclick="resetFilters()">
                                    <i class="fas fa-undo me-2"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary flex-grow-1 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-filter me-2"></i> Apply Filters
                                </button>
                            </div>
                        </form>

                        <!-- Tabs Navigation -->
                        <ul class="nav nav-tabs" id="unitsTab" role="tablist">
                            @php
                                $hasRented = $userUnits->where('type', 'Rented')->count() > 0;
                                $hasSold = $userUnits->where('type', 'Sold')->count() > 0;
                                $defaultTab = $hasRented ? 'rented' : ($hasSold ? 'sold' : 'rented');
                            @endphp

                            @if($hasRented)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $defaultTab === 'rented' ? 'active' : '' }}" id="rented-tab" data-bs-toggle="tab" data-bs-target="#rented" type="button" role="tab" aria-controls="rented" aria-selected="{{ $defaultTab === 'rented' ? 'true' : 'false' }}">
                                        Rented Units ({{ $userUnits->where('type', 'Rented')->count() }})
                                    </button>
                                </li>
                            @endif

                            @if($hasSold)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $defaultTab === 'sold' ? 'active' : '' }}" id="sold-tab" data-bs-toggle="tab" data-bs-target="#sold" type="button" role="tab" aria-controls="sold" aria-selected="{{ $defaultTab === 'sold' ? 'true' : 'false' }}">
                                        Sold Units ({{ $userUnits->where('type', 'Sold')->count() }})
                                    </button>
                                </li>
                            @endif
                        </ul>

                        <!-- Tabs Content -->
                        <div class="tab-content" id="unitsTabContent">
                            @if($hasRented)
                                <div class="tab-pane fade {{ $defaultTab === 'rented' ? 'show active' : '' }}" id="rented" role="tabpanel" aria-labelledby="rented-tab">
                                    <div class="unit-grid">
                                        @forelse($userUnits->where('type', 'Rented') as $userUnit)
                                            @php
                                                $unit = $userUnit->unit;
                                                $contractType = $userUnit->contract_type;
                                            @endphp
                                            <div class="unit-card">
                                                <div class="card-img-container">
                                                    @if(count($unit->pictures ?? []) > 0)
                                                        <img src="{{ asset($unit->pictures[0]->file_path) }}" class="card-img-top" alt="Unit Image">
                                                    @else
                                                        <img src="{{ asset('img/placeholder-img.jfif') }}" class="card-img-top" alt="Unit Image">
                                                    @endif
                                                    <div class="unit-type-badge">
                                                        {{ $unit->unit_type ?? 'N/A' }}
                                                    </div>
                                                    <div class="unit-status-badge">
                                                        @if($userUnit->renew_canceled === 1)
                                                            <button type="button" class="action-btn rented-status-btn btn-discontinue gap-1" title="Discontinue" onclick="updateContractStatus({{ $userUnit->id }}, 0)">
                                                                <i class='bx bx-pause'></i> Discontinue
                                                            </button>
                                                        @else
                                                            <button type="button" class="action-btn rented-status-btn btn-continue gap-1" title="Continue" onclick="updateContractStatus({{ $userUnit->id }}, 1)">
                                                                <i class='bx bx-play'></i> Continue
                                                            </button>
                                                        @endif
                                                    </div>
                                                    <div class="unit-price-tag">
                                                        PKR {{ $unit->price ?? 'N/A' }}
                                                    </div>
                                                    <div class="unit-availability-tag">
                                                        {{ $unit->availability_status ?? 'N/A' }}
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h5 class="card-title">{{ $unit->unit_name ?? 'N/A' }}</h5>
                                                        <a href="#"
                                                           class="btn btn-sm btn-warning contract-edit-btn rounded-circle py-1 px-0"  data-contract-id="{{ $userUnit->id }}"  style="color: #fff !important;"
                                                           data-bs-toggle="tooltip"
                                                           data-bs-placement="top"
                                                           title="Edit">
                                                            <x-icon name="edit" type="icon" size="16px" />
                                                        </a>
                                                    </div>
                                                    <p class="card-text"><i class='bx bx-buildings me-1'></i> {{ $unit->building->name ?? 'N/A' }}</p>
                                                    <p class="card-text"><i class='bx bxs-layer me-1'></i> {{ $unit->level->level_name ?? 'N/A' }}</p>
                                                    <p class="card-text"><i class='bx bx-time me-1'></i> Billing Cycle: {{ $userUnit->subscription->billing_cycle ?? 'N/A' }} Month</p>
                                                    <p class="card-text"><i class='bx bx-calendar me-1'></i> Start Date: {{ $userUnit->subscription->created_at ? \Carbon\Carbon::parse($userUnit->subscription->created_at)->format('M d, Y') : 'N/A' }}</p>
                                                    <p class="card-text"><i class='bx bx-calendar me-1'></i> Next Billing: {{ $userUnit->subscription->ends_at ? \Carbon\Carbon::parse($userUnit->subscription->ends_at)->format('M d, Y') : 'N/A' }}</p>
                                                    <p class="card-text"><i class='bx bx-money me-1'></i> Next Billing Amount: {{ $unit->price ?? 'N/A' }}</p>

                                                    <div class="action-buttons">
                                                        <a href="{{ route('owner.units.show', $unit->id) }}" class="action-btn btn-add btn-view view-unit gap-1" title="View">
                                                            <i class='bx bx-show'></i> View
                                                        </a>
                                                        <form action="#" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="action-btn btn-add btn-danger gap-1" title="Delete" onclick="return confirm('Are you sure you want to delete this unit?')">
                                                                <i class='bx bx-trash'></i> Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12">
                                                <div class="empty-state">
                                                    <div class="empty-state-icon">
                                                        <i class='bx bx-building-house'></i>
                                                    </div>
                                                    <h4>No Rented Units Found</h4>
                                                    <p class="text-muted">This user doesn't have any rented units associated with your organization.</p>
                                                </div>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            @endif

                            @if($hasSold)
                                <div class="tab-pane fade {{ $defaultTab === 'sold' ? 'show active' : '' }}" id="sold" role="tabpanel" aria-labelledby="sold-tab">
                                    <div class="unit-grid">
                                        @forelse($userUnits->where('type', 'Sold') as $userUnit)
                                            @php
                                                $unit = $userUnit->unit;
                                                $contractType = $userUnit->contract_type;
                                            @endphp
                                            <div class="unit-card">
                                                <div class="card-img-container">
                                                    @if(count($unit->pictures ?? []) > 0)
                                                        <img src="{{ asset($unit->pictures[0]->file_path) }}" class="card-img-top" alt="Unit Image">
                                                    @else
                                                        <img src="{{ asset('img/placeholder-img.jfif') }}" class="card-img-top" alt="Unit Image">
                                                    @endif
                                                    <div class="unit-type-badge">
                                                        {{ $unit->unit_type ?? 'N/A' }}
                                                    </div>
                                                    <div class="unit-price-tag">
                                                        PKR {{ $unit->price ?? 'N/A' }}
                                                    </div>
                                                    <div class="unit-availability-tag">
                                                        {{ $unit->availability_status ?? 'N/A' }}
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <h5 class="card-title">{{ $unit->unit_name ?? 'N/A' }}</h5>
                                                    </div>
                                                    <p class="card-text"><i class='bx bx-buildings me-1'></i> {{ $unit->building->name ?? 'N/A' }}</p>
                                                    <p class="card-text"><i class='bx bxs-layer me-1'></i> {{ $unit->level->level_name ?? 'N/A' }}</p>
                                                    <p class="card-text"><i class='bx bx-calendar me-1'></i> Contract Date: {{ $userUnit->created_at ? \Carbon\Carbon::parse($userUnit->created_at)->format('M d, Y') : 'N/A' }}</p>

                                                    <div class="action-buttons">
                                                        <a href="{{ route('owner.units.show', $unit->id) }}" class="action-btn btn-add btn-view view-unit gap-1" title="View">
                                                            <i class='bx bx-show'></i> View
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12">
                                                <div class="empty-state">
                                                    <div class="empty-state-icon">
                                                        <i class='bx bx-building-house'></i>
                                                    </div>
                                                    <h4>No Sold Units Found</h4>
                                                    <p class="text-muted">This user doesn't have any sold units associated with your organization.</p>
                                                </div>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection

@push('scripts')
    <script>
        function resetFilters() {
            window.location.href = '{{ route('owner.property.users.show', $user->id) }}';
        }
    </script>
    <script>
        // View toggle functionality
        document.getElementById('gridViewBtn').addEventListener('click', function() {
            document.getElementById('cardView').style.display = 'block';
            document.getElementById('tableView').style.display = 'none';
            this.classList.add('active');
            document.getElementById('tableViewBtn').classList.remove('active');
        });

        document.getElementById('tableViewBtn').addEventListener('click', function() {
            document.getElementById('cardView').style.display = 'none';
            document.getElementById('tableView').style.display = 'block';
            this.classList.add('active');
            document.getElementById('gridViewBtn').classList.remove('active');
        });

        // View unit modal (you'll need to implement this)
        document.querySelectorAll('.view-unit').forEach(button => {
            button.addEventListener('click', function() {
                const unitId = this.getAttribute('data-id');
                // Implement your modal view logic here
                console.log('View unit:', unitId);
            });
        });

        // Initialize Bootstrap tabs if needed
        if (typeof bootstrap !== 'undefined') {
            var tabElms = document.querySelectorAll('button[data-bs-toggle="tab"]');
            tabElms.forEach(function(tabEl) {
                new bootstrap.Tab(tabEl);
            });
        }
    </script>

    <script>
        async function updateContractStatus(contractId, value) {
            const action = value === 1 ? 'discontinue' : 'continue';

            try {
                const result = await Swal.fire({
                    title: 'Are you sure?',
                    text: `Are you sure you want to ${action} this rental?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: `Yes, ${action} it!`,
                    background: 'var(--body-background-color)',
                    color: 'var(--sidenavbar-text-color)',
                });

                if (result.isConfirmed) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                    const response = await fetch('{{ route("owner.property.users.contractStatus") }}', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            contract_id: contractId,
                            value: value
                        })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.error || 'Failed to update contract status');
                    }

                    await Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        background: 'var(--body-background-color)',
                        color: 'var(--sidenavbar-text-color)',
                    });

                    window.location.reload();
                }
            } catch (error) {
                Swal.fire({
                    title: 'Error!',
                    text: error.message || 'An unexpected error occurred',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    background: 'var(--body-background-color)',
                    color: 'var(--sidenavbar-text-color)',
                });
            }
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle contract edit button clicks
            document.querySelectorAll('.contract-edit-btn').forEach(button => {
                button.addEventListener('click', async function(e) {
                    e.preventDefault();

                    // Get the contract ID
                    const contractId = this.getAttribute('data-contract-id') ||
                        this.closest('[data-contract-id]')?.getAttribute('data-contract-id');

                    if (!contractId) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Contract ID not found',
                            confirmButtonColor: '#3085d6',
                            background: 'var(--body-background-color)',
                            color: 'var(--sidenavbar-text-color)',
                        });
                        return;
                    }

                    try {

                        // Make the fetch request
                        const response = await fetch(`{{ route('owner.property.users.contract.edit', ':id') }}`.replace(':id', contractId), {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        // Close loading SweetAlert
                        Swal.close();

                        if (!response.ok) {
                            const errorData = await response.json();
                            throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
                        }

                        const data = await response.json();

                        if (data.error) {
                            throw new Error(data.error);
                        }

                        // Create and show the edit modal
                        showEditContractModal(data.contract);

                    } catch (error) {
                        console.error('Error fetching contract:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message || 'Failed to load contract details',
                            confirmButtonColor: '#3085d6',
                            background: 'var(--body-background-color)',
                            color: 'var(--sidenavbar-text-color)',
                        });
                    }
                });
            });

            // Function to show the edit contract modal
            function showEditContractModal(contract) {
                // Create modal HTML
                const modalHtml = `
            <div class="modal fade" id="editContractModal" tabindex="-1" aria-labelledby="editContractModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-md modal-dialog-centered">
                    <div class="modal-content" style="background-color: var(--body-card-bg);">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editContractModalLabel" style="color: var(--sidenavbar-text-color)">Edit Contract</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editContractForm" method="POST" action="{{ route('owner.property.users.contract.update') }}">
                                @csrf
                                    @method('PUT')
                                    <input type="hidden" name="contract_id" value="${contract.id}">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="billingCycle" class="form-label">Billing Cycle (months)</label>
                                        <input type="number" class="form-control" id="billingCycle" name="billing_cycle"
                                               value="${contract.billing_cycle}" min="1" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="price" class="form-label">Price</label>
                                        <input type="number" class="form-control" id="price" name="price"
                                               value="${contract.price}" min="0" step="0.01" required>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" form="editContractForm" class="btn btn-primary" id="saveContractChanges">Save Changes</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

                // Add modal to DOM
                const modalContainer = document.createElement('div');
                modalContainer.innerHTML = modalHtml;
                document.body.appendChild(modalContainer);

                // Initialize modal
                const modal = new bootstrap.Modal(document.getElementById('editContractModal'));
                modal.show();

                // Handle form submission
                document.getElementById('editContractForm').addEventListener('submit', function(e) {
                    // Show loading state
                    const saveButton = document.getElementById('saveContractChanges');
                    saveButton.disabled = true;
                    saveButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';

                    // The form will submit normally and the page will refresh
                });

                // Clean up modal when closed
                document.getElementById('editContractModal').addEventListener('hidden.bs.modal', function() {
                    modalContainer.remove();
                });
            }
        });
    </script>
@endpush
