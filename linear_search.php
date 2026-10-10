<?php

function linearSearchMeal($meals, $day, $category)
{
    foreach ($meals as $meal) {

        if ($meal["day"] == $day && $meal["category"] == $category) {
            return true;
        }

    }

    return false;
}

?>