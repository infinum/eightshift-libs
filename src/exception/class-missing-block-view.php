<?php
/**
 * File containing invalid Block view exception
 *
 * @since 1.0.0
 * @package Eightshift_Libs\Exception
 */

namespace Eightshift_Libs\Exception;

/**
 * Class Missing_Block_View.
 */
class Missing_Block_View extends \InvalidArgumentException implements General_Exception {

  /**
   * Create a new instance of the exception for an missing block view.
   *
   * @param string $key Asset handle that is not valid.
   *
   * @return static
   *
   * @since 1.0.0
   */
  public static function message( string $name, string $path ) {
    $message = sprintf(
      esc_html__( 'Missing view template for block called: %1$s | Expecting a template in path: %2$s', 'eightshift-libs' ),
      $name,
      $path
    );

    return new static( $message );
  }
}
