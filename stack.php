<?php 


$stack= [];
$top=-1;

function push (&$stack, &$top, $meal){
    $top++;
    $stack[$top] = $meal;

}

function pop (&$stack, &$top) {
if ($top == -1 ) {
    return null;

}

 $meal = $stack [$top];
  unset($stack[$top]);
 $top--;

 return $meal;

 }

 function peek ($stack, $top) {
    if ($top == -1 ){ 
        return null;

    }
    
    return $stack[$top];
 }

 function isEmpty ($top) {
    return $top ==-1;


 }

?>