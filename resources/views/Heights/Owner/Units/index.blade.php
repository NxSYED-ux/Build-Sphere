@extends('layouts.app')

@section('title', 'Units')

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

        th, td {
            white-space: nowrap;
        }

        .dataTables_wrapper {
            width: 100%;
            overflow-x: auto;
        }

        /* ================ Unit CARDS ================ */

        .unit-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        /* Card Styles */
        .unit-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: none;
            margin-bottom: 0;
            display: flex;
            flex-direction: column;
            height: 100%;
            background: var(--body-background-color);
        }

        .unit-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        /* Card Image */
        .unit-card .card-img-container {
            height: 180px;
            overflow: hidden;
            position: relative;
            background-color: #f8f9fa;
        }

        .unit-card .card-img-top {
            object-fit: cover;
            width: 100%;
            height: 100%;
            transition: transform 0.5s ease;
        }

        .unit-card:hover .card-img-top {
            transform: scale(1.05);
        }

        .unit-type-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .unit-sale-rent-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .unit-price-tag {
            position: absolute;
            bottom: 5px;
            left: 5px;
            background: rgba(0, 0, 0, 0.4);
            color: white;
            padding: 5px 15px;
            /*border-radius: 20px;*/
            font-size: 14px;
            font-weight: 600;
        }
        .unit-availability-tag {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: rgba(0, 0, 0, 0.4);
            color: white;
            padding: 5px 15px;
            /*border-radius: 20px;*/
            font-size: 14px;
            font-weight: 600;
        }


        /* Card Body */
        .unit-card .card-body {
            padding: 1.25rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            background-color: var(--body-background-color);
        }

        .unit-card .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--sidenavbar-text-color);
            word-break: break-word;
        }

        .unit-card .card-text {
            color: var(--sidenavbar-text-color);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            word-break: break-word;
        }

        /* Status Badges */
        .unit-card .badge-status {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
            border-radius: 50px;
            font-weight: 600;
            white-space: nowrap;
        }

        .unit-card .badge-under-review {
            background-color: #fff3cd;
            color: #856404 !important;
        }

        .unit-card .badge-active {
            background-color: #d4edda;
            color: #155724 !important;
        }

        .unit-card .badge-inactive {
            background-color: #f8d7da;
            color: #721c24 !important;
        }

        /* Action Buttons */
        .unit-card .action-buttons {
            display: flex;
            margin-top: auto;
            flex-wrap: wrap;
            gap: 8px;
        }

        .unit-card .action-btn {
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
            color: #27ae60;
            border: 1px solid rgba(46, 204, 113, 0.2);
        }

        .btn-edit:hover {
            background-color: rgba(46, 204, 113, 0.2);
            color: #27ae60;
        }

        /* Levels Button */
        .unit-card .levels-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            color: #7f8c8d;
            transition: all 0.2s ease;
            z-index: 1;
        }

        .unit-card .levels-btn:hover {
            background: white;
            color: #3498db;
            transform: scale(1.1);
        }

        /* ================ EMPTY STATE ================ */
        .empty-state {
            text-align: center;
            padding: 40px;
            border-radius: 12px;
            grid-column: 1 / -1;
        }

        .empty-state-icon {
            font-size: 3rem;
            color: var(--sidenavbar-text-color);
            margin-bottom: 15px;
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

        /* ================ RESPONSIVE ADJUSTMENTS ================ */
        @media (max-width: 1399.98px) {
            .buildings-grid {
                grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            }
        }

        @media (max-width: 1199.98px) {
            .buildings-grid {
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            }

            .unit-card .card-img-container {
                height: 170px;
            }
        }

        @media (max-width: 991.98px) {
            .buildings-grid {
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            }

            .filter-group {
                min-width: 180px;
            }
        }

        @media (max-width: 767.98px) {
            .buildings-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }

            .unit-card .card-img-container {
                height: 160px;
            }

            .filter-container {
                flex-direction: column;
            }

            .filter-buttons {
                margin-left: 0;
                width: 100%;
            }

            .filter-buttons .btn {
                flex: 1;
            }
        }

        @media (max-width: 575.98px) {
            .buildings-grid {
                grid-template-columns: 1fr;
            }

            .unit-card .card-img-container {
                height: 200px;
            }

            .filter-group {
                min-width: 100%;
            }

            .unit-card .action-btn {
                flex: 1 1 100%;
            }
        }

        @media (max-width: 400px) {
            .unit-card .card-text {
                font-size: 0.85rem;
            }

            .unit-card .card-title {
                font-size: 1rem;
            }

            .unit-card .badge-status {
                font-size: 0.65rem;
            }

            .btn-add {
                min-width: 90px;
                font-size: 0.85rem !important;
                padding: 8px 5px !important;
            }
            .unit-card .action-btn {
                flex: 1 1 calc(50% - 4px);
            }
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
        .unit-card {
            animation: fadeInUp 0.5s ease forwards;
            opacity: 0;
        }

        .unit-card:nth-child(1) { animation-delay: 0.1s; }
        .unit-card:nth-child(2) { animation-delay: 0.2s; }
        .unit-card:nth-child(3) { animation-delay: 0.3s; }
        .unit-card:nth-child(4) { animation-delay: 0.4s; }
        .unit-card:nth-child(5) { animation-delay: 0.5s; }
        .unit-card:nth-child(6) { animation-delay: 0.6s; }
        .unit-card:nth-child(7) { animation-delay: 0.7s; }
        .unit-card:nth-child(8) { animation-delay: 0.8s; }
    </style>
@endpush

@section('content')

    <!-- Top Navbar -->
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('owner_manager_dashboard'), 'label' => 'Dashboard'],
            ['url' => '', 'label' => 'Units']
        ]"
    />
    <!--  -->
    <x-Owner.side-navbar :openSections="['Buildings', 'Units']"/>
    <x-error-success-model />

    <div id="main">

        <section class="content  my-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h3 class="mb-1">Units</h3>
                            <a href="{{ route('owner.units.create') }}" class="btn btn-primary d-flex align-items-center Owner-Unit-Add-Button hidden" data-bs-toggle="tooltip" data-bs-placement="top" title="Add Unit">
                                <x-icon name="add" type="svg" class="me-1" size="18" />
                                Add Unit
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
                                <label for="building_id">Building</label>
                                <select name="building_id" id="building_id" class="form-select filter-select">
                                    <option value="">All buildings</option>
                                    @forelse($buildings ?? [] as $building)
                                        <option value="{{ $building->id }}" {{ request('building_id') == $building->id ? 'selected' : '' }}>
                                            {{ $building->name }}
                                        </option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>

                            <div class="filter-group">
                                <label for="type">Status</label>
                                <select name="status" id="status" class="form-select filter-select">
                                    <option value="">All Status</option>
                                    @forelse($statuses ?? [] as $status)
                                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                            {{ $status }}
                                        </option>
                                    @empty
                                    @endforelse
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
                                    <div class="unit-grid">
                                        @forelse($units ?? [] as $unit)
                                            <div class="card unit-card">
                                                <div class="card-img-container">
                                                    @if(count($unit->pictures ?? []) > 0)
                                                        <img src="{{ asset($unit->pictures[0]->file_path) }}" class="card-img-top" alt="Building Image">
                                                    @else
                                                        <img src="{{ asset('img/placeholder-img.jfif') }}" class="card-img-top" alt="Building Image">
                                                    @endif
                                                    <div class="unit-type-badge">
                                                        {{ $unit->unit_type }}
                                                    </div>
                                                    <div class="unit-sale-rent-badge">
                                                        {{ $unit->sale_or_rent }}
                                                    </div>
                                                    <div class="unit-price-tag">
                                                        PKR {{ $unit->price ?? 'N/A' }}
                                                    </div>
                                                    <div class="unit-availability-tag">
                                                        {{ $unit->availability_status ?? 'N/A' }}
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <h5 class="card-title">{{ $unit->unit_name }}</h5>
                                                        <span class="badge badge-status
                                                            @if($unit->status === 'Approved') badge-active
                                                            @elseif($unit->status === 'Rejected') badge-inactive
                                                            @else badge-inactive
                                                            @endif">
                                                            {{ $unit->status }}
                                                        </span>
                                                    </div>
                                                    <p class="card-text"><i class='bx bx-buildings me-1'></i> {{ $unit->building->name ?? 'N/A' }}</p>
                                                    <p class="card-text"><i class='bx bxs-layer me-1'></i> {{ $unit->level->level_name ?? 'N/A' }}</p>


                                                    <div class="action-buttons">
                                                        <a href="{{ route('owner.units.show', $unit->id) }}" class="action-btn btn-add btn-view view-unit gap-1" title="View">
                                                            <i class='bx bx-show'></i> View
                                                        </a>

                                                        <a href="{{ route('owner.units.edit', $unit->id) }}" class="action-btn btn-add btn-edit gap-1 Owner-Unit-Edit-Button hidden" title="Edit">
                                                            <i class='bx bx-edit'></i> Edit
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="empty-state">
                                                <div class="empty-state-icon">
                                                    <i class='bx bx-building-house'></i>
                                                </div>
                                                <h4>No Units Found</h4>
                                                <p class="">There are no units to display. You can add a new unit by clicking the "Add Unit" button.</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- Table View (Hidden by default) -->
                                <div id="tableView" style="display: none; margin-top: 0!important; padding-top: 0 !important;">
                                    <div class="table-responsive">
                                        <table id="unitsTable" class="table shadow-sm table-hover table-striped">
                                            <thead class="shadow">
                                            <tr>
                                                <th>ID</th>
                                                <th>Picture</th>
                                                <th>Name</th>
                                                <th>Type</th>
                                                <th>Price</th>
                                                <th>Status</th>
                                                <th>Sale or Rent</th>
                                                <th>Availability Status</th>
                                                <th>Building</th>
                                                <th>Level</th>
                                                <th class="w-170 text-center">Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse ($units ?? [] as $unit)
                                                <tr>
                                                    <td>{{ $unit->id }}</td>
                                                    <td>
                                                        <div id="unitCarousel{{ $unit->id }}" class="carousel slide" data-bs-ride="carousel">
                                                            <div class="carousel-inner">
                                                                @forelse($unit->pictures ?? [] as $key => $picture)
                                                                    <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                                                        <img src="{{ asset($picture->file_path) }}" class="d-block" alt="Unit Picture" style="border-radius: 5px; width:100px; height:50px;">
                                                                    </div>
                                                                @empty
                                                                    <img src="{{ asset('img/placeholder-img.jfif') }}" class="d-block" alt="Unit Picture" style="border-radius: 5px; width:100px; height:50px;">
                                                                @endforelse
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $unit->unit_name }}</td>
                                                    <td>
                                                        {{ $unit->unit_type }}
                                                    </td>
                                                    <td>{{ $unit->price ?? 'N/A' }}</td>
                                                    <td>{{ $unit->status ?? 'N/A' }}</td>
                                                    <td>{{ $unit->sale_or_rent ?? 'N/A' }}</td>
                                                    <td>{{ $unit->availability_status ?? 'N/A' }}</td>
                                                    <td>{{ $unit->level->building->name ?? 'N/A' }}</td>
                                                    <td>{{ $unit->level->level_name ?? 'N/A' }}</td>
                                                    <td class="text-center ">
                                                        <div class="d-flex justify-content-center align-items-center gap-3">
                                                            <a href="{{ route('owner.units.show', $unit->id) }}" class="text-info view-unit" data-bs-toggle="tooltip" data-bs-placement="top" title="View">
                                                                <x-icon name="view" type="icon" class="" size="20px" />
                                                            </a>
                                                            <a href="{{ route('owner.units.edit', $unit->id) }}" class="text-warning Owner-Unit-Edit-Button hidden" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                                <x-icon name="edit" type="icon" class="" size="20px" />
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="12" class="text-center">No units found.</td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                @if ($units && $units->count() > 0)
                                    <div class="mt-3">
                                        {{ $units->appends(request()->query())->links('pagination::bootstrap-5') }}
                                    </div>
                                @endif
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </section>
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
            var table = new DataTable("#unitsTable", {
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
        function resetFilters() {
            window.location.href = '{{ route("owner.units.index") }}';
        }
    </script>


@endpush
