<?php
/**
 * Class that registers WPCLI command for Blocks.
 * 
 * Command Develop:
 * wp eval-file bin/cli.php create_enqueue_admin --skip-wordpress
 *
 * @package EightshiftLibs\Enqueue\Admin
 */

namespace EightshiftLibs\Enqueue\Admin;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class EnqueueAdminCli
 */
class EnqueueAdminCli extends AbstractCli {

  /**
   * Output dir relative path.
   */
  const OUTPUT_DIR = 'src/enqueue/admin';

  /**
   * Output class name.
   */
  const CLASS_NAME = 'EnqueueAdmin';

  /**
   * Get WPCLI command name
   *
   * @return string
   */
  public function get_command_name() : string {
    return 'create_enqueue_admin';
  }

  /**
   * Get WPCLI trigger class name.
   *
   * @return string
   */
  public function get_class_name() : string {
    return EnqueueAdminCli::class;
  }

  /**
   * Get WPCLI command doc.
   *
   * @return string
   */
  public function get_doc() : array {
    return [
      'shortdesc' => 'Generates Enqueue Admin class.',
    ];
  }

  public function __invoke( array $args, array $assoc_args ) {

    $class_name = static::CLASS_NAME;

    // Read the template contents, and replace the placeholders with provided variables.
    $class = $this->get_example_template( __DIR__, $class_name );

    // Replace stuff in file.
    $class = $this->rename_class_name( $class_name, $class );
    $class = $this->rename_namespace( $assoc_args, $class );
    $class = $this->rename_use( $assoc_args, $class );

    // Output final class to new file/folder and finish.
    $this->output_write( static::OUTPUT_DIR, $class_name, $class, "{$class_name}::class" );
  }
}
