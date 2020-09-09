<?php
/**
 * Script used to run project build process.
 *
 * Available commands:
 * - wp eval-file bin/Build.php
 */

// Define project root.
$project_root_path = dirname( __FILE__, 2 );

// Define project path for theme or plugin.
$project_path = "{$project_root_path}/wp-content/themes/eightshift-boilerplate";

// Check if folder exists.
if ( ! file_exists( $project_path ) ) {
  throw new Exception(
    sprintf(
      'Provided folder path %s is missing.',
      $project_path
    )
  );
}

// Change execution folder.
chdir( $project_path );

// Run setup scripts for npm.
\WP_CLI::log( shell_exec( "npm install" ) );
\WP_CLI::log( '--------------------------------------------------' );

// Run setup scripts for coomposer.
\WP_CLI::log( shell_exec( "composer install --no-dev --no-scripts" ) );
\WP_CLI::log( '--------------------------------------------------' );

// Run setup scripts for building assets.
\WP_CLI::log( shell_exec( "npm run build" ) );
\WP_CLI::log( '--------------------------------------------------' );

// Change execution folder back to root.
chdir( $project_root_path );

// Run setup scripts for installing plugins, themes, and core.
\WP_CLI::runcommand( "eval-file bin/setup.php" );
\WP_CLI::log( '--------------------------------------------------' );
