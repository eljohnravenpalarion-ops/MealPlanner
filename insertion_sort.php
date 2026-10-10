<?php

function insertionSortMeals(&$meals) {

    for ($i = 1; $i < count($meals); $i++) {

        $currentMeal = $meals[$i];
        $j = $i - 1;

        while ($j >= 0 && strcasecmp($meals[$j]["meal_name"], $currentMeal["meal_name"]) > 0) {

            $meals[$j + 1] = $meals[$j];
            $j--;
        }

        $meals[$j + 1] = $currentMeal;
    }
}

?>