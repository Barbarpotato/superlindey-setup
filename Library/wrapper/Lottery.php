<?php

class Lottery extends _LindseyEngine {

    public function __construct($config) {
        parent::__construct('lottery', $config);
    }

    public function get($parameters) {
        return include __DIR__ . '/../hooks/lottery_get.php';
    }

    public function set($save_data) {
        return include __DIR__ . '/../hooks/lottery_set.php';
    }

    public function delete($id) {
        return include __DIR__ . '/../hooks/lottery_delete.php';
    }

    public function set_published($id, $target_state) {
        return include __DIR__ . '/../hooks/lottery_set_published.php';
    }

    public function set_ready($id, $target_state) {
        return include __DIR__ . '/../hooks/lottery_set_ready.php';
    }

    public function set_closed($id, $target_state) {
        return include __DIR__ . '/../hooks/lottery_set_closed.php';
    }

    public function random_winner($DATA = array()) {
        include_once __DIR__ . '/../custom/random_winner.php';
        return random_winner($DATA);
    }

}

?>