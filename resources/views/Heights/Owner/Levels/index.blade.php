@extends('layouts.app')

@section('title', 'Levels')

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
            ['url' => route('owner_manager_dashboard'), 'label' => 'Dashboard'],
            ['url' => '', 'label' => 'Levels']
        ]"
    />
    <!--  -->
    <x-Owner.side-navbar :openSections="['Buildings', 'Levels']"/>
    <x-error-success-model />


    <div id="main">

        <section class="content mt-1 mb-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                            <div class="container mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h3 class="mb-1">Levels</h3>
                                    <a href="#" class="btn float-end" id="add_button"  data-bs-toggle="tooltip" data-bs-placement="top" title="Add Level"><i class="fa fa-plus"></i></a>
                                </div>
                                <div class="card shadow p-3 mb-5 bg-body rounded" style="border: none;">
                                    <div class="card-body " style="overflow-x: auto;">
                                        <table id="LevelsTable" class="table shadow-sm table-hover table-striped">
                                            <thead class="shadow">
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Description</th>
                                                <th>Level Number</th>
                                                <th>Status</th>
                                                <th>Building</th>
                                                <th class="w-170 text-center">Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($levels ?? [] as $level)
                                                <tr>
                                                    <td>{{ $level->id }}</td>
                                                    <td>{{ $level->level_name }}</td>
                                                    <td>{{ $level->description ?? 'N/A' }}</td>
                                                    <td>{{ $level->level_number ?? 'N/A' }}</td>
                                                    <td>
                                                        {{ $level->status ?? 'N/A' }}
                                                    </td>
                                                    <td>{{ $level->building->name ?? 'N/A' }}</td>
                                                    <td class="w-170 text-center">
                                                        <a href="{{ route('owner.units.index', ['level_id' => $level->id]) }}" class="text-info" title="View Units"><i class="bx bxs-home icons" style="font-size: 20px;margin-right:5px;;"></i></a>
                                                        <a href="#" class="text-warning edit_level_button" id="edit_level_button" data-id="{{ $level->id }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                            <i class="fa fa-pencil" style="font-size: 20px;"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">No levels found.</td>
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
    <div class="modal fade" id="createLevelModal" tabindex="-1" aria-labelledby="createLevelModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createLevelModalLabel">Add New Level</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createLevelForm" method="POST" action="{{ route('owner.levels.store') }}">
                    @csrf
                    <div class="modal-body">

                        <div class="row my-0 py-2">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="level_name">Level Name</label>
                                    <span class="required__field">*</span><br>
                                    <input type="text" name="level_name" id="level_name" class="form-control @error('level_name') is-invalid @enderror" value="{{ old('level_name') }}" maxlength="50" placeholder="Level Name" required>
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
                                    <input type="number" name="level_number" id="level_number" class="form-control @error('level_number') is-invalid @enderror" value="{{ old('level_number') }}" placeholder="Enter Level/Floor no" required>
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
                                    <input type="text" name="description" id="description" class="form-control @error('description') is-invalid @enderror" value="{{ old('description') }}" maxlength="50" placeholder="Description">
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
                                    <select class="form-select" id="building_id" name="building_id"  required>
                                        <option value="" disabled {{ old('building_id') === null ? 'selected' : '' }}>Select Building</option>
                                    </select>
                                    @error('building_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="status" value="Rejected">
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
        document.addEventListener("DOMContentLoaded", function () {
            // Show the 'Create Level' modal
            document.getElementById("add_button").addEventListener("click", function (e) {
                e.preventDefault();
                let createModal = new bootstrap.Modal(document.getElementById("createLevelModal"));
                createModal.show();

                // Perform AJAX request to get buildings
                fetchBuildings();
            });

            function fetchBuildings() {
                fetch("{{ route('owner.levels.create') }}")
                    .then(response => response.json())
                    .then(data => {
                        const buildingSelect = document.getElementById('building_id');
                        buildingSelect.innerHTML = '<option value="" disabled selected>Select Building</option>';

                        if (!data || data.length === 0) {
                            const option = document.createElement('option');
                            option.value = "";
                            option.textContent = "No buildings available";
                            option.disabled = true;
                            buildingSelect.appendChild(option);
                            return;
                        }

                        data.forEach(building => {
                            if (building && building.id && building.name) {
                                const option = document.createElement('option');
                                option.value = building.id;
                                option.textContent = building.name;
                                buildingSelect.appendChild(option);
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching buildings:', error);
                        alert("Failed to fetch buildings. Please try again.");
                    });
            }



            const editButtons = document.querySelectorAll(".edit_level_button");

            editButtons.forEach(button => {
                button.addEventListener("click", function (e) {
                    e.preventDefault();
                    const id = this.getAttribute("data-id"); // 'this' refers to the clicked button

                    fetch(`{{ route('owner.levels.edit', ':id') }}`.replace(':id', id), {
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
                                document.getElementById("edit_updated_at").value = data.level?.updated_at || "";

                                const buildingSelect = document.getElementById("edit_building_id");
                                buildingSelect.innerHTML = `<option value="" disabled>Select Building</option>`;

                                if (Array.isArray(data.buildings) && data.buildings.length > 0) {
                                    data.buildings.forEach(building => {
                                        if (building && building.id && building.name) {
                                            const isSelected = building.id === data.level?.building_id ? 'selected' : '';
                                            buildingSelect.innerHTML += `<option value="${building.id}" ${isSelected}>${building.name}</option>`;
                                        }
                                    });
                                } else {
                                    buildingSelect.innerHTML += `<option value="" disabled>No buildings available</option>`;
                                }


                                // Set form action dynamically
                                const editForm = document.getElementById("editLevelForm");
                                editForm.setAttribute("action", `{{ route('owner.levels.update', ':id') }}`.replace(':id', id));

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
