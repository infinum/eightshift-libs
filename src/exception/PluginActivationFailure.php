<?php
/**
 * File containing the plugin activation failure exception class
 *
 * @package EightshiftLibs\Exception
 */

declare( strict_types=1 );

namespace EightshiftLibs\Exception;

/**
 * Class Plugin_Activation_Failure.
 */
final class PluginActivationFailure extends \RuntimeException implements GeneralExceptionInterface {

  /**
   * Create a new instance of the exception in case plugin cannot be activated.
   *
   * @param string $message Error message to show on plugin activation failure.
   *
   * @return static
   */
  public static function activation_message( $message ) {
    return new static( $message );
  }
}
