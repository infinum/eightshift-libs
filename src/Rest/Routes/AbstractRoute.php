<?php

/**
 * The class file that holds abstract class for REST routes registration
 *
 * @package EightshiftLibs\Rest\Routes
 */

declare(strict_types=1);

namespace EightshiftLibs\Rest\Routes;

use EightshiftLibs\Services\ServiceInterface;
use EightshiftLibs\Rest\RouteInterface;

/**
 * Abstract base route class
 */
abstract class AbstractRoute implements RouteInterface, ServiceInterface
{

	/**
	 * A register method holds register_rest_route function to register api route
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_action('rest_api_init', [$this, 'routeRegisterCallback']);
	}

	/**
	 * Method that registers rest route that is used inside rest_api_init hook
	 *
	 * @param \WP_REST_Server $wpRestServer Server object.
	 *
	 * @return void
	 */
	public function routeRegisterCallback(\WP_REST_Server $wpRestServer): void
	{
		\register_rest_route(
			$this->getNamespace() . '/' . $this->getVersion(),
			$this->getRouteName(),
			$this->getCallbackArguments(),
			$this->overrideRoute()
		);
	}

	/**
	 * Method that returns project Route namespace
	 *
	 * @return string Project namespace for REST route.
	 */
	abstract protected function getNamespace(): string;

	/**
	 * Method that returns project route version
	 *
	 * @return string Route version as a string.
	 */
	abstract protected function getVersion(): string;

	/**
	 * Get the base url of the route
	 *
	 * @return string The base URL for route you are adding.
	 */
	abstract protected function getRouteName(): string;

	/**
	 * Get callback arguments array
	 *
	 * @return array<string, mixed> Either an array of options for the endpoint, or an array of arrays for multiple methods.
	 */
	abstract protected function getCallbackArguments(): array;

	/**
	 * Override the existing route
	 *
	 * True overrides, false merges (with newer overriding if duplicate keys exist).
	 *
	 * @return bool If the route already exists, should we override it?
	 */
	protected function overrideRoute(): bool
	{
		return false;
	}
}
