<?php
/**
 * File that holds base abstract class for custom taxonomy registration
 *
 * @since 1.0.0
 * @package Eightshift_Libs\Custom_Taxonomy
 */

namespace Eightshift_Libs\Custom_Taxonomy;

use Eightshift_Libs\Core\Service;

/**
 * Abstract class Base_Taxonomy.
 *
 * @since 1.0.0
 */
abstract class Base_Taxonomy implements Service {

  /**
   * Register custom taxonomy.
   */
  public function register() : void {
    add_action(
      'init',
      function() {
        register_taxonomy(
          $this->get_taxonomy_slug(),
          [ $this->get_post_type_slug() ],
          $this->get_taxonomy_arguments()
        );
      }
    );
  }

  /**
   * Get the slug of the custom taxonomy
   *
   * @return string Custom taxonomy slug.
   */
  abstract protected function get_taxonomy_slug() : string;

  /**
   * Get the post type slug to use the taxonomy to
   *
   * @return string Custom post type slug.
   */
  abstract protected function get_post_type_slug() : string;

  /**
   * Get the arguments that configure the custom taxonomy.
   *
   * @return array Array of arguments.
   */
  abstract protected function get_taxonomy_arguments() : array;
}
