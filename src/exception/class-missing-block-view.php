<?php
/**
 * Class Missing_Block_View provides Exception if block view is not foud.
 *
 * @since   1.0.0
 * @package Eightshift_Blocks\Exception
 */

namespace Eightshift_Blocks\Exception;

/**
 * Class Missing_Block_View.
 *
 * @since 1.0.0
 */
class Missing_Block_View extends \InvalidArgumentException implements General_Exception {

  /**
   * Create a new instance of the exception for an missing block view.
   *
   * @param string $name Block name.
   * @param string $path Path to block on disk.
   *
   * @return static
   *
   * @since 1.0.0
   */
  public static function view_exception( string $name, string $path ) {
    $message = sprintf(
      esc_html__( 'Missing view template for block called: %1$s. Unable to find template in path: %2$s', 'eightshift-blocks' ),
      $name,
      $path
    );

    return new static( $message );
  }
}
