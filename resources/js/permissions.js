document.addEventListener("DOMContentLoaded", function() {
    // 1. Check for required elements
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const userMeta = document.querySelector('meta[name="user-id"]');
    const roleMeta = document.querySelector('meta[name="role-id"]');

    if (!csrfMeta || !userMeta || !roleMeta) {
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
    if (import.meta.env.VITE_PUSHER_APP_KEY) {
        initializePusher(csrfMeta.content, userMeta.content, roleMeta.content);
    } else {
        console.warn("User menu error 1");
    }

    // 5. Watch for cross-tab updates
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

    const userChannelName = `private-userPermissions.${userId}`;

    const userChannel = pusher.subscribe(userChannelName);

    userChannel.bind('pusher:subscription_error', (err) => {
        console.error("Usermenu error 2:", err);
    });

    userChannel.bind('App\\Events\\UserPermissionUpdated', function(data) {

        if (data.permissionsList) {
            localStorage.setItem("userPermissions", JSON.stringify(data.permissionsList));
            applyPermissions();
        }
    });

    const roleChannelName = `private-rolePermissions.${roleId}`;

    const roleChannel = pusher.subscribe(roleChannelName);

    roleChannel.bind('pusher:subscription_succeeded', () => {
        console.log("[Permissions] Successfully subscribed to channel");
    });


    roleChannel.bind('pusher:subscription_error', (err) => {
        console.error("Rolemenu error 1:", err);
    });

    roleChannel.bind('App\\Events\\RolePermissionUpdated', function(data) {

         alert('Role permissions' + data);
    });
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
