@extends('layouts.app')

@section('title', 'Units')

@push('styles')
    <style>
        body {
        }
        #main {
            margin-top: 45px;
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
    </style>
@endpush

@section('content')

    <!-- Top Navbar -->
    <x-Admin.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('admin_dashboard'), 'label' => 'Dashboard'],
            ['url' => '', 'label' => 'Units']
        ]"
    />

    <!-- Side Navbar -->
    <x-Admin.side-navbar :openSections="['Buildings', 'Units']" />
    <!--  -->
    <x-error-success-model />


    <div id="main">

        <section class="content  mt-1 mb-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box ">
                            <div class="container mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h3 class="mb-1">Units</h3>
                                    <a href="{{ route('units.create') }}" class="btn float-end hidden add_button" id="Admin-Unit-Add-Button"  data-bs-toggle="tooltip" data-bs-placement="top" title="Add Unit">
                                        <x-icon name="add" type="svg" class="" size="25" />
                                    </a>
                                </div>
                                <div class="card shadow p-3 pt-1 mb-5 bg-body rounded" style="border: none;">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">

                                            <div  class="d-flex align-items-center">
                                                <button class="btn btn-light" type="button" id="menu-icon" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <x-icon name="export" type="icon" class="" size="20px" />
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

                                            <form method="GET" action="{{ route('units.index') }}" class="d-flex" style="margin-left: 6px;">
                                                <input
                                                    type="text"
                                                    name="search"
                                                    class="form-control me-2"
                                                    placeholder="Search units..."
                                                    value="{{ request('search') }}"
                                                >
                                                <button type="submit" class="btn btn-primary">
                                                    <x-icon name="search" type="icon" class="" size="20px" />
                                                </button>
                                            </form>
                                        </div>

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
                                                        <td class="text-center ">
                                                            <div class="d-flex justify-content-center align-items-center gap-3">
                                                                <a href="javascript:void(0);" class="text-info view-unit" data-id="{{ $unit->id }}" data-bs-toggle="tooltip" data-bs-placement="top" title="View">
                                                                    <x-icon name="view" type="icon" class="" size="20px" />
                                                                </a>
                                                                <a href="{{ route('units.edit', $unit->id) }}" class="text-warning Admin-Unit-Edit-Button hidden" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
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


    <!-- User Details Modal -->
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

                    fetch(`{{ route('units.show', ':id') }}`.replace(':id', userId), {
                        method: "GET",
                        headers: {
                            "Accept": "application/json"
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            let unit = data.Unit;

                            document.getElementById("unitPicture").src = unit.pictures.length > 0 ? '/' + unit.pictures[0].file_path : 'default-image.jpg';
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
