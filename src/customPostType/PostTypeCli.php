<?php
/**
 * Class that registers WPCLI command for Custom Taxonomy.
 *
 * @package EightshiftLibs\CustomPostType
 */

namespace EightshiftLibs\CustomPostType;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class PostTypeCli
 */
class PostTypeCli extends AbstractCli {

  /**
   * Output dir relative path.
   */
  const OUTPUT_DIR = 'src/customPostType';

  /**
   * Define default develop props.
   *
   * @param array $args WPCLI eval-file arguments.
   *
   * @return array
   */
  public function get_develop_args( array $args ) : array {
    return [
      'label'              => $args[1] ?? 'Products',
      'slug'               => $args[2] ?? 'product',
      'rewrite_url'        => $args[3] ?? 'product',
      'rest_endpoint_slug' => $args[4] ?? 'products',
      'capability'         => $args[5] ?? 'post',
      'menu_position'      => $args[6] ?? 40,
      'menu_icon'          => $args[7] ?? 'admin-settings',
    ];
  }

  /**
   * Get WPCLI command doc.
   *
   * @return string
   */
  public function get_doc() : array {
    return [
      'shortdesc' => 'Generates custom post type class file.',
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
          'description' => 'The custom post type slug. Example: location.',
          'optional'    => false,
        ],
        [
          'type'        => 'assoc',
          'name'        => 'rewrite_url',
          'description' => 'The custom post type url. Example: location.',
          'optional'    => false,
        ],
        [
          'type'        => 'assoc',
          'name'        => 'rest_endpoint_slug',
          'description' => 'The name of the custom post type REST-API endpoint slug. Example: locations.',
          'optional'    => false,
        ],
        [
          'type'        => 'assoc',
          'name'        => 'capability',
          'description' => 'The default capability for the custom post types. Example: post.',
          'optional'    => true,
        ],
        [
          'type'        => 'assoc',
          'name'        => 'menu_position',
          'description' => 'The default menu position for the custom post types. Example: 20.',
          'optional'    => true,
        ],
        [
          'type'        => 'assoc',
          'name'        => 'menu_icon',
          'description' => 'The default menu icon for the custom post types. Example: dashicons-analytics.',
          'optional'    => true,
        ],
      ],
    ];
  }

  public function __invoke( array $args, array $assoc_args ) {

    // Get Props.
    $label              = $assoc_args['label'];
    $slug               = $this->prepare_slug( $assoc_args['slug'] );
    $rewrite_url        = $this->prepare_slug( $assoc_args['rewrite_url'] );
    $rest_endpoint_slug = $this->prepare_slug( $assoc_args['rest_endpoint_slug'] );
    $capability         = $assoc_args['capability'] ?? '';
    $menu_position      = $assoc_args['menu_position'] ?? '';
    $menu_icon          = $assoc_args['menu_icon'] ?? '';

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
    $class = str_replace( 'example-url-slug', $rewrite_url, $class );
    $class = str_replace( 'example-endpoint-slug', $rest_endpoint_slug, $class );
    $class = str_replace( 'Example Name', $label, $class );

    if ( ! empty( $capability ) ) {
      $class = str_replace( "'post'", "'{$capability}'", $class );
    }

    if ( ! empty( $menu_position ) ) {
      $class = str_replace( '20', $menu_position, $class );
    }

    if ( ! empty( $menu_icon ) ) {
      $class = str_replace( 'dashicons-analytics', $menu_icon, $class );
    }

    // Output final class to new file/folder and finish.
    $this->output_write( static::OUTPUT_DIR, $class_name, $class );
  }
}
