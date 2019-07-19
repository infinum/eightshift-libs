<?php
/**
 * File containing an abstract class for holding Assets Manifest functionality.
 *
 * It is used to provide manifest.json file location used with Webpack to fetch correct file locations.
 *
 * @since   0.6.0 Added multiple methods for easier extending.
 * @since   0.1.0
 * @package Eightshift_Libs\Assets
 */

namespace Eightshift_Libs\Assets;

use Eightshift_Libs\Core\Service;
use Eightshift_Libs\Exception\Missing_Manifest;

/**
 * Abstract class Manifest class.
 *
 * @since 0.7.0 Added Manifest_Data Interface.
 * @since 0.1.0 Init.
 */
abstract class Manifest implements Service, Manifest_Data {

  /**
   * Global variable name constant.
   *
   * @var string
   *
   * @since 0.6.0
   */
  const GLOBAL_VARIABLE_NAME = 'ES_ASSETS_MANIFEST';

  /**
   * Register all hooks.
   *
   * @return void
   *
   * @since 0.8.0 Removing type hinting void for php 7.0.
   * @since 0.6.0 Init.
   */
  public function register() {
    add_action( 'init', [ $this, 'register_global_variable' ] );
  }

  /**
   * Define global variable for assets manifest.
   * Used to cache data inside a global variable so you don't have to fetch manifest.json file on every call.
   *
   * @return void
   *
   * @since 0.6.0 Init.
   */
  public function register_global_variable() {
    define( $this->get_global_variable_name(), $this->get_raw_data() );
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
   * @since 0.7.0 Changed to non static method and added Exception for missing key.
   * @since 0.6.0 Init
   */
  public function get_assets_manifest_item( string $key ) : string {
    if ( ! $key ) {
      return '';
    }

    $data = $this->get_decoded_manifest_data();

    if ( ! isset( $data[ $key ] ) ) {
      $error_message = sprintf(
        esc_html__( '%s is missing in manifest.json. Please check if provided key is correct.', 'eightshift-libs' ),
        $key
      );
      throw Missing_Manifest::message( $error_message );
    }

    return $this->get_assets_manifest_output_prefix() . $data[ $key ];
  }

  /**
   * Return json_decoded manifest data, now you can call items by object key to get the value.
   *
   * @return array Manifest Array Data.
   *
   * @since 0.8.0 Changed to public method.
   * @since 0.7.0 Changed to non static method.
   * @since 0.6.0 Init
   */
  public function get_decoded_manifest_data() : array {
    $data = json_decode( constant( $this->get_global_variable_name() ), true );

    if ( ! $data ) {
      return [];
    }

    return $data;
  }

  /**
   * Get global variable name to store the cached data into.
   *
   * @return string
   *
   * @since 0.7.0 Fetching variable name as static.
   * @since 0.6.0 Init.
   */
  protected function get_global_variable_name() : string {
    return static::GLOBAL_VARIABLE_NAME;
  }

  /**
   * Fetches manifest.json data from get_manifest_url() location, parses and returns as a sanitized array.
   * Generally, you would assign this data to a global variable or some helper that is going to be used in the application to fetch assets data.
   *
   * @throws Exception\Missing_Manifest Throws error if manifest is missing.
   *
   * @return string
   *
   * @since 0.7.0 Fixed Exception msg.
   * @since 0.1.0
   */
  protected function get_raw_data() : string {

    $manifest = $this->get_manifest_url();

    if ( ! file_exists( $manifest ) ) {
      $error_message = esc_html__( 'manifest.json is missing. Bundle the theme before using it.', 'eightshift-libs' );
      throw Missing_Manifest::message( $error_message );
    }

    return implode( ' ', file( $manifest ) );
  }

  /**
   * Get manifest.json url location.
   * If you are using a plugin or a different manifest location provide location with this method.
   *
   * @return string
   *
   * @since 0.6.0 Changed from abstract method to prefilled.
   * @since 0.1.0
   */
  protected function get_manifest_url() : string {
    return get_template_directory() . '/skin/public/manifest.json';
  }

  /**
   * Retrun string as an assets output prefix.
   * Override this if you are using lib for a plugin.
   *
   * @return string
   *
   * @since 0.6.0
   */
  protected function get_assets_manifest_output_prefix() : string {
    return home_url( '/' );
  }
}
