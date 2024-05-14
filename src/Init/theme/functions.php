<?php

/**
 * Theme Name: %g_project_name%
 * Description: %g_project_description%
 * Author: %g_project_author%
 * Author URI: %g_project_author_url%
 * Version: %g_project_version%
 * License: MIT
 * License URI: http://www.gnu.org/licenses/gpl.html
 * Text Domain: %g_textdomain%
 *
 * @package %g_namespace%
 */

declare(strict_types=1);

namespace %g_namespace%;

use %g_namespace%\Cache\ManifestCache;
use %g_namespace%\Main\Main;
use %g_use_libs%\Cli\Cli;

/**
 * If this file is called directly, abort.
 */
if (! \defined('WPINC')) {
	die;
}

/**
 * Bailout, if the theme is not loaded via Composer.
 */
if (!\file_exists(__DIR__ . '/vendor/autoload.php')) {
	return;
}

/**
 * Require the Composer autoloader.
 */
$loader = require __DIR__ . '/vendor/autoload.php';

/**
 * Require the Composer autoloader for the prefixed libraries.
 */
if (\file_exists(__DIR__ . '/vendor-prefixed/autoload.php')) {
	require __DIR__ . '/vendor-prefixed/autoload.php';
}

/**
 * Set all the cache for the theme.
 */
if (\class_exists(ManifestCache::class)) {
	(new ManifestCache())->setAllCache();
}

/**
 * Begins execution of the theme.
 *
 * Since everything within the theme is registered via hooks,
 * then kicking off the theme from this point in the file does
 * not affect the page life cycle.
 */
if (\class_exists(Main::class)) {
	(new Main($loader->getPrefixesPsr4(), __NAMESPACE__))->register();
}

/**
 * Run all WPCLI commands.
 */
if (\class_exists(Cli::class)) {
	(new Cli())->load('boilerplate');
}
