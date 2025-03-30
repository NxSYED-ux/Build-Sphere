document.addEventListener("DOMContentLoaded", function() {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const userMeta = document.querySelector('meta[name="user-id"]');
    const roleMeta = document.querySelector('meta[name="role-id"]');

    if (!csrfMeta || !userMeta || !roleMeta) {
        return;
    }

    const sessionPermissions = window.initialPermissions || {};

    if (Object.keys(sessionPermissions).length > 0) {
        localStorage.setItem("userPermissions", JSON.stringify(sessionPermissions));
    }

    applyPermissions();

    if (import.meta.env.VITE_PUSHER_APP_KEY) {
        initializePusher(csrfMeta.content, userMeta.content, roleMeta.content);
    } else {
        console.warn("Menu error");
    }

    watchLocalStorage();
});

function initializePusher(csrfToken, userId, roleId) {

    const pusher = new Pusher(import.meta.env.VITE_PUSHER_APP_KEY, {
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'ap2',
        encrypted: true,
        authEndpoint: '/pusher/auth',
        auth: {
            headers: {
                'X-CSRF-Token': csrfToken
            }
        }
    });

    // User Permission Channel
    const userChannelName = `private-userPermissions.${userId}`;
    const userChannel = pusher.subscribe(userChannelName);

    userChannel.bind('pusher:subscription_error', (err) => {
        console.error("Error in User Menu : ", err);
    });

    userChannel.bind('App\\Events\\UserPermissionUpdated', function(data) {
        if (data.permissionsList) {
            localStorage.setItem("userPermissions", JSON.stringify(data.permissionsList));
            applyPermissions();
        }
    });

    // Role Permission Channel
    const roleChannelName = `private-rolePermissions.${roleId}`;
    const roleChannel = pusher.subscribe(roleChannelName);

    roleChannel.bind('pusher:subscription_error', (err) => {
        console.error("Error in Role Menu : ", err);
    });

    roleChannel.bind('App\\Events\\RolePermissionUpdated', function(data) {
        handleRolePermissionUpdate(data);
    });
}

function handleRolePermissionUpdate(data) {
    const storedPermissions = JSON.parse(localStorage.getItem("userPermissions")) || {};

    if (data.permissionStatus === 0) {
        storedPermissions[data.permissionHeader] = storedPermissions[data.permissionHeader].filter(
            perm => perm !== data.permissionName
        );

        if (storedPermissions[data.permissionHeader].length === 0) {
            delete storedPermissions[data.permissionHeader];
        }
    } else if (data.permissionStatus === 1) {
        if (!storedPermissions[data.permissionHeader]) {
            storedPermissions[data.permissionHeader] = [];
        }

        if (!storedPermissions[data.permissionHeader].includes(data.permissionName)) {
            storedPermissions[data.permissionHeader].push(data.permissionName);
        }
    }

    localStorage.setItem("userPermissions", JSON.stringify(storedPermissions));

    applyPermissions();
}

function applyPermissions() {
    const storedPermissions = JSON.parse(localStorage.getItem("userPermissions")) || {};

    const safeApply = (elementId, shouldShow) => {
        const element = document.getElementById(elementId);
        if (element) {
            element.style.display = shouldShow ? "block" : "none";
        }
    };

    if (storedPermissions['Owner Portal']) {
        const hasBuildingAccess = ['Owner Buildings', 'Owner Levels', 'Owner Units']
            .some(perm => storedPermissions['Owner Portal'].includes(perm));

        safeApply("OwnerBuildingss", hasBuildingAccess);
        safeApply("OwnerBuildings", storedPermissions['Owner Portal'].includes('Owner Buildings'));
        safeApply("OwnerLevels", storedPermissions['Owner Portal'].includes('Owner Levels'));
        safeApply("OwnerUnits", storedPermissions['Owner Portal'].includes('Owner Units'));

        // Other Sections
        safeApply("OwnerAssignUnits", storedPermissions['Owner Portal'].includes('Owner Assign Units'));
        safeApply("OwnerBuildingsTree", storedPermissions['Owner Portal'].includes('Owner Buildings Tree'));
        safeApply("OwnerRentals", storedPermissions['Owner Portal'].includes('Owner User Units'));
        safeApply("OwnerDepartments", storedPermissions['Owner Portal'].includes('Owner Departments'));
        safeApply("OwnerMemberships", storedPermissions['Owner Portal'].includes('Owner Memberships'));
        safeApply("OwnerStaff", storedPermissions['Owner Portal'].includes('Owner Staff'));
        safeApply("OwnerReports", storedPermissions['Owner Portal'].includes('Owner Reports'));
    }
}

function watchLocalStorage() {
    window.addEventListener("storage", (event) => {
        if (event.key === "userPermissions") {
            applyPermissions();
        }
    });
}
