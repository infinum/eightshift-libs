<?php
/**
 * Class that registers WPCLI command for Main Services Container.
 * 
 * Command Develop:
 * wp eval-file bin/cli.php create_service_container 'temp' 'post' --skip-wordpress
 *
 * @package EightshiftLibs\Main
 */

namespace EightshiftLibs\Main;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\CliHelpers;

/**
 * Class MainCli
 */
class MainCli extends AbstractCli {

  /**
   * Output dir relative path.
   */
  const OUTPUT_DIR = 'src/main';

  /**
   * Output template name.
   */
  const TEMPLATE = 'MainExample';

  /**
   * Get WPCLI command name
   *
   * @return string
   */
  public function get_command_name() : string {
    return 'create_service_container';
  }

  /**
   * Get WPCLI trigger class name.
   *
   * @return string
   */
  public function get_class_name() : string {
    return MainCli::class;
  }

  /**
  * Generates Main entrypoint file for all other features using Service Container pattern.
  *
  * ## EXAMPLES
  * 
  *     wp boilerplate create_service_container
  */
  public function __invoke( array $args, array $assoc_args ) {

    // Get full class name.
    $class_name = CliHelpers::get_class_name( 'main' );

    // Read the template contents, and replace the placeholders with provided variables.
    $template_file = CliHelpers::get_template( __DIR__ . '/' . static::TEMPLATE . '.php' );

    // Replace stuff in file.
    $class = str_replace( 'MainExample', $class_name, $template_file );

    // Output final class to new file/folder and finish.
    CliHelpers::output_write( static::OUTPUT_DIR, $class_name, $class );
  }
}
