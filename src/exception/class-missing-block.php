<?php
/**
 * File containing invalid Gutenberg Block exception
 *
 * @since   0.1.0
 * @package Eightshift_Libs\Exception
 */

namespace Eightshift_Libs\Exception;

/**
 * Class Missing_Block.
 */
class Missing_Block extends \InvalidArgumentException implements General_Exception {

  /**
   * Create a new instance of the exception for an missing block name.
   *
   * @return static
   *
   * @since 0.1.0
   */
  public static function name_exception() {
    $message = esc_html_e( 'Missing Block Name', 'eightshift-libs' );

    return new static( $message );
  }

  /**
   * Create a new instance of the exception for an missing block view.
   *
   * @param string $name Block name.
   * @param string $path Path to block on disk.
   *
   * @return static
   *
   * @since 0.1.0
   */
  public static function view_exception( string $name, string $path ) {
    $message = sprintf(
      esc_html__( 'Missing view template for block called: %1$s | Expecting a template in path: %2$s', 'eightshift-libs' ),
      $name,
      $path
    );

    return new static( $message );
  }
}
