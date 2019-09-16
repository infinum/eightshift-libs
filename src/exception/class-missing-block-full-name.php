<?php
/**
 * Class Missing_Block_Full_Name provides Exception if block fullname is not foud.
 *
 * TODO: Provide better name_exception msg with block path.
 *
 * @since   1.0.0
 * @package Eightshift_Blocks\Exception
 */

namespace Eightshift_Blocks\Exception;

/**
 * Class Missing_Block_Full_Name.
 *
 * @since 1.0.0
 */
class Missing_Block_Full_Name extends \InvalidArgumentException implements General_Exception {

  /**
   * Create a new instance of the exception for an missing block name.
   *
   * @return static
   *
   * @since 1.0.0
   */
  public static function full_name_exception() {
    $message = esc_html__( 'Missing block fullname in block manifest.', 'eightshift-blocks' );

    return new static( $message );
  }
}
