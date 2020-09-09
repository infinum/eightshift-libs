<?php
/**
 * Class that registers WPCLI command for Development Show All.
 * Only used for development and can't be called via WPCLI.
 * It will output all commands at the same time but it will not run them!
 *
 * @package EightshiftLibs\Cli
 */

namespace EightshiftLibs\Cli;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class CliShowAll
 */
class CliShowAll extends AbstractCli {

  /**
   * Get WPCLI command name
   *
   * @return string
   */
  public function get_command_name() : string {
    return 'show_all';
  }

  public function __invoke( array $args, array $assoc_args ) {

    \WP_CLI::log( \WP_CLI::colorize( '%mCommands for wp-cli and development:%n' ) );
    $this->get_eval_loop( Cli::CLASSES_LIST );
    \WP_CLI::log( '-----------------------------------------' );

    \WP_CLI::log( \WP_CLI::colorize( '%mCommands for wp-cli only:%n' ) );
    $this->get_eval_loop( Cli::PUBLIC_CLASSES );
    \WP_CLI::log( '-----------------------------------------' );

    \WP_CLI::log( \WP_CLI::colorize( '%mCommands for development:%n' ) );
    $this->get_eval_loop( Cli::DEVELOP_CLASSES );
    \WP_CLI::log( '-----------------------------------------' );

    \WP_CLI::log( \WP_CLI::colorize( '%mCommands for project setup:%n' ) );
    $this->get_eval_loop( Cli::SETUP_CLASSES );
    \WP_CLI::log( '-----------------------------------------' );

    \WP_CLI::success( 'All commands are outputed.' );
  }
}
