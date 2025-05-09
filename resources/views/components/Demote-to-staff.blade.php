@push('styles')
    <style>
        :root {
            --primary-color: var(--color-blue);
            --primary-hover: var(--color-blue);
            --secondary-color: #f8f9fa;
            --border-color: #e2e8f0;
            --error-color: #e53e3e;
            --success-color: #38a169;
            --permission-header-bg: rgba(var(--color-blue), 0.05);
            --permission-card-bg: var(--body-background-color);
            --child-permission-indicator: var(--color-blue);
        }

        #demoteModel .accordion-item {
            background-color: transparent;
            border: 1px solid var(--border-color) !important;
            border-radius: 8px !important;
            margin-bottom: 12px;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        #demoteModel .accordion-button {
            background-color: var(--body-background-color);
            color: var(--sidenavbar-text-color);
            padding: 1rem 1.5rem;
            box-shadow: none;
        }

        #demoteModel .accordion-button:not(.collapsed) {
            background-color: var(--sidenavbar-body-color);
            color: var(--sidenavbar-text-color) !important;
            box-shadow: none;
        }

        #demoteModel .accordion-arrow {
            transition: transform 0.3s ease;
            color: var(--sidenavbar-text-color) !important;
            font-size: 1.1em;
        }

        #demoteModel .accordion-button:not(.collapsed) .accordion-arrow {
            transform: rotate(90deg);
            color: var(--sidenavbar-text-color) !important;
        }

        #demoteModel .accordion-button::after {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%236b7280'%3E%3Cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
            transition: transform 0.3s ease;
        }

        #demoteModel .accordion-button:not(.collapsed)::after {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%236b7280'%3E%3Cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
        }

        #demoteModel .accordion-body {
            padding: 1rem 1.5rem;
            background-color: var(--sidenavbar-body-color);
            color: var(--sidenavbar-text-color);
        }

        #demoteModel .permission-item-container {
            background-color: var(--body-background-color);
            border-radius: 8px;
            padding: 0;
            margin-bottom: 10px;
            border: 2px solid var(--border-color) !important;
            transition: all 0.2s ease;
        }

        #demoteModel .permission-toggle-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            background-color: var(--body-background-color);
            border-radius: 8px;
        }

        #demoteModel .child-permission{
            border-radius: 0 !important;
        }

        #demoteModel .child-permission:last-child{
            border-radius: 8px !important;
        }

        #demoteModel .permission-label {
            font-weight: 600;
            color: var(--sidenavbar-text-color);
            margin-bottom: 0;
            display: flex;
            align-items: center;
        }

        #demoteModel .child-permission .permission-label {
            font-weight: 400;
        }

        #demoteModel .permission-icon {
            margin-right: 10px;
            color: var(--sidenavbar-text-color);
            font-size: 1.1em;
        }

        #demoteModel .child-permission .permission-icon {
            color: var(--sidenavbar-text-color);
            font-size: 1.1em;
        }

        #demoteModel .child-permissions .permission-toggle-container {
            padding: 10px 16px;
            background-color: var(--body-background-color);
        }

        #demoteModel .form-switch .form-check-input {
            width: 2.8em;
            height: 1.5em;
            cursor: pointer;
            background-color: var(--border-color);
            border-color: var(--border-color);
        }

        #demoteModel .accordion-header{
            color: var(--sidenavbar-text-color);
            font-size: 20px;
        }

        #demoteModel .form-switch .form-check-input:checked {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }
    </style>
@endpush

{{--<button class="btn btn-primary demote-btn" data-staff-id="">Demote To Staff</button>--}}
<div class="modal fade" id="demoteModel" tabindex="-1" role="dialog" aria-hidden="true"></div>


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            // Handle promote button clicks
            document.querySelectorAll('.demote-btn').forEach(button => {
                button.addEventListener('click', handlePromoteClick);
            });

            async function handlePromoteClick(e) {
                e.preventDefault();
                const staffId = this.dataset.staffId;

                try {
                    const response = await fetch(`{{ route('owner.managers.promote.index', ':id') }}`.replace(':id', staffId), {
                        method: "GET",
                        headers: {
                            "X-Requested-With": "XMLHttpRequest",
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": csrfToken
                        }
                    });

                    if (!response.ok) {
                        const error = await response.json();

                        // Check for plan upgrade error
                        if (error.plan_upgrade_error) {
                            Swal.fire({
                                title: 'Upgrade Plan Error',
                                text: error.plan_upgrade_error || error.error || 'Request failed',
                                icon: 'error',
                                background: 'var(--body-background-color)',
                                color: 'var(--sidenavbar-text-color)',
                                showConfirmButton: true,
                                confirmButtonText: 'Upgrade Plan',
                                confirmButtonColor: '#3085d6',
                                showCancelButton: true,
                                allowOutsideClick: true,
                                allowEscapeKey: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "{{ route('owner.plan.upgrade.index') }}";
                                }
                            });
                        } else {
                            throw new Error(error.error || 'Request failed');
                        }
                        return;
                    }

                    const data = await response.json();
                    const modal = createdemoteModel(data);
                    document.body.appendChild(modal);

                    const bootstrapModal = new bootstrap.Modal(modal);
                    bootstrapModal.show();

                    // Setup modal events after it's shown
                    setupModalEvents(modal);

                    modal.addEventListener('hidden.bs.modal', () => {
                        document.body.removeChild(modal);
                    });
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message,
                        confirmButtonColor: '#3085d6',
                    });
                }
            }

            function createdemoteModel(data) {
                const modal = document.createElement('div');
                modal.className = 'modal fade';
                modal.id = 'demoteModel';
                modal.tabIndex = '-1';
                modal.setAttribute('aria-hidden', 'true');

                const staff = data.staffInfo?.user || {};
                const staffName = staff.name || 'Staff Member';
                const staffEmail = staff.email || 'No email';
                const staffPicture = staff.picture ? "{{ asset('/') }}" + staff.picture : "{{ asset('assets/placeholder-profile.png') }}";
                const currentBuildingId = data.staffInfo?.building_id || '';
                const currentDepartmentId = data.staffInfo?.department_id || '';

                modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: var(--body-card-bg) !important;">
                        <h5 class="modal-title" style="color: var(--sidenavbar-text-color);">Demotion ${staffName}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="background-color: var(--body-card-bg) !important;">
                        <div class="staff-info-section mb-4 p-3 border rounded">
                            <div class="d-flex align-items-center">
                                <img src="${staffPicture}" alt="${staffName}"
                                     class="member-avatar rounded-circle me-3"
                                     style="width: 80px; height: 80px; object-fit: cover;">
                                <div>
                                    <h5  style="color: var(--sidenavbar-text-color);">${staffName}</h5>
                                    <p class="mb-1"  style="color: var(--sidenavbar-text-color);">${staffEmail}</p>
                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                        <i class='bx bx-user me-1'></i>Manager
                                    </span>
                                </div>
                            </div>
                        </div>

                        <form id="promotionForm">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <input type="hidden" name="manager_id" value="${data.staffInfo?.id || ''}">

                            <div class="row mb-1">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Department</label>
                                        ${renderDepartments(data.departments || [], currentDepartmentId)}
                                        <div class="form-text" style="color: var(--sidenavbar-text-color);">Select the department this staff will oversee</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Building</label>
                                        ${renderBuildings(data.buildings || [], currentBuildingId)}
                                        <div class="form-text" style="color: var(--sidenavbar-text-color);">Select the building this staff will oversee</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h6 class="mb-3">Manager Permissions</h6>
                                ${renderPermissions(data.permissions || [])}
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer" style="background-color: var(--body-card-bg) !important;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="submitPromotion">Demote to Staff Member</button>
                    </div>
                </div>
            </div>
        `;

                return modal;
            }

            function renderDepartments(departments, currentDepartmentId) {
                if (!departments || Object.keys(departments).length === 0) {
                    return '<select class="form-select" name="department_id" disabled><option>No departments available</option></select>';
                }

                let options = Object.entries(departments).map(([id, name]) =>
                    `<option value="${id}" ${id == currentDepartmentId ? 'selected' : ''}>${name}</option>`
                ).join('');

                return `
            <select class="form-select" name="department_id" required>
                <option value="">Select Department</option>
                ${options}
            </select>
        `;
            }

            function renderBuildings(buildings, currentBuildingId) {
                if (!buildings || Object.keys(buildings).length === 0) {
                    return '<select class="form-select" name="building_id" disabled><option>No buildings available</option></select>';
                }

                let options = Object.entries(buildings).map(([id, name]) =>
                    `<option value="${id}" ${id == currentBuildingId ? 'selected' : ''}>${name}</option>`
                ).join('');

                return `
            <select class="form-select" name="building_id" required>
                <option value="">Select Building</option>
                ${options}
            </select>
        `;
            }

            function renderPermissions(permissions) {
                if (!permissions.length) return '<div class="text-center fw-bold">No permissions found.</div>';

                const grouped = permissions.reduce((acc, perm) => {
                    const header = perm.permission?.header || 'General Permissions';
                    if (!acc[header]) acc[header] = [];
                    acc[header].push(perm);
                    return acc;
                }, {});

                return `
            <div class="accordion" id="permissionsAccordion">
                ${Object.entries(grouped).map(([header, perms], index) => `
                    <div class="accordion-item mb-3">
                        <h6 class="accordion-header" id="heading${index}">
                            <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapse${index}"
                                    aria-expanded="false" aria-controls="collapse${index}">
                                <div class="d-flex align-items-center w-100">
                                    <i class='bx bx-category-alt me-2'></i>
                                    <span class="fw-semibold">${header}</span>
                                    <span class="badge bg-primary bg-opacity-10 text-primary ms-auto me-2">
                                        ${perms.length} ${perms.length === 1 ? 'permission' : 'permissions'}
                                    </span>
                                </div>
                            </button>
                        </h6>
                        <div id="collapse${index}" class="accordion-collapse collapse"
                             aria-labelledby="heading${index}" data-bs-parent="#permissionsAccordion">
                            <div class="accordion-body pt-2">
                                <div class="row">
                                    ${renderPermissionGroup(perms)}
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;
            }

            function renderPermissionGroup(permissions) {
                const parents = permissions.filter(p => !p.permission?.parent_id);

                return parents.map(permission => {
                    const children = permissions.filter(
                        p => p.permission?.parent_id === permission.permission_id
                    );

                    return `
                <div class="col-sm-12 mb-3 parent-permission">
                    <div class="permission-item-container">
                        <div class="permission-toggle-container border-bottom ${children.length > 0 ? 'rounded-bottom-0' : 'rounded-bottom'}">
                            <label class="permission-label">
                                <i class='bx bxs-check-circle permission-icon'></i>
                                ${permission.permission?.name || 'Permission'}
                            </label>
                            <div class="form-check form-switch">
                                <input type="hidden" name="permissions[${permission.permission_id}]" value="0">
                                <input class="form-check-input permission-toggle parent-toggle"
                                       type="checkbox"
                                       name="permissions[${permission.permission_id}]"
                                       value="1"
                                       data-permission-id="${permission.permission_id}"
                                       ${permission.status ? 'checked' : ''}>
                            </div>
                        </div>
                        ${children.length ? renderChildPermissions(children, permission) : ''}
                    </div>
                </div>
            `;
                }).join('');
            }

            function renderChildPermissions(children, parentPermission) {
                return `
            <div class="child-permissions">
                ${children.map(child => `
                    <div class="permission-toggle-container child-permission">
                        <label class="permission-label">
                            <i class='bx bx-radio-circle permission-icon'></i>
                            ${child.permission?.name || 'Child Permission'}
                        </label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="permissions[${child.permission_id}]" value="0">
                            <input class="form-check-input permission-toggle child-toggle"
                                   type="checkbox"
                                   name="permissions[${child.permission_id}]"
                                   value="1"
                                   data-parent-id="${parentPermission.permission_id}"
                                   ${parentPermission.status && child.status ? 'checked' : ''}
                                   ${!parentPermission.status ? 'disabled' : ''}>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;
            }

            function setupModalEvents(modal) {
                // Parent-child permission logic
                modal.querySelectorAll('.parent-toggle').forEach(toggle => {
                    const permissionId = toggle.dataset.permissionId;
                    const children = modal.querySelectorAll(`.child-toggle[data-parent-id="${permissionId}"]`);

                    toggle.addEventListener('change', function() {
                        children.forEach(child => {
                            child.checked = this.checked;
                            child.disabled = !this.checked;
                        });
                    });

                    // Initialize child states
                    children.forEach(child => {
                        child.disabled = !toggle.checked;

                        child.addEventListener('change', function() {
                            if (this.checked && !toggle.checked) {
                                this.checked = false;
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Permission Conflict',
                                    text: 'Please enable the parent permission first.',
                                    confirmButtonColor: '#3085d6',
                                });
                            }
                        });
                    });
                });

                // Form submission
                modal.querySelector('#submitPromotion').addEventListener('click', async () => {
                    const form = modal.querySelector('#promotionForm');
                    const submitBtn = modal.querySelector('#submitPromotion');

                    try {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';


                        const formData = new FormData(form);

                        // Convert permissions object to array format expected by backend
                        const permissions = {};
                        form.querySelectorAll('input[name^="permissions["]').forEach(input => {
                            if (input.type === 'checkbox') {
                                const key = input.name.match(/\[(.*?)\]/)[1];
                                permissions[key] = input.checked ? 1 : 0;
                            }
                        });

                        // Add permissions to form data
                        formData.delete('permissions');
                        for (const [key, value] of Object.entries(permissions)) {
                            formData.append(`permissions[${key}]`, value);
                        }

                        const response = await fetch(`{{ route('owner.managers.promote.store') }}`, {
                            method: "POST",
                            body: formData,
                            headers: {
                                "X-Requested-With": "XMLHttpRequest",
                                "Accept": "application/json"
                            }
                        });

                        if (!response.ok) {
                            const error = await response.json();
                            throw new Error(error.error || error.message || 'Failed to promote staff');
                        }

                        const result = await response.json();

                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: result.message || 'Staff promoted successfully!',
                            confirmButtonColor: '#3085d6',
                        }).then(() => {
                            const bsModal = bootstrap.Modal.getInstance(modal);
                            bsModal.hide();
                            window.location.reload();
                        });
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message,
                            confirmButtonColor: '#3085d6',
                        });
                    } finally {
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Promote to Manager';
                    }
                });
            }
        });
    </script>
@endpush

