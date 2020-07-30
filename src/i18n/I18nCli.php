<?php
/**
 * Class that registers WPCLI command for I18n.
 * 
 * Command Develop:
 * wp eval-file bin/cli.php create_i18n --skip-wordpress
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
   * Get WPCLI command doc.
   *
   * @return string
   */
  public function get_doc() : array {
    return [
      'shortdesc' => 'Generates i18n language class.',
    ];
  }

  public function __invoke( array $args, array $assoc_args ) {

    // Read the template contents, and replace the placeholders with provided variables.
    $class = $this->get_example_template( __DIR__, static::CLASS_NAME );

    // Replace stuff in file.
    $class = $this->rename_class_name( static::CLASS_NAME, $class );
    $class = $this->rename_namespace( $assoc_args, $class );
    $class = $this->rename_use( $assoc_args, $class );

    // Output final class to new file/folder and finish.
    $this->output_write( static::OUTPUT_DIR, static::CLASS_NAME, $class );
  }
}
