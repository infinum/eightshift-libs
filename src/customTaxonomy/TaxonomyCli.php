<?php
/**
 * Class that registers WPCLI command for Custom Taxonomy.
 * 
 * Command Develop:
 * wp eval-file bin/cli.php create_taxonomy --skip-wordpress
 *
 * @package EightshiftLibs\CustomTaxonomy
 */

namespace EightshiftLibs\CustomTaxonomy;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class TaxonomyCli
 */
class TaxonomyCli extends AbstractCli {

  /**
   * Output dir relative path.
   */
  const OUTPUT_DIR = 'src/customTaxonomy';

  /**
   * Output class name.
   */
  const CLASS_NAME = 'Taxonomy';

  /**
   * Get WPCLI command name
   *
   * @return string
   */
  public function get_command_name() : string {
    return 'create_taxonomy';
  }

  /**
   * Get WPCLI trigger class name.
   *
   * @return string
   */
  public function get_class_name() : string {
    return TaxonomyCli::class;
  }

  /**
   * Get WPCLI command doc.
   *
   * @return string
   */
  public function get_doc() : array {
    return [
      'shortdesc' => 'Generates custom taxonomy class file.',
      'synopsis' => [
        [
          'type'        => 'assoc',
          'name'        => 'label',
          'description' => 'The label of the custom taxonomy to show in WP admin.',
          'optional'    => false,
        ],
        [
          'type'        => 'assoc',
          'name'        => 'taxonomy_slug',
          'description' => 'The name of the custom taxonomy slug. Example: location.',
          'optional'    => false,
        ],
        [
          'type'        => 'assoc',
          'name'        => 'rest_endpoint_slug',
          'description' => 'The name of the custom taxonomy REST-API endpoint slug. Example: locations.',
          'optional'    => false,
        ],
        [
          'type'        => 'assoc',
          'name'        => 'post_type_slug',
          'description' => 'The position where to assign the new custom taxonomy. Example: post.',
          'optional'    => false,
        ],
      ]
    ];
  }

  public function __invoke( array $args, array $assoc_args ) {

    // Get Props.
    $label              = $assoc_args['label'];
    $taxonomy_slug      = $this->prepare_slug( $assoc_args['taxonomy_slug'] );
    $rest_endpoint_slug = $this->prepare_slug( $assoc_args['rest_endpoint_slug'] );
    $post_type_slug     = $this->prepare_slug( $assoc_args['post_type_slug'] );

    // Get full class name.
    $class_name    = $this->get_file_name( $taxonomy_slug );
    $class_name    = static::CLASS_NAME . $class_name;

    // Read the template contents, and replace the placeholders with provided variables.
    $class = $this->get_example_template( __DIR__, static::CLASS_NAME );

    // Replace stuff in file.
    $class = $this->rename_class_name_with_sufix( static::CLASS_NAME, $class_name, $class );
    $class = $this->rename_namespace( $assoc_args, $class );
    $class = $this->rename_use( $assoc_args, $class );
    $class = $this->rename_text_domain( $assoc_args, $class );
    $class = str_replace( "example-slug", $taxonomy_slug, $class );
    $class = str_replace( "example-endpoint-slug", $rest_endpoint_slug, $class );
    $class = str_replace( "'post'", "'{$post_type_slug}'", $class );
    $class = str_replace( "Example Name", $label, $class );

    // Output final class to new file/folder and finish.
    $this->output_write( static::OUTPUT_DIR, $class_name, $class );
  }
}
