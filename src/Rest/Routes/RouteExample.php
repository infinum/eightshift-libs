<?php

/**
 * The class register route for $className endpoint
 *
 * @package EightshiftBoilerplate\Rest\Routes
 */

declare(strict_types=1);

namespace EightshiftBoilerplate\Rest\Routes;

use EightshiftBoilerplate\Config\Config;
use EightshiftLibs\Rest\Routes\AbstractRoute;
use EightshiftLibs\Rest\CallableRouteInterface;
use WP_REST_Request;

/**
 * Class RouteExample
 */
class RouteExample extends AbstractRoute implements CallableRouteInterface
{
	/**
	 * Method that returns project Route namespace.
	 *
	 * @return string Project namespace for REST route.
	 */
	protected function getNamespace(): string
	{
		return Config::getProjectRoutesNamespace();
	}

	/**
	 * Method that returns project route version.
	 *
	 * @return string Route version as a string.
	 */
	protected function getVersion(): string
	{
		return Config::getProjectRoutesVersion();
	}

	/**
	 * Get the base url of the route
	 *
	 * @return string The base URL for route you are adding.
	 */
	protected function getRouteName(): string
	{
		return '/%endpoint_slug%';
	}

	/**
	 * Get callback arguments array
	 *
	 * @return array<string, mixed> Either an array of options for the endpoint, or an array of arrays for multiple methods.
	 */
	protected function getCallbackArguments(): array
	{
		return [
			'methods' => '%method%',
			'callback' => [$this, 'routeCallback'],
			'permission_callback' => '__return_true'
		];
	}

	/**
	 * Method that returns rest response
	 *
	 * @param WP_REST_Request $request Data got from endpoint url.
	 *
	 * @return WP_REST_Response|mixed If response generated an error, WP_Error, if response
	 *                                is already an instance, WP_HTTP_Response, otherwise
	 *                                returns a new WP_REST_Response instance.
	 */
	public function routeCallback(WP_REST_Request $request)
	{
		$response = \json_decode($request->get_body(), true);

		return \rest_ensure_response($response);
	}
}
