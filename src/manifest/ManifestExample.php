<?php
/**
 * File containing an abstract class for holding Assets Manifest functionality.
 *
 * It is used to provide manifest.json file location used with Webpack to fetch correct file locations.
 *
 * @package EightshiftLibs\Manifest
 */

declare( strict_types=1 );

namespace EightshiftLibs\Manifest;

use EightshiftLibs\Config\Config;
use EightshiftLibs\Manifest\AbstractManifest;

/**
 * Class ManifestExample
 */
class ManifestExample extends AbstractManifest {

  /**
   * Register all hooks. Changed filter name to manifest.
   *
   * @return void
   */
  public function register() {
    \add_action( 'init', [ $this, 'set_assets_manifest_raw' ] );
    \add_filter( Config::get_config( static::MANIFEST_ITEM_FILTER_NAME ), [ $this, 'get_assets_manifest_item' ] );
  }

  /**
   * Manifest file path getter.
   *
   * @return string
   */
  public function get_manifest_file_path() : string {
    return Config::get_project_path() . '/public/manifest.json';
  }
}
