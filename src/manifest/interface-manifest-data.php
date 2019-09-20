<?php
/**
 * Assets manifest data interface.
 *
 * Used to define the way manifest item is retrieved from the manifest file.
 *
 * @package Eightshift_Libs\Manifest
 */

namespace Eightshift_Libs\Manifest;

/**
 * Interface Manifest_Data
 *
 * @since 0.7.0
 */
interface Manifest_Data {

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
