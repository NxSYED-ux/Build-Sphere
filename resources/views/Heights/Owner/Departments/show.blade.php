@extends('layouts.app')

@section('title', 'Department')

@push('styles')
    <style>
        body {
        }
        #main {
            margin-top: 45px;
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

        #menu-icon {
            position: relative;
            z-index: 1001;
        }

        #button-list {
            z-index: 1000;
        }
    </style>
@endpush

@section('content')

    <!-- Top Navbar -->
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => url('owner_manager_dashboard'), 'label' => 'Dashboard'],
            ['url' => route('owner.departments.index'), 'label' => 'Departments'],
            ['url' => '', 'label' => 'Show']
        ]"
    />

    <!-- Side Navbar -->
    <x-Owner.side-navbar :openSections="['Departments']" />
    <x-error-success-model />


    <div id="main">

        <section class="content mt-1 mb-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <div class="d-flex align-items-center gap-3">
                                    <h1 class="mb-0">{{ $department->name }}</h1>
                                    <!-- Simple Edit Icon -->
                                    <a href="#" class="text-warning Owner-Department-Edit-Button" data-id="{{ $department->id }}" title="Edit Department">
                                        <i class="fas fa-pen p-2 rounded" style="font-size: 20px; background-color: var(--sidenavbar-body-color);"></i>
                                    </a>
                                    <!-- Simple Delete Icon -->
                                    <form action="{{ route('owner.departments.destroy', $department->id) }}" method="POST" class="d-inline" id="delete-department-form-{{ $department->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="id" value="{{ $department->id }}">
                                        <button type="button" class="text-danger bg-transparent border-0 p-0 delete-department-btn" title="Delete Department" data-id="{{ $department->id }}">
                                            <i class="fas fa-trash p-2 rounded" style="font-size: 20px; background-color: var(--sidenavbar-body-color);"></i>
                                        </button>
                                    </form>
                                </div>
                                <p>{{ $department->description }}</p>
                            </div>
                            <a href="{{ route('owner.departments.index') }}" class="btn btn-secondary">Go Back</a>
                        </div>


                        <div class="card shadow px-3 pb-3 pt-0 mb-5 mt-0 bg-body rounded" style="border: none;">
                            <h4 class="pt-2" style="color: var(--sidenavbar-text-color);">Staff Members</h4>
                            <div class="card-body py-0" style="position: relative; overflow-x: auto;">
                                <div class="d-flex align-items-center position-absolute mt-0" style="top: 25px; left: 30px;">
                                    <button class="btn btn-light" type="button" id="menu-icon" data-bs-toggle="dropdown" aria-expanded="false">
                                        <x-icon name="export" type="icon" class="" size="20px" />
                                    </button>

                                    <ul id="button-list" class="dropdown-menu dropdown-menu-end" style="position: absolute; top: 100%; left: 0;">
                                        <li><button class="dropdown-item" type="button" id="copyButton">Copy</button></li>
                                        <li><button class="dropdown-item" type="button" id="csvButton">CSV</button></li>
                                        <li><button class="dropdown-item" type="button" id="excelButton">Excel</button></li>
                                        <li><button class="dropdown-item" type="button" id="pdfButton">PDF</button></li>
                                        <li><button class="dropdown-item" type="button" id="printButton">Print</button></li>
                                    </ul>
                                </div>
                                <table id="departmentStaffTable" class="table shadow-sm table-hover table-striped">
                                    <thead class="shadow">
                                    <tr>
                                        <th>ID</th>
                                        <th>Picture</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone no</th>
                                        <th>City</th>
                                        <th>Building</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($staffMembers ?? [] as $staffMember)
                                        <tr>
                                            <td>{{ $staffMember->id }}</td>
                                            <td>
                                                <img src="{{ $staffMember->user->picture ? asset($staffMember->user->picture) : asset('img/placeholder-profile.png') }}" alt="Staff Picture" class="rounded-circle" width="50" height="50">
                                            </td>
                                            <td>{{ $staffMember->user->name }}</td>
                                            <td>{{ $staffMember->user->email }}</td>
                                            <td>{{ $staffMember->user->phone_no ?? 'N/A' }}</td>
                                            <td>{{ $staffMember->user->address ? $staffMember->user->address->city : 'N/A' }}</td>
                                            <td>{{ $staffMember->building ? $staffMember->building->name : 'N/A' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">No staff found.</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                    @if ($staffMembers)
                                        <div class="mt-3 custom-pagination-wrapper">
                                            {{ $staffMembers->links('pagination::bootstrap-5') }}
                                        </div>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>

    <!-- Edit Dropdown Value Modal -->
    <div class="modal fade" id="editDepartmentModel" tabindex="-1" aria-labelledby="editDepartmentModelLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDepartmentModelLabel">Edit Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editDepartmentForm" action="{{ route('owner.departments.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id" id="edit_department_id">
                    <input type="hidden" name="updated_at" id="edit_updated_at">

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="name">Name</label>
                                    <span class="required__field">*</span><br>
                                    <input type="text" name="edit_name" id="edit_department_name" class="form-control @error('edit_name') is-invalid @enderror" value="{{ old('edit_name') }}" maxlength="50" placeholder="Department Name" required>
                                    @error('edit_name')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-2">
                                    <label for="edit_department_description">Description</label>
                                    <textarea name="edit_description" id="edit_department_description" rows="3" class="form-control @error('edit_description') is-invalid @enderror" maxlength="250" placeholder="Description">{{ old('edit_description') }}</textarea>
                                    @error('edit_description')
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
        document.addEventListener("DOMContentLoaded", function () {
            var table = new DataTable("#departmentStaffTable", {
                paging: false,
                info: false,
                dom: "Bfrtip",
                lengthChange: false,
                language: {
                    searchPlaceholder: "Search staff..."
                },
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle all delete buttons
            document.querySelectorAll('.delete-department-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const departmentId = this.getAttribute('data-id');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Submit the corresponding form
                            document.getElementById(`delete-department-form-${departmentId}`).submit();
                        }
                    });
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editButtons = document.querySelectorAll(".Owner-Department-Edit-Button");

            editButtons.forEach(button => {
                button.addEventListener("click", function (e) {
                    e.preventDefault();
                    const id = this.getAttribute("data-id");

                    fetch(`{{ route('owner.departments.edit', ':id') }}`.replace(':id', id), {
                        method: "GET",
                        headers: {
                            "X-Requested-With": "XMLHttpRequest",
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.error) {
                                alert(data.error);
                            } else {
                                document.getElementById("edit_department_id").value = data.department?.id || "";
                                document.getElementById("edit_department_name").value = data.department?.name || "";
                                document.getElementById("edit_department_description").value = data.department?.description || "";
                                document.getElementById("edit_updated_at").value = data.department?.updated_at || "";

                                const editForm = document.getElementById("editDepartmentForm");
                                editForm.setAttribute("action", `{{ route('owner.departments.update') }}`);

                                // Ensure the hidden department_id field exists
                                let departmentIdInput = editForm.querySelector('input[name="department_id"]');
                                if (!departmentIdInput) {
                                    departmentIdInput = document.createElement("input");
                                    departmentIdInput.type = "hidden";
                                    departmentIdInput.name = "department_id";
                                    editForm.appendChild(departmentIdInput);
                                }
                                departmentIdInput.value = id;

                                // Show the modal
                                let editModal = new bootstrap.Modal(document.getElementById("editDepartmentModel"));
                                editModal.show();
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert("An error occurred while retrieving the data.");
                        });
                });
            });
        });
    </script>
@endpush
