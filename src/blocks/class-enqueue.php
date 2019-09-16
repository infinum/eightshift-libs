<?php
/**
 * Enqueue class used to define all script and style enqueues for Gutenberg blocks.
 *
 * @since   1.0.0
 * @package Eightshift_Blocks
 */

namespace Eightshift_Blocks;

use Eightshift_Libs\Core\Service;
use Eightshift_Blocks\Manifest_Data;
use Eightshift_Blocks\Exception\Missing_Assets_Manifest;
use Eightshift_Blocks\Exception\Missing_Assets_Manifest_Key;

/**
 * Enqueue class.
 *
 * @since 1.0.0
 */
abstract class Enqueue implements Service, Manifest_Data {

  /**
   * Default project name variable used in enqueue methods.
   *
   * @var string
   *
   * @since 1.0.0
   */
  protected $project_name = 'eightshift-blocks';

  /**
   * Default project version variable used in enqueue methods.
   *
   * @var string
   *
   * @since 1.0.0
   */
  protected $project_version = '1.0.0';

  /**
   * Abstract method to provide projects manifest array.
   * Using this manifest you are able to provide project specific implementation of assets locations.
   *
   * @return array
   *
   * @since 1.0.0
   */
  abstract public function get_project_manifest() : array;

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
   *
   * @return void
   */
  public function enqueue_block_editor_script() {
    \wp_register_script(
      "{$this->get_project_name()}-block-editor-scripts",
      $this->get_project_manifest_value( $this->get_block_editor_script_key() ),
      array(
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
      ),
      $this->get_project_version(),
      true
    );
    \wp_enqueue_script( "{$this->get_project_name()}-block-editor-scripts" );
  }

  /**
   * Enqueue blocks style for editor only.
   *
   * @since 1.0.0
   *
   * @return void
   */
  public function enqueue_block_editor_style() {
    \wp_register_style(
      "{$this->get_project_name()}-block-editor-style",
      $this->get_project_manifest_value( $this->get_block_editor_style_key() ),
      [ "{$this->get_project_name()}-block-style" ],
      $this->get_project_version(),
      false
    );
    \wp_enqueue_style( "{$this->get_project_name()}-block-editor-style" );
  }

  /**
   * Enqueue blocks style for editor and frontend.
   *
   * @since 1.0.0
   *
   * @return void
   */
  public function enqueue_block_style() {
    \wp_register_style(
      "{$this->get_project_name()}-block-style",
      $this->get_project_manifest_value( $this->get_block_style_key() ),
      [],
      $this->get_project_version(),
      false
    );
    \wp_enqueue_style( "{$this->get_project_name()}-block-style" );
  }

  /**
   * Enqueue blocks script for frontend only.
   *
   * @since 1.0.0
   *
   * @return void
   */
  public function enqueue_block_script() {
    \wp_register_script(
      "{$this->get_project_name()}-block-scripts",
      $this->get_project_manifest_value( $this->get_block_script_key() ),
      [],
      $this->get_project_version(),
      true
    );
    \wp_enqueue_script( "{$this->get_project_name()}-block-scripts" );
  }

  /**
   * Get block editor only script key from project manifest.json
   *
   * @return string
   *
   * @since 1.0.0
   */
  public function get_block_editor_script_key() : string {
    return 'applicationBlocksEditor.js';
  }

  /**
   * Get block editor only style key from project manifest.json
   *
   * @return string
   *
   * @since 1.0.0
   */
  public function get_block_editor_style_key() : string {
    return 'applicationBlocksEditor.css';
  }

  /**
   * Get block editor and frontend style key from project manifest.json
   *
   * @return string
   *
   * @since 1.0.0
   */
  public function get_block_style_key() : string {
    return 'applicationBlocks.css';
  }

  /**
   * Get block frontend only script key from project manifest.json
   *
   * @return string
   *
   * @since 1.0.0
   */
  public function get_block_script_key() : string {
    return 'applicationBlocks.js';
  }

  /**
   * Get project name used in enqueue methods for scripts and styles.
   *
   * @return string
   *
   * @since 1.0.0
   */
  protected function get_project_name() : string {
    return $this->project_name;
  }

  /**
   * Get project version used in enqueue methods for scripts and styles.
   *
   * @return string
   *
   * @since 1.0.0
   */
  protected function get_project_version() : string {
    return $this->project_version;
  }

  /**
   * Get project manifest value by key.
   * Manifest is used to provide key->value map of all assets and scripts.
   * It is build with webpack manifest plugin.
   *
   * @throws Exception\Missing_Assets_Manifest Throws error if assets manifest is missing.
   * @throws Exception\Missing_Assets_Manifest_Key Throws error if assets manifest key is missing.
   *
   * @param string $key Manifest key to search by.
   * @return string
   *
   * @since 1.0.0
   */
  protected function get_project_manifest_value( $key ) : string {
    $manifest = $this->get_project_manifest();

    if ( ! $manifest ) {
      throw Missing_Assets_Manifest::manifest_exception();
    }

    if ( ! isset( $manifest[ $key ] ) ) {
      throw Missing_Assets_Manifest_Key::manifest_item_exception( $key );
    }

    return $manifest[ $key ];
  }
}
