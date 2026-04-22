<?php

include __DIR__ . '/../../_db_config.php';

class ApiToken {
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

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM api_tokens ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM api_tokens WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO api_tokens (token, name, scopes, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$data['token'], $data['name'], $data['scopes']]);
        return $this->pdo->lastInsertId();
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE api_tokens SET name = ?, scopes = ? WHERE id = ?");
        return $stmt->execute([$data['name'], $data['scopes'], $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM api_tokens WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getTotalCount() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM api_tokens");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}

?>