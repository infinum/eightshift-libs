#!/usr/bin/env php
<?php

use EightshiftLibs\Cli\Cli;

$root = dirname( __FILE__, 2 );

require $root . '/src/cli/Cli.php';

$cli = ( new Cli( $root ) )->run();
