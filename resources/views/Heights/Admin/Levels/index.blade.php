@extends('layouts.app')

@section('title', 'Levels')

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

        /* Card View Styles */
        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px 0;
        }

        .level-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }

        .level-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .card-body {
            padding: 15px;
        }

        .card-footer {
            padding: 15px;
            background-color: #f8f9fa;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .level-name {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .level-info {
            color: #6c757d;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }

        .level-description {
            margin: 10px 0;
            color: #495057;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .no-levels {
            text-align: center;
            padding: 40px;
            color: #6c757d;
            font-size: 1.1rem;
        }
    </style>
@endpush

@section('content')

    <!-- Top Navbar -->
    <x-Admin.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('admin_dashboard'), 'label' => 'Dashboard'],
            ['url' => '', 'label' => 'Levels']
        ]"
    />

    <!-- Side Navbar -->
    <x-Admin.side-navbar :openSections="['Buildings', 'Levels']" />
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
                                    <a href="#" class="btn float-end hidden add_button" id="Admin-Level-Add-Button"  data-bs-toggle="tooltip" data-bs-placement="top" title="Add Level">
                                        <i class='bx bxs-layer-plus' style="font-size: 35px;"></i>
                                    </a>
                                </div>
                                <div class="card shadow p-3 mb-5 bg-body rounded" style="border: none;">
                                    <div class="card-body">
                                        @if($levels && count($levels) > 0)
                                            <div class="card-container">
                                                @foreach($levels as $level)
                                                    <div class="level-card">
                                                        <div class="card-header">
                                                            <div class="level-name">{{ $level->level_name }}</div>
                                                            <div class="level-info">Level #{{ $level->level_number ?? 'N/A' }}</div>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="level-description">
                                                                {{ $level->description ?? 'No description available' }}
                                                            </div>
                                                            <div class="level-info">
                                                                <strong>Building:</strong> {{ $level->building->name ?? 'N/A' }}
                                                            </div>
                                                        </div>
                                                        <div class="card-footer">
                                                            <span class="status-badge status-{{ strtolower($level->status) }}">
                                                                {{ $level->status ?? 'N/A' }}
                                                            </span>
                                                            <div class="action-buttons">
                                                                <a href="{{ route('units.index', ['level_id' => $level->id]) }}" class="text-info" title="View Units" data-bs-toggle="tooltip">
                                                                    <x-icon name="view" type="icon" class="" size="20px" />
                                                                </a>
                                                                <a href="#" class="text-warning Admin-Level-Edit-Button hidden" data-id="{{ $level->id }}" title="Edit" data-bs-toggle="tooltip">
                                                                    <x-icon name="edit" type="icon" class="" size="20px" />
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="no-levels">
                                                <i class='bx bx-building-house' style="font-size: 50px; margin-bottom: 15px;"></i>
                                                <p>No levels found.</p>
                                            </div>
                                        @endif
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
                <form id="createLevelForm" method="POST" action="{{ route('levels.store') }}">
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

                        <input type="hidden" name="status" value="Approved">
                        <input type="hidden" name="organization_id" id="organization_id">
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buildingSelect = document.getElementById('building_id');
            const organizationIdInput = document.getElementById('organization_id');

            buildingSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const organizationId = selectedOption.getAttribute('data-organization-id');
                organizationIdInput.value = organizationId;
            });

            const editBuildingSelect = document.getElementById('edit_building_id');
            const editOrganizationIdInput = document.getElementById('edit_organization_id');

            editBuildingSelect.addEventListener('change', function() {
                const editSelectedOption = this.options[this.selectedIndex];
                const editOrganizationId = editSelectedOption.getAttribute('data-organization-id');
                editOrganizationIdInput.value = editOrganizationId;
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
            // Show the 'Create Level' modal
            document.getElementById("Admin-Level-Add-Button").addEventListener("click", function (e) {
                e.preventDefault();
                let createModal = new bootstrap.Modal(document.getElementById("createLevelModal"));
                createModal.show();

                // Perform AJAX request to get buildings
                fetchBuildings();
            });

            function fetchBuildings() {
                fetch("{{ route('levels.create') }}", {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
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
                                option.setAttribute('data-organization-id', building.organization_id);
                                buildingSelect.appendChild(option);
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching buildings:', error);
                        alert("Failed to fetch buildings. Please try again.");
                    });
            }

            const editButtons = document.querySelectorAll(".Admin-Level-Edit-Button");

            editButtons.forEach(button => {
                button.addEventListener("click", function (e) {
                    e.preventDefault();
                    const id = this.getAttribute("data-id"); // 'this' refers to the clicked button

                    fetch(`{{ route('levels.edit', ':id') }}`.replace(':id', id), {
                        method: 'GET',
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
