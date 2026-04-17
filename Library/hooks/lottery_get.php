<?php
    // **
    // custom pre-hook goes there
    // ..

	if(isset($_PUBLIC['member_number'])){
    	$parametrs['member_number'] = $_PUBLIC['member_number']; 
    }

    // **
    // calling the parent function
    $res = parent::get($parameters);

    // **
    // custom post-hook goes there
    // ..

    // **
    // done
    return $res;
?>