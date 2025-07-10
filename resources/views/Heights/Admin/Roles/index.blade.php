@extends('layouts.app')

@section('title', 'Roles')

@push('styles')
    <style>
        :root {
            --card-border: #e9ecef;
            --primary-rgb: 99, 102, 241;
            --input-border: #ced4da;
        }

        body {
        }

        #main {
            margin-top: 45px;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(var(--primary-rgb), 0.2);
        }

        /* Card Styles */
        .roles-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
            padding: 20px 0;
        }

        .role-card {
            background: var(--sidenavbar-body-color);
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            border: 1px solid rgba(var(--color-blue), 0.3) !important;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
            position: relative;
        }

        .role-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            border-color: rgba(var(--primary-rgb), 0.3);
        }

        .role-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(var(--primary-rgb), 0.03) 0%, rgba(var(--primary-rgb), 0.01) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }

        .role-card:hover::before {
            opacity: 1;
        }

        .role-header {
            padding: 18px 20px;
            border-bottom: 1px solid var(--card-border);
            background: var(--sidenavbar-body-color);
            position: relative;
        }

        .role-card:hover .role-header::after {
            transform: scaleX(1);
        }

        .role-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--sidenavbar-text-color);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .role-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(var(--primary-rgb), 0.1) 0%, rgba(var(--primary-rgb), 0.2) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--color-blue);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .role-card:hover .role-icon {
            transform: rotate(10deg);
            box-shadow: 0 5px 15px rgba(var(--primary-rgb), 0.2);
        }

        .role-id {
            font-size: 0.8rem;
            color: #fff;
            background: var(--color-blue);
            padding: 3px 10px;
            border-radius: 20px;
            position: absolute;
            top: 15px;
            right: 15px;
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }

        .role-body {
            padding: 20px;
            flex-grow: 1;
        }

        .role-description {
            color: var(--sidenavbar-text-color);
            margin-bottom: 20px;
            line-height: 1.6;
            font-size: 0.95rem;
            position: relative;
            padding-left: 15px;
        }

        .role-description::before {
            content: '';
            position: absolute;
            left: 0;
            top: 6px;
            height: calc(100% - 12px);
            width: 3px;
            background: linear-gradient(to bottom, var(--color-blue), var(--color-blue));
            border-radius: 3px;
        }

        .role-meta {
            display: flex;
            gap: 15px;
            margin-top: auto;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            color: var(--sidenavbar-text-color);
            padding: 6px 12px;
            border-radius: 8px;
            background: rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease;
        }

        .meta-item:hover {
            background: rgba(var(--primary-rgb), 0.05);
            color: var(--color-blue);
            transform: translateY(-2px);
        }

        .meta-icon {
            font-size: 1rem;
            color: inherit;
        }

        .role-footer {
            padding: 0;
            border-top: 1px solid var(--card-border);
            background: var(--sidenavbar-body-color);
        }

        .role-actions {
            display: flex;
        }

        .role-actions > * {
            flex: 1;
            min-width: 0; /* Important for text truncation if needed */
        }

        .role-actions a,
        .role-actions button,
        .role-actions .action-btn,
        .role-actions form {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 48px;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            background: transparent;
            position: relative;
            overflow: hidden;
            margin: 0;
            text-decoration: none;
            color: var(--sidenavbar-text-color);
            width: 100%;
        }

        .role-actions form {
            display: flex;
        }

        .role-actions .action-btn:not(:last-child),
        .role-actions > *:not(:last-child) {
            border-right: 1px solid var(--card-border);
        }

        .action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: currentColor;
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .action-btn:hover::before {
            opacity: 0.08;
        }

        .action-btn:hover {
            color: var(--sidenavbar-text-color);
        }

        .action-btn i {
            font-size: 1.1rem;
            position: relative;
            z-index: 1;
            transition: all 0.3s ease;
        }

        .action-btn:hover i {
            transform: scale(1.2);
        }

        .users-btn:hover {
            color: #4e73df;
        }

        .permissions-btn:hover {
            color: #1cc88a;
        }

        .edit-btn:hover {
            color: #f6c23e;
        }

        .delete-btn:hover {
            color: #e74a3b;
        }


        .bs-tooltip-auto[data-popper-placement^=top] .tooltip-arrow::before,
        .bs-tooltip-top .tooltip-arrow::before {
            border-top-color: #2d3748;
        }

        /* Empty state */
        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 40px;
            color: var(--sidenavbar-text-color);
            background: var(--sidenavbar-body-color);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px dashed var(--card-border);
            transition: all 0.3s ease;
        }

        .empty-state:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border-color: var(--color-blue);
        }

        .empty-icon {
            font-size: 3.5rem;
            margin-bottom: 20px;
            opacity: 0.3;
            transition: all 0.3s ease;
        }

        .empty-state:hover .empty-icon {
            opacity: 0.5;
            transform: scale(1.05);
        }

        .empty-text {
            font-size: 1.3rem;
            margin-bottom: 25px;
            font-weight: 500;
            color: var(--sidenavbar-text-color);
        }

        /* Search styles */
        .filter-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 5px;
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

        .search-input {
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


        /* Badge styles */
        .role-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 10px;
            background: rgba(var(--primary-rgb), 0.1);
            color: var(--color-blue);
            transition: all 0.3s ease;
        }

        .role-card:hover .role-badge {
            background: rgba(var(--primary-rgb), 0.2);
            transform: translateY(-2px);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .roles-container {
                grid-template-columns: 1fr;
            }

            .role-actions a,
            .role-actions button,
            .role-actions .action-btn {
                height: 42px;
            }

            .action-btn i {
                font-size: 1rem;
            }

            .role-title {
                font-size: 1.1rem;
            }

            .role-icon {
                width: 36px;
                height: 36px;
            }
        }

        /* Animation for cards */
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

        .role-card {
            animation: fadeInUp 0.5s ease forwards;
            opacity: 0;
        }

        .role-card:nth-child(1) { animation-delay: 0.1s; }
        .role-card:nth-child(2) { animation-delay: 0.2s; }
        .role-card:nth-child(3) { animation-delay: 0.3s; }
        .role-card:nth-child(4) { animation-delay: 0.4s; }
        .role-card:nth-child(5) { animation-delay: 0.5s; }
        .role-card:nth-child(6) { animation-delay: 0.6s; }
        .role-card:nth-child(7) { animation-delay: 0.7s; }
        .role-card:nth-child(8) { animation-delay: 0.8s; }
    </style>
@endpush

@section('content')
    <x-Admin.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('admin_dashboard'), 'label' => 'Dashboard'],
            ['url' => '', 'label' => 'Roles']
        ]"
    />
    <x-Admin.side-navbar :openSections="['AdminControl','UserRoles']" />
    <x-error-success-model />

    <div id="main">
        <section class="content my-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-1">Roles Management</h4>
                            <a href="{{ route('roles.create') }}" class="btn btn-primary" title="Add New Role">
                                <i class="fas fa-plus me-2"></i> Add Role
                            </a>
                        </div>

                        <form method="GET" id="filterForm" class="filter-container">
                            <div class="filter-group">
                                <label for="search">Search</label>
                                <input type="text" name="search" id="roleSearch" class="search-input"
                                       placeholder="Search by name or description"
                                       value="{{ request('search') }}">
                            </div>
                        </form>

                        <div class="roles-container" id="rolesContainer">
                            @forelse($roles ?? [] as $role)
                                <div class="role-card" data-search="{{ strtolower($role->name.' '.$role->description) }}">
                                    <div class="role-header">
                                        <h3 class="role-title">
                                            <span class="role-icon">
                                                <i class="fas fa-user-shield"></i>
                                            </span>
                                            {{ $role->name }}
                                        </h3>
                                        <span class="role-id">ID: {{ $role->id }}</span>
                                    </div>
                                    <div class="role-body">
                                        <p class="role-description">
                                            {{ $role->description ?: 'No description provided for this role' }}
                                        </p>

                                        <div class="role-meta">
                                            <span class="meta-item">
                                                <i class="fas fa-users meta-icon"></i>
                                                <span class="count" data-count="{{ $role->users_count ?? 0 }}">0</span> users
                                            </span>
                                            <span class="meta-item">
                                                <i class="fas fa-key meta-icon"></i>
                                                <span class="count" data-count="{{ $role->role_permissions_count ?? 0 }}">0</span> permissions
                                            </span>
                                        </div>
                                    </div>
                                    <div class="role-footer">
                                        <div class="role-actions d-flex justify-content-between">
                                            <a href="{{ route('users.index', ['role_id' => $role->id]) }}"
                                               class="action-btn users-btn"
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top"
                                               title="View Users">
                                                <i class="fas fa-users"></i>
                                            </a>

                                            <a href="{{ route('role.permissions', ['role_id' => $role->id]) }}"
                                            class="action-btn permissions-btn"
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top"
                                               title="Manage Permissions">
                                                <i class="fas fa-key"></i>
                                            </a>

                                            <a href="{{ route('roles.edit', $role->id) }}"
                                               class="action-btn edit-btn"
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top"
                                               title="Edit Role">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form action="{{ route('roles.destroy', $role->id) }}"
                                                  method="POST"
                                                  class="delete-form d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                        class="action-btn delete-btn delete-role text-center"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="Delete Role">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <i class="fas fa-user-shield"></i>
                                    </div>
                                    <h4 class="empty-text">No roles have been created yet</h4>
                                    <a href="{{ route('roles.create') }}" class="btn btn-primary btn-lg">
                                        <i class="fas fa-plus me-2"></i> Create New Role
                                    </a>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Delete confirmation
            const deleteButtons = document.querySelectorAll('.delete-role');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const form = this.closest('.delete-form');

                    Swal.fire({
                        title: 'Delete Role?',
                        text: "This will remove the role and all its associations. This action cannot be undone!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true,
                        backdrop: `
                            rgba(0,0,0,0.4)
                            url("/images/nyan-cat.gif")
                            left top
                            no-repeat
                        `
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // Search functionality
            const searchInput = document.getElementById('roleSearch');
            const rolesContainer = document.getElementById('rolesContainer');
            const roleCards = document.querySelectorAll('.role-card');

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                let hasVisibleCards = false;

                roleCards.forEach(card => {
                    const searchData = card.getAttribute('data-search');
                    if (searchTerm === '' || searchData.includes(searchTerm)) {
                        card.style.display = 'flex';
                        hasVisibleCards = true;
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Handle empty search results
                const existingEmpty = rolesContainer.querySelector('.empty-state.search-empty');
                if (!hasVisibleCards && searchTerm !== '') {
                    if (!existingEmpty) {
                        const emptySearch = document.createElement('div');
                        emptySearch.className = 'empty-state search-empty';
                        emptySearch.innerHTML = `
                            <div class="empty-icon">
                                <i class="fas fa-search fa-2x"></i>
                            </div>
                            <h4 class="empty-text">No roles found matching "${searchTerm}"</h4>
                            <button class="btn btn-secondary" onclick="document.getElementById('roleSearch').value='';document.getElementById('roleSearch').dispatchEvent(new Event('input'))">
                                Clear search
                            </button>
                        `;
                        rolesContainer.appendChild(emptySearch);
                    }
                } else if (existingEmpty) {
                    existingEmpty.remove();
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const counters = document.querySelectorAll('.count');

            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-count'));
                const duration = 2000; // 2 seconds
                const increment = target / (duration / 16); // 60fps

                let current = 0;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        clearInterval(timer);
                        current = target;
                    }
                    counter.textContent = Math.floor(current);
                }, 16);
            });
        });
    </script>
@endpush
