<?php
function printr($DATA = array()) {
// **
    // custom function
    // the function header for a custom function is
    // function printr ($DATA = array()) 
    // so you can access the data through $DATA variable
    echo "<pre>";
    print_r($DATA);
    echo "</pre>";
}
?>