<?php
/**
 * The Blog_Taxonomy specific functionality.
 *
 * @package EightshiftLibs\CustomTaxonomy
 */

declare( strict_types=1 );

namespace EightshiftLibs\CustomTaxonomy;

use EightshiftLibs\CustomTaxonomy\AbstractTaxonomy;

/**
 * Class TaxonomyExample
 */
class TaxonomyExample extends AbstractTaxonomy {

  /**
   * Register custom taxonomy.
   *
   * @return void
   */
  public function register() : void {
    \add_action(
      'init',
      function() {
        \register_taxonomy(
          $this->get_taxonomy_slug(),
          [ $this->get_post_type_slug() ],
          $this->get_taxonomy_arguments()
        );
      }
    );
  }

  /**
   * Taxonomy slug costant.
   *
   * @var string
   */
  const TAXONOMY_SLUG = 'example-slug';

  /**
   * Rest API Endpoint slug costant.
   *
   * @var string
   */
  const REST_API_ENDPOINT_SLUG = 'example-endpoint-slug';

  /**
   * Get the slug of the custom taxonomy
   *
   * @return string Custom taxonomy slug.
   */
  protected function get_taxonomy_slug() : string {
    return static::TAXONOMY_SLUG;
  }

  /**
   * Get the post type slug to use the taxonomy.
   *
   * @return string Custom post type slug.
   */
  protected function get_post_type_slug() : string {
    return 'post';
  }

  /**
   * Get the arguments that configure the custom taxonomy.
   *
   * @return array Array of arguments.
   */
  protected function get_taxonomy_arguments() : array {
    return [
      'hierarchical'      => true,
      'label'             => \esc_html__( 'Example Name', 'eightshift-libs' ),
      'show_ui'           => true,
      'show_admin_column' => true,
      'show_in_nav_menus' => false,
      'public'            => true,
      'show_in_rest'      => true,
      'query_var'         => true,
      'rest_base'         => static::REST_API_ENDPOINT_SLUG,
      'rewrite'           => array(
        'hierarchical'  => true,
        'with_front'    => false,
      ),
    ];
  }
}
