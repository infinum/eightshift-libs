<?php

/**
 * Helper method for running WPCLI commands without WordPress instalation.
 *
 * @package EightshiftLibs
 */

use EightshiftLibs\Cli\Cli;

$root = dirname(__DIR__, 1);

require "{$root}/vendor/autoload.php";

require $root . '/src/Cli/Cli.php';

( new Cli() )->loadDevelop($args);
