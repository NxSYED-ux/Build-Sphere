@extends('layouts.app')

@section('title', 'Levels')

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
    </style>
@endpush 

@section('content')

    <!-- Top Navbar -->
    <x-Admin.top-navbar :searchVisible="false"/>

    <!-- Side Navbar -->
    <x-Admin.side-navbar :openSections="['Buildings', 'Levels']" />  
    <x-error-success-model />


    <div id="main">
        <section class="content-header pt-2">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mx-5">
                    <li class="breadcrumb-item"><a href="{{url('admin_dashboard')}}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="">Levels</a></li>
                </ol>
            </nav> 
        </section>

        <section class="content content-top my-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box ">
                            <div class="container mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h3 class="mb-1">Levels</h3>
                                    <a href="{{ route('levels.create') }}" class="btn float-end" id="add_button"  data-bs-toggle="tooltip" data-bs-placement="top" title="Add Level"><i class="fa fa-plus"></i></a> 
                                </div>  
                                <div class="card shadow p-3 mb-5 bg-body rounded" style="border: none;">
                                    <div class="card-body " style="overflow-x: auto;">
                                        <table id="LevelsTable" class="table shadow-sm table-hover table-striped">
                                            <thead class="shadow">
                                                <tr>
                                                    <th>ID</th> 
                                                    <th>Name</th>
                                                    <th>Description</th>
                                                    <th>Level Number</th>
                                                    <th>Status</th>
                                                    <th>Building</th> 
                                                    <th class="w-170 text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($levels as $level)
                                                <tr>
                                                    <td>{{ $level->id }}</td> 
                                                    <td>{{ $level->level_name }}</td>
                                                    <td>{{ $level->description ?? 'N/A' }}</td>
                                                    <td>{{ $level->level_number ?? 'N/A' }}</td>
                                                    <td>
                                                        {{ $level->status ?? 'N/A' }}
                                                    </td>
                                                    <td>{{ $level->building->name ?? 'N/A' }}</td> 
                                                    <td class="w-170 text-center"> 
                                                        <a href="{{ route('units.index', ['level_id' => $level->id]) }}" class="text-info" title="View Units"><i class="bx bxs-home icons" style="font-size: 20px;margin-right:5px;;"></i></a>
                                                        <a href="{{ route('levels.edit', $level->id) }}" class="text-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                            <i class="fa fa-pencil mx-2" style="font-size: 20px;"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                @endforeach
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
        $(document).ready(function () {
            $('#LevelsTable').DataTable({
                "pageLength": 10,
                "lengthMenu": [10, 20, 50, 100],
                "language": {
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    }
                }
            }); 
        });
    </script>

    <!-- Toogle script -->
    <script>  
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script> 

@endpush
