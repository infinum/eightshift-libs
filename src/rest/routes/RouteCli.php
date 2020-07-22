<?php

namespace EightshiftLibs\Rest\Routes;

use EightshiftLibs\Console\ConsoleHelpers;
use EightshiftLibs\Services\ServiceInterface;

class RouteCli implements ServiceInterface {

  /**
   * Project root
   */
  protected $root;

  /**
   * Undocumented function
   *
   * @param string $root
   */
  public function __construct( string $root ) {
    $this->root = $root;
  }

  public function register() {
    \add_action( 'cli_init', [ $this, 'register_command'] );
  }

  public function __invoke( array $args ) {
    \WP_CLI::success( $args[0] );
  }

  function register_command() {
    \WP_CLI::add_command( 'wds', 'RouteCli' );
  }

  /**
   * Returns 'Hello World'
   *
   * @since  0.0.1
   * @author Scott Anderson
   */
  public function callback( array $args ) {

    $endpoint_slug = $args[0] ?? '';

    if ( empty( $endpoint_slug ) ) {
      \WP_CLI::error( 'Endpoint slug empty' );
    }

    $method   = $args[1] ?? '';

    if ( ! in_array( $method, [ 'GET', 'POST', 'PATCH', 'PUT', 'DELETE' ], true ) ) {
      \WP_CLI::error(
        sprintf( 'HTTP verb must be one of: \'GET\', \'POST\', \'PATCH\', \'PUT\', or \'DELETE\'. %s provided.', $method )
      );
    }

    $endpoint = str_replace( '_', '-', str_replace( ' ', '-', strtolower( $endpoint_slug ) ) );

    $class_name = ConsoleHelpers::get_class_name( $endpoint_slug );

    $verb_mapping = [
      'GET'    => 'READABLE',
      'POST'   => 'CREATABLE',
      'PATCH'  => 'EDITABLE',
      'PUT'    => 'UPDATEABLE',
      'DELETE' => 'DELETABLE',
    ];

    // Read the template contents, and replace the placeholders with provided variables.
    $template_file = file_get_contents( __DIR__ . '/Route.php' ); // phpcs:ignore WordPress.WP.AlternativeFunctions

    if ( $template_file === false ) {
      \WP_CLI::error( 'The template "/Route.php" seems to be missing.' );
    }

    $class = str_replace( 'class Route', "class Route{$class_name}", $template_file );
    $class = str_replace( "const ENDPOINT_SLUG = '/route'", "const ENDPOINT_SLUG = '/{$endpoint}'", $class );
    $class = str_replace( "'methods'  => static::READABLE,", "'methods'  => static::{$verb_mapping[ $method ]},", $class );

    $rest_dir = $this->root . '/src/rest/routes';
    $file     = $rest_dir . "/Route{$class_name}.php";

    if ( file_exists( $file ) ) {
      \WP_CLI::error(
        sprintf( 'The file "%s" can\'t be generated because it already exists.', "{$endpoint}.php" )
      );
    }

    if ( ! is_dir( $rest_dir ) ) {
      mkdir( $rest_dir, 0755, true );
    }

    $fp = fopen( $file, 'wb' ); // phpcs:ignore WordPress.WP.AlternativeFunctions

    if ( $fp !== false ) {
        fwrite( $fp, $class ); // phpcs:ignore WordPress.WP.AlternativeFunctions
        fclose( $fp ); // phpcs:ignore WordPress.WP.AlternativeFunctions
    } else {
      \WP_CLI::success( "File {$endpoint}.php couldn't be created in {$rest_dir} directory. There was an error." );
    }

    \WP_CLI::success( "File {$endpoint}.php successfully created in {$rest_dir} directory." );

  }
}
