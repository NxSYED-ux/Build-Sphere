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
            margin-right: 5px;
        }

        .permission-header-icon #roleIconFill{
            fill: var(--sidenavbar-text-color);
        }


        .toggle-btn {
            width: 50px;
            height: 25px;
            border-radius: 100px;
            background-color: #ccc;
            border: 2px solid #ced4da;
            position: relative;
            cursor: pointer;
            transition: background-color 0.3s ease, border-color 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding: 2px;
            outline: none;
        }



        .toggle-btn:hover {
            background-color: #dee2e6;
        }

        .toggle-switch {
            width: 20px;
            height: 20px;
            background-color: white;
            border-radius: 100px;
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
            left: calc(100% - 21px);
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
                /*flex-direction: column;*/
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
                    <div class="card mt-4 border-0">
                        <div class="card-header d-flex flex-column flex-lg-row justify-content-between align-items-center text-center text-lg-start permission-header">
                            <h4 class="mb-2 mb-lg-0 permission-header-text">
                                Permissions for Role: {{ $roles ? optional($roles->where('id', $roleId)->first())->name : 'Name'}}
                            </h4>

                            <div class="d-flex flex-column flex-sm-row align-items-center">
                                <svg width="20" height="20" class="permission-header-icon mb-2 mb-sm-0 me-sm-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17.6771 12.554C16.6364 12.554 15.6192 12.8625 14.7539 13.4407C13.8887 14.0188 13.2143 14.8406 12.816 15.802C12.4178 16.7634 12.3136 17.8213 12.5166 18.842C12.7196 19.8626 13.2208 20.8001 13.9566 21.536C14.6924 22.2718 15.63 22.7729 16.6506 22.9759C17.6712 23.1789 18.7292 23.0748 19.6906 22.6765C20.652 22.2783 21.4737 21.6039 22.0519 20.7386C22.63 19.8734 22.9386 18.8561 22.9386 17.8155C22.9313 16.4223 22.3747 15.0882 21.3895 14.1031C20.4043 13.1179 19.0703 12.5612 17.6771 12.554ZM18.6001 18.277C18.457 18.2735 18.3159 18.2422 18.1848 18.1847L16.2001 20.1693C16.0889 20.2795 15.9411 20.3452 15.7848 20.354C15.7055 20.3616 15.6256 20.3487 15.5528 20.3163C15.48 20.284 15.4168 20.2333 15.3694 20.1693C15.2581 20.0505 15.1962 19.8937 15.1962 19.7309C15.1962 19.568 15.2581 19.4113 15.3694 19.2924L17.354 17.3078C17.3014 17.1751 17.2703 17.0349 17.2617 16.8924C17.2406 16.6724 17.2654 16.4503 17.3343 16.2403C17.4033 16.0302 17.515 15.8368 17.6624 15.672C17.8098 15.5073 17.9897 15.3748 18.1908 15.283C18.3919 15.1912 18.6099 15.142 18.8309 15.1386C18.9741 15.1421 19.1151 15.1734 19.2463 15.2309C19.3386 15.2309 19.3386 15.3232 19.2925 15.3693L18.3694 16.2463C18.348 16.257 18.3301 16.2735 18.3175 16.2939C18.305 16.3143 18.2983 16.3377 18.2983 16.3616C18.2983 16.3856 18.305 16.409 18.3175 16.4294C18.3301 16.4498 18.348 16.4663 18.3694 16.477L18.9694 17.077C18.9858 17.0981 19.0068 17.1152 19.0308 17.1269C19.0548 17.1387 19.0811 17.1448 19.1078 17.1448C19.1346 17.1448 19.1609 17.1387 19.1849 17.1269C19.2089 17.1152 19.2299 17.0981 19.2463 17.077L20.1232 16.2001C20.1694 16.154 20.3078 16.154 20.3078 16.2463C20.3558 16.3803 20.3868 16.5199 20.4001 16.6616C20.3967 16.8887 20.3463 17.1126 20.2522 17.3193C20.158 17.526 20.0222 17.711 19.8532 17.8627C19.6841 18.0144 19.4856 18.1295 19.2699 18.2008C19.0543 18.2721 18.8263 18.2981 18.6001 18.277Z" id="roleIconFill"/>
                                    <path d="M10.0154 12.8308C13.3036 12.8308 15.9692 10.1652 15.9692 6.87694C15.9692 3.58872 13.3036 0.923096 10.0154 0.923096C6.72715 0.923096 4.06152 3.58872 4.06152 6.87694C4.06152 10.1652 6.72715 12.8308 10.0154 12.8308Z" id="roleIconFill"/>
                                    <path d="M11.631 22.9846C12.6464 22.9846 12.0925 22.2923 12.0925 22.2923C11.0735 21.023 10.5197 19.4431 10.5233 17.8153C10.5184 16.7951 10.7391 15.7865 11.1694 14.8615C11.1888 14.8087 11.2205 14.7612 11.2618 14.723C11.5848 14.0769 10.9387 14.0307 10.9387 14.0307C10.648 13.9923 10.3548 13.9769 10.0618 13.9846C7.88877 13.993 5.79075 14.7799 4.14808 16.2024C2.50541 17.6249 1.42684 19.5889 1.10791 21.7384C1.10791 22.2 1.24637 23.0307 2.67714 23.0307H11.4925C11.5848 22.9846 11.5848 22.9846 11.631 22.9846Z" id="roleIconFill"/>
                                </svg>

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
                                        <!-- Collapsible Header -->
                                        <h5 class="text mb-3 font-weight-bold permission-Heading">
                                            <a class="text-decoration-none font-weight-bold permission-Heading d-flex justify-content-between align-items-center w-100" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ Str::slug($header) }}" aria-expanded="true" onclick="toggleIcon(this)">
                                                {{ $header }}
                                                <i class="bx bx-chevron-down collapse-icon fw-bold" style="font-size: 25px; margin-right: 5px;"></i>
                                            </a>
                                        </h5>

                                        <!-- Collapsible Content -->
                                        <div class="collapse show" id="collapse-{{ Str::slug($header) }}">
                                            <div class="row">
                                                @foreach ($perms ?? [] as $perm)
                                                    <div class="col-md-12 mb-2 border rounded-4 shadow-sm">
                                                        <div class="permission-item d-flex justify-content-between align-items-center px-2">
                                                            <span class="permission-name font-weight-medium  d-inline-flex align-items-center">{{ $perm['name'] }}</span>
                                                            <button class="toggle-btn {{ $perm['status'] == 0 ? '' : 'active' }}"
                                                                    data-permission-id="{{ $perm['id'] }}"
                                                                    data-role-id="{{ $roleId }}"
                                                                    data-status="{{ $perm['status'] }}"
                                                                    onclick="togglePermission(this)">
                                                                <div class="toggle-switch"></div>
                                                            </button>
                                                        </div>

                                                        @if ($perm['children']->isNotEmpty())
                                                            <div class="row mt-1 px-1 py-2" style="border-top: 1px solid #e3e6f0;">
                                                                @foreach ($perm['children'] as $child)
                                                                    <div class="col-12 mb-1">
                                                                        <div class="d-flex justify-content-between align-items-center">
                                                                            <span class="permission-name font-weight-medium d-inline-flex align-items-center">
                                                                                <i class='bx bx-radio-circle' style="margin-right: 10px;"></i>
                                                                                {{ $child['name'] }}
                                                                            </span>
                                                                            <button class="toggle-btn {{ $child['status'] == 0 ? '' : 'active' }}"
                                                                                    data-permission-id="{{ $child['id'] }}"
                                                                                    data-role-id="{{ $roleId }}"
                                                                                    data-status="{{ $child['status'] }}"
                                                                                    onclick="togglePermission(this)">
                                                                                <div class="toggle-switch"></div>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
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

            let parentContainer = button.closest('.border.rounded-4'); // Parent wrapper
            let isChildPermission = button.closest('.row.mt-1'); // Check if it's a child
            let parentButton = parentContainer ? parentContainer.querySelector('.toggle-btn') : null; // Find the parent button

            // **Restrict activating child if parent is inactive**
            if (isChildPermission && parentButton && parseInt(parentButton.getAttribute("data-status"), 10) === 0) {
                Swal.fire({
                    title: "Action Denied",
                    text: "You cannot activate a child permission while the parent is inactive.",
                    icon: "warning",
                    confirmButtonText: 'OK',
                    timer: 3000,
                    timerProgressBar: true,
                    background: getComputedStyle(document.documentElement).getPropertyValue('--swal-bg-color').trim(),
                    color: getComputedStyle(document.documentElement).getPropertyValue('--swal-text-color').trim(),
                    iconColor: getComputedStyle(document.documentElement).getPropertyValue('--swal-icon-warning-color').trim(),
                    customClass: {
                        popup: 'theme-swal-popup',
                        confirmButton: 'theme-swal-button'
                    }
                });
                return;
            }

            fetch("{{ route('toggle.role.permission') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}" // Ensure CSRF token is included
                },
                body: JSON.stringify({ permission_id: permissionId, role_id: roleId, status: newStatus })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        button.setAttribute("data-status", newStatus);
                        button.classList.toggle("active", newStatus === 1);

                        // **Parent Logic: If deactivated, deactivate all children**
                        if (!isChildPermission && newStatus === 0) {
                            let childButtons = parentContainer.querySelectorAll('.row.mt-1 .toggle-btn');
                            childButtons.forEach(childBtn => {
                                childBtn.setAttribute("data-status", 0);
                                childBtn.classList.remove("active");
                            });
                        }

                        Swal.fire({
                            title: "Success!",
                            text: newStatus === 1 ? "Permission assigned successfully" : "Permission removed successfully",
                            icon: "success",
                            confirmButtonText: 'OK',
                            timer: 3000,
                            timerProgressBar: true,
                            background: getComputedStyle(document.documentElement).getPropertyValue('--swal-bg-color').trim(),
                            color: getComputedStyle(document.documentElement).getPropertyValue('--swal-text-color').trim(),
                            iconColor: getComputedStyle(document.documentElement).getPropertyValue('--swal-icon-success-color').trim(),
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



        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".collapse").forEach((collapseElement) => {
                collapseElement.addEventListener("shown.bs.collapse", function () {
                    let icon = document.querySelector(`[data-bs-target="#${this.id}"] .collapse-icon`);
                    icon.classList.remove("bx-chevron-right");
                    icon.classList.add("bx-chevron-down");
                });

                collapseElement.addEventListener("hidden.bs.collapse", function () {
                    let icon = document.querySelector(`[data-bs-target="#${this.id}"] .collapse-icon`);
                    icon.classList.remove("bx-chevron-down");
                    icon.classList.add("bx-chevron-right");
                });
            });
        });


    </script>
@endpush
