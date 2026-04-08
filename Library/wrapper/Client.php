<?php

class Client extends _LindseyEngine {

    public function __construct() {
        parent::__construct('client');
    }

    public function get($id) {
        return include __DIR__ . '/../hooks/client_get.php';
    }

    public function set($save_data) {
        return include __DIR__ . '/../hooks/client_set.php';
    }

    public function delete($id) {
        return include __DIR__ . '/../hooks/client_delete.php';
    }

}

?>