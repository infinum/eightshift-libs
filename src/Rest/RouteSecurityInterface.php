<?php

/**
 * File that holds the Securable Route interface.
 *
 * @package EightshiftLibs\Rest
 */

declare(strict_types=1);

namespace EightshiftLibs\Rest;

/**
 * Rest security interface
 *
 * An object that defines the authentication checks for REST API routes.
 */
interface RouteSecurityInterface
{

	/**
	 * Authenticate the access of the endpoint
	 *
	 * A register method holds authenticationCheck function for the route.
	 *
	 * @return void
	 */
	public function authenticationCheck(): void;
}
