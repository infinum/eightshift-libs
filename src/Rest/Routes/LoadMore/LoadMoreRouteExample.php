<?php

/**
 * The class register load more route.
 *
 * @package EightshiftBoilerplate\Rest\Routes
 */

declare(strict_types=1);

namespace EightshiftBoilerplate\Rest\Routes;

use EightshiftBoilerplate\Config\Config;
use EightshiftLibs\Helpers\Components;
use EightshiftLibs\Rest\Routes\LoadMore\AbstractLoadMore;

/**
 * Class LoadMoreRoute
 */
class LoadMoreRoute extends AbstractLoadMore
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
		return '/' . self::LOAD_MORE_ROUTE;
	}

	/**
	 * Get callback arguments array
	 *
	 * @return array Either an array of options for the endpoint, or an array of arrays for multiple methods.
	 */
	protected function getCallbackArguments(): array
	{
		return [
			'methods' => static::CREATABLE,
			'callback' => [$this, 'routeCallback'],
			'permission_callback' => '__return_true'
		];
	}

	/**
	 * Map load more data reponse with the component to provide to output.
	 *
	 * @param string $type Type of load more used, usually block name.
	 * @param array<int> $response Response of IDs.
	 *
	 * @return string
	 */
	public function getMappedData(string $type, array $response): string
	{
		switch ($type) {
			case 'featured-content':
				return Components::renderPartial(
					'block',
					$type,
					'cards',
					[
						'items' => $response,
						'blockSsr' => true,
					]
				);
			default:
				return '';
		}
	}
}
