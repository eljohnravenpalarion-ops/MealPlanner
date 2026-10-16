
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once "db.php";
require_once "linear_search.php";

$user_id = (int) $_SESSION["user_id"];
$message = "";
$success = isset($_GET["success"]) && $_GET["success"] === "1";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $meal_name = trim($_POST["meal_name"] ?? "");
    $category = $_POST["category"] ?? "";
    $day = $_POST["day"] ?? "";
    $calories = trim($_POST["calories"] ?? "");
    $ingredients = trim($_POST["ingredients"] ?? "");
    $description = trim($_POST["description"] ?? "");

    $validCategories = ["Breakfast", "Lunch", "Dinner", "Snacks"];

    $validDays = [
        "Monday", "Tuesday", "Wednesday", "Thursday",
        "Friday", "Saturday", "Sunday"
    ];

    if (
        $meal_name === "" ||
        $ingredients === "" ||
        $description === "" ||
        $calories === ""
    ) {
        $message = "Please fill in all fields.";

    } elseif (!in_array($category, $validCategories, true)) {
        $message = "Please select a valid category.";

    } elseif (!in_array($day, $validDays, true)) {
        $message = "Please select a valid day.";

    } elseif (!is_numeric($calories) || !is_finite((float)$calories) || (float)$calories < 0) {
        $message = "Calories must be a valid non-negative number.";

    } else {

        // Get existing meals belonging to this user
        $stmt = mysqli_prepare(
            $conn,
            "SELECT * FROM meal WHERE user_id = ?"
        );

        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $existingMeals = [];

        while ($meal = mysqli_fetch_assoc($result)) {
            $existingMeals[] = $meal;
        }

        mysqli_stmt_close($stmt);

        // Check if this day and category are already occupied
        if (linearSearchMeal($existingMeals, $day, $category)) {

            $message = "This day and category are already occupied.";

        } else {

            // Save the meal using a prepared statement
            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO meal
                (user_id, meal_name, category, day, calories,
                 ingredients, description)
                VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            $calories_value = (float) $calories;

            mysqli_stmt_bind_param(
                $stmt,
                "isssdss",
                $user_id,
                $meal_name,
                $category,
                $day,
                $calories_value,
                $ingredients,
                $description
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);

                // Stay on this page after successfully adding a meal
                header("Location: add_meal.php?success=1");
                exit();

            } else {
                $message = "Unable to add meal. Please try again.";
                mysqli_stmt_close($stmt);
            }
        }
    }
}

// Fetch the five most recently added meals for this user
// Assumes the meal table's primary key is named id.
$stmt = mysqli_prepare(
    $conn,
    "SELECT meal_name, category, day, calories, description
     FROM meal
     WHERE user_id = ?
     ORDER BY id DESC
     LIMIT 5"
);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$recentResult = mysqli_stmt_get_result($stmt);
$recentMeals = [];

while ($meal = mysqli_fetch_assoc($recentResult)) {
    $recentMeals[] = $meal;
}

mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Meal - Meal Planner</title>

    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>

    <?php require_once "sidebar.php"; ?>

    <main class="add-meal-page">

        <?php if ($message !== ""): ?>
            <div class="warning-popup" id="warningPopup">
                <div class="warning-box">
                    <div class="warning-icon">⚠️</div>
                    <h2>Unable to Add Meal</h2>

                    <p>
                        <?php echo htmlspecialchars($message); ?>
                    </p>

                    <button type="button" onclick="closeWarning()">
                        OK
                    </button>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="meal-success-message">
                <i data-lucide="check-circle"></i>
                <span>Meal added successfully!</span>
            </div>
        <?php endif; ?>

        <h1 class="add-meal-title">Add Meal</h1>

        <!-- ADD MEAL FORM -->
        <div class="form-card add-meal-card">

            <form method="POST" class="add-meal-grid">

                <div class="add-meal-field">
                    <label for="meal_name">Meal Name</label>
                    <input
                        type="text"
                        id="meal_name"
                        name="meal_name"
                        value="<?php echo htmlspecialchars($_POST["meal_name"] ?? ""); ?>"
                        required
                    >
                </div>

                <div class="add-meal-field">
                    <label for="category">Category</label>
                    <select name="category" id="category" required>
                        <option value="">Select Category</option>

                        <?php foreach (["Breakfast", "Lunch", "Dinner", "Snacks"] as $cat): ?>
                            <option
                                value="<?php echo $cat; ?>"
                                <?php echo (($_POST["category"] ?? "") === $cat) ? "selected" : ""; ?>
                            >
                                <?php echo $cat; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="add-meal-field">
                    <label for="day">Day</label>
                    <select name="day" id="day" required>
                        <option value="">Select Day</option>

                        <?php foreach (
                            ["Monday", "Tuesday", "Wednesday", "Thursday",
                             "Friday", "Saturday", "Sunday"] as $weekday
                        ): ?>
                            <option
                                value="<?php echo $weekday; ?>"
                                <?php echo (($_POST["day"] ?? "") === $weekday) ? "selected" : ""; ?>
                            >
                                <?php echo $weekday; ?>
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
                        value="<?php echo htmlspecialchars($_POST["calories"] ?? ""); ?>"
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
                    ><?php echo htmlspecialchars($_POST["ingredients"] ?? ""); ?></textarea>
                </div>

                <div class="add-meal-field">
                    <label for="description">Description</label>
                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        required
                    ><?php echo htmlspecialchars($_POST["description"] ?? ""); ?></textarea>
                </div>

                <div class="add-meal-actions">
                    <button type="submit">
                        <i data-lucide="plus"></i>
                        Add Meal
                    </button>
                </div>

            </form>
        </div>

        <!-- RECENTLY ADDED MEALS -->
        <section class="recent-meals-section">

            <div class="recent-meals-heading">
                <div>
                    <h2>Recently Added Meals</h2>
                    <p>Your five most recently added meals.</p>
                </div>

                <span class="recent-meals-count">
                    <?php echo count($recentMeals); ?>
                    <?php echo count($recentMeals) === 1 ? "meal" : "meals"; ?>
                </span>
            </div>

            <?php if (!empty($recentMeals)): ?>

                <div class="recent-meals-list">

                    <?php foreach ($recentMeals as $meal): ?>

                        <article class="recent-meal-card">

                            <div class="recent-meal-icon">
                                <i data-lucide="utensils"></i>
                            </div>

                            <div class="recent-meal-info">

                                <h3>
                                    <?php echo htmlspecialchars($meal["meal_name"]); ?>
                                </h3>

                                <div class="recent-meal-meta">
                                    
<div class="recent-meal-meta">
    <span class="meal-category">
        <?php echo htmlspecialchars($meal["category"]); ?>
    </span>

    <span class="meal-day">
        <?php echo htmlspecialchars($meal["day"]); ?>
    </span>

    <span class="meal-calories">
        <?php
        echo htmlspecialchars(
            rtrim(
                rtrim(
                    number_format((float)$meal["calories"], 2, ".", ""),
                    "0"
                ),
                "."
            )
        );
        ?>
        calories
    </span>
</div>

                                </div>

                                <p class="recent-meal-description">
                                    <?php echo htmlspecialchars($meal["description"]); ?>
                                </p>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <div class="recent-meals-empty">
                    <i data-lucide="clipboard-list"></i>
                    <h3>No meals added yet</h3>
                    <p>Your newly added meals will appear here.</p>
                </div>

            <?php endif; ?>

        </section>

    </main>

    <script>
        if (window.lucide) {
            lucide.createIcons();
        }

        function closeWarning() {
            const popup = document.getElementById("warningPopup");

            if (popup) {
                popup.style.display = "none";
            }
        }
    </script>

</body>
</html>
