<?php

class LoyaltyToken extends _LindseyEngine {

    public function __construct($config) {
        parent::__construct('loyalty_token', $config);
    }

    public function get($parameters) {
        return include __DIR__ . '/../hooks/loyalty_token_get.php';
    }

    public function set($save_data) {
        return include __DIR__ . '/../hooks/loyalty_token_set.php';
    }

    public function delete($id) {
        return include __DIR__ . '/../hooks/loyalty_token_delete.php';
    }

}

?>