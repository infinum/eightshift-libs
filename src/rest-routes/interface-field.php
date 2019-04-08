<?php
/**
 * File containing type interface
 *
 * @since 1.0.0
 * @package Eightshift_Libs\Routes
 */

namespace Eightshift_Libs\Routes;

use Eightshift_Libs\Core\Service;

/**
 * Route interface that adds routes
 */
interface Field extends Service {

  /**
   * Method for adding custom routes
   *
   * @return void
   *
   * @since 1.0.0
   */
  public function register_field() : void;
}
