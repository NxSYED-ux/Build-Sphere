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
    </style> 
@endpush

@section('content') 

    <!-- Top Navbar -->
    <x-Admin.top-navbar :searchVisible="false"/>

    <!-- Side Navbar -->
    <x-Admin.side-navbar :openSections="['Buildings', 'Units']" /> 
    <!--  -->
    <x-error-success-model />


    <div id="main">
        <section class="content-header pt-2">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mx-5">
                    <li class="breadcrumb-item"><a href="{{url('admin_dashboard')}}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="">Units</a></li>
                </ol>
            </nav> 
        </section>

        <section class="content content-top  my-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box ">
                            <div class="container mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h3 class="mb-1">Units</h3>
                                    <a href="{{ route('units.create') }}" class="btn float-end" id="add_button"  data-bs-toggle="tooltip" data-bs-placement="top" title="Add Unit"><i class="fa fa-plus"></i></a> 
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

                                            <form method="GET" action="{{ route('units.index') }}" class="d-flex" style="margin-left: 6px;">
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
                                                @forelse ($units as $unit)
                                                    <tr>
                                                        <td>{{ $unit->id }}</td>
                                                        <td>
                                                            @if ($unit->pictures && $unit->pictures->count() > 0)
                                                                <img src="{{ asset($unit->pictures->first()->file_path) }}" alt="Unit Picture" style="border-radius: 5px;" width="100" height="50">
                                                            @else
                                                                <img src="https://via.placeholder.com/150" alt="Placeholder Image" style="border-radius: 5px;" width="100" height="50">
                                                            @endif
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
                                                            <a href="{{ route('units.edit', $unit->id) }}" class="text-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
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

                                        <div class="mt-3">
                                            {{ $units->links('pagination::bootstrap-5') }}
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

            var table = $('#unitsTable').DataTable({ 
                searching: false,
                paging: false,
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

<!-- <script>
  $(document).ready(function () {
    var table = $('#unitsTable').DataTable({
        // Disable search and pagination
        searching: false,
        paging: false,
        info: false,

        // Export buttons configuration (excluding Copy, CSV, Excel, PDF, and Print)
        dom: 'Bfrtip',
        buttons: [
            'colvis', // Column visibility button remains in the DataTable toolbar
            // Add other buttons here like 'copy', 'csv', 'excel', etc.
        ],

        // Ensure buttons are displayed in the container
        initComplete: function () {
            // Make sure the buttons are initialized before interacting with them
            var tableButtons = table.buttons();
            if (tableButtons.length > 0) {
                tableButtons.container().appendTo('#datatable-buttons');
            } else {
                console.log('DataTables buttons were not initialized.');
            }
        }
    });

    // Initialize the Bootstrap dropdown behavior
    var dropdown = new bootstrap.Dropdown(document.getElementById('menu-icon'));

    // Toggle the dropdown list visibility when clicking the menu icon
    $('#menu-icon').on('click', function () {
        $('#button-list').toggle(); // Show/hide the dropdown menu
    });

    // Trigger DataTable button actions when clicking on the menu items
    $('#csvButton').on('click', function () {
        console.log('CSV Button clicked');
        table.button('.buttons-csv').trigger();
    });
    $('#excelButton').on('click', function () {
        console.log('Excel Button clicked');
        table.button('.buttons-excel').trigger();
    });
    $('#pdfButton').on('click', function () {
        console.log('PDF Button clicked');
        table.button('.buttons-pdf').trigger();
    });
    $('#printButton').on('click', function () {
        console.log('Print Button clicked');
        table.button('.buttons-print').trigger();
    });
    $('#colvisButton').on('click', function () {
        console.log('ColVis Button clicked');
        table.button('.buttons-colvis').trigger();
    });
});

</script> -->
@endpush 
