<?php
/**
 * Script used to export database and images to zip.
 *
 * Available commands:
 * - php bin/db-export.php
 * - php bin/db-export.php --skip-db
 * - php bin/db-export.php --skip-uploads
 */

// Provide optional parameters.
$args = getopt(
  null,
  [
    "skip-db",
    "skip-uploads",
  ]
);

// Check if optional parameters exists.
$skip_db      = isset( $args['skip-db'] );
$skip_uploads = isset( $args['skip-uploads'] );

 // Define project root.
$project_root_path = dirname( __FILE__, 2 );

// Change execution folder.
chdir( $project_root_path );

// Define db export file name.
$db_file_name = 'latest.sql';

// Define export file name.
$export_file_name = 'latest_dump.tar.gz';

// Define path to uploads folder.
$uploads_folder = 'wp-content/uploads';

// Remove old export file if it exists.
if ( file_exists( $export_file_name ) ) {
  unlink( $export_file_name );
}

// Execute db export.
if ( ! $skip_db ) {
  echo shell_exec( "wp db export {$db_file_name}" );
  echo "Exported db to {$project_root_path} folder.\n";
  echo "-------------------------------------\n";
}

// Execute compress and export for db and uploads folder.
$export_files = "{$db_file_name} {$uploads_folder}";

if ( $skip_db ) {
  $export_files = "{$uploads_folder}";

  if ( ! file_exists( $uploads_folder ) ) {
    $export_files = "";
  }
}

if ( $skip_uploads ) {
  $export_files = "{$db_file_name}";
}

if ( ! empty( $export_files ) ) {
  echo shell_exec( "tar czf {$export_file_name} {$export_files}" );
  echo "Compressing folders success.\n";
  echo "-------------------------------------\n";
}

// Finishing.
echo "Export complete! File {$export_file_name} is located in {$project_root_path} folder.\n";
echo "-------------------------------------\n";

// Remove old db export file if it exists.
if ( file_exists( $db_file_name ) ) {
  unlink( $db_file_name );
}
