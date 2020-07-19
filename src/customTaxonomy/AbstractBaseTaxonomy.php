<?php
/**
 * File that holds base abstract class for custom taxonomy registration.
 *
 * @package EightshiftLibs\Custom_Taxonomy
 */

declare( strict_types=1 );

namespace EightshiftLibs\CustomTaxonomy;

use EightshiftLibs\Core\ServiceInterface;

/**
 * Abstract class AbstractBaseTaxonomy class.
 */
abstract class AbstractBaseTaxonomy implements ServiceInterface {

  /**
   * Register custom taxonomy.
   *
   * @return void
   */
  public function register() {
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
   * Get the post type slug to use the taxonomy.
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
