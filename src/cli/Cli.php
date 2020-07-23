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
   * Define test output namespace.
   */
  const NAMESPACE = 'EightshiftBoilerplate';

    /**
   * Define test output vendor prefix.
   */
  const VENDOR_PREFIX = 'EightshiftBoilerplateVendor';

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
        $this->run_command(
          new MainCli(),
          $this->combine_args( $args ),
        );
        break;

      case 'create_rest_route':
        $this->run_command(
          new RouteCli(),
          $this->combine_args(
            $args,
            [
              'endpoint_slug' => $args[3] ?? 'test',
              'method'        => $args[4] ?? 'get',
            ]
          ),
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
    if ( ! function_exists( 'add_action' ) ) {
      // Run if in development from library env.
      $class->__invoke(
        [],
        $args
      );
    } else {
      // Run if normal WPCLI.
      $class->register( $this->command_parent_name );
    }
  }

  /**
   * Define common attrs.
   *
   * @param array $args      Arguments from WPCLI command.
   * @param array $args_user Arguments from WPCLI command user defined.
   *
   * @return array
   */
  public function combine_args( array $args = [], array $args_user = [] ) : array {
    return array_merge(
      [
      'namespace'     => $args[1] ?? static::NAMESPACE,
      'vendor_prefix' => $args[2] ?? static::VENDOR_PREFIX,
      ],
      $args_user,
    );
  }
}
