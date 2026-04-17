<?php

class APP {

    public function printr($DATA = array()) {
        include_once __DIR__ . '/../custom/printr.php';
        return printr($DATA);
    }

}

?>