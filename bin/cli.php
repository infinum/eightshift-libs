#!/usr/bin/env php
<?php

/**
 * Helper method for running WPCLI commands without WordPress instalation.
 */

use EightshiftLibs\Cli\Cli;

$root = dirname( __DIR__, 1 );

require "{$root}/vendor/autoload.php";

require $root . '/src/cli/Cli.php';

( new Cli() )->run_develop( $args );

