<?php

/**
 * Bootstrap file for PHPUnit tests.
 *
 * @package EightshiftLibs\Tests
 */

declare(strict_types=1);

// Composer autoloader.
require_once __DIR__ . '/../vendor/autoload.php';

// Initialize Brain\Monkey for WordPress function mocking.
\Brain\Monkey\setUp();

// Define WordPress constants that might be needed in tests.
if (!defined('ABSPATH')) {
	define('ABSPATH', '/tmp/wordpress/');
}

if (!defined('WP_DEBUG')) {
	define('WP_DEBUG', true);
}

if (!defined('WP_DEBUG_LOG')) {
	define('WP_DEBUG_LOG', true);
}

if (!defined('WP_DEBUG_DISPLAY')) {
	define('WP_DEBUG_DISPLAY', false);
}

if (!defined('SCRIPT_DEBUG')) {
	define('SCRIPT_DEBUG', true);
}

if (!defined('WP_CONTENT_DIR')) {
	define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
}

if (!defined('WP_CONTENT_URL')) {
	define('WP_CONTENT_URL', 'http://example.org/wp-content');
}

if (!defined('WP_PLUGIN_DIR')) {
	define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');
}

if (!defined('WP_PLUGIN_URL')) {
	define('WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins');
}

// Register a teardown function to clean up after each test.
register_shutdown_function(function () {
	\Brain\Monkey\tearDown();
});
