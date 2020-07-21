<?php
/**
 * The class file that holds abstract class for REST routes registration
 *
 * @package EightshiftLibs\Rest
 */

declare( strict_types=1 );

namespace EightshiftLibs\Rest;

use EightshiftLibs\Core\ServiceInterface;

/**
 * Abstract base route class
 */
abstract class AbstractRoute implements RouteInterface, ServiceInterface {

  /**
   * A register method holds register_rest_route funtion to register api route.
   *
   * @return void
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
   * @return string Project namespace for REST route.
   */
  abstract protected function get_namespace() : string;

  /**
   * Method that returns project route version.
   *
   * @return string Route version as a string.
   */
  abstract protected function get_version() : string;

  /**
   * Get the base url of the route
   *
   * @return string The base URL for route you are adding.
   */
  abstract protected function get_route_name() : string;

  /**
   * Get callback arguments array
   *
   * @return array Either an array of options for the endpoint, or an array of arrays for multiple methods.
   */
  abstract protected function get_callback_arguments() : array;

  /**
   * Override the existing route
   * True overrides, false merges (with newer overriding if duplicate keys exist).
   *
   * @return bool If the route already exists, should we override it?
   */
  protected function override_route() : bool {
    return false;
  }
}
