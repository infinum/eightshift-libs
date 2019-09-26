<?php
/**
 * Projects Menu_Positions interface.
 *
 * Used to define available menu positions in your project.
 *
 * @package Eightshift_Libs\Menu
 */

namespace Eightshift_Libs\Menu;

/**
 * Interface Menu_Positions
 *
 * @since 2.0.0
 */
interface Menu_Positions {

  /**
   * Return all menu poistions
   *
   * @return array Of menu positions with name and slug.
   *
   * @since 2.0.0
   */
  public function get_menu_positions() : array;
}
