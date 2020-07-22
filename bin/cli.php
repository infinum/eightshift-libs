#!/usr/bin/env php
<?php

use EightshiftLibs\Cli\Cli;

$root = dirname( __FILE__, 2 );

require "{$root}/vendor/autoload.php";

require $root . '/src/cli/Cli.php';

( new Cli( $root ) )->run();
