<?php
/**
 * File that holds base abstract class for custom taxonomy registration.
 *
 * @package Eightshift_Libs\Custom_Taxonomy
 */

declare( strict_types=1 );

namespace Eightshift_Libs\Custom_Taxonomy;

use Eightshift_Libs\Core\Service;

/**
 * Abstract class Base_Taxonomy class.
 *
 * @since 0.1.0
 */
abstract class Base_Taxonomy implements Service {

  /**
   * Register custom taxonomy.
   *
   * @return void
   *
   * @since 0.8.0 Removing type hinting void for php 7.0.
   * @since 0.1.0
   */
  public function register() {
    add_action(
      'init',
      function() {
        register_taxonomy(
          $this->get_taxonomy_slug(),
          $this->get_post_type_slug(),
          $this->get_taxonomy_arguments()
        );
      }
    );
  }

  /**
   * Get the slug of the custom taxonomy
   *
   * @return string Custom taxonomy slug.
   *
   * @since 0.1.0
   */
  abstract protected function get_taxonomy_slug() : string;

  /**
   * Get the post type slug(s) that use the taxonomy.
   *
   * @return string|array Custom post type slug or an array of slugs.
   *
   * @since 0.1.0
   */
  abstract protected function get_post_type_slug();

  /**
   * Get the arguments that configure the custom taxonomy.
   *
   * @return array Array of arguments.
   *
   * @since 0.1.0
   */
  abstract protected function get_taxonomy_arguments() : array;
}
