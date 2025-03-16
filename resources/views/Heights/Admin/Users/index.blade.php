@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;300;400;600;700;900&display=swap" rel="stylesheet">

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

        .user-modal-content {
            border-radius: 20px !important;
            overflow: hidden; /* Important for applying radius properly */
        }

        .user-modal-dialog {
            border-radius: 20px !important;
        }

        #userModal h5{
            font-size: 15px;
            font-weight: 600;
            color: var(--modal-text);
            font-family: 'Montserrat', sans-serif;
        }

        #userEmail {
            display: inline-block;
            width: 170px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: middle;
        }

        #userModal span{
            font-size: 15px;
            color: var(--modal-text);
            font-family: 'Montserrat', sans-serif;
        }

        .user-modal-header {
            background: var(--modal-bg) !important;
            /*background: var(--modal-header-bg);*/
            color: var(--modal-text) !important;
            font-family: 'Montserrat', sans-serif !important;
        }

        #userModalLabel{
            font-size: 18px !important;
            font-weight: bold !important;
        }

        .user-modal-body {
            background: var(--modal-bg) !important;
            color: var(--modal-text) !important;
            font-family: 'Montserrat', sans-serif !important;
        }

        .user-modal-footer {
            background: var(--modal-bg) !important;
            border-top: 1px solid var(--modal-border) !important;
        }

        .user-modal-close-btn {
            background: white;
            color: var(--modal-btn-text);
            border: 2px solid var(--modal-btn-bg);
            border-radius: 10px;
        }

        .user-modal-close-btn:hover {
            background: var(--modal-btn-bg);
            color: var(--modal-btn-text-hover);
            opacity: 0.8;
        }

        .user-close-btn {
            filter: invert(var(--invert, 0));
        }

        .user-img-border {
            border: 2px solid var(--modal-border);
        }


        @media (max-width: 576px) { /* Small screens */
            #userEmail {
                width: 155px;
            }
        }


    </style>
@endpush

@section('content')

    <!-- Top Navbar -->
    <x-Admin.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => url('admin_dashboard'), 'label' => 'Dashboard'],
            ['url' => '', 'label' => 'Users']
        ]"
    />

    <!-- Side Navbar -->
    <x-Admin.side-navbar :openSections="['AdminControl', 'UserManagement']" />
    <x-error-success-model />

    <div id="main">

        <section class="content mt-1 mb-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box ">
                            <div class="container mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h3 class="mb-1">User Management</h3>
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
                                                @forelse($users ?? [] as $user)
                                                <tr>
                                                    <td>{{ $user->id }}</td>
                                                    <td>
                                                        <img src="{{ $user->picture ? asset($user->picture) : asset('uploads/users/images/Placeholder.jpg') }}" alt="User Picture" class="rounded-circle" width="50" height="50">
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
                                                @empty
                                                    <tr>
                                                        <td colspan="10" class="text-center">No user found.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>

                                        @if($users)
                                            <div class="mt-3">
                                                {{ $users->links('pagination::bootstrap-5') }}
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
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content shadow-lg user-modal-content">
                <!-- Header -->
                <div class="modal-header user-modal-header position-relative">
                    <h5 class="modal-title fw-bold w-100 text-center" id="userModalLabel">User Details</h5>
{{--                    <button type="button" class="btn-close user-close-btn position-absolute end-0 me-2" data-bs-dismiss="modal" aria-label="Close"></button>--}}
                </div>

                <!-- Body -->
                <div class="modal-body user-modal-body">
                    <div class="d-flex flex-column align-items-center justify-content-center mb-3">
                        <div class="d-flex align-items-center">
                            <img id="userPicture" src="" alt="User Picture" class="img-fluid rounded-circle shadow-sm border user-img-border"
                                 style="width: 140px; height: 140px; object-fit: cover;">
                            <div class="ms-3" style="padding-left: 10px !important;">
                                <h5 id="userName" class="mb-1"></h5>
                                <p class="mb-0"><span id="userRole"></span> <span id="userStatus" class="rounded-circle d-inline-block mt-2 mx-2" style="width: 10px; height: 10px;"></span></p>

                            </div>
                        </div>
                    </div>


                    <div class="container">
                        <div class="row px-3">
                            <div class="col-7 mb-2">
                                <h5>Email</h5>
                                <span id="userEmail"></span>
                            </div>
                            <div class="col-5 mb-2">
                                <h5>Gender</h5>
                                <span id="userGender"></span>
                            </div>
                            <div class="col-7 mb-2">
                                <h5>CNIC</h5>
                                <span id="userCnic"></span>
                            </div>
                            <div class="col-5 mb-2">
                                <h5>Phone no</h5>
                                <span id="userPhone"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer user-modal-footer">
                    <button type="button" class="btn user-modal-close-btn w-100" data-bs-dismiss="modal">Close</button>
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
            var table = new DataTable("#usersTable", {
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

    <!-- User Detail Model script -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".view-user").forEach(button => {
                button.addEventListener("click", function () {
                    let userId = this.dataset.id;

                    fetch(`{{ route('users.show', ':id') }}`.replace(':id', userId), {
                        method: "GET",
                        headers: {
                            "Accept": "application/json"
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            let user = data.user;

                            document.getElementById("userPicture").src = user.picture ? "{{ asset('/') }}" + user.picture : "{{ asset('assets/placeholder-profile.png') }}";
                            document.getElementById("userName").textContent = user.name;
                            document.getElementById("userEmail").textContent = user.email;
                            document.getElementById("userPhone").textContent = user.phone_no;
                            document.getElementById("userCnic").textContent = user.cnic;
                            document.getElementById("userGender").textContent = user.gender;
                            document.getElementById("userRole").textContent = user.role.name;

                            let userStatus = document.getElementById("userStatus");
                            userStatus.classList.remove("bg-success", "bg-danger");
                            userStatus.classList.add(user.status ? "bg-success" : "bg-danger");

                            let userModal = new bootstrap.Modal(document.getElementById("userModal"));
                            userModal.show();
                        })
                        .catch(error => {
                            console.error("Error:", error);
                        });
                });
            });
        });
    </script>


    @endpush
