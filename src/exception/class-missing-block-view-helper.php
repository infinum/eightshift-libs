<?php
/**
 * Class Missing_Block_View_Helper provides Exception if block view is not foud.
 *
 * @package Eightshift_Libs\Exception
 */

namespace Eightshift_Libs\Exception;

/**
 * Class Missing_Block_View_Helper.
 *
 * @since 1.0.0
 */
class Missing_Block_View_Helper extends \InvalidArgumentException implements General_Exception {

  /**
   * Create a new instance of the exception for an missing block view.
   *
   * @param string $path Path to block on disk.
   *
   * @return static
   *
   * @since 1.0.0
   */
  public static function view_exception( string $path ) {
    $message = sprintf(
      esc_html__( 'Unable to find template in path: %1$s, please check block view.', 'eightshift-blocks' ),
      $path
    );

    return new static( $message );
  }
}
