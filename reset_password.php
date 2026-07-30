<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reset Password | AI Workload Assistant</title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css">

    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

</head>

<body>

<div class="background-overlay">

    <div class="forgot-card">

        <div class="logo-container">

            <img src="assets/images/ceu_logo2.jpg" class="logo-main">

        </div>

        <h2>Create New Password</h2>

        <p class="subtitle">

            Enter your new password below.

        </p>

        <?php

        if(isset($_SESSION["error"])){

            echo "<div class='error-message'>".$_SESSION["error"]."</div>";

            unset($_SESSION["error"]);

        }

        if(isset($_SESSION["success"])){

            echo "<div class='success-message'>".$_SESSION["success"]."</div>";

            unset($_SESSION["success"]);

        }

        ?>

        <form action="update_password.php" method="POST">

            <div class="input-group">

                <i class="fa-solid fa-lock"></i>

                <input
                type="password"
                name="password"
                id="password"
                placeholder="New Password"
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

                RESET PASSWORD

            </button>

            <div class="register-text">

                Back to

                <a href="login.php">Login</a>

            </div>

        </form>

    </div>

</div>

<script src="assets/js/script.js"></script>

</body>

</html>