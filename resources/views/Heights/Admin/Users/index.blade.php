@extends('layouts.app')

@section('title', 'Dashboard')

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

        .theme-modal-header {
            background: var(--modal-header-bg);
            color: var(--modal-text);
        }

        .theme-modal-body {
            background: var(--modal-bg);
            color: var(--modal-text);
        }

        .theme-modal-footer {
            background: var(--modal-bg);
            border-top: 1px solid var(--modal-border);
        }

        .theme-modal-close-btn {
            background: var(--modal-btn-bg);
            color: var(--modal-btn-text);
            border: none;
        }

        .theme-modal-close-btn:hover {
            background: var(--modal-btn-bg);
            color: var(--modal-btn-text);
            opacity: 0.8;
            border: none;
        }

        .theme-close-btn {
            filter: invert(var(--invert, 0));
        }

        .theme-img-border {
            border: 2px solid var(--modal-border);
        }





    </style>
@endpush

@section('content')

    <!-- Top Navbar -->
    <x-Admin.top-navbar :searchVisible="false"/>

    <!-- Side Navbar -->
    <x-Admin.side-navbar :openSections="['AdminControl', 'UserManagement']" />
    <x-error-success-model />

    <!-- filter: invert(1); -->

    <div id="main">
        <section class="content-header pt-2">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-chevron" style="margin-left: 35px;">
                    <li class="breadcrumb-item"><a href="{{url('admin_dashboard')}}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="">Users</a></li>
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
                                    <h3 class="mb-1">All users</h3>
                                    <a href="{{ route('users.create') }}" class="btn float-end" id="add_button"  data-bs-toggle="tooltip" data-bs-placement="top" title="Add User"><i class="fa fa-plus"></i></a>
                                </div>
                                <div class="card shadow p-1 mb-5 bg-body rounded" style="border: none;">
                                    <div class="card-body rounded" style="overflow-x: auto;">
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

                                            <form method="GET" action="{{ route('users.index') }}" class="d-flex" style="margin-left: 6px;">
                                                <input type="text"  name="search" class="form-control me-2" placeholder="Search users..." value="{{ request('search') }}">
                                                <button type="submit" class="btn btn-primary d-flex justify-content-center align-items-center" style="height: 40px; width: 40px;">
                                                    <i class='bx bx-search' style="font-size: 20px;"></i>
                                                </button>

                                            </form>
                                        </div>

                                        <table id="usersTable" class="table shadow-sm table-hover table-striped">  <!-- table-bordered -->
                                            <thead class="shadow">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Picture</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Phone no</th>
                                                    <th>Gender</th>
                                                    <th>Role</th>
                                                    <th>City</th>
                                                    <th>Status</th>
                                                    <th class="w-170 text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($users as $user)
                                                <tr>
                                                    <td>{{ $user->id }}</td>
                                                    <td>
                                                        <img src="{{ $user->picture ? asset($user->picture) : asset('https://via.placeholder.com/150') }}" alt="User Picture" class="rounded-circle" width="50" height="50">
                                                    </td>
                                                    <td>{{ $user->name }}</td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>{{ $user->phone_no }}</td>
                                                    <td>{{ $user->gender }}</td>
                                                    <td>{{ $user->role->name }}</td>
                                                    <td>{{ $user->address->city ?? 'N/A' }}</td>
                                                    <td>{{ $user->status ? 'Active' : 'Inactive' }}</td>
                                                    <td class="w-170 text-center">
                                                        <a href="javascript:void(0);" class="text-info view-user" data-id="{{ $user->id }}" data-bs-toggle="tooltip" data-bs-placement="top" title="View"><i class="fa fa-eye mx-2" style="font-size: 20px;"></i></a>
                                                        <a href="{{ route('users.edit', $user->id) }}" class="text-warning @if($user->role_id === 6) disabled-link @endif" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"> <i class="fa fa-pencil mx-2" style="font-size: 20px;"></i> </a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        <div class="mt-3">
                                            {{ $users->links('pagination::bootstrap-5') }}
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

    <!-- User Details Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <!-- Header -->
                <div class="modal-header theme-modal-header position-relative">
                    <h5 class="modal-title fw-bold w-100 text-center" id="userModalLabel">User Details</h5>
                    <button type="button" class="btn-close theme-close-btn position-absolute end-0 me-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Body -->
                <div class="modal-body theme-modal-body">
                    <div class="text-center mb-3">
                        <img id="userPicture" src="" alt="User Picture" class="img-fluid rounded-circle shadow-sm border theme-img-border" style="width: 120px; height: 120px; object-fit: cover;">
                    </div>
                    <h5 id="userName" class="text-center fw-semibold mb-3"></h5>
                    <div class="container">
                        <div class="row px-3">
                            <div class="col-md-6">
                                <p><strong>Email:</strong> <span id="userEmail"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Phone no:</strong> <span id="userPhone"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>CNIC:</strong> <span id="userCnic"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Gender:</strong> <span id="userGender"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Role:</strong> <span id="userRole"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Status:</strong>
                                    <span id="userStatus" class="badge rounded-pill px-3 py-2"></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer theme-modal-footer">
                    <button type="button" class="btn theme-modal-close-btn w-100" data-bs-dismiss="modal">Close</button>
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
        $(document).ready(function () {

            var table = $('#usersTable').DataTable({
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

    <!-- User Detail Model script -->
    <script>
        $(document).ready(function () {
            // Show user details modal
            $(document).on('click', '.view-user', function () {
                var userId = $(this).data('id');
                $.ajax({
                    url: `{{ route('users.show', ':id') }}`.replace(':id', userId),
                    method: 'GET',
                    success: function (response) {
                        var data = response.user;
                        $('#userModal #userPicture').attr('src', data.picture ? data.picture : 'https://via.placeholder.com/150');
                        $('#userModal #userName').text(data.name);
                        $('#userModal #userEmail').text(data.email);
                        $('#userModal #userPhone').text(data.phone_no);
                        $('#userModal #userCnic').text(data.cnic);
                        $('#userModal #userGender').text(data.gender);
                        $('#userModal #userRole').text(data.role.name);
                        $('#userModal #userStatus') .text(data.status ? 'Active' : 'Inactive') .removeClass('bg-success bg-danger text-light text-dark').addClass(data.status ? 'bg-success text-dark' : 'bg-danger text-light');
                        $('#userModal').modal('show');
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });
        });

    </script>

@endpush
