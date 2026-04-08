<?php

// Database configuration for LindseyEngine

$host = 'mysql';  // Database host
$db = 'test';  // Database name
$user = 'root';  // Database username
$pass = 'root';  // Database password
$charset = 'utf8mb4';  // Character set

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// Autoloader for wrapper classes
spl_autoload_register(function ($class_name) {
    $file = __DIR__ . '/Library/wrapper/' . $class_name . '.php';
    if (file_exists($file)) {
        include $file;
    }
});

// Include the base LindseyEngine
include __DIR__ . '/Library/engine/_LindseyEngine.php';

?>