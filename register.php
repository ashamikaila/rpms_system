<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | PRISM</title>
    <link rel="icon" type="image/png" href="assets/images/prismicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?= filemtime(__DIR__ . '/assets/css/style.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body class="unified-login">
<div class="background-overlay">
    <div class="register-card">
        <div class="logo-container">
            <img src="assets/images/prismlogo1.png" class="logo-main" alt="PRISM logo">
        </div>
        <h2>RPMS Staff Registration</h2>
        <p class="subtitle">Create an administrator account using the private staff registration code.</p>
        <!-- Frontend preview: fields intentionally have no names so credentials are never submitted. -->
        <form id="registerForm" action="register.php" method="get">
<div class="registration-field">
<div class="input-group"><i class="fa-solid fa-key" aria-hidden="true"></i><input type="password" id="staffRegistrationCode" aria-label="Staff registration code" placeholder="Staff registration code" autocomplete="off" required></div>
</div>
<div class="registration-field">
<div class="input-group"><i class="fa-solid fa-id-card" aria-hidden="true"></i><input type="text" id="employeeId" aria-label="Employee ID" placeholder="Employee ID" maxlength="60" required></div>
</div><div class="registration-field" id="fullNameField">
            <div class="input-group">
                <i class="fa-solid fa-user" aria-hidden="true"></i>
                <input type="text" id="fullName" aria-label="Full name" placeholder="Enter your full name" autocomplete="name" maxlength="120" required>
            </div>
</div>
<div class="registration-field" id="emailField">
            <div class="input-group">
                <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                <input type="email" id="email" aria-label="Email" placeholder="@ceu.edu.ph, @mls.ceu.edu.ph, @gmail.com"
                    pattern="[^@\s]+@([cC][eE][uU]\.[eE][dD][uU]\.[pP][hH]|[mM][lL][sS]\.[cC][eE][uU]\.[eE][dD][uU]\.[pP][hH]|[gG][mM][aA][iI][lL]\.[cC][oO][mM])"
                    title="Use an @ceu.edu.ph, @mls.ceu.edu.ph, or @gmail.com email address."
                    autocomplete="username" autocapitalize="none" spellcheck="false" required>
            </div>
</div>
<div class="registration-field" id="passwordField">
            <div class="input-group">
                <i class="fa-solid fa-lock" aria-hidden="true"></i>
                <input type="password" id="password" aria-label="Password (8+ characters)" placeholder="Password (8+ characters)" minlength="8" autocomplete="new-password" required>
            </div>
</div>
<div class="registration-field" id="confirmPasswordField">
            <div class="input-group">
                <i class="fa-solid fa-lock" aria-hidden="true"></i>
                <input type="password" id="confirmPassword" aria-label="Confirm password" placeholder="Confirm password" minlength="8" autocomplete="new-password" required>
            </div>
</div>
            <button type="submit">Register</button>
            <p id="registrationPreview" class="success-message" role="status" hidden>Registration details are valid. Account creation will be available when registration is connected.</p>
            <div class="register-text">Already have an account? <a href="login.php">Log in</a></div>
        </form>
    </div>
</div>
<script src="assets/js/demo-accounts.js"></script>
<script src="assets/js/register.js?v=staff-registration-1"></script>
</body>
</html>


