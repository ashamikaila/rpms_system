const loginForm = document.getElementById('loginForm');
const loginError = document.getElementById('loginError');
loginForm.addEventListener('input', () => { loginError.hidden = true; });
loginForm.addEventListener('submit', (event) => {
    event.preventDefault();
    if (!loginForm.reportValidity()) return;
    try {
        const email = demoAccounts.normalize(document.getElementById('email').value);
        const accounts = demoAccounts.read();
        const role = Object.prototype.hasOwnProperty.call(accounts, email) ? accounts[email] : null;
        const route = demoAccounts.route(role);
        if (route) {
            sessionStorage.setItem('prismCurrentAccount', JSON.stringify({email, role: typeof role === 'object' ? role.role : role}));
            window.location.assign(route);
            return;
        }
        loginError.textContent = 'Account not found. RPMS staff can register; research advisers and students should contact the RPMS office for their login.';
    } catch (_) {
        loginError.textContent = 'Unable to read your category. Please enable browser storage and try again.';
    }
    loginError.hidden = false;
});
