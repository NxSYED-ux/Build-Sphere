@extends('layouts.app')

@section('title', 'Users')

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

        .member-actions-dropdown {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
        }

        .member-actions-dropdown .enable-query-toggle-btn {
            position: relative;
            display: inline-block;
            width: 36px;
            height: 18px;
        }

        .member-actions-dropdown .enable-query-toggle-btn input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .member-actions-dropdown .status-text{
            color: var(--sidenavbar-text-color) !important;
        }

        .member-actions-dropdown .toggle-slider {
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

        .member-actions-dropdown .toggle-slider:before {
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

        .member-actions-dropdown input:checked + .toggle-slider {
            background-color: #4CAF50;
        }

        .member-actions-dropdown input:checked + .toggle-slider:before {
            transform: translateX(18px);
        }

        .member-actions-dropdown input:focus + .toggle-slider {
            box-shadow: 0 0 1px #4CAF50;
        }


        /**/

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
    <x-Admin.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('admin_dashboard'), 'label' => 'Dashboard'],
            ['url' => '', 'label' => 'Users']
        ]"
    />

    <!-- Side Navbar -->
    <x-Admin.side-navbar :openSections="['AdminControl', 'UserManagement']" />
    <x-error-success-model />

    <div id="main">

        <section class="content my-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-1">User Management</h4>
                            <a href="{{ route('users.create') }}" class="btn btn-primary hidden AdminAddUser" title="Add New User">
                                <i class="fas fa-user-plus me-2"></i> Add User
                            </a>
                        </div>

                        <!-- Filter Form -->
                        <form method="GET" id="filterForm" class="filter-container">
                            <div class="filter-group">
                                <label for="search">Search</label>
                                <input type="text" name="search" id="search" class="search-input"
                                       placeholder="Search by name, email or phone no"
                                       value="{{ request('search') }}">
                            </div>

                            <div class="filter-group">
                                <label for="DepartmentId">Role</label>
                                <select name="role_id" id="role_id" class="form-select filter-select">
                                    <option value="">All Roles</option>
                                    @forelse($roles ?? [] as $role)
                                        <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>

                            <div class="filter-group">
                                <label for="Status">Status</label>
                                <select name="status" id="status" class="form-select filter-select">
                                    <option value="" {{ request('status') == '' ? 'selected' : '' }}>All Status</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>In Active</option>
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

                        <!-- User Cards -->
                        <div class="team-members">
                            @forelse($users as $user)
                                <div class="member-card">
                                    <div class="member-header">
                                        <img src="{{ $user->picture ? asset($user->picture) : asset('img/placeholder-profile.png') }}"
                                             alt="{{ $user->name }}" onerror="this.onerror=null; this.src='{{ asset('img/placeholder-profile.png') }}';"
                                             class="member-avatar">
                                        <h3 class="member-name">{{ $user->name }}</h3>
                                        <p class="member-position">
                                            {{ $user->role->name }}
                                            <span class="member-card-status {{ $user->status ? 'bg-success' : 'bg-danger' }}"></span>

                                        </p>
                                        <div class="dropdown member-actions-dropdown">
                                            <button class="btn btn-sm dropdown-toggle-btn rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v fa-lg"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end" style="padding: 0.5rem;">
                                                <li class="px-3 py-2 d-flex align-items-center justify-content-between" onclick="event.stopPropagation()">
                                                    <span class="me-2 status-text">{{ $user->status === 1 ? 'Active' : 'Inactive' }}</span>
                                                    <label class="enable-query-toggle-btn">
                                                        <input type="checkbox" class="status-toggle" data-user-id="{{ $user->id }}" {{ $user->status === 1 ? 'checked' : '' }}>
                                                        <span class="toggle-slider"></span>
                                                    </label>
                                                </li>
                                            </ul>
                                        </div>
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
                                            <i class="bx bx-map detail-icon fs-5"></i>
                                            <div class="detail-text">
                                                {{ $user->address->city ?? 'Not provided' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="member-actions">
                                        <a href="javascript:void(0);" class="btn btn-add btn-sm btn-view view-user btn-member gap-1" data-id="{{ $user->id }}" title="View Details">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        @if($user->is_verified === 0)
                                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-add btn-sm btn-edit btn-member gap-1 hidden AdminEditUser" title="Edit Manager">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        @else
                                            <span class="btn btn-add btn-sm btn-edit btn-member gap-1 disabled pointer-events-none opacity-50 hidden AdminEditUser" title="User not verified">
                                                <i class="fas fa-edit"></i> Edit
                                            </span>
                                        @endif


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
            window.location.href = '{{ route("users.index") }}';
        }
    </script>

    <!-- User Detail Model script -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // This works for both table and card view user clicks
            document.querySelectorAll(".view-user").forEach(button => {
                button.addEventListener("click", function () {
                    let userId = this.dataset.id;

                    fetch(`{{ route('users.show', ':id') }}`.replace(':id', userId), {
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
                            let user = data.user;

                            document.getElementById("userPicture").src = user.picture ? "{{ asset('/') }}" + user.picture : "{{ asset('img/placeholder-profile.png') }}";
                            document.getElementById("userName").textContent = user.name;
                            document.getElementById("userEmail").textContent = user.email;
                            document.getElementById("userPhone").textContent = user.phone_no;
                            document.getElementById("userCnic").textContent = user.cnic;
                            document.getElementById("userGender").textContent = user.gender;
                            document.getElementById("userRole").textContent = user.role.name;

                            let userStatus = document.getElementById("userStatus");
                            userStatus.classList.remove("bg-success", "bg-danger");
                            userStatus.classList.add(user.status ? "bg-success" : "bg-danger");

                            let userModal = new bootstrap.Modal(document.getElementById("userModal"));
                            userModal.show();
                        })
                        .catch(error => {
                            console.error("Error:", error);
                        });
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.status-toggle').forEach(toggle => {
                toggle.addEventListener('change', function() {
                    const userId = this.dataset.userId;
                    const isActive = this.checked;
                    const card = this.closest('.member-card');
                    const statusText = this.closest('.dropdown-menu').querySelector('.status-text');
                    const statusIndicator = card.querySelector('.member-card-status');

                    // Store original state in case we need to revert
                    const originalState = this.checked;

                    // Show confirmation dialog
                    Swal.fire({
                        title: 'Confirm Status Change',
                        text: `Are you sure you want to set this user to ${isActive ? 'Active' : 'Inactive'}?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, update it!',
                        background: 'var(--body-background-color)',
                        color: 'var(--sidenavbar-text-color)',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Proceed with the status change
                            fetch('{{ route("user.toggleStatus") }}', {
                                method: 'PUT',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    id: userId
                                })
                            })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error('Network response was not ok');
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    // Update the status text
                                    statusText.textContent = data.new_status ? 'Active' : 'Inactive';

                                    // Update the status indicator in the card
                                    if (statusIndicator) {
                                        statusIndicator.classList.remove('bg-success', 'bg-danger');
                                        statusIndicator.classList.add(data.new_status ? 'bg-success' : 'bg-danger');
                                    }

                                    // Show success message
                                    Swal.fire(
                                        'Updated!',
                                        'User status has been updated.',
                                        'success'
                                    );
                                    Swal.fire({
                                        title: 'Updated!',
                                        text: `User Status set to ${isActive ? 'Active' : 'Inactive'} Successfuly!`,
                                        icon: 'success',
                                        confirmButtonColor: '#3085d6',
                                        background: 'var(--body-background-color)',
                                        color: 'var(--sidenavbar-text-color)',
                                    })
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    // Revert the toggle if there's an error
                                    this.checked = originalState;
                                    Swal.fire({
                                        title: 'Error!',
                                        text: `Failed to update user status.`,
                                        icon: 'error',
                                        background: 'var(--body-background-color)',
                                        color: 'var(--sidenavbar-text-color)',
                                    })
                                });
                        } else {
                            // User cancelled - revert the toggle
                            this.checked = !originalState;
                        }
                    });
                });
            });
        });
    </script>
@endpush
