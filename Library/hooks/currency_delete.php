<?php
    // **
    // custom pre-hook goes there
    // ..

    // **
    // calling the parent function
    $res = parent::delete($id);

    // **
    // custom post-hook goes there
    // ..

    // **
    // done
    return $res;
?>