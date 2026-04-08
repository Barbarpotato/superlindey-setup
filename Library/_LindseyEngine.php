<?php

class _LindseyEngine {

    private $pdo;
    private $model_config;
    private $model_name;

    public function __construct($model_name) {
        // Assume $pdo is available globally
        global $pdo;
        $this->pdo = $pdo;
        $config = json_decode(file_get_contents('Library/config.json'), true);
        foreach ($config['object_models'] as $model) {
            if ($model['model']['name'] == $model_name) {
                $this->model_config = $model['model'];
                $this->model_name = $model_name;
                break;
            }
        }
    }

    public function get($id) {
        $table = $this->model_name;
        $sql = "SELECT * FROM $table WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function set($save_data) {
        $table = $this->model_name;
        $fields = $this->model_config['fields'];
        $savable_fields = array_filter($fields, function($f) { return $f['can_be_saved']; });
        if (isset($save_data['id'])) {
            // update
            $set_parts = [];
            $params = [];
            foreach ($savable_fields as $field) {
                if (isset($save_data[$field['name']])) {
                    $set_parts[] = $field['name'] . ' = ?';
                    $params[] = $save_data[$field['name']];
                }
            }
            $field_names = array_column($fields, 'name');
            if (in_array('updated_at', $field_names)) {
                $set_parts[] = 'updated_at = CURRENT_TIMESTAMP';
            }
            $sql = "UPDATE $table SET " . implode(', ', $set_parts) . " WHERE id = ?";
            $params[] = $save_data['id'];
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } else {
            // insert
            $cols = [];
            $placeholders = [];
            $params = [];
            foreach ($savable_fields as $field) {
                if (isset($save_data[$field['name']])) {
                    $cols[] = $field['name'];
                    $placeholders[] = '?';
                    $params[] = $save_data[$field['name']];
                }
            }
            $field_names = array_column($fields, 'name');
            if (in_array('created_at', $field_names) && !in_array('created_at', $cols)) {
                $cols[] = 'created_at';
                $placeholders[] = 'CURRENT_TIMESTAMP';
            }
            if (in_array('updated_at', $field_names) && !in_array('updated_at', $cols)) {
                $cols[] = 'updated_at';
                $placeholders[] = 'CURRENT_TIMESTAMP';
            }
            $sql = "INSERT INTO $table (" . implode(', ', $cols) . ") VALUES (" . implode(', ', $placeholders) . ")";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $this->pdo->lastInsertId();
        }
    }

    public function delete($id) {
        $table = $this->model_name;
        $sql = "DELETE FROM $table WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function set_published($id, $target_state) {
        $table = $this->model_name;
        $fields = $this->model_config['fields'];
        $set_parts = [];
        $params = [];
        $field_names = array_column($fields, 'name');
        if (in_array('state', $field_names)) {
            $set_parts[] = 'state = ?';
            $params[] = $target_state;
        }
        foreach ($fields as $field) {
            if (isset($field['auto_generated_by_state']) && $field['auto_generated_by_state'] == $target_state) {
                if ($field['type'] == 'boolean') {
                    $set_parts[] = $field['name'] . ' = 1';
                } elseif ($field['type'] == 'timestamp') {
                    $set_parts[] = $field['name'] . ' = CURRENT_TIMESTAMP';
                } else {
                    $set_parts[] = $field['name'] . ' = \'\'';
                }
            }
        }
        if (in_array('updated_at', $field_names)) {
            $set_parts[] = 'updated_at = CURRENT_TIMESTAMP';
        }
        if (!empty($set_parts)) {
            $sql = "UPDATE $table SET " . implode(', ', $set_parts) . " WHERE id = ?";
            $params[] = $id;
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        }
        return true;
    }

    public function set_ready($id, $target_state) {
        $table = $this->model_name;
        $fields = $this->model_config['fields'];
        $set_parts = [];
        $params = [];
        $field_names = array_column($fields, 'name');
        if (in_array('state', $field_names)) {
            $set_parts[] = 'state = ?';
            $params[] = $target_state;
        }
        foreach ($fields as $field) {
            if (isset($field['auto_generated_by_state']) && $field['auto_generated_by_state'] == $target_state) {
                if ($field['type'] == 'boolean') {
                    $set_parts[] = $field['name'] . ' = 1';
                } elseif ($field['type'] == 'timestamp') {
                    $set_parts[] = $field['name'] . ' = CURRENT_TIMESTAMP';
                } else {
                    $set_parts[] = $field['name'] . ' = \'\'';
                }
            }
        }
        if (in_array('updated_at', $field_names)) {
            $set_parts[] = 'updated_at = CURRENT_TIMESTAMP';
        }
        if (!empty($set_parts)) {
            $sql = "UPDATE $table SET " . implode(', ', $set_parts) . " WHERE id = ?";
            $params[] = $id;
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        }
        return true;
    }

    public function set_closed($id, $target_state) {
        $table = $this->model_name;
        $fields = $this->model_config['fields'];
        $set_parts = [];
        $params = [];
        $field_names = array_column($fields, 'name');
        if (in_array('state', $field_names)) {
            $set_parts[] = 'state = ?';
            $params[] = $target_state;
        }
        foreach ($fields as $field) {
            if (isset($field['auto_generated_by_state']) && $field['auto_generated_by_state'] == $target_state) {
                if ($field['type'] == 'boolean') {
                    $set_parts[] = $field['name'] . ' = 1';
                } elseif ($field['type'] == 'timestamp') {
                    $set_parts[] = $field['name'] . ' = CURRENT_TIMESTAMP';
                } else {
                    $set_parts[] = $field['name'] . ' = \'\'';
                }
            }
        }
        if (in_array('updated_at', $field_names)) {
            $set_parts[] = 'updated_at = CURRENT_TIMESTAMP';
        }
        if (!empty($set_parts)) {
            $sql = "UPDATE $table SET " . implode(', ', $set_parts) . " WHERE id = ?";
            $params[] = $id;
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        }
        return true;
    }

    public function set_confirmed($id, $target_state) {
        $table = $this->model_name;
        $fields = $this->model_config['fields'];
        $set_parts = [];
        $params = [];
        $field_names = array_column($fields, 'name');
        if (in_array('state', $field_names)) {
            $set_parts[] = 'state = ?';
            $params[] = $target_state;
        }
        foreach ($fields as $field) {
            if (isset($field['auto_generated_by_state']) && $field['auto_generated_by_state'] == $target_state) {
                if ($field['type'] == 'boolean') {
                    $set_parts[] = $field['name'] . ' = 1';
                } elseif ($field['type'] == 'timestamp') {
                    $set_parts[] = $field['name'] . ' = CURRENT_TIMESTAMP';
                } else {
                    $set_parts[] = $field['name'] . ' = \'\'';
                }
            }
        }
        if (in_array('updated_at', $field_names)) {
            $set_parts[] = 'updated_at = CURRENT_TIMESTAMP';
        }
        if (!empty($set_parts)) {
            $sql = "UPDATE $table SET " . implode(', ', $set_parts) . " WHERE id = ?";
            $params[] = $id;
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        }
        return true;
    }

    public function set_rejected($id, $target_state) {
        $table = $this->model_name;
        $fields = $this->model_config['fields'];
        $set_parts = [];
        $params = [];
        $field_names = array_column($fields, 'name');
        if (in_array('state', $field_names)) {
            $set_parts[] = 'state = ?';
            $params[] = $target_state;
        }
        foreach ($fields as $field) {
            if (isset($field['auto_generated_by_state']) && $field['auto_generated_by_state'] == $target_state) {
                if ($field['type'] == 'boolean') {
                    $set_parts[] = $field['name'] . ' = 1';
                } elseif ($field['type'] == 'timestamp') {
                    $set_parts[] = $field['name'] . ' = CURRENT_TIMESTAMP';
                } else {
                    $set_parts[] = $field['name'] . ' = \'\'';
                }
            }
        }
        if (in_array('updated_at', $field_names)) {
            $set_parts[] = 'updated_at = CURRENT_TIMESTAMP';
        }
        if (!empty($set_parts)) {
            $sql = "UPDATE $table SET " . implode(', ', $set_parts) . " WHERE id = ?";
            $params[] = $id;
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        }
        return true;
    }

}

?>