@extends('layouts.app')

@section('title', 'Units')

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

        th, td {
            white-space: nowrap;
        }

        .dataTables_wrapper {
            width: 100%;
            overflow-x: auto;
        }



        /*  Unit Model */
        .modal-dialog {
            max-width: 400px;
            border-radius: 20px !important;
            overflow: hidden;
        }

        .modal-content {
            max-width: 400px;
            border-radius: 20px !important;
            overflow: hidden; /* Ensures the content respects the border radius */
            box-shadow: none !important; /* Remove Bootstrap shadow */
            border: 2px solid var(--modal-border); /* Ensures corners have a visible border */
        }

        .unit-modal-content {
            border-radius: 20px !important;
            overflow: hidden; /* Important for applying radius properly */
        }

        .unit-modal-dialog {
            border-radius: 20px !important;
        }

        #unitModal h5{
            font-size: 15px;
            font-weight: 600;
            color: var(--modal-text);
            font-family: 'Montserrat', sans-serif;
        }

        #unitModal span{
            font-size: 15px;
            color: var(--modal-text);
            font-family: 'Montserrat', sans-serif;
        }

        .unit-modal-header {
            background: var(--modal-bg) !important;
            /*background: var(--modal-header-bg);*/
            color: var(--modal-text) !important;
            font-family: 'Montserrat', sans-serif !important;
        }

        #unitModalLabel{
            font-size: 18px !important;
            font-weight: bold !important;
        }

        .unit-modal-body {
            background: var(--modal-bg) !important;
            color: var(--modal-text) !important;
            font-family: 'Montserrat', sans-serif !important;
        }

        .unit-modal-footer {
            background: var(--modal-bg) !important;
            border-top: 1px solid var(--modal-border) !important;
        }

        .unit-modal-close-btn {
            background: white;
            color: var(--modal-btn-text);
            border: 2px solid var(--modal-btn-bg);
            border-radius: 10px;
        }

        .unit-modal-close-btn:hover {
            background: var(--modal-btn-bg);
            color: var(--modal-btn-text-hover);
            opacity: 0.8;
        }

        .unit-close-btn {
            filter: invert(var(--invert, 0));
        }

        .unit-img-border {
            border: 2px solid var(--modal-border);
        }

        .nav-tabs .nav-item {
            flex: 1;
            text-align: center;
            max-width: 20%;
        }

        .nav-tabs .nav-link {
            background-color: var(--nav-tabs-inactive-bg-color) !important; /* Change to your desired color */
            color: var(--nav-tabs-inactive-text-color) !important;
            border-bottom: 1px solid var(--nav-tabs-inactive-border-color) !important; /* Corrected */
        }
        .nav-tabs .nav-link.active {
            background-color: var(--nav-tabs-active-bg-color) !important; /* Change to your desired color */
            color: var(--nav-tabs-active-text-color) !important;
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

    <!-- Top Navbar -->
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('owner_manager_dashboard'), 'label' => 'Dashboard'],
            ['url' => '', 'label' => 'Units']
        ]"
    />
    <!--  -->
    <x-Owner.side-navbar :openSections="['Buildings', 'Units']"/>
    <x-error-success-model />

    @php
        $activeTab = 'Cards';
    @endphp

    <div id="main">

        <section class="content  mt-1 mb-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box ">
                            <div class="container mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h3 class="mb-1">Units</h3>
                                    <a href="{{ route('owner.units.create') }}" class="btn float-end" id="add_button"  data-bs-toggle="tooltip" data-bs-placement="top" title="Add Unit"><i class="fa fa-plus"></i></a>
                                </div>
                                <div class="card shadow p-3 pt-1 mb-5 bg-body rounded" style="border: none;">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">

                                            <div  class="d-flex align-items-center">
                                                <button class="btn btn-light" type="button" id="menu-icon" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <!-- <i class="bx bx-menu"></i> -->
                                                    <i class='bx bx-export' style="font-size: 20px;"></i>
                                                </button>

                                                <ul id="button-list" class="dropdown-menu dropdown-menu-end" >
                                                    <li><button class="dropdown-item" type="button" id="copyButton">Copy</button></li>
                                                    <li><button class="dropdown-item" type="button" id="csvButton">CSV</button></li>
                                                    <li><button class="dropdown-item" type="button" id="excelButton">Excel</button></li>
                                                    <li><button class="dropdown-item" type="button" id="pdfButton">PDF</button></li>
                                                    <li><button class="dropdown-item" type="button" id="printButton">Print</button></li>
                                                    <!-- <li><button class="dropdown-item" type="button" id="colvisButton">Column Visibility</button></li> -->
                                                </ul>
                                            </div>

                                            <form method="GET" action="{{ route('owner.units.index') }}" class="d-flex" style="margin-left: 6px;">
                                                <input
                                                    type="text"
                                                    name="search"
                                                    class="form-control me-2"
                                                    placeholder="Search units..."
                                                    value="{{ request('search') }}"
                                                >
                                                <button type="submit" class="btn btn-primary"><i class='bx bx-search' style="font-size: 20px;"></i></button>
                                            </form>
                                        </div>
                                        <ul class="nav nav-tabs mt-2" id="myTab" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link {{ $activeTab === 'Cards' ? 'active' : '' }}" id="dropdwon-cards-tab" data-bs-toggle="tab" href="#dropdwon-cards" role="tab" aria-controls="dropdwon-cards" aria-selected="{{ $activeTab === 'Cards' ? 'true' : 'false' }}">Cards</a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link {{ $activeTab === 'Table' ? 'active' : '' }}" id="dropdwon-table-tab" data-bs-toggle="tab" href="#dropdwon-table" role="tab" aria-controls="dropdwon-table" aria-selected="{{ $activeTab === 'Table' ? 'true' : 'false' }}">Table</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content mt-0 pt-0" id="myTabContent"   style="margin: 0px !important; padding: 0px !important;">
                                            <div class="tab-pane fade {{ $activeTab === 'Cards' ? 'show active' : '' }}" id="dropdwon-cards" role="tabpanel" aria-labelledby="dropdwon-cards-tab">
                                                <div class="container">
                                                    <div class="row text-center mt-2">
                                                        @forelse ($units ?? [] as $unit)
                                                            <div class="col-lg-4 mb-4">
                                                                <div class="card shadow-sm" style="border-radius: 10px;">
                                                                    <div class="position-relative">
                                                                        @if($unit->pictures->isNotEmpty())
                                                                            <div id="carousel{{ $unit->id }}" class="carousel slide" data-bs-ride="carousel">
                                                                                <div class="carousel-inner">
                                                                                    @foreach ($unit->pictures as $key => $picture)
                                                                                        <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                                                                            <img src="{{ asset($picture->file_path) }}" class="d-block w-100"
                                                                                                 alt="Unit Picture" style="border-radius: 10px 10px 0 0; height: 180px; object-fit: cover;">
                                                                                        </div>
                                                                                    @endforeach
                                                                                </div>
                                                                            </div>
                                                                        @else
                                                                            <img src="{{ asset('img/placeholder-img.jfif') }}" class="d-block w-100"
                                                                                 alt="Unit Picture" style="border-radius: 10px 10px 0 0; height: 180px; object-fit: cover;">
                                                                        @endif

                                                                        <!-- Sale Badge -->
                                                                        <span class="badge bg-warning position-absolute top-0 start-0 m-2 px-3 py-1 text-white">Sale</span>

                                                                        <!-- Favorite Icon -->
                                                                        <span class="position-absolute top-0 end-0 m-2">
                                                                            <i class="bi bi-heart text-white fs-4"></i>
                                                                        </span>
                                                                    </div>

                                                                    <div class="card-body text-start">
                                                                        <h5 class="fw-bold mb-1">{{ $unit->unit_name ?? 'Shop 03' }}</h5>

                                                                        <!-- Location -->
                                                                        <p class="text-muted mb-1">
                                                                            <i class="bi bi-geo-alt-fill text-success"></i> {{ $unit->building->address ? $unit->building->address->location .', ' . $unit->building->address->city .', ' .  $unit->building->address->province  .', ' .  $unit->building->address->country : ''  }}
                                                                        </p>

                                                                        <!-- Price -->
                                                                        <p class="fw-bold mb-1">
                                                                            Rs {{ number_format($unit->price ?? 135000) }} PKR
                                                                        </p>

                                                                        <!-- Link -->
                                                                        <a href="#" class="text-primary fw-bold text-decoration-none">{{ $unit->unit_type }}</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <h1>No Units Found.</h1>
                                                        @endforelse
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="tab-pane fade {{ $activeTab === 'Table' ? 'show active' : '' }}" id="dropdwon-table" role="tabpanel" aria-labelledby="dropdwon-table-tab"   style="margin: 0px !important; padding: 0px !important;">

                                                <table id="unitsTable" class="table shadow-sm table-hover table-striped" style="margin: 0px !important; padding: 0px !important;">
                                                <thead class="shadow"  style="margin: 0px !important; padding: 0px !important;">
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
                                                    <th>Organization</th>
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
                                                        <td>{{ $unit->organization->name ?? 'N/A' }}</td>
                                                        <td class="text-center">
                                                            <a href="javascript:void(0);" class="text-info view-unit" data-id="{{ $unit->id }}" data-bs-toggle="tooltip" data-bs-placement="top" title="View"><i class="fa fa-eye mx-2" style="font-size: 20px;"></i></a>
                                                            <a href="{{ route('owner.units.edit', $unit->id) }}" class="text-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                                <i class="fa fa-pencil mx-2" style="font-size: 20px;"></i>
                                                            </a>
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
                                        @if ($units)
                                            <div class="mt-3">
                                                {{ $units->links('pagination::bootstrap-5') }}
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


    <!-- Unit Details Modal -->
    <div class="modal fade" id="unitModal" tabindex="-1" aria-labelledby="unitModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content shadow-lg unit-modal-content">
                <!-- Header -->
                <div class="modal-header unit-modal-header position-relative">
                    <h5 class="modal-title fw-bold w-100 text-center" id="unitModalLabel">Unit Details</h5>
                </div>

                <!-- Body -->
                <div class="modal-body unit-modal-body">
                    <div class="d-flex flex-column align-items-center justify-content-center mb-3">
                        <div class="d-flex align-items-center">
                            <img id="unitPicture" src="" alt="Unit Picture" class="img-fluid rounded-circle shadow-sm border unit-img-border"
                                 style="width: 140px; height: 140px; object-fit: cover;">
                            <div class="ms-3" style="padding-left: 10px !important;">
                                <h5 id="unitName" class="mb-1"></h5>
                                <p class="mb-0"><strong>PKR </strong><span id="unitPrice"></span></p>

                            </div>
                        </div>
                    </div>


                    <div class="container">
                        <div class="row px-3">
                            <div class="col-7 mb-2">
                                <h5>Building</h5>
                                <span id="unitBuilding"></span>
                            </div>
                            <div class="col-5 mb-2">
                                <h5>Level</h5>
                                <span id="unitLevel"></span>
                            </div>
                            <div class="col-7 mb-2">
                                <h5>Organization</h5>
                                <span id="unitOrganization"></span>
                            </div>
                            <div class="col-5 mb-2">
                                <h5>Type</h5>
                                <span id="unitType"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer unit-modal-footer">
                    <button type="button" class="btn unit-modal-close-btn w-100" data-bs-dismiss="modal">Close</button>
                </div>
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

    <!-- Data Table script -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
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

            document.getElementById("colvisButton")?.addEventListener("click", function () {
                triggerButton(".buttons-colvis", "Column Visibility Button clicked");
            });
        });
    </script>


    <!-- Unit Detail Model script -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".view-unit").forEach(button => {
                button.addEventListener("click", function () {
                    let userId = this.dataset.id;

                    fetch(`{{ route('owner.units.details', ':id') }}`.replace(':id', userId), {
                        method: "GET",
                        headers: {
                            "Accept": "application/json"
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            let unit = data.Unit;

                            document.getElementById("userPicture").src = unit.picture ? unit.picture : "https://via.placeholder.com/150";
                            document.getElementById("unitName").textContent = unit.unit_name;
                            document.getElementById("unitBuilding").textContent = unit.building.name;
                            document.getElementById("unitLevel").textContent = unit.level.level_name;
                            document.getElementById("unitOrganization").textContent = unit.organization.name;
                            document.getElementById("unitType").textContent = unit.unit_type;
                            document.getElementById("unitPrice").textContent = unit.price;

                            let unitModal = new bootstrap.Modal(document.getElementById("unitModal"));
                            unitModal.show();
                        })
                        .catch(error => {
                            console.error("Error:", error);
                        });
                });
            });
        });
    </script>

@endpush
