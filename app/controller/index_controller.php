<?php

include __DIR__ . '/../model/error_log.php';
include __DIR__ . '/../model/api_token.php';

class IndexController {
    public function index() {
        // Get counts
        $errorLogModel = new ErrorLog();
        $errorLogCount = $errorLogModel->getTotalCount();

        $apiTokenModel = new ApiToken();
        $apiTokenCount = $apiTokenModel->getTotalCount();

        // Display dashboard
        include __DIR__ . '/../view/index.php';
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location: ?action=login');
        exit;
    }
}

?>