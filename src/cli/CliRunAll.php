<?php
/**
 * Class that registers WPCLI command for Development Run All.
 * Only used for development and can't be called via WPCLI.
 * It will run all commands at the same time.
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

  public function __invoke( array $args, array $assoc_args ) {

    $this->run_reset();

    \WP_CLI::log( '--------------------------------------------------' );
    $this->get_eval_loop( Cli::CLASSES_LIST, true );
    \WP_CLI::log( '--------------------------------------------------' );

    \WP_CLI::success( 'All commands are finished.' );
  }
}
