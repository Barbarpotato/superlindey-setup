<?php
/**
 * Hook Scanner
 *
 * Scans all PHP files in Library/wrapper/* and detects functions
 * excluding __construct. Stores the results in generated_function.json.
 */

// Configuration
$configPath = __DIR__ . '/hooks.json';
$appDir = __DIR__ . '/Library/wrapper';
$excludedDirs = ['.', '..']; // Directories to exclude
$excludedFiles = ['APP.php']; // Files to exclude

// Load existing config or create new structure
if (file_exists($configPath)) {
    $config = json_decode(file_get_contents($configPath), true);
    if ($config === null) {
        $config = [];
    }
} else {
    $config = [];
}

// Helper function to determine allowed functions for a model definition
function getAllowedFunctionsForModel($model) {
    $functions = ['get', 'set', 'delete']; // Base CRUD always allowed
    
    // Add state transition functions from state_machine
    if (isset($model['state_machine']['transitions']) && is_array($model['state_machine']['transitions'])) {
        foreach ($model['state_machine']['transitions'] as $transition) {
            if (isset($transition['to'])) {
                $functions[] = 'set_' . $transition['to'];
            }
        }
    }
    
    return array_unique($functions);
}

// Load model definitions from Library/config.json to determine allowed functions
$libraryConfigPath = __DIR__ . '/Library/config.json';
$allowedFunctionsMap = []; // Format: 'object_name' => ['get', 'set', 'delete', 'set_published', ...]
$projectInfo = [];

if (file_exists($libraryConfigPath)) {
    $libraryConfig = json_decode(file_get_contents($libraryConfigPath), true);
    
    // Load project_info
    if (isset($libraryConfig['project_info'])) {
        $projectInfo = $libraryConfig['project_info'];
    }
    
    // Build allowed functions map from object_models
    if (isset($libraryConfig['object_models'])) {
        foreach ($libraryConfig['object_models'] as $group => $items) {
            foreach ($items as $item) {
                // Process main model
                if (isset($item['model']['name'])) {
                    $modelName = $item['model']['name'];
                    $allowedFunctionsMap[$modelName] = getAllowedFunctionsForModel($item['model']);
                }
                // Process grouping_objects
                if (isset($item['grouping_objects'])) {
                    foreach ($item['grouping_objects'] as $groupObj) {
                        if (isset($groupObj['name'])) {
                            $allowedFunctionsMap[$groupObj['name']] = getAllowedFunctionsForModel($groupObj);
                        }
                    }
                }
                // Process child_objects
                if (isset($item['child_objects'])) {
                    foreach ($item['child_objects'] as $childObj) {
                        if (isset($childObj['name'])) {
                            $allowedFunctionsMap[$childObj['name']] = getAllowedFunctionsForModel($childObj);
                        }
                    }
                }
            }
        }
    }
    
    // Add custom functions from custom_functions section
    if (isset($libraryConfig['custom_functions']) && is_array($libraryConfig['custom_functions'])) {
        foreach ($libraryConfig['custom_functions'] as $custom) {
            if (isset($custom['object_name'], $custom['name'])) {
                $obj = $custom['object_name'];
                // Remove leading $ if present (e.g., $APP -> APP)
                if (strpos($obj, '$') === 0) {
                    $obj = substr($obj, 1);
                }
                // Convert to snake_case to match wrapper naming
                $obj = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $obj));
                if (!isset($allowedFunctionsMap[$obj])) {
                    $allowedFunctionsMap[$obj] = [];
                }
                $allowedFunctionsMap[$obj][] = $custom['name'];
            }
        }
    }
}

// Initialize hooks array
$hooks = [];

// Recursively scan app directory
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($appDir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iterator as $file) {
    // Skip if not a PHP file
    if ($file->getExtension() !== 'php') {
        continue;
    }

    // Skip excluded files
    if (in_array($file->getFilename(), $excludedFiles)) {
        continue;
    }

    // Get relative path from app directory to determine object_name
    $relativePath = $file->getPathname();
    $appDirLength = strlen($appDir) + 1; // +1 for directory separator
    $objectName = substr($relativePath, $appDirLength);
    $objectName = str_replace(['/', '.php'], ['_', ''], $objectName);
    // Convert PascalCase to snake_case
    $objectName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $objectName));

    // Read file content
    $content = file_get_contents($file->getPathname());

    // Tokenize to find functions
    $tokens = token_get_all($content);
    $numTokens = count($tokens);
    $i = 0;

    while ($i < $numTokens) {
        $token = $tokens[$i];

        // Check if this is a T_FUNCTION token
        if (is_array($token) && $token[0] === T_FUNCTION) {
            // Get function name by looking ahead
            $functionName = '';
            $j = $i + 1;
            while ($j < $numTokens && is_array($tokens[$j]) && in_array($tokens[$j][0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT])) {
                $j++;
            }

            if ($j < $numTokens && is_array($tokens[$j]) && $tokens[$j][0] === T_STRING) {
                $functionName = $tokens[$j][1];
            }

            // Skip __construct
            if ($functionName === '__construct') {
                $i = $j + 1;
                continue;
            }

            // Collect function body starting from T_FUNCTION token
            $functionBody = '';
            $braceCount = 0;
            $k = $i;
            $inFunction = false;

            while ($k < $numTokens) {
                $currentToken = $tokens[$k];

                if (is_array($currentToken)) {
                    $functionBody .= $currentToken[1];
                } else {
                    $functionBody .= $currentToken;

                    if ($currentToken === '{') {
                        $braceCount++;
                        $inFunction = true;
                    } elseif ($currentToken === '}') {
                        $braceCount--;
                        if ($inFunction && $braceCount === 0) {
                            break;
                        }
                    }
                }
                $k++;
            }

            // Add to hooks array
            if ($functionName !== '') {
                $hooks[] = [
                    'name' => $functionName,
                    'function_text' => "<?php\n" . $functionBody . "\n?>",
                    'object_name' => $objectName
                ];
            }

            $i = $k + 1;
            continue;
        }

        $i++;
    }
}

// Validate that all scanned hooks are allowed based on model configuration
foreach ($hooks as $hook) {
    $objectName = $hook['object_name'];
    $functionName = $hook['name'];
    
    if (!isset($allowedFunctionsMap[$objectName])) {
        die("Error: Object '{$objectName}' not found in Library/config.json object_models\n");
    }
    
    if (!in_array($functionName, $allowedFunctionsMap[$objectName])) {
        die("Error: Forbidden function '{$functionName}' for object '{$objectName}'. Allowed: " . implode(', ', $allowedFunctionsMap[$objectName]) . "\n");
    }
}

// Update config with hooks and project_info
$config['project_info'] = $projectInfo;
$config['hooks'] = $hooks;

// Save config back to file
$result = file_put_contents($configPath, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

if ($result === false) {
    echo "Error: Failed to write to config.json\n";
} else {
    echo "Success: Scanned " . count($hooks) . " functions and updated hooks.json\n";
}
