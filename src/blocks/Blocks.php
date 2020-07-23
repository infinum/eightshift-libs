<?php
/**
 * Class Blocks is the base class for Gutenberg blocks registration.
 * It provides the ability to register custom blocks using manifest.json.
 *
 * @package EightshiftLibs\Blocks
 */

declare( strict_types=1 );

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Config\ConfigInterface;
use EightshiftLibs\Blocks\AbstractBlocks;

/**
 * Class Blocks
 */
class Blocks extends AbstractBlocks {

  /**
   * Create a new instance that injects config data to get project specific details.
   *
   * @param ConfigInterface $config Inject config which holds data regarding project details.
   */
  public function __construct( ConfigInterface $config ) {
    $this->config = $config;
  }

  /**
   * Register all the hooks
   *
   * @return void
   */
  public function register() {

    // // Register all custom blocks.
    add_action( 'init', [ $this, 'get_blocks_data_full_raw' ], 10 );
    add_action( 'init', [ $this, 'register_blocks' ], 11 );

    // Remove P tags from content.
    remove_filter( 'the_content', 'wpautop' );

    // Create new custom category for custom blocks.
    add_filter( 'block_categories', [ $this, 'get_custom_category' ] );

    add_action( 'after_setup_theme', [ $this, 'add_theme_support' ], 25 );

    add_action( 'after_setup_theme', [ $this, 'change_editor_color_palette' ], 11 );
  }
}
