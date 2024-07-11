<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @package %g_namespace%
 */

declare(strict_types=1);

if (! current_user_can('activate_plugins')) {
	return;
}

// If uninstall is not called from WordPress, then exit.
if (! defined('WP_UNINSTALL_PLUGIN')) {
	exit;
}
