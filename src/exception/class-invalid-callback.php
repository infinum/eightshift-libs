<?php
/**
 * File containing the invalid callback exception class
 *
 * @since   0.1.0
 * @package Eightshift_Libs\Exception
 */

namespace Eightshift_Libs\Exception;

/**
 * Class Invalid_Callback.
 */
class Invalid_Callback extends \InvalidArgumentException implements General_Exception {

  /**
   * Create a new instance of the exception for a callback class name that is
   * not recognized.
   *
   * @param string $callback Class name of the callback that was not recognized.
   *
   * @return static
   *
   * @since 0.1.0
   */
  public static function from_callback( $callback ) {
    $message = sprintf(
      esc_html__( 'The callback %s is not recognized and cannot be registered.', 'eightshift-libs' ),
      is_object( $callback )
        ? get_class( $callback )
        : (string) $callback
    );

    return new static( $message );
  }
}
