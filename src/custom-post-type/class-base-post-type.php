<?php
/**
 * File that holds base abstract class for custom post type registration
 *
 * @since   1.0.0
 * @package Eightshift_Libs\Custom_Post_Type
 */

namespace Eightshift_Libs\Custom_Post_Type;

use Eightshift_Libs\Core\Service;

/**
 * Abstract class Base_Post_Type.
 */
abstract class Base_Post_Type implements Service {

  /**
   * Register custom post type and taxonomy.
   *
   * @return void
   *
   * @since 1.0.0
   */
  public function register() : void {
    add_action(
      'init',
      function() {
        register_post_type( $this->get_post_type_slug(), $this->get_post_type_arguments() );
      }
    );
  }

  /**
   * Get the slug to use for the custom post type.
   *
   * @return string Custom post type slug.
   *
   * @since 1.0.0
   */
  abstract protected function get_post_type_slug() : string;

  /**
   * Get the arguments that configure the custom post type.
   *
   * @return array Array of arguments.
   *
   * @since 1.0.0
   */
  abstract protected function get_post_type_arguments() : array;
}
