<?php
/**
 * Abstract class that holds all methods for WPCLI options.
 *
 * @package EightshiftLibs\Cli
 */

namespace EightshiftLibs\Cli;

/**
 * Class AbstractCli
 */
abstract class AbstractCli implements CliInterface {

  /**
   * Project root absolute path
   */
  protected $root;

  /**
   * Output dir relative path.
   */
  const OUTPUT_DIR = '';

  /**
   * Output template name.
   */
  const TEMPLATE = '';

  /**
   * Register method for WPCLI command
   *
   * @return void
   */
  public function register() {
    \add_action( 'cli_init', [ $this, 'register_command'] );
  }

  /**
   * Call internal method for passing arguments
   *
   * @param array $args Array of arguments form terminal
   *
   * @return void
   */
  public function __invoke( array $args ) {
    \WP_CLI::success( $args[0] );
  }

  /**
   * Method that creates actual WPCLI command in terminal.
   *
   * @return void
   */
  public function register_command() {
    \WP_CLI::add_command( $this->get_command_name(), $this->get_class_name() );
  }
}
