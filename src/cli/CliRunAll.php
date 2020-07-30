<?php
/**
 * Class that registers WPCLI command for Development Run All.
 * Only used for development and can't be called via WPCLI.
 * It will run all commands at the same time.
 * 
 * Command Develop:
 * wp eval-file bin/cli.php run_all --skip-wordpress
 *
 * @package EightshiftLibs\Cli
 */

namespace EightshiftLibs\Cli;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class CliRunAll
 */
class CliRunAll extends AbstractCli {

  /**
   * Get WPCLI command name
   *
   * @return string
   */
  public function get_command_name() : string {
    return 'run_all';
  }

  /**
   * Get WPCLI trigger class name.
   *
   * @return string
   */
  public function get_class_name() : string {
    return CliRunAll::class;
  }

  /**
   * Get WPCLI command doc.
   *
   * @return string
   */
  public function get_doc() : array {
    return [];
  }

  public function __invoke( array $args, array $assoc_args ) {

    $reset = new CliReset();

    $reset->__invoke(
      [],
      $reset->get_develop_args( $args )
    );

    \WP_CLI::log( '--------------------------------------------------' );

    foreach ( Cli::PUBLIC_CLASSES as $item ) {
      $class_name = new $item;

      $class_name->__invoke(
        [],
        $class_name->get_develop_args( $args )
      );
    }

    // Return success.
    \WP_CLI::success( 'All commands are finished.' );
  }
}
