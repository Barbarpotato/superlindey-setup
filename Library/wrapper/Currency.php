<?php

class Currency extends _LindseyEngine {

    public function __construct() {
        parent::__construct('currency');
    }

    public function get($id) {
        return include __DIR__ . '/../hooks/currency_get.php';
    }

    public function set($save_data) {
        return include __DIR__ . '/../hooks/currency_set.php';
    }

    public function delete($id) {
        return include __DIR__ . '/../hooks/currency_delete.php';
    }

    public function set_confirmed($id, $target_state) {
        return include __DIR__ . '/../hooks/currency_set_confirmed.php';
    }

    public function set_rejected($id, $target_state) {
        return include __DIR__ . '/../hooks/currency_set_rejected.php';
    }

}

?>