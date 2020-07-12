<?php
/**
 * File holding the REST route generation command
 *
 * @package Eightshift_Libs\Commands
 */

declare(strict_types=1);

namespace Eightshift_Libs\Commands;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class Generate_Rest_Route
 *
 * Symfony command generator class used for REST route generation.
 *
 * @package Eightshift_Libs\Commands
 */
class Generate_Rest_Route extends Command {

  /**
   * Command name property
   *
   * @var string Command name.
   */
  protected static $defaultName = 'generate:route'; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase

  /**
   * Configures the current command
   *
   * @inheritDoc
   */
  protected function configure() : void {
    $this
      ->setDescription( 'Generate a custom REST class' )
      ->setHelp( 'This command will create a template class for the custom WordPress REST API endpoint.' )
      ->addArgument( 'endpoint-name', InputArgument::REQUIRED, 'Pass the slug of your endpoint. Example: my-endpoint' )
      ->addArgument( 'method', InputArgument::REQUIRED, 'Pass the desired HTTP transport method (verb). Can be one of: \'GET\', \'POST\', \'PATCH\', \'PUT\', or \'DELETE\'' );
  }

  /**
   * Execute the current command
   *
   * @param InputInterface  $input  Input values.
   * @param OutputInterface $output Output values.
   *
   * @return int
   * @throws Exception Validation exceptions.
   */
  protected function execute( InputInterface $input, OutputInterface $output ) : int {
    $io = new SymfonyStyle( $input, $output );

    /**
     * Passed endpoint name argument
     *
     * @var string
     */
    $endpoint_slug = $input->getArgument( 'endpoint-name' );

    /**
     * Passed method argument
     *
     * @var string
     */
    $method = $input->getArgument( 'method' );

    if ( empty( $endpoint_slug ) ) {
      throw new Exception( 'Endpoint slug empty' );
    }

    if ( ! in_array( $method, [ 'GET', 'POST', 'PATCH', 'PUT', 'DELETE' ], true ) ) {
      throw new Exception(
        sprintf( 'HTTP verb must be one of: \'GET\', \'POST\', \'PATCH\', \'PUT\', or \'DELETE\'. %s provided.', $method )
      );
    }

    $endpoint = str_replace( '_', '-', str_replace( ' ', '-', strtolower( $endpoint_slug ) ) );
    $class    = explode( '_', str_replace( '-', '_', str_replace( ' ', '_', strtolower( $endpoint_slug ) ) ) );

    $class_name = implode( '_', array_map( function( $item ) { // phpcs:ignore PEAR.Functions.FunctionCallSignature
        return ucfirst( $item );
    }, $class ) ); // phpcs:ignore PEAR.Functions.FunctionCallSignature

    $verb_mapping = [
      'GET'    => 'READABLE',
      'POST'   => 'CREATABLE',
      'PATCH'  => 'EDITABLE',
      'PUT'    => 'UPDATEABLE',
      'DELETE' => 'DELETABLE',
    ];

    $class_boilerplate = $this->get_class_boilerplate( $class_name, $endpoint, $verb_mapping[ $method ] );

    $rest_dir  = dirname( __FILE__, 2 ) . '/rest';
    $directory = $rest_dir . "/class-{$endpoint}.php";

    $fp = fopen( $directory, 'wb' ); // phpcs:ignore WordPress.WP.AlternativeFunctions

    if ( $fp !== false ) {
        fwrite( $fp, $class_boilerplate ); // phpcs:ignore WordPress.WP.AlternativeFunctions
        fclose( $fp ); // phpcs:ignore WordPress.WP.AlternativeFunctions
    } else {
        $io->error( "File class-{$endpoint}.php couldn't be created in {$rest_dir} directory. There was an error." );
    }

    $io->success( "File class-{$endpoint}.php successfully created in {$rest_dir} directory." );

    return 0;
  }

  /**
   * Class boilerplate
   *
   * @param string $class_name Name of the REST class to create.
   * @param string $endpoint   Name of the endpoint of the REST class.
   * @param string $verb       HTTP verb.
   *
   * @return string Class boilerplate contents.
   */
  private function get_class_boilerplate( string $class_name, string $endpoint, string $verb ) {
    return <<<EOT
<?php
/**
 * The class register route for $class_name endpoint
 *
 * @package Eightshift_Boilerplate\Rest
 */

namespace Eightshift_Boilerplate\Rest;

use Eightshift_Libs\Rest\Base_Route;
use Eightshift_Libs\Rest\Callable_Route;
use Eightshift_Libs\Core\Config_Data;

/**
 * Class Example_Route
 */
class $class_name extends Base_Route implements Callable_Route {

  /**
   * Route slug
   *
   * @var string
   */
  const ENDPOINT_SLUG = '/$endpoint';

  /**
   * Instance variable of project config data.
   *
   * @var object
   */
  protected \$config;

  /**
   * Create a new instance that injects classes
   *
   * @param Config_Data \$config Inject config which holds data regarding project details.
   */
  public function __construct( Config_Data \$config ) {
    \$this->config = \$config;
  }

  /**
   * Method that returns project Route namespace.
   *
   * @return string Project namespace for REST route.
   */
  protected function get_namespace() : string {
    return \$this->config->get_project_routes_namespace();
  }

  /**
   * Method that returns project route version.
   *
   * @return string Route version as a string.
   */
  protected function get_version() : string {
    return \$this->config->get_project_routes_version();
  }

  /**
   * Get the base url of the route
   *
   * @return string The base URL for route you are adding.
   */
  protected function get_route_name() : string {
    return static::ENDPOINT_SLUG;
  }

  /**
   * Get callback arguments array
   *
   * @return array Either an array of options for the endpoint, or an array of arrays for multiple methods.
   */
  protected function get_callback_arguments() : array {
    return [
      'methods'  => static::$verb,
      'callback' => [ \$this, 'route_callback' ],
    ];
  }

  /**
   * Method that returns rest response
   *
   * @param  \WP_REST_Request \$request Data got from endpoint url.
   *
   * @return WP_REST_Response|mixed If response generated an error, WP_Error, if response
   *                                is already an instance, WP_HTTP_Response, otherwise
   *                                returns a new WP_REST_Response instance.
   */
  public function route_callback( \WP_REST_Request \$request ) {
    return rest_ensure_response();
  }
}
EOT;
  }
}
