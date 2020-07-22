<?php
/**
 * The class file that holds abstract class for WPCLI
 *
 * @package EightshiftLibs\Cli
 */

namespace EightshiftLibs\Cli;

use EightshiftLibs\Rest\Routes\RouteCli;

/**
 * Class Cli
 */
class Cli {

  /**
   * Project root absolute path
   */
  protected $root;

  /**
   * Create a new instance that injects classes
   *
   * @param string $root Absolute path to project root.
   */
  public function __construct( string $root ) {
    $this->root = $root;
  }

  /**
   * Run all CLI commands
   *
   * @return void
   */
  public function run() {
    $this->route();
  }

  /**
   * Run Rest route command
   *
   * @return void
   */
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
