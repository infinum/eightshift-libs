<?php
/**
 * Script used to export database and images to zip.
 * Must provide from and to parameters that are defined in setup.json file.
 *
 * Available commands:
 * - php bin/db-import.php --from=staging --to=develop
 * - php bin/db-import.php --from=production --to=develop
 */

// Define project root.
$project_root_path = dirname( __FILE__, 2 );

// Define setup.json file path.
$setup_file = 'setup.json';

// Provide mandatory parameters.
$args = getopt(
  null,
  [
    "from:",
    "to:",
  ]
);

// Check if mandatory parameters exists.
$from = isset( $args['from'] ) ? $args['from'] : '';
$to   = isset( $args['to'] ) ? $args['to'] : '';

if ( empty( $from ) ) {
  echo "--from parameter is mandatory. Please provide one url key from {$setup_file} file.",
  die;
}

if ( empty( $to ) ) {
  echo "--to parameter is mandatory. Please provide one url key from {$setup_file} file.",
  die;
}

// Change execution folder.
chdir( $project_root_path );

// Check if setup exists.
if ( ! file_exists( $setup_file ) ) {
  throw new Exception(
    sprintf(
      'setup.json is missing at this path: %s.',
      $setup_file
    )
  );
}

// Parse json file to array.
$data = json_decode( implode( ' ', (array) file( $setup_file ) ), true );

// Check if $data is empty.
if ( empty( $data ) ) {
  echo "{$setup_file} is empty.\n",
  die;
}

// Check if urls key esists.
$urls = $data['urls'] ?? [];

if ( empty( $urls ) ) {
  echo "Urls key is missing or empty.\n",
  die;
}

// Die if from key is missing and not valid.
if ( ! isset( $urls[ $from ] ) || empty( $urls[ $from ] ) ) {
  echo "{$from} key is missing or empty in urls.\n",
  die;
} else {
  $from        = parse_url( $urls[ $from ] );
  $from_host   = $from['host'];
  $from_scheme = $from['scheme'];
}

// Die if to key is missing and not valid.
if ( ! isset( $urls[ $to ] ) || empty( $urls[ $to ] ) ) {
  echo "{$to} key is missing or empty in urls.\n",
  die;
} else {
  $to        = parse_url( $urls[ $to ] );
  $to_host   = $to['host'];
  $to_scheme = $to['scheme'];
}

// Define db export file name.
$db_file_name = 'latest.sql';

// Define export file name.
$export_file_name = 'latest_dump.tar.gz';

// Define export folder name.
$export_folder_name = 'latest_dump';

// Remove old db export folder if it exists.
if ( file_exists( $export_folder_name ) ) {
  echo shell_exec( "rm -rf {$export_folder_name}" );
  echo "Removed old temp {$export_folder_name} folder.\n";
  echo "-------------------------------------\n";
}

// Create new temp folder.
mkdir( $export_folder_name );
echo "Created temp {$export_folder_name} folder.\n";
echo "-------------------------------------\n";

// Export files to new temp folder.
echo shell_exec( "tar zxf {$export_file_name} -C {$export_folder_name}" );
echo "Exported {$export_file_name} to {$export_folder_name} folder.\n";
echo "-------------------------------------\n";

// Execute db export.
echo shell_exec( "wp db export" );
echo "Db exported successfully.\n";
echo "-------------------------------------\n";

// TO FIX. User prompt not working.
echo "Are you sure you want to reset the 'eightshift_internal' database? [y/n]\n";
echo shell_exec( "wp db reset" );
echo "-------------------------------------\n";

// Import new database.
echo shell_exec( "wp db import {$export_folder_name}/{$db_file_name}" );
echo "Database import done.\n";
echo "-------------------------------------\n";

// Search and replace url host.
echo shell_exec( "wp search-replace {$from_host} {$to_host} --url={$from_host} --all-tables --network" );
echo "Database search replace for host successfully finished.\n";
echo "-------------------------------------\n";

// Search and replace url sheme.
if ( $to_scheme !== $from_scheme ) {
  echo shell_exec( "wp search-replace {$from_scheme}://{$to_host} {$to_scheme}://{$to_host} --all-tables --network" );
  echo "Database search replace for scheme successfully finished.\n";
  echo "-------------------------------------\n";
}

// Clean up.
echo shell_exec( "wp cache flush" );
echo shell_exec( "wp transient delete --all" );
echo shell_exec( "wp rewrite flush" );
echo "Flushing cache, removing transients and resetting premalinks!\n";
echo "-------------------------------------\n";

echo "Finished! Success!\n";
