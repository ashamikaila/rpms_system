<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register | AI Workload Assistant</title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css">

    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

</head>

<body>

<div class="background-overlay">

    <div class="register-card">

        <div class="logo-container">

            <img src="assets/images/ceu_logo2.jpg" class="logo-main">

        </div>

        <h2>Register</h2>

        <p class="subtitle">
            Create your account
        </p>

        <form action="register_process.php" method="POST">

            <div class="input-group">

                <i class="fa-solid fa-id-badge"></i>

                <input
                    type="text"
                    name="employee_id"
                    placeholder="Employee ID"
                    required>

            </div>

            <div class="input-group">

                <i class="fa-solid fa-user"></i>

                <input
                    type="text"
                    name="fullname"
                    placeholder="Full Name"
                    required>

            </div>

            <div class="input-group">

                <i class="fa-solid fa-envelope"></i>

                <input
                    type="email"
                    name="email"
                    placeholder="CEU Email"
                    required>

            </div>

            <div class="input-group">

                <i class="fa-solid fa-user-circle"></i>

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

            <div class="input-group">

                <i class="fa-solid fa-lock"></i>

                <input
                    type="password"
                    name="confirm_password"
                    id="confirmPassword"
                    placeholder="Confirm Password"
                    required>

                <span class="toggle-password">

                    <i class="fa-solid fa-eye" id="toggleConfirmPassword"></i>

                </span>

            </div>

            <button type="submit">

                REGISTER

            </button>

            <div class="register-text">

                Already have an account?

                <a href="login.php">Login</a>

            </div>

        </form>

    </div>

</div>

<script src="assets/js/script.js"></script>

</body>
</html>