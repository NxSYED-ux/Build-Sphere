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

        /* ================ CARD DESIGN IMPROVEMENTS ================ */
        .levels-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
            margin-top: 20px;
        }

        .level-card {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: none;
            display: flex;
            flex-direction: column;
            height: 100%;
            background: var(--sidenavbar-body-color);
            position: relative;
        }

        .level-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }

        .level-card-header {
            padding: 16px 20px;
            background: var(--sidenavbar-body-color);
            color: var(--sidenavbar-text-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .level-card-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.8) 50%, rgba(255,255,255,0.3) 100%);
        }

        .level-card-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0;
            color: var(--sidenavbar-text-color);
            letter-spacing: 0.2px;
        }

        .level-card-number {
            background: var(--body-background-color);
            color: var(--sidenavbar-text-color);
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            backdrop-filter: blur(2px);
        }

        .level-card-body {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .level-card-detail {
            margin-bottom: 14px;
            display: flex;
            align-items: flex-start;
        }

        .level-card-detail:last-child {
            margin-bottom: 0;
        }

        .card-icon {
            color: var(--color-blue);
            font-size: 1.1rem;
            margin-right: 12px;
            margin-top: 2px;
            flex-shrink: 0;
        }

        /* Status and Tags */
        .status-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }

        .status-approved {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745 !important;
        }

        .status-rejected {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545 !important;
        }

        .building-tag {
            display: inline-block;
            /*background-color: rgba(108, 117, 125, 0.1);*/
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 0.85rem;
            color: var(--sidenavbar-text-color) !important;
            font-weight: 500;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            margin-top: auto;
            gap: 10px;
            padding: 0 20px 20px 20px;
        }

        .action-btn {
            flex: 1;
            padding: 8px 0;
            border-radius: 8px;
            font-size: 0.8rem;
            text-align: center;
            font-weight: 500;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-view {
            background-color: rgba(52, 152, 219, 0.08);
            color: #3498db;
            border: 1px solid rgba(52, 152, 219, 0.15);
            text-decoration: none;
        }

        .btn-view:hover {
            background-color: rgba(52, 152, 219, 0.15);
        }

        .btn-edit {
            background-color: rgba(40, 167, 69, 0.08);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.15);
            text-decoration: none;
        }

        .btn-edit:hover {
            background-color: rgba(40, 167, 69, 0.15);
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

        .card-icon {
            font-size: 1.2rem;
            vertical-align: middle;
            margin-right: 5px;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .level-card {
            animation: fadeInUp 0.5s ease forwards;
            opacity: 0;
        }

        .level-card:nth-child(1) { animation-delay: 0.1s; }
        .level-card:nth-child(2) { animation-delay: 0.2s; }
        .level-card:nth-child(3) { animation-delay: 0.3s; }
        .level-card:nth-child(4) { animation-delay: 0.4s; }
        .level-card:nth-child(5) { animation-delay: 0.5s; }
        .level-card:nth-child(6) { animation-delay: 0.6s; }
        .level-card:nth-child(7) { animation-delay: 0.7s; }
        .level-card:nth-child(8) { animation-delay: 0.8s; }
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

        <section class="content my-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                            <div class="container mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h3 class="mb-1">Levels</h3>
                                    <a href="#" class="btn btn-primary d-flex align-items-center hidden Owner-Level-Add-Button" id="Owner-Level-Add-Button"  data-bs-toggle="tooltip" data-bs-placement="top" title="Add Level">
                                        <x-icon name="add" type="svg" class="me-1" size="18" /> Add Level
                                    </a>
                                </div>

                                <!-- Filter Form -->
                                <form method="GET" id="filterForm" class="filter-container">
                                    <div class="filter-group">
                                        <label for="search">Search</label>
                                        <input type="text" name="search" id="search" class="search-input"
                                               placeholder="Search by name or description"
                                               value="{{ request('search') }}">
                                    </div>

                                    <div class="filter-group">
                                        <label for="organization_id">Building</label>
                                        <select name="building_id" id="building_id" class="form-select filter-select">
                                            <option value="">All buildings</option>
                                            @foreach($buildings ?? [] as $building)
                                                <option value="{{ $building->id }}" {{ request('building_id') == $building->id ? 'selected' : '' }}>
                                                    {{ $building->name }}
                                                </option>
                                            @endforeach
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

                                <div class="levels-container">
                                    @forelse($levels ?? [] as $level)
                                        <div class="level-card">
                                            <div class="level-card-header">
                                                <h3 class="level-card-title">{{ $level->level_name }}</h3>
                                                <span class="level-card-number">Level {{ $level->level_number ?? 'N/A' }}</span>
                                            </div>
                                            <div class="level-card-body">
                                                <div class="level-card-detail">
                                                    <i class="bx bx-buildings card-icon"></i>
                                                    <span class="building-tag">{{ $level->building->name ?? 'N/A' }}</span>
                                                </div>
                                                <div class="level-card-detail">
                                                    <i class="bx bx-stats card-icon"></i>
                                                    <span class="status-badge status-{{ strtolower($level->status) }} mx-2">
                                                        {{ $level->status ?? 'N/A' }}
                                                    </span>
                                                </div>
                                                <div class="level-card-detail">
                                                    <i class="bx bx-info-circle card-icon"></i>
                                                    <span class="mx-2">{{ $level->description ?? 'No description provided' }}</span>
                                                </div>
                                            </div>
                                            <div class="action-buttons">
                                                <a href="{{ route('owner.units.index', ['level_id' => $level->id]) }}" class="action-btn btn-add btn-view view-unit gap-1" title="View">
                                                    <i class='bx bxs-home'></i> Units
                                                </a>

                                                <a href="#" class="action-btn btn-add btn-edit gap-1 Owner-Level-Edit-Button hidden" id="Owner-Level-Edit-Button" data-id="{{ $level->id }}"  title="Edit">
                                                    <i class='bx bx-edit'></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="empty-state">
                                            <div class="empty-state-icon">
                                                <i class="bx bx-layer"></i>
                                            </div>
                                            <h4>No Levels Found</h4>
                                            <p class="empty-state-text">You haven't added any levels yet. Click the button above to add your first level.</p>
                                        </div>
                                    @endforelse
                                </div>

                                @if ($levels && $levels->count() > 0)
                                    <div class="mt-3">
                                        {{ $levels->appends(request()->query())->links('pagination::bootstrap-5') }}
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Create Level Modal -->
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

                        <div class="row my-0 py-1">
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

                            <!--  -->
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="building_id">Building</label>
                                    <span class="required__field">*</span><br>
                                    <select class="form-select" id="building_id" name="building_id" required>
                                        <option value="" disabled selected>Loading buildings...</option>
                                    </select>
                                    @error('building_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="2" maxlength="50" placeholder="Description">{{ old('description') }}</textarea>
                                    @error('description')
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

    <!-- Edit Level Modal -->
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
                        <div class="row my-0 py-1">
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
                                    <textarea name="description" id="edit_description" class="form-control @error('description') is-invalid @enderror" rows="2" maxlength="50" placeholder="Description">{{ old('description') }}</textarea>
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
        function resetFilters() {
            window.location.href = '{{ route("owner.levels.index") }}';
        }

        document.addEventListener("DOMContentLoaded", function () {
            // Show the 'Create Level' modal
            document.getElementById("Owner-Level-Add-Button").addEventListener("click", function (e) {
                e.preventDefault();
                const createModal = new bootstrap.Modal(document.getElementById("createLevelModal"), {
                    focus: false
                });
                createModal.show();

                // Perform AJAX request to get buildings
                fetchBuildings();
            });

            function fetchBuildings() {
                console.log('Fetching buildings...');

                fetch("{{ route('owner.levels.create') }}", {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        console.log('Response status:', response.status);
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(buildings => {
                        console.log('Buildings data:', buildings);

                        const buildingSelect = document.querySelector('#createLevelModal #building_id');
                        if (!buildingSelect) {
                            console.error('Building select not found in modal');
                            return;
                        }

                        // Clear and rebuild options
                        buildingSelect.innerHTML = '';

                        // Add default option
                        const defaultOption = new Option('Select Building', '', true, true);
                        defaultOption.disabled = true;
                        buildingSelect.appendChild(defaultOption);

                        // Add building options
                        buildings.forEach(building => {
                            const option = new Option(building.name, building.id);
                            buildingSelect.appendChild(option);
                        });

                        console.log('Buildings dropdown populated');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        const buildingSelect = document.querySelector('#createLevelModal #building_id');
                        if (buildingSelect) {
                            buildingSelect.innerHTML = '';
                            const errorOption = new Option('Error loading buildings', '', false, false);
                            errorOption.disabled = true;
                            buildingSelect.appendChild(errorOption);
                        }
                    });
            }



            const editButtons = document.querySelectorAll(".Owner-Level-Edit-Button");

            editButtons.forEach(button => {
                button.addEventListener("click", function (e) {
                    e.preventDefault();
                    const id = this.getAttribute("data-id"); // 'this' refers to the clicked button

                    fetch(`{{ route('owner.levels.edit', ':level') }}`.replace(':level', id), {
                        method: "GET",
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.message || data.error ){
                                alert(data.message || data.error);
                            } else {
                                document.getElementById("edit_level_id").value = data.level?.id || "";
                                document.getElementById("edit_level_name").value = data.level?.level_name || "";
                                document.getElementById("edit_description").value = data.level?.description || "";
                                document.getElementById("edit_level_number").value = data.level?.level_number !== undefined ? data.level.level_number : "";
                                document.getElementById("edit_updated_at").value = data.level?.updated_at || "";

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
                        .catch((e) => {
                            alert(e);
                        });
                });
            });
        });

    </script>

@endpush
