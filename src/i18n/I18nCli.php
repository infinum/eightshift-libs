<?php
/**
 * Class that registers WPCLI command for I18n.
 * 
 * Command Develop:
 * wp eval-file bin/cli.php i18n --skip-wordpress
 *
 * @package EightshiftLibs\I18n
 */

namespace EightshiftLibs\I18n;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\CliHelpers;

/**
 * Class I18nCli
 */
class I18nCli extends AbstractCli {

  /**
   * Output dir relative path.
   */
  const OUTPUT_DIR = 'src/i18n';

  /**
   * Template name.
   */
  const TEMPLATE = 'I18nExample';

  /**
   * Output class name.
   */
  const CLASS_NAME = 'I18n';

  /**
   * Get WPCLI command name
   *
   * @return string
   */
  public function get_command_name() : string {
    return 'create_i18n';
  }

  /**
   * Get WPCLI trigger class name.
   *
   * @return string
   */
  public function get_class_name() : string {
    return I18nCli::class;
  }

  /**
  * Generates i18n language class.
  *
  * --namespace=<namespace>
  * : Define your projects namespace. Default: EightshiftBoilerplate.
  *
  * --vendor_prefix=<vendor_prefix>
  * : Define your projects vendor prefix. Default: EightshiftBoilerplateVendor.
  *
  * ## EXAMPLES
  *
  *     wp boilerplate create_i18n --namespace='EightshiftBoilerplate' --vendor_prefix='EightshiftBoilerplateVendor'
  */
  public function __invoke( array $args, array $assoc_args ) {

    // Read the template contents, and replace the placeholders with provided variables.
    $class = CliHelpers::get_template( __DIR__ . '/' . static::TEMPLATE . '.php' );

    // Replace stuff in file.
    $class = CliHelpers::change_class_name( static::TEMPLATE, static::CLASS_NAME, $class );
    $class = CliHelpers::change_namespace( $assoc_args['namespace'], $class );
    $class = CliHelpers::change_use( $assoc_args['vendor_prefix'], $class );

    // Output final class to new file/folder and finish.
    CliHelpers::output_write( static::OUTPUT_DIR, static::CLASS_NAME, $class );
  }
}
