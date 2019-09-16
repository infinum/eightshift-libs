<?php
/**
 * Enqueue class used to define all script and style enqueues for Gutenberg blocks.
 *
 * @since   1.0.0
 * @package Eightshift_Boilerplate\Blocks
 */

namespace Eightshift_Boilerplate\Blocks;

use Eightshift_Blocks\Enqueue as Lib_Enqueue;
use Eightshift_Libs\Manifest\Manifest_Data;

/**
 * Enqueue class.
 *
 * @since 1.0.0
 */
class Enqueue extends Lib_Enqueue {

  /**
   * Instance variable of manifest data.
   *
   * @var object
   *
   * @since 1.0.0 Init.
   */
  protected $manifest;

  /**
   * Create a new admin instance that injects manifest data for use in assets registration.
   *
   * @param Manifest_Data $manifest Inject manifest which holds data about assets from manifest.json.
   *
   * @since 1.0.0 Init.
   */
  public function __construct( Manifest_Data $manifest ) {
    $this->manifest = $manifest;
  }

  /**
   * Method to provide projects manifest array.
   * Using this manifest you are able to provide project specific implementation of assets locations.
   *
   * @return array
   *
   * @since 1.0.0
   */
  public function get_project_manifest() : array {
    return $this->manifest->get_decoded_manifest_data();
  }

  /**
   * Get project name used in enqueue methods for scripts and styles.
   *
   * @return string
   *
   * @since 1.0.0
   */
  protected function get_project_name() : string {
    return ES_THEME_NAME;
  }

  /**
   * Get project version used in enqueue methods for scripts and styles.
   *
   * @return string
   *
   * @since 1.0.0
   */
  protected function get_project_version() : string {
    return ES_THEME_VERSION;
  }
}
