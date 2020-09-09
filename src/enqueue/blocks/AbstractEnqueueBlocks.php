<?php
/**
 * Enqueue class used to define all script and style enqueues for Gutenberg blocks.
 *
 * @package EightshiftLibs\Enqueue\Blocks
 */

declare( strict_types=1 );

namespace EightshiftLibs\Enqueue\Blocks;

use EightshiftLibs\Enqueue\AbstractAssets;
use EightshiftLibs\Manifest\ManifestInterface;

/**
 * Enqueue_Blocks class.
 */
abstract class AbstractEnqueueBlocks extends AbstractAssets {

  const BLOCKS_EDITOR_SCRIPT_URI = 'applicationBlocksEditor.js';
  const BLOCKS_EDITOR_STYLE_URI  = 'applicationBlocksEditor.css';

  const BLOCKS_STYLE_URI  = 'applicationBlocks.css';
  const BLOCKS_SCRIPT_URI = 'applicationBlocks.js';

  /**
   * Instance variable of manifest data.
   *
   * @var ManifestInterface
   */
  protected $manifest;

  /**
   * Enqueue blocks script for editor only.
   *
   * @return void
   */
  public function enqueue_block_editor_script() : void {
    $handler = "{$this->get_assets_prefix()}-block-editor-scripts";

    \wp_register_script(
      $handler,
      $this->manifest->get_assets_manifest_item( static::BLOCKS_EDITOR_SCRIPT_URI ),
      $this->get_admin_script_dependencies(),
      $this->get_assets_version(),
      $this->script_in_footer()
    );
    \wp_enqueue_script( $handler );
  }

  /**
   * Enqueue blocks style for editor only.
   *
   * @return void
   */
  public function enqueue_block_editor_style() : void {
    $handler = "{$this->get_assets_prefix()}-block-editor-style";

    \wp_register_style(
      $handler,
      $this->manifest->get_assets_manifest_item( static::BLOCKS_EDITOR_STYLE_URI ),
      $this->get_admin_style_dependencies(),
      $this->get_assets_version(),
      $this->get_media()
    );

    \wp_enqueue_style( $handler );
  }

  /**
   * Enqueue blocks style for editor and frontend.
   *
   * @return void
   */
  public function enqueue_block_style() : void {
    $handler = "{$this->get_assets_prefix()}-block-style";

    \wp_register_style(
      $handler,
      $this->manifest->get_assets_manifest_item( static::BLOCKS_STYLE_URI ),
      $this->get_frontend_style_dependencies(),
      $this->get_assets_version(),
      $this->get_media()
    );

    \wp_enqueue_style( $handler );
  }

  /**
   * Enqueue blocks script for frontend only.
   *
   * @return void
   */
  public function enqueue_block_script() : void {
    $handler = "{$this->get_assets_prefix()}-block-scripts";

    \wp_register_script(
      $handler,
      $this->manifest->get_assets_manifest_item( static::BLOCKS_SCRIPT_URI ),
      $this->get_frontend_script_dependencies(),
      $this->get_assets_version(),
      $this->script_in_footer()
    );

    \wp_enqueue_script( $handler );
  }

  /**
   * Get style dependencies
   *
   * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style/
   *
   * @return array List of all the style dependencies.
   */
  protected function get_admin_style_dependencies() : array {
    return [ "{$this->get_assets_prefix()}-block-style" ];
  }

    /**
     * List of admin script dependencies
     *
     * @return array List of all the admin dependencies.
     */
  protected function get_admin_script_dependencies() : array {
    return [
      'jquery',
      'wp-components',
      'wp-blocks',
      'wp-element',
      'wp-editor',
      'wp-date',
      'wp-data',
      'wp-i18n',
      'wp-viewport',
      'wp-blob',
      'wp-url',
    ];
  }
}
