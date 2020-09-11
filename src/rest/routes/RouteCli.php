<?php
/**
 * Class that registers WPCLI command for Rest Routes.
 *
 * @package EightshiftLibs\Rest\Routes
 */

declare( strict_types=1 );

namespace EightshiftLibs\Rest\Routes;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class RouteCli
 */
class RouteCli extends AbstractCli {

  /**
   * Output dir relative path.
   */
  const OUTPUT_DIR = 'src/rest/routes';

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
   * Define default develop props.
   *
   * @param array $args WPCLI eval-file arguments.
   *
   * @return array
   */
  public function get_develop_args( array $args ) : array {
    return [
      'endpoint_slug' => $args[1] ?? 'test',
      'method'        => $args[2] ?? 'get',
    ];
  }

  /**
   * Get WPCLI command doc.
   *
   * @return string
   */
  public function get_doc() : array {
    return [
      'shortdesc' => 'Generates REST-API Route in your project.',
      'synopsis' => [
        [
          'type'        => 'assoc',
          'name'        => 'endpoint_slug',
          'description' => 'The name of the endpoint slug. Example: test-route.',
          'optional'    => false,
        ],
        [
          'type'        => 'assoc',
          'name'        => 'method',
          'description' => 'HTTP verb must be one of: GET, POST, PATCH, PUT, or DELETE.',
          'optional'    => false,
        ],
      ],
    ];
  }

  public function __invoke( array $args, array $assoc_args ) { // phpcs:ignore Squiz.Commenting.FunctionComment.Missing, Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassBeforeLastUsed

    // Get Props.
    $endpoint_slug = $this->prepare_slug( $assoc_args['endpoint_slug'] );
    $method        = strtoupper( $assoc_args['method'] );

    // Get full class name.
    $class_name = $this->get_file_name( $endpoint_slug );
    $class_name = $this->get_class_short_name() . $class_name;

    // Read the template contents, and replace the placeholders with provided variables.
    $class = $this->get_example_template( __DIR__, $this->get_class_short_name() );

    // Replace stuff in file.
    $class = $this->rename_class_name_with_sufix( $this->get_class_short_name(), $class_name, $class );
    $class = $this->rename_namespace( $assoc_args, $class );
    $class = $this->rename_use( $assoc_args, $class );
    $class = str_replace( '/example-route', "/{$endpoint_slug}", $class );
    $class = str_replace( 'static::READABLE', static::VERB_ENUM[ $method ], $class );

    // Output final class to new file/folder and finish.
    $this->output_write( static::OUTPUT_DIR, $class_name, $class );
  }
}
