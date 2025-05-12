@extends('layouts.app')

@section('title', 'Roles')

@push('styles')
    <style>
        body {
        }
        #main {
            margin-top: 45px;
        }

        .modal-content{
            background: var(--modal-header-bg);
            color: var(--modal-text);
        }

        .modal-header {
            background: var(--modal-header-bg);
            color: var(--modal-text);
        }

        .modal-title {
            font-weight: bold;
            width: 100%;
            text-align: center;
        }

        .modal-body {
            background: var(--modal-bg);
            color: var(--modal-text);
        }

        .modal-footer {
            background: var(--modal-bg);
            border-top: 1px solid var(--modal-border);
        }

        .btn-close {
            filter: invert(var(--invert, 0));
        }

        /* DataTables Entries Dropdown */
        .dataTables_wrapper .dataTables_length select {
            background-color: var(--dataTable-paginate-menu-bg-color);
            color: var(--dataTable-paginate-menu-text-color);
            border: 1px solid var(--dataTable-paginate-menu-border-color);
        }
        .dataTables_wrapper .dataTables_length label {
            color: var(--dataTable-paginate-menu-label-color);
        }
        /* DataTables Search Box */
        .dataTables_wrapper .dataTables_filter input {
            background-color: var(--dataTable-search-input-bg-color);
            color: var(--dataTable-search-input-text-color);
            border: 1px solid var(--dataTable-search-input-border-color);
        }
        .dataTables_wrapper .dataTables_filter label {
            color: var(--dataTable-search-label-color);
        }
        .dataTables_filter input::placeholder {
            color: var(--dataTable-search-placeholder-color); /* Standard */
        }
        .dataTables_filter input::-webkit-input-placeholder {
            color: var(--dataTable-search-placeholder-color); /* WebKit browsers */
        }
        .dataTables_filter input::-moz-placeholder {
            color: var(--dataTable-search-placeholder-color); /* Mozilla Firefox 19+ */
        }
        .dataTables_filter input:-ms-input-placeholder {
            color: var(--dataTable-search-placeholder-color); /* Internet Explorer 10+ */
        }




    </style>
@endpush

@section('content')

    <!--  -->
    <x-Admin.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('admin_dashboard'), 'label' => 'Dashboard'],
            ['url' => '', 'label' => 'Roles']
        ]"
    />
    <!--  -->
    <x-Admin.side-navbar :openSections="['AdminControl','UserRoles']" />
    <x-error-success-model />

    <div id="main">

        <section class="content mt-1 mb-3 mx-2">
            <div class="container-fluid ">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box mx-0">
                            <div class="container mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h3 class="mb-1">Roles</h3>
                                    <a href="#" class="btn float-end add_button" id="add_button"> <x-icon name="add" type="svg" class="" size="25" /></a>
                                </div>
                                <div class="card shadow p-3 mb-5 bg-body rounded" style="border: none;">
                                    <div class="card-body" style="overflow-x: auto;">
                                        <table id="rolesTable" class="table shadow-sm table-hover table-striped">
                                            <thead class="shadow">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Role Name</th>
                                                    <th>Description</th>
                                                    <th>Status</th>
                                                    <th class="text-center" style="width: 100px;">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($roles ?? [] as $role)
                                                    <tr>
                                                        <td>{{ $role->id }}</td>
                                                        <td>{{ $role->name }}</td>
                                                        <td>{{ $role->description }}</td>
                                                        <td>{{ $role->status ? 'Active' : 'Inactive' }}</td>
                                                        <td class="text-center" style="width: 100px;">
                                                            <a href="#" class="text-warning edit-role-button" id="edit-role-button" data-id="{{ $role->id }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                                <x-icon name="edit" type="icon" class="" size="20px" />
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center">No roles found</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Create Role Modal -->
    <div class="modal fade" id="createRoleModal" tabindex="-1" aria-labelledby="createRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
            <div class="modal-header">
                    <h5 class="modal-title" id="createRoleModalLabel">Create new role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createRoleForm" method="POST" action="{{ route('roles.store') }}" >
                    @csrf
                    <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Role Name</label>
                                <span class="required__field">*</span><br>
                                <div class="position-relative">
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" maxlength="20" placeholder="Role Name" required>
                                    <i class='bx bx-street-view input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                </div>
                                @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <span class="required__field">*</span><br>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status', 1) == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" maxlength="250" placeholder="Description">{{ old('description') }}</textarea>
                                @error('description')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Permissions -->
                        <div class="mb-3">
                            <label class="form-label">Permissions <span class="text-danger">*</span></label>
                            <div class="" id="permissions-container">
                                {{-- Permissions checkboxes will be loaded here via AJAX --}}
                            </div>
                            <div id="permissions-error" class="text-danger mt-2"></div>
                            @error('permissions')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Role Modal -->
    <div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editRoleModalLabel">Edit Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- The edit form will be loaded here via AJAX -->
                <form id="editForm" action="" method="POST" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                        <input type="hidden" name="role_id" id="edit_role_id">
                        <input type="hidden" name="updated_at" id="edit_updated_at">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_name" class="form-label">Role Name <span class="text-danger">*</span></label>
                                <div class="position-relative">
                                    <input type="text" class="form-control" id="edit_name" name="name" maxlength="20" placeholder="Role Name" required>
                                    <i class='bx bx-street-view input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_status" name="status" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="edit_description" class="form-label">Description</label>
                                <textarea class="form-control" id="edit_description" name="description" maxlength="250" placeholder="Description"></textarea>
                            </div>
                        </div>

                        <!-- Permissions -->
                        <div class="mb-3">
                            <label class="form-label">Permissions <span class="text-danger">*</span></label>
                            <div class="" id="permissionsContainer"></div>
                            <small class="text-danger d-none" id="permissionsError">At least one permission must be selected.</small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')

    <!-- Add DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>

    <!-- Add DataTables Buttons JS -->
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

    <!-- Data Tables Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            new DataTable("#rolesTable", {
                pageLength: 10,
                lengthMenu: [10, 20, 50, 100],
                language: {
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    },
                    searchPlaceholder: "Search users..."
                }
            });
        });
    </script>


    <!-- Roles Scripts -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            document.addEventListener("click", function (e) {
                if (e.target.closest(".edit-role-button")) {
                    e.preventDefault();
                    const button = e.target.closest(".edit-role-button");
                    const id = button.getAttribute("data-id");

                    fetch(`{{ route('roles.edit', ':id') }}`.replace(":id", id), {
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
                            if (data.message) {
                                alert(data.message);
                            } else if (data.role) {
                                // Populate role details
                                document.getElementById("edit_name").value = data.role.name;
                                document.getElementById("edit_description").value = data.role.description;
                                document.getElementById("edit_status").value = data.role.status;
                                document.getElementById("edit_role_id").value = data.role.id;
                                document.getElementById("edit_updated_at").value = data.role.updated_at;

                                // Clear old permissions
                                const permissionsContainer = document.getElementById("permissionsContainer");
                                permissionsContainer.innerHTML = "";

                                if (data.permissions.length > 0) {
                                    let permissionsHtml = `<div class="row">`; // 3 parents per row

                                    data.permissions.forEach((parentPermission, index) => {
                                        // Start a new row every 3 parents
                                        if (index % 3 === 0 && index !== 0) {
                                            permissionsHtml += `</div><div class="row">`;
                                        }

                                        const isChecked = data.activePermissionsId.includes(parentPermission.id) ? "checked" : "";

                                        permissionsHtml += `
                                <div class="col-12 mb-1">
                                    <div class="card p-2">
                                        <div class="d-flex justify-content-between align-items-center mx-2">
                                            <label class="fw-bold mb-0" for="permission_${parentPermission.id}">
                                                ${parentPermission.name}
                                            </label>
                                            <input class="form-check-input parent-permission"
                                                   type="checkbox"
                                                   name="permissions[]"
                                                   value="${parentPermission.id}"
                                                   id="permission_${parentPermission.id}" ${isChecked}>
                                        </div>
                            `;

                                        // Only add child permissions section if there are children
                                        if (parentPermission.children.length > 0) {
                                            permissionsHtml += `
                                    <div class="child-permissions mt-2 row pt-2 pb-0 mb-0 border-top" id="child-container-${parentPermission.id}" style="padding-right: 8px;">
                                `;

                                            parentPermission.children.forEach(child => {
                                                const isChildChecked = data.activePermissionsId.includes(child.id) ? "checked" : "";
                                                permissionsHtml += `
                                        <div class="form-check col-md-6 col-12">
                                            <label class="form-check-label d-flex justify-content-between w-100" for="permission_${child.id}">
                                                ${child.name}
                                                <input class="form-check-input child-permission ms-2"
                                                       type="checkbox"
                                                       name="permissions[]"
                                                       value="${child.id}"
                                                       id="permission_${child.id}" ${isChildChecked}
                                                       data-parent-id="${parentPermission.id}">
                                            </label>
                                        </div>
                                    `;
                                            });

                                            permissionsHtml += `</div>`; // Close child container
                                        }

                                        permissionsHtml += `</div></div>`; // Close parent card & column
                                    });

                                    permissionsHtml += `</div>`; // Close last row
                                    permissionsContainer.innerHTML = permissionsHtml;

                                    attachEditCheckboxLogic();
                                } else {
                                    permissionsContainer.innerHTML = '<p class="text-danger">No permissions available.</p>';
                                }

                                // Set form action URL
                                document.getElementById("editForm").setAttribute("action", `{{ route('roles.update', ':id') }}`.replace(":id", id));

                                // Show the modal
                                let editRoleModal = new bootstrap.Modal(document.getElementById("editRoleModal"));
                                editRoleModal.show();
                            } else {
                                console.error("One or more input fields are missing in the DOM.");
                            }
                        })
                        .catch(error => {
                            console.error("Error:", error);
                            alert("An error occurred while retrieving the data.");
                        });
                }
            });

            // Parent-child checkbox logic with toggle effect
            function attachEditCheckboxLogic() {
                document.querySelectorAll(".parent-permission").forEach(parentCheckbox => {
                    parentCheckbox.addEventListener("change", function () {
                        const parentId = this.value;
                        const childContainer = document.getElementById(`child-container-${parentId}`);

                        if (childContainer) {
                            document.querySelectorAll(`.child-permission[data-parent-id="${parentId}"]`)
                                .forEach(childCheckbox => {
                                    childCheckbox.checked = this.checked;
                                });
                        }
                    });
                });

                document.querySelectorAll(".child-permission").forEach(childCheckbox => {
                    childCheckbox.addEventListener("change", function () {
                        const parentId = this.getAttribute("data-parent-id");
                        const parentCheckbox = document.querySelector(`#permission_${parentId}`);

                        if (this.checked) {
                            parentCheckbox.checked = true;
                        } else {
                            const anyChecked = document.querySelectorAll(`.child-permission[data-parent-id="${parentId}"]:checked`).length > 0;
                            if (!anyChecked) {
                                parentCheckbox.checked = false;
                            }
                        }
                    });
                });
            }


            document.getElementById("editForm").addEventListener("submit", function (e) {
                let permissionCheckboxes = document.querySelectorAll('input[name="permissions[]"]:checked');
                let permissionsError = document.getElementById("permissionsError");

                if (permissionCheckboxes.length === 0) {
                    e.preventDefault();
                    permissionsError.classList.remove("d-none");
                } else {
                    permissionsError.classList.add("d-none");
                }
            });


            //  Add Role Model
            const addButton = document.getElementById("add_button");
            const createRoleModal = document.getElementById("createRoleModal");
            const permissionsContainer = document.getElementById("permissions-container");
            const permissionsError = document.getElementById("permissions-error");

            if (addButton) {
                addButton.addEventListener("click", function (e) {
                    e.preventDefault();

                    // Clear permissions before fetching new ones
                    permissionsContainer.innerHTML = '<p class="text-muted">Loading permissions...</p>';

                    // Show modal
                    let modalInstance = new bootstrap.Modal(createRoleModal);
                    modalInstance.show();

                    // Fetch permissions
                    fetchPermissions();
                });
            }

            function fetchPermissions() {
                fetch("{{ route('roles.create') }}", {
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
                        permissionsContainer.innerHTML = ""; // Clear previous permissions

                        if (data.permissions && data.permissions.length > 0) {
                            let permissionsHtml = `<div class="row">`; // 3 parents per row

                            data.permissions.forEach((permission, index) => {
                                // Start a new row every 3 parents
                                if (index % 3 === 0 && index !== 0) {
                                    permissionsHtml += `</div><div class="row">`;
                                }

                                permissionsHtml += `
                        <div class="col-12 mb-1">
                            <div class="card p-2">
                                <div class="d-flex justify-content-between align-items-center mx-2">
                                    <label class="fw-bold mb-0" for="permission_${permission.id}">
                                        ${permission.name}
                                    </label>
                                    <input class="form-check-input parent-permission" type="checkbox"
                                           name="permissions[]"
                                           value="${permission.id}"
                                           id="permission_${permission.id}">
                                </div>
                    `;

                                // Only add child permissions section if there are children
                                if (permission.children.length > 0) {
                                    permissionsHtml += `
                            <div class="child-permissions mt-2 row mx-2 pt-2 pb-0 mb-0 border-top d-none" id="child-container-${permission.id}">
                        `;

                                    permission.children.forEach(child => {
                                        permissionsHtml += `
                                <div class="form-check col-md-6 col-12">
                                    <label class="form-check-label d-flex justify-content-between w-100" for="permission_${child.id}">
                                        ${child.name}
                                        <input class="form-check-input child-permission ms-2" type="checkbox"
                                               name="permissions[]"
                                               value="${child.id}"
                                               id="permission_${child.id}"
                                               data-parent-id="${permission.id}">
                                    </label>
                                </div>
                            `;
                                    });

                                    permissionsHtml += `</div>`; // Close child container
                                }

                                permissionsHtml += `</div></div>`; // Close parent card & column
                            });

                            permissionsHtml += `</div>`; // Close last row
                            permissionsContainer.innerHTML = permissionsHtml;
                            attachCheckboxLogic();
                        } else {
                            permissionsContainer.innerHTML = '<p class="text-danger">No permissions found.</p>';
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        permissionsError.textContent = "Failed to load permissions. Please try again.";
                    });
            }

            // Parent-child checkbox logic with toggle effect
            function attachCheckboxLogic() {
                document.querySelectorAll(".parent-permission").forEach(parentCheckbox => {
                    parentCheckbox.addEventListener("change", function () {
                        const parentId = this.value;
                        const childContainer = document.getElementById(`child-container-${parentId}`);

                        if (childContainer) {
                            if (this.checked) {
                                childContainer.classList.remove("d-none"); // Show children
                                document.querySelectorAll(`.child-permission[data-parent-id="${parentId}"]`)
                                    .forEach(childCheckbox => {
                                        childCheckbox.checked = true;
                                    });
                            } else {
                                childContainer.classList.add("d-none"); // Hide children
                                document.querySelectorAll(`.child-permission[data-parent-id="${parentId}"]`)
                                    .forEach(childCheckbox => {
                                        childCheckbox.checked = false;
                                    });
                            }
                        }
                    });
                });

                document.querySelectorAll(".child-permission").forEach(childCheckbox => {
                    childCheckbox.addEventListener("change", function () {
                        const parentId = this.getAttribute("data-parent-id");
                        const parentCheckbox = document.querySelector(`#permission_${parentId}`);

                        if (this.checked) {
                            parentCheckbox.checked = true;
                        } else {
                            const anyChecked = document.querySelectorAll(`.child-permission[data-parent-id="${parentId}"]:checked`).length > 0;
                            if (!anyChecked) {
                                parentCheckbox.checked = false;
                            }
                        }
                    });
                });
            }



        });

    </script>


@endpush
