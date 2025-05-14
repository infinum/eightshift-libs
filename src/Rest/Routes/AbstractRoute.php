<?php

/**
 * The class file that holds abstract class for REST routes registration
 *
 * @package EightshiftLibs\Rest\Routes
 */

declare(strict_types=1);

namespace EightshiftLibs\Rest\Routes;

use EightshiftLibs\Helpers\Helpers;
use EightshiftLibs\Services\ServiceInterface;
use EightshiftLibs\Rest\RouteInterface;
use WP_REST_Request;
use WP_REST_Server;

/**
 * Abstract base route class
 */
abstract class AbstractRoute implements RouteInterface, ServiceInterface
{
	/**
	 * Status error const.
	 *
	 * @var string
	 */
	public const STATUS_ERROR = 'error';

	/**
	 * Status success const.
	 *
	 * @var string
	 */
	public const STATUS_SUCCESS = 'success';

	/**
	 * Status warning const.
	 *
	 * @var string
	 */
	public const STATUS_WARNING = 'warning';

	/**
	 * API response code success const.
	 *
	 * @var int
	 */
	public const API_RESPONSE_CODE_SUCCESS = 200;

	/**
	 * API response code success range const.
	 *
	 * @var int
	 */
	public const API_RESPONSE_CODE_SUCCESS_RANGE = 299;

	/**
	 * API response code error const.
	 *
	 * @var int
	 */
	public const API_RESPONSE_CODE_ERROR = 400;

	/**
	 * API response code error missing const.
	 *
	 * @var int
	 */
	public const API_RESPONSE_CODE_ERROR_MISSING = 404;

	/**
	 * API response code error server const.
	 *
	 * @var int
	 */
	public const API_RESPONSE_CODE_ERROR_SERVER = 500;

	/**
	 * API response code error forbidden const.
	 *
	 * @var int
	 */
	public const API_RESPONSE_CODE_ERROR_FORBIDDEN = 403;

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
	 * @param WP_REST_Server $wpRestServer Server object.
	 *
	 * @return void
	 */
	public function routeRegisterCallback(WP_REST_Server $wpRestServer): void
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


	/**
	 * Extract params from request.
	 * Check if array then output only value that is not empty.
	 *
	 * @param WP_REST_Request $request $request Data got from endpoint url.
	 * @param string $type Request type.
	 *
	 * @return array<string, mixed>
	 */
	protected function getRequestParams(WP_REST_Request $request, string $type = self::CREATABLE): array
	{
		// Check type of request and extract params.
		switch ($type) {
			case self::CREATABLE:
				$params = $request->get_body_params();
				break;
			case self::READABLE:
				$params = $request->get_params();
				break;
			default:
				$params = [];
				break;
		}

		// Check if request maybe has json params usually sent by the Block editor.
		if ($request->get_json_params()) {
			$params = \array_merge(
				$params,
				$request->get_json_params(),
			);
		}

		return $params;
	}

	/**
	 * Convert JS FormData object to usable data in php.
	 *
	 * @param WP_REST_Request $request $request Data got from endpoint url.
	 * @param string $type Request type.
	 *
	 * @return array<string, mixed>
	 */
	protected function prepareSimpleApiParams(WP_REST_Request $request, string $type = self::CREATABLE): array
	{
		// Get params.
		$params = $this->getRequestParams($request, $type);

		// Bailout if there are no params.
		if (!$params) {
			return [];
		}

		return \array_map(
			static function ($item) {
				return \sanitize_text_field($item);
			},
			$params
		);
	}

	/**
	 * Check user permission for route action.
	 *
	 * @param string $permission Permission to check.
	 * @param array<string, mixed> $additional Additional data to pass.
	 *
	 * @return array<string, mixed>
	 */
	protected function checkUserPermission(string $permission, array $additional = []): array
	{
		if (\current_user_can($permission)) {
			return [];
		}

		return Helpers::getApiErrorPublicOutput(
			\__('You don\'t have enough permissions to perform this action!', 'eightshift-libs'),
			$additional
		);
	}
}
