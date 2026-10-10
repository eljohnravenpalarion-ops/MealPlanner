<?php 
session_start();
require_once "db.php";
require_once "queue.php";


if (!isset($_SESSION ["user_id"])){
    header ("Location: login.php");
    exit();

}
$user_id = $_SESSION["user_id"];
$sql = "SELECT * FROM meal
        WHERE user_id = '$user_id'
        ORDER BY CASE day
            WHEN 'Monday' THEN 1
            WHEN 'Tuesday' THEN 2
            WHEN 'Wednesday' THEN 3
            WHEN 'Thursday' THEN 4
            WHEN 'Friday' THEN 5
            WHEN 'Saturday' THEN 6
            WHEN 'Sunday' THEN 7
            ELSE 8
        END";

$result = mysqli_query($conn, $sql);
$queue = []; 
$front = 0; 
$rear = -1;

$totalMeals = 0;
$totalCalories = 0;

while ($meal = mysqli_fetch_assoc($result)) { 

    enqueue($queue, $rear, $meal);

    $totalMeals++;
    $totalCalories += $meal["calories"];
}
$tempQueue = $queue;
$tempFront = $front;
$tempRear = $rear;

$weeklyMeals = [];

while (!queueIsEmpty($tempFront, $tempRear)) {

    $meal = dequeue($tempQueue, $tempFront, $tempRear);

    $dayName = ucfirst(strtolower(trim($meal["day"])));

    if (!isset($weeklyMeals[$dayName])) {
        $weeklyMeals[$dayName] = [];
    }

    $weeklyMeals[$dayName][] = $meal;
}

$days = [
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday",
    "Sunday"
];


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Meal Planner</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
 
  <?php require_once "sidebar.php"; ?>


    <header class="dashboard-header">
        <div>
            <h1>Meal Planner Dashboard</h1>
            <p>Welcome to your dashboard!</p>
        </div>

     <img src="dashboard-food.jpg.jfif"
     alt="Fresh vegetables and healthy food"
     class="dashboard-food-image">
    </header>

    

    <main class="dashboard-container">

        <section class="stats-section">

            <div class="stat-card">
                <h2>Total Meals</h2>
                <p><?php echo $totalMeals; ?></p>
            </div>

            <div class="stat-card">
                <h2>Total Calories</h2>
                <p><?php echo $totalCalories; ?></p>
            </div>

        </section>


        <section class="dashboard-section">

            <h2>Daily Calories</h2>

            <div class="daily-grid">

                <?php

                foreach ($days as $day) {

                    $dayCalories = 0;

                    if (isset($weeklyMeals[$day])) {

                        foreach ($weeklyMeals[$day] as $meal) {
                            $dayCalories += $meal["calories"];
                        }
                    }

                    echo '<div class="daily-card">';
                    echo '<h3>' . $day . '</h3>';
                    echo '<p>' . $dayCalories . ' calories</p>';
                    echo '</div>';
                }

                ?>

            </div>

        </section>


        <section class="dashboard-section">

            <h2>Weekly Meals</h2>

            <div class="weekly-container">

                <?php

                foreach ($days as $day) {

                    echo '<div class="weekly-day">';
                    echo '<h3>' . $day . '</h3>';

                    if (isset($weeklyMeals[$day])) {

                        foreach ($weeklyMeals[$day] as $meal) {

                            echo '<p>';
                            echo htmlspecialchars($meal["category"]) . ' - ';
                            echo htmlspecialchars($meal["meal_name"]) . ' - ';
                            echo htmlspecialchars($meal["calories"]) . ' calories';
                            echo '</p>';
                        }

                    } else {

                        echo '<p>No meal planned yet.</p>';
                    }

                    echo '</div>';
                }

                ?>

            </div>

        </section>

    </main>


    
</body>
</html>