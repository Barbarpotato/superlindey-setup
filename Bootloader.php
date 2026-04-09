<?php

include '_db_config.php';

// Autoloader for wrapper classes
spl_autoload_register(function ($class_name) {
    $file = __DIR__ . '/Library/wrapper/' . $class_name . '.php';
    if (file_exists($file)) {
        include $file;
    }
});

// Include the base LindseyEngine
include __DIR__ . '/Library/engine/_LindseyEngine.php';

// Include all custom functions
$custom_dir = __DIR__ . '/Library/custom/';
if (is_dir($custom_dir)) {
    foreach (glob($custom_dir . '*.php') as $custom_file) {
        include $custom_file;
    }
}

class Bootloader {

    public function run() {
        // Load the configuration
        $config = json_decode(file_get_contents('Library/config.json'), true);

        // Find the API channel
        $apiChannel = null;
        foreach ($config['channels'] as $channel) {
            if ($channel['type'] === 'api') {
                // Set the API channel
                header('Content-Type: application/json; charset=utf-8');
                $apiChannel = $channel;
                break;
            }
        }

        if (!$apiChannel) {
            http_response_code(500);
            echo json_encode(['error' => 'API channel not found in config']);
            return;
        }

        // Construct the base URL from channel_name
        $baseUrl = '/' . $apiChannel['channel_name']; // e.g., "/api/v1"

        // Parse the request URI
        $requestUri = $_SERVER['REQUEST_URI'];

        // Find the position of the base URL
        $pos = strpos($requestUri, $baseUrl);
        if ($pos !== false) {
            // Extract the base path
            $basePath = substr($requestUri, 0, $pos + strlen($baseUrl) + 1); // +1 for trailing /
            $endpoint = substr($requestUri, strlen($basePath));
        } else {
            $endpoint = '';
        }

        // Remove query string
        $endpoint = explode('?', $endpoint)[0];
        
        // Construct the file path based on the base URL
        $filePath = ltrim($baseUrl, '/') . '/' . $endpoint . '.php'; // e.g., "api/v1/wallet_journal.php"

        // Check if the file exists and include it
        if (!empty($endpoint) && file_exists($filePath)) {
            include $filePath;
        } else {
            // Endpoint not found
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
        }
    }
}

?>