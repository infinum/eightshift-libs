<?php
/**
 * File containing an abstract class for holding Assets Manifest functionality.
 *
 * It is used to provide manifest.json file location used with Webpack to fetch correct file locations.
 *
 * @package Eightshift_Libs\Manifest
 */

declare( strict_types=1 );

namespace Eightshift_Libs\Manifest;

use Eightshift_Libs\Core\Service;
use Eightshift_Libs\Exception\Invalid_Manifest;
use Eightshift_Libs\Manifest\Manifest_Data;
use Eightshift_Libs\Core\Config_Data;

/**
 * Abstract class Manifest class.
 *
 * @since 2.0.0 Removing global variable setup.
 * @since 0.9.0 Adding Manifest Item filter.
 * @since 0.7.0 Added Manifest_Data Interface.
 * @since 0.1.0 Init.
 */
class Manifest implements Service, Manifest_Data {

  /**
   * Manifest item filter name constant.
   *
   * @var string
   *
   * @since 0.9.0 Init.
   */
  const MANIFEST_ITEM_FILTER_NAME = 'manifest-item';

  /**
   * Instance variable of project config data.
   *
   * @var object
   *
   * @since 2.0.0
   */
  protected $config;

  /**
   * Full data of manifest items.
   *
   * @var array
   *
   * @since 2.0.0
   */
  protected $manifest = [];

  /**
   * Create a new instance that injects config data to get project specific details.
   *
   * @param Config_Data $config Inject config which holds data regarding project details.
   *
   * @since 2.0.0
   */
  public function __construct( Config_Data $config ) {
    $this->config = $config;
  }

  /**
   * Register all hooks.
   *
   * @since 2.0.0 Changed filter name to manifest.
   * @since 0.9.0 Adding manifest item filter.
   * @since 0.8.0 Removing type hinting void for php 7.0.
   * @since 0.6.0 Init.
   *
   * @return void
   */
  public function register() {
    add_action( 'init', [ $this, 'set_assets_manifest_raw' ] );
    add_filter( $this->config->get_config( static::MANIFEST_ITEM_FILTER_NAME ), [ $this, 'get_assets_manifest_item' ] );
  }

  /**
   * Set the manifest data with site url prefix.
   * You should never call this method directly instead you should call $this->manifest.
   *
   * @throws Invalid_Manifest Throws error if manifest.json file is missing.
   *
   * @return void Sets the manifest variable.
   *
   * @since 2.0.0
   */
  public function set_assets_manifest_raw() : void {
    $path = $this->config->get_project_path() . '/public/manifest.json';

    if ( ! file_exists( $path ) ) {
      throw Invalid_Manifest::missing_manifest_exception( $path );
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
   * @throws Invalid_Manifest Throws error if manifest key is missing.
   *
   * @since 2.0.0 Returned data from manifest and not global variable.
   * @since 0.7.0 Changed to non static method and added Exception for missing key.
   * @since 0.6.0 Init
   *
   * @return string Full path to asset.
   */
  public function get_assets_manifest_item( string $key ) : string {
    $manifest = $this->manifest;

    if ( ! isset( $manifest[ $key ] ) ) {
      throw Invalid_Manifest::missing_manifest_item_exception( $key );
    }

    return $manifest[ $key ];
  }

  /**
   * Config getter
   *
   * @since 2.2.0 Added config getter.
   *
   * @return Config_Data|object
   */
  public function get_config() {
    return $this->config;
  }

  /**
   * This method appends full site url to the relative manifest data item.
   *
   * @since 0.6.0
   *
   * @return string
   */
  protected function get_assets_manifest_output_prefix() : string {
    return site_url();
  }
}
