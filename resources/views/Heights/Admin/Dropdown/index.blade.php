@extends('layouts.app')

@section('title', 'Values')

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

        #add_dropdwon_type_button{
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

    <!--  -->
    <x-Admin.top-navbar :searchVisible="false"/>
    <!--  -->
    <x-Admin.side-navbar :openSections="['AdminControl', 'Dropdown']" /> 
    <x-error-success-model />

    <div id="main">
        <section class="content-header pt-2">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mx-5">
                    <li class="breadcrumb-item"><a href="{{ url('admin_dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="">Dropdowns</a></li>
                </ol>
            </nav>
        </section>

        <section class="content content-top my-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box" style="overflow-x: auto;">
                            <div class="container mt-2">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link {{ $activeTab === 'Types' ? 'active' : '' }}" id="dropdwon-types-tab" data-bs-toggle="tab" href="#dropdwon-types" role="tab" aria-controls="dropdwon-types" aria-selected="{{ $activeTab === 'Types' ? 'true' : 'false' }}">Types</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link {{ $activeTab === 'Values' ? 'active' : '' }}" id="dropdwon-values-tab" data-bs-toggle="tab" href="#dropdwon-values" role="tab" aria-controls="dropdwon-values" aria-selected="{{ $activeTab === 'Values' ? 'true' : 'false' }}">Values</a>
                                    </li>
                                </ul>
                                <div class="tab-content mt-0" id="myTabContent">
                                    <!-- Value Types Tab -->
                                    <div class="tab-pane fade {{ $activeTab === 'Types' ? 'show active' : '' }}" id="dropdwon-types" role="tabpanel" aria-labelledby="dropdwon-types-tab">
                                        <div class="card shadow p-3 pt-1 mb-5 bg-body rounded" style="border: none;">
                                            <div class="card-body" style="overflow-x: auto;">
                                                <a href="#" class="btn float-end" id="add_dropdwon_type_button"  data-bs-toggle="tooltip" data-bs-placement="top" title="Add"><i class="fa fa-plus"></i></a> 
                                                <h3 class="mb-4">Types</h3>
                                                <div style="overflow-x: auto;">
                                                    <table id="typesTable" class="table shadow-sm table-hover table-striped"> 
                                                        <thead class="shadow">
                                                            <tr>
                                                                <th>ID</th>
                                                                <th>Name</th>
                                                                <th>Description</th>
                                                                <th>Parent Type</th>
                                                                <th>Status</th>
                                                                <th class="text-center" style="width: 70px;">Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($types as $type)
                                                                <tr>
                                                                    <td>{{ $type->id }}</td>
                                                                    <td>{{ $type->type_name }}</td>
                                                                    <td>{{ $type->description ?? 'N/A' }}</td>
                                                                    <td>{{ $type->parent->type_name ?? 'N/A' }}</td> 
                                                                    <td>{{ $type->status ? 'Active' : 'Inactive' }}</td>
                                                                    <td class="text-center" style="width: 70px;">
                                                                        <a href="#" class="text-warning edit_dropdwon_type_button" id="edit_dropdwon_type_button" data-id="{{ $type->id }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
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
                                    <!-- Value Masters Tab -->
                                    <div class="tab-pane fade {{ $activeTab === 'Values' ? 'show active' : '' }}" id="dropdwon-values" role="tabpanel" aria-labelledby="dropdwon-values-tab">
                                        <div class="card shadow p-3 pt-1 mb-5 bg-body rounded" style="border: none;">
                                            <div class="card-body" style="overflow-x: auto;">
                                                <a href="{{ route('values.create') }}" class="btn float-end" id="add_button"><i class="fa fa-plus"></i></a>
                                                <h3 class="mb-4">Values</h3>
                                                <div style="overflow-x: auto;">
                                                    <table id="valuesTable" class="table shadow-sm table-hover table-striped"> 
                                                        <thead class="shadow">
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Name</th>
                                                                <th>Description</th>
                                                                <th>Type</th>
                                                                <th>Parent Value</th> 
                                                                <th>Status</th> 
                                                                <th class="text-center" style="width: 100px;">Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($values as $value)
                                                            <tr>
                                                                <td>{{ $value->id }}</td>
                                                                <td>{{ $value->value_name }}</td>
                                                                <td>{{ $value->description }}</td>
                                                                <td>{{ $value->type->type_name }}</td>
                                                                <td>{{ $value->parent->value_name ?? 'N/A' }}</td>  
                                                                <td>{{ $value->status ? 'Active' : 'Inactive' }}</td> 
                                                                <td class="text-center" style="width: 100px;">
                                                                    <a href="{{ route('values.edit', $value->id) }}" class="text-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i class="fa fa-pencil" style="font-size: 20px;"></i></a>
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
                    </div>
                </div>
            </div>
        </section>
    </div>  


    <!-- Create Dropdwon Type Modal -->
    <div class="modal fade" id="createDropdownTypeModal" tabindex="-1" aria-labelledby="createDropdownTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createDropdownTypeModalLabel">Create Dropdown Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createDropdownForm" method="POST" action="{{ route('types.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="type_name" class="form-label">Name</label>
                            <span class="required__field">*</span><br>
                            <input type="text" class="form-control @error('type_name') is-invalid @enderror" id="type_name" name="type_name" value="{{ old('type_name') }}" maxlength="30" placeholder="Type Name" required>
                            @error('type_name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" value="{{ old('description') }}" maxlength="250" placeholder="Description" rows="2" ></textarea>
                            @error('description')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div> 
                        <div class="mb-3">
                            <label for="parent_type" class="form-label">Parnet Type</label>
                            <select class="form-select" id="parent_type_id" name="parent_type_id">
                                <option value="" {{ old('parent_type_id') == '' ? 'selected' : '' }}>Select Parnet Type</option>
                                @foreach($types as $type)
                                    <option value="{{ $type->id }}" {{ old('parnet_type_id') == $type->id ? 'selected' : '' }}>{{ $type->type_name }}</option>
                                @endforeach  
                            </select> 
                            @error('parent_type_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <span class="required__field">*</span><br>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" value="{{ old('status') }}" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            @error('status')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
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
    <div class="modal fade" id="editDropdownTypeModal" tabindex="-1" aria-labelledby="editDropdownTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDropdownTypeModalLabel">Edit Lov Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- The edit form will be loaded here via AJAX -->
                    <form id="editForm" action="" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="type_name" class="form-label">Type Name</label>
                            <span class="required__field">*</span><br>
                            <input type="text" class="form-control" id="type_name" name="type_name" maxlength="30" placeholder="Type Name" required> 
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" maxlength="250" placeholder="Description"></textarea> 
                        </div>
                        
                        <div class="mb-3">
                            <label for="parent_type" class="form-label">Parnet Type</label>
                            <select class="form-select" id="parent_type_id" name="parent_type_id">
                                <option value="" {{ old('parent_type_id') == '' ? 'selected' : '' }}>Select Parnet Type</option>
                                @foreach($types as $type)
                                    <option value="{{ $type->id }}" {{ old('parnet_type_id') == $type->id ? 'selected' : '' }}>{{ $type->type_name }}</option>
                                @endforeach  
                            </select> 
                            @error('parent_type_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <span class="required__field">*</span><br>
                            <select class="form-select" id="status" name="status" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select> 
                        </div> 

                        <button type="submit" class="btn btn-primary">Update Type</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </form>
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

    <!-- DataTables script -->
    <script>
        $(document).ready(function() {
            $('#valuesTable').DataTable({
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
        $(document).ready(function() {
            $('#typesTable').DataTable({
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

    <!-- Value Type Scripts -->
    <script>
        $(document).ready(function () {
            $('#add_dropdwon_type_button').on('click', function (e) {
                e.preventDefault();
                $('#createDropdownTypeModal').modal('show');
            });

            $(document).on('click', '.edit_dropdwon_type_button', function (e) {
                e.preventDefault();
                const id = $(this).data('id');
                $.ajax({
                    url: `{{ route('types.edit', ':id') }}`.replace(':id', id),
                    type: 'GET',
                    success: function (data) {
                        if (data.message) {
                            alert(data.message);
                        } else {
                            $('#editDropdownTypeModal #type_name').val(data.type_name);
                            $('#editDropdownTypeModal #description').val(data.description);
                            $('#editDropdownTypeModal #parent_type_id').val(data.parent_type_id);
                            $('#editDropdownTypeModal #status').val(data.status);
                            $('#editForm').attr('action', `{{ route('types.update', ':id') }}`.replace(':id', id));
                            $('#editForm').append('<input type="hidden" name="_method" value="PUT">'); // Adding the PUT method input
                            $('#editDropdownTypeModal').modal('show');
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