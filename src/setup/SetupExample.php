<?php
/**
 * Script used to run project setup and installing all plugins, themes and core.
 *
 * Available commands:
 * - php bin/setup.php
 * - php bin/setup.php --skip-core
 * - php bin/setup.php --skip-plugins
 * - php bin/setup.php --skip-plugins-core
 * - php bin/setup.php --skip-plugins-github
 * - php bin/setup.php --skip-themes
 * 
 * or you can combine multiple parameters:
 * - php bin/setup.php  --skip-core --skip-themes
 *
 */

require './wp-content/themes/eightshift-boilerplate/vendor/infinum/eightshift-libs/bin/setup.php';

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
