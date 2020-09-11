<?php
/**
 * File that holds class for PostTypeExample custom post type registration.
 *
 * @package EightshiftLibs\CustomPostType
 */

declare( strict_types=1 );

namespace EightshiftLibs\CustomPostType;

use EightshiftLibs\CustomPostType\AbstractPostType;

/**
 * Class PostTypeExample.
 */
class PostTypeExample extends AbstractPostType {

  /**
   * Post type slug constant.
   *
   * @var string
   */
  const POST_TYPE_SLUG = 'example-slug';

  /**
   * URL slug for the custom post type.
   *
   * @var string
   */
  const POST_TYPE_URL_SLUG = 'example-url-slug';

  /**
   * Rest API Endpoint slug constant.
   *
   * @var string
   */
  const REST_API_ENDPOINT_SLUG = 'example-endpoint-slug';

  /**
   * Capability type for projects post type.
   *
   * @var string
   */
  const POST_CAPABILITY_TYPE = 'post';

  /**
   * Location of menu in sidebar.
   *
   * @var int
   */
  const MENU_POSITION = 20;

  /**
   * Set menu icon.
   *
   * @var string
   */
  const MENU_ICON = 'dashicons-analytics';

  /**
   * Get the slug to use for the Projects custom post type.
   *
   * @return string Custom post type slug.
   */
  protected function get_post_type_slug() : string {
    return self::POST_TYPE_SLUG;
  }

  /**
   * Get the arguments that configure the Projects custom post type.
   *
   * @return array Array of arguments.
   */
  protected function get_post_type_arguments() : array {

    return [
      'label'              => \esc_html__( 'Example Name', 'eightshift-libs' ),
      'public'             => true,
      'publicly_queryable' => true,
      'show_ui'            => true,
      'show_in_menu'       => true,
      'query_var'          => true,
      'capability_type'    => self::POST_CAPABILITY_TYPE,
      'has_archive'        => true,
      'rewrite'            => [ 'slug' => static::POST_TYPE_URL_SLUG ],
      'hierarchical'       => false,
      'menu_icon'          => static::MENU_ICON,
      'menu_position'      => static::MENU_POSITION,
      'supports'           => [ 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ],
      'show_in_rest'       => true,
      'rest_base'          => static::REST_API_ENDPOINT_SLUG,
    ];
  }
}
