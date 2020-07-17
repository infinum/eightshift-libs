<?php
/**
 * The object helper specific functionality inside classes.
 * Used in admin or theme side but only inside a class.
 *
 * @package Eightshiftlibs\Helpers
 */

declare( strict_types=1 );

namespace Eightshiftlibs\Helpers;

/**
 * Class Object Helper
 *
 * @since 1.0.0
 */
trait ObjectHelperTrait {

  /**
   * Check if XML is valid file used for svg.
   *
   * @param xml $xml Full xml document.
   * @return boolean
   *
   * @since 1.0.0
   */
  public function is_valid_xml( $xml ) {
    libxml_use_internal_errors( true );
    $doc = new \DOMDocument( '1.0', 'utf-8' );
    $doc->loadXML( $xml );
    $errors = libxml_get_errors();
    return empty( $errors );
  }

  /**
   * Check if json is valid
   *
   * @param string $string String to check.
   *
   * @return bool
   *
   * @since 1.0.0
   */
  public static function is_json( string $string ) : bool {
    json_decode( $string );
    return ( json_last_error() === JSON_ERROR_NONE );
  }

  /**
   * Flatten multidimensional array.
   *
   * @param  array $array Multidimensional array.
   * @return array
   *
   * @since 2.0.0
   */
  public static function flatten_array( array $array ) : array {
    $output = [];

    array_walk_recursive(
      $array,
      function( $a ) use ( &$output ) {
        if ( ! empty( $a ) ) {
          $output[] = $a;
        }
      }
    );

    return $return;
  }

  /**
   * Sanitize all values in an array.
   *
   * @param array  $array                 Provided array.
   * @param string $sanitization_function WordPress function used for sanitization purposes.
   *
   * @link https://developer.wordpress.org/themes/theme-security/data-sanitization-escaping/
   *
   * @return array
   *
   * @since 1.0.0
   */
  public static function sanitize_array( array $array, string $sanitization_function ) : array {
    foreach ( $array as $key => $value ) {
      if ( is_array( $value ) ) {
          $value = sanitize_array( $value );
      }

      $value = $sanitization_function( $value );
    }

    return $array;
  }

  /**
   * Sort array by order key. Used to sort terms.
   *
   * @param array $items Items array to sort. Must have order key.
   * @return array
   *
   * @since 1.0.0
   */
  public static function sort_array_by_order_key( array $items ) : array {
    usort(
      $items,
      function( $item1, $item2 ) {
        return $item1['order'] <=> $item2['order'];
      }
    );

    return $items;
  }
}
