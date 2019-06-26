<?php
/**
 * File that holds the registrable Route interface.
 *
 * @since   0.2.0
 * @package Eightshift_Libs\Routes
 */

namespace Eightshift_Libs\Routes;

/**
 * Interface Registrable.
 *
 * An object that can be registered.
 */
interface Registrable_Route {

  /**
   * Register the rest route.
   *
   * A register method holds register_rest_route funtion to register api route.
   *
   * @return void
   *
   * @since 0.8.0 Removing type hinting void for php 7.0.
   * @since 0.2.0
   */
  public function register_route();
}
