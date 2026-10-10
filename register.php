<?php
require_once "db.php";
if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $username= $_POST ["username"];
    $password= $_POST ["password"];

$password_hash = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO user (username, password)
VALUES('$username', '$password_hash')";

mysqli_query ($conn, $sql);
}

?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register_Meal Planner</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="form-card">

        <h1>Create an Account</h1>

        <form method="POST">

            <label>Username</label>
            <input type="text" name="username" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit">Register</button>

        </form>

        <p>Already have an account?</p>
       <a class="dashboard-button" href="login.php">Back to Login</a>

    </div>



   
</body>
</html>