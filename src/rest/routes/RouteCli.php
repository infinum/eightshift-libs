<?php
/**
 * Class that registers WPCLI command for Rest Routes.
 * 
 * Command Develop:
 * wp eval-file bin/cli.php create_rest_route 'temp' 'post' --skip-wordpress
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
   * Get WPCLI command name
   *
   * @return string
   */
  public function get_command_name() : string {
    return 'create_rest_route';
  }

  /**
   * Get WPCLI trigger class name.
   *
   * @return string
   */
  public function get_class_name() : string {
    return RouteCli::class;
  }

  /**
  * Generates REST-API Route in your project.
  *
  * --endpoint_slug=<endpoint_slug>
  * : The name of the endpoint slug. Example: test-route.
  *
  * --method=<method>
  * : HTTP verb must be one of: GET, POST, PATCH, PUT, or DELETE.
  *
  * [--namespace=<namespace>]
  * : Define your projects namespace. Default: EightshiftBoilerplate.
  *
  * [--vendor_prefix=<vendor_prefix>]
  * : Define your projects vendor prefix. Default: EightshiftBoilerplateVendor.
  *
  * ## EXAMPLES
  * 
  *     wp boilerplate create_rest_route --endpoint_slug='temp-route' --method='POST'
  *     wp boilerplate create_rest_route --endpoint_slug='temp-route' --method='post' --namespace='EightshiftBoilerplate' --vendor_prefix='EightshiftBoilerplateVendor'
  */
  public function __invoke( array $args, array $assoc_args ) {

    // Check if endpoint slug exists as prop.
    $endpoint_slug = $assoc_args['endpoint_slug'];

    // Check if method exists as prop.
    $method = strtoupper( $assoc_args['method'] );

    // Check if namespace exists as prop.
    $namespace = $assoc_args['namespace'];

    var_dump($assoc_args);

    // Check if namespace exists as prop.
    $vendor_prefix = $assoc_args['vendor_prefix'];

    // Get full class name.
    $class_name = CliHelpers::get_class_name( $endpoint_slug );

    // Read the template contents, and replace the placeholders with provided variables.
    $template_file = CliHelpers::get_template( __DIR__ . '/' . static::TEMPLATE . '.php' );

    // Remove unecesery stuff from props.
    $endpoint = str_replace( '_', '-', str_replace( ' ', '-', strtolower( $endpoint_slug ) ) );

    // Replace stuff in file.
    $class = str_replace( 'RouteExample', "Route{$class_name}", $template_file );
    $class = str_replace( "/example-route", "/{$endpoint}", $class );
    $class = str_replace( "static::READABLE", static::VERB_ENUM[ $method ], $class );

    // Output final class to new file/folder and finish.
    CliHelpers::output_write( static::OUTPUT_DIR, "Route{$class_name}", $class );
  }
}
