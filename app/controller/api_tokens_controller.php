<?php

include __DIR__ . '/../model/api_token.php';

class ApiTokensController {
    public function index() {
        $apiTokenModel = new ApiToken();
        $tokens = $apiTokenModel->getAll();
        include __DIR__ . '/../view/api_tokens/index.php';
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'token' => bin2hex(random_bytes(20)), // Generate random token
                'name' => $_POST['name'],
                'scopes' => $_POST['scopes']
            ];
            $apiTokenModel = new ApiToken();
            $id = $apiTokenModel->create($data);
            header('Location: ?action=api_tokens&method=edit&id=' . $id);
            exit;
        }
        include __DIR__ . '/../view/api_tokens/add.php';
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ?action=api_tokens');
            exit;
        }
        $apiTokenModel = new ApiToken();
        $token = $apiTokenModel->getById($id);
        if (!$token) {
            header('Location: ?action=api_tokens');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'name' => $_POST['name'],
                'scopes' => $_POST['scopes']
            ];
            $apiTokenModel->update($id, $data);
            header('Location: ?action=api_tokens');
            exit;
        }
        include __DIR__ . '/../view/api_tokens/edit.php';
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $apiTokenModel = new ApiToken();
            $apiTokenModel->delete($id);
        }
        header('Location: ?action=api_tokens');
        exit;
    }
}

?>