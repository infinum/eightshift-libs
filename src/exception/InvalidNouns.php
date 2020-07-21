<?php
/**
 * File containing invalid nouns exception
 *
 * @package EightshiftLibs\Exception
 */

declare( strict_types=1 );

namespace EightshiftLibs\Exception;

/**
 * Class Invalid_Nouns.
 */
final class InvalidNouns extends \InvalidArgumentException implements GeneralExceptionInterface {

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
