<?php
/**
 * File containing the main intro class
 *
 * @since   1.0.0
 * @package Eightshift_Libs\Assets
 */

namespace Eightshift_Libs\Assets;

/**
 * Manifest class
 */
final class Manifest {

  /**
   * Provide menifest json url location.
   *
   * @return string
   *
   * @since 1.0.0
   */
  protected function get_manifest_url() : string {
    return get_template_directory() . '/skin/public/manifest.json';
  }

  /**
   * Register bundled asset manifest
   *
   * @throws Exception\Missing_Manifest Throws error if manifest is missing.
   *
   * @return mixed
   *
   * @since 1.0.0
   */
  public function register_assets_manifest_data() : mixed {

    $manifest = $this->get_manifest_url();
    if ( ! file_exists( $manifest ) ) {
      $error_message = esc_html__( 'manifest.json is missing. Bundle the theme before using it.', 'developer-portal' );
      throw Exception\Missing_Manifest::message( $error_message );
    }

    return implode( ' ', file( $manifest ) );
  }
}
