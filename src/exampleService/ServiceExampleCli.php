<?php
/**
 * Class that registers WPCLI command for Service Example.
 *
 * @package EightshiftLibs\ExampleService
 */

namespace EightshiftLibs\ExampleService;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class ServiceExampleCli
 */
class ServiceExampleCli extends AbstractCli {

  /**
   * Output dir relative path.
   */
  const OUTPUT_DIR = 'src';

  /**
   * Template name.
   */
  const TEMPLATE = 'Service';

  /**
   * Define default develop props.
   *
   * @param array $args WPCLI eval-file arguments.
   *
   * @return array
   */
  public function get_develop_args( array $args ) : array {
    return [
      'folder'    => $args[1] ?? 'testFolder/novi',
      'file_name' => $args[2] ?? 'TestTest',
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

    // Create new namespace from folder structure.
    $folder_parts = array_map(
      function( $item ) {
        return ucfirst( $item );
      },
      explode('/', $folder)
    );

    $new_namespace = "\\" . implode('\\', $folder_parts);
    $class         = str_replace('\\ExampleService', $new_namespace, $class);

    // Output final class to new file/folder and finish.
    $this->output_write( static::OUTPUT_DIR . '/' . $folder, $class_name, $class );
  }
}
