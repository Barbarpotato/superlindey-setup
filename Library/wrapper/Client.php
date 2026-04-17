<?php

class Client extends _LindseyEngine {

    public function __construct($config) {
        parent::__construct('client', $config);
    }

    public function get($parameters) {
        return include __DIR__ . '/../hooks/client_get.php';
    }

    public function set($save_data) {
        return include __DIR__ . '/../hooks/client_set.php';
    }

    public function delete($id) {
        return include __DIR__ . '/../hooks/client_delete.php';
    }

    public function random_client($DATA = array()) {
        include_once __DIR__ . '/../custom/random_client.php';
        return random_client($DATA);
    }

}

?>