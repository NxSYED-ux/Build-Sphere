@extends('layouts.app')

@section('title', 'Buildings')

@push('styles')
    <style>
        body {
        }
        #main {
            margin-top: 45px;
        }
        #Owner-Building-Add-Button {
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

        .Owner-Building-Edit-Button{
        }

        th, td {
            white-space: nowrap;
        }

        .dataTables_wrapper {
            width: 100%;
            overflow-x: auto;
        }
    </style>
@endpush

@section('content')

    <!-- Top Navbar -->
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('owner_manager_dashboard'), 'label' => 'Dashboard'],
            ['url' => '', 'label' => 'Buildings']
        ]"
    />
    <!--  -->
    <x-Owner.side-navbar :openSections="['Buildings', 'Building']"/>
    <x-error-success-model />

    <div id="main">

        <section class="content my-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box ">
                            <div class="container mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h3 class="mb-1">Buildings</h3>
                                    <a href="{{ route('owner.buildings.create') }}" class="btn float-end hidden" id="Owner-Building-Add-Button"  data-bs-toggle="tooltip" data-bs-placement="top" title="Add Building"><i class="fa fa-plus"></i></a>
                                </div>
                                <div class="card shadow p-3 pt-1 mb-5 bg-body rounded" style="border: none;">
                                    <div class="card-body " style="overflow-x: auto;">

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

                                            <form method="GET" action="{{ route('owner.buildings.index') }}" class="d-flex" style="margin-left: 6px;">
                                                <input type="text"  name="search" class="form-control me-2" placeholder="Search buildings..." value="{{ request('search') }}">
                                                <button type="submit" class="btn btn-primary d-flex justify-content-center align-items-center" style="height: 40px; width: 40px;">
                                                    <i class='bx bx-search' style="font-size: 20px;"></i>
                                                </button>

                                            </form>
                                        </div>

                                        <table id="buildingsTable" class="table shadow-sm table-hover table-striped">
                                            <thead class="shadow">
                                            <tr>
                                                <th>ID</th>
                                                <th>Picture</th>
                                                <th>Name</th>
                                                <th>Remarks</th>
                                                <th>Area</th>
                                                <th>City</th>
                                                <th>Status</th>
                                                <th class="w-170 text-center">Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($buildings ?? [] as $building)
                                                <tr>
                                                    <td>{{ $building->id }}</td>
                                                    <td>
                                                        <div id="unitCarousel{{ $building->id }}" class="carousel slide" data-bs-ride="carousel">
                                                            <div class="carousel-inner">
                                                                @forelse($building->pictures ?? [] as $key => $picture)
                                                                    <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                                                        <img src="{{ asset($picture->file_path) }}" class="d-block" alt="Building Picture" style="border-radius: 5px; width:100px; height:50px;">
                                                                    </div>
                                                                @empty
                                                                    <img src="{{ asset('img/placeholder-img.jfif') }}" class="d-block" alt="Building Picture" style="border-radius: 5px; width:100px; height:50px;">
                                                                @endforelse
                                                            </div>
                                                        </div>
                                                     </td>
                                                    <td>{{ $building->name }}</td>
                                                    <td>{{ $building->remarks ?? 'N/A' }}</td>
                                                    <td>{{ $building->area ?? 'N/A' }}</td>
                                                    <td>{{ $building->address->city ?? 'N/A' }}</td>
                                                    <td>
                                                        {{ $building->status ?? 'N/A' }}
                                                    </td>
                                                    <td class="w-170 text-center">
                                                        @if($building->status === "Under Processing" || $building->status === "Reapproved")
                                                            <a href="{{ route('owner.buildings.show', ['building' => $building->id]) }}" class="text-info" title="Submit"><i class='bx bxs-right-top-arrow-circle' style="font-size: 20px;margin-right:5px; color: orange;"></i></a>
                                                        @else
                                                        <a href="{{ route('owner.buildings.show', ['building' => $building->id]) }}" class="text-info" title="View"><i class="fa fa-eye mx-2" style="font-size: 20px;margin-right:5px;;"></i></a>
                                                        @endif
                                                        <a href="{{ route('owner.levels.index', ['building_id' => $building->id]) }}" class="text-" title="View Levels"><i class="bx bxs-city icons" style="font-size: 20px;margin-right:5px; color: grey;"></i></a>
                                                        <a href="{{ route('owner.buildings.edit', $building->id) }}" class="text-warning Owner-Building-Edit-Button hidden" title="Edit">
                                                            <i class="fa fa-pencil mx-2" style="font-size: 20px;"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="8" class="text-center">No buildings found.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>

                                        @if ($buildings)
                                            <div class="mt-3">
                                                {{ $buildings->links('pagination::bootstrap-5') }}
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
            var table = new DataTable("#buildingsTable", {
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

@endpush

