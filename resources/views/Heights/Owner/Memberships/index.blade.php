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
            flex: 1 0 calc(20% - 15px); /* 5 items per row */
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
        .membership-card {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            margin-bottom: 30px;
            background: var(--body-card-bg);
            height: 100%;
            display: flex;
            flex-direction: column;
            border: none;
            position: relative;
            border: 1px solid rgba(0,0,0,0.03);
        }
        .membership-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        .membership-card .membership-img-container {
            height: 180px;
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
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .membership-card .membership-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--sidenavbar-text-color);
            margin-bottom: 12px;
            line-height: 1.3;
            font-family: 'Poppins', sans-serif;
        }
        .membership-card .membership-description {
            color: var(--sidenavbar-text-color);
            line-height: 1.5;
            flex-grow: 1;
            font-size: 0.95rem;
            margin-bottom: 0 !important;
        }
        .membership-card .price-container {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 0;
            margin-top: 0 !important;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .membership-card .membership-price {
            font-size: 1.2rem;
            font-weight: 800;
            color: #e74c3c;
            line-height: 2;
            /*font-family: 'Poppins', sans-serif;*/
        }
        .membership-card .membership-period {
            font-size: 0.9rem;
            color: #5F5F5F !important;
            font-weight: 500;
            line-height: 2.5;
        }
        .membership-card .original-price {
            font-size: 1rem;
            color: #5F5F5F;
            text-decoration: line-through;
            font-weight: 500;
        }
        .membership-card .discount-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.85rem;
            box-shadow: 0 3px 8px rgba(0,0,0,0.15);
            z-index: 2;
        }
        .membership-card .popular-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.85rem;
            box-shadow: 0 3px 8px rgba(0,0,0,0.15);
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .membership-card .membership-footer {
            padding: 0 20px 20px;
        }
        .membership-card .btn-membership {
            border-radius: 10px;
            padding: 12px 20px;
            font-weight: 700;
            width: 100%;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #3498db, #3498db);
            border: none;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85rem;
            box-shadow: 0 2px 5px rgba(52, 152, 219, 0.2);
        }
        .membership-card .btn-membership:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
            background: linear-gradient(135deg, #2980b9, #3498db);
        }
        .membership-card .section-title {
            position: relative;
            margin-bottom: 30px;
            font-weight: 800;
            color: #2c3e50;
            font-size: 2rem;
            text-align: center;
            font-family: 'Poppins', sans-serif;
        }
        .membership-card .add-membership-btn {
            border-radius: 10px;
            padding: 12px 24px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--color-blue), var(--color-blue));
            border: none;
            transition: all 0.3s ease;
        }
        .membership-card .add-membership-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(46, 204, 113, 0.3);
        }

        /* Action Buttons */
        .membership-card .action-buttons {
            position: absolute;
            bottom: 15px;
            right: 15px;
            display: flex;
            gap: 8px;
            z-index: 3;
        }
        .membership-card .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.9);
            color: #2c3e50;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .membership-card .action-btn:hover {
            transform: scale(1.1);
            background: white;
            box-shadow: 0 3px 8px rgba(0,0,0,0.15);
        }
        .membership-card .view-btn {
            color: #3498db;
        }
        .membership-card .edit-btn {
        }
        .membership-card .delete-btn {
            color: #e74c3c;
        }
        .membership-card .action-btn .icon {
            font-size: 16px !important;
        }

        /* Enhanced Building/Unit Info - Beautiful Design */
        .membership-card .property-info {
            margin-bottom: 15px;
            background: transparent;
            padding: 0;
        }
        .membership-card .property-row {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            padding: 8px 12px;
            border-radius: 8px;
            background-color: rgba(241, 242, 246, 0.5);
            transition: all 0.3s ease;
        }
        .membership-card .property-row:hover {
            background-color: rgba(241, 242, 246, 0.9);
            transform: translateX(3px);
        }
        .membership-card .property-icon {
            margin-right: 10px;
            color: #3498db;
            font-size: 1rem;
            min-width: 20px;
            text-align: center;
        }
        .membership-card .property-label {
            font-size: 0.8rem;
            color: var(--sidenavbar-text-color);
            font-weight: 600;
            margin-right: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            min-width: 60px;
        }
        .membership-card .property-value {
            font-size: 0.9rem;
            color: var(--sidenavbar-text-color);
            font-weight: 600;
            flex-grow: 1;
        }

        /* Price Details - Elegant Design */
        .membership-card .price-details {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 5px;
        }
        .membership-card .current-price {
            display: flex;
            align-items: flex-end;
        }
        .membership-card .price-comparison {
            text-align: right;
        }
        .membership-card .savings-badge {
            display: inline-block;
            background: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 3px;
        }

        /* Toggle Switch Styles */
        .membership-card .featured-toggle-container {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .membership-card .switch {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 20px;
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
            height: 16px;
            width: 16px;
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
            transform: translateX(20px);
        }

        /* Ribbon for Featured Items */
        .membership-card .featured-ribbon {
            position: absolute;
            top: 15px;
            left: -30px;
            width: 120px;
            padding: 5px 0;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            text-align: center;
            font-weight: 700;
            font-size: 0.75rem;
            transform: rotate(-45deg);
            box-shadow: 0 3px 8px rgba(0,0,0,0.15);
            z-index: 1;
            letter-spacing: 0.5px;
        }

        /* Responsive adjustments */
        @media (max-width: 767.98px) {
            .membership-card .membership-img-container {
                height: 160px;
            }
            .membership-card .membership-title {
                font-size: 1.3rem;
            }
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
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h3 class="section-title">Premium Memberships</h3>
                                    <a href="#" class="btn btn-primary add-membership-btn" id="Owner-Level-Add-Button">
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
                                               placeholder="Search by name or email"
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
                                        <button type="button" class="btn btn-secondary" onclick="resetFilters()">
                                            <i class="fas fa-undo me-2"></i> Reset
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-filter me-2"></i> Apply Filters
                                        </button>
                                    </div>
                                </form>

                                <div class="row">
                                    @foreach($memberships as $membership)
                                        <div class="col-md-6 col-lg-3 mb-4">
                                            <div class="membership-card">
                                                @if($membership->mark_as_featured)
                                                    <div class="featured-ribbon">FEATURED</div>
                                                @endif

                                                <div class="membership-img-container">
                                                    <img src="{{ $membership->image ? asset($membership->image) : 'https://images.unsplash.com/photo-1571902943202-507ec2618e8f?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80' }}"
                                                         alt="{{ $membership->name }}"
                                                         class="membership-img">

                                                    <div class="action-buttons">
                                                        <button class="action-btn view-btn" title="View Details" onclick="viewMembership({{ $membership->id }})">
                                                            <x-icon name="view" type="material" class="icon" />
                                                        </button>
                                                        <button class="action-btn edit-btn text-warning" title="Edit Membership" onclick="editMembership({{ $membership->id }})">
                                                            <x-icon name="edit" type="material" class="icon" />
                                                        </button>
                                                        <button class="action-btn delete-btn" title="Delete Membership" onclick="confirmDelete({{ $membership->id }})">
                                                            <x-icon name="delete" type="material" class="icon" />
                                                        </button>
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
                                                </div>

                                                <div class="membership-body">
                                                    <h4 class="membership-title">{{ $membership->name }}</h4>

                                                    <!-- Beautiful Building/Unit Info -->
                                                    <div class="property-info">
                                                        <div class="property-row text-center">
                                                            <div class="property-value">{{ $membership->building->name ?? 'N/A' }}</div>
                                                        </div>
                                                        <div class="property-row text-center">
                                                            <div class="property-value">{{ $membership->unit->unit_name ?? 'N/A' }}</div>
                                                        </div>
                                                    </div>

                                                    <p class="membership-description">
                                                        {{ Str::limit($membership->description, 120) }}
                                                    </p>

                                                    <div class="price-container">
                                                        <div class="price-details">
                                                            <div class="current-price">
                                                                <span class="membership-price">{{ number_format($membership->price, 2) . ' ' . $membership->currency }}</span>
                                                                <span class="membership-period">/{{ $membership->duration_months . ' Month' }}</span>
                                                            </div>
                                                            @if($membership->original_price > $membership->price)
                                                                <div class="price-comparison">
                                                                    <div class="original-price">{{ number_format($membership->original_price, 2) }}</div>
                                                                    <div class="savings-badge">Save {{ number_format(100 - ($membership->price/$membership->original_price)*100, 0) }}%</div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="membership-footer">
                                                    <button class="btn btn-primary btn-membership">
                                                        Assign Membership
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
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
{{--    <script>--}}
{{--        $(document).ready(function() {--}}
{{--            // Initialize tooltips--}}
{{--            $('[data-bs-toggle="tooltip"]').tooltip();--}}

{{--            // Handle featured toggle--}}
{{--            $('.featured-toggle').change(function() {--}}
{{--                const membershipId = $(this).data('membership-id');--}}
{{--                const isFeatured = $(this).is(':checked');--}}

{{--                $.ajax({--}}
{{--                    url: '/owner/memberships/' + membershipId + '/toggle-featured',--}}
{{--                    method: 'POST',--}}
{{--                    data: {--}}
{{--                        _token: '{{ csrf_token() }}',--}}
{{--                        featured: isFeatured ? 1 : 0--}}
{{--                    },--}}
{{--                    success: function(response) {--}}
{{--                        if (response.success) {--}}
{{--                            location.reload(); // Reload to show updated state--}}
{{--                        } else {--}}
{{--                            toastr.error('Failed to update featured status');--}}
{{--                            $(this).prop('checked', !isFeatured);--}}
{{--                        }--}}
{{--                    },--}}
{{--                    error: function() {--}}
{{--                        toastr.error('An error occurred');--}}
{{--                        $(this).prop('checked', !isFeatured);--}}
{{--                    }--}}
{{--                });--}}
{{--            });--}}
{{--        });--}}
{{--    </script>--}}
@endpush
