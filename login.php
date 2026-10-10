
<?php 
session_start();
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"]== "POST") {
    $username = $_POST ["username"];
    $password = $_POST ["password"];

$sql = "SELECT * FROM user WHERE username= '$username'";

$result=mysqli_query($conn,$sql);

if (mysqli_num_rows($result)==0 ) {

    echo "Username not found. ";

} else {
    $user = mysqli_fetch_assoc($result);

if (password_verify($password, $user["password"])){
    
    $_SESSION ["user_id"]= $user ["id"];

    header("Location:dashboard.php");
    exit();


} else {
    echo "Incorrect password. ";
}





}

}


?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login-Meal Planner</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="login-page">

    <div class="login-image">

        <img src="image/login-food.jpg.jfif"
             alt="Fresh vegetables and healthy food">

        <div class="login-image-text">
            <h2>Eat Healthy.<br>Plan Better.</h2>
            <p>Good food fuels a better tomorrow.</p>
        </div>

    </div>


    <div class="login-form-area">

        <div class="login-brand">
            <span class="brand-name">Meal Planner</span>
        </div>

        <h1>Welcome Back!</h1>

        <p class="login-subtitle">
            Log in to your account and continue your healthy journey.
        </p>

        <form method="POST" class="login-form">

            <label>Username</label>

            <input
                type="text"
                name="username"
                placeholder="Enter your username"
                required
            >

            <label>Password</label>

            <input
                type="password"
                name="password"
                placeholder="Enter your password"
                required
            >

            <button type="submit">
                → &nbsp; Login
            </button>

        </form>

        <p class="register-text">
            Don't have an account?
            <a href="register.php">Create an Account</a>
        </p>

    </div>

</div>

</body>
</html>