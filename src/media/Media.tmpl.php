<?php
/**
 * The Media specific functionality.
 *
 * @package EightshiftBoilerplate\Media
 */

declare( strict_types=1 );

namespace EightshiftBoilerplate\Media;

use EightshiftBoilerplateVendor\EightshiftLibs\Media\AbstractMedia;

/**
 * Class Media
 *
 * This class handles all media options. Sizes, Types, Features, etc.
 */
class Media extends AbstractMedia {

  /**
   * Register all the hooks
   *
   * @return void
   */
  public function register() {
    add_action( 'after_setup_theme', [ $this, 'add_custom_image_sizes' ], 20 );
    add_action( 'after_setup_theme', [ $this, 'add_theme_support' ], 20 );
  }
}
