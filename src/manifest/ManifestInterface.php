<?php
/**
 * Assets manifest data interface.
 *
 * Used to define the way manifest item is retrieved from the manifest file.
 *
 * @package EightshiftLibs\Manifest
 */

declare( strict_types=1 );

namespace EightshiftLibs\Manifest;

use EightshiftLibs\Config\ConfigInterface;

/**
 * Interface ManifestInterface
 */
interface ManifestInterface {

  /**
   * Config getter
   *
   * @return ConfigInterface
   */
  public function get_config();

  /**
   * Return full path for specific asset from manifest.json
   * This is used for cache busting assets.
   *
   * @param string $key File name key you want to get from manifest.
   *
   * @return string Full path to asset.
   */
  public function get_assets_manifest_item( string $key ) : string;
}
