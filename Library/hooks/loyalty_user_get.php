<?php
    // **
    // custom pre-hook goes there
    // ..

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