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
   * @throws Component_Exception When $variable is not a string or array.
   */
  public static function ensure_string( $variable ) : string {
    $output = '';

    if ( is_array( $variable ) ) {
      $output = implode( '', $variable );
    } elseif ( is_string( $variable ) ) {
      $output = $variable;
    } else {
      Component_Exception::throw_not_string_or_variable( $variable );
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
   * Note about "parentClass" attribute: If provided, the component will be wrapped with a
   * parent BEM selector. For example, if $attributes['parentClass'] === 'header' and $component === 'logo'
   * are set, the component will be wrapped with a <div class="header__logo"></div>.
   *
   * @param  string $component  Component's name or full path (ending with .php).
   * @param  array  $attributes Array of attributes that's implicitly passed to component.
   * @param  string $parent_path If parent path is provides it will be appended to the file location, if not get_template_directory_uri() will be used as a default parent path.
   * @return string
   *
   * @throws \Exception When we're unable to find the component by $component.
   */
  public static function render( string $component, array $attributes = [], string $parent_path = '' ) {

    if ( empty( $parent_path ) ) {
      $parent_path = \get_template_directory();
    }

    // Detect if user passed component name or path.
    if ( strpos( $component, '.php' ) !== false ) {
      $component_path = "{$parent_path}/$component";
    } else {
      $component_path = "{$parent_path}/src/blocks/components/{$component}/{$component}.php";
    }

    if ( ! file_exists( $component_path ) ) {
      Component_Exception::throw_unable_to_locate_component( $component_path );
    }

    ob_start();

    // Wrap component with parent BEM selector if parent's class is provided. Used
    // for setting specific styles for components rendered inside other components.
    if ( isset( $attributes['parentClass'] ) ) {
      echo \wp_kses_post( "<div class=\"{$attributes['parentClass']}__{$component}\">" );
    }

    require $component_path;

    if ( isset( $attributes['parentClass'] ) ) {
      echo '</div>';
    }

    return (string) ob_get_clean();
  }

  /**
   * Create responsive selectors used for responsive attributes.
   *
   * @param array   $items        Array of breakpoints.
   * @param string  $selector     Selector for this breakpoint.
   * @param string  $parent       Parent block selector.
   * @param boolean $use_modifier If false you can use this selector for visibility.
   * @return string
   *
   * Example:
   * Components::responsive_selectors($attributes['width'], 'width', $block_class);
   *
   * Output:
   * block-column__width-large--4
   */
  public static function responsive_selectors( array $items, string $selector, string $parent, $use_modifier = true ) {
    $output = [];

    foreach ( $items as $item_key => $item_value ) {
      if ( empty( $item_value ) && $item_value !== 0 ) {
        continue;
      }

      if ( $use_modifier ) {
        $output[] = "{$parent}__{$selector}-{$item_key}--{$item_value}";
      } else {
        $output[] = "{$parent}__{$selector}-{$item_key}";
      }
    }

    return static::classnames( $output );
  }
}
