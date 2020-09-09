<?php
/**
 * Enqueue class used to define all script and style enqueues for Gutenberg blocks.
 *
 * @package EightshiftLibs\Enqueue\Blocks
 */

declare( strict_types=1 );

namespace EightshiftLibs\Enqueue\Blocks;

use EightshiftBoilerplate\Config\Config;
use EightshiftLibs\Enqueue\Blocks\AbstractEnqueueBlocks;
use EightshiftLibs\Manifest\ManifestInterface;

/**
 * Enqueue_Blocks class.
 */
class EnqueueBlocksExample extends AbstractEnqueueBlocks {

  /**
   * Create a new admin instance.
   *
   * @param ManifestInterface $manifest Inject manifest which holds data about assets from manifest.json.
   */
  public function __construct( ManifestInterface $manifest ) {
    $this->manifest = $manifest;
  }

  /**
   * Register all the hooks
   */
  public function register() : void {

    // Editor only script.
    add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_block_editor_script' ] );

    // Editor only style.
    add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_block_editor_style' ], 50 );

    // Editor and frontend style.
    add_action( 'enqueue_block_assets', [ $this, 'enqueue_block_style' ], 50 );

    // Frontend only script.
    add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_block_script' ] );
  }

  /**
   * Method that returns assets name used to prefix asset handlers.
   *
   * @return string
   */
  public function get_assets_prefix() : string {
    return Config::get_project_name();
  }

  /**
   * Method that returns assets version for versioning asset handlers.
   *
   * @return string
   */
  public function get_assets_version() : string {
    return Config::get_project_version();
  }
}
