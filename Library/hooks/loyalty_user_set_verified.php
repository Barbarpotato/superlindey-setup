<?php
    // **
    // custom pre-hook goes there
    // ..

    // **
    // calling the parent function
    $res = parent::set_verified($id, $target_state);

    // **
    // custom post-hook goes there
    // ..

    // **
    // done
    return $res;
?>