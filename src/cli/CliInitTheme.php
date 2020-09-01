<?php
/**
 * Class that registers WPCLI command initial setup of theme project.
 * 
 * Command Develop:
 * wp eval-file bin/cli.php init_theme --skip-wordpress
 *
 * @package EightshiftLibs\Cli
 */

namespace EightshiftLibs\Cli;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class CliInitTheme
 */
class CliInitTheme extends AbstractCli {

  /**
   * Get WPCLI command name
   *
   * @return string
   */
  public static function get_command_name() : string {
    return 'init_theme';
  }

  /**
   * Get WPCLI trigger class name.
   *
   * @return string
   */
  public function get_class_name() : string {
    return CliInitTheme::class;
  }

  /**
   * Get WPCLI command doc.
   *
   * @return string
   */
  public function get_doc() : array {
    return [
      'shortdesc' => 'Generates initial setup for WordPress theme project.',
    ];
  }

  public function __invoke( array $args, array $assoc_args ) {

    // TODO : finish this command and test.
    \WP_CLI::log( "COMMANDS FOR WP-CLI:" );

    foreach ( Cli::INIT_THEME_CLASSES as $item ) {
      if ( function_exists( 'add_action' ) ) {
      \WP_CLI::runcommand( "{$this->command_parent_name} {$item::get_command_name()}" );
    } else {
      \WP_CLI::runcommand( "eval-file bin/cli.php {$item::get_command_name()} --skip-wordpress" );
      }
    }
  }
}
