<?php

session_start();
require_once "db.php";
require_once "stack.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$stack = $_SESSION["recent_stack"] ?? [];
$top = $_SESSION["recent_top"] ?? -1;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recently Viewed Meals</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
 
 
 <?php require_once "sidebar.php"; ?>
  <h1 class="recent-title">Recently Viewed Meals</h1>
   <?php 
if (isEmpty($top)) {

    echo '<div class="meal-card">';
    echo '<p>No recently viewed meals.</p>';
    echo '</div>';

}
else {

    $tempStack = $stack;
    $tempTop = $top;

    while (!isEmpty($tempTop)) {

        $meal_id = pop($tempStack, $tempTop);

        $sql = "SELECT * FROM meal
                WHERE id = '$meal_id'
                AND user_id = '$user_id'";

        $result = mysqli_query($conn, $sql);

        $meal = mysqli_fetch_assoc($result);

        if ($meal) {

            echo '<div class="meal-card">';

            echo '<h2>' . htmlspecialchars($meal["meal_name"]) . '</h2>';

            echo '<p><strong>Category:</strong> ' .
                 htmlspecialchars($meal["category"]) . '</p>';

            echo '<p><strong>Day:</strong> ' .
                 htmlspecialchars($meal["day"]) . '</p>';

            echo '<p><strong>Calories:</strong> ' .
                 htmlspecialchars($meal["calories"]) . '</p>';

            echo '<p><strong>Ingredients:</strong> ' .
                 htmlspecialchars($meal["ingredients"]) . '</p>';

            echo '<p><strong>Description:</strong> ' .
                 htmlspecialchars($meal["description"]) . '</p>';

            echo '<a class="dashboard-button" href="view_single_meal.php?id=' .
                 $meal["id"] . '">View Meal</a>';

            echo '</div>';
        }
    }
}

    ?>
</body>
</html>