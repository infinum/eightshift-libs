<?php
/**
 * The Shortcode specific functionality.
 *
 * @package EightshiftLibs\Helpers
 */

declare( strict_types=1 );

namespace EightshiftLibs\Helpers;

/**
 * Class Shortcode
 */
class Shortcode {

  /**
   * Call a shortcode function by tag name.
   *
   * @author J.D. Grimes
   * @link https://codesymphony.co/dont-do_shortcode/
   *
   * @param string $tag     The shortcode whose function to call.
   * @param array  $atts    The attributes to pass to the shortcode function. Optional.
   * @param array  $content The shortcode's content. Default is null (none).
   *
   * @return string|bool False on failure, the result of the shortcode on success.
   */
  public function get_shortcode( string $tag, array $atts = [], $content = null ) {

    global $shortcode_tags;

    if ( ! isset( $shortcode_tags[ $tag ] ) ) {
      return false;
    }

    return \call_user_func( $shortcode_tags[ $tag ], $atts, $content, $tag );
  }
}
