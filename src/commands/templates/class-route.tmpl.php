<?php
/**
 * The class register route for $class_name endpoint
 *
 * @package Eightshift_Boilerplate\Rest
 */

namespace Eightshift_Boilerplate\Rest;

use Eightshift_Libs\Core\ConfigDataInterface;

/**
 * Class Example_Route
 */
class %CLASS_NAME% extends AbstractBaseRoute implements CallableRouteInterface {

  /**
   * Route slug
   *
   * @var string
   */
  const ENDPOINT_SLUG = '/%ENDPOINT%';

  /**
   * Instance variable of project config data.
   *
   * @var object
   */
  protected $config;

  /**
   * Create a new instance that injects classes
   *
   * @param ConfigDataInterface $config Inject config which holds data regarding project details.
   */
  public function __construct( ConfigDataInterface $config ) {
    $this->config = $config;
  }

  /**
   * Method that returns project Route namespace.
   *
   * @return string Project namespace for REST route.
   */
  protected function get_namespace(): string {
    return $this->config->get_project_routes_namespace();
  }

  /**
   * Method that returns project route version.
   *
   * @return string Route version as a string.
   */
  protected function get_version(): string {
    return $this->config->get_project_routes_version();
  }

  /**
   * Get the base url of the route
   *
   * @return string The base URL for route you are adding.
   */
  protected function get_route_name(): string {
    return static::ENDPOINT_SLUG;
  }

  /**
   * Get callback arguments array
   *
   * @return array Either an array of options for the endpoint, or an array of arrays for multiple methods.
   */
  protected function get_callback_arguments(): array {
    return [
      'methods'  => static::%VERB%,
      'callback' => [ $this, 'route_callback' ],
    ];
  }

  /**
   * Method that returns rest response
   *
   * @param \WP_REST_Request $request Data got from endpoint url.
   *
   * @return WP_REST_Response|mixed If response generated an error, WP_Error, if response
   *                                is already an instance, WP_HTTP_Response, otherwise
   *                                returns a new WP_REST_Response instance.
   */
  public function route_callback( \WP_REST_Request $request ) {
    return \rest_ensure_response();
  }
}
