<?php

/**
 * File that holds Has_Activation interface
 *
 * @package EightshiftLibs\Core
 */

declare(strict_types=1);

namespace EightshiftLibs\Plugin;

/**
 * Interface Has_Activation.
 *
 * An object that can be activated.
 */
interface HasActivationInterface
{
	/**
	 * Activate the service.
	 *
	 * Used when adding certain capabilities of a service.
	 *
	 * Example: add_role, add_cap, etc.
	 *
	 * @return void
	 */
	public function activate(): void;
}
