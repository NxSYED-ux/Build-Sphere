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
    </style>
@endpush

@section('content')

    <!-- Top Navbar -->
    <x-Admin.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => url('admin_dashboard'), 'label' => 'Dashboard'],
            ['url' => '', 'label' => 'Levels']
        ]"
    />

    <!-- Side Navbar -->
    <x-Admin.side-navbar :openSections="['Buildings', 'Levels']" />
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
                                    <a href="#" class="btn float-end" id="add_button"  data-bs-toggle="tooltip" data-bs-placement="top" title="Add Level"><i class="fa fa-plus"></i></a>
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
                                                        <a href="#" class="text-warning edit_level_button" id="edit_level_button" data-id="{{ $level->id }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                            <i class="fa fa-pencil" style="font-size: 20px;"></i>
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
                                    <select class="form-select" id="building_id" name="building_id" value="{{ old('building_id') }}" required>
                                        <option value="" disabled {{ old('building_id') === null ? 'selected' : '' }}>Select Building</option>
                                        @foreach($buildings as $building)
                                            <option value="{{ $building->id }}" {{ old('building_id') == $building->id ? 'selected' : '' }}>
                                                {{ $building->name }}
                                            </option>
                                        @endforeach
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
                    <div class="modal-body">
                        <div class="row mb-4">
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
                                    <input type="number" name="level_number" id="level_number" class="form-control @error('level_number') is-invalid @enderror" value="{{ old('level_number' ) }}" placeholder="Enter Level/Floor no" required>
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
                                    <select class="form-select" id="building_id" name="building_id" required>
                                        <option value="" disabled {{ old('building_id') === null ? 'selected' : '' }}>Select Building</option>
                                        @foreach($buildings as $building)
                                            <option value="{{ $building->id }}" {{ old('building_id') == $building->id ? 'selected' : '' }}>
                                                {{ $building->name }}
                                            </option>
                                        @endforeach
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
                                    <select name="status" id="status" class="form-select" required>
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

    <!-- Value Type Scripts -->
    <script>
        $(document).ready(function () {
            $('#add_button').on('click', function (e) {
                e.preventDefault();
                $('#createLevelModal').modal('show');
            });

            $(document).on('click', '.edit_level_button', function (e) {
                e.preventDefault();
                const id = $(this).data('id');
                $.ajax({
                    url: `{{ route('levels.edit', ':id') }}`.replace(':id', id),
                    type: 'GET',
                    success: function (data) {
                        if (data.message) {
                            alert(data.message);
                        } else {
                            $('#editLevelModal #level_name').val(data.level_name);
                            $('#editLevelModal #description').val(data.description);
                            $('#editLevelModal #level_number').val(data.level_number);
                            $('#editLevelModal #status').val(data.status);
                            $('#editLevelModal #building_id').val(data.building_id);
                            $('#editLevelForm').attr('action', `{{ route('levels.update', ':id') }}`.replace(':id', id));
                            $('#editLevelForm').append('<input type="hidden" name="_method" value="PUT">'); // Adding the PUT method input
                            $('#editLevelModal').modal('show');
                        }
                    },
                    error: function () {
                        alert('An error occurred while retrieving the data.');
                    }
                });
            });
        });
    </script>

@endpush
