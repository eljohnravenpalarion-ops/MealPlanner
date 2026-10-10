<?php

function binarySearchMeal($meals, $target) {

    $left = 0;
    $right = count($meals) - 1;

    while ($left <= $right) {

        $middle = floor(($left + $right) / 2);

        $comparison = strcasecmp(
            $meals[$middle]["meal_name"],
            $target
        );

        if ($comparison == 0) {
            return $meals[$middle];
        }

        if ($comparison < 0) {
            $left = $middle + 1;
        } else {
            $right = $middle - 1;
        }
    }

    return null;
}

?>