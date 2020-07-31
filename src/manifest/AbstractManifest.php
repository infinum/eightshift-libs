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

use EightshiftLibs\Exception\InvalidManifest;
use EightshiftLibs\Manifest\ManifestInterface;
use EightshiftLibs\Services\ServiceInterface;

/**
 * Abstract class Manifest class.
 */
abstract class AbstractManifest implements ServiceInterface, ManifestInterface {

  /**
   * Full data of manifest items.
   *
   * @var array
   */
  protected $manifest = [];

  /**
   * Set the manifest data with site url prefix.
   * You should never call this method directly instead you should call $this->manifest.
   *
   * @throws InvalidManifest Throws error if manifest.json file is missing.
   *
   * @return void Sets the manifest variable.
   */
  public function set_assets_manifest_raw() : void {
    $path = $this->get_manifest_file_path();

    if ( ! file_exists( $path ) ) {
      throw InvalidManifest::missing_manifest_exception( $path );
    }

    $data = json_decode( implode( ' ', (array) file( $path ) ), true );

    if ( empty( $data ) ) {
      return;
    }

    $this->manifest = array_map(
      function( $manifest_item ) {
        return "{$this->get_assets_manifest_output_prefix()}{$manifest_item}";
      },
      $data
    );
  }

  /**
   * Return full path for specific asset from manifest.json.
   *
   * @param string $key File name key you want to get from manifest.
   *
   * @throws InvalidManifest Throws error if manifest key is missing. Returned data from manifest and not global variable.
   *
   * @return string Full path to asset.
   */
  public function get_assets_manifest_item( string $key ) : string {
    $manifest = $this->manifest;

    if ( ! isset( $manifest[ $key ] ) ) {
      throw InvalidManifest::missing_manifest_item_exception( $key );
    }

    return $manifest[ $key ];
  }

  /**
   * Manifest file path getter.
   *
   * @return string
   */
  abstract protected function get_manifest_file_path() : string;

  /**
   * This method appends full site url to the relative manifest data item.
   *
   * @return string
   */
  protected function get_assets_manifest_output_prefix() : string {
    return \site_url();
  }
}
