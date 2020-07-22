<?php
/**
 * Class that registers WPCLI command for Rest Routes.
 * 
 * Command Develop:
 * `wp eval-file bin/cli.php --skip-wordpress`
 * 
 * Command Production:
 * ``
 *
 * @package EightshiftLibs\Rest\Routes
 */

namespace EightshiftLibs\Rest\Routes;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\CliHelpers;

/**
 * Class RouteCli
 */
class RouteCli extends AbstractCli {

  /**
   * Output dir relative path.
   */
  const OUTPUT_DIR = 'src/rest/routes';

  /**
   * Output template name.
   */
  const TEMPLATE = 'RouteExample';

  /**
   * Route method enum.
   */
  const VERB_ENUM = [
    'GET'    => 'static::READABLE',
    'POST'   => 'static::CREATABLE',
    'PATCH'  => 'static::EDITABLE',
    'PUT'    => 'static::UPDATEABLE',
    'DELETE' => 'static::DELETABLE',
  ];

  /**
   * Create a new instance that injects classes
   *
   * @param string $root Absolute path to project root.
   */
  public function __construct( string $root ) {
    $this->root = $root;
  }

  /**
   * Get WPCLI command name
   *
   * @return string
   */
  public function get_command_name() : string {
    return 'wds';
  }

  /**
   * Get WPCLI trigger class name.
   *
   * @return string
   */
  public function get_class_name() : string {
    return 'RouteCli';
  }

  /**
   * Get WPCLI command callback.
   *
   * @param array $args Arguments provided.
   *
   * @return void
   */
  public function callback( array $args ) {

    // Check if endpoint slug exists as prop.
    $endpoint_slug = $args[0] ?? '';

    if ( empty( $endpoint_slug ) ) {
      \WP_CLI::error( 'Endpoint slug empty' );
    }

    // Check if method exists as prop.
    $method = $args[1] ?? '';

    if ( ! in_array( $method, [ 'GET', 'POST', 'PATCH', 'PUT', 'DELETE' ], true ) ) {
      \WP_CLI::error(
        sprintf( 'HTTP verb must be one of: \'GET\', \'POST\', \'PATCH\', \'PUT\', or \'DELETE\'. %s provided.', $method )
      );
    }

    // Remove unecesery stuff from props.
    $endpoint = str_replace( '_', '-', str_replace( ' ', '-', strtolower( $endpoint_slug ) ) );

    // Get full class name.
    $class_name = CliHelpers::get_class_name( $endpoint_slug );

    // Read the template contents, and replace the placeholders with provided variables.
    $template_file = CliHelpers::get_template( __DIR__ . '/' . static::TEMPLATE . '.php' );

    // Replace stuff in file.
    $class = str_replace( 'RouteExample', "Route{$class_name}", $template_file );
    $class = str_replace( "/example-route", "/{$endpoint}", $class );
    $class = str_replace( "static::READABLE", static::VERB_ENUM[ $method ], $class );

    // Output final class to new file/folder and finish.
    CliHelpers::output_write( $this->root, static::OUTPUT_DIR, "Route{$class_name}", $class );
  }
}
