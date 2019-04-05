<?php
/**
 * File containing the failure exception class when assets aren't bundled
 *
 * @since 1.0.0
 * @package Eightshift_Libs\Exception
 */

namespace Eightshift_Libs\Exception;

/**
 * Class Plugin_Activation_Failure.
 */
class Missing_Manifest extends \InvalidArgumentException implements General_Exception {

  /**
   * Create a new instance of the exception in case
   * a manifest file is missing.
   *
   * @param string $message Error message to show on
   * thrown exception.
   *
   * @return static
   */
  public static function message( $message ) {
    return new static( $message );
  }
}
