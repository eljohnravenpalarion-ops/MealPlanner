<?php

session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $current_password = $_POST["current_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    $sql = "SELECT password FROM `user` WHERE id = '$user_id'";
    $result = mysqli_query($conn, $sql);

    $user = mysqli_fetch_assoc($result);

    if (!password_verify($current_password, $user["password"])) {

        $message = "Current password is incorrect.";

    } elseif ($new_password != $confirm_password) {

        $message = "New passwords do not match.";

    } else {

        $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

        $update_sql = "UPDATE `user`
                       SET password = '$new_password_hash'
                       WHERE id = '$user_id'";

        if (mysqli_query($conn, $update_sql)) {
            $message = "Password changed successfully.";
        } else {
            $message = "Failed to change password.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>  <script src="https://unpkg.com/lucide@latest"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
 <?php require_once "sidebar.php"; ?>
    <div class="form-card">

    <h1>Change Password</h1>

    <?php echo $message; ?>

    <form method="POST">

        <label>Current Password:</label>
        <input type="password" name="current_password" required>

        <label>New Password:</label>
        <input type="password" name="new_password" required>

        <label>Confirm New Password:</label>
        <input type="password" name="confirm_password" required>

        <button type="submit">Change Password</button>

    </form>

    <br>

    <a class="dashboard-button" href="profile.php">Back to Profile</a>
</div>

</body>
</html>