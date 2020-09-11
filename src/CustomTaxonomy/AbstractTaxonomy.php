<?php
/**
 * File that holds base abstract class for custom taxonomy registration.
 *
 * @package EightshiftLibs\CustomTaxonomy
 */

declare( strict_types=1 );

namespace EightshiftLibs\CustomTaxonomy;

use EightshiftLibs\Services\ServiceInterface;

/**
 * Abstract class AbstractBaseTaxonomy class.
 */
abstract class AbstractTaxonomy implements ServiceInterface {

  /**
   * Register custom taxonomy.
   *
   * @return void
   */
  public function register() : void {
    \add_action( 'init', [ $this, 'taxonomy_register_callback' ] );
  }

  /**
   * Method that registers taxonomy that is used inside init hook.
   *
   * @return void
   */
  protected function taxonomy_register_callback() : void {
    \register_taxonomy(
      $this->get_taxonomy_slug(),
      $this->get_post_type_slug(),
      $this->get_taxonomy_arguments()
    );
  }

  /**
   * Get the slug of the custom taxonomy
   *
   * @return string Custom taxonomy slug.
   */
  abstract protected function get_taxonomy_slug() : string;

  /**
   * Get the post type slug(s) that use the taxonomy.
   *
   * @return string|array Custom post type slug or an array of slugs.
   */
  abstract protected function get_post_type_slug();

  /**
   * Get the arguments that configure the custom taxonomy.
   *
   * @return array Array of arguments.
   */
  abstract protected function get_taxonomy_arguments() : array;
}
