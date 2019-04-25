<?php
/**
 * File containing the invalid service exception class
 *
 * @since   0.1.0
 * @package Eightshift_Libs\Exception
 */

namespace Eightshift_Libs\Exception;

/**
 * Class Invalid_Service.
 */
class Invalid_Service extends \InvalidArgumentException implements General_Exception {

  /**
   * Create a new instance of the exception for a service class name that is
   * not recognized.
   *
   * @param string $service Class name of the service that was not recognized.
   *
   * @return static
   *
   * @since 0.1.0
   */
  public static function from_service( $service ) {
    $message = sprintf(
      esc_html__( 'The service %s is not recognized and cannot be registered.', 'eightshift-libs' ),
      is_object( $service )
        ? get_class( $service )
        : (string) $service
    );

    return new static( $message );
  }
}
