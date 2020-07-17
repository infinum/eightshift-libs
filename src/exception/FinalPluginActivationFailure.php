<?php
/**
 * File containing the plugin activation failure exception class
 *
 * @since 2.0.5
 * @package Eightshiftlibs\Exception
 */

declare( strict_types=1 );

namespace Eightshiftlibs\Exception;

/**
 * Class Plugin_Activation_Failure.
 */
final class FinalPluginActivationFailure extends \RuntimeException implements GeneralExceptionInterface {

  /**
   * Create a new instance of the exception in case plugin cannot be activated.
   *
   * @param string $message Error message to show on plugin activation failure.
   *
   * @return static
   *
   * @since 2.0.5
   */
  public static function activation_message( $message ) {
    return new static( $message );
  }
}
