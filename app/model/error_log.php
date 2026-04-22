<?php

include __DIR__ . '/../../_db_config.php';

class ErrorLog {
    private $pdo;

    public function __construct() {
        try {
            include __DIR__ . '/../../_db_config.php';
            global $auth_pdo;
            $this->pdo = $auth_pdo;
        } catch (Exception $e) {
            $this->pdo = null;
        }
    }

    public function getAll($page = 1, $perPage = 10, $filters = []) {
        $offset = ($page - 1) * $perPage;
        $where = [];
        $params = [];
        if (!empty($filters['token'])) {
            $where[] = "token LIKE ?";
            $params[] = '%' . $filters['token'] . '%';
        }
        if (!empty($filters['channel_name'])) {
            $where[] = "channel_name LIKE ?";
            $params[] = '%' . $filters['channel_name'] . '%';
        }
        if (!empty($filters['function_name'])) {
            $where[] = "function_name LIKE ?";
            $params[] = '%' . $filters['function_name'] . '%';
        }
        if (!empty($filters['created_at'])) {
            $where[] = "DATE(created_at) = ?";
            $params[] = $filters['created_at'];
        }
        $whereClause = !empty($where) ? "WHERE " . implode(' AND ', $where) : "";
        $stmt = $this->pdo->prepare("SELECT * FROM error_log $whereClause ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $params[] = $perPage;
        $params[] = $offset;
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalCount($filters = []) {
        if (!$this->pdo) return 0;
        $where = [];
        $params = [];
        if (!empty($filters['token'])) {
            $where[] = "token LIKE ?";
            $params[] = '%' . $filters['token'] . '%';
        }
        if (!empty($filters['channel_name'])) {
            $where[] = "channel_name LIKE ?";
            $params[] = '%' . $filters['channel_name'] . '%';
        }
        if (!empty($filters['function_name'])) {
            $where[] = "function_name LIKE ?";
            $params[] = '%' . $filters['function_name'] . '%';
        }
        if (!empty($filters['created_at'])) {
            $where[] = "DATE(created_at) = ?";
            $params[] = $filters['created_at'];
        }
        $whereClause = !empty($where) ? "WHERE " . implode(' AND ', $where) : "";
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM error_log $whereClause");
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }
}

?>