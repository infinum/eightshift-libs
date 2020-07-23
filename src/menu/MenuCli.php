<?php
/**
 * Class that registers WPCLI command for Menu.
 * 
 * Command Develop:
 * wp eval-file bin/cli.php create_menu --skip-wordpress
 *
 * @package EightshiftLibs\Menu
 */

namespace EightshiftLibs\Menu;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\CliHelpers;

/**
 * Class MenuCli
 */
class MenuCli extends AbstractCli {

  /**
   * Output dir relative path.
   */
  const OUTPUT_DIR = 'src/menu';

  /**
   * Template name.
   */
  const TEMPLATE = 'MenuExample';

  /**
   * Output class name.
   */
  const CLASS_NAME = 'Menu';

  /**
   * Get WPCLI command name
   *
   * @return string
   */
  public function get_command_name() : string {
    return 'create_menu';
  }

  /**
   * Get WPCLI trigger class name.
   *
   * @return string
   */
  public function get_class_name() : string {
    return MenuCli::class;
  }

  /**
  * Generates menu class.
  *
  * --namespace=<namespace>
  * : Define your projects namespace. Default: EightshiftBoilerplate.
  *
  * --vendor_prefix=<vendor_prefix>
  * : Define your projects vendor prefix. Default: EightshiftBoilerplateVendor.
  *
  * ## EXAMPLES
  *
  *     wp boilerplate create_menu --namespace='EightshiftBoilerplate' --vendor_prefix='EightshiftBoilerplateVendor'
  */
  public function __invoke( array $args, array $assoc_args ) {

    // Read the template contents, and replace the placeholders with provided variables.
    $class = CliHelpers::get_template( __DIR__ . '/' . static::TEMPLATE . '.php' );

    // Replace stuff in file.
    $class = CliHelpers::change_class_name( static::TEMPLATE, static::CLASS_NAME, $class );
    $class = CliHelpers::change_namespace( $assoc_args['namespace'], $class );
    $class = CliHelpers::change_use( $assoc_args['vendor_prefix'], $class );
    $class = CliHelpers::change_text_domain( $assoc_args['namespace'], $class );

    // Output final class to new file/folder and finish.
    CliHelpers::output_write( static::OUTPUT_DIR, static::CLASS_NAME, $class );
  }
}
