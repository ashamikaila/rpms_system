const registerForm = document.getElementById('registerForm');
const password = document.getElementById('password');
const confirmation = document.getElementById('confirmPassword');
const registrationPreview = document.getElementById('registrationPreview');
const fullName = document.getElementById('fullName');
const employeeId = document.getElementById('employeeId');
const staffRegistrationCode = document.getElementById('staffRegistrationCode');

function validateProfile() {
    [fullName, employeeId, staffRegistrationCode].forEach((field) => {
        field.setCustomValidity(!field.value.trim() ? 'Please complete this field.' : '');
    });
    password.setCustomValidity(password.value.length < 8 ? 'Use at least 8 characters.' : '');
    confirmation.setCustomValidity(confirmation.value && confirmation.value !== password.value ? 'Passwords must match.' : '');
}
registerForm.addEventListener('input', () => {
    registrationPreview.hidden = true;
    validateProfile();
});
registerForm.addEventListener('submit', (event) => {
    event.preventDefault();
    validateProfile();
    if (!registerForm.reportValidity()) return;
    const email = demoAccounts.normalize(document.getElementById('email').value);
    try {
        const accounts = demoAccounts.read();
        if (Object.prototype.hasOwnProperty.call(accounts, email)) {
            registrationPreview.textContent = 'This email is already registered in this browser. Please log in.';
        } else {
            // Frontend prototype only: private code verification requires a backend.
            // Never store passwords or the private staff registration code.
            accounts[email] = { role: 'staff', fullName: fullName.value.trim(), employeeId: employeeId.value.trim() };
            localStorage.setItem(demoAccounts.key, JSON.stringify(accounts));
            window.location.assign('login.php');
            return;
        }
    } catch (_) {
        registrationPreview.textContent = 'Unable to save your profile in this browser. Please enable browser storage and try again.';
    }
    registrationPreview.hidden = false;
});
