<?php

session_start();
require_once "db.php";
require_once "stack.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$meal_id = (int)($_GET["id"] ?? 0);

$sql = "SELECT * FROM meal
        WHERE id = '$meal_id'
        AND user_id = '$user_id'";

$result = mysqli_query($conn, $sql);

$meal = mysqli_fetch_assoc($result);

if (!$meal) {
    echo "Meal not found.";
    exit();
}




$stack = $_SESSION["recent_stack"] ?? [];
$top = $_SESSION["recent_top"] ?? -1;


push($stack, $top, $meal["id"]);




$_SESSION["recent_stack"] = $stack;
$_SESSION["recent_top"] = $top;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Meal</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php require_once "sidebar.php"; ?>
    <h1 class="meal-details-title">Meal Details</h1>

    <div class="meal-card">

        <h2><?php echo htmlspecialchars($meal["meal_name"]); ?></h2>

        <p>
            <strong>Category:</strong>
            <?php echo htmlspecialchars($meal["category"]); ?>
        </p>

        <p>
            <strong>Day:</strong>
            <?php echo htmlspecialchars($meal["day"]); ?>
        </p>

        <p>
            <strong>Calories:</strong>
            <?php echo htmlspecialchars($meal["calories"]); ?>
        </p>

        <p>
            <strong>Ingredients:</strong>
            <?php echo htmlspecialchars($meal["ingredients"]); ?>
        </p>

        <p>
            <strong>Description:</strong>
            <?php echo htmlspecialchars($meal["description"]); ?>
        </p>

    </div>

</body>


</html>