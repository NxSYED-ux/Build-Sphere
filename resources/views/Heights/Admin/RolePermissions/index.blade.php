@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <!-- Role Selection Card -->
        <div class="card shadow-lg border-0">
            <div class="card-header bg-gradient-primary text-white">
                <h3 class="mb-0">Manage Role Permissions</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('role.permissions') }}">
                    <div class="form-group">
                        <label for="role" class="font-weight-bold">Select Role:</label>
                        <select name="role_id" id="role" class="form-control custom-select" onchange="this.form.submit()">
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ $roleId == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <!-- Permissions Card -->
        @if ($roleId)
            <div class="card mt-4 shadow-lg border-0">
                <div class="card-header bg-gradient-secondary text-white">
                    <h4 class="mb-0">Permissions for Role: {{ optional($roles->where('id', $roleId)->first())->name }}</h4>
                </div>
                <div class="card-body">
                    @if ($permissions->isEmpty())
                        <p class="text-muted">No permissions assigned to this role.</p>
                    @else
                        @foreach ($permissions as $header => $perms)
                            <div class="mb-4">
                                <h5 class="text-primary mb-3 font-weight-bold">{{ $header }}</h5>
                                <div class="permission-list bg-white rounded-lg shadow-sm">
                                    @foreach ($perms as $perm)
                                        <div class="permission-item d-flex justify-content-between align-items-center p-3">
                                            <span class="permission-name font-weight-medium">{{ $perm['name'] }}</span>
                                            <button class="toggle-btn {{ $perm['status'] == 0 ? '' : 'active' }}"
                                                    data-permission-id="{{ $perm['id'] }}"
                                                    data-role-id="{{ $roleId }}"
                                                    data-status="{{ $perm['status'] }}"
                                                    onclick="togglePermission(this)">
                                                <div class="toggle-switch"></div>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        @endif
    </div>

    <style>
        /* Gradient Backgrounds */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #4e73df, #224abe);
        }

        .bg-gradient-secondary {
            background: linear-gradient(135deg, #858796, #60616f);
        }

        /* Toggle Button Styling */
        .toggle-btn {
            width: 50px;
            height: 25px;
            border-radius: 25px;
            background-color: #e9ecef;
            border: 2px solid #ced4da;
            position: relative;
            cursor: pointer;
            transition: background 0.3s ease, border-color 0.3s ease;
            display: flex;
            align-items: center;
            padding: 3px;
            outline: none;
            flex-shrink: 0;
        }

        .toggle-btn:hover {
            background-color: #dee2e6;
        }

        .toggle-switch {
            width: 20px;
            height: 20px;
            background-color: white;
            border-radius: 50%;
            position: absolute;
            left: 3px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .toggle-btn.active {
            background-color: #4CAF50;
            border-color: #4CAF50;
        }

        .toggle-btn.active .toggle-switch {
            left: calc(100% - 23px);
        }

        /* Permission List Styling */
        .permission-list {
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            background-color: #fff;
            overflow: hidden;
        }

        .permission-item {
            padding: 0.75rem 1.5rem;
            border-bottom: 1px solid #e3e6f0;
            transition: background-color 0.2s ease;
        }

        .permission-item:hover {
            background-color: #f8f9fa;
        }

        .permission-item:last-child {
            border-bottom: none;
        }

        .permission-name {
            font-size: 1rem;
            color: #4a4a4a;
            flex-grow: 1;
        }

        /* Card Styling */
        .card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            font-weight: 600;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .permission-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .toggle-btn {
                margin-top: 0.5rem;
            }
        }
    </style>

    <script>
        function togglePermission(button) {
            let permissionId = button.getAttribute("data-permission-id");
            let roleId = button.getAttribute("data-role-id");
            let currentStatus = parseInt(button.getAttribute("data-status"), 10);
            let newStatus = currentStatus === 1 ? 0 : 1;

            fetch("{{ route('toggle.role.permission') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ permission_id: permissionId, role_id: roleId, status: newStatus })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        button.setAttribute("data-status", newStatus);
                        button.classList.toggle("active", newStatus === 1);
                    } else {
                        alert("Failed to update permission status");
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("An error occurred while updating the permission status.");
                });
        }
    </script>
@endsection
