<?php
/**
 * Class Blocks is the base class for Gutenberg blocks registration.
 * It provides the ability to register custom blocks using manifest.json.
 *
 * @package EightshiftLibs\Blocks
 */

declare( strict_types=1 );

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Config\Config;
use EightshiftLibs\Blocks\AbstractBlocks;

/**
 * Class Blocks
 */
class Blocks extends AbstractBlocks {

  /**
   * Register all the hooks
   *
   * @return void
   */
  public function register() : void {

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

  /**
   * Get blocks absolute path.
   * Prefix path is defined by project config.
   *
   * @return string
   */
  protected function get_blocks_path() : string {
    return Config::get_project_path() . '/src/Blocks';
  }
}
