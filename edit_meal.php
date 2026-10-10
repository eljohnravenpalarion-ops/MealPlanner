<?php 
                    session_start();
                    require_once "db.php";

                    if (!isset ($_SESSION ["user_id"])) {
                        header ("Location: login.php");
                        exit();

                    }
                    $user_id = $_SESSION ["user_id"];

                    $meal_id = $_GET ["id"];

                    $sql = "SELECT * FROM meal
                            WHERE id = '$meal_id'
                            AND user_id = '$user_id'";
                            
                $result = mysqli_query($conn, $sql);

                if (!$result) {
                    die("SQL Error: " . mysqli_error($conn));
                }

                $meal = mysqli_fetch_assoc($result);

                if ($meal === null) {
                    die("Meal not found. ID: " . $meal_id . " | User ID: " . $user_id);
                }

                    if ($_SERVER ["REQUEST_METHOD"]=="POST") {


                        $meal_name = $_POST["meal_name"];
                        $category = $_POST["category"];
                        $day = $_POST["day"];
                        $calories = $_POST["calories"];
                        $ingredients = $_POST["ingredients"];
                        $description = $_POST["description"];


                        $meal_name = mysqli_real_escape_string($conn, $meal_name);
                        $category = mysqli_real_escape_string($conn, $category);
                        $day = mysqli_real_escape_string($conn, $day);
                        $calories = mysqli_real_escape_string($conn, $calories);
                        $ingredients = mysqli_real_escape_string($conn, $ingredients);
                        $description = mysqli_real_escape_string($conn, $description);

                        $sql = "UPDATE meal SET
                                meal_name = '$meal_name',
                                category = '$category',
                                day = '$day',
                                calories = '$calories',
                                ingredients = '$ingredients',
                                description = '$description'
                                WHERE id = '$meal_id'
                                AND user_id = '$user_id'";

                        mysqli_query($conn, $sql);

                        header("Location: view_meal.php");
                        exit();
                    }



                    ?>

                    <!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Edit Meal</title>
                        <link rel="stylesheet" href="style.css">
                    </head>
                    <body>
                        <?php require_once "sidebar.php"; ?>
                        <h1 class="edit-meal-title">Edit Meal</h1>
                        <div class="form-card">

                        <form method="POST">

                        
                        <label>Meal Name:</label>
                <input type="text" name="meal_name"
                    value="<?php echo htmlspecialchars($meal["meal_name"] ?? ""); ?>">

                <br><br>

                <label>Category:</label>
                <input type="text" name="category"
                    value="<?php echo htmlspecialchars($meal["category"] ?? ""); ?>">

                <br><br>

                <label>Day:</label>
                <input type="text" name="day"
                    value="<?php echo htmlspecialchars($meal["day"] ?? ""); ?>">

                <br><br>

                <label>Calories:</label>
                <input type="number" name="calories"
                    value="<?php echo htmlspecialchars($meal["calories"] ?? ""); ?>">

                <br><br>

                <label>Ingredients:</label>
                <textarea name="ingredients"><?php echo htmlspecialchars($meal["ingredients"] ?? ""); ?></textarea>

                <br><br>

                <label>Description:</label>
                <textarea name="description"><?php echo htmlspecialchars($meal["description"] ?? ""); ?></textarea>
                            <br><br>

                            <button type="submit">Update Meal</button>


            </form>

            </div>
     </body>
     </html>