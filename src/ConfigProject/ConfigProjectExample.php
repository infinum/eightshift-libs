<?php

/**
 * Global Environment variable
 *
 * Define global environment variable, and define certain
 * settings based on it.
 *
 * @package EightshiftBoilerplate
 */

// phpcs:disable
if (! defined('WP_ENVIRONMENT_TYPE')) {
	return false;
}

// Limit revisions for better optimizations.
define('WP_POST_REVISIONS', 10);

// Optimize assets in admin.
define('COMPRESS_CSS', true);
define('COMPRESS_SCRIPTS', true);
define('CONCATENATE_SCRIPTS', true);
define('ENFORCE_GZIP', true);

// Disable editing theme from admin.
define('DISALLOW_FILE_EDIT', true);

// Auto save interval higher to optimize admin.
define('AUTOSAVE_INTERVAL', 240);

// Disable automatic updating of plugins.
define('AUTOMATIC_UPDATER_DISABLED', true);

if (WP_ENVIRONMENT_TYPE !== 'production') {
	// Enable debug and error logging.
	define('WP_DEBUG', true);
	define('WP_DEBUG_LOG', true);
}

// Environment based setup.
if (WP_ENVIRONMENT_TYPE === 'development' || WP_ENVIRONMENT_TYPE === 'local') {
	// Enable debug and error logging.
	define('WP_DEBUG', true);
	define('WP_DEBUG_LOG', true);

	// Enable direct upload from admin.
	define('FS_METHOD', 'direct');

	// Enable debug and error logging.
	define('WP_DEBUG_DISPLAY', true);
} else {
	// Disable plugins / themes updates from admin.
	define('DISALLOW_FILE_MODS', true);

	// Force login to admin with ssl.
	define('FORCE_SSL_LOGIN', true);

	// Enable debug and error logging.
	define('WP_DEBUG_DISPLAY', false);
}

// phpcs:enable
