<?php
/**
 * File that holds the registrable Field interface.
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
interface Registrable_Field {

  /**
   * Register the rest field.
   *
   * A register method holds register_rest_field funtion to register api field.
   *
   * @return void
   *
   * @since 0.8.0 Removing type hinting void for php 7.0.
   * @since 0.2.0
   */
  public function register_field();
}
