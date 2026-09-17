const registerForm = document.getElementById('registerForm');
const password = document.getElementById('password');
const confirmation = document.getElementById('confirmPassword');
const registrationPreview = document.getElementById('registrationPreview');
const accountRole = document.getElementById('accountRole');
const course = document.getElementById('course');
const yearLevel = document.getElementById('yearLevel');
const fullName = document.getElementById('fullName');
const department = document.getElementById('department');

function validateProfile() {
    const isStudent = accountRole.value === 'student';
    const needsDepartment = isStudent || accountRole.value === 'adviser';
    [[department, needsDepartment], [course, isStudent], [yearLevel, isStudent]].forEach(([field, visible]) => {
        document.getElementById(field.id + 'Field').hidden = !visible;
        field.disabled = !visible;
        field.required = visible;
    });
    [fullName, department, course].forEach((field) => {
        field.setCustomValidity(field.required && !field.value.trim() ? 'Please complete this field.' : '');
    });
}
accountRole.addEventListener('change', validateProfile);
validateProfile();

function validateConfirmation() {
    confirmation.setCustomValidity(
        confirmation.value && confirmation.value !== password.value
            ? 'Passwords must match.'
            : ''
    );
}

registerForm.addEventListener('input', () => {
    registrationPreview.hidden = true;
    validateConfirmation();
    validateProfile();
});

registerForm.addEventListener('submit', (event) => {
    event.preventDefault();
    validateConfirmation();
    validateProfile();
    if (registerForm.reportValidity()) {
        const email = demoAccounts.normalize(document.getElementById('email').value);
        const role = document.getElementById('accountRole').value;
        if (!demoAccounts.route(role)) return;
        try {
            const accounts = demoAccounts.read();
            if (Object.prototype.hasOwnProperty.call(accounts, email)) {
                registrationPreview.textContent = 'This email is already registered in this browser. Please log in.';
            } else {
                accounts[email] = {
                    role,
                    fullName: fullName.value.trim(),
                    ...(department.disabled ? {} : { department: department.value.trim() }),
                    ...(course.disabled ? {} : { course: course.value.trim(), yearLevel: yearLevel.value })
                };
                localStorage.setItem(demoAccounts.key, JSON.stringify(accounts));
                window.location.assign('login.php');
                return;
            }
        } catch (_) {
            registrationPreview.textContent = 'Unable to save your profile in this browser. Please enable browser storage and try again.';
        }
        registrationPreview.hidden = false;
    }
});
