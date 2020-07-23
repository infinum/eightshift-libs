<?php
/**
 * File that holds base abstract class for custom post type registration.
 *
 * @package EightshiftLibs\CustomPostType
 */

declare( strict_types=1 );

namespace EightshiftLibs\CustomPostType;

use EightshiftLibs\Services\ServiceInterface;

/**
 * Abstract class AbstractPostType class.
 */
abstract class AbstractPostType implements ServiceInterface {

  /**
   * Register custom post type.
   *
   * @return void
   */
  public function register() {
    \add_action(
      'init',
      function() {
        \register_post_type(
          $this->get_post_type_slug(),
          $this->get_post_type_arguments()
        );
      }
    );
  }

  /**
   * Get the slug to use for the custom post type.
   *
   * @return string Custom post type slug.
   */
  abstract protected function get_post_type_slug() : string;

  /**
   * Get the arguments that configure the custom post type.
   *
   * @return array Array of arguments.
   */
  abstract protected function get_post_type_arguments() : array;
}
