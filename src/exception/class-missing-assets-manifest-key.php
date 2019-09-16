<?php
/**
 * Class Missing_Assets_Manifest_Key provides Exception if assets manifest.json key is not foud.
 *
 * @since   1.0.0
 * @package Eightshift_Blocks\Exception
 */

namespace Eightshift_Blocks\Exception;

/**
 * Class Missing_Assets_Manifest_Key.
 *
 * @since 1.0.0
 */
class Missing_Assets_Manifest_Key extends \InvalidArgumentException implements General_Exception {

  /**
   * Create a new instance of the exception for an missing assets manifest key.
   *
   * @param string $key Assets Manifest Keys.
   *
   * @return static
   *
   * @since 1.0.0
   */
  public static function manifest_item_exception( string $key ) {
    $message = sprintf(
      esc_html__( 'Missing assets manifest.json key: %1$s. Check if your webpack has build the correct manifest with key: %1$s', 'eightshift-blocks' ),
      $key
    );

    return new static( $message );
  }
}
