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

class Bootloader {

    public function run() {
        // Load the configuration
        $config = json_decode(file_get_contents('Library/config.json'), true);

        // Create global APP object for custom functions
        global $APP;
        $APP = new APP();

        // Iterate over each object_title in object_models
        foreach ($config['object_models'] as $title => $model_configs) {
            // Create global object for this title
            global $${title};
            $${title} = new stdClass();

            $all_models = [];
            foreach ($model_configs as $model_data) {
                if (isset($model_data['model'])) {
                    $model = $model_data['model'];
                    if (isset($model['name'])) {
                        $all_models[] = ['name' => $model['name'], 'type' => 'main'];
                    }
                }
                if (isset($model_data['grouping_objects'])) {
                    foreach ($model_data['grouping_objects'] as $group) {
                        if (isset($group['name'])) {
                            $all_models[] = ['name' => $group['name'], 'type' => 'group'];
                        }
                    }
                }
                if (isset($model_data['child_objects'])) {
                    foreach ($model_data['child_objects'] as $child) {
                        if (isset($child['name'])) {
                            $all_models[] = ['name' => $child['name'], 'type' => 'child'];
                        }
                    }
                }
            }

            foreach ($all_models as $model_info) {
                $obj = $model_info['name'];
                $class_name = str_replace(' ', '', ucwords(str_replace('_', ' ', $obj)));
                $${title}->{$obj} = new $class_name($config);
            }
        }

        // Parse the request URI
        $requestUri = $_SERVER['REQUEST_URI'];

        // the request endpoint of uri must only have 3 parts take the middle parts for the endpoint
        $requestEndpoint = explode('/', $requestUri)[2];

        // Find the API channel
        $apiChannel = null;
        foreach ($config['channels'] as $channel) {
            // re routing request to the specific endpoint channel
            if ($channel['type'] === 'api' && $requestEndpoint ==  $channel["channel_name"]) {
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

        // Include policy if exists
        if (isset($apiChannel['include'])) {
            $policyFile = ltrim($baseUrl, '/') . '/' . $apiChannel['include']['name'];
            if (file_exists($policyFile)) {
                include $policyFile;
            }
        }

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