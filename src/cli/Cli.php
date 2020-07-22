<?php
/**
 * The class file that holds abstract class for WPCLI
 *
 * @package EightshiftLibs\Cli
 */

namespace EightshiftLibs\Cli;

use EightshiftLibs\Main\MainCli;
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
      case 'create_service_container':
        $this->run_command( new MainCli() );
        break;

      case 'create_rest_route':
        $this->run_command(
          new RouteCli(),
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
   * Run all CLI commands for normal WPCLI.
   *
   * @param string $command_parent_name Define top level commands name.
   *
   * @return void
   */
  public function run( string $command_parent_name ) {
    $this->command_parent_name = $command_parent_name;

    $this->run_command( new MainCli() );
    $this->run_command( new RouteCli() );
  }

  /**
   * Run simgle command depending on what type of env.
   *
   * @return void
   */
  public function run_command( $class, array $args = [] ) {

    // Run if in development from library env.
    if ( ! function_exists( 'add_action' ) ) {
      $class->__invoke(
        [],
        $args
      );
    }

    // Run if normal WPCLI.
    $class->register( $this->command_parent_name );
  }
}
