<?php
/**
 * Class that registers WPCLI command for ModifyAdminAppearance.
 * 
 * Command Develop:
 * wp eval-file bin/cli.php create_modify_admin_appearance --skip-wordpress
 *
 * @package EightshiftLibs\ModifyAdminAppearance
 */

namespace EightshiftLibs\ModifyAdminAppearance;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class ModifyAdminAppearanceCli
 */
class ModifyAdminAppearanceCli extends AbstractCli {

  /**
   * Output dir relative path.
   */
  const OUTPUT_DIR = 'src/modifyAdminAppearance';

  /**
   * Template name.
   */
  const TEMPLATE = 'ModifyAdminAppearanceExample';

  /**
   * Output class name.
   */
  const CLASS_NAME = 'ModifyAdminAppearance';

  /**
   * Get WPCLI command name
   *
   * @return string
   */
  public function get_command_name() : string {
    return 'create_modify_admin_appearance';
  }

  /**
   * Get WPCLI trigger class name.
   *
   * @return string
   */
  public function get_class_name() : string {
    return ModifyAdminAppearanceCli::class;
  }

  /**
   * Get WPCLI command doc.
   *
   * @return string
   */
  public function get_doc() : array {
    return [
      'shortdesc' => 'Generates Modify Admin Appearance class.',
    ];
  }

  public function __invoke( array $args, array $assoc_args ) {

    // Read the template contents, and replace the placeholders with provided variables.
    $class = $this->get_example_template( __DIR__ . '/' . static::TEMPLATE . '.php' );

    // Replace stuff in file.
    $class = $this->rename_class_name( static::TEMPLATE, static::CLASS_NAME, $class );
    $class = $this->rename_namespace( $assoc_args, $class );
    $class = $this->rename_use( $assoc_args, $class );

    // Output final class to new file/folder and finish.
    $this->output_write( static::OUTPUT_DIR, static::CLASS_NAME, $class );
  }
}
