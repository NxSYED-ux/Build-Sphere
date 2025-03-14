@extends('layouts.app')

@section('title', 'Buildings')

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
    </style>
@endpush

@section('content')

    <!-- Top Navbar -->
    <x-Owner.top-navbar :searchVisible="false"/>
    <!--  -->
    <x-Owner.side-navbar :openSections="['Buildings', 'Building']"/>

    <div id="main">

        <section class="content my-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box ">
                            <div class="container mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h3 class="mb-1">Buildings</h3>
                                    <a href="{{ route('buildings.create') }}" class="btn float-end" id="add_button"  data-bs-toggle="tooltip" data-bs-placement="top" title="Add Building"><i class="fa fa-plus"></i></a>
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

                                            <form method="GET" action="{{ route('buildings.index') }}" class="d-flex" style="margin-left: 6px;">
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
                                                <th>Organization</th>
                                                <th>City</th>
                                                <th>Status</th>
                                                <th class="w-170 text-center">Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($buildings as $building)
                                                <tr>
                                                    <td>{{ $building->id }}</td>
                                                    <td>
                                                        <img src="{{ $building->pictures->isNotEmpty() ? asset($building->pictures->first()->file_path) : asset('https://via.placeholder.com/150') }}" alt="Building Picture" style="border-radius: 5px;" width="100" height="50">
                                                    </td>
                                                    <td>{{ $building->name }}</td>
                                                    <td>{{ $building->remarks ?? 'N/A' }}</td>
                                                    <td>{{ $building->area ?? 'N/A' }}</td>
                                                    <td>{{ $building->organization->name ?? 'N/A' }}</td>
                                                    <td>{{ $building->address->city ?? 'N/A' }}</td>
                                                    <td>
                                                        {{ $building->status ?? 'N/A' }}
                                                    </td>
                                                    <td class="w-170 text-center">
                                                        <a href="{{ route('buildings.show', ['building' => $building->id]) }}" class="text-info" title="View Levels"><i class="fa fa-eye mx-2" style="font-size: 20px;margin-right:5px;;"></i></a>
                                                        <a href="{{ route('levels.index', ['building_id' => $building->id]) }}" class="text-info" title="View Levels"><i class="bx bxs-city icons" style="font-size: 20px;margin-right:5px;;"></i></a>
                                                        <a href="{{ route('buildings.edit', $building->id) }}" class="text-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                            <i class="fa fa-pencil mx-2" style="font-size: 20px;"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>

                                        <div class="mt-3">
                                            {{ $buildings->links('pagination::bootstrap-5') }}
                                        </div>

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
        $(document).ready(function () {

            var table = $('#buildingsTable').DataTable({
                searching: false,
                paging: true,
                info: false,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'csv',
                        text: 'CSV',
                        className: 'btn btn-secondary d-none'
                    },
                    {
                        extend: 'excel',
                        text: 'Excel',
                        className: 'btn btn-secondary d-none'
                    },
                    {
                        extend: 'pdf',
                        text: 'PDF',
                        className: 'btn btn-secondary d-none'
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        className: 'btn btn-secondary d-none'
                    }
                ]
            });

            // CSV Button Click
            $('#csvButton').on('click', function () {
                console.log('CSV Button clicked');
                table.button('.buttons-csv').trigger();
            });

            // Excel Button Click
            $('#excelButton').on('click', function () {
                console.log('Excel Button clicked');
                table.button('.buttons-excel').trigger();
            });

            // PDF Button Click
            $('#pdfButton').on('click', function () {
                console.log('PDF Button clicked');
                table.button('.buttons-pdf').trigger();
            });

            // Print Button Click
            $('#printButton').on('click', function () {
                console.log('Print Button clicked');
                table.button('.buttons-print').trigger();
            });

            // Column Visibility Button Click
            $('#colvisButton').on('click', function () {
                console.log('Column Visibility Button clicked');
                table.button('.buttons-colvis').trigger();
            });
        });

    </script>

@endpush

