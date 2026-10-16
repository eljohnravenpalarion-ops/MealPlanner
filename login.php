
<?php
session_start();
require_once "db.php";
$registrationSuccess = isset($_GET["registered"])
    && $_GET["registered"] === "success";
$usernameInvalid = false;
$passwordInvalid = false;
if ($_SERVER["REQUEST_METHOD"]== "POST") {
    $username = $_POST ["username"];
    $password = $_POST ["password"];

$sql = "SELECT * FROM user WHERE username= '$username'";

$result=mysqli_query($conn,$sql);

if (mysqli_num_rows($result)==0 ) {

  $usernameInvalid = true;

} else {
    $user = mysqli_fetch_assoc($result);

if (password_verify($password, $user["password"])){
    
    $_SESSION ["user_id"]= $user ["id"];

    header("Location:dashboard.php");
    exit();


} else {
 $passwordInvalid = true;
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
<script>
const errorToast = document.getElementById("loginErrorToast");

if (errorToast) {
    setTimeout(() => {
        errorToast.classList.add("hide");

        setTimeout(() => {
            errorToast.remove();
        }, 300);
    }, 2500);
}
</script>
<body>

<div class="login-page">

    <div class="login-image">

        <img src="image/login-food.jpg.jfif"
             alt="Fresh vegetables and healthy food">


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
    value="<?= htmlspecialchars($username ?? '', ENT_QUOTES, 'UTF-8') ?>"
    class="<?= $usernameInvalid ? 'input-error' : '' ?>"
    placeholder="Enter your username"
    required
>

<?php if ($usernameInvalid): ?>
    <small class="field-error">Incorrect username.</small>
<?php endif; ?>

            <label>Password</label>

           <input
    type="password"
    name="password"
    class="<?= $passwordInvalid ? 'input-error' : '' ?>"
    placeholder="Enter your password"
    required
>

<?php if ($passwordInvalid): ?>
    <small class="field-error">Incorrect password.</small>
<?php endif; ?>

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
<?php if ($registrationSuccess): ?>
    <script>
        alert("Account created successfully! You can now log in.");
    </script>
<?php endif; ?>
</body>


</html>