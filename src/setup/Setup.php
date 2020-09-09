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
    \WP_CLI::error( sprintf(
      'setup.json is missing at this path: %s.',
      $project_root_path
    ) );
  }

  // Parse json file to array.
  $data = json_decode( implode( ' ', (array) file( $setup_file ) ), true );

  if ( empty( $data ) ) {
    \WP_CLI::error( "{$setup_file} is empty." );
  }

  // Check if core key exists in config.
  if ( ! $skip_core ) {
    $core = $data['core'] ?? '';
    
    // Install core version.
    if ( ! empty( $core ) ) {
    \WP_CLI::runcommand( "core update --version={$core} --force" );
    \WP_CLI::log( '--------------------------------------------------' );
    } else {
      \WP_CLI::warning( 'No core version is defined. Skipping.' );
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
          \WP_CLI::runcommand( "plugin install {$name} --version={$version} --force" );
          \WP_CLI::log( '--------------------------------------------------' );
        }
      } else {
        \WP_CLI::warning( 'No core plugins are defined. Skipping.' );
      }
    }

    if ( ! $skip_plugins_github ) {

      // Check if plugins github key exists in config.
      $plugins_github = $plugins['github'] ?? [];

      // Instale github plugins.
      if ( ! empty( $plugins_github ) ) {
        foreach( $plugins_github as $name => $version ) {
          \WP_CLI::runcommand( "plugin install https://github.com/{$name}/archive/{$version}.zip --force" );
          \WP_CLI::log( '--------------------------------------------------' );
        }
      } else {
        \WP_CLI::warning( 'No Github plugins are defined. Skipping.' );
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
        \WP_CLI::runcommand( "theme install {$name} --version={$version} --force" );
        \WP_CLI::log( '--------------------------------------------------' );
      }
    } else {
      \WP_CLI::warning( 'No themes are defined. Skipping.' );
    }
  }


  \WP_CLI::success( 'All commands are finished.' );
  \WP_CLI::log( '--------------------------------------------------' );

}

 