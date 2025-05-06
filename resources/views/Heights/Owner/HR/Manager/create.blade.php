@extends('layouts.app')

@section('title', 'Add Manager')

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

        .accordion-body {
            padding: 1rem 1.5rem;
            background-color: var(--sidenavbar-body-color);
            color: var(--sidenavbar-text-color);
        }

        .permission-item-container {
            background-color: var(--body-background-color);
            border-radius: 8px;
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
            border: 1px solid var(--border-color) !important;
        }

         .child-permission{
            border-radius: 0 !important;
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
    <!-- Top Navbar -->
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
        ['url' => route('owner_manager_dashboard'), 'label' => 'Dashboard'],
        ['url' => route('owner.managers.index'), 'label' => 'Managers'],
        ['url' => '', 'label' => 'Add Manager']
    ]" />

    <!-- Side Navbar -->
    <x-Owner.side-navbar :openSections="['Managers']" />

    <!-- Error/Success Modal -->
    <x-error-success-model />

    <div id="main">
        <section class="content my-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                            <div class="container">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="mb-0">Add Manager</h4>
                                    <a href="{{ route('owner.managers.index') }}" class="btn btn-secondary" title="Go Back">
                                        <i class="fas fa-arrow-left me-2"></i> Go Back
                                    </a>
                                </div>

                                <div class="card shadow py-2 px-4 mb-5 rounded">
                                    <div class="card-body">
                                        <form action="{{ route('owner.managers.store') }}" method="POST">
                                            @csrf

                                            <!-- Basic Information Section -->
                                            <div class="form-section">
                                                <h5 class="section-header">
                                                    <i class='bx bxs-user-detail'></i> Basic Information
                                                </h5>
                                                <div class="row">
                                                    <!-- Name Field -->
                                                    <div class="col-sm-12 col-md-6 col-lg-4">
                                                        <div class="form-group mb-3">
                                                            <label for="name" class="form-label">Name <span class="required__field">*</span></label>
                                                            <div class="position-relative">
                                                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                                                       value="{{ old('name') }}" maxlength="50" placeholder="Manager Name" required>
                                                                <i class='bx bxs-user input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                            </div>
                                                            @error('name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <!-- Email Field -->
                                                    <div class="col-sm-12 col-md-6 col-lg-4">
                                                        <div class="form-group mb-3">
                                                            <label for="email" class="form-label">Email <span class="required__field">*</span></label>
                                                            <div class="position-relative">
                                                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                                                                       value="{{ old('email') }}" placeholder="Email" maxlength="50" required>
                                                                <i class='bx bxs-envelope input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                            </div>
                                                            @error('email')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <!-- Gender Field -->
                                                    <div class="col-sm-12 col-md-6 col-lg-4">
                                                        <div class="form-group mb-3">
                                                            <label for="gender" class="form-label">Gender <span class="required__field">*</span></label>
                                                            <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                                                <option value="">Select Gender</option>
                                                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                                                <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                                            </select>
                                                            @error('gender')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <!-- Phone Field -->
                                                    <div class="col-sm-12 col-md-6 col-lg-4">
                                                        <div class="form-group mb-3">
                                                            <label for="contact" class="form-label">Phone Number</label>
                                                            <div class="position-relative">
                                                                <input type="text" name="phone_no" id="contact" value="{{ old('phone_no') }}"
                                                                       class="form-control contact @error('phone_no') is-invalid @enderror" placeholder="0312-3456789" maxlength="14">
                                                                <i class='bx bxs-mobile input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                            </div>
                                                            @error('phone_no')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <!-- CNIC Field -->
                                                    <div class="col-sm-12 col-md-6 col-lg-4">
                                                        <div class="form-group mb-3">
                                                            <label for="cnic" class="form-label">CNIC <span class="required__field">*</span></label>
                                                            <div class="position-relative">
                                                                <input type="text" name="cnic" id="cnic" class="form-control @error('cnic') is-invalid @enderror"
                                                                       value="{{ old('cnic') }}" maxlength="15" placeholder="12345-1234567-1" required>
                                                                <i class='bx bxs-id-card input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                                            </div>
                                                            @error('cnic')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <!-- Date of Birth Field -->
                                                    <div class="col-sm-12 col-md-6 col-lg-4">
                                                        <div class="form-group mb-3">
                                                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                                                            <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth"
                                                                   value="{{ old('date_of_birth') }}">
                                                            @error('date_of_birth')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Buildings Section -->
                                            <div class="form-section mt-4">
                                                <h5 class="section-header">
                                                    <i class='bx bxs-building me-2'></i> Select Buildings
                                                </h5>
                                                <div class="row">
                                                    @foreach($buildings as $building)
                                                    <div class="col-sm-12 col-md-6 col-xl-4">
                                                        <div class="form-group mb-3">
                                                            <div class="permission-toggle-container">
                                                                <label class="permission-label">
                                                                    <i class='bx bxs-check-circle permission-icon'></i>
                                                                    {{ $building->name }}
                                                                </label>
                                                                <div class="form-check form-switch">
                                                                    <input class="form-check-input permission-toggle parent-toggle"
                                                                           type="checkbox"
                                                                           name="buildings[{{ $building->id }}]"
                                                                           value="1">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>


                                            <!-- Permissions Section -->
                                            <div class="form-section mt-4">
                                                <h5 class="section-header d-flex align-items-center">
                                                    <i class='bx bxs-key me-2'></i>Permissions
                                                </h5>

                                                @isset($permissions)
                                                @php
                                                    $groupedPermissions = $permissions->groupBy(function($item) {
                                                        return $item->permission->header;
                                                    });
                                                @endphp

                                                <div class="accordion" id="permissionsAccordion">
                                                    @foreach($groupedPermissions as $header => $permissionGroup)
                                                        <div class="accordion-item mb-3">
                                                            <h6 class="accordion-header" id="heading{{ $loop->index }}">
                                                                <button class="accordion-button collapsed" type="button"
                                                                        data-bs-toggle="collapse" data-bs-target="#collapse{{ $loop->index }}"
                                                                        aria-expanded="false" aria-controls="collapse{{ $loop->index }}">
                                                                    <div class="d-flex align-items-center w-100">
                                                                        <span class="fw-bold">{{ $header ?? 'General Permissions' }}</span>
                                                                    </div>
                                                                </button>
                                                            </h6>

                                                            <div id="collapse{{ $loop->index }}" class="accordion-collapse collapse"
                                                                 aria-labelledby="heading{{ $loop->index }}" data-bs-parent="#permissionsAccordion">
                                                                <div class="accordion-body pt-2">
                                                                    <div class="row">
                                                                        @foreach($permissionGroup->filter(fn($permission) => $permission->permission->parent_id === null) as $permission)
                                                                            @php
                                                                                $hasChildren = $permission->permission->children->isNotEmpty();
                                                                            @endphp

                                                                            <div class="col-sm-12 mb-3 parent-permission">
                                                                                <div class="permission-item-container">
                                                                                    <div class="permission-toggle-container border-bottom rounded-bottom-0">
                                                                                        <label class="permission-label">
                                                                                            <i class='bx bxs-check-circle permission-icon'></i>
                                                                                            {{ $permission->permission->name }}
                                                                                        </label>
                                                                                        <div class="form-check form-switch">
                                                                                            <input type="hidden" name="permissions[{{ $permission->permission_id }}]" value="0">
                                                                                            <input class="form-check-input permission-toggle parent-toggle"
                                                                                                   type="checkbox"
                                                                                                   name="permissions[{{ $permission->permission_id }}]"
                                                                                                   value="1"
                                                                                                   data-permission-id="{{ $permission->permission_id }}"
                                                                                                {{ $permission->status ? 'checked' : '' }}>
                                                                                        </div>
                                                                                    </div>

                                                                                    @if($hasChildren)
                                                                                        <div class="child-permissions">
                                                                                            @foreach($permission->permission->children as $child)
                                                                                                @php
                                                                                                    $childPermission = $permissionGroup->firstWhere('permission_id', $child->id);
                                                                                                @endphp
                                                                                                <div class="permission-toggle-container child-permission">
                                                                                                    <label class="permission-label">
                                                                                                        <i class='bx bx-radio-circle permission-icon'></i>
                                                                                                        {{ $child->name }}
                                                                                                    </label>
                                                                                                    <div class="form-check form-switch">
                                                                                                        <input type="hidden" name="permissions[{{ $childPermission->permission_id }}]" value="0">
                                                                                                        <input class="form-check-input permission-toggle child-toggle"
                                                                                                               type="checkbox"
                                                                                                               name="permissions[{{ $childPermission->permission_id }}]"
                                                                                                               value="1"
                                                                                                               data-parent-id="{{ $permission->permission_id }}"
                                                                                                            {{ $childPermission && $childPermission->status ? 'checked' : '' }}>
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
                                                    <i class='bx bx-save me-1'></i> Create Staff
                                                </button>
                                            </div>
                                        </form>
                                    </div>
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
        document.getElementById('accept_query').addEventListener('change', function() {
            const badge = this.closest('.form-group').querySelector('.badge');
            if (this.checked) {
                badge.textContent = 'Enabled';
                badge.classList.remove('bg-danger');
                badge.classList.add('bg-success');
            } else {
                badge.textContent = 'Disabled';
                badge.classList.remove('bg-success');
                badge.classList.add('bg-danger');
            }
        });

        // CNIC formatting
        document.getElementById('cnic').addEventListener('input', function (e) {
            const x = e.target.value.replace(/\D/g, '').match(/(\d{0,5})(\d{0,7})(\d{0,1})/);
            e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2] + (x[3] ? '-' + x[3] : '');
        });

        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.parent-toggle').forEach(toggle => {
                const parentId = toggle.getAttribute('data-permission-id');
                const childToggles = document.querySelectorAll(`.child-toggle[data-parent-id="${parentId}"]`);

                if(!toggle.checked) {
                    childToggles.forEach(child => {
                        child.checked = false;
                        child.disabled = true;
                        const hiddenInput = child.closest('.form-switch').querySelector('input[type="hidden"]');
                        if (hiddenInput) {
                            hiddenInput.value = "0";
                        }
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
                            child.disabled = true;
                            const hiddenInput = child.closest('.form-switch').querySelector('input[type="hidden"]');
                            if (hiddenInput) {
                                hiddenInput.value = "0";
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
