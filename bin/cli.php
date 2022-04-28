<?php

/**
 * Helper method for running WPCLI commands without WordPress instalation.
 *
 * @package EightshiftLibs
 */

use EightshiftLibs\Cli\Cli;
use WP_CLI\ExitException;

$root = \dirname(__DIR__);
$ds = DIRECTORY_SEPARATOR;

require_once "{$root}{$ds}vendor{$ds}autoload.php";
require_once  "{$root}{$ds}src{$ds}Cli{$ds}Cli.php";

try {
	(new Cli())->loadDevelop($args);
} catch (ReflectionException $e) {
	// Let it go.
} catch (ExitException $e) {
	exit("{$e->getCode()}: {$e->getMessage()}");
}
