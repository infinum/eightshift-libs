<?php
/**
 * The Menu specific functionality.
 *
 * @package Eightshiftlibs\Menu
 */

declare( strict_types=1 );

namespace Eightshiftlibs\Menu;

use Eightshiftlibs\Core\ServiceInterface;
use Eightshiftlibs\Menu\MenuPositionsInterface;

/**
 * Class Menu
 *
 * @since 2.0.0
 */
abstract class AbstractMenu implements ServiceInterface, MenuPositionsInterface {

  /**
   * Register all the hooks
   *
   * @return void
   *
   * @since 2.0.0
   */
  public function register() {
    add_action( 'after_setup_theme', [ $this, 'register_menu_positions' ], 11 );
  }

  /**
   * Register All Menu positions
   *
   * @return void
   *
   * @since 2.0.0
   */
  public function register_menu_positions() {
    \register_nav_menus(
      $this->get_menu_positions()
    );
  }

  /**
   * Return all menu positions
   *
   * @return array Of menu positions with name and slug.
   *
   * @since 2.0.0
   */
  public function get_menu_positions() : array {
    return [];
  }

  /**
   * Bem_menu returns an instance of the Bem_Menu_Walker class with the following arguments
   *
   * @param  string $location            This must be the same as what is set in wp-admin/settings/menus for menu location and registered in register_menu_positions function.
   * @param  string $css_class_prefix    This string will prefix all of the menu's classes, BEM syntax friendly.
   * @param  string $css_class_modifiers Provide either a string or array of values to apply extra classes to the <ul> but not the <li's>.
   * @param  bool   $echo                Echo the menu.
   *
   * @return string|false|void Menu output if $echo is false, false if there are no items or no menu was found.
   *
   * @since 2.0.0
   */
  public static function bem_menu( string $location = 'main_menu', string $css_class_prefix = 'main-menu', $css_class_modifiers = null, bool $echo = true ) {

    // Check to see if any css modifiers were supplied.
    $modifiers = '';
    if ( $css_class_modifiers ) {
      if ( is_array( $css_class_modifiers ) ) {
        $modifiers = implode( ' ', $css_class_modifiers );
      } elseif ( is_string( $css_class_modifiers ) ) {
        $modifiers = $css_class_modifiers;
      }
    }

    $args = [
      'theme_location' => $location,
      'container'      => false,
      'items_wrap'     => '<ul class="' . $css_class_prefix . ' ' . $modifiers . '">%3$s</ul>',
      'echo'           => $echo,
      'walker'         => new BemMenuWalker( $css_class_prefix ),
    ];

    if ( ! \has_nav_menu( $location ) ) {
      return '';
    }

    return \wp_nav_menu( $args );
  }
}
