<?php
/**
 * Class Missing_Block_Namespace provides Exception if block namespace is not foud.
 *
 * @package Eightshift_Libs\Exception
 */

namespace Eightshift_Libs\Exception;

/**
 * Class Missing_Block_Namespace.
 *
 * @since 1.0.0
 */
class Missing_Block_Namespace extends \InvalidArgumentException implements General_Exception {

  /**
   * Create a new instance of the exception for an missing block namespace.
   *
   * @return static
   *
   * @since 1.0.0
   */
  public static function namespace_exception() {
    $message = esc_html__( 'Missing block namespace. Please check global blocks settings manifest.', 'eightshift-blocks' );

    return new static( $message );
  }
}
