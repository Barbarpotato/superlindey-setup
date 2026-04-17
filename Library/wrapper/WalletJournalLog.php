<?php

class WalletJournalLog extends _LindseyEngine {

    public function __construct($config) {
        parent::__construct('wallet_journal_log', $config);
    }

    public function get($parameters) {
        return include __DIR__ . '/../hooks/wallet_journal_log_get.php';
    }

    public function set($save_data) {
        return include __DIR__ . '/../hooks/wallet_journal_log_set.php';
    }

    public function delete($id) {
        return include __DIR__ . '/../hooks/wallet_journal_log_delete.php';
    }

}

?>