<?php
/**
 * File containing the plugin activation failure exception class
 *
 * @since 2.0.5
 * @package Eightshift_Libs\Exception
 */

declare( strict_types=1 );

namespace Eightshift_Libs\Exception;

/**
 * Class Plugin_Activation_Failure.
 */
final class Plugin_Activation_Failure extends \RuntimeException implements General_Exception {

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
