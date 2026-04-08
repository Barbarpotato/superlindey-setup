<?php

$config = json_decode(file_get_contents('Library/config.json'), true);

// create hooks

foreach ($config['hooks'] as $hook) {

    $filename = 'Library/hooks/' . $hook['object_name'] . '_' . $hook['name'] . '.php';

    file_put_contents($filename, $hook['function_text']);

}

// custom functions

foreach ($config['custom_functions'] as $func) {

    $filename = 'Library/custom/' . $func['name'] . '.php';

    file_put_contents($filename, $func['function_text']);

}

// channel pages

foreach ($config['channel_pages'] as $page) {

    $filename = ltrim($page['endpoint'], '/');

    file_put_contents($filename, $page['code']);

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
                $params = '$id';
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
                    $params = '$id';
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

$operations = ['get', 'set', 'delete']; // basic
foreach ($config['object_models'] as $model_data) {
    $model = $model_data['model'];
    if (!isset($model['name'])) continue;
    if (isset($model['state_machine']['state_variables'])) {
        foreach ($model['state_machine']['state_variables'] as $state_var) {
            if (isset($state_var['name'])) {
                $operations[] = 'set_' . $state_var['name'];
            }
        }
    }
    
    // sub models
    $sub_models = [];
    if (isset($model['grouping_objects'])) {
        $sub_models = array_merge($sub_models, $model['grouping_objects']);
    }
    if (isset($model['child_objects'])) {
        $sub_models = array_merge($sub_models, $model['child_objects']);
    }
    
    foreach ($sub_models as $sub_model) {
        if (!isset($sub_model['name'])) continue;
        if (isset($sub_model['state_machine']['state_variables'])) {
            foreach ($sub_model['state_machine']['state_variables'] as $state_var) {
                if (isset($state_var['name'])) {
                    $operations[] = 'set_' . $state_var['name'];
                }
            }
        }
    }
}
$operations = array_unique($operations);

$content = "<?php\n\nclass _LindseyEngine {\n\n    private \$pdo;\n    private \$model_config;\n    private \$model_name;\n\n    public function __construct(\$model_name) {\n        // Assume \$pdo is available globally\n        global \$pdo;\n        \$this->pdo = \$pdo;\n        \$config = json_decode(file_get_contents(__DIR__ . '/../config.json'), true);\n        foreach (\$config['object_models'] as \$model) {\n            if (\$model['model']['name'] == \$model_name) {\n                \$this->model_config = \$model['model'];\n                \$this->model_name = \$model_name;\n                break;\n            }\n        }\n    }\n\n";

foreach ($operations as $op) {
    if ($op == 'set') {
        $content .= "    public function set(\$save_data) {\n        \$table = \$this->model_name;\n        \$fields = \$this->model_config['fields'];\n        \$savable_fields = array_filter(\$fields, function(\$f) { return \$f['can_be_saved']; });\n        if (isset(\$save_data['id'])) {\n            // update\n            \$set_parts = [];\n            \$params = [];\n            foreach (\$savable_fields as \$field) {\n                if (isset(\$save_data[\$field['name']])) {\n                    \$set_parts[] = \$field['name'] . ' = ?';\n                    \$params[] = \$save_data[\$field['name']];\n                }\n            }\n            \$field_names = array_column(\$fields, 'name');\n            if (in_array('updated_at', \$field_names)) {\n                \$set_parts[] = 'updated_at = CURRENT_TIMESTAMP';\n            }\n            \$sql = \"UPDATE \$table SET \" . implode(', ', \$set_parts) . \" WHERE id = ?\";\n            \$params[] = \$save_data['id'];\n            \$stmt = \$this->pdo->prepare(\$sql);\n            return \$stmt->execute(\$params);\n        } else {\n            // insert\n            \$cols = [];\n            \$placeholders = [];\n            \$params = [];\n            foreach (\$fields as \$field) {\n                if (isset(\$save_data[\$field['name']])) {\n                    \$cols[] = \$field['name'];\n                    \$placeholders[] = '?';\n                    \$params[] = \$save_data[\$field['name']];\n                }\n            }\n            \$field_names = array_column(\$fields, 'name');\n            if (in_array('created_at', \$field_names) && !in_array('created_at', \$cols)) {\n                \$cols[] = 'created_at';\n                \$placeholders[] = 'CURRENT_TIMESTAMP';\n            }\n            if (in_array('updated_at', \$field_names) && !in_array('updated_at', \$cols)) {\n                \$cols[] = 'updated_at';\n                \$placeholders[] = 'CURRENT_TIMESTAMP';\n            }\n            \$sql = \"INSERT INTO \$table (\" . implode(', ', \$cols) . \") VALUES (\" . implode(', ', \$placeholders) . \")\";\n            \$stmt = \$this->pdo->prepare(\$sql);\n            \$stmt->execute(\$params);\n            return \$this->pdo->lastInsertId();\n        }\n    }\n\n";
    } elseif ($op == 'delete') {
        $content .= "    public function delete(\$id) {\n        \$table = \$this->model_name;\n        \$sql = \"DELETE FROM \$table WHERE id = ?\";\n        \$stmt = \$this->pdo->prepare(\$sql);\n        return \$stmt->execute([\$id]);\n    }\n\n";
    } elseif ($op == 'get') {
        $content .= "    public function get(\$id) {\n        \$table = \$this->model_name;\n        \$sql = \"SELECT * FROM \$table WHERE id = ?\";\n        \$stmt = \$this->pdo->prepare(\$sql);\n        \$stmt->execute([\$id]);\n        return \$stmt->fetch(PDO::FETCH_ASSOC);\n    }\n\n";
    } else {
        // state change
        $target_state = str_replace('set_', '', $op);
        $content .= "    public function $op(\$id, \$target_state) {\n        \$table = \$this->model_name;\n        \$fields = \$this->model_config['fields'];\n        \$set_parts = [];\n        \$params = [];\n        \$field_names = array_column(\$fields, 'name');\n        if (in_array('state', \$field_names)) {\n            \$set_parts[] = 'state = ?';\n            \$params[] = \$target_state;\n        }\n        foreach (\$fields as \$field) {\n            if (isset(\$field['auto_generated_by_state']) && \$field['auto_generated_by_state'] == \$target_state) {\n                if (\$field['type'] == 'boolean') {\n                    \$set_parts[] = \$field['name'] . ' = 1';\n                } elseif (\$field['type'] == 'timestamp') {\n                    \$set_parts[] = \$field['name'] . ' = CURRENT_TIMESTAMP';\n                } else {\n                    \$set_parts[] = \$field['name'] . ' = \'\'';\n                }\n            }\n        }\n        if (in_array('updated_at', \$field_names)) {\n            \$set_parts[] = 'updated_at = CURRENT_TIMESTAMP';\n        }\n        if (!empty(\$set_parts)) {\n            \$sql = \"UPDATE \$table SET \" . implode(', ', \$set_parts) . \" WHERE id = ?\";\n            \$params[] = \$id;\n            \$stmt = \$this->pdo->prepare(\$sql);\n            return \$stmt->execute(\$params);\n        }\n        return true;\n    }\n\n";
    }
}

$content .= "}\n\n?>";

file_put_contents('Library/engine/_LindseyEngine.php', $content);

// generate model classes for all object_models and sub_models
$all_models = [];
foreach ($config['object_models'] as $model_data) {
    $model = $model_data['model'];
    if (isset($model['name'])) {
        $all_models[] = ['name' => $model['name'], 'type' => 'main'];
    }
    if (isset($model['grouping_objects'])) {
        foreach ($model['grouping_objects'] as $group) {
            if (isset($group['name'])) {
                $all_models[] = ['name' => $group['name'], 'type' => 'group'];
            }
        }
    }
    if (isset($model['child_objects'])) {
        foreach ($model['child_objects'] as $child) {
            if (isset($child['name'])) {
                $all_models[] = ['name' => $child['name'], 'type' => 'child'];
            }
        }
    }
}

foreach ($all_models as $model_info) {
    $obj = $model_info['name'];
    $class_name = str_replace(' ', '', ucwords(str_replace('_', ' ', $obj)));
    $model_content = "<?php\n\nclass $class_name extends _LindseyEngine {\n\n    public function __construct() {\n        parent::__construct('$obj');\n    }\n\n";

    $methods = ['get', 'set', 'delete'];
    // find state_variables for this model
    foreach ($config['object_models'] as $md) {
        $found = false;
        if (isset($md['model']['name']) && $md['model']['name'] == $obj) {
            $target = $md['model'];
            $found = true;
        } elseif (isset($md['model']['grouping_objects'])) {
            foreach ($md['model']['grouping_objects'] as $group) {
                if (isset($group['name']) && $group['name'] == $obj) {
                    $target = $group;
                    $found = true;
                    break;
                }
            }
        }
        if (!$found && isset($md['model']['child_objects'])) {
            foreach ($md['model']['child_objects'] as $child) {
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
            $params = '$id';
        }
        $model_content .= "    public function $method($params) {\n        return include __DIR__ . '/../hooks/{$obj}_{$method}.php';\n    }\n\n";
    }

    $model_content .= "}\n\n?>";

    file_put_contents("Library/wrapper/$class_name.php", $model_content);
}

echo "Generated";

?>