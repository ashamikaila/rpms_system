<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | PRISM</title>

    <link rel="icon" type="image/png" href="assets/images/prismicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?= filemtime(__DIR__ . '/assets/css/style.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

</head>

<body class="unified-login">

<div class="background-overlay">

    <div class="login-card">

        <div class="logo-container">
            <img src="assets/images/prismlogo1.png" class="logo-main" alt="PRISM logo">
        </div>

        <div class="prism-branding">
            <h1>Welcome to <span>PRISM</span>!</h1>
            <p><strong>IERB Progress &amp; Reporting System</strong></p>
            <p>Centro Escolar University - Malolos <span aria-hidden="true">&bull;</span> RPMS</p>
        </div>

        <form id="loginForm" action="login.php" method="get">
            <p id="loginError" class="success-message" role="alert" hidden></p>
            <div class="input-group">
                <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                <input type="email" id="email" aria-label="School email" placeholder="name@ceu.mls.edu.ph"
                    pattern="[^@\s]+@([cC][eE][uU]\.[mM][lL][sS]\.[eE][dD][uU]\.[pP][hH]|[cC][eE][uU]\.[eE][dD][uU]\.[pP][hH]|[mM][lL][sS]\.[cC][eE][uU]\.[eE][dD][uU]\.[pP][hH]|[gG][mM][aA][iI][lL]\.[cC][oO][mM])"
                    title="Use your registered CEU or Gmail email address."
                    autocomplete="username" autocapitalize="none" spellcheck="false" required autofocus>
            </div>
            <div class="input-group">
                <i class="fa-solid fa-lock" aria-hidden="true"></i>
                <input type="password" id="password" aria-label="Password" placeholder="Password" autocomplete="current-password" required>
            </div>
            <div class="form-options"><a href="forgot_password.php">Forgot Password?</a></div>
            <button type="submit">Log in</button>
            <div class="register-text">RPMS staff without an account? <a href="register.php">Register</a></div>
            <p class="login-account-note">Research advisers and students are given their login by the RPMS office.</p>
        </form>

    </div>

</div>

<script src="assets/js/demo-accounts.js"></script>
<script src="assets/js/login.js"></script>
</body>
</html>
