<?php
/**
 * File that holds the Securable Route interface.
 *
 * @package EightshiftLibs\Rest
 */

declare( strict_types=1 );

namespace EightshiftLibs\Rest;

/**
 * Interface Securable.
 *
 * An object that can be registered.
 *
 * @since   2.0.0
 */
interface RouteSecurityInterface {

  /**
   * Register the rest route.
   *
   * A register method holds authentification_check funtion to for route.
   *
   * @return void
   *
   * @since 2.0.0
   */
  public function authentification_check();
}
