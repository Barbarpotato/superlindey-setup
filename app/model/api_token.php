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
        $ownership_data_binding = isset($data['ownership_data_binding'])
            ? json_encode($data['ownership_data_binding'], JSON_UNESCAPED_UNICODE)
            : '{}';
        $channel_list = isset($data['channel_list']) ? json_encode($data['channel_list']) : json_encode([]);
        $stmt = $this->pdo->prepare(
            "INSERT INTO api_tokens (token, name, scopes, channel_list, ownership_data_binding, created_at) VALUES (?, ?, ?, ?, ?, NOW())"
        );
        $stmt->execute([
            $data['token'],
            $data['name'],
            $data['scopes'],
            $channel_list,
            $ownership_data_binding
        ]);
        return $this->pdo->lastInsertId();
    }

    public function update($id, $data) {
        $ownership_data_binding = isset($data['ownership_data_binding'])
            ? json_encode($data['ownership_data_binding'], JSON_UNESCAPED_UNICODE)
            : null;
        $channel_list = isset($data['channel_list']) ? json_encode($data['channel_list']) : null;

        if ($ownership_data_binding !== null && $channel_list !== null) {
            $stmt = $this->pdo->prepare(
                "UPDATE api_tokens SET name = ?, scopes = ?, channel_list = ?, ownership_data_binding = ? WHERE id = ?"
            );
            return $stmt->execute([$data['name'], $data['scopes'], $channel_list, $ownership_data_binding, $id]);
        } elseif ($ownership_data_binding !== null) {
            $stmt = $this->pdo->prepare(
                "UPDATE api_tokens SET name = ?, scopes = ?, ownership_data_binding = ? WHERE id = ?"
            );
            return $stmt->execute([$data['name'], $data['scopes'], $ownership_data_binding, $id]);
        } else {
            $stmt = $this->pdo->prepare(
                "UPDATE api_tokens SET name = ?, scopes = ?, channel_list = ? WHERE id = ?"
            );
            return $stmt->execute([$data['name'], $data['scopes'], $channel_list, $id]);
        }
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
