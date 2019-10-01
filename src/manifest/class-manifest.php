<?php
/**
 * File containing an abstract class for holding Assets Manifest functionality.
 *
 * It is used to provide manifest.json file location used with Webpack to fetch correct file locations.
 *
 * @package Eightshift_Libs\Manifest
 */

namespace Eightshift_Libs\Manifest;

use Eightshift_Libs\Core\Service;
use Eightshift_Libs\Exception\Missing_Manifest;
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
   * Instance variable of project config data.
   *
   * @var object
   *
   * @since 2.0.0
   */
  protected $config;

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
   * Manifest item filter name constant.
   *
   * @var string
   *
   * @since 2.0.0 Added Project Prefix.
   * @since 0.9.0 Init.
   */
  const MANIFEST_ITEM_FILTER_NAME = 'manifest-item';

  /**
   * Register all hooks.
   *
   * @return void
   *
   * @since 2.0.0 Changed filter name to manifest.
   * @since 0.9.0 Adding manifest item filter.
   * @since 0.8.0 Removing type hinting void for php 7.0.
   * @since 0.6.0 Init.
   */
  public function register() {
    add_filter( $this->config->get_config( static::MANIFEST_ITEM_FILTER_NAME ), [ $this, 'get_assets_manifest_item' ] );
  }

  /**
   * Return full path for specific asset from manifest.json.
   * This is used for cache busting assets.
   *
   * @param string $key File name key you want to get from manifest.
   *
   * @throws Exception\Missing_Manifest Throws error if manifest key is missing.
   *
   * @return string Full path to asset.
   *
   * @since 2.0.0 Returned data from manifest and not global variable.
   * @since 0.7.0 Changed to non static method and added Exception for missing key.
   * @since 0.6.0 Init
   */
  public function get_assets_manifest_item( string $key ) : string {
    $path = $this->config->get_project_path() . '/public/manifest.json';

    if ( ! file_exists( $path ) ) {
      throw Missing_Manifest::message( esc_html__( 'manifest.json is missing. Bundle the theme before using it. Or your bundling process is returning and error.', 'eightshift-libs' ) );
    }

    $data = json_decode( implode( ' ', file( $path ) ), true );

    if ( ! isset( $data[ $key ] ) ) {
      throw Missing_Manifest::message(
        sprintf(
          esc_html__( '%s is missing in manifest.json. Please check if provided key is correct.', 'eightshift-libs' ),
          $key
        )
      );
    }

    return $this->get_assets_manifest_output_prefix() . $data[ $key ];
  }

  /**
   * This method appends full site url to the relative manifest data item.
   *
   * @return string
   *
   * @since 0.6.0
   */
  protected function get_assets_manifest_output_prefix() : string {
    return site_url();
  }
}
