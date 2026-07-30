<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Staff Login | AI Workload Assistant</title>

    <link rel="icon" type="image/png" href="assets/images/ceu_logo1.jpg">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

</head>

<body>

<div class="background-overlay">

    <div class="login-card">

        <div class="logo-container">
            <img src="assets/images/ceu_logo2.jpg" class="logo-main">
        </div>

        <h2>Welcome to Research Planning, Monitoring and Evaluation Section!</h2>

        <p class="subtitle">
            Centralized Web-Based AI Workload Assistant
        </p>

        <form action="login_process.php" method="POST">

            <div class="input-group">
                <i class="fa-solid fa-user"></i>
                <input
                    type="text"
                    name="username"
                    placeholder="Username"
                    required>
            </div>

            <div class="input-group">
                <i class="fa-solid fa-lock"></i>
                <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="Password"
                    required>

                <span class="toggle-password">
                    <i class="fa-solid fa-eye" id="togglePassword"></i>
                </span>
            </div>

            <div class="form-options">
                <div></div>
                <!-- Routes link through loading.php -->
                <a href="loading.php?redirect=forgot_password.php">
                    Forgot Password?
                </a>
            </div>

            <button type="submit">
                LOGIN
            </button>

            <div class="register-text">
                Don't have an account?
                <!-- Routes link through loading.php -->
                <a href="loading.php?redirect=register.php">Register</a>
            </div>

        </form>

    </div>

</div>

<script src="assets/js/script.js"></script>

</body>
</html>