<?php
/**
 * The class file that holds abstract class for REST routes registration
 *
 * @package EightshiftLibs\Rest\Routes
 */

namespace EightshiftLibs\Cli;

use EightshiftLibs\Rest\Routes\RouteCli;

/**
 * Abstract base route class
 */
class Cli {

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

  public function run() {
    $this->route();
  }

  public function route() {
    $route = new RouteCli( $this->root );

    if ( function_exists( 'add_action' ) ) {
      $route->register();
    } else {
      $route->callback(
        [
          'Test',
          'GET'
        ]
      );
    }
  }
}
