<?php
/**
 * The Media specific functionality.
 *
 * @since   1.0.0
 * @package EightshiftLibs\Media
 */

declare( strict_types=1 );

namespace EightshiftLibs\Media;

use EightshiftLibs\Core\ServiceInterface;

/**
 * Class Media
 *
 * This class handles all media options. Sizes, Types, Features, etc.
 *
 * @since 1.0.0
 */
class Media implements ServiceInterface {

  /**
   * Register all the hooks
   *
   * @return void
   *
   * @since 1.0.0
   */
  public function register() {
    add_action( 'after_setup_theme', [ $this, 'add_theme_support' ], 20 );
  }

  /**
   * Enable theme support
   * for full list check: https://developer.wordpress.org/reference/functions/add_theme_support/
   *
   * @return void
   *
   * @since 1.0.0
   */
  public function add_theme_support() {
    \add_theme_support( 'title-tag' );
    \add_theme_support( 'html5' );
    \add_theme_support( 'post-thumbnails' );
  }
}
