<?php

session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$sql = "SELECT username FROM `user` WHERE id = '$user_id'";
$result = mysqli_query($conn, $sql);

$user = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
<?php require_once "sidebar.php"; ?>
    <div class="profile-card">

    <h1>My Profile</h1>

    <p>
        <strong>Username:</strong>
        <?php echo htmlspecialchars($user["username"]); ?>
    </p>

    <div class="profile-actions">

    <a class="dashboard-button" href="change_password.php">
        Change Password
    </a>

   

</div>
</body>
</html>