<?php

$queue = [];
$front = 0;
$rear = -1;

function enqueue(&$queue, &$rear, $meal) {
    $rear++;
    $queue[$rear] = $meal;
}

function dequeue(&$queue, &$front, &$rear) {
    if ($front > $rear) {
        return null;
    }

    $meal = $queue[$front];
    $front++;

    return $meal;
}
function queuePeek($queue, $front, $rear) {
    if ($front > $rear) {
        return null;
    }

    return $queue[$front];
}
   
function queueIsEmpty($front, $rear) {
    return $front > $rear;

}

?>