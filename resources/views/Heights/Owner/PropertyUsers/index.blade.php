@extends('layouts.app')

@section('title', 'Property Users')

@push('styles')
    <style>
        body {
        }
        #main {
            margin-top: 50px;
        }

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
            min-width: 200px;
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

        @media (max-width: 768px) {
            .filter-group {
                min-width: 100%;
            }

            .filter-buttons {
                width: 100%;
                margin-left: 0;
                margin-top: 10px;
            }

            .filter-buttons .btn {
                flex-grow: 1;
            }
        }


        th, td {
            white-space: nowrap;
        }

        .modal-dialog {
            max-width: 400px;
            border-radius: 20px !important;
            overflow: hidden;
        }

        .modal-content {
            max-width: 400px;
            border-radius: 20px !important;
            overflow: hidden;
            box-shadow: none !important;
            border: 2px solid var(--modal-border);
        }

        .user-modal-content {
            border-radius: 20px !important;
            overflow: hidden;
        }

        .user-modal-dialog {
            border-radius: 20px !important;
        }

        #userModal h5{
            font-size: 15px;
            font-weight: 600;
            color: var(--modal-text);
            font-family: 'Montserrat', sans-serif;
        }

        #userEmail {
            display: inline-block;
            width: 170px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: middle;
        }

        #userModal span{
            font-size: 15px;
            color: var(--modal-text);
            font-family: 'Montserrat', sans-serif;
        }

        .user-modal-header {
            background: var(--modal-bg) !important;
            color: var(--modal-text) !important;
            font-family: 'Montserrat', sans-serif !important;
        }

        #userModalLabel{
            font-size: 18px !important;
            font-weight: bold !important;
        }

        .user-modal-body {
            background: var(--modal-bg) !important;
            color: var(--modal-text) !important;
            font-family: 'Montserrat', sans-serif !important;
        }

        .user-modal-footer {
            background: var(--modal-bg) !important;
            border-top: 1px solid var(--modal-border) !important;
        }

        .user-modal-close-btn {
            background: white;
            color: var(--modal-btn-text);
            border: 2px solid var(--modal-btn-bg);
            border-radius: 10px;
        }

        .user-modal-close-btn:hover {
            background: var(--modal-btn-bg);
            color: var(--modal-btn-text-hover);
            opacity: 0.8;
        }

        .user-close-btn {
            filter: invert(var(--invert, 0));
        }

        .user-img-border {
            border: 2px solid var(--modal-border);
        }

        /* User Card */
        .team-members {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .member-card {
            background: var(--sidenavbar-body-color);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            position: relative;
        }

        .member-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 10px rgba(0,0,0,0.1);
            border-color: #a5c4d4;
        }

        .member-header {
            padding: 20px;
            text-align: center;
            background: linear-gradient(135deg, var(--body-background-color) 0%, var(--sidenavbar-body-color) 100%);
        }

        .member-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
            margin-bottom: 15px;
        }

        .member-card:hover .member-avatar{
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            border-color: #e8c7c8;
        }

        .member-name {
            margin: 0;
            color: var(--sidenavbar-text-color);
            font-size: 1.3rem;
        }

        .member-position {
            margin: 5px 0 0;
            color: var(--sidenavbar-text-color);
            font-size: 0.9rem;
        }

        .member-details {
            padding: 15px 20px 0 20px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }
        .detail-item:last-child {
            margin-bottom: 5px;
        }

        .detail-icon {
            width: 24px;
            color: #3498db;
            text-align: center;
            margin-right: 10px;
        }

        .detail-text {
            flex: 1;
            color: var(--sidenavbar-text-color);
            font-size: 0.95rem;
        }

        .member-actions {
            display: flex;
            justify-content: space-between;
            padding: 10px 10px;
        }

        .btn-member {
            flex: 1;
            margin: 4px;
            padding: 8px 0;
            border-radius: 5px;
            font-size: 0.85rem;
        }

        .no-members {
            grid-column: 1 / -1;
            text-align: center;
            padding: 100px;
            color: #bdc3c7;
        }

        .btn-add {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 120px;
            font-size: 0.95rem;
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
            color: #27ae60;
            border: 1px solid rgba(46, 204, 113, 0.2);
        }

        .btn-edit:hover {
            background-color: rgba(46, 204, 113, 0.2);
            color: #27ae60;
        }

        .member-card-status {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-left: 4px;
            display: inline-block;
        }

        .member-details .enable-query-toggle-btn {
            position: relative;
            display: inline-block;
            width: 36px;
            height: 18px;
        }

        .member-details .enable-query-toggle-btn input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .member-details .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #e0e0e0;
            transition: .4s;
            border-radius: 18px;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
        }

        .member-details .toggle-slider:before {
            position: absolute;
            content: "";
            height: 14px;
            width: 14px;
            left: 2px;
            bottom: 2px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
            box-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }

        .member-details input:checked + .toggle-slider {
            background-color: #4CAF50;
        }

        .member-details input:checked + .toggle-slider:before {
            transform: translateX(18px);
        }

        .member-details input:focus + .toggle-slider {
            box-shadow: 0 0 1px #4CAF50;
        }



        .member-actions-dropdown {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
        }

        .dropdown-toggle-btn {
            background: transparent;
            border: none;
            color: var(--sidenavbar-text-color);
            padding: 5px 8px;
            transition: all 0.3s ease;
        }

        .dropdown-toggle-btn:hover {
            background: rgba(0,0,0,0.05);
            transform: scale(1.1);
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-radius: 8px;
            background-color: var(--sidenavbar-body-color);
            overflow: hidden;
        }

        .dropdown-item {
            padding: 8px 16px;
            font-size: 0.9rem;
            color: var(--sidenavbar-text-color);
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            color: var(--sidenavbar-text-color);
            background-color: rgba(0,0,0,0.05);
        }

        .dropdown-item.delete-item:hover {
            background-color: rgba(220, 53, 69, 0.1);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .member-card {
            animation: fadeInUp 0.5s ease forwards;
            opacity: 0;
        }

        .member-card:nth-child(1) { animation-delay: 0.1s; }
        .member-card:nth-child(2) { animation-delay: 0.2s; }
        .member-card:nth-child(3) { animation-delay: 0.3s; }
        .member-card:nth-child(4) { animation-delay: 0.4s; }
        .member-card:nth-child(5) { animation-delay: 0.5s; }
        .member-card:nth-child(6) { animation-delay: 0.6s; }
        .member-card:nth-child(7) { animation-delay: 0.7s; }
        .member-card:nth-child(8) { animation-delay: 0.8s; }

    </style>
@endpush

@section('content')

    <!-- Top Navbar -->
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('owner_manager_dashboard'), 'label' => 'Dashboard'],
            ['url' => '', 'label' => 'Property Users']
        ]"
    />

    <!-- Side Navbar -->
    <x-Owner.side-navbar :openSections="['PropertyUsers']" />
    <x-error-success-model />

    <div id="main">

        <section class="content mt-1 mb-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h3 class="mb-1">Property Users</h3>
                            <a href="{{ route('owner.assignunits.index') }}" class="btn btn-primary">
                                <i class="bx bxs-user-check me-1"></i>
                                Assign Unit
                            </a>
                        </div>

                        <!-- Filter Form -->
                        <form method="GET" id="filterForm" class="filter-container">
                            <div class="filter-group">
                                <label for="search">Search</label>
                                <input type="text" name="search" id="search" class="search-input"
                                       placeholder="Search by name or email"
                                       value="{{ request('search') }}">
                            </div>

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
                                    <i class="fas fa-filter me-2"></i> Apply
                                </button>
                            </div>
                        </form>

                        <!-- Staff Cards -->
                        <div class="team-members">
                            @forelse($users as $user)
                                <div class="member-card">
                                    <div class="member-header">
                                        <img src="{{ $user->picture ? asset($user->picture) : asset('img/placeholder-profile.png') }}"
                                             alt="{{ $user->name }}"
                                             class="member-avatar">
                                        <h3 class="member-name">{{ $user->name }}</h3>
                                    </div>
                                    <div class="member-details">
                                        <div class="detail-item">
                                            <i class="fas fa-envelope detail-icon"></i>
                                            <div class="detail-text">
                                                <a class="text-decoration-none" href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                            </div>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-phone detail-icon"></i>
                                            <div class="detail-text">
                                                {{ $user->phone_no ?? 'Not provided' }}
                                            </div>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-id-card detail-icon"></i>
                                            <div class="detail-text">
                                                {{ $user->cnic ?? 'Not provided' }}
                                            </div>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-home detail-icon"></i>
                                            <div class="detail-text">
                                                Buy Units: <strong>{{ $user->sold_units_count ?? 0 }}</strong>
                                            </div>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-key detail-icon"></i>
                                            <div class="detail-text">
                                                Rented Units: <strong>{{ $user->rented_units_count ?? 0 }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="member-actions">
                                        <a href="{{ route('owner.property.users.show', $user->id) }}" class="btn btn-add btn-sm btn-view view-user btn-member gap-1" title="View Details">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="javascript:void(0);" class="btn btn-add btn-sm btn-edit btn-member gap-1" title="Edit Manager">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="no-members">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <h4>No users found</h4>
                                    <p>There are currently no users matching your filters.</p>
                                </div>
                            @endforelse
                        </div>


                        <!-- Pagination -->
                        @if ($users)
                            <div class="mt-4">
                                {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}
                            </div>
                        @endif



                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- User Details Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content shadow-lg user-modal-content">
                <!-- Header -->
                <div class="modal-header user-modal-header position-relative">
                    <h5 class="modal-title fw-bold w-100 text-center" id="userModalLabel">User Details</h5>
                </div>

                <!-- Body -->
                <div class="modal-body user-modal-body">
                    <div class="d-flex flex-column align-items-center justify-content-center mb-3">
                        <div class="d-flex align-items-center">
                            <img id="userPicture" src="" alt="User Picture" class="img-fluid rounded-circle shadow-sm border user-img-border"
                                 style="width: 140px; height: 140px; object-fit: cover;">
                            <div class="ms-3" style="padding-left: 10px !important;">
                                <h5 id="userName" class="mb-1"></h5>
                                <p class="mb-0"><span id="userRole"></span> <span id="userStatus" class="rounded-circle d-inline-block mt-2 mx-2" style="width: 10px; height: 10px;"></span></p>
                            </div>
                        </div>
                    </div>

                    <div class="container">
                        <div class="row px-3">
                            <div class="col-7 mb-2">
                                <h5>Email</h5>
                                <span id="userEmail"></span>
                            </div>
                            <div class="col-5 mb-2">
                                <h5>Gender</h5>
                                <span id="userGender"></span>
                            </div>
                            <div class="col-7 mb-2">
                                <h5>CNIC</h5>
                                <span id="userCnic"></span>
                            </div>
                            <div class="col-5 mb-2">
                                <h5>Phone no</h5>
                                <span id="userPhone"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer user-modal-footer">
                    <button type="button" class="btn user-modal-close-btn w-100" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function resetFilters() {
            window.location.href = '{{ route("owner.property.users.index") }}';
        }
    </script>
@endpush
