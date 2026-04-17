<?php
    // **
    // custom pre-hook goes there
    // ..

    // **
    // calling the parent function
    $res = parent::set($save_data);

    // **
    // custom post-hook goes there
    // ..

    // **
    // done
    return $res;
?>