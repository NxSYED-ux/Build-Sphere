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

        #promotionModal .accordion-item {
            background-color: transparent;
            border: 1px solid var(--border-color) !important;
            border-radius: 8px !important;
            margin-bottom: 12px;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        #promotionModal .accordion-button {
            background-color: var(--body-background-color);
            color: var(--sidenavbar-text-color);
            padding: 1rem 1.5rem;
            box-shadow: none;
        }

        #promotionModal .accordion-button:not(.collapsed) {
            background-color: var(--sidenavbar-body-color);
            color: var(--sidenavbar-text-color) !important;
            box-shadow: none;
        }

        #promotionModal .accordion-arrow {
            transition: transform 0.3s ease;
            color: var(--sidenavbar-text-color) !important;
            font-size: 1.1em;
        }

        #promotionModal .accordion-button:not(.collapsed) .accordion-arrow {
            transform: rotate(90deg);
            color: var(--sidenavbar-text-color) !important;
        }

        #promotionModal .accordion-button::after {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%236b7280'%3E%3Cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
            transition: transform 0.3s ease;
        }

        #promotionModal .accordion-button:not(.collapsed)::after {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%236b7280'%3E%3Cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
        }

        #promotionModal .accordion-body {
            padding: 1rem 1.5rem;
            background-color: var(--sidenavbar-body-color);
            color: var(--sidenavbar-text-color);
        }

        #promotionModal .permission-item-container {
            background-color: var(--body-background-color);
            border-radius: 8px;
            padding: 0;
            margin-bottom: 10px;
            border: 2px solid var(--border-color) !important;
            transition: all 0.2s ease;
        }

        #promotionModal .permission-toggle-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            background-color: var(--body-background-color);
            border-radius: 8px;
        }

        #promotionModal .child-permission{
            border-radius: 0 !important;
        }

        #promotionModal .child-permission:last-child{
            border-radius: 8px !important;
        }

        #promotionModal .permission-label {
            font-weight: 600;
            color: var(--sidenavbar-text-color);
            margin-bottom: 0;
            display: flex;
            align-items: center;
        }

        #promotionModal .child-permission .permission-label {
            font-weight: 400;
        }

        #promotionModal .permission-icon {
            margin-right: 10px;
            color: var(--sidenavbar-text-color);
            font-size: 1.1em;
        }

        #promotionModal .child-permission .permission-icon {
            color: var(--sidenavbar-text-color);
            font-size: 1.1em;
        }

        #promotionModal .child-permissions .permission-toggle-container {
            padding: 10px 16px;
            background-color: var(--body-background-color);
        }

        #promotionModal .form-switch .form-check-input {
            width: 2.8em;
            height: 1.5em;
            cursor: pointer;
            background-color: var(--border-color);
            border-color: var(--border-color);
        }

        #promotionModal .accordion-header{
            color: var(--sidenavbar-text-color);
            font-size: 20px;
        }

        #promotionModal .form-switch .form-check-input:checked {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }
    </style>
@endpush

{{--<button class="btn btn-primary promote-btn" data-staff-id="">Promote to Manager</button>--}}
<div class="modal fade" id="promotionModal" tabindex="-1" role="dialog" aria-hidden="true"></div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            // Handle promote button clicks
            document.querySelectorAll('.promote-btn').forEach(button => {
                button.addEventListener('click', handlePromoteClick);
            });

            async function handlePromoteClick(e) {
                e.preventDefault();
                const staffId = this.dataset.staffId;

                try {
                    const response = await fetch(`{{ route('owner.staff.promote.index', ':id') }}`.replace(':id', staffId), {
                        method: "GET",
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
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
                    const modal = createPromotionModal(data);
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
                        background: 'var(--body-background-color)',
                        color: 'var(--sidenavbar-text-color)',
                    });
                }
            }

            function createPromotionModal(data) {
                const modal = document.createElement('div');
                modal.className = 'modal fade';
                modal.id = 'promotionModal';
                modal.tabIndex = '-1';
                modal.setAttribute('aria-hidden', 'true');

                const staff = data.staffInfo?.user || {};
                const staffName = staff.name || 'Staff Member';
                const staffEmail = staff.email || 'No email';
                const staffPicture = staff.picture ? "{{ asset('/') }}" + staff.picture : "{{ asset('img/placeholder-profile.png') }}";
                const currentBuildingId = data.staffInfo?.building_id || '';

                modal.innerHTML = `
                    <div class="modal-dialog modal-lg" >
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: var(--body-card-bg) !important;">
                                <h5 class="modal-title">Promote ${staffName}</h5>
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
                                                <i class='bx bx-user me-1'></i>Staff Member
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <form id="promotionForm">
                                    <input type="hidden" name="_token" value="${csrfToken}">
                                    <input type="hidden" name="staff_id" value="${data.staffInfo?.id || ''}">

                                    <div class="mb-4">
                                        <h6 class="mb-3">Buildings to Manage</h6>
                                        <div class="row">
                                            ${renderBuildings(data.buildings || [], currentBuildingId)}
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
                                <button type="button" class="btn btn-primary" id="submitPromotion">Promote to Manager</button>
                            </div>
                        </div>
                    </div>
                `;

                return modal;
            }

            function renderBuildings(buildings, currentBuildingId) {
                if (!buildings.length) return '<p class="col-12">No buildings available</p>';

                return buildings.map(building => `
                    <div class="col-md-4 col-lg-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   name="buildings[]"
                                   value="${building.id}"
                                   id="building-${building.id}"
                                   ${building.id == currentBuildingId ? 'checked' : ''}>
                            <label class="form-check-label" for="building-${building.id}">
                                ${building.name || 'Unnamed Building'}
                            </label>
                        </div>
                    </div>
                `).join('');
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
                                            <span class="fw-bold">${header}</span>
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

                    toggle.addEventListener('change', function () {
                        children.forEach(child => {
                            child.checked = this.checked;
                            child.disabled = !this.checked;
                        });
                    });

                    // Initialize child states
                    children.forEach(child => {
                        child.disabled = !toggle.checked;

                        child.addEventListener('change', function () {
                            if (this.checked && !toggle.checked) {
                                this.checked = false;
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Permission Conflict',
                                    text: 'Please enable the parent permission first.',
                                    confirmButtonColor: '#3085d6',
                                    background: 'var(--body-background-color)',
                                    color: 'var(--sidenavbar-text-color)',
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

                        // Validate at least one building is selected
                        const buildingCheckboxes = form.querySelectorAll('input[name="buildings[]"]:checked');
                        if (buildingCheckboxes.length === 0) {
                            throw new Error('Please select at least one building');
                        }

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

                        const response = await fetch(`{{ route('owner.staff.promote.store') }}`, {
                            method: "POST",
                            body: formData,
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
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
                            window.location.href = '{{ route("owner.managers.index") }}';
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
