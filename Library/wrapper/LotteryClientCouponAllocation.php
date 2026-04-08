<?php

class LotteryClientCouponAllocation extends _LindseyEngine {

    public function __construct() {
        parent::__construct('lottery_client_coupon_allocation');
    }

    public function set_confirmed($id, $target_state) {
        return include 'Library/hooks/lottery_client_coupon_allocation_set_confirmed.php';
    }

    public function set_suspended($id, $target_state) {
        return include 'Library/hooks/lottery_client_coupon_allocation_set_suspended.php';
    }

}

?>