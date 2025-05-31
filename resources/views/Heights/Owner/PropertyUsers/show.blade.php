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
        .unit-sale-rent-badge {
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
            background-color: var(--sidenavbar-body-color);
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
            justify-content: space-between;
            /*padding: 10px 10px;*/
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
            min-width: 110px;
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
            font-size: 0.7rem !important;
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

        .btn-warning {
            background-color: rgba(241, 196, 15, 0.1);
            color: #f39c12;
            border: 1px solid rgba(241, 196, 15, 0.2);
        }

        .btn-warning:hover {
            background-color: rgba(241, 196, 15, 0.2);
            color: #d35400;
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

                            <div class="filter-group">
                                <label for="type">Types</label>
                                <select name="type" id="type" class="form-select filter-select">
                                    <option value="">All Type</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type }}" {{ request('$type') == $type ? 'selected' : '' }}>
                                            {{ $type }}
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

                        <div class="unit-grid">
                            @forelse($userUnits ?? [] as $userUnit)
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
{{--                                        <div class="unit-sale-rent-badge">--}}
{{--                                            --}}
{{--                                        </div>--}}
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
                                            @if($userUnit->type === 'Rented')
                                                @if($userUnit->renew_canceled === 0)
                                                    <form action="" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="action-btn rented-status-btn btn-warning gap-1" title="Discontinue" onclick="return confirm('Are you sure you want to discontinue this rental?')">
                                                            <i class='bx bx-pause'></i> Discontinue
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="action-btn rented-status-btn btn-edit gap-1" title="Continue" onclick="return confirm('Are you sure you want to continue this rental?')">
                                                            <i class='bx bx-play'></i> Continue
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                        <p class="card-text"><i class='bx bx-buildings me-1'></i> {{ $unit->building->name ?? 'N/A' }}</p>
                                        <p class="card-text"><i class='bx bxs-layer me-1'></i> {{ $unit->level->level_name ?? 'N/A' }}</p>
                                        @if($userUnit->type === 'Sold')
                                        <p class="card-text"><i class='bx bx-calendar me-1'></i> Contract Date: {{ $userUnit->created_at ? \Carbon\Carbon::parse($userUnit->contract_date)->format('M d, Y') : 'N/A' }}</p>
                                        @elseif($userUnit->type === 'Rented')
                                            <p class="card-text"><i class='bx bx-calendar me-1'></i> Start Date: {{ $userUnit->subscription->created_at ? \Carbon\Carbon::parse($userUnit->contract_date)->format('M d, Y') : 'N/A' }}</p>
                                            <p class="card-text"><i class='bx bx-calendar me-1'></i> End Date: {{ $userUnit->subscription->ends_at ? \Carbon\Carbon::parse($userUnit->contract_date)->format('M d, Y') : 'N/A' }}</p>
                                        @endif

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
                                        <h4>No Units Found</h4>
                                        <p class="text-muted">This user doesn't have any rented or sold units associated with your organization.</p>
                                    </div>
                                </div>
                            @endforelse


                        </div>
                        <!-- Pagination -->
                        @if ($userUnits)
                            <div class="mt-4">
                                {{ $userUnits->appends(request()->query())->links('pagination::bootstrap-5') }}
                            </div>
                        @endif


                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection

@push('scripts')
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

        // Reset filters
        function resetFilters() {
            document.getElementById('search').value = '';
            document.getElementById('contract_type').value = '';
            document.getElementById('status').value = '';
            document.getElementById('filterForm').submit();
        }

        // View unit modal (you'll need to implement this)
        document.querySelectorAll('.view-unit').forEach(button => {
            button.addEventListener('click', function() {
                const unitId = this.getAttribute('data-id');
                // Implement your modal view logic here
                console.log('View unit:', unitId);
            });
        });
    </script>
@endpush
