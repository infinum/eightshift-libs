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
   * Output template name.
   */
  const TEMPLATE = 'FieldExample';

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
  * Generates REST-API Field in your project.
  *
  * --field_name=<field_name>
  * : The name of the endpoint slug. Example: title.
  *
  * --object_type=<object_type>
  * : Object(s) the field is being registered to. Example: post.
  *
    * --namespace=<namespace>
  * : Define your projects namespace. Default: EightshiftBoilerplate.
  *
  * --vendor_prefix=<vendor_prefix>
  * : Define your projects vendor prefix. Default: EightshiftBoilerplateVendor.
  *
  * ## EXAMPLES
  *
  *     wp boilerplate create_rest_field --field_name='title' --object_type='post' --namespace='EightshiftBoilerplate' --vendor_prefix='EightshiftBoilerplateVendor'
  */
  public function __invoke( array $args, array $assoc_args ) {

    // Get Props.
    $field_name  = $assoc_args['field_name'];
    $object_type = $assoc_args['object_type'];

    // Get full class name.
    $class_name = CliHelpers::get_class_name( $field_name );
    $class_name = static::CLASS_NAME . $class_name;

    // Read the template contents, and replace the placeholders with provided variables.
    $class = CliHelpers::get_template( __DIR__ . '/' . static::TEMPLATE . '.php' );

    // Remove unecesery stuff from props.
    $endpoint = str_replace( '_', '-', str_replace( ' ', '-', strtolower( $field_name ) ) );

    // Replace stuff in file.
    $class = CliHelpers::change_class_name( static::TEMPLATE, $class_name, $class );
    $class = CliHelpers::change_namespace( $assoc_args['namespace'], $class );
    $class = CliHelpers::change_use( $assoc_args['vendor_prefix'], $class );
    $class = str_replace( "example-post-type", $object_type, $class );
    $class = str_replace( "example-field", $endpoint, $class );

    // Output final class to new file/folder and finish.
    CliHelpers::output_write( static::OUTPUT_DIR, $class_name, $class );
  }
}
