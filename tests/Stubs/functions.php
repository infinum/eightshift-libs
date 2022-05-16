<?php

/**
 * Theme Name: Integration Test theme
 * Description: Used for integration tests
 * Author: Team Eightshift
 * Author URI: https://eightshift.com/
 * Version: 1.0.0
 * Text Domain: testing
 *
 * @package Testing
 */

declare(strict_types=1);

namespace EightshiftBoilerplate;

use EightshiftBoilerplate\Main\Main;

/**
 * If this file is called directly, abort.
 */
if (! \defined('WPINC')) {
	die;
}

/**
 * Include the autoloader so we can dynamically include the rest of the classes.
 */
$loader = require dirname(__DIR__, 2) . '/vendor/autoload.php';


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

