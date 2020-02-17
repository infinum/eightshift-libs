<?php
/**
 * File containing the failure exception class when trying to locate a template that doesn't exist.
 *
 * @package Eightshift_Libs\Exception
 */

declare( strict_types=1 );

namespace Eightshift_Libs\Exception;

/**
 * Class Missing_Template.
 */
class Component_Exception extends \InvalidArgumentException implements General_Exception {

  /**
   * Throws exception if ensure_string argument is invalid.
   *
   * @param string $variable Variable that's of invalid type
   *
   * @return static
   */
  public static function not_string_or_variable( $variable ) {
    return new static(
      sprintf(
        esc_html__( '%s variable is not a string or array but rather %s', 'eightshift-libs' ),
        $variable,
        gettype($variable)
      )
    );
  }

  /**
   * Throws exception if ensure_string argument is invalid.
   *
   * @param string $variable Variable that's of invalid type
   *
   * @return static
   */
  public static function unable_to_locate_component( string $component ) {
    return new static(
      sprintf(
        esc_html__( 'Unable to locate component by path: %s', 'eightshift-libs' ),
        $component
      )
    );
  }
}
