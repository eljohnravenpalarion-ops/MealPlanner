<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once "db.php";

$user_id = (int) $_SESSION["user_id"];
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $current_password = $_POST["current_password"] ?? "";
    $new_password = $_POST["new_password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    // Retrieve the existing password hash securely
    $stmt = mysqli_prepare(
        $conn,
        "SELECT password FROM `user` WHERE id = ?"
    );

    if (!$stmt) {
        $message = "Unable to verify your current password.";
    } else {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        if (!$user || !password_verify($current_password, $user["password"])) {

            $message = "Current password is incorrect.";

        } elseif ($new_password === "") {

            $message = "Please enter a new password.";

        } elseif ($new_password !== $confirm_password) {

            $message = "New passwords do not match.";

        } elseif (password_verify($new_password, $user["password"])) {

            // Prevent reusing the current password
            $message = "Your new password must be different from your current password.";

        } else {

            $new_password_hash = password_hash(
                $new_password,
                PASSWORD_DEFAULT
            );

            $update = mysqli_prepare(
                $conn,
                "UPDATE `user` SET password = ? WHERE id = ?"
            );

            if (!$update) {
                $message = "Failed to prepare the password update.";
            } else {
                mysqli_stmt_bind_param(
                    $update,
                    "si",
                    $new_password_hash,
                    $user_id
                );

                if (mysqli_stmt_execute($update)) {
                    $message = "Password changed successfully.";
                } else {
                    $message = "Failed to change password.";
                }

                mysqli_stmt_close($update);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <?php require_once "sidebar.php"; ?>

    <main class="add-meal-page">
        <h1 class="add-meal-title">Change Password</h1>

        <div class="form-card add-meal-card">
            <?php if ($message !== ""): ?>
    <div class="password-status-message <?php echo $message === 'Password changed successfully.' ? 'success' : 'error'; ?>" role="status" aria-live="polite">
        <?php echo htmlspecialchars($message, ENT_QUOTES, "UTF-8"); ?>
    </div>
<?php endif; ?>

            <form method="POST" class="add-meal-grid">

                <div class="add-meal-field">
                    <label for="current_password">Current Password</label>
                    <input
                        type="password"
                        id="current_password"
                        name="current_password"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <div class="add-meal-field">
                    <label for="new_password">New Password</label>
                    <input
                        type="password"
                        id="new_password"
                        name="new_password"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <div class="add-meal-field">
                    <label for="confirm_password">Confirm New Password</label>
                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <div class="add-meal-actions">
                    <button type="submit">
                        <i data-lucide="lock-keyhole"></i>
                        Change Password
                    </button>
                </div>

            </form>

            <p>
                <a class="dashboard-button" href="profile.php">Back to Profile</a>
            </p>
        </div>
    </main>

    <script>
        if (window.lucide) {
            lucide.createIcons();
        }
    </script>
</body>
</html>