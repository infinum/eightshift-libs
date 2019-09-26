<?php
/**
 * The object helper specific functionality inside classes.
 * Used in admin or theme side but only inside a class.
 *
 * @package Eightshift_Libs\Helpers
 */

declare( strict_types=1 );

namespace Eightshift_Libs\Helpers;

/**
 * Class Object Helper
 *
 * @since 1.0.0
 */
trait Object_Helper {

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
   * @return boolean
   *
   * @since 1.0.0
   */
  public static function is_json( $string ) {
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
  public static function flatten_array( $array = null ) {
    if ( ! $array || ! is_array( $array ) ) {
      return false;
    }

    $return = array();
    array_walk_recursive(
      $array,
      function( $a ) use ( &$return ) {
        if ( ! empty( $a ) ) {
          $return[] = $a;
        }
      }
    );
    return $return;
  }

  /**
   * Sanitise all values in array.
   *
   * @param array $array                 Provided array.
   * @param array $sanitization_function funcition from wp to sanitize.
   * @return array
   *
   * @since 1.0.0
   */
  public static function sanitize_array( $array = null, $sanitization_function = null ) {
    if ( ! $array || ! $sanitization_function ) {
      return false;
    }

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
  public static function sort_array_by_order_key( $items = null ) {
    usort(
      $items,
      function( $item1, $item2 ) {
        return $item1['order'] <=> $item2['order'];
      }
    );

    return $items;
  }
}
