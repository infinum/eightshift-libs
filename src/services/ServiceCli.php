<?php
/**
 * Class that registers WPCLI command for Service Example.
 * 
 * Command Develop:
 * wp eval-file bin/cli.php create_service --skip-wordpress
 *
 * @package EightshiftLibs\Services
 */

namespace EightshiftLibs\Services;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class ServiceCli
 */
class ServiceCli extends AbstractCli {

  /**
   * Output dir relative path.
   */
  const OUTPUT_DIR = 'src';

  /**
   * Template name.
   */
  const TEMPLATE = 'Service';

  /**
   * Get WPCLI command name
   *
   * @return string
   */
  public function get_command_name() : string {
    return 'create_service';
  }

  /**
   * Get WPCLI trigger class name.
   *
   * @return string
   */
  public function get_class_name() : string {
    return ServiceCli::class;
  }

  /**
   * Define default develop props.
   *
   * @param array $args WPCLI eval-file arguments.
   *
   * @return array
   */
  public function get_develop_args( array $args ) : array {
    return [
      'folder'    => $args[1] ?? 'testFolder',
      'file_name' => $args[2] ?? 'Test slass',
    ];
  }

  /**
   * Get WPCLI command doc.
   *
   * @return string
   */
  public function get_doc() : array {
    return [
      'shortdesc' => 'Generates empty generic service class.',
      'synopsis' => [
        [
          'type'        => 'assoc',
          'name'        => 'folder',
          'description' => 'The output folder path relative to src folder. Example: main or main/config',
          'optional'    => false,
        ],
        [
          'type'        => 'assoc',
          'name'        => 'file_name',
          'description' => 'The output file name. Example: Main',
          'optional'    => false,
        ],
      ],
    ];
  }

  public function __invoke( array $args, array $assoc_args ) {

    // FIX namespace and better handle folder structure.

    // Get Props.
    $folder    = $assoc_args['folder'];
    $file_name = $this->prepare_slug( $assoc_args['file_name'] );

    // Get full class name.
    $class_name = $this->get_file_name( $file_name );

    // Read the template contents, and replace the placeholders with provided variables.
    $class = $this->get_example_template( __DIR__, static::TEMPLATE );

    // Replace stuff in file.
    $class = $this->rename_class_name( $class_name, $class );
    $class = $this->rename_namespace( $assoc_args, $class );
    $class = $this->rename_use( $assoc_args, $class );

    // Output final class to new file/folder and finish.
    $this->output_write( static::OUTPUT_DIR . '/' . $folder, $class_name, $class );
  }
}
