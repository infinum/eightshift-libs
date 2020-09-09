<?php
/**
 * Script used to export database and images to zip.
 *
 * Available commands:
 * - wp eval-file bin/DbExport.php
 * - wp eval-file bin/DbExport.php --skip-db
 * - wp eval-file bin/DbExport.php --skip-uploads
 */

require './wp-content/themes/eightshift-boilerplate/vendor/infinum/eightshift-libs/src/db/DbExport.php';

// Provide optional parameters.
$args = getopt(
  null,
  [
    "skip-db",
    "skip-uploads",
  ]
);

 // Define project root.
$project_root_path = dirname( __FILE__, 2 );

db_export(
  dirname( __FILE__, 2 ),
  $args
);
