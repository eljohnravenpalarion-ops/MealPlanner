<?php 
session_start();
require_once "db.php";
require_once "linear_search.php";
$message = "";

if ($_SERVER ["REQUEST_METHOD"]=="POST") {
    $meal_name = $_POST ["meal_name"];
   $category = $_POST["category"];

if ($category == "Custom") {

    $category = trim($_POST["custom_category"]);

    if ($category == "") {
        echo "Please enter a custom category.";
        exit();
    }
}

if ($category == "") {
    echo "Category is required.";
    exit();
}
    $day = $_POST ["day"];
    $validDays = [
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday",
    "Sunday"
];

if (!in_array($day, $validDays)) {
    echo "Please select a valid day.";
    exit();
}
    $calories = $_POST ["calories"];
    $ingredients = $_POST ["ingredients"];
    $description = $_POST ["description"];
    if ($meal_name == "" || $category == "" || $day == "" ||
    $calories == "" || $ingredients == "" || $description == "") {

    echo "Please fill in all fields.";
    exit();
}

if (!is_numeric($calories) || $calories < 0) {
    echo "Calories must be a valid number.";
    exit();
}

    $user_id = $_SESSION["user_id"];
    $meal_name = mysqli_real_escape_string($conn, $meal_name);
    $category = mysqli_real_escape_string($conn, $category);
    $day = mysqli_real_escape_string($conn, $day);
    $calories = mysqli_real_escape_string($conn, $calories);
    $ingredients = mysqli_real_escape_string($conn, $ingredients);
    $description = mysqli_real_escape_string($conn, $description);


    $sql = "SELECT * FROM meal
    WHERE user_id = '$user_id'";

$result = mysqli_query($conn, $sql);

$existingMeals = [];

while ($meal = mysqli_fetch_assoc($result)) {
    $existingMeals[] = $meal;
}
if (linearSearchMeal($existingMeals, $day, $category)) { 

    $message = "This day category are already occupied."; 

} else {

    $sql = "INSERT INTO meal  
     (user_id, meal_name, category, day, calories, ingredients, description)  
     VALUES   
     ('$user_id', '$meal_name','$category','$day', '$calories', '$ingredients', '$description')"; 
 
    if (mysqli_query($conn,$sql)) { 
        header("Location: view_meal.php"); 
        exit();  
    }
}
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Meal - Meal Planner</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require_once "sidebar.php"; ?>
   
<?php if ($message != ""): ?>

<div class="warning-popup" id="warningPopup">

    <div class="warning-box">

        <div class="warning-icon">⚠️</div>

        <h2>Meal Already Exists</h2>

        <p><?php echo htmlspecialchars($message); ?></p>

        <button type="button" onclick="closeWarning()">OK</button>

    </div>

</div>

<?php endif; ?>
    
    <h1 class="add-meal-title">Add Meal</h1>

    <div class="form-card">

    <form method="POST">


        <label> Meal Name </label>
       <input type="text" name="meal_name" required>

        <br> <br>

        <label> Category </label>

    <select name="category" required>
    <option value="Breakfast">Breakfast</option>
    <option value="Lunch">Lunch</option>
    <option value="Dinner">Dinner</option>
    <option value="Snacks">Snacks</option>
    <option value="Custom">Custom</option>
    </select> 

        <input type="text" name="custom_category" placeholder="Enter custom category">

        <br> <br>

        <label> Day </label>

<select name="day" required>
    <option value="">Select Day</option>
    <option value="Monday">Monday</option>
    <option value="Tuesday">Tuesday</option>
    <option value="Wednesday">Wednesday</option>
    <option value="Thursday">Thursday</option>
    <option value="Friday">Friday</option>
    <option value="Saturday">Saturday</option>
    <option value="Sunday">Sunday</option>
</select>

        <br> <br>

        <label> Calories </label>
       <input type="number" name="calories" required>

        <br> <br>

        <label>Ingredients:</label>
       <textarea name="ingredients" required></textarea>
        
        <br> <br>
  
        <label>Description:</label>
      <textarea name="description" required></textarea>

        <br> <br>

        <button type="submit">Add Meal</button>
    
    </form>

</div>


<script>

function closeWarning() {
    document.getElementById("warningPopup").style.display = "none";
}

</script>
</body>
</html>