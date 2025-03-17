@extends('layouts.app')

@section('title', 'Roles')

@push('styles')
    <style>
        body {
        }
        #main {
            margin-top: 45px;
        }

        #add_button {
            width: 45px;
            height: 45px;
            margin-right: 10px;
            background-color: #adadad;
            color: black;
            border: 1px solid grey;
            font-size: 25px;
            font-weight: bold;
            align-items: center;
            justify-content: center;
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
            ['url' => url('admin_dashboard'), 'label' => 'Dashboard'],
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
                                    <a href="#" class="btn float-end" id="add_button"> <i class="fa fa-plus"></i> </a>
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
                                                            <a href="#" class="text-warning edit-role-button" id="edit-role-button" data-id="{{ $role->id }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i class="fa fa-pencil" style="font-size: 20px;"></i></a>
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
                            <div class="row" id="permissions-container">
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
                <form id="editForm" action="" method="POST">
                    @method('PUT')
                    <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                        <input type="hidden" name="role_id" id="edit_role_id">
                        <input type="hidden" name="updated_at" id="edit_updated_at">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Role Name</label>
                                <span class="required__field">*</span><br>
                                <div class="position-relative">
                                    <input type="text" class="form-control" id="edit_name" name="name" maxlength="20" placeholder="Role Name" required>
                                    <i class='bx bx-street-view input-icon position-absolute top-50 end-0 translate-middle-y me-3'></i>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <span class="required__field">*</span><br>
                                <select class="form-select" id="edit_status" name="status" required>
                                    <option value="1" >Active</option>
                                    <option value="0" >Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="edit_description" name="description" maxlength="250" placeholder="Description"></textarea>
                            </div>
                        </div>

                        <!-- Permissions -->
                        <div class="mb-3">
                            <label class="form-label">Permissions <span class="text-danger">*</span></label>
                            <div class="row" id="permissionsContainer"></div>
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
            //  Edit Role Model
            document.addEventListener("click", function (e) {
                if (e.target.closest(".edit-role-button")) {
                    e.preventDefault();
                    const button = e.target.closest(".edit-role-button");
                    const id = button.getAttribute("data-id");

                    fetch(`{{ route('roles.edit', ':id') }}`.replace(":id", id), {
                        method: "GET",
                        headers: {
                            "X-Requested-With": "XMLHttpRequest"
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.message) {
                                alert(data.message);
                            } else if (data.role) {
                                let role = data.role;
                                document.getElementById("edit_name").value = role.name;
                                document.getElementById("edit_description").value = role.description;
                                document.getElementById("edit_status").value = role.status;
                                document.getElementById("edit_role_id").value = role.id;
                                document.getElementById("edit_updated_at").value = role.updated_at;

                                // Clear old permissions
                                const permissionsContainer = document.getElementById("permissionsContainer");
                                permissionsContainer.innerHTML = "";

                                if (data.permissions.length > 0) {
                                    data.permissions.forEach(permission => {
                                        const isChecked = data.activePermissionsId.includes(permission.id) ? "checked" : "";
                                        permissionsContainer.innerHTML += `
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="${permission.id}" id="permission_${permission.id}" ${isChecked}>
                                            <label class="form-check-label" for="permission_${permission.id}">${permission.name}</label>
                                        </div>
                                    </div>
                                `;
                                    });
                                } else {
                                    permissionsContainer.innerHTML = '<p class="text-danger">No permissions available.</p>';
                                }

                                // Set the form action URL
                                document.getElementById("editForm").setAttribute("action", `{{ route('roles.update', ':id') }}`.replace(":id", id));

                                // Append hidden _method input for PUT request
                                if (!document.querySelector("#editForm input[name='_method']")) {
                                    let hiddenMethodInput = document.createElement("input");
                                    hiddenMethodInput.type = "hidden";
                                    hiddenMethodInput.name = "_method";
                                    hiddenMethodInput.value = "PUT";
                                    document.getElementById("editForm").appendChild(hiddenMethodInput);
                                }

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

                // Validate form before submission
                document.getElementById("editForm").addEventListener("submit", function (e) {
                if (document.querySelectorAll('input[name="permissions[]"]:checked').length === 0) {
                    e.preventDefault();
                    document.getElementById("permissionsError").classList.remove("d-none");
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
                    let modalInstance = new bootstrap.Modal(createRoleModal);
                    modalInstance.show();

                    // Fetch permissions when the modal opens
                    fetchPermissions();
                });
            }

            function fetchPermissions() {
                fetch("{{ route('roles.create') }}", {
                    method: "GET",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.permissions) {
                            let permissionsHtml = "";
                            data.permissions.forEach(permission => {
                                permissionsHtml += `
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           name="permissions[]"
                                           value="${permission.id}"
                                           id="permission_${permission.id}">
                                    <label class="form-check-label" for="permission_${permission.id}">
                                        ${permission.name}
                                    </label>
                                </div>
                            </div>
                        `;
                            });

                            permissionsContainer.innerHTML = permissionsHtml;
                        } else {
                            permissionsContainer.innerHTML = '<p class="text-danger">No permissions found.</p>';
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        permissionsError.textContent = "Failed to load permissions. Please try again.";
                    });
            }
        });


    </script>


@endpush
