<?php
/**
 * File that holds faq custom post type registration details
 *
 * @since 1.0.0
 * @package Custom_Namespace\Examples
 *
 * TODO: Refactor and test
 */

namespace Custom_Namespace\Examples;

use Eightshift_Libs\Custom_Post_Type\Base_Post_Type;
use Eightshift_Libs\Custom_Post_Type\Label_Generator;

/**
 * Class that initializes the faq custom post type and taxonomy
 *
 * @since 1.0.0
 */
final class Example_Faq_Post_Type extends Base_Post_Type {

  /**
   * The custom post type type slug
   *
   * @var string
   */
   const POST_TYPE_SLUG = 'faq';

  /**
   * The custom post type menu icon
   *
   * @var string
   */
   const MENU_ICON = 'dashicons-list-view';

  /**
   * Get the slug to use for the custom post type.
   *
   * @return string Custom post type slug.
   */
  protected function get_post_type_slug() : string {
    return self::POST_TYPE_SLUG;
  }

  /**
   * Get the arguments that configure the custom post type.
   *
   * @return array Array of arguments.
   */
  protected function get_post_type_arguments() : array {
    $nouns = [
      Label_Generator::SINGULAR_NAME_UC => esc_html_x( 'FAQ', 'post type upper case singular name', 'eightshift-libs' ),
      Label_Generator::SINGULAR_NAME_LC => esc_html_x( 'faq', 'post type lower case singular name', 'eightshift-libs' ),
      Label_Generator::PLURAL_NAME_UC   => esc_html_x( 'FAQs', 'post type upper case plural name', 'eightshift-libs' ),
      Label_Generator::PLURAL_NAME_LC   => esc_html_x( 'faqs', 'post type lower case plural name', 'eightshift-libs' ),
    ];

    return [
      'label'              => $nouns[ Label_Generator::SINGULAR_NAME_UC ],
      'labels'             => ( new Label_Generator() )->get_generated_labels( $nouns ),
      'public'             => true,
      'menu_position'      => 50,
      'menu_icon'          => self::MENU_ICON,
      'supports'           => array( 'title', 'revisions', 'editor' ),
      'has_archive'        => false,
      'show_in_rest'       => true,
      'publicly_queryable' => true,
      'capability_type'    => 'page',
      'map_meta_cap'       => true,
      'can_export'         => true,
    ];
  }
}
