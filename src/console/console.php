<?php

use EightshiftLibs\Rest\Routes\GenerateRestRoute;
use Symfony\Component\Console\Application;

function consoleRun( string $root ) {

  if (!in_array(PHP_SAPI, ['cli', 'phpdbg', 'embed'], true)) {
    echo 'Warning: The console should be invoked via the CLI version of PHP, not the '.PHP_SAPI.' SAPI'.PHP_EOL;
  }

  set_time_limit(0);

  require "{$root}/vendor/autoload.php";

  $app = new Application();

  $app->add( new GenerateRestRoute( $root ) );

  $app->run();

  return $app;
}
