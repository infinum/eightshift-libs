<?php
/**
 * Class that registers WPCLI command for Media.
 * 
 * Command Develop:
 * wp eval-file bin/cli.php create_media --skip-wordpress
 *
 * @package EightshiftLibs\Media
 */

namespace EightshiftLibs\Media;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\CliHelpers;

/**
 * Class MediaCli
 */
class MediaCli extends AbstractCli {

  /**
   * Output dir relative path.
   */
  const OUTPUT_DIR = 'src/media';

  /**
   * Template name.
   */
  const TEMPLATE = 'MediaExample';

  /**
   * Output class name.
   */
  const CLASS_NAME = 'Media';

  /**
   * Get WPCLI command name
   *
   * @return string
   */
  public function get_command_name() : string {
    return 'create_media';
  }

  /**
   * Get WPCLI trigger class name.
   *
   * @return string
   */
  public function get_class_name() : string {
    return MediaCli::class;
  }

  /**
  * Generates media class.
  *
  * --namespace=<namespace>
  * : Define your projects namespace. Default: EightshiftBoilerplate.
  *
  * --vendor_prefix=<vendor_prefix>
  * : Define your projects vendor prefix. Default: EightshiftBoilerplateVendor.
  *
  * ## EXAMPLES
  *
  *     wp boilerplate create_media --namespace='EightshiftBoilerplate' --vendor_prefix='EightshiftBoilerplateVendor'
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
