<?php
/**
 * The class file that holds abstract class for REST routes registration
 *
 * @package Eightshift_Libs\Rest
 */

declare( strict_types=1 );

namespace Eightshift_Libs\Rest;

use Eightshift_Libs\Core\Config_Data;
use Eightshift_Libs\Core\Service;
use Eightshift_Libs\Rest\Route;

/**
 * Abstract base route class
 *
 * @since 2.0.0 Added in the project
 */
class Base_Route implements Route, Service {

  /**
   * Instance variable of project config data.
   *
   * @var object
   *
   * @since 2.0.0 Added in the project
   */
  protected $config;

  /**
   * Create a new instance that injects config data to get project specific details.
   *
   * @param Config_Data $config Inject config which holds data regarding project details.
   *
   * @since 2.0.0 Added in the project
   */
  public function __construct( Config_Data $config ) {
    $this->config = $config;
  }

  /**
   * Register the rest route.
   *
   * A register method holds register_rest_route funtion to register api route.
   *
   * @return void
   *
   * @since 2.0.0 Added in the project
   */
  public function register() : void {
    add_action(
      'rest_api_init',
      function() {
        register_rest_route(
          $this->get_namespace() . '/' . $this->get_version(),
          $this->get_route_name(),
          $this->get_callback_arguments(),
          $this->override_route()
        );
      }
    );
  }

  /**
   * Method that returns project Route namespace.
   *
   * @since 2.0.0 Added in the project
   */
  protected function get_namespace() : string {
    return $this->config->get_project_routes_namespace();
  }

  /**
   * Method that returns project route version.
   *
   * @since 2.0.0 Added in the project
   */
  protected function get_version() : string {
    return $this->config->get_project_routes_version();
  }

  /**
   * Get the base url of the route
   *
   * @return string The base URL for route you are adding.
   *
   * @since 2.0.0 Added in the project
   */
  protected function get_route_name() : string {
    return '';
  }

  /**
   * Get callback arguments array
   *
   * @return array Either an array of options for the endpoint, or an array of arrays for multiple methods.
   *
   * @since 2.0.0 Added in the project
   */
  protected function get_callback_arguments() : array {
    return [];
  }

  /**
   * Override the existing route
   * True overrides, false merges (with newer overriding if duplicate keys exist).
   *
   * @return bool If the route already exists, should we override it?
   *
   * @since 2.0.0 Added in the project
   */
  protected function override_route() : bool {
    return false;
  }
}
