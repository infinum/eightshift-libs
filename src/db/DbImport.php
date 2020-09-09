<?php
/**
 * Script used to export database and images to zip.
 * Must provide from and to parameters that are defined in setup.json file.
 *
 * Available commands:
 * - wp eval-file bin/DbImport.php --from=staging --to=develop
 * - wp eval-file bin/DbImport.php --from=production --to=develop
 */
function db_import( string $project_root_path, array $args = [], string $setup_file = 'setup.json' ) {

  // Check if mandatory parameters exists.
  $from = isset( $args['from'] ) ? $args['from'] : '';
  $to   = isset( $args['to'] ) ? $args['to'] : '';

  if ( empty( $from ) ) {
    \WP_CLI::error( "--from parameter is mandatory. Please provide one url key from {$setup_file} file." );
  }

  if ( empty( $to ) ) {
    \WP_CLI::error( "--to parameter is mandatory. Please provide one url key from {$setup_file} file." );
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
    \WP_CLI::error( "{$setup_file} is empty." );
  }

  // Check if urls key esists.
  $urls = $data['urls'] ?? [];

  if ( empty( $urls ) ) {
    \WP_CLI::error( "Urls key is missing or empty." );
  }

  // Die if from key is missing and not valid.
  if ( ! isset( $urls[ $from ] ) || empty( $urls[ $from ] ) ) {
    \WP_CLI::error( "{$from} key is missing or empty in urls." );
  } else {
    $from        = parse_url( $urls[ $from ] );
    $from_host   = $from['host'];
    $from_scheme = $from['scheme'];
  }

  // Die if to key is missing and not valid.
  if ( ! isset( $urls[ $to ] ) || empty( $urls[ $to ] ) ) {
    \WP_CLI::error( "{$to} key is missing or empty in urls." );
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
    \WP_CLI::log( shell_exec( "rm -rf {$export_folder_name}" ) );
    \WP_CLI::log( "Removed old temp {$export_folder_name} folder." );
    \WP_CLI::log( '--------------------------------------------------' );
  }

  // Create new temp folder.
  mkdir( $export_folder_name );
  \WP_CLI::log( "Created temp {$export_folder_name} folder." );
  \WP_CLI::log( '--------------------------------------------------' );

  // Export files to new temp folder.
  \WP_CLI::log( shell_exec( "tar zxf {$export_file_name} -C {$export_folder_name}" ) );
  \WP_CLI::log( "Exported {$export_file_name} to {$export_folder_name} folder." );
  \WP_CLI::log( '--------------------------------------------------' );

  // Execute db export.
  \WP_CLI::log( shell_exec( "db export" ) );
  \WP_CLI::log( "Db exported successfully." );
  \WP_CLI::log( '--------------------------------------------------' );

  // TO FIX. User prompt not working.
  \WP_CLI::log( "Are you sure you want to reset the 'eightshift_internal' database? [y/n]" );
  \WP_CLI::log( shell_exec( "db reset" ) );
  \WP_CLI::log( '--------------------------------------------------' );

  // Import new database.
  \WP_CLI::log( shell_exec( "db import {$export_folder_name}/{$db_file_name}" ) );
  \WP_CLI::log( "Database import done." );
  \WP_CLI::log( '--------------------------------------------------' );

  // Search and replace url host.
  \WP_CLI::log( shell_exec( "search-replace {$from_host} {$to_host} --url={$from_host} --all-tables --network" ) );
  \WP_CLI::log( "Database search replace for host successfully finished." );
  \WP_CLI::log( '--------------------------------------------------' );

  // Search and replace url sheme.
  if ( $to_scheme !== $from_scheme ) {
    \WP_CLI::log( shell_exec( "search-replace {$from_scheme}://{$to_host} {$to_scheme}://{$to_host} --all-tables --network" ) );
    \WP_CLI::log( "Database search replace for scheme successfully finished." );
    \WP_CLI::log( '--------------------------------------------------' );
  }

  // Clean up.
  \WP_CLI::log( shell_exec( "cache flush" ) );
  \WP_CLI::log( shell_exec( "transient delete --all" ) );
  \WP_CLI::log( shell_exec( "rewrite flush" ) );
  \WP_CLI::log( "Flushing cache, removing transients and resetting premalinks!" );
  \WP_CLI::log( '--------------------------------------------------' );

  \WP_CLI::success( "Finished! Success!" );
  }
