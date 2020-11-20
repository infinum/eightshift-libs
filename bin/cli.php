<?php

/**
 * Helper method for running WPCLI commands without WordPress instalation.
 *
 * @package EightshiftLibs
 */

use EightshiftLibs\Cli\Cli;
use WP_CLI\ExitException;

$root = dirname(__DIR__, 1);

require "{$root}/vendor/autoload.php";
require  "{$root}/src/Cli/Cli.php";

try {
	(new Cli())->loadDevelop($args);
} catch (ReflectionException $e) {
	// Let it go.
} catch (ExitException $e) {
	exit("{$e->getCode()}: {$e->getMessage()}");
}
