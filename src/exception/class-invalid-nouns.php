<?php
/**
 * File containing invalid nouns exception
 *
 * @since 1.0.0
 * @package Eightshift_Libs\Exception
 */

declare( strict_types=1 );

namespace Eightshift_Libs\Exception;

/**
 * Class Invalid_Nouns.
 */
class Invalid_Nouns extends \InvalidArgumentException implements General_Exception {

  /**
   * Create a new instance of the exception for an array of nouns that is
   * missing a required key.
   *
   * @param string $key Asset handle that is not valid.
   *
   * @return static
   */
  public static function from_key( string $key ) {
    $message = sprintf(
      esc_html__( 'The array of nouns passed into the Label_Generator is missing the %s noun.', 'eightshift-libs' ),
      $key
    );

    return new static( $message );
  }
}
