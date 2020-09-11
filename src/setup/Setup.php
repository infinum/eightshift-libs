<?php
/**
 * Script used to run project setup and installing all plugins, themes and core.
 */
function setup( string $project_root_path, array $args = [], string $setup_file = 'setup.json' ) {

  // Check if optional parameters exists.
  $skip_core           = $args['skip_core'] ?? false;
  $skip_plugins        = $args['skip_plugins'] ?? false;
  $skip_plugins_core   = $args['skip_plugins_core'] ?? false;
  $skip_plugins_github = $args['skip_plugins_github'] ?? false;
  $skip_themes         = $args['skip_themes'] ?? false;

  // Change execution folder.
  if ( ! is_dir( $project_root_path ) ) {
    \WP_CLI::error( "Folder doesn't exist on this path: {$project_root_path}." );
  }

  chdir( $project_root_path );

  // Check if setup exists.
  if ( ! file_exists( $setup_file ) ) {
    \WP_CLI::error( "setup.json is missing at this path: {$setup_file}." );
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

    // Install themes.
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

 