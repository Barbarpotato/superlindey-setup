<?php

class WalletJournalType extends _LindseyEngine {

    public function __construct() {
        parent::__construct('wallet_journal_type');
    }

    public function get($parameters) {
        return include __DIR__ . '/../hooks/wallet_journal_type_get.php';
    }

    public function set($save_data) {
        return include __DIR__ . '/../hooks/wallet_journal_type_set.php';
    }

    public function delete($id) {
        return include __DIR__ . '/../hooks/wallet_journal_type_delete.php';
    }

}

?>