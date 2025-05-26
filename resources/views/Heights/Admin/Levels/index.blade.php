@extends('layouts.app')

@section('title', 'Levels')

@push('styles')
    <style>
        body {
        }
        #main {
            margin-top: 45px;
        }

        /* ================ FILTERS SECTION ================ */
        .filter-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 25px;
            background: var(--sidenavbar-body-color);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            align-items: flex-end;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            min-width: 220px;
        }

        .filter-group label {
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--sidenavbar-text-color);
            font-size: 0.9rem;
        }

        .filter-select, .search-input {
            width: 100%;
            padding: 10px 15px;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
            background-color: white;
            transition: all 0.3s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .search-input {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23999' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: 15px center;
            background-size: 16px 16px;
            padding-left: 40px;
        }

        .filter-buttons {
            display: flex;
            gap: 12px;
            margin-left: auto;
            align-self: center;
            margin-top: 30px;
        }

        .filter-buttons .btn {
            padding: 10px 15px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 120px;
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
        .levels-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .level-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: none;
            margin-bottom: 0;
            display: flex;
            flex-direction: column;
            height: 100%;
            background: var(--sidenavbar-body-color);
        }

        .level-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .level-card-header {
            padding: 15px 20px;
            background: linear-gradient(135deg, var(--color-blue) 0%, var(--sidenavbar-body-color) 100%);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .level-card-body {
            padding: 15px;
        }

        .level-card-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin: 0;
            color: white;
        }

        .level-card-number {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .level-card-detail {
            margin-bottom: 12px;
            display: flex;
            align-items: center;
        }
        .level-card-detail:last-child {
            margin-bottom: 2px;
            display: flex;
            align-items: center;
        }

        .level-card-detail i {
            margin-right: 10px;
            color: var(--sidenavbar-text-color);
            width: 20px;
            text-align: center;
        }

        .level-card-detail span {
            color: var(--sidenavbar-text-color);
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            margin-top: auto;
            flex-wrap: wrap;
            gap: 8px;
            padding: 0 8px 8px 8px;
        }

        .action-btn {
            flex: 1 1 calc(50% - 4px);
            margin: 0;
            padding: 8px 0;
            border-radius: 5px;
            font-size: 0.85rem;
            text-align: center;
            min-width: 100px;
            transition: all 0.2s ease;
        }

        /* Special Button Styles */
        .btn-add {
            padding: 10px 10px !important;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 110px;
            font-size: 0.95rem !important;
            text-decoration: none;
        }

        .btn-view {
            background-color: rgba(52, 152, 219, 0.1);
            color: #3498db;
            border: 1px solid rgba(52, 152, 219, 0.2);
        }

        .btn-view:hover {
            background-color: rgba(52, 152, 219, 0.2);
            color: #2980b9;
        }

        .btn-edit {
            background-color: rgba(46, 204, 113, 0.1);
            color: green;
            border: 1px solid rgba(46, 204, 113, 0.2);
        }

        .btn-edit:hover {
            background-color: rgba(46, 204, 113, 0.2);
            color: #27ae60;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            grid-column: 1 / -1;
        }

        .empty-state-icon {
            font-size: 3rem;
            color: #dee2e6;
            margin-bottom: 20px;
        }

        .empty-state-text {
            color: var(--sidenavbar-text-color);
            font-size: 1.1rem;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: capitalize;
        }

        .status-approved {
            background-color: #d4edda;
            color: #155724 !important;
        }

        .status-rejected {
            background-color: #f8d7da;
            color: #721c24 !important;
        }

        .building-tag {
            display: inline-block;
            background-color: #e9ecef;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            color: #495057 !important;
        }

        .card-icon {
            font-size: 1.2rem;
            vertical-align: middle;
            margin-right: 5px;
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
                                    <a href="#" class="btn float-end hidden Admin-Level-Add-Button add_button" id="Admin-Level-Add-Button" data-bs-toggle="tooltip" data-bs-placement="top" title="Add Level">
                                        <i class='bx bxs-layer-plus' style="font-size: 35px;"></i>
                                    </a>
                                </div>

                                <!-- Filter Form -->
                                <form method="GET" id="filterForm" class="filter-container">
                                    <div class="filter-group">
                                        <label for="search">Search</label>
                                        <input type="text" name="search" id="search" class="search-input"
                                               placeholder="Search by name"
                                               value="{{ request('search') }}">
                                    </div>

                                    <div class="filter-group">
                                        <label for="DepartmentId">Organization</label>
                                        <select name="DepartmentId" id="DepartmentId" class="form-select filter-select">
                                            <option value="">All Organizations</option>
                                        </select>
                                    </div>

                                    <div class="filter-group">
                                        <label for="BuildingId">Building</label>
                                        <select name="BuildingId" id="BuildingId" class="form-select filter-select">
                                            <option value="">All Buildings</option>
                                        </select>
                                    </div>

                                    <div class="filter-buttons">
                                        <button type="button" class="btn btn-secondary flex-grow-1 d-flex align-items-center justify-content-center" onclick="resetFilters()">
                                            <i class="fas fa-undo me-2"></i> Reset
                                        </button>
                                        <button type="submit" class="btn btn-primary flex-grow-1 d-flex align-items-center justify-content-center">
                                            <i class="fas fa-filter me-2"></i> Apply Filters
                                        </button>
                                    </div>
                                </form>

                                @if($levels)
                                    <div class="levels-container">
                                        @foreach($levels as $level)
                                            <div class="level-card">
                                                <div class="level-card-header">
                                                    <h3 class="level-card-title">{{ $level->level_name }}</h3>
                                                    <span class="level-card-number">Level {{ $level->level_number ?? 'N/A' }}</span>
                                                </div>
                                                <div class="level-card-body">
                                                    <div class="level-card-detail">
                                                        <i class="bx bxl-slack card-icon"></i>
                                                        <span class="building-tag">{{ $level->building->organization->name ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="level-card-detail">
                                                        <i class="bx bx-buildings card-icon"></i>
                                                        <span class="building-tag">{{ $level->building->name ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="level-card-detail">
                                                        <i class="bx bx-stats card-icon"></i>
                                                        <span class="status-badge status-{{ strtolower($level->status) }}">
                                                            {{ $level->status ?? 'N/A' }}
                                                        </span>
                                                    </div>
                                                    <div class="level-card-detail">
                                                        <i class="bx bx-info-circle card-icon"></i>
                                                        <span>{{ $level->description ?? 'No description provided' }}</span>
                                                    </div>
                                                </div>
                                                <div class="action-buttons">
                                                    <a href="{{ route('units.index', ['level_id' => $level->id]) }}" class="action-btn btn-add btn-view view-unit gap-1" title="View">
                                                        <i class='bx bxs-home'></i> Units
                                                    </a>

                                                    <a href="#" class="action-btn btn-add btn-edit gap-1 Admin-Level-Edit-Button hidden" id="Admin-Level-Edit-Button" data-id="{{ $level->id }}"  title="Edit">
                                                        <i class='bx bx-edit'></i> Edit
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="levels-container">
                                        <div class="empty-state">
                                            <div class="empty-state-icon">
                                                <i class="bx bx-layer"></i>
                                            </div>
                                            <h4>No Levels Found</h4>
                                            <p class="empty-state-text">You haven't added any levels yet. Click the button above to add your first level.</p>
                                        </div>
                                    </div>
                                @endif

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
