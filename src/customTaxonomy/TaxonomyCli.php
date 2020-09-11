<?php
/**
 * Class that registers WPCLI command for Custom Taxonomy.
 *
 * @package EightshiftLibs\CustomTaxonomy
 */

declare( strict_types=1 );

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
   * Define default develop props.
   *
   * @param array $args WPCLI eval-file arguments.
   *
   * @return array
   */
  public function get_develop_args( array $args ) : array {
    return [
      'label'              => $args[1] ?? 'Locations',
      'slug'               => $args[2] ?? 'location',
      'rest_endpoint_slug' => $args[3] ?? 'locations',
      'post_type_slug'     => $args[4] ?? 'post',
    ];
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
          'name'        => 'slug',
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
      ],
    ];
  }

  public function __invoke( array $args, array $assoc_args ) { // phpcs:ignore Squiz.Commenting.FunctionComment.Missing, Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassBeforeLastUsed

    // Get Props.
    $label              = $assoc_args['label'];
    $slug               = $this->prepare_slug( $assoc_args['slug'] );
    $rest_endpoint_slug = $this->prepare_slug( $assoc_args['rest_endpoint_slug'] );
    $post_type_slug     = $this->prepare_slug( $assoc_args['post_type_slug'] );

    // Get full class name.
    $class_name = $this->get_file_name( $slug );
    $class_name = $this->get_class_short_name() . $class_name;

    // Read the template contents, and replace the placeholders with provided variables.
    $class = $this->get_example_template( __DIR__, $this->get_class_short_name() );

    // Replace stuff in file.
    $class = $this->rename_class_name_with_sufix( $this->get_class_short_name(), $class_name, $class );
    $class = $this->rename_namespace( $assoc_args, $class );
    $class = $this->rename_use( $assoc_args, $class );
    $class = $this->rename_text_domain( $assoc_args, $class );
    $class = str_replace( 'example-slug', $slug, $class );
    $class = str_replace( 'example-endpoint-slug', $rest_endpoint_slug, $class );
    $class = str_replace( "'post'", "'{$post_type_slug}'", $class );
    $class = str_replace( 'Example Name', $label, $class );

    // Output final class to new file/folder and finish.
    $this->output_write( static::OUTPUT_DIR, $class_name, $class );
  }
}
