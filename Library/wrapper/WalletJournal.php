<?php

class WalletJournal extends _LindseyEngine {

    public function __construct($config) {
        parent::__construct('wallet_journal', $config);
    }

    public function get($parameters) {
        return include __DIR__ . '/../hooks/wallet_journal_get.php';
    }

    public function set($save_data) {
        return include __DIR__ . '/../hooks/wallet_journal_set.php';
    }

    public function delete($id) {
        return include __DIR__ . '/../hooks/wallet_journal_delete.php';
    }

    public function set_confirmed($id, $target_state) {
        return include __DIR__ . '/../hooks/wallet_journal_set_confirmed.php';
    }

}

?>