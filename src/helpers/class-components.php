<?php
/**
 * Helpers for components
 *
 * @package Eightshift_Libs\Helpers
 */

declare( strict_types=1 );

namespace Eightshift_Libs\Helpers;

use Eightshift_Libs\Exception\Component_Exception;

/**
 * Helpers for components
 */
class Components {

  /**
   * Makes sure the output is string. Useful for converting an array of components into a string.
   *
   * @param  array|string $variable Variable we need to convert into a string.
   * @return string
   *
   * @throws Component_Exception When $variable is not a string or array
   */
  public static function ensure_string( $variable ) : string {
    if ( is_array( $variable ) ) {
      $output = implode( '', $variable );
    } elseif ( is_string( $variable ) ) {
      $output = $variable;
    } else {
      Component_Exception::not_string_or_variable( $variable );
    }

    return $output;
  }

  /**
   * Converts an array of classes into a string which can be echoed.
   *
   * @param  array $classes Array of classes.
   * @return string
   */
  public static function classnames( array $classes ) : string {
    return trim( implode( ' ', $classes ) );
  }

  /**
   * Renders a components and (optionally) passes some attributes to it.
   *
   * @param  string $component  Component's name or full path (ending with .php).
   * @param  array  $attributes Array of attributes that's implicitly passed to component.
   * @return string
   *
   * @throws \Exception When we're unable to find the component by $component.
   */
  public static function render( string $component, array $attributes = [] ) {

    // Detect if user passed component name or path.
    if ( strpos( $component, '.php' ) !== false ) {
      $component_path = $component;
    } else {
      $component_path = "src/blocks/components/$component/$component.php";
    }

    $template = \locate_template( $component_path );

    if ( empty( $template ) ) {
      Component_Exception::unable_to_locate_component( $component_path );
    }
    ob_start();
    require $template;
    return ob_get_clean();
  }
}
