<?php
/**
 * Script used to run project build process.
 *
 * Available commands:
 * - php bin/build.php
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
echo shell_exec( "npm install" );
echo "-------------------------------------\n";

// Run setup scripts for coomposer.
echo shell_exec( "composer install --no-dev --no-scripts" );
echo "-------------------------------------\n";

// Run setup scripts for building assets.
echo shell_exec( "npm run build" );
echo "-------------------------------------\n";

// Change execution folder back to root.
chdir( $project_root_path );

// Run setup scripts for installing plugins, themes, and core.
echo shell_exec( "php bin/setup.php" );
echo "-------------------------------------\n";
