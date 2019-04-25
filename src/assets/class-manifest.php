<?php
/**
 * File containing an abstract class for holding Assets Manifest functionality.
 *
 * It is used to provide manifest.json file location used with Webpack to fetch correct file locations.
 *
 * @since   0.1.0
 * @package Eightshift_Libs\Assets
 */

namespace Eightshift_Libs\Assets;

/**
 * Abstract class Manifest class.
 */
abstract class Manifest {

  /**
   * Provide manifest.json url location.
   * You project must provide location for the manifest.json for this to work.
   *
   * @return string
   *
   * @since 0.1.0
   */
  abstract protected function get_manifest_url() : string;

  /**
   * Fetches manifest.json data from get_manifest_url() location, parses and returns as a sanitized array.
   * Generally, you would assign this data to a global variable or some helper that is going to be used in the application to fetch assets data.
   *
   * @throws Exception\Missing_Manifest Throws error if manifest is missing.
   *
   * @return string
   *
   * @since 0.1.0
   */
  public function register_assets_manifest_data() : string {

    $manifest = $this->get_manifest_url();
    if ( ! file_exists( $manifest ) ) {
      $error_message = esc_html__( 'manifest.json is missing. Bundle the theme before using it.', 'developer-portal' );
      throw Exception\Missing_Manifest::message( $error_message );
    }

    return implode( ' ', file( $manifest ) );
  }
}
