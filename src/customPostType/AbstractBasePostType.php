<?php
/**
 * File that holds base abstract class for custom post type registration.
 *
 * @package EightshiftLibs\CustomPostType
 */

declare( strict_types=1 );

namespace EightshiftLibs\CustomPostType;

use EightshiftLibs\Core\ServiceInterface;

/**
 * Abstract class AbstractBasePostType class.
 *
 * @since 0.1.0
 */
abstract class AbstractBasePostType implements ServiceInterface {

  /**
   * Register custom post type.
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
        register_post_type( $this->get_post_type_slug(), $this->get_post_type_arguments() );
      }
    );
  }

  /**
   * Get the slug to use for the custom post type.
   *
   * @return string Custom post type slug.
   *
   * @since 0.1.0
   */
  abstract protected function get_post_type_slug() : string;

  /**
   * Get the arguments that configure the custom post type.
   *
   * @return array Array of arguments.
   *
   * @since 0.1.0
   */
  abstract protected function get_post_type_arguments() : array;
}
