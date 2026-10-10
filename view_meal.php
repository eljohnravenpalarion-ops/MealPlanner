<?php  

session_start(); 

require_once "db.php"; 
require_once "linked_list.php"; 
require_once "insertion_sort.php"; 
require_once "binary_search.php"; 


if (!isset($_SESSION["user_id"])) { 

    header ("Location: login.php"); 
    exit(); 

} 


$user_id = $_SESSION ["user_id"]; 


$search = $_GET ["search"]?? "" ; 
$category = $_GET["category"]?? ""; 
$sort = $_GET ["sort"]??""; 
$exact_search = $_GET["exact_search"] ?? ""; 


$search = mysqli_real_escape_string($conn, $search); 
$category = mysqli_real_escape_string($conn, $category); 
$exact_search = mysqli_real_escape_string($conn, $exact_search); 


$user_id = (int)$user_id; 


$sql = "SELECT * FROM meal 
        WHERE user_id = '$user_id' 
        AND ( 
            meal_name LIKE '%$search%' 
            OR category LIKE '%$search%' 
            OR ingredients LIKE '%$search%' 
            OR day LIKE '%$search%' 
        )"; 


if ($category !="") { 

    $sql .= " AND category = '$category'"; 

} 


$result = mysqli_query ($conn, $sql); 


$meals=[]; 


while ($meal= mysqli_fetch_assoc($result)) { 

    $meals[]=$meal; 

} 


/* CALORIES LOW TO HIGH */

if ($sort=="calories_asc") {  

    for ($i = 0; $i < count($meals) -1;  $i++) { 

        for ($j = 0; $j < count ($meals)-$i -1; $j++) { 

            if ($meals[$j]["calories"] > $meals[$j + 1]["calories"]) { 

                $temp = $meals[$j]; 

                $meals[$j] = $meals[$j+1]; 

                $meals[$j + 1] = $temp; 

            } 

        } 

    } 

} 


/* CALORIES HIGH TO LOW */

if ($sort == "calories_desc") { 

    for ($i = 0; $i < count($meals) - 1; $i++) { 

        for ($j = 0; $j < count($meals) - $i - 1; $j++) { 

            if ($meals[$j]["calories"] < $meals[$j + 1]["calories"]) { 

                $temp = $meals[$j]; 

                $meals[$j] = $meals[$j + 1]; 

                $meals[$j + 1] = $temp; 

            } 

        } 

    } 

} 


/* MEAL NAME A-Z */

if ($sort == "name_asc") { 

    insertionSortMeals($meals); 

} 


/* EXACT SEARCH */

if ($exact_search != "") { 

    insertionSortMeals($meals); 

    $foundMeal = binarySearchMeal($meals, $exact_search); 

    if ($foundMeal) { 

        echo "<h2>Search Result</h2>"; 

        echo "<p>Meal Found: " . 
             htmlspecialchars($foundMeal["meal_name"]) . 
             "</p>"; 

        $meals = [$foundMeal];

    } else { 

        echo "<p>Meal not found.</p>"; 

        $meals = [];

    } 
} 


/* LINKED LIST */

$mealList = new LinkedList(); 


foreach ($meals as $meal) { 

    $mealList->add($meal); 

} 

?> 


<!DOCTYPE html> 

<html lang="en"> 

<head> 

    <meta charset="UTF-8"> 

    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 

    <title>My Meals</title> 

     
    <link rel="stylesheet" href="style.css"> 

</head> 


<body> 


<?php require_once "sidebar.php"; ?>


<?php 

if (isset($_GET["deleted"]) && $_GET["deleted"] == "1") { 

    echo '<p class="success-message">
            Meal deleted successfully.
          </p>'; 

} 

?> 


<h1>My Meals</h1> 


<form method="GET" class="filter-form"> 


    <!-- LIVE SEARCH INPUT -->

    <input 
        type="text" 
        id="liveSearch"
        name="search" 
        placeholder="Search meals...."
        autocomplete="off"
        value="<?php echo htmlspecialchars($_GET["search"] ?? ""); ?>"
    > 


    <!-- EXACT SEARCH -->

    <input 
        type="text" 
        name="exact_search" 
        placeholder="Exact meal name"
        value="<?php echo htmlspecialchars($_GET["exact_search"] ?? ""); ?>"
    > 


    <!-- CATEGORY -->

    <select name="category">    

        <option value=""> All Categories </option> 

        <option value="Breakfast"
            <?php echo (($_GET["category"] ?? "") == "Breakfast") ? "selected" : ""; ?>>
            Breakfast
        </option> 

        <option value="Lunch"
            <?php echo (($_GET["category"] ?? "") == "Lunch") ? "selected" : ""; ?>>
            Lunch
        </option> 

        <option value="Dinner"
            <?php echo (($_GET["category"] ?? "") == "Dinner") ? "selected" : ""; ?>>
            Dinner
        </option> 

        <option value="Snacks"
            <?php echo (($_GET["category"] ?? "") == "Snacks") ? "selected" : ""; ?>>
            Snacks
        </option> 

    </select> 


    <!-- SORT -->

    <select name="sort"> 

        <option value="">Sort By</option> 

        <option value="name_asc"
            <?php echo (($_GET["sort"] ?? "") == "name_asc") ? "selected" : ""; ?>>
            Meal Name A-Z
        </option> 

        <option value="calories_asc"
            <?php echo (($_GET["sort"] ?? "") == "calories_asc") ? "selected" : ""; ?>>
            Calories Low-High
        </option> 

        <option value="calories_desc"
            <?php echo (($_GET["sort"] ?? "") == "calories_desc") ? "selected" : ""; ?>>
            Calories High-Low
        </option> 

    </select> 


    <!-- SEARCH BUTTON LAST -->

    <button type="submit">
        Search
    </button>


</form> 




<?php

$orderedMeals = [];
$current = $mealList->head;

while ($current != null) {
    $orderedMeals[] = $current->data;
    $current = $current->next;
}

$showCategoryHeaders = ($sort == "");

if ($showCategoryHeaders) {

    $groupedMeals = [
        "breakfast" => ["title" => "Breakfast", "meals" => []],
        "lunch" => ["title" => "Lunch", "meals" => []],
        "dinner" => ["title" => "Dinner", "meals" => []],
        "snacks" => ["title" => "Snacks", "meals" => []],
        "pre-workout" => ["title" => "Pre-workout", "meals" => []]
    ];

    $displayNames = [
        "breakfast" => "Breakfast",
        "lunch" => "Lunch",
        "dinner" => "Dinner",
        "snacks" => "Snacks",
        "pre-workout" => "Pre-workout"
    ];

    foreach ($orderedMeals as $meal) {

        $categoryName = trim($meal["category"] ?? "");
        $categoryKey = strtolower($categoryName);

        if ($categoryKey == "") {
            $categoryKey = "uncategorized";
            $categoryName = "Uncategorized";
        }

        if (!isset($groupedMeals[$categoryKey])) {
            $groupedMeals[$categoryKey] = [
                "title" => $displayNames[$categoryKey]
                    ?? ucwords($categoryName),
                "meals" => []
            ];
        }

        $groupedMeals[$categoryKey]["meals"][] = $meal;
    }

    $categoryOrder = [
        "breakfast",
        "lunch",
        "dinner",
        "snacks",
        "pre-workout"
    ];

    uksort($groupedMeals, function ($a, $b) use ($categoryOrder) {

        $posA = array_search($a, $categoryOrder, true);
        $posB = array_search($b, $categoryOrder, true);

        $posA = ($posA === false) ? 99 : $posA;
        $posB = ($posB === false) ? 99 : $posB;

        if ($posA == $posB) {
            return strcmp($a, $b);
        }

        return $posA <=> $posB;
    });

    $displayGroups = $groupedMeals;

} else {

    // Preserve the global sorted order.
    $displayGroups = [
        "sorted" => [
            "title" => "",
            "meals" => $orderedMeals
        ]
    ];
}

foreach ($displayGroups as $categoryKey => $group) {

    if ($showCategoryHeaders) {
?>

    <h2 class="category-header"
        data-category="<?php echo htmlspecialchars($categoryKey); ?>">
        <?php echo htmlspecialchars($group["title"]); ?>
    </h2>

<?php
    }

    foreach ($group["meals"] as $meal) {
?>

    <div class="meal-card"
         data-category="<?php echo htmlspecialchars(strtolower($meal["category"])); ?>">

        <h2><?php echo htmlspecialchars($meal["meal_name"]); ?></h2>

        <p><strong>Category:</strong>
            <?php echo htmlspecialchars($meal["category"]); ?>
        </p>

        <p><strong>Day:</strong>
            <?php echo htmlspecialchars($meal["day"]); ?>
        </p>

        <p><strong>Calories:</strong>
            <?php echo htmlspecialchars($meal["calories"]); ?>
        </p>

        <p><strong>Ingredients:</strong>
            <?php echo htmlspecialchars($meal["ingredients"]); ?>
        </p>

        <p><strong>Description:</strong>
            <?php echo htmlspecialchars($meal["description"]); ?>
        </p>

        <div class="meal-actions">

            <a class="action-button" href="recent_meal.php">
                Recently Viewed
            </a>

            <a class="action-button"
               href="view_single_meal.php?id=<?php echo $meal["id"]; ?>">
                View
            </a>

            <a class="action-button"
               href="edit_meal.php?id=<?php echo $meal["id"]; ?>">
                Edit
            </a>

            <a class="action-button delete-button"
               href="delete_meal.php?id=<?php echo $meal["id"]; ?>"
               onclick="return confirm('Are you sure to delete this meal?');">
                Delete
            </a>

        </div>

    </div>

<?php
    }
}
?>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("liveSearch");
    const mealCards = document.querySelectorAll(".meal-card");
    const categoryHeaders = document.querySelectorAll(".category-header");

    if (!searchInput) return;

    function filterMeals() {
        const query = searchInput.value.trim().toLowerCase();

        mealCards.forEach(function (card) {
            const title = card.querySelector("h2");
            const mealName = title
                ? title.textContent.trim().toLowerCase()
                : "";

            card.style.display =
                query === "" || mealName.startsWith(query)
                    ? ""
                    : "none";
        });

        categoryHeaders.forEach(function (header) {
            const category = (header.dataset.category || "")
                .trim().toLowerCase();

            const hasVisibleMeal = Array.from(mealCards).some(
                function (card) {
                    return (
                        (card.dataset.category || "").toLowerCase() === category &&
                        card.style.display !== "none"
                    );
                }
            );

            header.style.display =
                query === "" || hasVisibleMeal ? "" : "none";
        });
    }

    searchInput.addEventListener("input", filterMeals);
    filterMeals();
});
</script>



</body> 
</html>