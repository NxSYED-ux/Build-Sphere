@extends('layouts.app')

@section('title', 'Memberships')

@push('styles')
    <style>
        body {
        }
        #main {
            margin-top: 45px;
        }

        .filter-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
            background: var(--sidenavbar-body-color);
        }

        /* All filter groups have equal width */
        .filter-group {
            flex: 1 0 calc(20% - 15px);
            min-width: 180px;
            max-width: 100%;
        }

        /* Buttons container matches filter width */
        .filter-buttons {
            flex: 1 0 calc(20% - 15px);
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            min-width: 180px;
            align-items: flex-end;
        }

        .filter-group label {
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--sidenavbar-text-color);
            font-size: 0.9rem;
        }

        /* Unified form controls */
        .form-control {
            width: 100%;
            height: 40px;
            padding: 10px 15px;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
            background-color: white;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            font-size: 0.9rem;
            color: #333;
            appearance: none;
        }

        .search-input {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23999' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: 15px center;
            background-size: 16px 16px;
            padding-left: 40px;
        }

        select.form-select {
            padding: 7px;
        }

        .filter-buttons .btn {
            height: 40px;
            padding: 0 15px;
            border-radius: 6px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 120px;
            font-size: 0.9rem;
        }

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .filter-group,
            .filter-buttons {
                flex: 1 0 calc(25% - 15px); /* 4 items per row */
            }
        }

        @media (max-width: 992px) {
            .filter-group,
            .filter-buttons {
                flex: 1 0 calc(33.333% - 15px); /* 3 items per row */
            }
        }

        @media (max-width: 768px) {
            .filter-group,
            .filter-buttons {
                flex: 1 0 calc(50% - 15px); /* 2 items per row */
            }
        }

        @media (max-width: 576px) {
            .filter-group,
            .filter-buttons {
                flex: 1 0 100%; /* 1 item per row */
            }
            .filter-buttons {
                flex-direction: column;
            }
            .filter-buttons .btn {
                width: 100%;
            }
        }

        /* Memberships Cards */
        /* Memberships Cards - Compact Design with Category/Status */
        .membership-card {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            margin-bottom: 25px;
            background: var(--body-card-bg);
            height: 100%;
            display: flex;
            flex-direction: column;
            border: none;
            position: relative;
            border: 1px solid rgba(0,0,0,0.03);
        }
        .membership-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        .membership-card .membership-img-container {
            height: 150px;
            overflow: hidden;
            position: relative;
        }
        .membership-card .membership-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .membership-card:hover .membership-img {
            transform: scale(1.05);
        }
        .membership-card .membership-body {
            padding: 15px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .membership-card .membership-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--sidenavbar-text-color);
            margin-bottom: 10px;
            line-height: 1.3;
            font-family: 'Poppins', sans-serif;
        }

        .membership-card .membership-description {
            color: var(--sidenavbar-text-color);
            line-height: 1.5;
            flex-grow: 1;
            font-size: 0.95rem;
            margin-bottom: 0 !important;
            opacity: 0.8;
        }

        .membership-card .price-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 8px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .membership-card .membership-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
        }
        .membership-card .membership-period {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.8);
            font-weight: 500;
        }
        .membership-card .original-price {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.7);
            text-decoration: line-through;
            font-weight: 500;
            margin-right: 5px;
        }
        .membership-card .discount-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.75rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            z-index: 2;
        }
        .membership-card .popular-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.75rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Action Menu (Three Dots) */
        .membership-card .action-menu {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 3;
        }
        .membership-card .action-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--sidenavbar-body-color);
            color: var(--sidenavbar-text-color);
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .membership-card .action-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }
        .membership-card .dropdown-menu {
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border: none;
            padding: 5px 0;
            width: 70px !important;
            background-color: var(--sidenavbar-body-color);
        }
        .membership-card .dropdown-item {
            padding: 8px 15px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 8px;
            background-color: var(--sidenavbar-body-color);
        }
        .membership-card .dropdown-item .icon {
            font-size: 16px !important;
            /*margin-right: 4px !important;*/
        }
        .membership-card .view-item {
            color: #3498db;
        }
        .membership-card .edit-item {
            color: #f39c12;
        }

        /* Meta Badges (Status and Category) */
        .membership-card .meta-badges {
            display: flex;
            gap: 8px;
            margin-bottom: 12px;
        }
        .membership-card .status-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .membership-card .status-active {
            background: rgba(46, 204, 113, 0.1);
            color: #2ecc71;
        }
        .membership-card .status-inactive {
            background: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
        }
        .membership-card .status-pending {
            background: rgba(241, 196, 15, 0.1);
            color: #f1c40f;
        }
        .membership-card .category-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            background: rgba(52, 152, 219, 0.1);
            color: #3498db;
        }

        /* Compact Property Info */
        .membership-card .property-info {
            margin-bottom: 12px;
            gap: 10px;
        }
        .membership-card .property-badge {
            padding: 4px 10px;
            border-radius: 6px;
            background-color: rgba(241, 242, 246, 0.5);
            font-size: 0.8rem;
            color: var(--sidenavbar-text-color);
            font-weight: 500;
        }

        /* Toggle Switch Styles */
        .membership-card .switch {
            position: relative;
            display: inline-block;
            width: 36px;
            height: 18px;
        }
        .membership-card .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .membership-card .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        .membership-card .slider:before {
            position: absolute;
            content: "";
            height: 14px;
            width: 14px;
            left: 2px;
            bottom: 2px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        .membership-card input:checked + .slider {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
        }
        .membership-card input:checked + .slider:before {
            transform: translateX(18px);
        }

        /* Footer Button */
        .membership-card .membership-footer {
            padding: 0 15px 15px;
        }
        .membership-card .btn-membership {
            border-radius: 8px;
            padding: 10px 15px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #3498db, #3498db);
            border: none;
            font-size: 0.8rem;
            box-shadow: 0 2px 5px rgba(52, 152, 219, 0.1);
        }
        .membership-card .btn-membership:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(52, 152, 219, 0.2);
            background: linear-gradient(135deg, #2980b9, #3498db);
        }

        /* Responsive adjustments */
        @media (max-width: 767.98px) {
            .membership-card .membership-img-container {
                height: 140px;
            }
            .membership-card .membership-title {
                font-size: 1.1rem;
            }
        }

        .empty-state {
            border-radius: 10px;
        }

        .empty-state-icon svg {
            opacity: 0.6;
        }

        .empty-state h3 {
            color: #343a40;
            font-weight: 600;
        }

        .btn-membership-create {
            padding: 10px 25px;
            font-weight: 500;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .btn-membership-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }
    </style>
@endpush

@section('content')

    <!-- Top Navbar -->
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('owner_manager_dashboard'), 'label' => 'Dashboard'],
            ['url' => '', 'label' => 'Memberships']
        ]"
    />

    <x-Owner.side-navbar :openSections="['Memberships']"/>
    <x-error-success-model />

    <div id="main">
        <section class="content mt-1 mb-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                            <div class="container mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h3 class="section-title">Memberships</h3>
                                    <a href="{{ route('owner.memberships.create') }}" class="btn btn-primary add-membership-btn" id="Owner-Level-Add-Button">
                                        <x-icon name="add" type="svg" size="20" />
                                        Add Membership
                                    </a>
                                </div>

                                <!-- Filter Form -->
                                <form method="GET" id="filterForm" class="filter-container">
                                    <!-- All filters with equal width -->
                                    <div class="filter-group">
                                        <label for="search">Search</label>
                                        <input type="text" name="search" id="search" class="form-control search-input"
                                               placeholder="Search by name"
                                               value="{{ request('search') }}">
                                    </div>

                                    @if(isset($buildings) && $buildings->count() > 0)
                                        <div class="filter-group">
                                            <label for="building_id">Building</label>
                                            <select name="building_id" id="building_id" class="form-select">
                                                <option value="">All Buildings</option>
                                                @foreach($buildings as $building)
                                                    <option value="{{ $building->id }}" {{ request('building_id') == $building->id ? 'selected' : '' }}>
                                                        {{ $building->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    <div class="filter-group">
                                        <label for="unit_id">Unit</label>
                                        <select name="unit_id" id="unit_id" class="form-select">
                                            <option value="">All Units</option>
                                            @foreach($units as $unit)
                                                <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                                                    {{ $unit->unit_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="filter-group">
                                        <label for="type">Type</label>
                                        <select name="type" id="type" class="form-select">
                                            <option value="">All Types</option>
                                            @foreach($types as $type)
                                                <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                                    {{ $type }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="filter-group">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-select">
                                            <option value="">All Statuses</option>
                                            @foreach($statuses as $status)
                                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                                    {{ $status }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- These will now match the width of the above filters -->
                                    <div class="filter-group">
                                        <label for="min_price">Min Price</label>
                                        <input type="number" name="min_price" id="min_price" class="form-control"
                                               placeholder="Minimum price" value="{{ request('min_price') }}">
                                    </div>

                                    <div class="filter-group">
                                        <label for="max_price">Max Price</label>
                                        <input type="number" name="max_price" id="max_price" class="form-control"
                                               placeholder="Maximum price" value="{{ request('max_price') }}">
                                    </div>

                                    <div class="filter-group">
                                        <label for="featured">Featured</label>
                                        <select name="featured" id="featured" class="form-select">
                                            <option value="">All</option>
                                            <option value="1" {{ request('featured') === '1' ? 'selected' : '' }}>Yes</option>
                                            <option value="0" {{ request('featured') === '0' ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>

                                    <div class="filter-buttons">
                                        <button type="button" class="btn btn-secondary w-100" onclick="resetFilters()">
                                            <i class="fas fa-undo me-2"></i> Reset
                                        </button>
                                    </div>
                                    <div class="filter-buttons">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-filter me-2"></i> Apply Filters
                                        </button>
                                    </div>
                                </form>

                                <div class="row">
                                    @if(count($memberships) > 0)
                                        @foreach($memberships as $membership)
                                            <div class="col-md-6 col-lg-4 mb-4">
                                                <div class="membership-card">
                                                    <div class="membership-img-container">
                                                        <img src="{{ $membership->image ? asset($membership->image) : 'https://images.unsplash.com/photo-1571902943202-507ec2618e8f?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80' }}"
                                                             alt="{{ $membership->name }}"
                                                             class="membership-img">

                                                        <!-- Price Overlay -->
                                                        <div class="price-overlay">
                                                            @if($membership->original_price > $membership->price)
                                                                <span class="original-price">{{ number_format($membership->original_price, 2) . ' ' . $membership->currency }}</span>
                                                            @endif
                                                            <span class="membership-price">{{ number_format($membership->price, 2) . ' ' . $membership->currency }}</span>
                                                            <span class="membership-period">/ {{ $membership->duration_months }} Month</span>
                                                        </div>

                                                        @if($membership->discount > 0)
                                                            <span class="discount-badge">{{ $membership->discount }}% OFF</span>
                                                        @endif

                                                        <span class="popular-badge">
                                                            <span>Featured</span>
                                                            <label class="switch">
                                                                <input type="checkbox" class="featured-toggle" data-membership-id="{{ $membership->id }}" {{ $membership->mark_as_featured ? 'checked' : '' }}>
                                                                <span class="slider"></span>
                                                            </label>
                                                        </span>

                                                        <!-- Action Menu (Three Dots) -->
                                                        <div class="action-menu">
                                                            <button class="action-btn" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="fas fa-ellipsis-v"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li>
                                                                    <a class="dropdown-item view-item" href="{{ route('owner.memberships.show', $membership->id) }}">
                                                                        <x-icon name="view" type="material" class="icon" /> View
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item edit-item" href="{{ route('owner.memberships.edit', $membership->id) }}">
                                                                        <x-icon name="edit" type="material" class="icon" /> Edit
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>

                                                    <div class="membership-body">
                                                        <!-- Status and Category Badges -->
                                                        <div class="meta-badges d-flex justify-content-between align-items-center">
                                                            <span class="status-badge status-{{ strtolower($membership->status) }}">
                                                                {{ $membership->status }}
                                                            </span>
                                                            @if($membership->category)
                                                                <span class="category-badge">
                                                                {{ $membership->category }}
                                                            </span>
                                                            @endif
                                                        </div>

                                                        <h4 class="membership-title">{{ $membership->name }}</h4>

                                                        <!-- Compact Property Info -->
                                                        <div class="property-info">
                                                            <span class="property-badge">{{ $membership->building->name ?? 'N/A' }}</span>
                                                            <span class="property-badge">{{ $membership->unit->unit_name ?? 'N/A' }}</span>
                                                        </div>

                                                        <p class="membership-description">
                                                            {{ Str::limit($membership->description, 120) }}
                                                        </p>
                                                    </div>

                                                    <div class="membership-footer">
                                                        <a href="{{ route('owner.memberships.assign.view', $membership->id) }}" class="btn btn-primary btn-membership text-decoration-none">
                                                            Assign Membership
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="col-12">
                                            <div class="empty-state text-center py-5">
                                                <i class="fas fa-users fa-3x mb-3"></i>
                                                <h4 class="mt-3">No Memberships Found</h4>
                                                <p class="">There are currently no memberships matching your filters.</p>
                                                <a href="{{ route('owner.memberships.create') }}" class="btn btn-primary mt-3">
                                                    <i class="fas fa-plus me-2"></i> Create New Membership
                                                </a>

                                            </div>
                                        </div>
                                    @endif
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

    <script>
        function resetFilters() {
            window.location.href = '{{ route("owner.memberships.index") }}';
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.body.addEventListener('change', function(e) {
                if (e.target.classList.contains('featured-toggle')) {
                    const button = e.target;
                    const membershipId = button.dataset.membershipId;
                    const isChecked = button.checked ? 1 : 0;

                    const originalHTML = button.nextElementSibling.innerHTML;
                    button.nextElementSibling.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    button.disabled = true;

                    fetch( "{{ route('owner.memberships.toggle.featured') }}" , {
                        method: 'PUT',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            membership_id: membershipId,
                            value: isChecked
                        })
                    })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => {
                                    throw new Error(
                                        err.message ||
                                        err.error ||
                                        'Request failed with status ' + response.status
                                    );
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                                return;
                            }

                            // Handle success message from redirect
                            const successMessage = data.success || 'Membership featured status updated';
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: successMessage,
                                timer: 2000,
                                background: 'var(--body-background-color)',
                                color: 'var(--sidenavbar-text-color)',
                                showConfirmButton: false
                            });
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: error.message || 'Something went wrong. Please try again.',
                                timer: 2000,
                                background: 'var(--body-background-color)',
                                color: 'var(--sidenavbar-text-color)',
                                showConfirmButton: true
                            });
                            button.checked = !button.checked;  // Revert checkbox on error
                        })
                        .finally(() => {
                            button.nextElementSibling.innerHTML = originalHTML;
                            button.disabled = false;
                        });
                }
            });
        });
    </script>

@endpush
