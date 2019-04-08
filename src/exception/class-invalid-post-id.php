<?php
/**
 * File containing the invalid post id exception class
 *
 * @since 1.0.0
 * @package Eightshift_Libs\Exception
 */

namespace Eightshift_Libs\Exception;

/**
 * Class Invalid_Post_ID.
 */
class Invalid_Post_ID extends \InvalidArgumentException implements General_Exception {

  /**
   * Create a new instance of the exception for a post ID that is not valid.
   *
   * @param int $id Post ID that is not valid.
   *
   * @return static
   */
  public static function from_id( $id ) {
    $message = sprintf(
      esc_html__( 'The post ID "%d" is not valid.', 'eightshift-libs' ),
      $id
    );

    return new static( $message );
  }
}
