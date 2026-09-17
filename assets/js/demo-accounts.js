// Frontend prototype only. This mapping is not authentication or authorization.
window.demoAccounts = {
    key: 'prismDemoAccountRoles',
    routes: { student: 'student.php', adviser: 'research_adviser.php', staff: 'dashboard.php' },
    normalize(email) { return email.trim().toLowerCase(); },
    read() {
        const accounts = JSON.parse(localStorage.getItem(this.key) || '{}');
        if (!accounts || typeof accounts !== 'object' || Array.isArray(accounts)) {
            throw new Error('Invalid account mapping');
        }
        return accounts;
    },
    route(role) {
        // Support both profile records and older role-only registrations.
        if (role && typeof role === 'object') role = role.role;
        // Keep previously registered prototype accounts on the renamed role.
        if (role === 'faculty') role = 'adviser';
        return Object.prototype.hasOwnProperty.call(this.routes, role) ? this.routes[role] : null;
    }
};
