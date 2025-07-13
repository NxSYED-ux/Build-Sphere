document.addEventListener("DOMContentLoaded", function() {
    // 1. Check for required elements
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const userMeta = document.querySelector('meta[name="user-id"]');
    const roleMeta = document.querySelector('meta[name="role-id"]');
    const superAdminMeta = document.querySelector('meta[name="is-super-admin"]');
    const isLinked = document.querySelector('meta[name="isLinked"]');

    const pusherKeyMeta = document.querySelector('meta[name="pusher-key"]');
    const pusherClusterMeta = document.querySelector('meta[name="pusher-cluster"]');

    if (!csrfMeta || !userMeta || !roleMeta || !superAdminMeta || !pusherKeyMeta || !isLinked) {
        return;
    }

    const isSuperAdmin = parseInt(superAdminMeta.content);
    window.roleId = roleMeta ? parseInt(roleMeta.content) : null;

    const linkedToOrg = parseInt(isLinked.content);
    window.linkedToOrganization = linkedToOrg === 1;

    if (isSuperAdmin === 1) {
        showAllPermissionBlocks();
        toggleVisibility(".switch-owner-portal-btn",  linkedToOrganization);
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

function applyPermissions() {
    const storedPermissions = JSON.parse(localStorage.getItem("userPermissions")) || {};

    const notOwnerAccess = !storedPermissions['Owner Portal'];
    const notAdminAccess = !storedPermissions['Admin Portal'];

    const match = window.location.pathname.toLowerCase().match(/^\/(owner|admin)\//);
    const currentPortal = match ? match[1] : null;
    const isAdminPortal = currentPortal === 'admin';
    const isOwnerPortal = currentPortal === 'owner';

    if ( notOwnerAccess && notAdminAccess) {
        window.location.href = window.loginRoute;
    }
    else if (isAdminPortal && notAdminAccess) {
        window.location.href = window.ownerRoute;
    }
    else if ( isOwnerPortal && notOwnerAccess) {
        window.location.href = window.adminRoute;
    }

    if (notOwnerAccess) {
        toggleVisibility(".switch-owner-portal-btn", false);
    }
    else if (notAdminAccess) {
        toggleVisibility(".switch-admin-portal-btn", false);
    }

    if (storedPermissions['Owner Portal']) {
        const hasBuildingAccess = ['Buildings', 'Levels', 'Units']
            .some(perm => storedPermissions['Owner Portal'].includes(perm));

        toggleVisibility("#OwnerBuildingss", hasBuildingAccess);
        toggleVisibility(".OwnerBuildings", storedPermissions['Owner Portal'].includes('Buildings'));
        toggleVisibility(".OwnerLevels", storedPermissions['Owner Portal'].includes('Levels'));
        toggleVisibility(".OwnerUnits", storedPermissions['Owner Portal'].includes('Units'));

        toggleVisibility(".Owner-Building-Add-Button", storedPermissions['Owner Portal'].includes('Add Building'));
        toggleVisibility(".Owner-Building-Edit-Button", storedPermissions['Owner Portal'].includes('Edit Building'));
        toggleVisibility(".Owner-Building-View-Details-Button", storedPermissions['Owner Portal'].includes('View Building Details'));
        toggleVisibility(".OwnerBuildingsTree", storedPermissions['Owner Portal'].includes('Building Tree'));
        toggleVisibility(".OwnerSubmitBuilding", storedPermissions['Owner Portal'].includes('Submit Building'));
        toggleVisibility(".OwnerRemindAdminBuilding", storedPermissions['Owner Portal'].includes('Remind Admin'));

        toggleVisibility(".Owner-Level-Add-Button", storedPermissions['Owner Portal'].includes('Add Level'));
        toggleVisibility(".Owner-Level-Edit-Button", storedPermissions['Owner Portal'].includes('Edit Level'));

        toggleVisibility(".Owner-Unit-Add-Button", storedPermissions['Owner Portal'].includes('Add Unit'));
        toggleVisibility(".Owner-Unit-Edit-Button", storedPermissions['Owner Portal'].includes('Edit Unit'));
        toggleVisibility(".Owner-Unit-View-Details-Button", storedPermissions['Owner Portal'].includes('View Unit Details'));

        // Other Sections
        toggleVisibility(".OwnerAssignUnits", storedPermissions['Owner Portal'].includes('Assign Units'));
        toggleVisibility(".OwnerPropertyUsers", storedPermissions['Owner Portal'].includes('Property Users'));
        toggleVisibility(".OwnerEditPropertyUsers", storedPermissions['Owner Portal'].includes('Edit Property User'));
        toggleVisibility(".OwnerViewDetailsPropertyUsers", storedPermissions['Owner Portal'].includes('View Details'));
        toggleVisibility(".OwnerDepartments", storedPermissions['Owner Portal'].includes('Departments'));
        toggleVisibility(".OwnerMemberships", storedPermissions['Owner Portal'].includes('Memberships'));
        toggleVisibility(".OwnerStaff", storedPermissions['Owner Portal'].includes('Staff'));
        toggleVisibility(".OwnerReports", storedPermissions['Owner Portal'].includes('Reports'));
        toggleVisibility(".OwnerFinance", storedPermissions['Owner Portal'].includes('Finance'));

        toggleVisibility(".switch-owner-portal-btn", linkedToOrganization);
    }

    if (storedPermissions['Admin Portal']) {
        const hasAdminControlAccess = ['User Management', 'Role Management', 'Manage Role Permissions', 'Dropdowns']
            .some(perm => storedPermissions['Admin Portal'].includes(perm));

        const hasBuildingAccess = ['Admin Buildings', 'Admin Levels', 'Admin Units']
            .some(perm => storedPermissions['Admin Portal'].includes(perm));

        toggleVisibility("#AdminControls", hasAdminControlAccess);
        toggleVisibility(".AdminUserManagement", storedPermissions['Admin Portal'].includes('User Management'));
        toggleVisibility(".AdminAddUser", storedPermissions['Admin Portal'].includes('Add User'));
        toggleVisibility(".AdminEditUser", storedPermissions['Admin Portal'].includes('Edit User'));

        toggleVisibility(".AdminRolesManagement", storedPermissions['Admin Portal'].includes('Role Management'));
        toggleVisibility(".AdminAddRoles", storedPermissions['Admin Portal'].includes('Add Role'));
        toggleVisibility(".AdminEditRoles", storedPermissions['Admin Portal'].includes('Edit Role'));
        toggleVisibility(".AdminDeleteRoles", storedPermissions['Admin Portal'].includes('Delete Role'));

        toggleVisibility(".AdminManageRolePermissions", storedPermissions['Admin Portal'].includes('Manage Role Permissions'));

        toggleVisibility(".AdminDropdowns", storedPermissions['Admin Portal'].includes('Dropdowns'));

        toggleVisibility("#AdminBuildingss", hasBuildingAccess);
        toggleVisibility(".AdminBuildings", storedPermissions['Admin Portal'].includes('Admin Buildings'));
        toggleVisibility(".AdminLevels", storedPermissions['Admin Portal'].includes('Admin Levels'));
        toggleVisibility(".AdminUnits", storedPermissions['Admin Portal'].includes('Admin Units'));

        toggleVisibility(".Admin-Building-Add-Button", storedPermissions['Admin Portal'].includes('Admin Add Building'));
        toggleVisibility(".Admin-Building-Edit-Button", storedPermissions['Admin Portal'].includes('Admin Edit Building'));
        toggleVisibility(".Admin-Building-Details-Button", storedPermissions['Admin Portal'].includes('Admin Building Details'));
        toggleVisibility(".Admin-Building-Accept-Button", storedPermissions['Admin Portal'].includes('Accept Building'));
        toggleVisibility(".Admin-Building-Reject-Button", storedPermissions['Admin Portal'].includes('Reject Building'));
        toggleVisibility(".Admin-Building-Report-Remarks-Button", storedPermissions['Admin Portal'].includes('Report Remarks'));

        toggleVisibility(".Admin-Level-Add-Button", storedPermissions['Admin Portal'].includes('Admin Add Level'));
        toggleVisibility(".Admin-Level-Edit-Button", storedPermissions['Admin Portal'].includes('Admin Edit Level'));
        toggleVisibility(".Admin-Unit-Add-Button", storedPermissions['Admin Portal'].includes('Admin Add Unit'));
        toggleVisibility(".Admin-Unit-Edit-Button", storedPermissions['Admin Portal'].includes('Admin Edit Unit'));

        // Other Sections
        toggleVisibility(".AdminOrganizations", storedPermissions['Admin Portal'].includes('Organization Management'));
        toggleVisibility(".AdminAddOrganizations", storedPermissions['Admin Portal'].includes('Add Organization'));
        toggleVisibility(".AdminEditOrganizations", storedPermissions['Admin Portal'].includes('Edit Organization'));
        toggleVisibility(".AdminViewOrganizationsDetails", storedPermissions['Admin Portal'].includes('View Organization Details'));
        toggleVisibility(".AdminUpgradeOrganizationsPlan", storedPermissions['Admin Portal'].includes('Upgrade Organization Plan'));
        toggleVisibility(".AdminRecordPlanPaymentOrganizationsPlan", storedPermissions['Admin Portal'].includes('Record Plan Payment'));

        toggleVisibility(".AdminPlans", storedPermissions['Admin Portal'].includes('Plans'));
        toggleVisibility(".AdminAddPlans", storedPermissions['Admin Portal'].includes('Add Plan'));
        toggleVisibility(".AdminEditPlans", storedPermissions['Admin Portal'].includes('Edit Plan'));
        toggleVisibility(".AdminViewPlansDetails", storedPermissions['Admin Portal'].includes('View Plan Details'));
        toggleVisibility(".AdminDeletePlan", storedPermissions['Admin Portal'].includes('Delete Plan'));


        toggleVisibility(".AdminFinance", storedPermissions['Admin Portal'].includes('Admin Finance'));
        toggleVisibility(".AdminTransactionDetails", storedPermissions['Admin Portal'].includes('Admin Transaction Details'));

        toggleVisibility(".switch-admin-portal-btn", true);
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
        "#OwnerBuildingss", ".OwnerBuildings", ".OwnerLevels", ".OwnerUnits",
        ".OwnerAssignUnits", ".OwnerPropertyUsers", ".OwnerEditPropertyUsers", ".OwnerViewDetailsPropertyUsers",
        ".Owner-Building-Add-Button", ".Owner-Building-Edit-Button", ".Owner-Building-View-Details-Button",
        ".OwnerBuildingsTree", ".OwnerSubmitBuilding", ".OwnerRemindAdminBuilding",
        ".Owner-Level-Add-Button", ".Owner-Level-Edit-Button", ".Owner-Unit-Add-Button", ".Owner-Unit-Edit-Button", ".Owner-Unit-View-Details-Button",
        ".OwnerDepartments", ".OwnerMemberships", ".OwnerStaff", ".OwnerReports",

        "#AdminControls", ".AdminUserManagement", ".AdminAddUser", ".AdminEditUser",
        ".AdminRolesManagement", ".AdminAddRoles", ".AdminEditRoles", ".AdminDeleteRoles", ".AdminManageRolePermissions", ".AdminDropdowns",
        "#AdminBuildingss", ".AdminBuildings", ".AdminLevels", ".AdminUnits",
        ".Admin-Building-Add-Button", ".Admin-Building-Edit-Button", "Admin-Building-Details-Button", ".Admin-Building-Accept-Button", ".Admin-Building-Reject-Button", ".Admin-Building-Report-Remarks-Button",
        ".Admin-Level-Add-Button", ".Admin-Level-Edit-Button", ".Admin-Unit-Add-Button", ".Admin-Unit-Edit-Button",
        ".AdminOrganizations", ".AdminAddOrganizations", ".AdminEditOrganizations", ".AdminViewOrganizationsDetails", ".AdminUpgradeOrganizationsPlan", ".AdminRecordPlanPaymentOrganizationsPlan",
        ".AdminPlans", ".AdminAddPlans", ".AdminEditPlans", ".AdminViewPlansDetails", ".AdminDeletePlan",
        ".AdminFinance", ".AdminTransactionDetails"
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
