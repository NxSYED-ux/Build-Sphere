@extends('layouts.app')

@section('title', 'Roles')

@push('styles')
    <style>
        body {
        }
        #main {
            margin-top: 45px;
        }

        /* Permission Header */
         .permission-header {
             background-color: var(--permission-header-bg) !important;
             padding: 15px;
             border-radius: 8px;
             border-bottom: 2px solid #ddd;
         }

        .permission-header-text {
            font-weight: bold;
            color: var(--permission-header-color);
            font-size: 1.2rem;
        }
        .text-muted{
            color: var(--main-text-color) !important;
        }

        .form-select-sm {
            min-width: 150px;
        }

        .permission-header-icon {
            color: #008CFF;
        }

        /* Toggle Button Styling */
        .toggle-btn {
            width: 40px;
            height: 20px;
            border-radius: 25px;
            background-color: #e9ecef;
            border: 2px solid #ced4da;
            position: relative;
            cursor: pointer;
            transition: background 0.3s ease, border-color 0.3s ease;
            display: flex;
            align-items: center;
            padding: 3px;
            outline: none;
            flex-shrink: 0;
        }

        .toggle-btn:hover {
            background-color: #dee2e6;
        }

        .toggle-switch {
            width: 20px;
            height: 15px;
            background-color: white;
            border-radius: 50%;
            position: absolute;
            left: 3px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .toggle-btn.active {
            background-color: #4CAF50;
            border-color: #4CAF50;
        }

        .toggle-btn.active .toggle-switch {
            left: calc(100% - 23px);
        }

        /* Permission List Styling */
        .permission-list {
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            background-color:  var(--body-background-color);
            overflow: hidden;
            padding: 10px;
        }

        .permission-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1.5rem;
            border-bottom: 1px solid #e3e6f0;
            transition: background-color 0.2s ease;
        }

        .permission-item:hover {
            /*background-color: #f8f9fa;*/
            opacity: 0.8;
        }

        .permission-item:last-child {
            border-bottom: none;
        }

        .permission-name {
            font-size: 1rem;
            color: #4a4a4a;
            flex-grow: 1;
        }

        /* Card Styling */
        .card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            background-color: #f8f9fa;
            border-bottom: 1px solid #e3e6f0;
        }

        .card-body {
            padding: 1.5rem;
            background-color: var(--permission-card-bg-color);
        }

        /* Permission Heading */
        .permission-Heading {
            font-size: 1.1rem;
            font-weight: bold;
            color: var(--permission-header-color);
        }



        /* Responsive Design */
        @media (max-width: 768px) {
            .permission-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .toggle-btn {
                margin-top: 0.5rem;
            }
        }

    </style>

@endpush

@section('content')

    <!--  -->
    <x-Admin.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('admin_dashboard'), 'label' => 'Dashboard'],
            ['url' => '', 'label' => 'Role Permissions']
        ]"
    />
    <!--  -->
    <x-Admin.side-navbar :openSections="['AdminControl','RolePermissions']" />
    <x-error-success-model />

    <div id="main">
        <section class="content my-3 mx-2">
            <div class="container">

                <!-- Permissions Card -->
                @if ($roleId)
                    <div class="card mt-4 shadow-lg border-0">
                        <div class="card-header d-flex justify-content-between align-items-center permission-header">
                            <h4 class="mb-0 permission-header-text">Permissions for Role: {{ $roles ? optional($roles->where('id', $roleId)->first())->name : 'Name'}}</h4>

                            <div class="d-flex align-items-center">
                                <i class="bx bx-street-view permission-header-icon me-2 fs-5"></i>
                                <form method="GET" action="{{ route('role.permissions') }}">
                                    <select name="role_id" id="role" class="form-select form-select-sm" onchange="this.form.submit()">
                                        @forelse ($roles ?? [] as $role)
                                            <option value="{{ $role->id }}" {{ $roleId == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @empty
                                            <option value="">Select Role</option>
                                        @endforelse
                                    </select>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            @if ($permissions->isEmpty())
                                <p class="text-muted">No permissions assigned to this role.</p>
                            @else
                                @foreach ($permissions ?? [] as $header => $perms)
                                    <div class="mb-4">
                                        <h5 class="text mb-3 font-weight-bold permission-Heading">{{ $header }}</h5>
                                        <div class="permission-list rounded-lg shadow-sm p-3">
                                            <div class="row">
                                                @foreach ($perms ?? [] as $perm)
                                                    <div class="col-md-6 col-lg-4 mb-3">
                                                        <div class="permission-item d-flex justify-content-between align-items-center p-1 px-2 border rounded shadow-sm">
                                                            <span class="permission-name font-weight-medium ">{{ $perm['name'] }}</span>
                                                            <button class="toggle-btn {{ $perm['status'] == 0 ? '' : 'active' }}"
                                                                    data-permission-id="{{ $perm['id'] }}"
                                                                    data-role-id="{{ $roleId }}"
                                                                    data-status="{{ $perm['status'] }}"
                                                                    onclick="togglePermission(this)">
                                                                <div class="toggle-switch"></div>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        function togglePermission(button) {
            let permissionId = button.getAttribute("data-permission-id");
            let roleId = button.getAttribute("data-role-id");
            let currentStatus = parseInt(button.getAttribute("data-status"), 10);
            let newStatus = currentStatus === 1 ? 0 : 1;

            fetch("{{ route('toggle.role.permission') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ permission_id: permissionId, role_id: roleId, status: newStatus })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        button.setAttribute("data-status", newStatus);
                        button.classList.toggle("active", newStatus === 1);

                        let title = "Success!";
                        let text = newStatus === 1 ? "Permission assigned successfully" : "Permission removed successfully";
                        let icon = "success";
                        let iconColor = getComputedStyle(document.documentElement).getPropertyValue('--swal-icon-success-color').trim();

                        Swal.fire({
                            title: title,
                            text: text,
                            icon: icon,
                            confirmButtonText: 'OK',
                            timer: 3000,
                            timerProgressBar: true,
                            background: getComputedStyle(document.documentElement).getPropertyValue('--swal-bg-color').trim(),
                            color: getComputedStyle(document.documentElement).getPropertyValue('--swal-text-color').trim(),
                            iconColor: iconColor,
                            customClass: {
                                popup: 'theme-swal-popup',
                                confirmButton: 'theme-swal-button'
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to update permission status',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            timer: 3000,
                            timerProgressBar: true,
                            background: getComputedStyle(document.documentElement).getPropertyValue('--swal-bg-color').trim(),
                            color: getComputedStyle(document.documentElement).getPropertyValue('--swal-text-color').trim(),
                            iconColor: getComputedStyle(document.documentElement).getPropertyValue('--swal-icon-error-color').trim(),
                            customClass: {
                                popup: 'theme-swal-popup',
                                confirmButton: 'theme-swal-button'
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("An error occurred while updating the permission status.");
                });
        }
    </script>
@endpush
