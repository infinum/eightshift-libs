<?php
/**
 * Class that registers WPCLI command for Main.
 * 
 * Command Develop:
 * wp eval-file bin/cli.php create_main --skip-wordpress
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
   * Output class name.
   */
  const CLASS_NAME = 'Main';

  /**
   * Get WPCLI command name
   *
   * @return string
   */
  public static function get_command_name() : string {
    return 'create_main';
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
   * Get WPCLI command doc.
   *
   * @return string
   */
  public function get_doc() : array {
    return [
      'shortdesc' => 'Generates Main class file for all other features using service container pattern.',
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
