<?php
/**
 * Script used to export database and images to zip.
 *
 * Available commands:
 * - wp eval-file bin/DbImport.php --from=staging --to=develop
 * - wp eval-file bin/DbImport.php --from=production --to=develop
 */

require './wp-content/themes/eightshift-boilerplate/vendor/infinum/eightshift-libs/src/db/DbImport.php';

// Provide mandatory parameters.
$args = getopt(
  null,
  [
    "from:",
    "to:",
  ]
);

db_import(
  dirname( __FILE__, 2 ),
  $args
);
