<?php

$config = json_decode(file_get_contents('Library/config.json'), true);

$object_title = $config['object_title'];

$all_models = [];
foreach ($config['object_models'] as $model_data) {
    $model = $model_data['model'];
    if (isset($model['name'])) {
        $all_models[] = ['name' => $model['name'], 'type' => 'main'];
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

// create hooks

foreach ($config['hooks'] as $hook) {

    $filename = 'Library/hooks/' . $hook['object_name'] . '_' . $hook['name'] . '.php';

    file_put_contents($filename, $hook['function_text']);

}

// custom functions

foreach ($config['custom_functions'] as $func) {

    $filename = 'Library/custom/' . $func['name'] . '.php';

    $function_body = trim(str_replace(['<?php', '?>'], '', $func['function_text']));
    $content = "<?php\nfunction " . $func['name'] . "(\$DATA = array()) {\n" . $function_body . "\n}\n?>";

    file_put_contents($filename, $content);

}

// create folders based on channels
foreach ($config['channels'] as $channel) {
    $channel_name = $channel['channel_name'];
    $folders = explode('/', $channel_name);
    $path = '';
    foreach ($folders as $folder) {
        if (!empty($folder)) {
            $path .= $folder . '/';
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }
    }
}

// channel pages
foreach ($config['channels'] as $channel) {
    if (isset($channel['pages'])) {
        foreach ($channel['pages'] as $page) {
            $code = $page['code'];
            foreach ($all_models as $model_info) {
                $obj = $model_info['name'];
                $class_name = str_replace(' ', '', ucwords(str_replace('_', ' ', $obj)));
                $code = str_replace("new $class_name()", '$' . $object_title . '->' . $obj, $code);
            }
            $channel_name = $channel['channel_name'];
            $filename = $channel_name . '/' . ltrim($page['endpoint'], '/');
            file_put_contents($filename, $code);
        }
    }
}

// generate additional hooks from object_models
foreach ($config['object_models'] as $model_data) {
    $model = $model_data['model'];
    if (!isset($model['name'])) continue;
    $object_name = $model['name'];
    
    // basic operations
    $basic_ops = ['get', 'set', 'delete'];
    foreach ($basic_ops as $op) {
        $filename = 'Library/hooks/' . $object_name . '_' . $op . '.php';
        if (!file_exists($filename)) {
            $params = '';
            if ($op == 'get' || $op == 'delete') {
                $params = '$parameters';
            } elseif ($op == 'set') {
                $params = '$save_data';
            }
            $content = "<?php\n    // **\n    // custom pre-hook goes there\n    // ..\n\n    // **\n    // calling the parent function\n    \$res = parent::$op($params);\n\n    // **\n    // custom post-hook goes there\n    // ..\n\n    // **\n    // done\n    return \$res;\n?>";
            file_put_contents($filename, $content);
        }
    }
    
    // state operations from state_variables
    if (isset($model['state_machine']['state_variables'])) {
        foreach ($model['state_machine']['state_variables'] as $state_var) {
            if (isset($state_var['name'])) {
                $op = 'set_' . $state_var['name'];
                $filename = 'Library/hooks/' . $object_name . '_' . $op . '.php';
                if (!file_exists($filename)) {
                    $content = "<?php\n    // **\n    // custom pre-hook goes there\n    // ..\n\n    // **\n    // calling the parent function\n    \$res = parent::$op(\$id, \$target_state);\n\n    // **\n    // custom post-hook goes there\n    // ..\n\n    // **\n    // done\n    return \$res;\n?>";
                    file_put_contents($filename, $content);
                }
            }
        }
    }
}

// also for grouping_objects and child_objects
foreach ($config['object_models'] as $model_data) {
    $model = $model_data['model'];
    if (!isset($model['name'])) continue;
    
    $sub_models = [];
    if (isset($model['grouping_objects'])) {
        $sub_models = array_merge($sub_models, $model['grouping_objects']);
    }
    if (isset($model['child_objects'])) {
        $sub_models = array_merge($sub_models, $model['child_objects']);
    }
    
    foreach ($sub_models as $sub_model) {
        if (!isset($sub_model['name'])) continue;
        $object_name = $sub_model['name'];
        
        // basic operations
        $basic_ops = ['get', 'set', 'delete'];
        foreach ($basic_ops as $op) {
            $filename = 'Library/hooks/' . $object_name . '_' . $op . '.php';
            if (!file_exists($filename)) {
                $params = '';
                if ($op == 'get' || $op == 'delete') {
                    $params = '$parameters';
                } elseif ($op == 'set') {
                    $params = '$save_data';
                }
                $content = "<?php\n    // **\n    // custom pre-hook goes there\n    // ..\n\n    // **\n    // calling the parent function\n    \$res = parent::$op($params);\n\n    // **\n    // custom post-hook goes there\n    // ..\n\n    // **\n    // done\n    return \$res;\n?>";
                file_put_contents($filename, $content);
            }
        }
        
        // state operations from state_variables
        if (isset($sub_model['state_machine']['state_variables'])) {
            foreach ($sub_model['state_machine']['state_variables'] as $state_var) {
                if (isset($state_var['name'])) {
                    $op = 'set_' . $state_var['name'];
                    $filename = 'Library/hooks/' . $object_name . '_' . $op . '.php';
                    if (!file_exists($filename)) {
                        $content = "<?php\n    // **\n    // custom pre-hook goes there\n    // ..\n\n    // **\n    // calling the parent function\n    \$res = parent::$op(\$id, \$target_state);\n\n    // **\n    // custom post-hook goes there\n    // ..\n\n    // **\n    // done\n    return \$res;\n?>";
                        file_put_contents($filename, $content);
                    }
                }
            }
        }
    }
}

// object models - generate _LindseyEngine.php

$content = $config['_LindseyEngine'];
$content = str_replace("'main_objects'", "'object_models'", $content);

file_put_contents('Library/engine/_LindseyEngine.php', $content);

// generate model classes for all object_models and sub_models
$all_models = [];
foreach ($config['object_models'] as $model_data) {
    $model = $model_data['model'];
    if (isset($model['name'])) {
        $all_models[] = ['name' => $model['name'], 'type' => 'main'];
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
    $model_content = "<?php\n\nclass $class_name extends _LindseyEngine {\n\n    public function __construct(\$config) {\n        parent::__construct('$obj', \$config);\n    }\n\n";

    $methods = ['get', 'set', 'delete'];
    // find state_variables for this model
    foreach ($config['object_models'] as $md) {
        $found = false;
        if (isset($md['model']['name']) && $md['model']['name'] == $obj) {
            $target = $md['model'];
            $found = true;
        } elseif (isset($md['grouping_objects'])) {
            foreach ($md['grouping_objects'] as $group) {
                if (isset($group['name']) && $group['name'] == $obj) {
                    $target = $group;
                    $found = true;
                    break;
                }
            }
        }
        if (!$found && isset($md['child_objects'])) {
            foreach ($md['child_objects'] as $child) {
                if (isset($child['name']) && $child['name'] == $obj) {
                    $target = $child;
                    $found = true;
                    break;
                }
            }
        }
        if ($found && isset($target['state_machine']['state_variables'])) {
            foreach ($target['state_machine']['state_variables'] as $state_var) {
                if (isset($state_var['name'])) {
                    $methods[] = 'set_' . $state_var['name'];
                }
            }
        }
    }
    $methods = array_unique($methods);

    foreach ($methods as $method) {
        $params = '';
        if (strpos($method, 'set_') === 0 && $method != 'set') {
            $params = '$id, $target_state';
        } elseif ($method == 'set') {
            $params = '$save_data';
        } elseif ($method == 'delete') {
            $params = '$id';
        } elseif ($method == 'get') {
            $params = '$parameters';
        }
        $model_content .= "    public function $method($params) {\n        return include __DIR__ . '/../hooks/{$obj}_{$method}.php';\n    }\n\n";
    }

    // custom functions
    foreach ($config['custom_functions'] as $func) {
        $expected_object_name = '$' . $object_title . '->' . $obj;
        if ($func['object_name'] === $expected_object_name) {
            $model_content .= "    public function " . $func['name'] . "(\$DATA = array()) {\n        return " . $func['name'] . "(\$DATA);\n    }\n\n";
        }
    }

    $model_content .= "}\n\n?>";

    file_put_contents("Library/wrapper/$class_name.php", $model_content);
}

// generate APP class if needed
$app_methods = [];
foreach ($config['custom_functions'] as $func) {
    if ($func['object_name'] === '$APP') {
        $app_methods[] = $func['name'];
    }
}
if (!empty($app_methods)) {
    $app_content = "<?php\n\nclass APP {\n\n";
    foreach ($app_methods as $method) {
        $app_content .= "    public function $method(\$DATA = array()) {\n        include_once __DIR__ . '/../custom/$method.php';\n        return $method(\$DATA);\n    }\n\n";
    }
    $app_content .= "}\n\n?>";
    file_put_contents("Library/wrapper/APP.php", $app_content);
}

echo "Generated";

?>