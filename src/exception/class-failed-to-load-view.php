<?php
/**
 * File containing failed to load view class
 *
 * @since   0.1.0
 * @package Eightshift_Libs\Exception
 */

namespace Eightshift_Libs\Exception;

/**
 * Class Failed_To_Load_View.
 */
class Failed_To_Load_View extends \RuntimeException implements General_Exception {

  /**
   * Create a new instance of the exception if the view file itself created
   * an exception.
   *
   * @param string     $uri       URI of the file that is not accessible or
   *                              not readable.
   * @param \Exception $exception Exception that was thrown by the view file.
   *
   * @return static
   *
   * @since 0.1.0
   */
  public static function view_exception( $uri, $exception ) {
    $message = sprintf(
      esc_html__( 'Could not load the View URI: %1$s. Reason: %2$s.', 'eightshift-libs' ),
      $uri,
      $exception->getMessage()
    );

    return new static( $message, $exception->getCode(), $exception );
  }
}
