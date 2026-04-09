<?php

class LotteryClientCouponAllocation extends _LindseyEngine {

    public function __construct() {
        parent::__construct('lottery_client_coupon_allocation');
    }

    public function get($parameters) {
        return include __DIR__ . '/../hooks/lottery_client_coupon_allocation_get.php';
    }

    public function set($save_data) {
        return include __DIR__ . '/../hooks/lottery_client_coupon_allocation_set.php';
    }

    public function delete($id) {
        return include __DIR__ . '/../hooks/lottery_client_coupon_allocation_delete.php';
    }

    public function set_confirmed($id, $target_state) {
        return include __DIR__ . '/../hooks/lottery_client_coupon_allocation_set_confirmed.php';
    }

    public function set_suspended($id, $target_state) {
        return include __DIR__ . '/../hooks/lottery_client_coupon_allocation_set_suspended.php';
    }

}

?>