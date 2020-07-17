<?php
/**
 * Assets manifest data interface.
 *
 * Used to define the way manifest item is retrieved from the manifest file.
 *
 * @package Eightshiftlibs\Manifest
 */

declare( strict_types=1 );

namespace Eightshiftlibs\Manifest;

use Eightshiftlibs\Core\ConfigDataInterface;

/**
 * Interface ManifestDataInterface
 *
 * @since 0.7.0
 */
interface ManifestDataInterface {

  /**
   * Config getter
   *
   * @since 2.2.0 Added config getter.
   *
   * @return ConfigDataInterface
   */
  public function get_config();

  /**
   * Return full path for specific asset from manifest.json
   * This is used for cache busting assets.
   *
   * @param string $key File name key you want to get from manifest.
   * @return string Full path to asset.
   *
   * @since 0.7.0 Init
   */
  public function get_assets_manifest_item( string $key ) : string;
}
