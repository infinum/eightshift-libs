<?php

/**
 * File that holds the Securable Route interface.
 *
 * @package EightshiftLibs\Rest
 */

declare(strict_types=1);

namespace EightshiftLibs\Rest;

/**
 * Interface Securable.
 *
 * An object that can be registered.
 */
interface RouteSecurityInterface
{

	/**
	 * Register the rest route.
	 *
	 * A register method holds authentificationCheck funtion to for route.
	 *
	 * @return void
	 */
	public function authentificationCheck(): void;
}
