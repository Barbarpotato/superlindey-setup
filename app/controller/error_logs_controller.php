<?php

include __DIR__ . '/../model/error_log.php';

class ErrorLogsController {
    public function index() {
        $page = $_GET['page'] ?? 1;
        $perPage = 10;
        $filters = [
            'token' => $_GET['token'] ?? '',
            'channel_name' => $_GET['channel_name'] ?? '',
            'function_name' => $_GET['function_name'] ?? '',
            'created_at' => $_GET['created_at'] ?? ''
        ];
        $errorLogModel = new ErrorLog();
        $logs = $errorLogModel->getAll($page, $perPage, $filters);
        foreach ($logs as &$log) {
            $date = new DateTime($log['created_at'], new DateTimeZone('UTC'));
            $date->setTimezone(new DateTimeZone('Asia/Makassar'));
            $log['created_at_formatted'] = $date->format('Y-m-d H:i:s');
        }
        $total = $errorLogModel->getTotalCount($filters);
        $totalPages = ceil($total / $perPage);
        include __DIR__ . '/../view/error_logs/index.php';
    }
}

?>