<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | PRISM</title>
    <link rel="icon" type="image/png" href="assets/images/prismicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body class="unified-login">
<div class="background-overlay">
    <div class="register-card">
        <div class="logo-container">
            <img src="assets/images/prismlogo1.png" class="logo-main" alt="PRISM logo">
        </div>
        <h2>Create your account</h2>
        <p class="subtitle">Use your CEU school email to register.</p>
        <!-- Frontend preview: fields intentionally have no names so credentials are never submitted. -->
        <form id="registerForm" action="register.php" method="get">
<div class="registration-field" id="accountRoleField">
            <label class="login-label" for="accountRole">Account category</label>
            <div class="input-group">
                <select id="accountRole" required>
                    <option value="">Select your category</option>
                    <option value="student">Student</option>
                    <option value="adviser">Research Adviser</option>
                    <option value="staff">RPMS Staff</option>
                </select>
            </div>
</div>
<div class="registration-field" id="fullNameField">
            <label class="login-label" for="fullName">Full name</label>
            <div class="input-group">
                <i class="fa-solid fa-user" aria-hidden="true"></i>
                <input type="text" id="fullName" placeholder="Enter your full name" autocomplete="name" maxlength="120" required>
            </div>
</div>
<div class="registration-field" id="departmentField" hidden>
            <label class="login-label" for="department">Department</label>
            <div class="input-group">
                <i class="fa-solid fa-building-columns" aria-hidden="true"></i>
                <input type="text" id="department" placeholder="e.g. AMT Department" maxlength="120" required>
            </div>
</div>
<div class="registration-field" id="courseField" hidden>
            <label class="login-label" for="course">Course</label>
            <div class="input-group">
                <i class="fa-solid fa-graduation-cap" aria-hidden="true"></i>
                <input type="text" id="course" placeholder="e.g. BS Nursing" maxlength="120">
            </div>
</div>
<div class="registration-field" id="yearLevelField" hidden>
            <label class="login-label" for="yearLevel">Year level</label>
            <div class="input-group">
                <select id="yearLevel">
                    <option value="">Select year level</option>
                    <option value="1">1st year</option>
                    <option value="2">2nd year</option>
                    <option value="3">3rd year</option>
                    <option value="4">4th year</option>
                    <option value="5">5th year</option>
                    <option value="6">6th year</option>
                    <option value="graduate">Graduate level</option>
                </select>
            </div>
</div>
<div class="registration-field" id="emailField">
            <label class="login-label" for="email">School email</label>
            <div class="input-group">
                <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                <input type="email" id="email" placeholder="name@ceu.mls.edu.ph"
                    pattern="[^@\s]+@[cC][eE][uU]\.[mM][lL][sS]\.[eE][dD][uU]\.[pP][hH]"
                    title="Use your @ceu.mls.edu.ph school email address."
                    autocomplete="username" autocapitalize="none" spellcheck="false" required>
            </div>
</div>
<div class="registration-field" id="passwordField">
            <label class="login-label" for="password">Password</label>
            <div class="input-group">
                <i class="fa-solid fa-lock" aria-hidden="true"></i>
                <input type="password" id="password" placeholder="Password" autocomplete="new-password" required>
            </div>
</div>
<div class="registration-field" id="confirmPasswordField">
            <label class="login-label" for="confirmPassword">Confirm password</label>
            <div class="input-group">
                <i class="fa-solid fa-lock" aria-hidden="true"></i>
                <input type="password" id="confirmPassword" placeholder="Confirm password" autocomplete="new-password" required>
            </div>
</div>
            <button type="submit">Register</button>
            <p id="registrationPreview" class="success-message" role="status" hidden>Registration details are valid. Account creation will be available when registration is connected.</p>
            <div class="register-text">Already have an account? <a href="login.php">Log in</a></div>
        </form>
    </div>
</div>
<script src="assets/js/demo-accounts.js"></script>
<script src="assets/js/register.js"></script>
</body>
</html>
