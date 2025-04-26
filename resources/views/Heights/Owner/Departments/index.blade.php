@extends('layouts.app')

@section('title', 'Departments')

@push('styles')
    <style>
        body {
        }
        #main {
            margin-top: 45px;
        }

        /* Model Windows */
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
    </style>
@endpush

@section('content')

    <!-- Top Navbar -->
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => url('owner_manager_dashboard'), 'label' => 'Dashboard'],
            ['url' => '', 'label' => 'Departments']
        ]"
    />

    <!-- Side Navbar -->
    <x-Owner.side-navbar :openSections="['Departments']" />
    <x-error-success-model />


    <div id="main">

        <section class="content mt-1 mb-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                            <div class="container mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h3 class="mb-1">Departments</h3>
                                    <a href="#" class="btn float-end add_button" id="Owner-Department-Add-Button"   data-bs-toggle="modal" data-bs-target="#createDepartmentModal" title="Add Level">
                                        <x-icon name="add" type="svg" class="" size="25" />
                                    </a>
                                </div>
                                <div class="card shadow p-3 mb-5 bg-body rounded" style="border: none;">
                                    <div class="card-body " style="overflow-x: auto;">
                                        <table id="LevelsTable" class="table shadow-sm table-hover table-striped">
                                            <thead class="shadow">
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Description</th>
                                                <th class="w-170 text-center">Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($departments ?? [] as $department)
                                                <tr>
                                                    <td>{{ $department->id }}</td>
                                                    <td>{{ $department->name }}</td>
                                                    <td>{{ $department->description ?? 'N/A' }}</td>
                                                    <td class="w-170 text-center">
                                                        <div class="d-flex justify-content-center align-items-center gap-3">
                                                            <a href="#" class="text-warning Admin-Level-Edit-Button hidden" data-id="{{ $department->id }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                                <x-icon name="edit" type="icon" class="" size="20px" />
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">No departments found.</td>
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

    <!-- Create Dropdwon Type Modal -->
    <div class="modal fade" id="createDepartmentModal" tabindex="-1" aria-labelledby="createDepartmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createDepartmentModalLabel">Add New Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createDepartmentForm" method="POST" action="{{ route('owner.departments.store') }}">
                    @csrf
                    <div class="modal-body">

                        <div class="row my-0 py-2">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="name">Name</label>
                                    <span class="required__field">*</span><br>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" maxlength="50" placeholder="Level Name" required>
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="description">Description</label>
                                    <input type="text" name="description" id="description" class="form-control @error('description') is-invalid @enderror" value="{{ old('description') }}" maxlength="50" placeholder="Description">
                                    @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

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

    <!-- Edit Dropdown Value Modal -->
    <div class="modal fade" id="editLevelModal" tabindex="-1" aria-labelledby="editLevelModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editLevelModalLabel">Edit Level</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div><!-- The edit form will be loaded here via AJAX -->
                <form id="editLevelForm" action="" method="POST">
                    @method('PUT')

                    <input type="hidden" name="updated_at" id="edit_updated_at">
                    <input type="hidden" name="level_id" id="edit_level_id">
                    <input type="hidden" name="organization_id" id="edit_organization_id">
                    <div class="modal-body">
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="level_name">Level Name</label>
                                    <span class="required__field">*</span><br>
                                    <input type="text" name="level_name" id="edit_level_name" class="form-control @error('level_name') is-invalid @enderror" value="{{ old('level_name') }}" maxlength="50" placeholder="Level Name" required>
                                    @error('level_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <!--  -->
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="level_number">Level Number</label>
                                    <span class="required__field">*</span><br>
                                    <input type="number" name="level_number" id="edit_level_number" class="form-control @error('level_number') is-invalid @enderror" value="{{ old('level_number' ) }}" min="0" placeholder="Enter Level/Floor no" required>
                                    @error('level_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="description">Description</label>
                                    <input type="text" name="description" id="edit_description" class="form-control @error('description') is-invalid @enderror" value="{{ old('description') }}" maxlength="50" placeholder="Description">
                                    @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <!--  -->
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="building_id">Building</label>
                                    <span class="required__field">*</span><br>
                                    <select class="form-select" id="edit_building_id" name="building_id" required>
                                        <option value="" disabled {{ old('building_id') === null ? 'selected' : '' }}>Select Building</option>

                                    </select>
                                    @error('building_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <span class="required__field">*</span><br>
                                    <select name="status" id="edit_status" class="form-select" required>
                                        <option value="Approved" {{ old('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="Rejected" {{ old('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                    @error('status')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

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
            new DataTable("#LevelsTable", {
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const editButtons = document.querySelectorAll(".Admin-Level-Edit-Button");

            editButtons.forEach(button => {
                button.addEventListener("click", function (e) {
                    e.preventDefault();
                    const id = this.getAttribute("data-id"); // 'this' refers to the clicked button

                    fetch(`{{ route('levels.edit', ':id') }}`.replace(':id', id), {
                        method: "GET",
                        headers: {
                            "X-Requested-With": "XMLHttpRequest",
                            "Accept": "application/json"
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.message) {
                                alert(data.message);
                            } else {
                                document.getElementById("edit_level_id").value = data.level?.id || "";
                                document.getElementById("edit_level_name").value = data.level?.level_name || "";
                                document.getElementById("edit_description").value = data.level?.description || "";
                                document.getElementById("edit_level_number").value = data.level.level_number !== undefined ? data.level.level_number : "";
                                document.getElementById("edit_status").value = data.level?.status || "";
                                document.getElementById("edit_organization_id").value = data.level?.organization_id || "";
                                document.getElementById("edit_updated_at").value = data.level?.updated_at || "";

                                const buildingSelect = document.getElementById("edit_building_id");
                                buildingSelect.innerHTML = `<option value="" disabled>Select Building</option>`;

                                if (Array.isArray(data.buildings) && data.buildings.length > 0) {
                                    data.buildings.forEach(building => {
                                        if (building && building.id && building.name) {
                                            const isSelected = building.id === data.level?.building_id ? 'selected' : '';
                                            buildingSelect.innerHTML += `<option value="${building.id}" ${isSelected} data-organization-id="${building.organization_id}">${building.name}</option>`;
                                        }
                                    });
                                } else {
                                    buildingSelect.innerHTML += `<option value="" disabled>No buildings available</option>`;
                                }


                                // Set form action dynamically
                                const editForm = document.getElementById("editLevelForm");
                                editForm.setAttribute("action", `{{ route('levels.update', ':id') }}`.replace(':id', id));

                                // Add hidden input for PUT method if not already present
                                if (!editForm.querySelector('input[name="_method"]')) {
                                    const methodInput = document.createElement("input");
                                    methodInput.type = "hidden";
                                    methodInput.name = "_method";
                                    methodInput.value = "PUT";
                                    editForm.appendChild(methodInput);
                                }

                                // Show the modal
                                let editModal = new bootstrap.Modal(document.getElementById("editLevelModal"));
                                editModal.show();
                            }
                        })
                        .catch(() => {
                            alert("An error occurred while retrieving the data.");
                        });
                });
            });
        });

    </script>

@endpush
