<?php
/**
 * Enqueue class used to define all script and style enqueues for Gutenberg blocks.
 *
 * @package Eightshiftlibs\Enqueue
 */

declare( strict_types=1 );

namespace Eightshiftlibs\Enqueue;

use Eightshiftlibs\Manifest\ManifestDataInterface;

/**
 * Enqueue_Blocks class.
 *
 * @since 1.0.0
 */
class EnqueueBlocks extends AbstractAssets {

  const BLOCKS_EDITOR_SCRIPT_URI = 'applicationBlocksEditor.js';
  const BLOCKS_EDITOR_STYLE_URI  = 'applicationBlocksEditor.css';

  const BLOCKS_STYLE_URI  = 'applicationBlocks.css';
  const BLOCKS_SCRIPT_URI = 'applicationBlocks.js';

  /**
   * Instance variable of manifest data.
   *
   * @var ManifestDataInterface
   *
   * @since 2.0.0
   */
  protected $manifest;

  /**
   * Create a new admin instance.
   *
   * @param ManifestDataInterface $manifest Inject manifest which holds data about assets from manifest.json.
   *
   * @since 2.0.0 Adding Config as a new DI.
   * @since 2.2.0 removed Config from the dependency.
   */
  public function __construct( ManifestDataInterface $manifest ) {
    $this->manifest = $manifest;
  }

  /**
   * Register all the hooks
   *
   * @since 1.0.0
   */
  public function register() {

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
   * Enqueue blocks script for editor only.
   *
   * @since 1.0.0
   * @since 2.0.3 Added methods for overrides.
   *              Fixed static calls from config class.
   * @since 2.2.0 Removed config dependency.
   *
   * @return void
   */
  public function enqueue_block_editor_script() {
    $handler = "{$this->manifest->get_config()->get_project_prefix()}-block-editor-scripts";

    \wp_register_script(
      $handler,
      $this->manifest->get_assets_manifest_item( static::BLOCKS_EDITOR_SCRIPT_URI ),
      $this->get_admin_script_dependencies(),
      $this->manifest->get_config()->get_project_version(),
      $this->script_in_footer()
    );
    \wp_enqueue_script( $handler );
  }

  /**
   * Enqueue blocks style for editor only.
   *
   * @since 1.0.0
   * @since 2.0.3 Added methods for overrides.
   *              Fixed static calls from config class.
   * @since 2.2.0 Removed config dependency.
   *
   * @return void
   */
  public function enqueue_block_editor_style() {
    $handler = "{$this->manifest->get_config()->get_project_prefix()}-block-editor-style";

    \wp_register_style(
      $handler,
      $this->manifest->get_assets_manifest_item( static::BLOCKS_EDITOR_STYLE_URI ),
      $this->get_admin_style_dependencies(),
      $this->manifest->get_config()->get_project_version(),
      $this->get_media()
    );

    \wp_enqueue_style( $handler );
  }

  /**
   * Enqueue blocks style for editor and frontend.
   *
   * @since 1.0.0
   * @since 2.0.3 Added methods for overrides.
   *              Fixed static calls from config class.
   * @since 2.2.0 Removed config dependency.
   *
   * @return void
   */
  public function enqueue_block_style() {
    $handler = "{$this->manifest->get_config()->get_project_prefix()}-block-style";

    \wp_register_style(
      $handler,
      $this->manifest->get_assets_manifest_item( static::BLOCKS_STYLE_URI ),
      $this->get_frontend_style_dependencies(),
      $this->manifest->get_config()->get_project_version(),
      $this->get_media()
    );

    \wp_enqueue_style( $handler );
  }

  /**
   * Enqueue blocks script for frontend only.
   *
   * @since 1.0.0
   * @since 2.0.3 Added methods for overrides.
   *              Fixed static calls from config class.
   * @since 2.2.0 Removed config dependency.
   *
   * @return void
   */
  public function enqueue_block_script() {
    $handler = "{$this->manifest->get_config()->get_project_prefix()}-block-scripts";

    \wp_register_script(
      $handler,
      $this->manifest->get_assets_manifest_item( static::BLOCKS_SCRIPT_URI ),
      $this->get_frontend_script_dependencies(),
      $this->manifest->get_config()->get_project_version(),
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
   *
   * @since 2.0.3
   * @since 2.2.0 Removed config dependency.
   */
  protected function get_admin_style_dependencies() : array {
    return [ "{$this->manifest->get_config()->get_project_prefix()}-block-style" ];
  }

    /**
     * List of admin script dependencies
     *
     * @return array List of all the admin dependencies.
     *
     * @since 2.0.3
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
