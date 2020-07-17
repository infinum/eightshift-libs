<?php
/**
 * File containing the invalid callback exception class
 *
 * @package Eightshiftlibs\Exception
 */

declare( strict_types=1 );

namespace Eightshiftlibs\Exception;

/**
 * Class Invalid_Callback.
 *
 * @since 0.1.0
 */
final class FinalInvalidCallback extends \InvalidArgumentException implements GeneralExceptionInterface {

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
