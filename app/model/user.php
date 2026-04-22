<?php

include __DIR__ . '/../../_db_config.php';

class User {
    private $pdo;

    public function __construct() {
        include __DIR__ . '/../../_db_config.php';
        global $auth_pdo;
        $this->pdo = $auth_pdo;
    }

    public function authenticate($username, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM user_login WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}

?>