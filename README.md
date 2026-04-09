# Project Setup

This is a PHP-based project for generating API endpoints, database wrappers, and hooks from a JSON configuration file.

## Structure

- `generator.php`: Main script that generates code based on `Library/config.json`
- `Library/config.json`: Configuration file containing object models, channels, hooks, and channel pages
- `Library/engine/`: Contains the base engine class `_LindseyEngine.php`
- `Library/hooks/`: Generated hook files for CRUD operations
- `Library/wrapper/`: Generated PHP classes for database models
- `Library/custom/`: Custom functions
- `api/`: Generated API endpoint files
- `Bootloader.php`: Likely the entry point for the application

## How to Use

1. Edit `Library/config.json` to define your object models, channels, and pages.
2. Run `php generator.php` to generate the code.
3. The generator will create:
    - Folders based on channel names (e.g., `api/` for channel "api")
    - API files inside channel folders based on channel_pages
    - Hook files in `Library/hooks/`
    - Wrapper classes in `Library/wrapper/`
    - Engine class in `Library/engine/`

## Configuration

The `Library/config.json` contains:

- `object_models`: Definitions of database models with fields, state machines, etc.
- `channels`: API channels with names like "api" or "api/v1"
- `channel_pages`: Endpoint definitions tied to channels
- `hooks`: Predefined hook functions
- `custom_functions`: Custom utility functions

## Generated Code

- **Channels**: Folders created based on `channel_name`, supporting nested paths (e.g., "api/v1" creates `api/v1/`)
- **Pages**: PHP files placed in channel folders using the `endpoint` as filename
- **Hooks**: CRUD and state-change hooks for each model
- **Wrappers**: PHP classes extending `_LindseyEngine` with methods for database operations
- **Engine**: Base class handling database interactions

## Requirements

- PHP with PDO support
- Database connection configured in `_db_config.php`
