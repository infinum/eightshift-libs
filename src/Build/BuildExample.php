<?php

/**
 * Script used to run project build process.
 *
 * Available commands:
 * - wp eval-file bin/Build.php
 *
 * @package EightshiftLibs
 */

declare(strict_types=1);

// Define project root.
$projectRootPath = dirname(__FILE__, 2);

// Define project path for theme or plugin.
$projectPath = "{$projectRootPath}/wp-content/themes/eightshift-boilerplate";

// Check if folder exists.
if (!file_exists($projectPath)) {
	WP_CLI::error("Provided folder path {$projectPath} is missing.");
}

// Change execution folder.
chdir($projectPath);

// Run setup scripts for npm.
WP_CLI::log((string)shell_exec('npm install'));
WP_CLI::log('--------------------------------------------------');

// Run setup scripts for composer.
WP_CLI::log((string)shell_exec('composer install --no-dev --no-scripts'));
WP_CLI::log('--------------------------------------------------');

// Run setup scripts for building assets.
WP_CLI::log((string)shell_exec('npm run build'));
WP_CLI::log('--------------------------------------------------');

// Change execution folder back to root.
chdir($projectRootPath);

// Run setup scripts for installing plugins, themes, and core.
WP_CLI::runcommand('boilerplate update');
WP_CLI::log('--------------------------------------------------');
