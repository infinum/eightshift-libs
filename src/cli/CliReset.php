<?php
/**
 * Class that registers WPCLI command for Development Reset.
 * Only used for development and can't be called via WPCLI.
 * It will delete CLI output directory.
 * 
 * Command Develop:
 * wp eval-file bin/cli.php reset --skip-wordpress
 *
 * @package EightshiftLibs\Cli
 */

namespace EightshiftLibs\Cli;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class CliReset
 */
class CliReset extends AbstractCli {

  /**
   * Get WPCLI command name
   *
   * @return string
   */
  public function get_command_name() : string {
    return 'reset';
  }

  /**
   * Get WPCLI trigger class name.
   *
   * @return string
   */
  public function get_class_name() : string {
    return CliReset::class;
  }

  /**
   * Get WPCLI command doc.
   *
   * @return string
   */
  public function get_doc() : array {
    return [];
  }

  /**
   * Reset method.
   *
   * @param array $args
   * @param array $assoc_args
   * @return void
   */
  public function __invoke( array $args, array $assoc_args ) {

    $output_dir = $this->get_output_dir( '' );

    system( "rm -rf " . escapeshellarg( $output_dir ) );

    // Return success.
    \WP_CLI::success( 'Output directory successfully removed.' );
  }
}
