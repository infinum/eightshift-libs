<?php
/**
 * Script used to run project setup and installing all plugins, themes and core.
 *
 * Available commands:
 * - wp eval-file bin/setup.php
 * - wp eval-file bin/setup.php --skip-core
 * - wp eval-file bin/setup.php --skip-plugins
 * - wp eval-file bin/setup.php --skip-plugins-core
 * - wp eval-file bin/setup.php --skip-plugins-github
 * - wp eval-file bin/setup.php --skip-themes
 * 
 * or you can combine multiple parameters:
 * - wp eval-file bin/setup.php  --skip-core --skip-themes
 *
 */

require './wp-content/themes/eightshift-boilerplate/vendor/infinum/eightshift-libs/src/setup/Setup.php';

// Provide optional parameters.
$args = getopt(
  null,
  [
    "skip-core",
    "skip-plugins",
    "skip-plugins-core",
    "skip-plugins-github",
    "skip-themes",
  ]
);

setup(
  dirname( __FILE__, 2 ),
  $args
);
