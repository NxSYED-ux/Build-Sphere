@extends('layouts.app')

@section('title', 'Users')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;300;400;600;700;900&display=swap" rel="stylesheet">

    <style>
        body {
        }
        #main {
            margin-top: 50px;
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
            overflow: hidden;
            box-shadow: none !important;
            border: 2px solid var(--modal-border);
        }

        .user-modal-content {
            border-radius: 20px !important;
            overflow: hidden;
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

        /* New styles for tab view */
        .nav-tabs {
            border-bottom: 2px solid #dee2e6;
        }

        .nav-tabs .nav-link {
            border: none;
            color: var(--sidenavbar-text-color);
            font-weight: 600;
            padding: 12px 20px;
            margin-right: 1px;
            border-radius: 8px 8px 0 0;
        }

        .nav-tabs .nav-link.active {
            color: var(--sidenavbar-text-color);
            background-color: var(--body-card-bg);
            border-bottom: 3px solid var(--color-blue);
            box-shadow: var(--bs-box-shadow) !important
        }

        .nav-tabs .nav-link:hover:not(.active) {
            background-color: var(--body-card-bg);
        }

        /* User cards styling */
        .user-cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            /*padding: 10px;*/
        }

        .user-card {
            background: var(--body-background-color);
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .user-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .user-card-header {
            padding: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
        }

        .user-card-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
            border: 2px solid #e9ecef;
        }

        .user-card-info {
            flex: 1;
        }

        .user-card-name {
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--sidenavbar-text-color);
        }

        .user-card-role {
            font-size: 0.85rem;
            color: var(--sidenavbar-text-color);
            display: flex;
            align-items: center;
        }

        .user-card-status {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 8px;
            display: inline-block;
        }

        .user-card-body {
            padding: 15px;
        }

        .user-card-detail {
            display: flex;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }

        .user-card-detail-label {
            font-weight: 600;
            color: #495057;
            min-width: 80px;
        }

        .user-card-detail-value {
            color: #212529;
            flex: 1;
        }

        .user-card-actions {
            padding: 10px 10px;
            border-top: 1px solid #eee;
            gap: 10px;
        }

        .user-card-action-btn {
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        @media (max-width: 576px) {
            #userEmail {
                width: 155px;
            }

            .user-cards-container {
                grid-template-columns: 1fr;
            }
        }

    </style>
@endpush

@section('content')

    <!-- Top Navbar -->
    <x-Admin.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('admin_dashboard'), 'label' => 'Dashboard'],
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
{{--                        <div class="box ">--}}
{{--                            <div class="container mt-2">--}}
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h3 class="mb-1">User Management</h3>
                                    <a href="{{ route('users.create') }}" class="btn float-end add_button" id="add_button"  data-bs-toggle="tooltip" data-bs-placement="top" title="Add User">
                                        <x-icon name="add" type="svg" class="" size="25" />
                                    </a>
                                </div>

                                <!-- Tab Navigation -->
                                <ul class="nav nav-tabs" id="userViewTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="cards-tab" data-bs-toggle="tab" data-bs-target="#cards-view" type="button" role="tab" aria-controls="cards-view" aria-selected="true">
                                            <x-icon name="grid_view" type="icon" class="me-1" size="18px" /> Card View
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="table-tab" data-bs-toggle="tab" data-bs-target="#table-view" type="button" role="tab" aria-controls="table-view" aria-selected="false">
                                            <x-icon name="table_view" type="icon" class="me-1" size="18px" /> Table View
                                        </button>
                                    </li>
                                </ul>

                                <div class="tab-content my-0 py-0" id="userViewTabsContent">
                                    <!-- Cards View Tab -->
                                    <div class="tab-pane py-0 my-0 fade show active" id="cards-view" role="tabpanel" aria-labelledby="cards-tab">
                                        <div class="card py-0 shadow my-0 mb-5 bg-body rounded" style="border: none;">
                                            <div class="card-body rounded">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <div class="d-flex align-items-center">
                                                        <button class="btn btn-light" type="button" id="menu-icon" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <x-icon name="export" type="icon" class="" size="20px" />
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                                                            <li><button class="dropdown-item" type="button" id="copyButtonCards"><i class="fas fa-copy me-2"></i>Copy</button></li>
                                                            <li><button class="dropdown-item" type="button" id="csvButtonCards"><i class="fas fa-file-csv me-2"></i>CSV</button></li>
                                                            <li><button class="dropdown-item" type="button" id="excelButtonCards"><i class="fas fa-file-excel me-2"></i>Excel</button></li>
                                                            <li><button class="dropdown-item" type="button" id="pdfButtonCards"><i class="fas fa-file-pdf me-2"></i>PDF</button></li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li><button class="dropdown-item" type="button" id="printButtonCards"><i class="fas fa-print me-2"></i>Print</button></li>
                                                        </ul>
                                                    </div>

                                                    <form method="GET" action="{{ route('users.index') }}" class="d-flex" style="margin-left: 6px;">
                                                        <input type="text" name="search" class="form-control me-2" placeholder="Search users..." value="{{ request('search') }}">
                                                        <button type="submit" class="btn btn-primary d-flex justify-content-center align-items-center" style="height: 40px; width: 40px;">
                                                            <x-icon name="search" type="icon" class="" size="20px" />
                                                        </button>
                                                    </form>
                                                </div>

                                                <div class="user-cards-container">
                                                    @forelse($users ?? [] as $user)
                                                        <div class="user-card shadow">
                                                            <div class="user-card-header position-relative">
                                                                <img src="{{ $user->picture ? asset($user->picture) : asset('img/placeholder-profile.png') }}" alt="User Picture" class="user-card-avatar">
                                                                <div class="user-card-info">
                                                                    <div class="user-card-name">{{ $user->name }}</div>
                                                                    <div class="user-card-role">
                                                                        <span class="user-card-status {{ $user->status ? 'bg-success' : 'bg-danger' }}"></span>
                                                                        {{ $user->role->name }}
                                                                    </div>
                                                                </div>
                                                                <div class="position-absolute  m-2 d-flex gap-1"  style="bottom: 10px; right: 15px;">
                                                                    <a href="{{ route('users.edit', $user->id) }}" class=" text-warning @if($user->role_id === 6) disabled-link @endif" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                                        <x-icon name="edit" type="icon" class="" size="18px" />
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <div class="user-card-body">
                                                                <div class="user-card-detail">
                                                                    <span class="user-card-detail-label">Email:</span>
                                                                    <span class="user-card-detail-value">{{ $user->email }}</span>
                                                                </div>
                                                                <div class="user-card-detail">
                                                                    <span class="user-card-detail-label">Phone:</span>
                                                                    <span class="user-card-detail-value">{{ $user->phone_no }}</span>
                                                                </div>
                                                                <div class="user-card-detail">
                                                                    <span class="user-card-detail-label">Gender:</span>
                                                                    <span class="user-card-detail-value">{{ $user->gender }}</span>
                                                                </div>
                                                                <div class="user-card-detail">
                                                                    <span class="user-card-detail-label">City:</span>
                                                                    <span class="user-card-detail-value">{{ $user->address->city ?? 'N/A' }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="user-card-actions d-flex justify-content-between align-items-center">
                                                                <a href="javascript:void(0);" class="btn btn-primary view-user w-100" data-id="{{ $user->id }}" data-bs-toggle="tooltip" data-bs-placement="top" title="View">
                                                                    <x-icon name="view" type="icon" class="" size="16px" /> View
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <div class="col-12 text-center py-4">
                                                            <p class="text-muted">No users found.</p>
                                                        </div>
                                                    @endforelse
                                                </div>

                                                @if($users)
                                                    <div class="mt-3 custom-pagination-wrapper">
                                                        {{ $users->links('pagination::bootstrap-5') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Table View Tab -->
                                    <div class="tab-pane fade" id="table-view" role="tabpanel" aria-labelledby="table-tab">
                                        <div class="card shadow p-0 mb-5 bg-body rounded" style="border: none;">
                                            <div class="card-body rounded" style="overflow-x: auto;">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <button class="btn btn-light" type="button" id="menu-icon" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <x-icon name="export" type="icon" class="" size="20px" />
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                                                            <li><button class="dropdown-item" type="button" id="copyButton"><i class="fas fa-copy me-2"></i>Copy</button></li>
                                                            <li><button class="dropdown-item" type="button" id="csvButton"><i class="fas fa-file-csv me-2"></i>CSV</button></li>
                                                            <li><button class="dropdown-item" type="button" id="excelButton"><i class="fas fa-file-excel me-2"></i>Excel</button></li>
                                                            <li><button class="dropdown-item" type="button" id="pdfButton"><i class="fas fa-file-pdf me-2"></i>PDF</button></li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li><button class="dropdown-item" type="button" id="printButton"><i class="fas fa-print me-2"></i>Print</button></li>
                                                        </ul>
                                                    </div>

                                                    <form method="GET" action="{{ route('users.index') }}" class="d-flex" style="margin-left: 6px;">
                                                        <input type="text" name="search" class="form-control me-2" placeholder="Search users..." value="{{ request('search') }}">
                                                        <button type="submit" class="btn btn-primary d-flex justify-content-center align-items-center" style="height: 40px; width: 40px;">
                                                            <x-icon name="search" type="icon" class="" size="20px" />
                                                        </button>
                                                    </form>
                                                </div>

                                                <table id="usersTable" class="table shadow-sm table-hover table-striped">
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
                                                                <img src="{{ $user->picture ? asset($user->picture) : asset('img/placeholder-profile.png') }}" alt="User Picture" class="rounded-circle" width="50" height="50">
                                                            </td>
                                                            <td>{{ $user->name }}</td>
                                                            <td>{{ $user->email }}</td>
                                                            <td>{{ $user->phone_no }}</td>
                                                            <td>{{ $user->gender }}</td>
                                                            <td>{{ $user->role->name }}</td>
                                                            <td>{{ $user->address->city ?? 'N/A' }}</td>
                                                            <td>{{ $user->status ? 'Active' : 'Inactive' }}</td>
                                                            <td class="w-170 text-center">
                                                                <div class="d-flex justify-content-center align-items-center gap-3">
                                                                    <a href="javascript:void(0);" class="text-info view-user" data-id="{{ $user->id }}" data-bs-toggle="tooltip" data-bs-placement="top" title="View">
                                                                        <x-icon name="view" type="icon" class="" size="20px" />
                                                                    </a>
                                                                    <a href="{{ route('users.edit', $user->id) }}" class="text-warning @if($user->role_id === 6) disabled-link @endif" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                                        <x-icon name="edit" type="icon" class="" size="20px" />
                                                                    </a>
                                                                </div>
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
                                                    <div class="mt-3 custom-pagination-wrapper">
                                                        {{ $users->links('pagination::bootstrap-5') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
{{--                            </div>--}}
{{--                        </div>--}}
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
            // Initialize DataTable for table view
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

            // Table view export buttons
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

            // Cards view export buttons (they trigger the same DataTable functions)
            document.getElementById("csvButtonCards")?.addEventListener("click", function () {
                triggerButton(".buttons-csv", "CSV Button clicked from Cards view");
            });

            document.getElementById("excelButtonCards")?.addEventListener("click", function () {
                triggerButton(".buttons-excel", "Excel Button clicked from Cards view");
            });

            document.getElementById("pdfButtonCards")?.addEventListener("click", function () {
                triggerButton(".buttons-pdf", "PDF Button clicked from Cards view");
            });

            document.getElementById("printButtonCards")?.addEventListener("click", function () {
                triggerButton(".buttons-print", "Print Button clicked from Cards view");
            });
        });
    </script>

    <!-- User Detail Model script -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // This works for both table and card view user clicks
            document.querySelectorAll(".view-user").forEach(button => {
                button.addEventListener("click", function () {
                    let userId = this.dataset.id;

                    fetch(`{{ route('users.show', ':id') }}`.replace(':id', userId), {
                        method: "GET",
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
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
