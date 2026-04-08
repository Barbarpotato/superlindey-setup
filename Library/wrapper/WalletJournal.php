<?php

class WalletJournal extends _LindseyEngine {

    public function __construct() {
        parent::__construct('wallet_journal');
    }

    public function get($id) {
        return include __DIR__ . '/../hooks/wallet_journal_get.php';
    }

    public function set($save_data) {
        return include __DIR__ . '/../hooks/wallet_journal_set.php';
    }

    public function delete($id) {
        return include __DIR__ . '/../hooks/wallet_journal_delete.php';
    }

    public function set_published($id, $target_state) {
        return include __DIR__ . '/../hooks/wallet_journal_set_published.php';
    }

}

?>