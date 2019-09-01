<?php
/**
 * Fake test file that mocks the usage of Rest Routes
 *
 * This is an example of a test class that can be used as a dependency for other classes.
 *
 * @since   0.9.0
 * @package Eightshift_Libs\Tests\Fixtures\Plugin
 */

namespace Eightshift_Libs\Tests\Fixtures\Plugin;

use Eightshift_Libs\Core\Registrable;
use Eightshift_Libs\Routes\Callable_Route;
use Eightshift_Libs\Routes\Registrable_Route;
use Eightshift_Libs\Routes\Route;

class Rest implements Registrable, Route, Callable_Route, Registrable_Route {
  const NAMESPACE_NAME = 'test-plugin';
  const VERSION        = '/v1';
  const ROUTE_NAME     = '/test-route';

  protected $data;

  /**
   * Dependency injection example
   *
   * @param Data $data Some dependency that we'll implement in our DI container.
   */
  public function __construct( Data $data ) {
    $this->data = $data;
  }

  public function register() {
    add_action( 'rest_api_init', [ $this, 'register_route' ] );
  }

  public function register_route() {
    register_rest_route(
      self::NAMESPACE_NAME . self::VERSION,
      $this->get_callback_route(),
      $this->get_callback_arguments(),
      $this->override_route()
    );
  }

  public function route_callback( \WP_REST_Request $request ) {
    return $this->data->get_data();
  }

  protected function get_callback_route() : string {
    return static::ROUTE_NAME;
  }

  protected function get_callback_arguments() : array {
    return [
      'methods'  => static::CREATABLE,
      'callback' => [ $this, 'route_callback' ],
    ];
  }

  protected function override_route() : bool {
    return false;
  }
}
