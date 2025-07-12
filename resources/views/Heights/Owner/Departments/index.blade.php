@extends('layouts.app')

@section('title', 'Departments')

@push('styles')
    <style>
        body {
        }
        #main {
            margin-top: 45px;
        }

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

        @media (max-width: 768px) {
            .filter-group {
                min-width: 100%;
            }

            .filter-buttons {
                width: 100%;
                margin-left: 0;
                margin-top: 10px;
            }

            .filter-buttons .btn {
                flex-grow: 1;
            }
        }

        /* ================ VIEW TOGGLE ================ */
        .grid-view-toggle {
            display: flex;
            align-items: center;
            margin-right: 15px;
        }

        .grid-view-toggle .btn {
            padding: 0.375rem 0.75rem;
        }

        /* ================ TABLE VIEW ================ */
        #tableView {
            display: none;
            margin-top: 0!important;
            padding-top: 0 !important;
        }

        /* Grid Layout */
        .departments-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin: 0 -10px;
        }

        @media (max-width: 768px) {
            .departments-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Department Cards */
        .department-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            border: none;
            background: var(--body-background-color) !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            padding: 16px;
            height: auto;
            color: var(--sidenavbar-text-color);
            position: relative;
            display: flex;
            flex-direction: column;
            min-height: 220px; /* Reduced height */
        }

        /* Card Header */
        .department-card .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding: 0;
            background: var(--body-background-color);
            background: transparent;
            border: none;
        }

        .department-card .card-header span {
            font-weight: 600;
            font-size: 1rem;
            color: var(--sidenavbar-text-color);
        }

        .department-card .department-id {
            font-size: 0.75rem;
            color: var(--sidenavbar-text-color);
            background: rgba(255,255,255,0.7);
            padding: 2px 6px;
            border-radius: 10px;
        }

        /* Card Body */
        .department-card .card-body {
            padding: 0;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        /* Description */
        .department-description {
            font-size: 0.85rem;
            color: var(--sidenavbar-text-color);
            margin-bottom: 12px;
            padding: 8px;
            border-radius: 8px;
            background-color: var(--sidenavbar-body-color);
            flex-grow: 1;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            text-overflow: ellipsis;
        }

        /* Stats Section */
        .department-stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .stat-item {
            text-align: center;
            flex: 1;
        }

        .stat-label {
            font-size: 0.75rem;
            color: var(--sidenavbar-text-color);
            margin-bottom: 2px;
        }

        .stat-value {
            font-size: 1.2rem;
            font-weight: bold;
            color: #0d6efd;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            justify-content: space-around;
            padding-top: 8px;
            border-top: 1px solid rgba(0,0,0,0.05);
        }

        .action-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--sidenavbar-body-color);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .action-btn svg {
            width: 16px;
            height: 16px;
        }

        .btn-view {
            color: #0d6efd;
        }

        .btn-edit {
            color: #fd7e14;
        }

        .btn-delete {
            color: #dc3545;
            border: none !important;
        }

        /* Empty State */
        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px;
            border-radius: 12px;
        }

        .empty-state .empty-state-icon {
            font-size: 3rem;
            color: var(--sidenavbar-text-color);
            margin-bottom: 10px;
        }

        .empty-state .empty-state-text {
            color: var(--sidenavbar-text-color);
            margin-bottom: 10px;
        }

        .empty-state p{
            color: var(--sidenavbar-text-color);
        }

        /* Animations */
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
        .department-card {
            animation: fadeInUp 0.5s ease forwards;
            opacity: 0;
        }

        .department-card:nth-child(1) { animation-delay: 0.1s; }
        .department-card:nth-child(2) { animation-delay: 0.2s; }
        .department-card:nth-child(3) { animation-delay: 0.3s; }
        .department-card:nth-child(4) { animation-delay: 0.4s; }
        .department-card:nth-child(5) { animation-delay: 0.5s; }
        .department-card:nth-child(6) { animation-delay: 0.6s; }
        .department-card:nth-child(7) { animation-delay: 0.7s; }
        .department-card:nth-child(8) { animation-delay: 0.8s; }

        /* Modal Windows */
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
            ['url' => '', 'label' => 'Departments']
        ]"
    />

    <!-- Side Navbar -->
    <x-Owner.side-navbar :openSections="['Departments']" />
    <x-error-success-model />


    <div id="main">

        <section class="content my-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="mb-1">Departments</h3>
                            <a href="#" class="btn btn-primary d-flex align-items-center Owner-Department-Add-Button" id="Owner-Department-Add-Button"   data-bs-toggle="modal" data-bs-target="#createDepartmentModal" title="Add Level">
                                <x-icon name="add" type="svg" class="me-1" size="18" /> Department
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
                        </form>

                        <div class="card shadow p-3 mb-5 bg-body rounded" style="border: none; background-color: var(--sidenavbar-body-color) !important;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-4 tools-container">
                                    <div class="grid-view-toggle me-3">
                                        <span class="me-2">View:</span>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-secondary active" id="gridViewBtn">
                                                <i class='bx bx-grid-alt'></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" id="tableViewBtn">
                                                <i class='bx bx-table'></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            <x-icon name="export" type="icon" class="me-1" size="16" />
                                            Export
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                                            <li><button class="dropdown-item" type="button" id="copyButton"><i class='bx bx-copy me-2'></i>Copy</button></li>
                                            <li><button class="dropdown-item" type="button" id="csvButton"><i class='bx bx-file me-2'></i>CSV</button></li>
                                            <li><button class="dropdown-item" type="button" id="excelButton"><i class='bx bx-spreadsheet me-2'></i>Excel</button></li>
                                            <li><button class="dropdown-item" type="button" id="pdfButton"><i class='bx bxs-file-pdf me-2'></i>PDF</button></li>
                                            <li><button class="dropdown-item" type="button" id="printButton"><i class='bx bx-printer me-2'></i>Print</button></li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Card View -->
                                <div id="cardView">
                                    <div class="departments-grid">
                                        @forelse($departments ?? [] as $department)
                                            <div class="card department-card">
                                                <!-- Header -->
                                                <div class="card-header">
                                                    <span>{{ $department->name }}</span>
                                                    <span class="department-id">ID: {{ $department->id }}</span>
                                                </div>

                                                <!-- Body -->
                                                <div class="card-body">
                                                    <p class="department-description">
                                                        {{ $department->description ?? 'No description provided' }}
                                                    </p>

                                                    <!-- Stats Section -->
{{--                                                    <div class="department-stats">--}}
{{--                                                        <div class="stat-item">--}}
{{--                                                            <div class="stat-label">Total Staff</div>--}}
{{--                                                            <div class="stat-value">{{ $department->staff_count ?? 0 }}</div>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}

                                                    <!-- Action Buttons -->
                                                    <div class="action-buttons">
                                                        <a href="{{ route('owner.departments.show', ['department' => $department->id]) }}"
                                                           class="action-btn btn-view"
                                                           data-bs-toggle="tooltip"
                                                           data-bs-placement="top"
                                                           title="View">
                                                            <x-icon name="view" type="icon" size="16px" />
                                                        </a>
                                                        <a href="#"
                                                           class="action-btn btn-edit Owner-Department-Edit-Button"
                                                           data-id="{{ $department->id }}"
                                                           data-bs-toggle="tooltip"
                                                           data-bs-placement="top"
                                                           title="Edit">
                                                            <x-icon name="edit" type="icon" size="16px" />
                                                        </a>
                                                        <form action="{{ route('owner.departments.destroy', $department->id) }}"
                                                              method="POST"
                                                              class="d-inline"
                                                              id="delete-department-form-{{ $department->id }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <input type="hidden" name="id" value="{{ $department->id }}">
                                                            <button type="button"
                                                                    class="action-btn btn-delete delete-department-btn"
                                                                    title="Delete Department"
                                                                    data-id="{{ $department->id }}">
                                                                <x-icon name="delete" type="icon" size="16px" />
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                        @empty
                                            <div class="empty-state">
                                                <div class="empty-state-icon">
                                                    <i class='bx bx-sitemap'></i>
                                                </div>
                                                <h4 class="empty-state-text">No departments found</h4>
                                                <p>There are currently no departments matching your search criteria. Try different search keyword or add a new department.</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- Table View (Hidden by default) -->
                                <div id="tableView" style="display: none; margin-top: 0!important; padding-top: 0 !important;">
                                    <div class="table-responsive">
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
                                                            <a href="{{ route('owner.departments.show', ['department' => $department->id]) }}" class="text-info" data-bs-toggle="tooltip" data-bs-placement="top" title="View">
                                                                <x-icon name="view" type="icon" class="" size="20px" />
                                                            </a>
                                                            <a href="#" class="text-warning Owner-Department-Edit-Button" data-id="{{ $department->id }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                                <x-icon name="edit" type="icon" class="" size="20px" />
                                                            </a>
                                                            <form action="{{ route('owner.departments.destroy', $department->id) }}" method="POST" class="d-inline" id="delete-department-form-{{ $department->id }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <input type="hidden" name="id" value="{{ $department->id }}">
                                                                <button type="button" class="text-danger bg-transparent border-0 p-0 delete-department-btn" title="Delete Department" data-id="{{ $department->id }}">
                                                                    <x-icon name="delete" type="icon" class="" size="20px" />
                                                                </button>
                                                            </form>
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
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" maxlength="50" placeholder="Department Name" required>
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
                                    <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror" maxlength="250" placeholder="Department Description">{{ old('description') }}</textarea>
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
    <div class="modal fade" id="editDepartmentModel" tabindex="-1" aria-labelledby="editDepartmentModelLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDepartmentModelLabel">Edit Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editDepartmentForm" action="{{ route('owner.departments.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id" id="edit_department_id">
                    <input type="hidden" name="updated_at" id="edit_updated_at">

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="name">Name</label>
                                    <span class="required__field">*</span><br>
                                    <input type="text" name="edit_name" id="edit_department_name" class="form-control @error('edit_name') is-invalid @enderror" value="{{ old('edit_name') }}" maxlength="50" placeholder="Department Name" required>
                                    @error('edit_name')
                                    <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-2">
                                    <label for="edit_department_description">Description</label>
                                    <textarea name="edit_description" id="edit_department_description" rows="3" class="form-control @error('edit_description') is-invalid @enderror" maxlength="250" placeholder="Department Description">{{ old('edit_description') }}</textarea>
                                    @error('edit_description')
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

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Initialize DataTable for table view
            var table = new DataTable("#LevelsTable", {
                searching: false,
                paging: false,
                info: false,
                dom: "Bfrtip",
                buttons: [
                    {
                        extend: "csv",
                        text: "CSV",
                        className: "btn btn-secondary d-none"
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        className: "btn btn-secondary d-none"
                    },
                    {
                        extend: "pdf",
                        text: "PDF",
                        className: "btn btn-secondary d-none"
                    },
                    {
                        extend: "print",
                        text: "Print",
                        className: "btn btn-secondary d-none"
                    }
                ]
            });

            // View toggle functionality
            const gridViewBtn = document.getElementById('gridViewBtn');
            const tableViewBtn = document.getElementById('tableViewBtn');
            const cardView = document.getElementById('cardView');
            const tableView = document.getElementById('tableView');

            gridViewBtn.addEventListener('click', function() {
                this.classList.add('active');
                tableViewBtn.classList.remove('active');
                cardView.style.display = 'block';
                tableView.style.display = 'none';
            });

            tableViewBtn.addEventListener('click', function() {
                this.classList.add('active');
                gridViewBtn.classList.remove('active');
                cardView.style.display = 'none';
                tableView.style.display = 'block';
            });

            function triggerButton(buttonClass, logMessage) {
                console.log(logMessage);
                table.buttons(buttonClass).trigger();
            }

            document.getElementById("csvButton")?.addEventListener("click", function () {
                triggerButton(".buttons-csv", "CSV Button clicked");
            });

            document.getElementById("excelButton")?.addEventListener("click", function () {
                triggerButton(".buttons-excel", "Excel Button clicked");
            });

            document.getElementById("pdfButton")?.addEventListener("click", function () {
                triggerButton(".buttons-pdf", "PDF Button clicked");
            });

            document.getElementById("printButton")?.addEventListener("click", function () {
                triggerButton(".buttons-print", "Print Button clicked");
            });

            document.getElementById("copyButton")?.addEventListener("click", function () {
                triggerButton(".buttons-copy", "Copy Button clicked");
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editButtons = document.querySelectorAll(".Owner-Department-Edit-Button");

            editButtons.forEach(button => {
                button.addEventListener("click", function (e) {
                    e.preventDefault();
                    const id = this.getAttribute("data-id");

                    fetch(`{{ route('owner.departments.edit', ':id') }}`.replace(':id', id), {
                        method: "GET",
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.error) {
                                alert(data.error);
                            } else {
                                document.getElementById("edit_department_id").value = data.department?.id || "";
                                document.getElementById("edit_department_name").value = data.department?.name || "";
                                document.getElementById("edit_department_description").value = data.department?.description || "";
                                document.getElementById("edit_updated_at").value = data.department?.updated_at || "";

                                const editForm = document.getElementById("editDepartmentForm");
                                editForm.setAttribute("action", `{{ route('owner.departments.update') }}`);

                                // Ensure the hidden department_id field exists
                                let departmentIdInput = editForm.querySelector('input[name="department_id"]');
                                if (!departmentIdInput) {
                                    departmentIdInput = document.createElement("input");
                                    departmentIdInput.type = "hidden";
                                    departmentIdInput.name = "department_id";
                                    editForm.appendChild(departmentIdInput);
                                }
                                departmentIdInput.value = id;

                                // Show the modal
                                let editModal = new bootstrap.Modal(document.getElementById("editDepartmentModel"));
                                editModal.show();
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert("An error occurred while retrieving the data.");
                        });
                });
            });
        });
    </script>


    <script>
        //<!-- Handle all delete buttons -->
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete-department-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const departmentId = this.getAttribute('data-id');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Submit the corresponding form
                            document.getElementById(`delete-department-form-${departmentId}`).submit();
                        }
                    });
                });
            });
        });

        //Handle card size
        // document.addEventListener('DOMContentLoaded', function() {
        //     // Function to equalize card description heights
        //     function equalizeCardHeights() {
        //         const cards = document.querySelectorAll('.department-card');
        //         let maxHeight = 0;
        //
        //         // Reset all heights first to get natural heights
        //         cards.forEach(card => {
        //             const desc = card.querySelector('.department-description');
        //             desc.style.height = 'auto';
        //         });
        //
        //         // Find the maximum height
        //         cards.forEach(card => {
        //             const desc = card.querySelector('.department-description');
        //             maxHeight = Math.max(maxHeight, desc.offsetHeight);
        //         });
        //
        //         // Apply the maximum height to all
        //         cards.forEach(card => {
        //             const desc = card.querySelector('.department-description');
        //             desc.style.height = `${maxHeight}px`;
        //         });
        //     }
        //
        //     // Run initially
        //     equalizeCardHeights();
        //
        //     // Run after search/filter operations
        //     const searchInput = document.getElementById('search');
        //     if (searchInput) {
        //         searchInput.addEventListener('input', function() {
        //             // Use setTimeout to allow DOM to update first
        //             setTimeout(equalizeCardHeights, 100);
        //         });
        //     }
        //
        //     // Run when switching between grid/table view
        //     const gridViewBtn = document.getElementById('gridViewBtn');
        //     const tableViewBtn = document.getElementById('tableViewBtn');
        //
        //     if (gridViewBtn && tableViewBtn) {
        //         gridViewBtn.addEventListener('click', function() {
        //             setTimeout(equalizeCardHeights, 100);
        //         });
        //     }
        //
        //     // Optional: Use MutationObserver to detect DOM changes
        //     const observer = new MutationObserver(function(mutations) {
        //         mutations.forEach(function(mutation) {
        //             if (mutation.addedNodes.length || mutation.removedNodes.length) {
        //                 equalizeCardHeights();
        //             }
        //         });
        //     });
        //
        //     const gridContainer = document.querySelector('.departments-grid');
        //     if (gridContainer) {
        //         observer.observe(gridContainer, {
        //             childList: true,
        //             subtree: true
        //         });
        //     }
        // });

        //Hnadle search
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Get all necessary elements
            const searchInput = document.getElementById('search');
            if (!searchInput) {
                console.error('Search input not found!');
                return;
            }

            const departmentsGrid = document.querySelector('.departments-grid');
            const tableView = document.getElementById('tableView');

            if (!departmentsGrid || !tableView) {
                console.error('Required containers not found!');
                return;
            }

            // 2. Store original elements
            const originalCards = Array.from(departmentsGrid.querySelectorAll('.department-card'));
            const originalRows = Array.from(tableView.querySelectorAll('tbody tr'));

            // 3. Debounce function to prevent rapid firing
            function debounce(func, wait) {
                let timeout;
                return function() {
                    const context = this, args = arguments;
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        func.apply(context, args);
                    }, wait);
                };
            }

            // 4. Search function
            const performSearch = debounce(function(searchTerm) {
                searchTerm = searchTerm.toLowerCase().trim();

                let hasCardResults = false;
                let hasTableResults = false;

                // Search cards
                originalCards.forEach(card => {
                    const cardText = [
                        card.querySelector('.card-header span:first-child')?.textContent || '',
                        card.querySelector('.department-description')?.textContent || '',
                        card.querySelector('.department-id')?.textContent || ''
                    ].join(' ').toLowerCase();

                    const isVisible = cardText.includes(searchTerm);
                    card.style.display = isVisible ? 'block' : 'none';
                    if (isVisible) hasCardResults = true;
                });

                // Search table rows
                originalRows.forEach(row => {
                    const rowText = Array.from(row.querySelectorAll('td'))
                        .map(td => td.textContent || '')
                        .join(' ')
                        .toLowerCase();

                    const isVisible = rowText.includes(searchTerm);
                    row.style.display = isVisible ? '' : 'none';
                    if (isVisible) hasTableResults = true;
                });

                // Handle empty states
                handleEmptyStates(!hasCardResults, !hasTableResults, searchTerm);
            }, 300);

            // 5. Empty state handler
            function handleEmptyStates(showCardEmpty, showTableEmpty, searchTerm) {
                // Card view empty state
                const cardEmptyState = departmentsGrid.querySelector('.empty-state');
                if (showCardEmpty) {
                    if (!cardEmptyState) {
                        departmentsGrid.insertAdjacentHTML('beforeend', `
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class='bx bx-sitemap'></i>
                        </div>
                        <h4 class="empty-state-text">No departments found</h4>
                        <p>No departments match your search for "${searchTerm}".</p>
                    </div>
                `);
                    }
                } else if (cardEmptyState) {
                    cardEmptyState.remove();
                }

                // Table view empty state
                const tableEmptyRow = tableView.querySelector('tr.empty-row');
                if (showTableEmpty) {
                    if (!tableEmptyRow) {
                        const tbody = tableView.querySelector('tbody');
                        tbody.insertAdjacentHTML('beforeend', `
                    <tr class="empty-row">
                        <td colspan="4" class="text-center">
                            No departments match your search for "${searchTerm}"
                        </td>
                    </tr>
                `);
                    }
                } else if (tableEmptyRow) {
                    tableEmptyRow.remove();
                }
            }

            // 6. Event listeners
            searchInput.addEventListener('input', function() {
                performSearch(this.value);
            });

            // Initial check in case there's a search term on page load
            if (searchInput.value) {
                performSearch(searchInput.value);
            }
        });
    </script>

@endpush
