<?php

/**
 * Theme Name: %projectName%
 * Description: %projectDescription%
 * Author: %projectAuthor%
 * Author URI: %projectAuthorUrl%
 * Version: %projectVersion%
 * License: MIT
 * License URI: http://www.gnu.org/licenses/gpl.html
 * Text Domain: %textdomain%
 *
 * @package %namespace%
 */

declare(strict_types=1);

namespace %namespace%;

use %namespace%\Cache\ManifestCache;
use %namespace%\Main\Main;
use %useLibs%\Cli\Cli;

/**
 * If this file is called directly, abort.
 */
if (! \defined('WPINC')) {
	die;
}

/**
 * Include the autoloader so we can dynamically include the rest of the classes.
 */
$loader = require __DIR__ . '/vendor/autoload.php';
if (\file_exists( __DIR__ . '/vendor-prefixed/autoload.php')) {
	require  __DIR__ . '/vendor-prefixed/autoload.php';
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
