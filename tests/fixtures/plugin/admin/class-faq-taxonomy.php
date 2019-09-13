<?php
/**
 * File that holds taxonomy class for faq taxonomy registration
 *
 * @since 0.9.0
 * @package Eightshift_Libs\Tests\Fixtures\Plugin
 */

namespace Eightshift_Libs\Tests\Fixtures\Plugin;

use Eightshift_Libs\Custom_Taxonomy\Base_Taxonomy;
use Eightshift_Libs\Custom_Post_Type\Label_Generator;

/**
 * FAQ Taxonomy class
 *
 * This class will register the custom taxonomy.
 *
 * @since 1.0.0
 */
class Faq_Taxonomy extends Base_Taxonomy {
  /**
   * The systems custom taxonomy type slug
   *
   * @var string
   */
   const TAXONOMY_SLUG = 'faq-category';

  /**
   * The custom post type type slug
   *
   * @var string
   */
   const POST_TYPE_SLUG = Faq_Post_Type::POST_TYPE_SLUG;

  /**
   * Get the slug of the custom taxonomy
   *
   * @return string Custom taxonomy slug.
   */
  protected function get_taxonomy_slug() : string {
    return self::TAXONOMY_SLUG;
  }

  /**
   * Get the post type slug to use the taxonomy to
   *
   * @return string Custom post type slug.
   */
  protected function get_post_type_slug() : string {
    return self::POST_TYPE_SLUG;
  }

  /**
   * Get the arguments that configure the custom taxonomy.
   *
   * @return array Array of arguments.
   */
  protected function get_taxonomy_arguments() : array {
    $nouns = [
      Label_Generator::SINGULAR_NAME_UC => esc_html_x( 'FAQ category', 'post type upper case singular name', 'eighshift-libs' ),
      Label_Generator::SINGULAR_NAME_LC => esc_html_x( 'faq category', 'post type lower case singular name', 'eighshift-libs' ),
      Label_Generator::PLURAL_NAME_UC   => esc_html_x( 'FAQ categories', 'post type upper case plural name', 'eighshift-libs' ),
      Label_Generator::PLURAL_NAME_LC   => esc_html_x( 'faq categories', 'post type lower case plural name', 'eighshift-libs' ),
    ];

    return [
      'label'              => $nouns[ Label_Generator::SINGULAR_NAME_UC ],
      'labels'             => ( new Label_Generator() )->get_generated_labels( $nouns ),
      'hierarchical'          => true,
      'show_ui'               => true,
      'show_admin_column'     => true,
      'update_count_callback' => '_update_post_term_count',
      'query_var'             => true,
    ];
  }
}
