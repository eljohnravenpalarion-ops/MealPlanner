
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once "db.php";

$user_id = (int) $_SESSION["user_id"];
$category = trim($_GET["category"] ?? "");
$sort = $_GET["sort"] ?? "newest";

$sortOptions = [
    "az"       => "meal_name ASC",
    "za"       => "meal_name DESC",
    "low_high" => "calories ASC",
    "high_low" => "calories DESC",
    "newest"   => "id DESC"
];

$orderBy = $sortOptions[$sort] ?? "id DESC";

$allowedCategories = [
    "Breakfast",
    "Lunch",
    "Dinner",
    "Snacks"
];

if (!in_array($category, $allowedCategories, true)) {
    $category = "";
}

$sql = "SELECT * FROM meal WHERE user_id = ?";
$types = "i";
$params = [$user_id];

if ($category !== "") {
    $sql .= " AND category = ?";
    $types .= "s";
    $params[] = $category;
}

$sql .= " ORDER BY " . $orderBy;

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Unable to load meals. Please try again.");
}

$bindParams = [$types];

foreach ($params as $key => $value) {
    $bindParams[] = &$params[$key];
}

call_user_func_array(
    "mysqli_stmt_bind_param",
    array_merge([$stmt], $bindParams)
);

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$meals = [];

while ($meal = mysqli_fetch_assoc($result)) {
    $meals[] = $meal;
}

$totalMeals = count($meals);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Meals | Meal Planner</title>

    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<?php require_once "sidebar.php"; ?>

<main class="meals-page">

    <header class="meals-header">
        <div>
            <h1 class="meals-heading">My Meals</h1>
        </div>

        <a href="add_meal.php" class="add-meal-button">
            + Add Meal
        </a>
    </header>

    <section class="meal-toolbar">
        <form
            action="view_meal.php"
            method="GET"
            class="meal-search-form"
            id="mealSearchForm"
        >

            <input
                type="search"
                id="searchInput"
                placeholder="Search meals, ingredients..."
                aria-label="Search meals"
                autocomplete="off"
            >

            <select
                name="category"
                id="categoryFilter"
                aria-label="Filter by category"
            >
                <option value="">All Categories</option>

                <?php foreach ($allowedCategories as $item): ?>
                    <option
                        value="<?php echo htmlspecialchars($item, ENT_QUOTES, 'UTF-8'); ?>"
                        <?php echo $category === $item ? "selected" : ""; ?>
                    >
                        <?php echo htmlspecialchars($item, ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select
                name="sort"
                id="sortFilter"
                aria-label="Sort meals"
            >
                <option value="newest" <?php echo $sort === "newest" ? "selected" : ""; ?>>
                    Newest First
                </option>
                <option value="az" <?php echo $sort === "az" ? "selected" : ""; ?>>
                    Name: A–Z
                </option>
                <option value="za" <?php echo $sort === "za" ? "selected" : ""; ?>>
                    Name: Z–A
                </option>
                <option value="low_high" <?php echo $sort === "low_high" ? "selected" : ""; ?>>
                    Calories: Low to High
                </option>
                <option value="high_low" <?php echo $sort === "high_low" ? "selected" : ""; ?>>
                    Calories: High to Low
                </option>
            </select>

        </form>
    </section>

    <h2 class="meal-results-heading" id="mealResultsHeading">
        Your Meals (<?php echo $totalMeals; ?>)
    </h2>

    <div class="meal-table-wrapper">
        <table class="meal-table" id="mealTable">
            <thead>
                <tr>
                    <th>Meal Name</th>
                    <th>Category</th>
                    <th>Day</th>
                    <th>Calories</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody id="mealTableBody">

                <?php foreach ($meals as $meal): ?>

                    <?php
                    $searchableText = implode(" ", [
                        $meal["meal_name"] ?? "",
                        $meal["category"] ?? "",
                        $meal["ingredients"] ?? "",
                        $meal["day"] ?? "",
                        $meal["description"] ?? ""
                    ]);
                    ?>

                    <tr
                        class="meal-row"
                        data-search="<?php
                            echo htmlspecialchars(
                                $searchableText,
                                ENT_QUOTES | ENT_SUBSTITUTE,
                                'UTF-8'
                            );
                        ?>"
                    >
                        <td class="meal-table-name">
                            <?php
                            echo htmlspecialchars(
                                $meal["meal_name"] ?? "Untitled Meal",
                                ENT_QUOTES,
                                "UTF-8"
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $meal["category"] ?? "",
                                ENT_QUOTES,
                                "UTF-8"
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $meal["day"] ?? "",
                                ENT_QUOTES,
                                "UTF-8"
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                (string) ($meal["calories"] ?? 0),
                                ENT_QUOTES,
                                "UTF-8"
                            );
                            ?>
                        </td>

                        <td>
                            <div class="meal-table-actions">
                                <a
                                    href="view_single_meal.php?id=<?php echo (int) $meal["id"]; ?>"
                                    class="view-btn"
                                >View</a>

                                <a
                                    href="edit_meal.php?id=<?php echo (int) $meal["id"]; ?>"
                                    class="edit-btn"
                                >Edit</a>

                                <a
                                    href="delete_meal.php?id=<?php echo (int) $meal["id"]; ?>"
                                    class="delete-btn"
                                    onclick="return confirm('Are you sure you want to delete this meal?');"
                                >Delete</a>
                            </div>
                        </td>
                    </tr>

                <?php endforeach; ?>

            </tbody>
        </table>
    </div>

    <div
        class="meal-empty-state"
        id="mealEmptyState"
        <?php echo $totalMeals > 0 ? "hidden" : ""; ?>
    >
        <h3>No meals found</h3>
    </div>

</main>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const searchForm = document.getElementById("mealSearchForm");
    const categoryFilter = document.getElementById("categoryFilter");
    const sortFilter = document.getElementById("sortFilter");
    const resultsHeading = document.getElementById("mealResultsHeading");
    const emptyState = document.getElementById("mealEmptyState");
    const tableBody = document.getElementById("mealTableBody");

    const mealRows = Array.from(
        tableBody.querySelectorAll(".meal-row")
    );

    function updateMeals() {
        const query = searchInput.value.trim().toLowerCase();
        const category = categoryFilter.value;
        const sort = sortFilter.value;

        // Sort all rows before applying the filters.
        const sortedRows = [...mealRows].sort(function (a, b) {
            const nameA = a.cells[0].textContent.trim();
            const nameB = b.cells[0].textContent.trim();

            const caloriesA =
                Number(a.cells[3].textContent.trim()) || 0;
            const caloriesB =
                Number(b.cells[3].textContent.trim()) || 0;

            switch (sort) {
                case "az":
                    return nameA.localeCompare(nameB);

                case "za":
                    return nameB.localeCompare(nameA);

                case "low_high":
                    return caloriesA - caloriesB;

                case "high_low":
                    return caloriesB - caloriesA;

                case "newest":
                default:
                    return mealRows.indexOf(b) - mealRows.indexOf(a);
            }
        });

        let visibleCount = 0;

        sortedRows.forEach(function (row) {
            // Append also applies the new sorting order.
            tableBody.appendChild(row);

            const text = (
                row.getAttribute("data-search") || ""
            ).toLowerCase();

            const rowCategory = row.cells[1].textContent.trim();

            const matchesSearch = text.includes(query);
            const matchesCategory =
                category === "" || rowCategory === category;

            const visible = matchesSearch && matchesCategory;

            row.style.display = visible ? "" : "none";

            if (visible) {
                visibleCount++;
            }
        });

        resultsHeading.textContent =
            "Your Meals (" + visibleCount + ")";

        emptyState.hidden = visibleCount > 0;
    }

    searchInput.addEventListener("input", updateMeals);
    categoryFilter.addEventListener("change", updateMeals);
    sortFilter.addEventListener("change", updateMeals);

    searchForm.addEventListener("submit", function (event) {
        event.preventDefault();
        updateMeals();
    });

    updateMeals();
});
</script>


</body>
</html>

<?php
mysqli_stmt_close($stmt);
?>
