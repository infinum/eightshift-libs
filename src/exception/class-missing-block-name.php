<?php
/**
 * File containing invalid Block name exception
 *
 * @since   1.0.0
 * @package Eightshift_Libs\Exception
 */

namespace Eightshift_Libs\Exception;

/**
 * Class Missing_Block_Name.
 */
class Missing_Block_Name extends \InvalidArgumentException implements General_Exception {

  /**
   * Create a new instance of the exception for an missing block name.
   *
   * @return static
   *
   * @since 1.0.0
   */
  public static function message() {
    $message = esc_html_e( 'Missing Block Name', 'eightshift-libs' );

    return new static( $message );
  }
}
