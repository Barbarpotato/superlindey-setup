# App Runner - LindseyEngine Workflow Specification

## Overview

The App Runner code generator is a PHP-based system that automatically generates database interaction layers, hooks, and wrapper classes from a JSON configuration file. It creates a complete CRUD and state management system for web applications, supporting main object models, grouping objects, and child objects.

## Input

- **Configuration File**: `Library/config.json`
    - Contains model definitions, hooks, custom functions, and channel pages
    - Defines database schema, relationships, and state machines

## Output Structure

```
Library/
├── engine/
│   └── _LindseyEngine.php          # Core database operations class
├── wrapper/
│   ├── ModelName.php              # Generated wrapper classes
│   └── ...
├── hooks/
│   ├── model_operation.php        # Generated hook files
│   └── ...
├── custom/
│   └── function_name.php          # Custom function files
└── config.json                    # Input configuration
```

## Workflow Steps

### 1. Configuration Loading

- Load `Library/config.json`
- Parse object models, hooks, custom functions, and channels

### 2. Hook Generation from Config

- Generate hooks defined in `config.json["hooks"]`
- Each hook contains custom PHP code for operations

### 3. Automatic Hook Generation

For each model in `object_models`, `grouping_objects`, and `child_objects`:

- Generate basic CRUD hooks: `get.php`, `set.php`, `delete.php`
- Generate state hooks from `state_machine.state_variables[]`
- Hook template:

```php
<?php
    // **
    // custom pre-hook goes there
    // ..

    // **
    // calling the parent function
    $res = parent::<operation>(<params>);

    // **
    // custom post-hook goes there
    // ..

    // **
    // done
    return $res;
?>
```

### 4. LindseyEngine Generation

- Collect all operations from main models, grouping objects, and child objects
- Generate `Library/engine/_LindseyEngine.php` with:
    - Database connection handling supporting all model types
    - CRUD methods (get, set, delete) for all objects
    - State transition methods
    - SQL query building based on field specifications
    - Model configuration lookup in nested object structures

### 5. Wrapper Class Generation

- Generate wrapper classes in `Library/wrapper/`
- Each class extends `_LindseyEngine`
- Methods include corresponding hook files
- Autoloader configured for automatic loading

### 6. Supporting File Generation

- Custom functions from `config.json["custom_functions"]`
- Channel pages from `config.json["channel_pages"]`

## Model Structure Processing

### Object Models

- Main models defined in `config.json["object_models"][]["model"]`
- Fields with properties: name, type, can_be_saved, compulsory, etc.
- State machines with state_variables and transitions

### Grouping Objects

- Sub-models under `model["grouping_objects"][]`
- Fully supported with own hooks, wrappers, and database operations
- Treated as separate entities with complete CRUD functionality

### Child Objects

- Sub-models under `model["child_objects"][]`
- Same full support as grouping objects with hooks, wrappers, and database operations

## State Machine Handling

### State Variables

- Array of objects with `name` property
- Generates hooks: `{model}_set_{state_name}.php`
- Updates fields marked `auto_generated_by_state`

### Transitions (Legacy)

- Previously used `transitions[]` with `name` property
- Now uses `state_variables[]` for consistency

## Database Integration

### Connection

- Uses PDO with global `$pdo` variable
- Configured in `_db_config.php`
- UTF8MB4 charset, exception error mode

### SQL Generation

- Dynamic query building based on field metadata
- Handles different field types (integer, varchar, timestamp, boolean)
- Automatic timestamp management (created_at, updated_at)

## Hook System Architecture

### Inheritance Chain

```
Wrapper Class (e.g., Lottery)
    ↓ extends
_LindseyEngine
    ↓ contains
Database Operations
```

### Hook Execution Flow

1. Application calls `Wrapper->operation()`
2. Wrapper includes `hook_file.php`
3. Hook executes pre-operation logic
4. Hook calls `parent::operation()` (\_LindseyEngine)
5. \_LindseyEngine executes database operation
6. Hook executes post-operation logic
7. Result returned to application

## Usage Example

```php
// Include configuration
include '_db_config.php';  // Sets up DB and autoloader

// Use wrapper class (autoloader loads it)
$lottery = new Lottery();

// Insert data (runs lottery_set.php hook)
$data = ['event_name' => 'Test', 'description' => 'Test lottery'];
$id = $lottery->set($data);

// Get data (runs lottery_get.php hook)
$record = $lottery->get($id);

// State change (runs lottery_set_published.php hook)
$lottery->set_published($id, 'published');
```

## File Dependencies

### Required Files

- `_db_config.php` - Database setup and autoloader
- `Library/config.json` - Configuration input
- Generated files in `Library/` subdirectories

### Generated Files

- `Library/engine/_LindseyEngine.php`
- `Library/wrapper/*.php`
- `Library/hooks/*.php`
- `Library/custom/*.php`
- Channel page files in project root

## Configuration Schema

### Model Definition

```json
{
  "model": {
    "name": "model_name",
    "fields": [
      {
        "name": "field_name",
        "type": "integer|varchar_short|timestamp|boolean",
        "can_be_saved": true,
        "compulsory": false,
        "auto_generated_by_state": "state_name"
      }
    ],
    "state_machine": {
      "state_variables": [
        {"name": "confirmed"},
        {"name": "published"}
      ]
    }
  },
  "grouping_objects": [
    {
      "name": "group_model_name",
      "fields": [...],
      "state_machine": {...}
    }
  ],
  "child_objects": [
    {
      "name": "child_model_name",
      "fields": [...],
      "state_machine": {...}
    }
  ]
}
```

## Error Handling

- PDO exceptions for database errors
- File existence checks before including hooks
- Validation of required fields in hooks

## Performance Considerations

- Autoloader loads classes on demand
- Hook files included only when operations called
- SQL prepared statements for security
- Minimal memory footprint for generated code
