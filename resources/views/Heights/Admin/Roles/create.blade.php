@extends('layouts.app')

@section('title', 'Add Role')

@push('styles')
    <style>
        :root {
            --primary-color: var(--color-blue);
            --primary-hover: var(--color-blue);
            --secondary-color: #f8f9fa;
            --border-color: #e2e8f0;
            --error-color: #e53e3e;
            --success-color: #38a169;
            --permission-header-bg: rgba(var(--color-blue), 0.05);
            --permission-card-bg: var(--body-background-color);
            --child-permission-indicator: var(--color-blue);
        }

        #main {
            margin-top: 45px;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            background-color: var(--body-background-color);
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .required__field {
            color: var(--error-color);
            font-size: 0.9em;
        }

        .btn {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        .form-label {
            font-weight: 500;
            color: var(--sidenavbar-text-color);
            margin-bottom: 8px;
            display: block;
        }

        .section-header {
            color: var(--sidenavbar-text-color);
            margin-bottom: 10px;
            padding-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .section-header i {
            margin-right: 10px;
            font-size: 1.2em;
            color: var(--primary-color);
        }

        .invalid-feedback {
            font-size: 0.85em;
            margin-top: 5px;
            color: var(--error-color);
        }

        .input-icon {
            color: var(--sidenavbar-text-color);
        }

        .form-section {
            animation: fadeIn 0.4s ease forwards;
            margin-bottom: 25px;
        }

        .accordion-item {
            background-color: transparent;
            border: 1px solid var(--border-color) !important;
            border-radius: 8px !important;
            margin-bottom: 12px;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .accordion-button {
            background-color: var(--body-background-color);
            color: var(--sidenavbar-text-color);
            padding: 1rem 1.5rem;
            box-shadow: none;
        }

        .accordion-button:not(.collapsed) {
            background-color: var(--sidenavbar-body-color);
            color: var(--sidenavbar-text-color) !important;
            box-shadow: none;
        }

        .accordion-arrow {
            transition: transform 0.3s ease;
            color: var(--sidenavbar-text-color) !important;
            font-size: 1.1em;
        }

        .accordion-button:not(.collapsed) .accordion-arrow {
            transform: rotate(90deg);
            color: var(--sidenavbar-text-color) !important;
        }

        .accordion-button::after {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%236b7280'%3E%3Cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
            transition: transform 0.3s ease;
        }

        .accordion-button:not(.collapsed)::after {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%236b7280'%3E%3Cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
        }

        .accordion-body {
            padding: 1rem 1.5rem;
            background-color: var(--sidenavbar-body-color);
            color: var(--sidenavbar-text-color);
        }

        .permission-item-container {
            background-color: var(--body-background-color);
            border-radius: 8px !important;
            padding: 0;
            margin-bottom: 10px;
            border: 2px solid var(--border-color);
            transition: all 0.2s ease;
        }

        .permission-toggle-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            background-color: var(--body-background-color);
            border-radius: 8px;
        }

        .child-permission{
            border-radius: 0 !important;
        }

        .child-permission:last-child{
            border-radius: 8px !important;
        }

        .permission-label {
            font-weight: 600;
            color: var(--sidenavbar-text-color);
            margin-bottom: 0;
            display: flex;
            align-items: center;
        }

        .child-permission .permission-label {
            font-weight: 400;
        }

        .permission-icon {
            margin-right: 10px;
            color: var(--sidenavbar-text-color);
            font-size: 1.1em;
        }

        .child-permission .permission-icon {
            color: var(--sidenavbar-text-color);
            font-size: 1.1em;
        }

        .child-permissions {
        }

        .child-permissions:last-child {
            border-radius: 8px !important;
        }


        .child-permissions .permission-toggle-container {
            padding: 10px 16px;
            background-color: var(--body-background-color);
        }

        .form-switch .form-check-input {
            width: 2.8em;
            height: 1.5em;
            cursor: pointer;
            background-color: var(--border-color);
            border-color: var(--border-color);
        }

        .accordion-header{
            color: var(--sidenavbar-text-color);
            font-size: 20px;
        }

        .form-switch .form-check-input:checked {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }

        .badge {
            color: white !important;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .btn {
                width: 100%;
                margin-bottom: 10px;
            }

            .accordion-body {
                padding: 1rem;
            }

            .permission-toggle-container {
                padding: 10px 12px;
            }

            .child-permissions {
            }
        }
    </style>
@endpush

@section('content')
    <x-Admin.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('admin_dashboard'), 'label' => 'Dashboard'],
            ['url' => route('roles.index'), 'label' => 'Roles'],
            ['url' => '', 'label' => 'Create role']
        ]"
    />
    <x-Admin.side-navbar :openSections="['AdminControl', 'UserRoles']" />
    <x-error-success-model />

    <div id="main">
        <section class="content my-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">Add Role</h4>
                            <a href="{{ route('roles.index') }}" class="btn btn-secondary" title="Go Back">
                                <i class="fas fa-arrow-left me-2"></i> Go Back
                            </a>
                        </div>

                        <div class="card shadow py-2 px-4 mb-5 rounded">
                            <div class="card-body">
                                <form action="{{ route('roles.store') }}" method="POST">
                                    @csrf

                                    <!-- Basic Information Section -->
                                    <div class="form-section">
                                        <div class="row">
                                            <!-- Name Field -->
                                            <div class="col-sm-12 col-md-12 col-lg-12">
                                                <div class="form-group mb-3">
                                                    <label for="name" class="form-label">Name <span class="required__field">*</span></label>
                                                    <div class="position-relative">
                                                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                                               value="{{ old('name') }}" maxlength="50" placeholder="Role Name" required>
                                                        <i class='bx bxs-user input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                    </div>
                                                    @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <label for="description" class="form-label">Description</label>
                                                    <textarea class="form-control" id="description" name="description" maxlength="250" placeholder="Description">{{ old('description') }}</textarea>
                                                    @error('description')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Permissions Section -->
                                    <div class="form-section mt-2">
                                        <h5 class="section-header d-flex align-items-center">
                                            <i class='bx bxs-key me-2'></i>Permissions
                                        </h5>

                                        @isset($permissions)
                                            @php
                                                $groupedPermissions = $permissions->groupBy(function($item) {
                                                    return $item->header;
                                                });

                                                $oldPermissions = old('permissions', []);
                                            @endphp

                                            <div class="accordion" id="permissionsAccordion">
                                                @foreach($groupedPermissions as $header => $permissionGroup)
                                                    <div class="accordion-item mb-3">
                                                        <h6 class="accordion-header" id="heading{{ $loop->index }}">
                                                            <button class="accordion-button collapsed" type="button"
                                                                    data-bs-toggle="collapse" data-bs-target="#collapse{{ $loop->index }}"
                                                                    aria-expanded="false" aria-controls="collapse{{ $loop->index }}">
                                                                <div class="d-flex align-items-center w-100">
                                                                    <i class='bx bx-category-alt me-2'></i>
                                                                    <span class="fw-bold">{{ $header ?? 'General Permissions' }}</span>
                                                                </div>
                                                            </button>
                                                        </h6>

                                                        <div id="collapse{{ $loop->index }}" class="accordion-collapse collapse"
                                                             aria-labelledby="heading{{ $loop->index }}" data-bs-parent="#permissionsAccordion">
                                                            <div class="accordion-body pt-2">
                                                                <div class="row">
                                                                    @foreach($permissionGroup->filter(fn($permission) => $permission->parent_id === null) as $permission)
                                                                        @php
                                                                            $hasChildren = $permission->children->isNotEmpty();
                                                                            // Determine checked state - use old input if available, otherwise use permission status
                                                                            $isChecked = array_key_exists($permission->id, $oldPermissions) ? (bool) $oldPermissions[$permission->id] : false;
                                                                        @endphp

                                                                        <div class="col-sm-12 mb-3 parent-permission">
                                                                            <div class="permission-item-container">
                                                                                <div class="permission-toggle-container border-bottom {{ $permission->children->count() > 0 ? 'rounded-bottom-0' : 'rounded-bottom' }}">

                                                                                    <label class="permission-label">
                                                                                        <i class='bx bxs-check-circle permission-icon'></i>
                                                                                        {{ $permission->name }}
                                                                                    </label>
                                                                                    <div class="form-check form-switch">
                                                                                        <input class="form-check-input permission-toggle parent-toggle"
                                                                                               type="checkbox"
                                                                                               name="permissions[{{ $permission->id }}]"
                                                                                               value="1"
                                                                                               data-permission-id="{{ $permission->id }}"
                                                                                            {{ $isChecked ? 'checked' : '' }}>
                                                                                    </div>
                                                                                </div>

                                                                                @if($hasChildren)
                                                                                    <div class="child-permissions">
                                                                                        @foreach($permission->children as $child)
                                                                                            @php
                                                                                                $childPermission = $permissionGroup->firstWhere('permission_id', $child->id);
                                                                                                // Determine checked state for child
                                                                                                $isChildChecked = array_key_exists($child->id, $oldPermissions)
                                                                                                    ? (bool) $oldPermissions[$child->id] : false;
                                                                                            @endphp
                                                                                            <div class="permission-toggle-container child-permission">
                                                                                                <label class="permission-label">
                                                                                                    <i class='bx bx-radio-circle permission-icon'></i>
                                                                                                    {{ $child->name }}
                                                                                                </label>
                                                                                                <div class="form-check form-switch">
                                                                                                    <input class="form-check-input permission-toggle child-toggle"
                                                                                                           type="checkbox"
                                                                                                           name="permissions[{{ $child->id }}]"
                                                                                                           value="1"
                                                                                                           data-parent-id="{{ $permission->id }}"
                                                                                                        {{ $isChildChecked ? 'checked' : '' }}>
                                                                                                </div>
                                                                                            </div>
                                                                                        @endforeach
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center fw-bold">No permissions found.</div>
                                        @endisset
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="d-flex justify-content-end mt-4">
                                        <button type="submit" class="btn btn-primary px-4">
                                            <i class='bx bx-save me-1'></i> Create Role
                                        </button>
                                    </div>
                                </form>
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
        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.parent-toggle').forEach(toggle => {
                const parentId = toggle.getAttribute('data-permission-id');
                const childToggles = document.querySelectorAll(`.child-toggle[data-parent-id="${parentId}"]`);

                if(!toggle.checked) {
                    childToggles.forEach(child => {
                        child.checked = false;
                        child.disabled = true;
                    });
                }

                toggle.addEventListener('change', function() {
                    if(this.checked) {
                        childToggles.forEach(child => {
                            child.disabled = false;
                        });
                    } else {
                        childToggles.forEach(child => {
                            child.checked = false;
                        });
                    }
                });
            });
        });
    </script>
@endpush
