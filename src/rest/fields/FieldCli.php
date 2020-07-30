<?php
/**
 * Class that registers WPCLI command for Rest Fields.
 * 
 * Command Develop:
 * wp eval-file bin/cli.php create_rest_field --skip-wordpress
 *
 * @package EightshiftLibs\Rest\Fields
 */

namespace EightshiftLibs\Rest\Fields;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\CliHelpers;

/**
 * Class FieldCli
 */
class FieldCli extends AbstractCli {

  /**
   * Output dir relative path.
   */
  const OUTPUT_DIR = 'src/rest/fields';

  /**
   * Output class name.
   */
  const CLASS_NAME = 'Field';

  /**
   * Get WPCLI command name
   *
   * @return string
   */
  public function get_command_name() : string {
    return 'create_rest_field';
  }

  /**
   * Get WPCLI trigger class name.
   *
   * @return string
   */
  public function get_class_name() : string {
    return FieldCli::class;
  }

  /**
   * Get WPCLI command doc.
   *
   * @return string
   */
  public function get_doc() : array {
    return [
      'shortdesc' => 'Generates REST-API Field in your project.',
      'synopsis' => [
        [
          'type'        => 'assoc',
          'name'        => 'field_name',
          'description' => 'The name of the endpoint slug. Example: title.',
          'optional'    => false,
        ],
        [
          'type'        => 'assoc',
          'name'        => 'object_type',
          'description' => 'Object(s) the field is being registered to. Example: post.',
          'optional'    => false,
        ],
      ]
    ];
  }

  public function __invoke( array $args, array $assoc_args ) {

    // Get Props.
    $field_name  = $this->prepare_slug( $assoc_args['field_name'] );
    $object_type = $this->prepare_slug( $assoc_args['object_type'] );

    // Get full class name.
    $class_name = $this->get_file_name( $field_name );
    $class_name = static::CLASS_NAME . $class_name;

    // Read the template contents, and replace the placeholders with provided variables.
    $class = $this->get_example_template( __DIR__, static::CLASS_NAME );

    // Replace stuff in file.
    $class = $this->rename_class_name_with_sufix( static::CLASS_NAME, $class_name, $class );
    $class = $this->rename_namespace( $assoc_args, $class );
    $class = $this->rename_use( $assoc_args, $class );
    $class = str_replace( "example-post-type", $object_type, $class );
    $class = str_replace( "example-field", $field_name, $class );

    // Output final class to new file/folder and finish.
    $this->output_write( static::OUTPUT_DIR, $class_name, $class );
  }
}
