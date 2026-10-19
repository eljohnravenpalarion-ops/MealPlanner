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
                        <script src="https://unpkg.com/lucide@latest"></script>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Edit Meal</title>
                        <link rel="stylesheet" href="style.css">
                    </head>
                    <body>
                        <?php require_once "sidebar.php"; ?>
                        <main class="add-meal-page">

    <h1 class="add-meal-title">Edit Meal</h1>

    <div class="form-card add-meal-card">

        <form method="POST" class="add-meal-grid">

            <div class="add-meal-field">
                <label for="meal_name">Meal Name</label>
                <input
                    type="text"
                    id="meal_name"
                    name="meal_name"
                    value="<?php echo htmlspecialchars($meal["meal_name"] ?? ""); ?>"
                    required
                >
            </div>

            <div class="add-meal-field">
                <label for="category">Category</label>
                <?php
                $editCategories = ["Breakfast", "Lunch", "Dinner", "Snacks"];
                $currentCategory = (string)($meal["category"] ?? "");

                if ($currentCategory !== "" &&
                    !in_array($currentCategory, $editCategories, true)) {
                    array_unshift($editCategories, $currentCategory);
                }
                ?>
                <select name="category" id="category" required>
                    <option value="">Select Category</option>
                    <?php foreach ($editCategories as $cat): ?>
                        <option
                            value="<?php echo htmlspecialchars($cat); ?>"
                            <?php echo $currentCategory === $cat ? "selected" : ""; ?>
                        >
                            <?php echo htmlspecialchars($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="add-meal-field">
                <label for="day">Day</label>
                <?php
                $editDays = [
                    "Monday", "Tuesday", "Wednesday", "Thursday",
                    "Friday", "Saturday", "Sunday"
                ];
                $currentDay = (string)($meal["day"] ?? "");

                if ($currentDay !== "" &&
                    !in_array($currentDay, $editDays, true)) {
                    array_unshift($editDays, $currentDay);
                }
                ?>
                <select name="day" id="day" required>
                    <option value="">Select Day</option>
                    <?php foreach ($editDays as $weekday): ?>
                        <option
                            value="<?php echo htmlspecialchars($weekday); ?>"
                            <?php echo $currentDay === $weekday ? "selected" : ""; ?>
                        >
                            <?php echo htmlspecialchars($weekday); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="add-meal-field">
                <label for="calories">Calories</label>
                <input
                    type="number"
                    id="calories"
                    name="calories"
                    min="0"
                    step="any"
                    value="<?php echo htmlspecialchars($meal["calories"] ?? ""); ?>"
                    required
                >
            </div>

            <div class="add-meal-field">
                <label for="ingredients">Ingredients</label>
                <textarea
                    id="ingredients"
                    name="ingredients"
                    rows="4"
                    required
                ><?php echo htmlspecialchars($meal["ingredients"] ?? ""); ?></textarea>
            </div>

            <div class="add-meal-field">
                <label for="description">Description</label>
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    required
                ><?php echo htmlspecialchars($meal["description"] ?? ""); ?></textarea>
            </div>

            <div class="add-meal-actions">
                <button type="submit">
                    <i data-lucide="save"></i>
                    Update Meal
                </button>
            </div>

        </form>
    </div>

</main>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    if (window.lucide) {
        lucide.createIcons();
    }
</script>

</body>
</html>