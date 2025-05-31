@extends('layouts.app')

@section('title', $department->name . ' Department')

@push('styles')
    <style>
        :root {
            --sage-green: var(--color-blue);
            --deep-teal: #0b5351;
            --warm-taupe: var(--color-blue);
            --soft-clay: #d7b29d;
            --mist-blue: #a5c4d4;
            --pale-blush: #e8c7c8;
            --dark-charcoal: #33312e;
            --light-ivory: #f8f5f2;
            --soft-gray: #e0ddd9;
        }

        #main {
            margin-top: 45px;
            transition: all 0.3s;
        }

        a{ text-decoration: none; }

        /* Department Header - Hero Section */
        .department-hero {
            background: linear-gradient(135deg, var(--deep-teal) 0%, var(--sage-green) 100%);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 2rem;
            margin-bottom: 2.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            z-index: 1;
            animation: fadeInUp 0.6s ease-out;
        }

        .department-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            z-index: -1;
        }

        .department-title {
            font-weight: 700;
            margin-bottom: 0.75rem;
            font-size: 2.2rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: relative;
            display: inline-block;
            color: var(--light-ivory);
        }

        .department-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 60px;
            height: 4px;
            background: var(--warm-taupe);
            border-radius: 2px;
        }

        .department-description {
            font-size: 1.1rem;
            line-height: 1.7;
            max-width: 800px;
            opacity: 0.9;
            margin-bottom: 1.5rem;
            color: rgba(255,255,255,0.9);
        }

        .department-meta {
            display: flex;
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
            color: rgba(255,255,255,0.85);
        }

        .meta-icon {
            font-size: 1.1rem;
            color: var(--warm-taupe);
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }

        .department-hero .btn-edit,.btn-delete,.btn-back{
            color: white;
            border-radius: 50px;
            padding: 0.6rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
        }

        .department-hero .btn-edit:hover,.btn-delete:hover,.btn-back:hover{
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .btn-edit {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
        }

        .btn-edit:hover {
            background: rgba(255,255,255,0.25);
        }

        .btn-delete {
            background: rgba(199, 84, 80, 0.15);
            border: 1px solid rgba(199, 84, 80, 0.3);
        }

        .btn-delete:hover {
            background: rgba(199, 84, 80, 0.25);
        }

        .btn-back {
            background: rgba(255,255,255,0.9);
            color: var(--deep-teal);
            font-weight: 500;
        }

        .btn-back:hover {
            background: white;
        }

        /* Staff Team Section */
        .team-section {
            background: var(--body-card-bg);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            animation: fadeIn 0.8s ease-out 0.2s both;
            border: 1px solid var(--soft-gray);
        }

        .section-header {
            padding: 0.7rem 2rem;
            border-bottom: 1px solid var(--soft-gray);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--body-card-bg);
        }

        .section-title {
            font-weight: 600;
            color: var(--sidenavbar-text-color);
            margin: 0;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-title-icon {
            color: var(--sage-green);
            font-size: 1.4rem;
        }

        .team-count {
            background: var(--sage-green);
            color: white;
            border-radius: 50px;
            padding: 0.25rem 0.75rem;
            font-size: 0.85rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }

        /* Team Members Grid */
        .team-members {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
            gap: 1rem;
            padding: 2rem;
        }

        .member-card {
            background: var(--body-background-color);
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
            position: relative;
        }

        .member-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-color: var(--mist-blue);
        }

        .member-card .member-header {
            padding: 1.5rem 1.5rem 0.1rem 1.5rem;
            text-align: center;
            background: linear-gradient(135deg, var(--body-background-color) 0%, var(--sidenavbar-body-color) 100%);
        }

        .member-card .member-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin: 0 auto 1rem;
            transition: all 0.3s ease;
        }

        .member-card:hover .member-avatar {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            border-color: var(--pale-blush);
        }

        .member-card .member-name {
            font-weight: 700;
            color: var(--sidenavbar-text-color);
            margin-bottom: 0.25rem;
            font-size: 1.1rem;
        }

        .member-card .member-position {
            color: var(--warm-taupe);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .member-card .member-details {
            padding: 1.25rem 1.25rem 0 1.25rem;
        }

        .member-card .detail-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .member-card .detail-icon {
            color: var(--sage-green);
            font-size: 1rem;
            width: 20px;
            text-align: center;
        }

        .member-card .detail-text {
            font-size: 0.9rem;
            color: var(--sidenavbar-text-color);
        }

        .member-card .member-actions {
            display: flex;
            justify-content: space-between;
            padding: 5px 10px 10px 10px;
        }

        .member-card .btn-member {
            flex: 1;
            margin: 4px;
            padding: 8px 0;
            border-radius: 5px;
            font-size: 0.85rem;
        }

        .member-card .btn-add {
            padding: 10px 10px !important;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 120px;
            font-size: 0.95rem;
        }

        .member-card .btn-view {
            background-color: rgba(52, 152, 219, 0.1);
            color: #3498db !important;
            border: 1px solid rgba(52, 152, 219, 0.2);
        }

        .member-card .btn-view:hover {
            background-color: rgba(52, 152, 219, 0.2);
            color: #2980b9;
        }

        .member-card .btn-edit {
            background-color: rgba(46, 204, 113, 0.1);
            color: green !important;
            border: 1px solid rgba(46, 204, 113, 0.2);
        }

        .member-card .btn-edit:hover {
            background-color: rgba(46, 204, 113, 0.2);
            color: #27ae60;
        }

        /* Empty State */
        .empty-team {
            padding: 2rem 2rem;
            text-align: center;
            grid-column: 1 / -1;
            background: var(--light-ivory);
            border-radius: 12px;
            margin: 0.5rem;
        }

        .empty-icon {
            font-size: 4rem;
            color: var(--mist-blue);
            margin-bottom: 1.5rem;
            opacity: 0.7;
        }

        .empty-title {
            font-weight: 600;
            color: var(--dark-charcoal);
            margin-bottom: 0.5rem;
        }

        .empty-text {
            color: var(--warm-taupe);
            max-width: 500px;
            margin: 0 auto 1.5rem;
            line-height: 1.6;
        }

        .btn-invite {
            background: var(--color-blue);
            color: white;
            border-radius: 50px;
            padding: 0.6rem 1.75rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            font-weight: 500;
            border: none;
        }

        .btn-invite:hover {
            background: var(--color-blue);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(135, 188, 141, 0.3);
        }

        /* Modal Styling */
        .modal-content {
            background: white;
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
            border: 1px solid var(--soft-gray);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--deep-teal) 0%, var(--sage-green) 100%);
            border-bottom: none;
            padding: 1rem 1.5rem;
            color: white;
        }

        .modal-title {
            font-weight: 700;
            font-size: 1.4rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            justify-content: center;
        }

        .modal-title-icon {
            font-size: 1.5rem;
            color: var(--warm-taupe);
        }

        .btn-close {
            filter: invert(1);
            opacity: 0.8;
        }

        .modal-body {
            padding: 1.5rem;
            background: var(--body-background-color);
        }

        .form-label {
            font-weight: 600;
            color: var(--sidenavbar-text-color);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-label-icon {
            color: var(--sage-green);
        }

        .form-control {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 1px solid var(--soft-gray);
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--sage-green);
            box-shadow: 0 0 0 0.25rem rgba(135, 188, 141, 0.1);
        }

        .modal-footer {
            border-top: 1px solid var(--soft-gray);
            padding: 1rem 2rem;
            background: var(--body-background-color);
        }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .department-hero {
                padding: 2rem 1.5rem;
            }

            .department-title {
                font-size: 1.8rem;
            }

            .team-members {
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .department-hero {
                text-align: center;
            }

            .department-title::after {
                left: 50%;
                transform: translateX(-50%);
            }

            .department-meta {
                justify-content: center;
            }

            .action-buttons {
                justify-content: center;
            }

            .section-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .team-members {
                grid-template-columns: 1fr;
                padding: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .department-hero {
                padding: 1.75rem 1.25rem;
            }

            .department-title {
                font-size: 1.6rem;
            }

            .department-description {
                font-size: 1rem;
            }

            .modal-body {
                padding: 1.5rem;
            }
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

        .dropdown-divider {
            margin: 0.3rem 0;
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
    </style>

@endpush

@section('content')
    <!-- Top Navbar -->
    <x-Owner.top-navbar
        :searchVisible="false"
        :breadcrumbLinks="[
            ['url' => route('owner_manager_dashboard'), 'label' => 'Dashboard'],
            ['url' => route('owner.departments.index'), 'label' => 'Departments'],
            ['url' => '', 'label' => $department->name]
        ]"
    />

    <!-- Side Navbar -->
    <x-Owner.side-navbar :openSections="['Departments']" />
    <x-error-success-model />
    <x-promote-to-manager />

    <div id="main">
        <section class="content mx-2 my-3">
            <div class="container-fluid">
                <!-- Department Hero Section -->
                <div class="department-hero">
                    <h1 class="department-title">{{ $department->name }}</h1>
                    <p class="department-description">
                        {{ $department->description ?? 'A dedicated team working in harmony to achieve excellence and create meaningful impact.' }}
                    </p>

                    <div class="department-meta">
                        <div class="meta-item">
                            <i class="fas fa-users meta-icon"></i>
                            <span>{{ $staffCount }} Team Members</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-calendar-alt meta-icon"></i>
                            <span>Established {{ $department->created_at->format('F Y') }}</span>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <a href="#" class="btn-edit Owner-Department-Edit-Button" data-id="{{ $department->id }}">
                            <i class="fas fa-pen"></i> Edit Department
                        </a>

                        <form action="{{ route('owner.departments.destroy', $department->id) }}"
                              method="POST"
                              class="d-inline"
                              id="delete-department-form-{{ $department->id }}">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="id" value="{{ $department->id }}">
                            <button type="button"
                                    class="btn-delete delete-department-btn"
                                    data-id="{{ $department->id }}">
                                <i class="fas fa-trash"></i> Delete Department
                            </button>
                        </form>

                        <a href="{{ route('owner.departments.index') }}" class="btn-back">
                            <i class="fas fa-arrow-left"></i> Back to Departments
                        </a>
                    </div>
                </div>

                <!-- Team Members Section -->
                <div class="team-section">
                    <div class="section-header d-flex justify-content-between align-items-center">
                        <h2 class="section-title">
                            <i class="fas fa-users section-title-icon"></i>
                            Our Team
                            <span class="team-count">{{ $staffCount }}</span>
                        </h2>
                        <a href="{{ route('owner.staff.create') }}" class="btn btn-invite">
                            <i class="fas fa-user-plus"></i> Add New Staff
                        </a>
                    </div>

                    <div class="team-members">
                        @forelse($staffMembers ?? [] as $staffMember)
                            <div class="member-card">
                                <div class="member-header">
                                    <img src="{{ $staffMember->user->picture ? asset($staffMember->user->picture) : asset('img/placeholder-profile.png') }}"
                                         alt="{{ $staffMember->user->name }}"
                                         class="member-avatar"
                                         onerror="this.src='{{ asset('img/placeholder-profile.png') }}'">
                                    <h3 class="member-name">{{ $staffMember->user->name }}</h3>
                                    <p class="member-position">{{ $staffMember->user->role_id === 3 ? 'Manager' : ($staffMember->user->role_id === 4 ? 'Team Member' : 'Team Member') }}
                                    </p>
                                    <div class="dropdown member-actions-dropdown">
                                        <button class="btn btn-sm dropdown-toggle-btn rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-lg"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item promote-item promote-btn"  href="#" data-staff-id="{{ $staffMember->id }}">
                                                    <i class="fas fa-user-shield me-2"></i> Promote to Manager
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item delete-item delete-member-btn text-danger" href="#" data-member-id="{{ $staffMember->id }}">
                                                    <i class="fas fa-trash-alt me-2"></i> Delete Staff
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="member-details">
                                    <div class="detail-item">
                                        <i class="fas fa-envelope detail-icon"></i>
                                        <div class="detail-text">
                                            <a href="" style="color: var(--sage-green);">{{ $staffMember->user->email }}</a>
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
                                        <i class="fas fa-award detail-icon"></i>
                                        <div class="detail-text" style="display: flex; align-items: center; gap: 8px;">
                                            <span title="Assign permission to accept or handle queries">Handle Queries</span>
                                            <label class="enable-query-toggle-btn">
                                                <input type="checkbox" class="enable-query-btn" data-staff-id="{{ $staffMember->id }}" {{ $staffMember->accept_queries ? 'checked' : '' }}>
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
                            <div class="empty-team">
                                <div class="empty-icon">
                                    <i class="fas fa-user-friends"></i>
                                </div>
                                <h3 class="empty-title">Let's Build Your Team</h3>
                                <p class="empty-text">
                                    This department is like a garden waiting to bloom. Invite your first team member to begin cultivating something beautiful together.
                                </p>
                                <a href="{{ route('owner.staff.create') }}" class="btn btn-invite">
                                    <i class="fas fa-user-plus"></i> Add New Staff
                                </a>
                            </div>
                        @endforelse
                    </div>

                </div>
                @if($staffMembers)
                    <div class="mt-3 custom-pagination-wrapper">
                        {{ $staffMembers->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </section>
    </div>

    <!-- Edit Department Modal -->
    <div class="modal fade" id="editDepartmentModel" tabindex="-1" aria-labelledby="editDepartmentModelLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDepartmentModelLabel">
                        <i class="fas fa-edit modal-title-icon"></i> Edit Department
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="editDepartmentForm" action="{{ route('owner.departments.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id" id="edit_department_id">
                    <input type="hidden" name="updated_at" id="edit_updated_at">

                    <div class="modal-body">
                        <div class="mb-2">
                            <label for="edit_department_name" class="form-label">
                                <i class="fas fa-tag form-label-icon"></i> Department Name
                            </label>
                            <input type="text" name="edit_name" id="edit_department_name" class="form-control @error('edit_name') is-invalid @enderror"
                                   value="{{ old('edit_name') }}" maxlength="50" placeholder="Enter department name" required>
                            @error('edit_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-2">
                            <label for="edit_department_description" class="form-label">
                                <i class="fas fa-align-left form-label-icon"></i> Description
                            </label>
                            <textarea name="edit_description" id="edit_department_description" rows="4" class="form-control @error('edit_description') is-invalid @enderror" maxlength="250"
                                      placeholder="What makes this department unique?">{{ old('edit_description') }}</textarea>
                            @error('edit_description')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                            <div class="form-text text-end mt-1" style="color: var(--warm-taupe);">
                                <span id="description-counter">0</span>/250 characters
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" >
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn" style="background: var(--sage-green); color: white;">
                            <i class="fas fa-save me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Character counter for description
            const descriptionTextarea = document.getElementById('edit_department_description');
            const counterElement = document.getElementById('description-counter');

            if (descriptionTextarea && counterElement) {
                descriptionTextarea.addEventListener('input', function() {
                    counterElement.textContent = this.value.length;
                });
            }

            // Delete department confirmation
            document.querySelectorAll('.delete-department-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const departmentId = this.getAttribute('data-id');
                    const departmentName = document.querySelector('.department-title').textContent;

                    Swal.fire({
                        title: 'Nurture or Let Go?',
                        html: `
                            <div style="text-align: center; color: var(--sidenavbar-text-color);">
                                <i class="fas fa-seedling" style="font-size: 4rem; color: var(--sage-green); margin-bottom: 1rem;"></i>
                                <p>You're about to remove the <strong>${departmentName}</strong> department.</p>
                            </div>
                        `,
                        showCancelButton: true,
                        background: 'var(--body-background-color)',
                        color: 'var(--sidenavbar-text-color)',
                        confirmButtonColor: 'var(--sage-green)',
                        cancelButtonColor: 'var(--warm-taupe)',
                        confirmButtonText: 'Yes, remove it',
                        cancelButtonText: 'No, keep it',
                        reverseButtons: true,
                        customClass: {
                            popup: 'custom-swal-popup',
                            confirmButton: 'custom-swal-confirm',
                            cancelButton: 'custom-swal-cancel'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Transforming...',
                                html: 'Gently reorganizing our garden',
                                background: 'var(--body-background-color)',
                                color: 'var(--sidenavbar-text-color)',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                    document.getElementById(`delete-department-form-${departmentId}`).submit();
                                }
                            });
                        }
                    });
                });
            });

            // Edit department modal handling
            document.querySelectorAll('.Owner-Department-Edit-Button').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');

                    Swal.fire({
                        html: ' ',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        background: 'var(--body-background-color)',
                        color: 'var(--sidenavbar-text-color)',
                        backdrop: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch(`{{ route('owner.departments.edit', ':id') }}`.replace(':id', id), {
                        method: "GET",
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            Swal.close();

                            if (data.error) {
                                showToast(data.error, 'error');
                            } else {
                                document.getElementById("edit_department_id").value = data.department?.id || "";
                                document.getElementById("edit_department_name").value = data.department?.name || "";
                                document.getElementById("edit_department_description").value = data.department?.description || "";
                                document.getElementById("edit_updated_at").value = data.department?.updated_at || "";

                                if (counterElement) {
                                    counterElement.textContent = data.department?.description?.length || 0;
                                }

                                const editModal = new bootstrap.Modal(document.getElementById("editDepartmentModel"));
                                editModal.show();

                                const modalContent = document.querySelector('.modal-content');
                                modalContent.style.animation = 'fadeInUp 0.4s ease-out';
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'Connection Lost',
                                text: 'The garden path could not be found. Please try again.',
                                icon: 'error',
                                confirmButtonColor: 'var(--sage-green)',
                                background: 'var(--body-background-color)',
                                color: 'var(--sidenavbar-text-color)',
                            });
                            console.error('Error:', error);
                        });
                });
            });

            // Helper function to show toast notifications
            function showToast(message, type = 'success') {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    background: 'var(--body-background-color)',
                    color: 'var(--sidenavbar-text-color)',
                    iconColor: type === 'success' ? 'var(--sage-green)' : 'var(--soft-clay)',
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: type,
                    title: message
                });
            }
        });

        // Handle Accept Queries
        document.addEventListener('DOMContentLoaded', function() {
            document.body.addEventListener('change', function(e) {
                if (e.target.classList.contains('enable-query-btn')) {
                    const button = e.target;
                    const staffId = button.dataset.staffId;
                    const isChecked = button.checked ? 1 : 0;

                    const queryUrl = "{{ route('owner.staff.handle.queries') }}";
                    const originalHTML = button.nextElementSibling.innerHTML;
                    button.nextElementSibling.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    button.disabled = true;

                    fetch(queryUrl, {
                        method: 'PUT',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            id: staffId,
                            accept_query: isChecked
                        })
                    })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => { throw err; });
                            }
                            return response.json();
                        })
                        .then(data => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: data.success || 'Status updated successfully',
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
                                text: error.error || 'Something went wrong. Please try again.',
                                timer: 2000,
                                background: 'var(--body-background-color)',
                                color: 'var(--sidenavbar-text-color)',
                                showConfirmButton: true
                            });
                            // Revert the checkbox state
                            button.checked = !button.checked;
                        })
                        .finally(() => {
                            button.nextElementSibling.innerHTML = originalHTML;
                            button.disabled = false;
                        });
                }
            });
        });

        // Delete staff member
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.delete-member-btn').forEach(button => {
                button.addEventListener('click', function (e) {
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
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Deleting...',
                                html: 'Please wait while we delete the staff member.',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                },
                                background: 'var(--body-background-color)',
                                color: 'var(--sidenavbar-text-color)',
                            });

                            deleteStaffMember(memberId)
                                .then(data => {
                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: data.success || 'Staff member has been deleted.',
                                        icon: 'success',
                                        background: 'var(--body-background-color)',
                                        color: 'var(--sidenavbar-text-color)',
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                })
                                .catch(error => {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: error.message || error.error || 'An error occurred while deleting.',
                                        icon: 'error',
                                        background: 'var(--body-background-color)',
                                        color: 'var(--sidenavbar-text-color)',
                                    });
                                });
                        }
                    });
                });
            });
        });
    </script>
@endpush
