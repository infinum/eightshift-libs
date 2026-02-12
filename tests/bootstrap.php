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

// Define WordPress functions that are used with \global scope call in source code
// and cannot be mocked by Brain\Monkey alone (which creates namespace-scoped mocks).
if (!function_exists('get_plugin_data')) {
	/**
	 * Stub for WordPress get_plugin_data function.
	 *
	 * @param string $plugin_file Path to the plugin file.
	 * @param bool   $markup      Whether to apply markup to the plugin data.
	 * @param bool   $translate   Whether to translate the plugin data.
	 *
	 * @return array<string, mixed>
	 */
	function get_plugin_data(string $plugin_file, bool $markup = true, bool $translate = true): array
	{
		return [];
	}
}
