<?php
/**
 * File that holds Has_Activation interface
 *
 * @since 1.0.0
 * @package Eightshiftlibs\Core
 */

declare( strict_types=1 );

namespace Eightshiftlibs\Core;

/**
 * Interface Has_Activation.
 *
 * An object that can be activated.
 *
 * @since 1.0.0
 */
interface HasActivationInterface {
  /**
   * Activate the service.
   *
   * Used when adding certain capabilities of a service.
   *
   * Example: add_role, add_cap, etc.
   *
   * @return void
   */
  public function activate() : void;
}
