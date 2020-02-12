<?php
/**
 * Enqueue class used to define all script and style enqueues for Gutenberg blocks.
 *
 * @package Eightshift_Libs\Enqueue
 */

declare( strict_types=1 );

namespace Eightshift_Libs\Enqueue;

use Eightshift_Libs\Core\Service;
use Eightshift_Libs\Manifest\Manifest_Data;
use Eightshift_Libs\Core\Config_Data;

/**
 * Enqueue_Blocks class.
 *
 * @since 1.0.0
 */
class Enqueue_Blocks extends Assets {

  const IN_FOOTER = true;

  const BLOCKS_EDITOR_SCRIPT_URI = 'applicationBlocksEditor.js';
  const BLOCKS_EDITOR_STYLE_URI  = 'applicationBlocksEditor.css';

  const BLOCKS_STYLE_URI  = 'applicationBlocks.css';
  const BLOCKS_SCRIPT_URI = 'applicationBlocks.js';

  /**
   * Instance variable of project config data.
   *
   * @var object
   *
   * @since 2.0.0
   */
  protected $config;

  /**
   * Instance variable of manifest data.
   *
   * @var object
   *
   * @since 2.0.0
   */
  protected $manifest;

  /**
   * Create a new admin instance.
   *
   * @param Config_Data   $config Inject config which holds data regarding project details.
   * @param Manifest_Data $manifest Inject manifest which holds data about assets from manifest.json.
   *
   * @since 2.0.0 Adding Config as a new DI.
   * @since 2.0.0
   */
  public function __construct( Config_Data $config, Manifest_Data $manifest ) {
    $this->config   = $config;
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
   *
   * @return void
   */
  public function enqueue_block_editor_script() {
    $handler = "{$this->config::get_project_prefix()}-block-editor-scripts";

    \wp_register_script(
      $handler,
      $this->manifest->get_assets_manifest_item( static::BLOCKS_EDITOR_SCRIPT_URI ),
      $this->get_admin_script_dependencies(),
      $this->config::get_project_version(),
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
   *
   * @return void
   */
  public function enqueue_block_editor_style() {
    $handler = "{$this->config::get_project_prefix()}-block-editor-style";

    \wp_register_style(
      $handler,
      $this->manifest->get_assets_manifest_item( static::BLOCKS_EDITOR_STYLE_URI ),
      $this->get_admin_style_dependencies(),
      $this->config::get_project_version(),
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
   *
   * @return void
   */
  public function enqueue_block_style() {
    $handler = "{$this->config::get_project_prefix()}-block-style";

    \wp_register_style(
      $handler,
      $this->manifest->get_assets_manifest_item( static::BLOCKS_STYLE_URI ),
      $this->get_frontend_style_dependencies(),
      $this->config::get_project_version(),
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
   *
   * @return void
   */
  public function enqueue_block_script() {
    $handler = "{$this->config::get_project_prefix()}-block-scripts";

    \wp_register_script(
      $handler,
      $this->manifest->get_assets_manifest_item( static::BLOCKS_SCRIPT_URI ),
      $this->get_frontend_script_dependencies(),
      $this->config::get_project_version(),
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
   */
  protected function get_admin_style_dependencies() : array {
    return [ "{$this->config::get_project_prefix()}-block-style" ];
  }

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
