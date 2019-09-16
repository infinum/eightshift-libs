<?php
/**
 * Class Missing_Block_Name provides Exception if block name is not foud.
 *
 * TODO: Provide better name_exception msg with block path.
 *
 * @since   1.0.0
 * @package Eightshift_Blocks\Exception
 */

namespace Eightshift_Blocks\Exception;

/**
 * Class Missing_Block_Name.
 *
 * @since 1.0.0
 */
class Missing_Block_Name extends \InvalidArgumentException implements General_Exception {

  /**
   * Create a new instance of the exception for an missing block name.
   *
   * @return static
   *
   * @since 1.0.0
   */
  public static function name_exception() {
    $message = esc_html__( 'Missing block name in block manifest.', 'eightshift-blocks' );

    return new static( $message );
  }
}
