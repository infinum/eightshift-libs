<?php
/**
 * Class Missing_Assets_Manifest provides Exception if assets manifest.json is not foud.
 *
 * @since   1.0.0
 * @package Eightshift_Blocks\Exception
 */

namespace Eightshift_Blocks\Exception;

/**
 * Class Missing_Assets_Manifest.
 *
 * @since 1.0.0
 */
class Missing_Assets_Manifest extends \InvalidArgumentException implements General_Exception {

  /**
   * Create a new instance of the exception for an missing assets manifest.
   *
   * @return static
   *
   * @since 1.0.0
   */
  public static function manifest_exception() {
    $message = esc_html__( 'Missing assets manifest.json. Check if your webpack has build the correct manifest.', 'eightshift-blocks' );

    return new static( $message );
  }
}
