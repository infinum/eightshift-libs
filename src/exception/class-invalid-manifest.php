<?php
/**
 * File containing the failure exception class when assets aren't bundled or missing.
 *
 * @package Eightshift_Libs\Exception
 */

declare( strict_types=1 );

namespace Eightshift_Libs\Exception;

/**
 * Class Invalid_Manifest.
 *
 * @since 0.1.0
 */
class Invalid_Manifest extends \InvalidArgumentException implements General_Exception {

  /**
   * Throws error if manifest key is missing.
   *
   * @param string $key Missing manifest key.
   *
   * @return static
   *
   * @since 2.0.0
   */
  public static function missing_manifest_item_exception( string $key ) {
    return new static(
      sprintf(
        esc_html__( '%s key does not exist in manifest.json. Please check if provided key is correct.', 'eightshift-libs' ),
        $key
      )
    );
  }

  /**
   * Throws error if manifest.json file is missing.
   *
   * @param string $path Missing manifest.json path.
   *
   * @return static
   *
   * @since 2.0.0
   */
  public static function missing_manifest_exception( string $path ) {
    return new static(
      sprintf(
        esc_html__( 'manifest.json is missing at this path: %s. Bundle the theme before using it. Or your bundling process is returning and error.', 'eightshift-libs' ),
        $path
      )
    );
  }
}
