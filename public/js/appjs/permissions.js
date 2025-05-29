document.addEventListener("DOMContentLoaded", function() {
    // 1. Check for required elements
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const userMeta = document.querySelector('meta[name="user-id"]');
    const roleMeta = document.querySelector('meta[name="role-id"]');
    const superAdminMeta = document.querySelector('meta[name="is-super-admin"]');

    const pusherKeyMeta = document.querySelector('meta[name="pusher-key"]');
    const pusherClusterMeta = document.querySelector('meta[name="pusher-cluster"]');

    if (!csrfMeta || !userMeta || !roleMeta || !superAdminMeta || !pusherKeyMeta) {
        return;
    }

    const isSuperAdmin = parseInt(superAdminMeta.content);

    if (isSuperAdmin === 1) {
        showAllPermissionBlocks();
        return;
    }

    // 2. Load initial permissions
    const sessionPermissions = window.initialPermissions || {};

    if (Object.keys(sessionPermissions).length > 0) {
        localStorage.setItem("userPermissions", JSON.stringify(sessionPermissions));
    }

    // 3. Apply initial permissions
    applyPermissions();

    // 4. Initialize Pusher if configured
    const pusherKey = pusherKeyMeta.content;
    const pusherCluster = pusherClusterMeta?.content || 'ap2';

    if (pusherKey) {
        initializePusher(csrfMeta.content, userMeta.content, roleMeta.content, pusherKey, pusherCluster);
    } else {
        console.warn("User menu error 1");
    }

    // 5. Watch for cross-tab updates
    watchLocalStorage();
});

function initializePusher(csrfToken, userId, roleId, pusherKey, pusherCluster) {
    const pusher = new Pusher(pusherKey, {
        cluster: pusherCluster || 'ap2',
        encrypted: true,
        authEndpoint: '/pusher/auth',
        auth: {
            headers: {
                'X-CSRF-Token': csrfToken
            }
        }
    });

    const userChannelName = `private-userPermissions.${userId}`;
    const userChannel = pusher.subscribe(userChannelName);

    userChannel.bind('pusher:subscription_error', (err) => {
        console.error("UserMenu error 2:", err);
    });

    userChannel.bind('App\\Events\\UserPermissionUpdated', function(data) {
        if (data.permissionsList) {
            localStorage.setItem("userPermissions", JSON.stringify(data.permissionsList));
            applyPermissions();
        }
    });

    const roleChannelName = `private-rolePermissions.${roleId}`;
    const roleChannel = pusher.subscribe(roleChannelName);

    roleChannel.bind('pusher:subscription_error', (err) => {
        console.error("RoleMenu error 1:", err);
    });

    roleChannel.bind('App\\Events\\RolePermissionUpdated', function(data) {
        handleRolePermissionUpdate(data);
    });
}

function handleRolePermissionUpdate(data) {
    const storedPermissions = JSON.parse(localStorage.getItem("userPermissions")) || {};

    if (!storedPermissions[data.permissionHeader]) {
        storedPermissions[data.permissionHeader] = [];
    }

    if (data.permissionStatus === 0) {
        storedPermissions[data.permissionHeader] = storedPermissions[data.permissionHeader].filter(
            perm => perm !== data.permissionName
        );

        if (storedPermissions[data.permissionHeader].length === 0) {
            delete storedPermissions[data.permissionHeader];
        }
    } else if (data.permissionStatus === 1) {
        if (!storedPermissions[data.permissionHeader].includes(data.permissionName)) {
            storedPermissions[data.permissionHeader].push(data.permissionName);
        }
    }
    localStorage.setItem("userPermissions", JSON.stringify(storedPermissions));

    applyPermissions();
}

function applyPermissions() {
    const storedPermissions = JSON.parse(localStorage.getItem("userPermissions")) || {};

    const toggleVisibility = (selector, shouldShow) => {
        document.querySelectorAll(selector).forEach(el => {
            if (shouldShow) {
                el.classList.remove('hidden');
            } else {
                el.classList.add('hidden');
            }
        });
        if (selector === '.switch-admin-portal-btn') {
            document.body.classList.toggle('admin-portal-visible', shouldShow);
        }
    };

    if (storedPermissions['Owner Portal']) {
        const hasBuildingAccess = ['Owner Buildings', 'Owner Levels', 'Owner Units']
            .some(perm => storedPermissions['Owner Portal'].includes(perm));

        toggleVisibility("#OwnerBuildingss", hasBuildingAccess);
        toggleVisibility("#OwnerBuildings", storedPermissions['Owner Portal'].includes('Owner Buildings'));
        toggleVisibility("#OwnerLevels", storedPermissions['Owner Portal'].includes('Owner Levels'));
        toggleVisibility("#OwnerUnits", storedPermissions['Owner Portal'].includes('Owner Units'));

        toggleVisibility(".Owner-Building-Add-Button", storedPermissions['Owner Portal'].includes('Owner Add Building'));
        toggleVisibility(".Owner-Building-Edit-Button", storedPermissions['Owner Portal'].includes('Owner Edit Building'));
        toggleVisibility(".Owner-Level-Add-Button", storedPermissions['Owner Portal'].includes('Owner Add Level'));
        toggleVisibility(".Owner-Level-Edit-Button", storedPermissions['Owner Portal'].includes('Owner Edit Level'));
        toggleVisibility(".Owner-Unit-Add-Button", storedPermissions['Owner Portal'].includes('Owner Add Unit'));
        toggleVisibility(".Owner-Unit-Edit-Button", storedPermissions['Owner Portal'].includes('Owner Edit Unit'));

        // Other Sections
        toggleVisibility("#OwnerAssignUnits", storedPermissions['Owner Portal'].includes('Owner Assign Units'));
        toggleVisibility("#OwnerBuildingsTree", storedPermissions['Owner Portal'].includes('Owner Buildings Tree'));
        toggleVisibility("#OwnerRentals", storedPermissions['Owner Portal'].includes('Owner User Units'));
        toggleVisibility("#OwnerDepartments", storedPermissions['Owner Portal'].includes('Owner Departments'));
        toggleVisibility("#OwnerMemberships", storedPermissions['Owner Portal'].includes('Owner Memberships'));
        toggleVisibility("#OwnerStaff", storedPermissions['Owner Portal'].includes('Owner Staff'));
        toggleVisibility("#OwnerReports", storedPermissions['Owner Portal'].includes('Owner Reports'));

        toggleVisibility(".switch-owner-portal-btn", storedPermissions['Owner Portal'].length > 0);
    }

    if (storedPermissions['Admin Portal']) {
        const hasAdminControlAccess = ['User Management', 'User Roles', 'Role Permissions', 'Dropdowns']
            .some(perm => storedPermissions['Admin Portal'].includes(perm));

        const hasBuildingAccess = ['Admin Buildings', 'Admin Levels', 'Admin Units']
            .some(perm => storedPermissions['Admin Portal'].includes(perm));

        toggleVisibility("#AdminControls", hasAdminControlAccess);
        toggleVisibility("#AdminUserManagement", storedPermissions['Admin Portal'].includes('User Management'));
        toggleVisibility("#AdminUserRoles", storedPermissions['Admin Portal'].includes('User Roles'));
        toggleVisibility("#AdminRolePermissions", storedPermissions['Admin Portal'].includes('Role Permissions'));
        toggleVisibility("#AdminDropdowns", storedPermissions['Admin Portal'].includes('Dropdowns'));

        toggleVisibility("#AdminBuildingss", hasBuildingAccess);
        toggleVisibility("#AdminBuildings", storedPermissions['Admin Portal'].includes('Admin Buildings'));
        toggleVisibility("#AdminLevels", storedPermissions['Admin Portal'].includes('Admin Levels'));
        toggleVisibility("#AdminUnits", storedPermissions['Admin Portal'].includes('Admin Units'));

        toggleVisibility(".Admin-Building-Add-Button", storedPermissions['Admin Portal'].includes('Admin Add Building'));
        toggleVisibility(".Admin-Building-Edit-Button", storedPermissions['Admin Portal'].includes('Admin Edit Building'));
        toggleVisibility(".Admin-Level-Add-Button", storedPermissions['Admin Portal'].includes('Admin Add Level'));
        toggleVisibility(".Admin-Level-Edit-Button", storedPermissions['Admin Portal'].includes('Admin Edit Level'));
        toggleVisibility(".Admin-Unit-Add-Button", storedPermissions['Admin Portal'].includes('Admin Add Unit'));
        toggleVisibility(".Admin-Unit-Edit-Button", storedPermissions['Admin Portal'].includes('Admin Edit Unit'));

        // Other Sections
        toggleVisibility("#AdminOrganizations", storedPermissions['Admin Portal'].includes('Organizations'));
        toggleVisibility("#AdminReports", storedPermissions['Admin Portal'].includes('Admin Reports'));

        toggleVisibility(".switch-admin-portal-btn", storedPermissions['Admin Portal'].length > 0);
    }
}

function watchLocalStorage() {
    window.addEventListener("storage", (event) => {
        if (event.key === "userPermissions") {
            applyPermissions();
        }
    });
}

function showAllPermissionBlocks() {
    const allSelectors = [
        ".switch-admin-portal-btn",
        "#OwnerBuildingss", "#OwnerBuildings", "#OwnerLevels", "#OwnerUnits",
        "#OwnerAssignUnits", "#OwnerBuildingsTree", "#OwnerRentals", ".Owner-Building-Add-Button", ".Owner-Building-Edit-Button", ".Owner-Level-Add-Button", ".Owner-Level-Edit-Button", ".Owner-Unit-Add-Button", ".Owner-Unit-Edit-Button",
        "#OwnerDepartments", "#OwnerMemberships", "#OwnerStaff", "#OwnerReports",

        ".switch-owner-portal-btn",
        "#AdminControls", "#AdminUserManagement", "#AdminUserRoles", "#AdminRolePermissions", "#AdminDropdowns",
        "#AdminBuildingss", "#AdminBuildings", "#AdminLevels", "#AdminUnits",
        ".Admin-Building-Add-Button", ".Admin-Building-Edit-Button", ".Admin-Level-Add-Button", ".Admin-Level-Edit-Button", ".Admin-Unit-Add-Button", ".Admin-Unit-Edit-Button",
        "#AdminOrganizations", "#AdminReports"
    ];

    allSelectors.forEach(selector => {
        if (selector.startsWith('.')) {
            document.querySelectorAll(selector).forEach(el => {
                el.classList.remove('hidden');
            });
        } else {
            const el = document.querySelector(selector);
            if (el) el.classList.remove('hidden');
        }
    });
}
