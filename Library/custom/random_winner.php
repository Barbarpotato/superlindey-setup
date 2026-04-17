<?php
function random_winner($DATA = array()) {
// **
    // custom function
    // the function header for a custom function is
    //     function random_winner ($DATA = array()) 
    // so you can access the data through $DATA variable
	$randomNumber = random_int(1, 1000);

    return $randomNumber;
}
?>