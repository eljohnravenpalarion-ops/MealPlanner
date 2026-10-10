<?php

session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$meal_id = (int)$_GET["id"];

$sql = "DELETE FROM meal
        WHERE id = '$meal_id'
        AND user_id = '$user_id'";

mysqli_query($conn, $sql);

header("Location: view_meal.php?deleted=1");
exit();
?>