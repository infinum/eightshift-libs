<?php
/**
 * Class that registers WPCLI command for Blocks.
 * 
 * Command Develop:
 * wp eval-file bin/cli.php create_enqueue_blocks --skip-wordpress
 *
 * @package EightshiftLibs\Enqueue\Blocks
 */

namespace EightshiftLibs\Enqueue\Blocks;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class EnqueueBlocksCli
 */
class EnqueueBlocksCli extends AbstractCli {

  /**
   * Output dir relative path.
   */
  const OUTPUT_DIR = 'src/enqueue/blocks';

  /**
   * Output class name.
   */
  const CLASS_NAME = 'EnqueueBlocks';

  /**
   * Get WPCLI command name
   *
   * @return string
   */
  public function get_command_name() : string {
    return 'create_enqueue_blocks';
  }

  /**
   * Get WPCLI trigger class name.
   *
   * @return string
   */
  public function get_class_name() : string {
    return EnqueueBlocksCli::class;
  }

  /**
   * Get WPCLI command doc.
   *
   * @return string
   */
  public function get_doc() : array {
    return [
      'shortdesc' => 'Generates Enqueue Blocks class.',
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
