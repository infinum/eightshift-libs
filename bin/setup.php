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

function setup( string $project_root_path, array $args = [], string $setup_file = 'setup.json' ) {

  // Check if optional parameters exists.
  $skip_core           = isset( $args['skip-core'] );
  $skip_plugins        = isset( $args['skip-plugins'] );
  $skip_plugins_core   = isset( $args['skip-plugins-core'] );
  $skip_plugins_github = isset( $args['skip-plugins-github'] );
  $skip_themes         = isset( $args['skip-themes'] );

  // Change execution folder.
  chdir( $project_root_path );

  // Check if setup exists.
  if ( ! file_exists( $setup_file ) ) {
  throw new Exception(
    sprintf(
      'setup.json is missing at this path: %s.',
      $project_root_path
    )
  );
  }

  // Parse json file to array.
  $data = json_decode( implode( ' ', (array) file( $setup_file ) ), true );

  if ( empty( $data ) ) {
  echo "{$setup_file} is empty.",
  die;
  }

  // Check if core key exists in config.
  if ( ! $skip_core ) {
  $core = $data['core'] ?? '';
  
  // Install core version.
  if ( ! empty( $core ) ) {
    echo shell_exec( "wp core update --version={$core} --force" );
    echo "-------------------------------------\n";
  } else {
    echo "No core version is defined. Skipping.\n";
  }
  }

  // Check if plugins key exists in config.
  if ( ! $skip_plugins ) {

  $plugins = $data['plugins'] ?? [];

  if ( ! empty( $plugins ) ) {

    if ( ! $skip_plugins_core ) {

      // Check if plugins core key exists in config.
      $plugins_core = $plugins['core'] ?? [];

      // Instale core plugins.
      if ( ! empty( $plugins_core ) ) {
        foreach( $plugins_core as $name => $version ) {
          echo shell_exec( "wp plugin install {$name} --version={$version} --force" );
          echo "-------------------------------------\n";
        }
      } else {
        echo "No core plugins are defined. Skipping.\n";
      }
    }

    if ( ! $skip_plugins_github ) {

      // Check if plugins github key exists in config.
      $plugins_github = $plugins['github'] ?? [];

      // Instale github plugins.
      if ( ! empty( $plugins_github ) ) {
        foreach( $plugins_github as $name => $version ) {
          echo shell_exec( "wp plugin install https://github.com/{$name}/archive/{$version}.zip --force;" );
          echo "-------------------------------------\n";
        }
      } else {
        echo "No Github plugins are defined. Skipping.\n";
      }
    }
  }
  }

  // Check if themes key exists in config.
  if ( ! $skip_themes ) {

  $themes = $data['themes'] ?? [];

  // Instale themes.
  if ( ! empty( $themes ) ) {
    foreach( $themes as $name => $version ) {
      echo shell_exec( "wp theme install {$name} --version={$version} --force" );
      echo "-------------------------------------\n";
    }
  } else {
    echo "No themes are defined. Skipping.\n";
  }
  }


  echo "Finished!\n";
  echo "-------------------------------------\n";

}

 