<?php
/**
 * The Assets abstract class.
 *
 * @package Eightshift_Libs\Enqueue
 */

declare( strict_types=1 );

namespace Eightshift_Libs\Enqueue;

use Eightshift_Libs\Core\Service;

/**
 * Class Assets
 *
 * This abstract class holds helper methods that can be used and overwritten in
 * user defined classes that extend the enqueue classes in their project.
 *
 * @package Eightshift_Libs\Enqueue
 */
abstract class Assets implements Service {

  /**
   * Media style const
   */
  const MEDIA_ALL    = 'all';
  const MEDIA_PRINT  = 'print';
  const MEDIA_SCREEN = 'screen';

  /**
   * Load scripts in footer const
   */
  const IN_FOOTER = true;

  /**
   * Get frontend script dependencies
   *
   * @link https://developer.wordpress.org/reference/functions/wp_enqueue_script/#default-scripts-included-and-registered-by-wordpress
   *
   * @return array List of all the script dependencies.
   *
   * @since 2.0.3
   */
  protected function get_frontend_script_dependencies() : array {
    return [];
  }

  /**
   * Get admin script dependencies
   *
   * @link https://developer.wordpress.org/reference/functions/wp_enqueue_script/#default-scripts-included-and-registered-by-wordpress
   *
   * @return array List of all the script dependencies.
   *
   * @since 2.0.3
   */
  protected function get_admin_script_dependencies() : array {
    return [];
  }

  /**
   * Get script localizations
   *
   * * Example: $localization_array => [
   *  'localizationHandler' => [
   *      'someValue'    => esc_html__( 'Hi there!', 'text-domain' ),
   *      'anotherValue' => $variableValue,
   *  ]
   * ];
   *
   * @link https://developer.wordpress.org/reference/functions/wp_localize_script/
   *
   * @return array Key value pair of different localizations.
   *
   * @since 2.0.3
   */
  protected function get_localizations() : array {
    return [];
  }

  /**
   * Get front end style dependencies
   *
   * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style/
   *
   * @return array List of all the style dependencies.
   *
   * @since 2.0.3
   */
  protected function get_frontend_style_dependencies() : array {
    return [];
  }

  /**
   * Get admin style dependencies
   *
   * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style/
   *
   * @return array List of all the style dependencies.
   *
   * @since 2.0.3
   */
  protected function get_admin_style_dependencies() : array {
    return [];
  }

  /**
   * Get style media definition
   *
   * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style/
   *
   * @return string The media for which this stylesheet has been defined.
   * Accepts media types like 'all', 'print' and 'screen',
   * or media queries like '(orientation: portrait)' and '(max-width: 640px)'.
   * Default value: 'all'
   *
   * @since 2.0.3
   */
  protected function get_media() : string {
    return static::MEDIA_ALL;
  }

  /**
   * Load script in footer
   *
   * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style/
   *
   * @return bool Whether to enqueue the script before </body> instead of in the <head>.
   * Default value: true
   *
   * @since 2.0.3
   */
  protected function script_in_footer() : bool {
    return static::IN_FOOTER;
  }
}
