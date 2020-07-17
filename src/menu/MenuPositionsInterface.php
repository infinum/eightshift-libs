<?php
/**
 * Projects MenuPositionsInterface interface.
 *
 * Used to define available menu positions in your project.
 *
 * @package Eightshift_Libs\Menu
 */

declare( strict_types=1 );

namespace Eightshift_Libs\Menu;

/**
 * Interface MenuPositionsInterface
 *
 * @since 2.0.0
 */
interface MenuPositionsInterface {

  /**
   * Return all menu poistions
   *
   * @return array Of menu positions with name and slug.
   *
   * @since 2.0.0
   */
  public function get_menu_positions() : array;
}
