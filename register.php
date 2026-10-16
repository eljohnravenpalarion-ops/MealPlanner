<?php
session_start();
require_once "db.php";

$registerError = "";
$username = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        $registerError = "Please fill in all fields.";
    } else {
        // Check if the username already exists.
        $check = mysqli_prepare(
            $conn,
            "SELECT id FROM user WHERE username = ?"
        );

        if (!$check) {
            $registerError = "Something went wrong. Please try again.";
        } else {
            mysqli_stmt_bind_param($check, "s", $username);
            mysqli_stmt_execute($check);
            $result = mysqli_stmt_get_result($check);

            if (mysqli_num_rows($result) > 0) {
                $registerError =
                    "Username already exists. Please choose another.";
            } else {
                // Securely hash the password.
                $passwordHash = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                $insert = mysqli_prepare(
                    $conn,
                    "INSERT INTO user (username, password) VALUES (?, ?)"
                );

                if (!$insert) {
                    $registerError =
                        "Registration failed. Please try again.";
                } else {
                    mysqli_stmt_bind_param(
                        $insert,
                        "ss",
                        $username,
                        $passwordHash
                    );

                    if (mysqli_stmt_execute($insert)) {
                        // Save a one-time success notification.
                        $_SESSION["registration_success"] = true;

                        // Redirect directly to Login.
                        header("Location: login.php");
                        exit();
                    } else {
                        $registerError =
                            "Registration failed. Please try again.";
                    }

                    mysqli_stmt_close($insert);
                }
            }

            mysqli_stmt_close($check);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Meal Planner</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="login-page">

    <div class="login-image">
        <img
            src="image/login-food.jpg.jfif"
            alt="Fresh vegetables and healthy food"
        >
    </div>

    <div class="login-form-area">

        <div class="login-brand">
            <span class="brand-name">Meal Planner</span>
        </div>

        <h1>Create an Account</h1>

        <p class="login-subtitle">
            Join Meal Planner and start planning your meals.
        </p>

        <?php if ($registerError !== ""): ?>
            <div class="register-feedback register-feedback-error"
                 role="alert">
                <?= htmlspecialchars(
                    $registerError,
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="login-form">

            <label for="username">Username</label>

            <input
                type="text"
                id="username"
                name="username"
                value="<?= htmlspecialchars(
                    $username,
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>"
                placeholder="Choose a username"
                autocomplete="username"
                required
            >

            <label for="password">Password</label>

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Create a password"
                autocomplete="new-password"
                required
            >

            <button type="submit">
                Create Account
            </button>

        </form>

        <p class="register-text">
            Already have an account?
            <a href="login.php">Log in</a>
        </p>

    </div>

</div>

</body>
</html>
