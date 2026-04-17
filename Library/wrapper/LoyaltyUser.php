<?php

class LoyaltyUser extends _LindseyEngine {

    public function __construct($config) {
        parent::__construct('loyalty_user', $config);
    }

    public function get($parameters) {
        return include __DIR__ . '/../hooks/loyalty_user_get.php';
    }

    public function set($save_data) {
        return include __DIR__ . '/../hooks/loyalty_user_set.php';
    }

    public function delete($id) {
        return include __DIR__ . '/../hooks/loyalty_user_delete.php';
    }

    public function set_verified($id, $target_state) {
        return include __DIR__ . '/../hooks/loyalty_user_set_verified.php';
    }

    public function set_blackist($id, $target_state) {
        return include __DIR__ . '/../hooks/loyalty_user_set_blackist.php';
    }

    public function get_user_number_format($DATA = array()) {
        include_once __DIR__ . '/../custom/get_user_number_format.php';
        return get_user_number_format($DATA);
    }

}

?>