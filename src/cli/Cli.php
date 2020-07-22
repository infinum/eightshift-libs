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
   * Top level commands name.
   */
  protected $command_parent_name;

  /**
   * Run all CLI commands
   *
   * @param array $args WPCLI eval-file arguments.
   *
   * @return void
   */
  public function run_develop( array $args = [] ) {

    $command_name = $args[0] ?? '';

    switch ( $command_name ) {
      case 'create_rest_route':
        $this->rest_route(
          [
            'endpoint_slug' => $args[1] ?? 'test',
            'method'        => $args[2] ?? 'get',
          ]
        );
        break;
      default:
        \WP_CLI::error( 'First argument must be a valid command name. Your command is missing or not valid.' );
        break;
    }
  }

  /**
   * Run all CLI commands
   *
   * @param string $command_parent_name Define top level commands name.
   *
   * @return void
   */
  public function run( string $command_parent_name ) {
    $this->command_parent_name = $command_parent_name;

    $this->rest_route();
  }

  /**
   * Run Rest route command
   *
   * @return void
   */
  public function rest_route( array $args = [] ) {
    $route = new RouteCli();

    if ( function_exists( 'add_action' ) ) {
      $route->register( $this->command_parent_name );
    } else {
      $route->__invoke(
        [],
        $args
      );
    }
  }
}
