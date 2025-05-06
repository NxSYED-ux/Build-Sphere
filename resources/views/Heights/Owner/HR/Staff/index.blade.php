@extends('layouts.app')

@section('title', 'Staff')

@push('styles')
    <style>
        #main {
            margin-top: 45px;
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
            border: 4px solid white;
            margin-bottom: 15px;
        }

        .delete-member-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .delete-member-btn:hover {
            transform: scale(1.1);
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
            color: var(--sage-green);
            border: 1px solid rgba(46, 204, 113, 0.2);
        }

        .btn-edit:hover {
            background-color: rgba(46, 204, 113, 0.2);
            color: #27ae60;
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
    </style>
@endpush

@section('content')

    <!-- Top Navbar -->
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
        ['url' => route('owner_manager_dashboard'), 'label' => 'Dashboard'],
        ['url' => '', 'label' => 'Staff']
    ]" />

    <!-- Side Navbar -->
    <x-Owner.side-navbar :openSections="['Staff']" />

    <!-- Error/Success Modal -->
    <x-error-success-model />

    <div id="main">
        <section class="content my-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                            <div class="container mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h3 class="mb-1">Staff Members</h3>
                                    <a href="{{ route('owner.staff.create') }}" class="btn btn-add btn-primary" title="Add New Staff Member">
                                        <i class="fas fa-user-plus me-2"></i> Add Staff
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
                                        <label for="DepartmentId">Department</label>
                                        <select name="DepartmentId" id="DepartmentId" class="form-select filter-select">
                                            <option value="">All Departments</option>
                                            @foreach($departments as $department)
                                                <option value="{{ $department->id }}" {{ request('DepartmentId') == $department->id ? 'selected' : '' }}>
                                                    {{ $department->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    @if(isset($buildings) && $buildings->count() > 0)
                                        <div class="filter-group">
                                            <label for="BuildingId">Building</label>
                                            <select name="BuildingId" id="BuildingId" class="form-select filter-select">
                                                <option value="">All Buildings</option>
                                                @foreach($buildings as $building)
                                                    <option value="{{ $building->id }}" {{ request('BuildingId') == $building->id ? 'selected' : '' }}>
                                                        {{ $building->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    <div class="filter-buttons">
                                        <button type="button" class="btn btn-secondary flex-grow-1 d-flex align-items-center justify-content-center" onclick="resetFilters()">
                                            <i class="fas fa-undo me-2"></i> Reset
                                        </button>
                                        <button type="submit" class="btn btn-primary flex-grow-1 d-flex align-items-center justify-content-center">
                                            <i class="fas fa-filter me-2"></i> Apply Filters
                                        </button>
                                    </div>
                                </form>

                                <!-- Staff Cards -->
                                <div class="team-members">
                                    @forelse($staffMembers as $staffMember)
                                        <div class="member-card">
                                            <div class="member-header">
                                                <img src="{{ $staffMember->user->picture ? asset($staffMember->user->picture) : asset('img/placeholder-profile.png') }}"
                                                     alt="{{ $staffMember->user->name }}"
                                                     class="member-avatar">
                                                <h3 class="member-name">{{ $staffMember->user->name }}</h3>
                                                <p class="member-position">
                                                    {{ $staffMember->department->name ?? 'No Department' }}
                                                </p>
                                                <button type="button"
                                                        class="btn btn-sm delete-member-btn btn-danger rounded-circle shadow-sm transition-all delete-plan-btn"
                                                        data-member-id="{{ $staffMember->id }}"  title="Delete Staff Member">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                            <div class="member-details">
                                                <div class="detail-item">
                                                    <i class="fas fa-envelope detail-icon"></i>
                                                    <div class="detail-text">
                                                        <a class="text-decoration-none" href="mailto:{{ $staffMember->user->email }}">{{ $staffMember->user->email }}</a>
                                                    </div>
                                                </div>
                                                <div class="detail-item">
                                                    <i class="fas fa-phone detail-icon"></i>
                                                    <div class="detail-text">
                                                        {{ $staffMember->user->phone_no ?? 'Not provided' }}
                                                    </div>
                                                </div>
                                                <div class="detail-item">
                                                    <i class="fas fa-building detail-icon"></i>
                                                    <div class="detail-text">
                                                        {{ $staffMember->building ? $staffMember->building->name : 'Building not assigned' }}
                                                    </div>
                                                </div>
                                                <div class="detail-item" style="display: flex; align-items: center;">
                                                    <i class="fas fa-award detail-icon" style="margin-right: 10px;"></i>
                                                    <div class="detail-text" style="display: flex; align-items: center; gap: 8px;">
                                                        Handle Queries
                                                        <label class="enable-query-toggle-btn">
                                                            <input type="checkbox" class="">
                                                            <span class="toggle-slider"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="member-actions">
                                                <a href="{{ route('owner.staff.show', $staffMember->id) }}" class="btn btn-add btn-sm btn-view btn-member gap-1" title="View Details">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="{{ route('owner.staff.edit', $staffMember->id) }}" class="btn btn-add btn-sm btn-edit btn-member gap-1" title="Edit Manager">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="no-members">
                                            <i class="fas fa-users fa-3x mb-3"></i>
                                            <h4>No staff members found</h4>
                                            <p>There are currently no staff members matching your filters.</p>
                                        </div>
                                    @endforelse
                                </div>

                                <!-- Pagination -->
                                @if ($staffMembers)
                                    <div class="mt-4">
                                        {{ $staffMembers->appends(request()->query())->links('pagination::bootstrap-5') }}
                                    </div>
                                @endif

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
            window.location.href = '{{ route("owner.staff.index") }}';
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add click event to all delete buttons
            document.querySelectorAll('.delete-member-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const memberId = this.getAttribute('data-member-id');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel',
                        background: 'var(--body-background-color)',
                        color: 'var(--sidenavbar-text-color)',
                        backdrop: true,
                        allowOutsideClick: false,
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp'
                        }
                    }).then((result) => {
                        {{--if (result.isConfirmed) {--}}
                        {{--    // Submit delete request--}}
                        {{--    fetch(`/owner/staff/${memberId}`, {--}}
                        {{--        method: 'DELETE',--}}
                        {{--        headers: {--}}
                        {{--            'X-CSRF-TOKEN': '{{ csrf_token() }}',--}}
                        {{--            'Content-Type': 'application/json',--}}
                        {{--            'Accept': 'application/json'--}}
                        {{--        }--}}
                        {{--    })--}}
                        {{--        .then(response => response.json())--}}
                        {{--        .then(data => {--}}
                        {{--            if (data.success) {--}}
                        {{--                Swal.fire(--}}
                        {{--                    'Deleted!',--}}
                        {{--                    'Staff member has been deleted.',--}}
                        {{--                    'success'--}}
                        {{--                ).then(() => {--}}
                        {{--                    window.location.reload();--}}
                        {{--                });--}}
                        {{--            } else {--}}
                        {{--                Swal.fire(--}}
                        {{--                    'Error!',--}}
                        {{--                    data.message || 'Something went wrong.',--}}
                        {{--                    'error'--}}
                        {{--                );--}}
                        {{--            }--}}
                        {{--        })--}}
                        {{--        .catch(error => {--}}
                        {{--            Swal.fire(--}}
                        {{--                'Error!',--}}
                        {{--                'An error occurred while deleting.',--}}
                        {{--                'error'--}}
                        {{--            );--}}
                        {{--        });--}}
                        {{--}--}}
                    });
                });
            });
        });
    </script>
@endpush
