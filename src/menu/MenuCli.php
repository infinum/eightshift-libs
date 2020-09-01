<?php
/**
 * Class that registers WPCLI command for Menu.
 *
 * @package EightshiftLibs\Menu
 */

namespace EightshiftLibs\Menu;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class MenuCli
 */
class MenuCli extends AbstractCli {

  /**
   * Output dir relative path.
   */
  const OUTPUT_DIR = 'src/menu';

  /**
   * Get WPCLI command doc.
   *
   * @return string
   */
  public function get_doc() : array {
    return [
      'shortdesc' => 'Generates menu class.',
    ];
  }

  public function __invoke( array $args, array $assoc_args ) {

    $class_name = $this->get_class_short_name();

    // Read the template contents, and replace the placeholders with provided variables.
    $class = $this->get_example_template( __DIR__, $class_name );

    // Replace stuff in file.
    $class = $this->rename_class_name( $class_name, $class );
    $class = $this->rename_namespace( $assoc_args, $class );
    $class = $this->rename_use( $assoc_args, $class );
    $class = $this->rename_text_domain( $assoc_args, $class );

    // Output final class to new file/folder and finish.
    $this->output_write( static::OUTPUT_DIR, $class_name, $class );
  }
}
