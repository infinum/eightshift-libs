<?php
/**
 * Class that registers WPCLI command for Development Show All.
 * Only used for development and can't be called via WPCLI.
 * It will output all commands at the same time but it will not run them!
 * 
 * Command Develop:
 * wp eval-file bin/cli.php show_all --skip-wordpress
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
  public static function get_command_name() : string {
    return 'show_all';
  }

  /**
   * Get WPCLI trigger class name.
   *
   * @return string
   */
  public function get_class_name() : string {
    return CliShowAll::class;
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

    \WP_CLI::log( "COMMANDS FOR WP-CLI:" );

    foreach ( Cli::PUBLIC_CLASSES as $item ) {
      $class_name = new $item;

      \WP_CLI::log( "wp eval-file bin/cli.php {$class_name->get_command_name()} --skip-wordpress" );
    }

    \WP_CLI::log( "-----------------------------------------" );
    
    \WP_CLI::log( "COMMANDS FOR DEVELOPMENT:" );
    foreach ( Cli::DEVELOP_CLASSES as $item ) {
      $class_name = new $item;

      \WP_CLI::log( "wp eval-file bin/cli.php {$class_name->get_command_name()} --skip-wordpress" );
    }

    \WP_CLI::log( "-----------------------------------------" );

    // Return success.
    \WP_CLI::success( 'All commands are outputed.' );
  }
}
